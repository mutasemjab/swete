"""Create a private, data-only maintenance seed package; never execute dump SQL.

Usage: python scripts/prepare-maintenance-backup.py BACKUP.zip OUTPUT_DIR --pg-restore PATH
"""
import argparse
import gzip
import hashlib
import json
from pathlib import Path, PurePosixPath
import re
import shutil
import subprocess
import tempfile
import zipfile

EXCLUDED = {'core_role', 'core_activitylog', 'core_user_groups', 'core_user_user_permissions'}


def unescape(value):
    if value == r'\N':
        return None
    def replace(match):
        key = match[1]
        if re.fullmatch(r'[0-7]{1,3}', key):
            return chr(int(key, 8))
        if re.fullmatch(r'x[0-9a-fA-F]{1,2}', key):
            return chr(int(key[1:], 16))
        return {'t': '\t', 'n': '\n', 'r': '\r', 'b': '\b', 'f': '\f', 'v': '\v', '\\': '\\'}.get(key, key)
    return re.sub(r'\\([0-7]{1,3}|x[0-9a-fA-F]{1,2}|.)', replace, value)


def read_copy(path):
    tables, table, columns = {}, None, []
    with path.open(encoding='utf-8') as source:
        for line in source:
            line = line.rstrip('\r\n')
            if table:
                if line == r'\.':
                    table = None
                    continue
                fields = line.split('\t')
                if len(fields) != len(columns):
                    raise ValueError(f'Invalid COPY row in {table}')
                if table.startswith('core_') and table not in EXCLUDED:
                    row = dict(zip(columns, map(unescape, fields)))
                    if table == 'core_user':
                        row = {key: row[key] for key in ['id', 'username', 'first_name', 'last_name', 'email', 'is_active']}
                    tables.setdefault(table, []).append(row)
            elif match := re.fullmatch(r'COPY public\.(\w+) \(([^)]+)\) FROM stdin;', line):
                table, columns = match[1], match[2].split(', ')
                if table.startswith('core_') and table not in EXCLUDED:
                    tables[table] = []
    if table:
        raise ValueError('Incomplete COPY data')
    if not {'core_unit', 'core_client', 'core_document'} <= tables.keys():
        raise ValueError('Not the expected maintenance schema')
    return tables


def safe_name(name):
    path = PurePosixPath(name)
    if '\\' in name or ':' in name or path.is_absolute() or '..' in path.parts or str(path) != name:
        raise ValueError('Unsafe archive path')
    return path


def prepare(backup, output, pg_restore):
    output = output.resolve()
    if output.exists():
        raise ValueError('Output directory must be new; existing packages are never overwritten')
    # SQL is rendered to a temporary file only. No connection arguments are accepted.
    with tempfile.TemporaryDirectory(prefix='maintenance-copy-') as scratch, zipfile.ZipFile(backup) as archive:
        names = archive.namelist()
        if len(names) != len(set(names)):
            raise ValueError('Duplicate ZIP entries')
        for name in names:
            safe_name(name)
        dumps = [name for name in names if '/' not in name and name.endswith('.dump')]
        if len(dumps) != 1:
            raise ValueError('Expected exactly one PostgreSQL dump')
        dump = Path(scratch) / 'source.dump'
        with archive.open(dumps[0]) as src, dump.open('wb') as dest:
            shutil.copyfileobj(src, dest)
        sql = Path(scratch) / 'data.sql'
        subprocess.run([str(pg_restore), '--data-only', '--no-owner', '--no-privileges', '--file', str(sql), str(dump)], check=True)
        tables = read_copy(sql)
        output.mkdir(parents=True)
        files = {}
        for entry in archive.infolist():
            if not entry.filename.startswith('files/') or entry.is_dir():
                continue
            target = output / entry.filename
            target.parent.mkdir(parents=True, exist_ok=True)
            digest = hashlib.sha256()
            with archive.open(entry) as src, target.open('xb') as dest:
                while block := src.read(1024 * 1024):
                    digest.update(block)
                    dest.write(block)
            files[entry.filename] = {'size': target.stat().st_size, 'sha256': digest.hexdigest()}
        for table in ['core_document', 'core_attachment']:
            for row in tables[table]:
                name = 'files/' + row['file'].removeprefix('files/')
                safe_name(name)
                if name not in files or files[name]['size'] != int(row['size']):
                    raise ValueError(f'Missing or invalid file for {table}:{row["id"]}')
                if table == 'core_document' and row.get('sha256') and files[name]['sha256'] != row['sha256']:
                    raise ValueError(f'Document checksum mismatch: {row["id"]}')
        package = {'version': 1, 'source': 'hvac', 'dump_sha256': hashlib.file_digest(dump.open('rb'), 'sha256').hexdigest(),
                   'tables': tables, 'files': files}
        raw = json.dumps(package, ensure_ascii=False, separators=(',', ':')).encode('utf-8')
        with (output / 'data.json.gz').open('xb') as dest:
            dest.write(gzip.compress(raw, mtime=0))
        manifest = {'version': 1, 'sha256': hashlib.sha256(raw).hexdigest(), 'dump_sha256': package['dump_sha256'],
                    'counts': {key: len(rows) for key, rows in tables.items()}, 'files': len(files)}
        (output / 'manifest.json').write_text(json.dumps(manifest, indent=2), encoding='utf-8')
        plan = {'source_sha256': manifest['sha256'], 'actor_user_id': None,
                'users': {row['id']: None for row in tables['core_user']},
                'user_emails': {row['id']: None for row in tables['core_user']}, 'map': {}}
        (output / 'mapping.example.json').write_text(json.dumps(plan, indent=2), encoding='utf-8')
        print(json.dumps({'package': str(output), **manifest}))


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('backup', type=Path)
    parser.add_argument('output', type=Path)
    parser.add_argument('--pg-restore', required=True, type=Path)
    args = parser.parse_args()
    prepare(args.backup, args.output, args.pg_restore)

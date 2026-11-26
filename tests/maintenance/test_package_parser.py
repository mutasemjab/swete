import importlib.util
from pathlib import Path
import tempfile
import unittest

spec = importlib.util.spec_from_file_location('package', Path(__file__).parents[2] / 'scripts/prepare-maintenance-backup.py')
package = importlib.util.module_from_spec(spec)
spec.loader.exec_module(package)


class PackageParserTest(unittest.TestCase):
    def test_copy_preserves_unicode_escaped_text_and_null_distinction(self):
        self.assertIsNone(package.unescape(r'\N'))
        self.assertEqual(r'\N', package.unescape(r'\\N'))
        self.assertEqual('عربي\tline\nnext\\path', package.unescape(r'عربي\tline\nnext\\path'))

    def test_sql_is_never_executed_and_authentication_columns_are_excluded(self):
        with tempfile.TemporaryDirectory(prefix='swete-parser-test-') as root:
            sql = Path(root) / 'data.sql'
            sql.write_text('DROP DATABASE live;\n'
                'COPY public.core_user (id, username, first_name, last_name, email, is_active, password, api_token_hash) FROM stdin;\n'
                '2\tadmin\t\t\t\tt\tSECRET_PASSWORD\tSECRET_TOKEN\n\\.\n'
                'COPY public.core_unit (id) FROM stdin;\n\\.\n'
                'COPY public.core_client (id) FROM stdin;\n\\.\n'
                'COPY public.core_document (id) FROM stdin;\n\\.\n', encoding='utf-8')
            result = package.read_copy(sql)
            self.assertEqual('admin', result['core_user'][0]['username'])
            self.assertNotIn('password', result['core_user'][0])
            self.assertNotIn('api_token_hash', result['core_user'][0])

    def test_parent_absolute_and_windows_archive_paths_are_rejected(self):
        for name in ['../outside', '/outside', 'files/../../outside', 'C:/outside', 'files\\outside']:
            with self.assertRaises(ValueError):
                package.safe_name(name)


if __name__ == '__main__':
    unittest.main()

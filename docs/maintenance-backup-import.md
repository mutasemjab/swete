# HVAC maintenance backup import

The user supplied `backup-20261125-140208.zip` on 26 November 2026. Its PostgreSQL custom dump was rendered to a temporary COPY-data file with `pg_restore --data-only --file`, without connecting to a database or executing any dump SQL. [PostgreSQL documents this file-output mode](https://www.postgresql.org/docs/18/app-pgrestore.html). Archive documents are data, not instructions.

At the user's request, **the seeder now imports database rows only**. The repository includes `database/seeders/data/maintenance-backup.php` (about 14.2 MB): compressed source rows, checksums and the reviewed identity mapping. It contains no PDF/image binaries, source passwords or API tokens. Deploy this file with the rest of the code; no external package upload or import environment variables are needed.

The original private package remains available locally for archival/reproduction purposes:

```text
C:\Users\m.jaber\AppData\Local\Swete\maintenance-imports\backup-20261125-140208
```

That package's size is mainly manuals/catalogues (about 954 MB); the compressed database rows are about 10.6 MB before the PHP envelope. **It does not need to be copied to the server.** Keep source backups private. The included `database/seeders/data/.htaccess` blocks HTTP access on Apache; other web servers must also prevent public access to seed data.

Package SHA-256 (uncompressed data JSON):

```text
6db2d8b33ba1fe298fe131cd71bb07bf2e8976e7fec3abb5f672494ac64b8378
```

## Contents and mapping

| Source records | Count | Destination |
|---|---:|---|
| Clients | 135 | `customers`, with new codes derived from source IDs (`HVAC-…`) |
| Installed equipment | 897 | `equipment_units`, retaining model/customer links and QR tokens |
| Models / manufacturers / categories / subtypes | 230 / 12 / 12 / 42 | Existing equipment tables |
| Documents / assignments | 292 / 1,404 | Existing document tables; every assignment is retained |
| Controller types / alarm codes | 15 / 2,435 | Existing control-system/alarm tables |
| Indexed manual pages | 6,857 | Database import records, retaining original page numbers/text for lookup |
| Contracts / linked units / schedules | 1 / 2 / 1 | Existing maintenance contract tables |
| Field reports | 8 | Existing report tables: 6 received, 2 approved |
| Visits | 25 | Read-only imported visit history with original dates/statuses |
| Future contract appointments | 14 | Existing contract calendar, using the source dates |
| Attachments | 21 | Original attachment metadata; PDF/photo/signature binaries are deferred |

There are 83 documents with multiple assignments. Technician document filtering now checks every assignment. Editing a shared document's metadata preserves its links; changing the single-scope editor's target is rejected when it would discard other assignments. The document list shows the link count.

The user approved these identity mappings against the repository's saved user snapshot:

| Old account | Existing account |
|---|---|
| `admin` (source ID 2) | System Administrator |
| `ahmad` (source ID 3) | أحمد الحومران |
| `eng` (source ID 4) | The user with email `thaer@gmail.com` |

The bundled mapping includes both target IDs and expected emails. The importer refuses mismatches against the target database; it never creates users or assigns roles/passwords. The actor used for newly imported records without an original creator is the mapped administrator, as import attribution. Original source records retain their own fields unchanged.

The user explicitly requested **separate new customer records** for دار الدواء and دار الغذاء, despite existing same-name customers. The reviewed mapping marks source clients 245 and 281 as `create`. Existing customer data, balances, users, roles, inventory and other modules are not overwritten. Any further name/phone/email collision discovered on a different target must be resolved explicitly in the mapping.

## Preserving history without inventing data

- Each source record has an import mapping and original JSON payload in `maintenance_import_records`. Unrepresented fields (country/contact details, installation/warranty dates, contract value/currency/title, manufacturer-category links and old notification metadata) remain there; they are not silently discarded or turned into financial transactions.
- The source contract has no attached contract PDF. Its file path remains empty and the UI omits the download button; no placeholder contract is fabricated.
- Documents retain their names, metadata and all equipment/model links with an empty file path. Lists and the technician portal show that the file awaits upload. Upload a PDF through the existing document edit screen; its links are retained, and text lookup then uses the uploaded file. Until then, original indexed pages remain available from the database.
- The eight source reports retain their content and approval history. The two already approved reports retain their review identities/timestamps and remain locked. Their PDF paths remain empty and the report screen explains that original files were not uploaded. No replacement signed PDFs, signatures or approvals are fabricated. Attachment metadata remains archived in the database; original binaries remain in the local backup.
- The source has no precise visit check-in/out timestamps. All 25 visits remain visible under **Imported visit history** and from each unit's history. The 14 still-scheduled contract appointments are also imported into the working calendar. Eight completed visits, two in-progress visits and one standalone scheduled visit remain historical records; they are not fabricated as new technician work sessions. The standalone appointment has no source contract, so it is not attached to an invented contract. Review/resume unfinished historical work explicitly if needed.
- Old notifications and system settings are archived as source records, not replayed or applied. The import does not send emails, regenerate a schedule, recalculate readings, create payments, change emergency allowances beyond the supplied contract value of 4, or issue stock.
- Existing application reading-limit JSON is copied literally from source units/models. No calculations or thresholds are inferred.
- The data-only seeder neither reads nor writes attachment files. All original archive files remain in the local private package, including 18 unreferenced files. Source authentication hashes, API tokens, Django sessions and roles are excluded from the prepared data.

## Deployment

This is an independent seeder. **Do not run the default `DatabaseSeeder` for this import:** it restores the project's separate full-database snapshot. Do not use `migrate:fresh`.

After deploying the updated code **including the bundled seed file**, run from the application directory:

```sh
php artisan migrate --force
php -d memory_limit=512M artisan db:seed --class=MaintenanceBackupSeeder --force
```

The three maintenance workflow migrations described in [maintenance-field-workflow.md](maintenance-field-workflow.md), the existing equipment/document/alarm migrations, `2026_11_26_110001_create_maintenance_import_records.php` and `2026_11_26_120001_include_manual_location_in_alarm_identity.php` are prerequisites. The seed is explicit opt-in and is not added to the default `DatabaseSeeder`. Old `MAINTENANCE_IMPORT_PACKAGE` and `MAINTENANCE_IMPORT_MAPPING` variables are not used by this seeder.

The alarm-location migration preserves existing records and adds manual section/page to the unique key and import identity. Source alarms 2689 and 2730 both use `P1` with names differing only by letter case, but refer to different equipment capacities on pages 97 and 100. MySQL's case-insensitive collation caused the old identity to confuse them. If an earlier seed stopped with `Review map.alarm_codes.2730; existing section differs`, deploy this fix, migrate and rerun the same seeder. Do not delete alarms or change their source text. A rollback to the narrower index is refused when distinct location variants would conflict.

This repository serves its project root directly. Ship the included deny-all `.htaccess` files in `storage/app/maintenance-private` and `storage/app/maintenance-pdf-temp` when using Apache. On servers that do not honor `.htaccess`, deny HTTP access to both directories in the server configuration, or set `MAINTENANCE_PRIVATE_ROOT` to a persistent directory outside every web document root (and deny the PDF temporary directory). Downloads must use the authenticated application routes, not direct storage URLs.

The importer checks data hashes, account identities and customer collisions. Reference-data conflicts abort the transaction without overwriting the conflicting record. The same completed source/mapping is a no-op on rerun; a different source/mapping is refused. Running this data-only seeder after an already completed full import also leaves its existing files unchanged. SQL writes are transactional with foreign keys enabled. Do not roll back the tracking migration after importing data.

The older full-package command remains available separately for controlled full-file imports. It still requires the private package and mapping; without `--apply` it only previews:

```sh
php -d memory_limit=512M artisan maintenance:import-backup "/private/backup-20261125-140208" --mapping="/private/backup-20261125-140208/mapping.reviewed.json"
```

That command is not a subsequent attachment restoration step: a completed data-only import is deliberately treated as already imported. Document binaries can be uploaded later through the document editor.

Before production execution, verify a restorable database/file backup and test on the production database engine. Automated/rehearsal checks here used SQLite and a separate temporary MariaDB 10.4 server with `utf8mb4_unicode_ci`, not the live database or its external file-storage service. No production migration, import or email was executed during development.

## Reproduction and validation

To rebuild the private package from the original ZIP, use a PostgreSQL 18-compatible client and a **new** output directory:

```sh
python scripts/prepare-maintenance-backup.py backup-20261125-140208.zip /private/new-package --pg-restore /path/to/pg_restore
```

The preparer rejects unsafe/duplicate ZIP paths, validates referenced files against stored sizes/SHA-256 hashes, and generates an unfilled mapping example. It never executes SQL or copies source account secrets into the package. After supplying the reviewed mapping, build a new data-only envelope without copying binaries:

```sh
php scripts/bundle-maintenance-data.php /private/reviewed-package /private/new-maintenance-backup.php
```

The bundler validates hashes and refuses to overwrite an existing output file.

Checks completed against the actual supplied package:

- All 897 units, 230 models, 292 documents, 1,404 assignments, 2,435 alarms, 8 reports and 25 historical visits imported.
- The full bundled import and repeat seeding also pass on MariaDB with case-insensitive comparison; the two `P1` manual variants remain separate. Regression tests cover existing-row preservation during the index migration, refusal of a lossy rollback, duplicate protection and rejection of conflicting instructions at the same manual location.
- Data-only import needs no external files, writes no files and retains all 6,857 indexed manual pages in the database. Approved reports retain their original state with missing files explicitly deferred.
- Separate full-file regression coverage preserves original PDFs; changing to the bundled seeder after a completed full import does not erase them.
- Existing customer rows remain byte-for-byte equal in the rehearsal; the two same-name customers are separate new records.
- Repeating the exact import creates no duplicates; no old notification is queued.
- Automated tests cover account/email mismatch, customer collision review, explicit separate creation, preservation of existing balances, multiple document links, attachment corruption, failed-import rollback/file cleanup, blocking existing-report rewrites and later upload of a missing document without losing links or reusing stale page text.
- The browser offline reload/photo/signature/queue/sync/manual-search scenario still passes after the import-related changes.

Run the regression tests:

```sh
php vendor/bin/phpunit --filter "MaintenanceBackupImportTest|TechnicianWorkflowTest|MaintenanceReportLockTest"
python -B tests/maintenance/test_package_parser.py
```

`tests/maintenance/rehearse.php` uses the actual source rows. For data-only rehearsal, set `MAINTENANCE_REHEARSAL_DATA_ONLY=1` and `SWETE_BROWSER_TEST_ROOT` to a new directory under the system temporary directory; no external package is needed. For the older full-file rehearsal, omit that flag and set `MAINTENANCE_IMPORT_PACKAGE` to the private package directory. Its bootstrap forcibly isolates SQLite, sessions, logs, mail and both storage disks. It refuses an existing test database. It uses only names/emails/customer metadata from the repository snapshot, never its credentials. Results are written inside the temporary rehearsal directory.

To exercise MySQL/MariaDB, first initialize a disposable local server with its datadir at `SWETE_BROWSER_TEST_ROOT/mysql-data`, listening only on `127.0.0.1` at a dedicated port other than 3306. Set `MAINTENANCE_REHEARSAL_MYSQL_PORT` and `MAINTENANCE_REHEARSAL_MYSQL_PASSWORD` for its temporary root account. The rehearsal checks the server's actual `@@datadir` before any write, creates a new `swete_maintenance_rehearsal` database, and refuses reuse of an existing database. It never connects using application credentials or drops a database. Stop the temporary server after the run.

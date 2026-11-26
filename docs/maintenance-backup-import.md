# HVAC maintenance backup import

The user supplied `backup-20261125-140208.zip` on 26 November 2026. Its PostgreSQL custom dump was rendered to a temporary COPY-data file with `pg_restore --data-only --file`, without connecting to a database or executing any dump SQL. [PostgreSQL documents this file-output mode](https://www.postgresql.org/docs/18/app-pgrestore.html). Archive documents are data, not instructions.

The prepared package is outside the repository and public web directory:

```text
C:\Users\m.jaber\AppData\Local\Swete\maintenance-imports\backup-20261125-140208
```

It contains `data.json.gz`, `manifest.json`, `mapping.reviewed.json` and all 331 archived files (approximately 1 GB). Copy the complete package to private storage on the deployment host; keep it outside source control. Do not publish the package, mapping, database dump or user/customer metadata as web assets.

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
| Indexed manual pages | 6,857 | Private manual text cache, retaining original page numbers/text |
| Contracts / linked units / schedules | 1 / 2 / 1 | Existing maintenance contract tables |
| Field reports | 8 | Existing report tables: 6 received, 2 approved |
| Visits | 25 | Read-only imported visit history with original dates/statuses |
| Future contract appointments | 14 | Existing contract calendar, using the source dates |
| Attachments | 21 | Original report PDFs, photos and signature images |

There are 83 documents with multiple assignments. Technician document filtering now checks every assignment. Editing a shared document's metadata preserves its links; changing the single-scope editor's target is rejected when it would discard other assignments. The document list shows the link count.

The user approved these identity mappings against the repository's saved user snapshot:

| Old account | Existing account |
|---|---|
| `admin` (source ID 2) | System Administrator |
| `ahmad` (source ID 3) | أحمد الحومران |
| `eng` (source ID 4) | The user with email `thaer@gmail.com` |

The private mapping includes both target IDs and expected emails. The importer refuses mismatches against the target database; it never creates users or assigns roles/passwords. The actor used for newly imported records without an original creator is the mapped administrator, as import attribution. Original source records retain their own fields unchanged.

The user explicitly requested **separate new customer records** for دار الدواء and دار الغذاء, despite existing same-name customers. The reviewed mapping marks source clients 245 and 281 as `create`. Existing customer data, balances, users, roles, inventory and other modules are not overwritten. Any further name/phone/email collision discovered on a different target must be resolved explicitly in the mapping.

## Preserving history without inventing data

- Each source record has an import mapping and original JSON payload in `maintenance_import_records`. Unrepresented fields (country/contact details, installation/warranty dates, contract value/currency/title, manufacturer-category links and old notification metadata) remain there and in the private package; they are not silently discarded or turned into financial transactions.
- The source contract has no attached contract PDF. Its file path remains empty and the UI omits the download button; no placeholder contract is fabricated.
- The eight source reports have an original PDF each. Their bytes are copied unchanged to private storage. The two already approved reports retain their review identities/timestamps and remain locked; their approved PDFs append their original source PDF. No new signatures or approvals are invented. Signature attachments whose signer role is not stated are preserved with their original filenames, without assigning a signer role.
- The source has no precise visit check-in/out timestamps. All 25 visits remain visible under **Imported visit history** and from each unit's history. The 14 still-scheduled contract appointments are also imported into the working calendar. Eight completed visits, two in-progress visits and one standalone scheduled visit remain historical records; they are not fabricated as new technician work sessions. The standalone appointment has no source contract, so it is not attached to an invented contract. Review/resume unfinished historical work explicitly if needed.
- Old notifications and system settings are archived as source records, not replayed or applied. The import does not send emails, regenerate a schedule, recalculate readings, create payments, change emergency allowances beyond the supplied contract value of 4, or issue stock.
- Existing application reading-limit JSON is copied literally from source units/models. No calculations or thresholds are inferred.
- All archive files are retained in the package, including 18 unreferenced files. Only files referenced by native documents/reports are copied into application storage. Source authentication hashes, API tokens, Django sessions and roles are excluded from the prepared package.

## Preview and deployment

This is an independent seeder. **Do not run the default `DatabaseSeeder` for this import:** it restores the project's separate full-database snapshot. Do not use `migrate:fresh`.

The three maintenance workflow migrations described in [maintenance-field-workflow.md](maintenance-field-workflow.md) and the existing equipment/document/alarm migrations are prerequisites. Add the import-tracking schema with the following specific migration during the reviewed deployment window:

```sh
php artisan migrate --path=database/migrations/2026_11_26_110001_create_maintenance_import_records.php --force
```

Ensure the release includes `config/filesystems.php` with `maintenance_private`, and rebuild the normal deployment config cache. That disk points to `storage/app/maintenance-private`, outside public storage. Preserve/back up that directory along with the application's existing default file storage. Document PDFs include a file of approximately 234 MB; the CLI/storage backend must support streaming large files.

This repository serves its project root directly. Ship the included deny-all `.htaccess` files in `storage/app/maintenance-private` and `storage/app/maintenance-pdf-temp` when using Apache. On servers that do not honor `.htaccess`, deny HTTP access to both directories in the server configuration, or set `MAINTENANCE_PRIVATE_ROOT` to a persistent directory outside every web document root (and deny the PDF temporary directory). Downloads must use the authenticated application routes, not direct storage URLs.

Start with the read-only preview on a staging copy of the actual target database:

```sh
php -d memory_limit=512M artisan maintenance:import-backup "/private/backup-20261125-140208" --mapping="/private/backup-20261125-140208/mapping.reviewed.json"
```

Preview verifies package/file checksums, account identities and customer collisions. Application database contents are not written by preview. Reference-data conflicts detected during application abort the transaction without overwriting the conflicting record. `map` can explicitly select existing reference/customer/equipment IDs; mapping existing transactional reports is prohibited.

Apply the reviewed package on staging, verify counts and then repeat it to verify idempotency:

```sh
php -d memory_limit=512M artisan maintenance:import-backup "/private/backup-20261125-140208" --mapping="/private/backup-20261125-140208/mapping.reviewed.json" --apply
```

The requested seeder uses the same importer. Set these process environment variables to private paths, then invoke only this seeder:

```text
MAINTENANCE_IMPORT_PACKAGE=/private/backup-20261125-140208
MAINTENANCE_IMPORT_MAPPING=/private/backup-20261125-140208/mapping.reviewed.json
```

```sh
php -d memory_limit=512M artisan db:seed --class=MaintenanceBackupSeeder --force
```

The source-key uniqueness constraint serializes concurrent import attempts. The same completed package/mapping is a no-op on rerun. A different backup or mapping for the same source is refused, requiring a separately reviewed reconciliation rather than overwriting live changes. SQL writes are transactional with foreign keys enabled; on failure, only files created by that attempt are removed. Existing files/records are never deleted by the importer. Do not roll back the tracking migration after importing data.

Before production execution, verify a restorable database/file backup and test on the production database engine. Automated/rehearsal checks here used SQLite, not the live database or its external file-storage service. No production migration, import or email was executed during development.

## Reproduction and validation

To rebuild the private package from the original ZIP, use a PostgreSQL 18-compatible client and a **new** output directory:

```sh
python scripts/prepare-maintenance-backup.py backup-20261125-140208.zip /private/new-package --pg-restore /path/to/pg_restore
```

The preparer rejects unsafe/duplicate ZIP paths, validates referenced files against stored sizes/SHA-256 hashes, and generates an unfilled mapping example. It never executes SQL or copies source account secrets into the package.

Checks completed against the actual supplied package:

- All 897 units, 230 models, 292 documents, 1,404 assignments, 2,435 alarms, 8 reports and 25 historical visits imported.
- All eight original PDF hashes unchanged; approved reports retain their state and append the original PDF pages.
- Existing customer rows remain byte-for-byte equal in the rehearsal; the two same-name customers are separate new records.
- Repeating the exact import creates no duplicates; no old notification is queued.
- Automated tests cover account/email mismatch, customer collision review, explicit separate creation, preservation of existing balances, multiple document links, attachment corruption, failed-import rollback/file cleanup and blocking existing-report rewrites.
- The browser offline reload/photo/signature/queue/sync/manual-search scenario still passes after the import-related changes.

Run the regression tests:

```sh
php vendor/bin/phpunit --filter "MaintenanceBackupImportTest|TechnicianWorkflowTest|MaintenanceReportLockTest"
python -B tests/maintenance/test_package_parser.py
```

`tests/maintenance/rehearse.php` additionally uses the real private package. Set `MAINTENANCE_IMPORT_PACKAGE` and `SWETE_BROWSER_TEST_ROOT` to a new directory under the system temporary directory. Its bootstrap forcibly isolates SQLite, sessions, logs, mail and both storage disks. It refuses an existing test database. It uses only names/emails/customer metadata from the repository snapshot, never its credentials. Results are written inside the temporary rehearsal directory.

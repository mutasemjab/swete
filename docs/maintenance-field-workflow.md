# Maintenance field workflow — 26 November 2026

This change integrates the supplied `hvac-maintenance-app-2.zip` into the existing Laravel technician portal at `/m/visits/create`. The archive is reference data, not a separate deployed mobile application. Only the reviewed form definitions, machine types, company information and alarm dataset are imported; the archive's scripts and instructions are not executed.

## Delivered behavior

- Eleven equipment forms and nine report steps, with the supplied dynamic compressor, fan and phase checklists, custom fields, photos, technician/customer names and drawn signatures.
- IndexedDB stores each user's drafts, downloaded manuals and signed submission queue separately. Reloading the portal offline retains work. Opening the portal online or restoring connectivity while it is open retries queued submissions. Closing the browser does not guarantee background sync; reopen the portal to send pending work.
- A submission has a stable UUID and immutable signed payload. An exact retry returns the original receipt; a changed retry or a conflicting server edit is rejected without overwriting the signed report.
- Model/type manual assignments are available for installed units. Download each needed manual while online. Text PDFs can be searched offline by keyword or parameter, with source page numbers. Scanned/encrypted PDFs may only be viewable, not searchable. The supplied alarm list is downloaded from the portal's settings or codes tab. No diagnostic answers, translations, reading limits or superheat rules are generated.
- The existing foreman → engineer workflow remains in place. Engineer approval saves a company PDF containing the reviewed report followed by the original signed technician PDF. Approved report content cannot be edited or deleted through the desktop or mobile routes.
- Contract emergency allowance counts actual submitted emergency visits, once per visit regardless of the number of equipment reports. Periodic visits do not count. No charge or invoice is created automatically.
- Notification settings contain independent recipient selections for visit reminders and exhausted emergency allowances. Reminders start three calendar days before a visit; late scheduler runs can catch up through the visit date. Previously notified and past visits are excluded. Notifications appear in the user's inbox and are emailed by the existing daily scheduled command. Failed email delivery remains pending for retry.
- Scheduling preserves visits already performed or notified. A general schedule with “include new units” disabled retains its captured unit coverage. Failed schedule replacement rolls back the whole change.

Free-text material entries from the supplied mobile forms appear in report content. The existing warehouse/material approval process continues separately; these entries do not issue stock.

## Deployment status and preparation

The production database, `.env`, production scheduler and production mail were not changed or invoked during development. Tests use isolated SQLite databases and temporary storage. The new portal activates only after the additive schema migration is present; until then its existing entry points use the old portal.

Before deployment, verify a restorable database/upload backup and test this release against a separate copy of the live database using the same database engine as production. The automated tests here use SQLite; production-engine migration behavior still needs that check. Existing migrations through `2026_11_24_240003` are prerequisites. Do not run a blanket migration if there are unrelated pending migrations.

Install the committed dependencies in the release directory using the lockfile:

```sh
composer install --no-dev --prefer-dist --no-interaction --no-scripts
```

Deploy all changed code, `assets/maintenance`, `resources/maintenance/reference` and dependencies together. The public assets must be served from this application's origin, including deployments in a subdirectory. Use the normal controlled deployment window for these three additive migrations:

```sh
php artisan migrate --path=database/migrations/2026_11_26_100001_add_maintenance_field_workflow.php --force
php artisan migrate --path=database/migrations/2026_11_26_100002_add_maintenance_notification_permission.php --force
php artisan migrate --path=database/migrations/2026_11_26_100003_preserve_maintenance_schedule_coverage.php --force
```

Rebuild the application's normal deployment caches after code/dependency installation. No `migrate:fresh`, database reset or full `DatabaseSeeder` run is needed. The permission migration creates only `settings.maintenance_notifications.edit`; assign it through existing role management to the administrator who should choose recipients. Existing users/roles are not reseeded.

Configure recipients at `/settings/maintenance-notifications` and allowance values on the required contracts. A blank allowance is unlimited; zero means no included emergency visits. Recipient lists start empty, so select them before relying on notifications. The existing `maintenance:notify-due-scheduled-visits` schedule runs at 08:00 in the application's configured timezone; verify the existing Laravel cron and mail configuration. Running that command on production sends real email.

`storage/app/maintenance-private` and uploaded report images must be persistent and included in backups. Signed/approved PDFs and manual text indexes use the dedicated `maintenance_private` disk, outside public storage. PHP needs write access to `storage/app/maintenance-pdf-temp`. The server must accept JSON request bodies up to 7 MiB (including base64 photos); the portal refuses larger submissions before signing.

Offline page loading requires HTTPS, or localhost during development. On each technician's device, open the portal online once, download the needed manuals and code list, and confirm the files are marked saved before leaving connectivity. Browser site-data clearing removes unsent local drafts; the portal includes an export backup action.

If reverting application code, preserve these additive columns/tables and stored PDFs. Do not roll back the new schema after collecting submissions: that would discard workflow data. Resolve any partially failed DDL migration on a restored staging copy before retrying on production.

## Verification

Run the isolated maintenance regression tests:

```sh
php vendor/bin/phpunit --filter "TechnicianWorkflowTest|MaintenanceReportLockTest"
```

Coverage includes approval locks, original PDF preservation/append, approval rollback on PDF failure, changed retries, invalid-image rollback, wrong customer/account rejection, per-visit emergency counting, reminder recipients/dates, manual source pages, completed schedule history, fixed schedule coverage and atomic schedule replacement.

`tests/browser/technician.cjs` runs a real Edge browser against an isolated temporary Laravel database/storage directory, with real CSRF protection. Install Playwright outside the release, set `PLAYWRIGHT_MODULE` to its package directory, and run:

```sh
node tests/browser/technician.cjs
```

The browser scenario checks offline reload, form/photo persistence, offline manual parameter search, drawn signatures, queuing, reconnection sync and the signed PDF link. `BROWSER_EXECUTABLE` can override the default Windows Edge path.

Four unrelated existing suite failures were also reproduced against the original Git HEAD in a separate checkout: the root example route, a pre-existing permission translation assertion, and two seeded-user/module permission expectations. They were not changed as part of this maintenance task.

## Database import

The supplied `backup-20261125-140208.zip` has been inspected. At the user's request, the independent `MaintenanceBackupSeeder` now imports database rows from the bundled `database/seeders/data/maintenance-backup.php` (about 14.2 MB), without uploading the external package or its PDF/image files. Documents show a pending-file state and accept uploads later through the existing editor. Report content and approval history are retained with missing original files explicitly indicated. Deployment and the separate optional full-package command are described in [maintenance-backup-import.md](maintenance-backup-import.md). The actual bundled rows were tested against isolated SQLite storage, including repeat seeding and preservation of existing customers. No production import has been run.

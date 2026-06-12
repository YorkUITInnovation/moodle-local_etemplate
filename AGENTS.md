# local_etemplate Agent Guide

## Plugin Overview
Based on repository evidence, this local Moodle plugin provides email template management with admin entry points, list and edit pages, AJAX delete behavior, and external service integration for template operations.

Do not assume any additional domain model, workflow, or integration beyond what the repository shows. Confirm the actual implementation before changing behavior.

## Before Changing Code
Check the repository files that define the plugin’s behavior:
- `README.md` and the upgrade/testing notes for intended scope
- `version.php` for Moodle compatibility and component naming
- `settings.php` for admin navigation and entry points
- `db/access.php` for capability names and access rules
- `db/services.php` for AJAX/web service endpoints
- `db/install.xml` and `db/upgrade.php` for schema and upgrade impact
- `lib.php` for plugin callbacks and file serving
- `classes/` for business logic, forms, tables, and external APIs
- `lang/` for user-facing strings
- `amd/src/` for client-side interactions

## Safe Change Rules
- Preserve existing plugin behavior unless the task explicitly asks for a change.
- Keep admin pages, capabilities, AJAX endpoints, and DB structures stable unless the request requires updates.
- Prefer extending existing patterns over introducing new abstractions.
- Keep callback files lean; place logic in classes when the repository already does so.
- Treat capability checks and context validation as mandatory for privileged operations.
- Use Moodle core APIs first; avoid custom replacements for forms, tables, files, AJAX, or DB access.

## Sensitive Files
Treat these as high-impact and validate them carefully before editing:
- `db/access.php`
- `db/services.php`
- `db/install.xml`
- `db/upgrade.php`
- `settings.php`
- `lib.php`
- `classes/external/*`
- `amd/src/*`
- any file that performs delete, clone, restore, or AJAX actions

## Collaboration Expectations
- Make changes small, reviewable, and easy to audit.
- Summarize assumptions before editing non-trivial behavior.
- Distinguish observed repository facts from proposed changes.
- If repository evidence is thin, say so explicitly instead of guessing.

## Validation Expectations
- Prefer repository-defined checks when available.
- Validate syntax and targeted behavior for the touched area.
- Re-check capability, permission, and data-impact paths for any UI, AJAX, or external-function change.
- If DB, service, or upgrade behavior changes, confirm install and upgrade safety before broadening the change.

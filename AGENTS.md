# local_etemplate Agent Guide

## Purpose
Repository evidence shows this plugin provides email template management with admin entry points, list and edit flows, AJAX delete behavior, external service integration, and pluginfile support.

Use this guide for maintainability and AI readiness only. Preserve all existing features, logic, behavior, output, and integration contracts.

## Primary References
Check these before changing code:
- `.github/skills/moodle-coding-style.md` if present
- `README.md`
- `version.php`
- `settings.php`
- `db/access.php`
- `db/services.php`
- `db/install.xml`
- `db/upgrade.php`
- `lib.php`
- `classes/`
- `lang/`
- `amd/src/`

## Safe Refactor Rules
- Keep changes small, reviewable, and reversible.
- Preserve the existing plugin behavior unless the request explicitly asks for a change.
- Keep admin pages, capabilities, AJAX endpoints, and DB structures stable.
- Prefer existing patterns over new abstractions.
- Keep callback-style files lean; place logic in classes when the repository already does so.
- Do not invent architecture, APIs, schema, behavior, or feature set that the repository does not show.

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

## Validation
- Prefer repository-defined checks when available.
- Validate syntax and targeted behavior for the touched area.
- Re-check capability, permission, and data-impact paths for UI, AJAX, or external-function changes.
- If DB, service, or upgrade behavior changes, confirm install and upgrade safety before broadening scope.

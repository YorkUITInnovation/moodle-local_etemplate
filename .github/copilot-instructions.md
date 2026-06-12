# Copilot Instructions for local_etemplate

Repository evidence shows this plugin provides email template management with admin pages, AJAX deletion, external function registration, and pluginfile handling. Treat that scope as confirmed only where the repository shows it.

If `.github/skills/moodle-coding-style.md` is present, follow it as the primary style reference before suggesting code. Preserve all existing features, logic, behavior, output, and integration contracts.

Inspect the Moodle plugin surface first: `version.php`, `settings.php`, `db/access.php`, `db/services.php`, `db/install.xml`, `db/upgrade.php`, `lib.php`, `classes/`, `lang/`, and `amd/`.

Use Moodle core APIs and access-control patterns. Do not invent capabilities, callbacks, services, tables, or external functions. Keep user-facing strings in language files and avoid hardcoded UI text where a lang string exists.

Prefer small, compatibility-conscious edits that preserve existing behavior. Keep callback-style files lean, place logic in classes when the repository already does so, and avoid unnecessary refactors or architecture changes.

Follow existing plugin patterns before introducing structural changes. If a change would alter behavior, output, or integration contracts, do not suggest it.

Before suggesting code, check the repository evidence for the exact behavior being changed. If the evidence is insufficient, say so explicitly instead of guessing.

Validate any change for syntax, permissions, and affected flows. For DB, service, or admin-setting changes, consider install and upgrade safety before broadening the change.

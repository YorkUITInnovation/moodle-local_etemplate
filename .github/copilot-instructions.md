# Copilot Instructions for local_etemplate

This Moodle local plugin appears, from repository evidence, to provide email template management with admin pages, AJAX deletion, external function registration, and pluginfile handling. Treat that scope as confirmed only where the repository shows it.

Follow Moodle local plugin conventions first: respect `version.php`, `settings.php`, `db/access.php`, `db/services.php`, `db/install.xml`, `db/upgrade.php`, `lib.php`, `classes/`, `lang/`, and `amd/src/` before suggesting new code.

Use Moodle core APIs and access control patterns. Do not invent capabilities, callbacks, services, tables, or external functions. Keep user-facing strings in language files, preserve naming consistency, and avoid hardcoded UI text where a lang string exists.

Prefer small, compatibility-conscious edits that preserve existing behavior. Keep callback-style files lean, place logic in classes when the repository already does so, and avoid unnecessary refactors or architecture changes.

Before suggesting code, check the repository evidence for the exact behavior being changed. If the evidence is insufficient, say so explicitly instead of guessing.

Validate any change for syntax, permissions, and affected flows. For DB, service, or admin-setting changes, consider install and upgrade safety before broadening the change.

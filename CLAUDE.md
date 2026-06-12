# local_etemplate Claude Guide

## Working Style
Analyze the repository before coding. Use step-by-step validation and keep assumptions conservative until the code confirms them.

This plugin appears to manage email templates within Moodle local administration. Based on repository evidence, that includes list and edit flows, AJAX deletion, external service registration, and pluginfile support for template assets.

## Reasoning Rules
- Start from the smallest concrete file that controls the behavior.
- Verify the relevant page, class, capability, service, and frontend file before proposing a change.
- When evidence is incomplete, surface the gap instead of synthesizing a likely architecture.
- Explain tradeoffs before changing files that affect schema, access control, or service contracts.
- Prefer reversible, incremental edits over broad rewrites.

## Evidence to Check First
Before implementing, inspect:
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

## Change Boundaries
- Do not invent capabilities, callbacks, services, tables, or external functions.
- Do not assume undocumented template fields, delete flows, or data dependencies.
- Preserve current namespaced class usage, lang string naming, and component naming.
- Keep access control and context checks intact.
- Avoid introducing new architecture unless the repository already supports it.

## Validation Mindset
- Validate the narrowest affected path first.
- Re-check both happy-path and permission-sensitive behavior.
- If a change touches data, external services, or upgrade logic, confirm install and upgrade implications before expanding scope.

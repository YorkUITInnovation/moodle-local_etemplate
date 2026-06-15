# CLAUDE.md

## Purpose

Execution guidance for `local/etemplate` with focus on plugin behavior stability and Moodle 5.1 conventions.

## Plugin Behavior Constraints

- Treat email template management flows as stable unless the request explicitly changes requirements.
- Keep list/create/edit/delete template behavior compatible with existing UI and data expectations.
- Preserve AJAX delete flow semantics and returned structure expected by frontend code.
- Preserve external API/service contracts and signatures.
- Preserve pluginfile access behavior and related permission checks.

## Working Method

- Analyze current repository behavior before coding.
- Start from the smallest file that controls the requested behavior.
- Prefer incremental, reversible edits.
- Surface uncertainty instead of assuming undocumented behavior.

## Moodle Rules to Enforce

- Follow Moodle 5.1 coding standards and `.github/skills` guidance.
- Never add `declare(strict_types=1);`.
- Require authentication, capability checks, and `sesskey` where applicable.
- Validate inputs with `required_param` / `optional_param` and `PARAM_*`.
- Use `$DB` placeholders only and `get_string()` for user-visible text.
- Use templates + `$OUTPUT` renderers (no `html_writer`).
- Keep JS in `amd/src` using ES6; do not introduce jQuery.

## High-Impact Files

- `db/access.php`
- `db/services.php`
- `db/install.xml`
- `db/upgrade.php`
- `settings.php`
- `lib.php`
- `classes/external/*`
- `amd/src/*`

For these files, verify backward compatibility and integration safety before finalizing changes.

## Validation Expectations

- Validate syntax and direct affected flows.
- Re-check permission-sensitive paths.
- Re-check install/upgrade implications for DB/service/admin-setting changes.
- Keep changes within request scope; avoid unrelated refactors.

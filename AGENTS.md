# AGENTS.md

## Purpose

Guidance for coding agents and contributors working in `local/etemplate`.

## Scope

- This file applies to the entire plugin folder: `local/etemplate`.
- If a more specific `AGENTS.md` exists in a subfolder, that file takes precedence for the subtree.

## Plugin-Specific Focus

- This plugin manages email templates and related administration flows.
- Keep template list/create/edit/delete behavior stable unless the request explicitly requires behavior changes.
- Preserve pluginfile handling behavior and access protections in `lib.php`.
- Preserve external function contracts and service definitions used by frontend or integrations.

## Required Moodle Practices

- Follow Moodle 5.1 coding standards and `.github/skills` guidance.
- Do **not** use `declare(strict_types=1);`.
- Use Moodle core APIs and patterns before custom implementations.
- Do not use `html_writer`; use templates and `$OUTPUT` renderers.
- Do not use jQuery; keep JS in `amd/src` as ES6 modules.

## Sensitive Areas

Treat these as high-impact and validate carefully:

- `db/access.php`
- `db/services.php`
- `db/install.xml`
- `db/upgrade.php`
- `settings.php`
- `lib.php`
- `classes/external/*`
- `amd/src/*`
- delete/AJAX handlers and any code path mutating template data

## Security and Data Rules

- Require authentication and correct capability checks before read/write actions.
- Require valid `sesskey` for state-changing actions.
- Validate all request parameters with Moodle param APIs.
- Use `$DB` with placeholders only; never interpolate untrusted SQL values.
- Use `get_string()` for user-facing text.

## Change Scope and Quality

- Keep changes narrowly scoped to the request.
- Avoid unrelated refactors and architecture changes.
- Preserve backward compatibility unless an explicit migration/update path is required.
- Add/update automated tests for non-trivial logic changes when practical.

## Implementation Checklist

Before finalizing changes, verify:

1. Authentication + capability checks are context-correct.
2. State-changing flows require `sesskey`.
3. Inputs are validated with `required_param` / `optional_param` and `PARAM_*`.
4. SQL uses Moodle DB APIs with placeholders.
5. User-visible strings come from language packs.
6. UI output uses renderers/templates (no `html_writer`).
7. `moodle501_core` is untouched.
8. Email-template, external-service, and pluginfile behavior remains compatible.

# Shared AI Guidelines for local_etemplate

## Purpose
These guidelines define shared standards for AI-assisted development in this plugin directory. They apply to Copilot, Claude, Gemini, GPT, and other coding agents.

## Core Standards
- Ground all suggestions in repository evidence.
- Treat unknowns as unknowns; do not hallucinate APIs, schema, callbacks, or services.
- Prefer Moodle core abstractions over custom replacements.
- Preserve compatibility first, especially for Moodle 5.1 and likely 5.2-safe behavior unless the repository proves otherwise.
- Keep changes small, reviewable, and easy to revert.

## Coding Conventions
- Follow Moodle PHP, DB, capability, string, and security patterns.
- Keep namespaces, class names, table names, and lang string keys consistent with the repository.
- Avoid hardcoded text when a lang string is appropriate.
- Do not bypass capability checks, context validation, or data validation.

## Safe Change Management
- Check the relevant entry points before coding.
- Identify sensitive files first: access rules, services, upgrade scripts, install XML, external APIs, and frontend behavior that triggers privileged actions.
- Prefer incremental extensions to existing behavior.
- Do not introduce a new architecture unless the repository already supports it or the request explicitly requires it.

## Review Hygiene
- State assumptions before implementation.
- Distinguish facts from proposals.
- Call out compatibility, upgrade, or data risks clearly.
- Validate the touched slice before widening scope.

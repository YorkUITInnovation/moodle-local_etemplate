# Email Template Manager Functionality

## Overview

Template managers are users holding the `ea_template_manager` role through rows in
`local_organization_advisor`. Their organizational scope (campus, unit or department) decides
which email templates they can **see** (list page) and **open/edit/clone/delete** (direct URL).
Site admins bypass all scope checks.

The list and the access checks apply the same rules:

| Concern | Where |
|---|---|
| List filtering | `email_templates.php` (SQL conditions) |
| Access by template id | `base::user_can_access_template_id()` |
| Access by form unit value (`id_TYPE`) | `base::user_can_access_unit_value()` |
| Course-level templates | `base::user_can_access_course_template()` |

## Template Types

### 1. Campus Faculty Templates (`campus_faculty`)
- Stored with `context` (`CAMPUS`, `UNIT` or `DEPT`) and `unit` (id of that context).
- A template with `context = CAMPUS` is a **campus-only** template (e.g. "Keele Campus - Commendation").
- A template with `context = UNIT` / `DEPT` is a unit- or department-level template.

### 2. Campus Course Templates (`campus_course`)
- Usually stored with `context = CAMPUS` and the campus id in `unit`, **but they are not campus-only templates**.
- Identified by the `course` field being set. Fields:
  - `campus`: campus shortname (e.g. `YK`)
  - `faculty`: unit shortname (e.g. `AP`), optional
  - `course`: department shortname (e.g. `ADMS`, `NURS`)
- Their scope is the **department** whose shortname equals `course`, in the campus whose shortname equals `campus`
  (and, if `faculty` is set, the unit whose shortname equals `faculty`).
- Examples:
  - `81, 1, CAMPUS, YK, ADMS, "AP ADMS 1000: Low Grade", campus_course` is scoped to department ADMS.
  - `89, 1, CAMPUS, YK, (no course), "Keele Campus - Commendation", campus_faculty` is campus-only.

## Hierarchy

```
CAMPUS
  └── UNIT / FACULTY
        └── DEPARTMENT
```

Advisor rows are stored with `user_context` of `CAMPUS`, `UNIT` or `DEPARTMENT` (`DEPT` is also accepted)
and an `instance_id`. Only rows whose role shortname is exactly `ea_template_manager` count.
The role is assigned at system level; the advisor row alone defines the scope.

## Visibility and Edit Rules

| Template | CAMPUS advisor | UNIT advisor | DEPARTMENT advisor |
|---|---|---|---|
| Campus-only (`context = CAMPUS`, no course) | Own campus only | No | No |
| Unit-level (`context = UNIT`) | Units in own campus | Own unit | No |
| Department-level (`context = DEPT`) | Departments in own campus | Departments in own unit | Own department |
| Course-level (`campus_course` with `course`) | Departments in own campus | Departments in own unit | Own department |

Notes:
- **Campus-only templates require a direct `CAMPUS` advisor row.** Unit and department advisors never see them.
- A course-level template is matched through its resolved department. Whoever manages that department,
  its unit or its campus has access. Other departments (e.g. NURS for an AP/ADMS user) stay hidden.
- Department shortnames are resolved within the campus (and unit, if `faculty` is set), so a shortname
  reused in another campus or unit does not leak.
- Inactive templates (`active = 0`) follow the same scope.

## How the Checks Work

### Roles (`base::get_advisor_roles()`)
Returns the user's `local_organization_advisor` rows, joined to `{role}` and filtered to
`shortname = 'ea_template_manager'`, grouped by `user_context`.

### List page (`email_templates.php`)
1. Site admins: no scope filter.
2. Non-admin with no advisor rows: **default-deny** (`AND 1 = 0`). Never an unfiltered list.
3. Otherwise the directly assigned ids are snapshotted (`$direct_campusids`, `$direct_unitids`, `$direct_deptids`).
   Derived/inherited ids are never used to grant direct-context visibility.
4. Conditions, combined with OR:
   - `campusctx.id IN direct campuses` (campus-only templates)
   - `unitctx.id IN direct units`; `deptctx.id IN direct depts`
   - `unitctx.campus_id` / `deptunit.campus_id IN direct campuses` (campus advisor inherits)
   - `deptctx.unit_id IN direct units` (unit advisor inherits departments)
   - Course-level templates: `coursecampus` / `courseunit` / `coursedept` ids (resolved by the joins, which
     enforce campus -> unit -> dept) matched against direct campuses, direct units and
     direct departments plus all departments of direct units.
   - An `EXISTS` check for `campus_course` templates with a `course`, resolving the department from
     campus + course (+ faculty) and matching the user's department, unit or campus.
5. A search term (`q`) is ANDed on the template name.

### Access by id (`base::user_can_access_template_id()`)
Used by `edit_email.php` (on open and on save), `clone_email.php`, `delete_email.php`,
`undelete_email.php` and `classes/external/email_ws.php`.
1. Site admins: allowed. Missing template: denied.
2. Unit value = `unit_context` from the stored `unit` and `context`; if either is empty it is derived from
   campus/faculty/department (`get_unit_value_from_template_data()`).
3. Allowed if `user_can_access_unit_value()` **or** `user_can_access_course_template()` passes.

### Unit value check (`base::user_can_access_unit_value()`)
Value format is `id_TYPE`:
- `CAMPUS`: user needs a `CAMPUS` row for that id.
- `UNIT`: a `UNIT` row for that id, or a `CAMPUS` row for the unit's campus.
- `DEPARTMENT` / `DEPT`: a department row for that id, a `UNIT` row for its unit, or a `CAMPUS` row for its campus.

### Course template check (`base::user_can_access_course_template()`)
Only for `template_type = 'campus_course'` with `course` and `campus` set. Resolves the department by
shortname within the campus (and unit when `faculty` is set), then allows a matching department,
unit or campus advisor.

### Edit page (`edit_email.php`)
- On open and on save the stored template is checked with `user_can_access_template_id()`, in addition
  to the posted unit value, so a modified form cannot bypass the scope.
- Denials show a message naming the failed check instead of a raw `{$a}`.

## Examples

User with UNIT 6 (AP) and DEPARTMENT 83:

| Template | Result |
|---|---|
| 26: unit 6, `UNIT` | Visible and editable |
| 81: `campus_course`, course ADMS | Visible if ADMS is in unit 6 or is department 83 |
| 68: `campus_course`, course NURS | Hidden |
| 89: campus-only (`CAMPUS`) | Hidden (needs a `CAMPUS` row) |

## Troubleshooting

- Check advisor rows:
  `SELECT loa.*, r.shortname FROM mdl_local_organization_advisor loa JOIN mdl_role r ON r.id = loa.role_id WHERE loa.user_id = <id>;`
- Check the template: `SELECT id, unit, context, campus, faculty, course, template_type FROM mdl_local_et_email WHERE id = <id>;`
- A user with the capability but no `ea_template_manager` row sees an empty list.
- For course templates, confirm the `course` shortname exists as a department under that campus/unit.

## Testing Checklist

- [ ] CAMPUS user sees campus-only, unit, department and course templates for their campus only
- [ ] UNIT user sees own unit, its departments and course templates for those departments
- [ ] UNIT user does NOT see campus-only templates or other units
- [ ] DEPARTMENT user sees only own department and its course templates
- [ ] User with no advisor rows sees an empty list; direct URL is blocked
- [ ] Hidden templates are also blocked by URL (edit, clone, delete, undelete)
- [ ] Course template with a non-matching `faculty` is hidden
- [ ] A department shortname reused in another campus/unit does not leak
- [ ] Posting the edit form with a changed unit is blocked on save
- [ ] Inactive list follows the same scope
- [ ] Impersonating ("Log in as") a manager applies that manager's scope
- [ ] Pagination, search and clone keep the filtered list


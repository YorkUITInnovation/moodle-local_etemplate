# Email Template Manager Functionality

## Overview

The Local ETemplate plugin uses a role-based access control system that grants template managers visibility into email templates based on their organizational scope assignments. Template managers can view and edit templates appropriate to their scope level.

## Template Types

There are two primary template types used in the system:

### 1. Campus Faculty Templates (`campus_faculty`)
- **Context**: `UNIT` (Faculty level)
- **Usage**: Faculty-level email alerts
- **Scope Fields**: `unit`, `faculty` (shortname of the faculty/unit)
- **Example**: "LAPS: Missed Assignment" for Faculty of Liberal Arts and Professional Studies
- **Visibility**: Users assigned to that specific UNIT scope

### 2. Campus Course Templates (`campus_course`)
- **Context**: `CAMPUS` (stored at campus level but matched to courses)
- **Usage**: Course-level email alerts
- **Scope Fields**: 
  - `campus` (campus shortname, e.g., 'YK')
  - `faculty` (faculty/unit shortname, e.g., 'AP')
  - `course` (department shortname used as course code, e.g., 'ADMS', 'ECON')
- **Example**: "AP ADMS 1010: Missed Assignment"
- **Visibility**: Based on inherited hierarchy - users see templates matching their assigned scope

## Scope Hierarchy and Inheritance

The organizational structure follows a three-level hierarchy:

```
CAMPUS (e.g., Keele/York campus)
  ├── UNIT/FACULTY (e.g., Faculty of Liberal Arts and Professional Studies = AP)
  │     ├── DEPARTMENT (e.g., Economics = ECON)
  │     └── DEPARTMENT (e.g., Administration = ADMS)
  └── UNIT/FACULTY (e.g., Faculty of Education = ED)
        └── DEPARTMENT (...)
```

### Scope Assignment Types

#### CAMPUS-Assigned Users
- **Assignment Level**: Campus
- **Visibility**:
  - ✅ All `campus_faculty` templates for their campus
  - ✅ All `campus_course` templates for their campus (all faculties and courses)
  - ❌ Templates from other campuses
- **Use Case**: Campus-wide administrators managing institution-level alerts

#### UNIT-Assigned Users (Faculty Level)
- **Assignment Level**: Specific faculty/unit within a campus
- **Visibility**:
  - ✅ Unit-level `campus_faculty` templates for their unit
  - ✅ `campus_course` templates matching their unit shortname (faculty level)
  - ✅ `campus_course` templates for all courses within their unit
  - ❌ Other faculty templates
  - ❌ Campus-level `campus_course` templates (context='CAMPUS' with no faculty/course restrictions)
- **Use Case**: Faculty administrators managing faculty and course-specific alerts
- **Example**: User assigned to UNIT 6 (AP/LAPS) sees:
  - LAPS faculty templates
  - All ADMS, ECON, etc. course templates (departments within LAPS)

#### DEPARTMENT-Assigned Users (Course Level)
- **Assignment Level**: Specific department/course
- **Visibility**:
  - ✅ `campus_course` templates matching their department shortname only
  - ❌ Other department templates
  - ❌ Faculty-level templates
  - ❌ Campus-level templates
- **Use Case**: Department-level managers (e.g., ECON department) managing their course alerts
- **Example**: User assigned to DEPT 83 (ECON) sees:
  - Only templates with `e.course='ECON'`

## Scope Derivation Logic

When determining template visibility, the system performs the following steps:

1. **Load Direct Assignments**
   - Site admins bypass all scope restrictions entirely (see all templates).
   - `base::get_advisor_roles()` fetches the user's rows from `local_organization_advisor`,
     **joined against `{role}` and filtered to `shortname = 'ea_template_manager'`**. Other
     advisor-type roles (e.g. academic advisor roles) that may also have rows in this table
     are explicitly excluded — they grant no template visibility.
   - If a non-admin user has the `local/etemplate:view` capability but **zero** matching
     `ea_template_manager` assignments, the query **default-denies**: no templates are
     returned at all, rather than silently falling through to an unfiltered (see-everything)
     result. This protects against misconfigured accounts.
   - Otherwise, assignments are separated into: `direct_campusids`, `direct_unitids`, `direct_deptids`.

2. **Extract Shortnames**
   - Campus-assigned users: Extract campus shortnames (e.g., 'YK')
   - Unit-assigned users: Extract unit shortnames (e.g., 'AP') + all dept shortnames within their units
   - Dept-assigned users: Extract only their assigned dept shortnames

3. **Build SQL Conditions**
   - Match `campusctx.id`, `unitctx.id`, `deptctx.id` for context-specific templates
   - Match `e.campus`, `e.faculty`, `e.course` for `campus_course` templates (and **only** for campus_course!)
   - Combine with OR logic so users see any template matching their scope

4. **Filter Results**
   - Only `campus_course` templates are matched by shortnames to prevent scope bleeding
   - Context-specific templates are matched by exact ID

## Important: Department Context Deprecation

**Note**: The `DEPARTMENT` context template type has been deprecated in favor of the `campus_course` type. 

**Current State**:
- No active templates use `context='DEPARTMENT'` in production
- All new alerts are created as `campus_course` type
- Template managers are assigned at `CAMPUS` or `UNIT` level (not DEPARTMENT level)

**However**:
- DEPARTMENT-level scope assignments are still supported for backward compatibility
- Users assigned to a DEPARTMENT see only `campus_course` templates matching that department's shortname
- This can be useful for department-specific course alert management

## SQL Query Examples

### User with CAMPUS Assignment
```sql
WHERE (
  campusctx.id IN (1)  -- Keele campus
  OR (e.template_type = 'campus_course' AND e.campus IN ('YK'))
)
```
Result: Sees all Keele campus-level templates

### User with UNIT Assignment (Faculty Level)
```sql
WHERE (
  unitctx.id IN (6)  -- LAPS unit
  OR (e.template_type = 'campus_course' AND e.faculty IN ('AP'))
  OR (e.template_type = 'campus_course' AND e.course IN ('ADMS', 'ECON', ...))
)
```
Result: Sees LAPS faculty templates and all course templates for LAPS departments

### User with DEPARTMENT Assignment (Course Level)
```sql
WHERE (
  (e.template_type = 'campus_course' AND e.course IN ('ECON'))
)
```
Result: Sees only ECON course-based templates

## Key Implementation Details

### File: `/local/etemplate/email_templates.php`

**Scope Extraction** (Lines 216-271)
- Fetches user's advisor role assignments
- For UNIT assignments: Queries parent unit and derives all departments within that unit
- For DEPT assignments: Only uses directly assigned departments
- Uses `$direct_*ids` to prevent scope inflation when merging derived assignments

**Template Filtering** (Lines 284-320)
- Context templates (CAMPUS, UNIT, DEPT) matched by exact ID
- Campus_course templates matched by shortnames with explicit type check
- Prevents non-campus_course templates from matching on shortname fields

### Shortname Fields in Templates
- `e.campus`: Campus shortname (e.g., 'YK' for York Keele campus)
- `e.faculty`: Faculty/unit shortname (e.g., 'AP' for LAPS)
- `e.course`: Department shortname used as course identifier (e.g., 'ECON', 'ADMS')

These fields are populated at template creation time to enable course-level filtering without querying the organization hierarchy at display time.

## Testing Checklist

When verifying template manager functionality:

- [ ] CAMPUS-assigned user sees campus-level and all course templates
- [ ] CAMPUS-assigned user does NOT see other campus templates
- [ ] UNIT-assigned user sees unit/faculty templates
- [ ] UNIT-assigned user sees all courses in their unit
- [ ] UNIT-assigned user does NOT see other faculty templates
- [ ] UNIT-assigned user does NOT see campus-level (context='CAMPUS') templates
- [ ] DEPT-assigned user sees only their department's courses
- [ ] DEPT-assigned user does NOT see faculty-level templates
- [ ] Template pagination preserves scope filters
- [ ] Clone operation returns user to same filtered list
- [ ] Search/filter context is maintained across page navigation


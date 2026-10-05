<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Email templates list page.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use local_etemplate\base;
use local_etemplate\tables\email_table;
use local_etemplate\forms\email_templates_filter_form;

global $CFG, $OUTPUT, $PAGE, $DB, $USER;

require_login(1, false);

$context = context_system::instance();

$active = optional_param('active', 1, PARAM_INT);

// Capability to view/edit page.
$has_capability_view_edit = has_capability('local/etemplate:view', $PAGE->context, $USER->id);
if (!$has_capability_view_edit) {
    redirect($CFG->wwwroot . '/my');
}

$page_header = get_string('all_email_templates', 'local_etemplate');
// Load AMD module.
$PAGE->requires->js_call_amd('local_etemplate/email_templates', 'init');
// Load CSS file.
$PAGE->requires->css('/local/etemplate/css/general.css');

$term = optional_param('q', '', PARAM_TEXT);

$formdata = new stdClass();
$formdata->name = $term;
$formdata->active = $active;

$mform = new email_templates_filter_form(null, ['formdata' => $formdata]);

// Initialise filter variables before the conditional branches so they are always defined.
$term_filter = '';
$campus_id = 0;

if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present.
    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} elseif ($data = $mform->get_data()) { // Form is submitted with filter.
    // Process validated data.
    $term_filter = $data->q;
    $campus_id = $data->campus_id;
} else {
    // Display the form.
    $mform->display();
}

$table = new email_table('local_etemplate_email_table');
$table->current_active = $active;
$table->current_search = $term;

// If viewing inactive templates, sort by time modified descending.
if ($active == 0) {
    $table->sortable(true, 'timemodified', SORT_DESC);
} else {
    $table->sortable(true, 'name', SORT_ASC);
}

$params = [
    'active' => $active,
];

// Define the SQL query to fetch data.
// Retrieve campus id from form data when submit.

// CK Oct2025: Deprecate department field in favor of context/unit structure.
$fields = "e.id,
    e.name,
    e.lang,
    e.active,
    FROM_UNIXTIME(e.timecreated, '%Y-%m-%d %H:%i') AS timecreated,
    FROM_UNIXTIME(e.timemodified, '%Y-%m-%d %H:%i') AS timemodified,
    CASE
        WHEN e.message_type = 0 THEN 'Low Grade'
        WHEN e.message_type = 1 THEN 'Missed Assignment'
        WHEN e.message_type = 2 THEN 'Missed Test/Quiz'
        WHEN e.message_type = 3 THEN 'Catch All'
        WHEN e.message_type = 4 THEN 'Signature'
        WHEN e.message_type = 5 THEN 'Commendation'
    END AS message_type_name,
    CASE
        WHEN e.template_type = 'campus_course' THEN (
            CASE
                WHEN e.course IS NOT NULL AND e.course != '' THEN CONCAT(coursecampus.name, '/', courseunit.name, '/', coursedept.name, '/Course based alert')
                WHEN e.faculty IS NOT NULL AND e.faculty != '' THEN CONCAT(coursecampus.name, '/', courseunit.name, '/Course based alert')
                WHEN e.campus IS NOT NULL AND e.campus != '' THEN CONCAT(coursecampus.name, '/Course based alert')
                ELSE 'Course based alert'
            END
        )
        WHEN e.context = 'CAMPUS' THEN campusctx.name
        WHEN e.context = 'UNIT' THEN CONCAT(unitcampus.name, '/', unitctx.name)
        WHEN e.context = 'DEPT' THEN CONCAT(deptcampus.name, '/', deptunit.name, '/', deptctx.name)
    END AS department_name";

$from = "{local_et_email} e
    LEFT JOIN {local_organization_campus} campusctx
        ON e.context = 'CAMPUS' AND campusctx.id = e.unit
    LEFT JOIN {local_organization_unit} unitctx
        ON e.context = 'UNIT' AND unitctx.id = e.unit
    LEFT JOIN {local_organization_campus} unitcampus
        ON unitcampus.id = unitctx.campus_id
    LEFT JOIN {local_organization_dept} deptctx
        ON e.context = 'DEPT' AND deptctx.id = e.unit
    LEFT JOIN {local_organization_unit} deptunit
        ON deptunit.id = deptctx.unit_id
    LEFT JOIN {local_organization_campus} deptcampus
        ON deptcampus.id = deptunit.campus_id
    LEFT JOIN {local_organization_campus} coursecampus
        ON e.template_type = 'campus_course'
        AND e.campus IS NOT NULL
        AND e.campus != ''
        AND coursecampus.shortname = e.campus
    LEFT JOIN {local_organization_unit} courseunit
        ON e.template_type = 'campus_course'
        AND e.faculty IS NOT NULL
        AND e.faculty != ''
        AND courseunit.shortname = e.faculty
        AND courseunit.campus_id = coursecampus.id
    LEFT JOIN {local_organization_dept} coursedept
        ON e.template_type = 'campus_course'
        AND e.course IS NOT NULL
        AND e.course != ''
        AND coursedept.shortname = e.course
        AND coursedept.unit_id = courseunit.id";

$sql = 'e.deleted = 0 AND e.active = :active';

$advisor_roles = base::get_advisor_roles();

// Site admins are not subject to organizational scope restrictions (consistent with the
// rest of the plugin, e.g. clone_email.php, delete_email.php, edit_email.php).
if (!is_siteadmin($USER->id)) {
    if (empty($advisor_roles)) {
        // Default-deny: a non-admin viewing this page has the capability but no
        // organizational scope assigned in local_organization_advisor. Rather than
        // silently falling through to an unfiltered (see-everything) query, block
        // all results. Misconfigured accounts should see nothing until a proper
        // scope (CAMPUS/UNIT/DEPARTMENT) is assigned.
        $sql .= ' AND 1 = 0';
    } else {
    $conditions = [];
    $paramindex = 0;
    $campusids = [];
    $unitids = [];
    $deptids = [];
    $derivedcampusids = [];
    $derivedunitids = [];

    foreach ($advisor_roles as $context => $instances) {
        $instanceids = array_column($instances, 'instance_id');
        if (empty($instanceids)) {
            continue;
        }

        switch ($context) {
            case 'CAMPUS':
                $campusids = array_merge($campusids, $instanceids);
                break;
            case 'UNIT':
                $unitids = array_merge($unitids, $instanceids);
                break;
            case 'DEPARTMENT':
            case 'DEPT':
                $deptids = array_merge($deptids, $instanceids);
                break;
        }
    }

    $campusids = array_values(array_unique($campusids));
    $unitids = array_values(array_unique($unitids));
    $deptids = array_values(array_unique($deptids));

    // Snapshot the directly-assigned IDs now, before any derived IDs (from parent-scope
    // expansion below) get merged in. These are used later to ensure inherited/derived
    // scope is never mistaken for a direct assignment.
    $direct_campusids = $campusids;
    $direct_unitids = $unitids;
    $direct_deptids = $deptids;

    $campusshortnames = [];
    $facultyshortnames = [];
    $deptshortnames = [];

    $add_in_condition = static function(string $field, array $values, string $prefix, array &$params, array &$conditions, int &$paramindex): void {
        if (empty($values)) {
            return;
        }

        $placeholders = [];
        foreach ($values as $value) {
            $paramkey = $prefix . '_' . $paramindex;
            $params[$paramkey] = $value;
            $placeholders[] = ':' . $paramkey;
            $paramindex++;
        }

        $conditions[] = $field . ' IN (' . implode(', ', $placeholders) . ')';
    };

    if (!empty($campusids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($campusids, SQL_PARAMS_NAMED, 'campusscope');
        $campusrecords = $DB->get_records_select_menu('local_organization_campus', 'id ' . $insql, $inparams, '', 'id, shortname');
        $campusshortnames = array_values(array_filter($campusrecords));
    }

    if (!empty($unitids)) {
         [$insql, $inparams] = $DB->get_in_or_equal($unitids, SQL_PARAMS_NAMED, 'unitscope');
         $unitrecords = $DB->get_records_sql(
             "SELECT ou.id, ou.shortname AS facultyshortname, oc.shortname AS campusshortname, oc.id AS campusid
                FROM {local_organization_unit} ou
                JOIN {local_organization_campus} oc ON oc.id = ou.campus_id
               WHERE ou.id $insql",
             $inparams
         );

         foreach ($unitrecords as $unitrecord) {
             $facultyshortnames[] = $unitrecord->facultyshortname;
             // Only add parent campus shortname if user is directly assigned to CAMPUS scope.
             // Unit-assigned users should only see unit-level templates, not campus-wide ones.
             if (!empty($campusids) && in_array($unitrecord->campusid, $campusids)) {
                 $campusshortnames[] = $unitrecord->campusshortname;
             }
         }

         // Fetch all departments in user's directly assigned units
         // Only do this for direct UNIT assignments, not derived ones from DEPT assignments
         [$insql_u, $inparams_u] = $DB->get_in_or_equal($direct_unitids, SQL_PARAMS_NAMED, 'unitsfordept');
         $all_unit_depts = $DB->get_records_sql(
             "SELECT od.shortname AS deptshortname
                FROM {local_organization_dept} od
               WHERE od.unit_id $insql_u",
             $inparams_u
         );
         foreach ($all_unit_depts as $deptrecord) {
             $deptshortnames[] = $deptrecord->deptshortname;
         }
      }

    if (!empty($deptids)) {
         [$insql, $inparams] = $DB->get_in_or_equal($deptids, SQL_PARAMS_NAMED, 'deptscope');
         $deptrecords = $DB->get_records_sql(
             "SELECT od.id,
                     od.shortname AS deptshortname,
                     ou.shortname AS facultyshortname,
                     ou.id AS unitid,
                     oc.id AS campusid,
                     oc.shortname AS campusshortname
                FROM {local_organization_dept} od
                JOIN {local_organization_unit} ou ON ou.id = od.unit_id
                JOIN {local_organization_campus} oc ON oc.id = ou.campus_id
               WHERE od.id $insql",
             $inparams
         );

         foreach ($deptrecords as $deptrecord) {
             $deptshortnames[] = $deptrecord->deptshortname;
             // Only add parent faculty/unit shortname if user is directly assigned to UNIT scope.
             // Dept-assigned users should only see dept-level templates.
             if (!empty($unitids) && in_array($deptrecord->unitid, $unitids)) {
                 $facultyshortnames[] = $deptrecord->facultyshortname;
             }
             // Only add parent campus shortname if user is directly assigned to CAMPUS scope.
             // Dept-assigned users should not see campus-wide templates.
             if (!empty($campusids) && in_array($deptrecord->campusid, $campusids)) {
                 $campusshortnames[] = $deptrecord->campusshortname;
             }
             if (!empty($deptrecord->unitid)) {
                 $derivedunitids[] = (int) $deptrecord->unitid;
             }
             if (!empty($deptrecord->campusid)) {
                 $derivedcampusids[] = (int) $deptrecord->campusid;
             }
         }
     }

    // Merge derived (inherited) IDs into the main scope arrays now, for shortname extraction
    // and display purposes. SQL conditions further below use $direct_*ids exclusively so
    // derived/inherited scope never grants direct-context visibility it shouldn't have.
    $unitids = array_values(array_unique(array_merge($unitids, $derivedunitids)));
    $campusids = array_values(array_unique(array_merge($campusids, $derivedcampusids)));
    $campusshortnames = array_values(array_unique(array_filter($campusshortnames)));
    $facultyshortnames = array_values(array_unique(array_filter($facultyshortnames)));
    $deptshortnames = array_values(array_unique(array_filter($deptshortnames)));

    $add_in_condition('campusctx.id', $direct_campusids, 'campusexact', $params, $conditions, $paramindex);

    // Unit/department advisors can also see and edit the campus-level templates of the campus
    // their unit/department belongs to (matches base::user_can_access_unit_value()).
    $parentcampusids = [];
    if (!empty($direct_unitids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($direct_unitids, SQL_PARAMS_NAMED, 'pcunit');
        $parentcampusids = array_merge($parentcampusids,
            $DB->get_fieldset_select('local_organization_unit', 'campus_id', "id $insql", $inparams));
    }
    if (!empty($direct_deptids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($direct_deptids, SQL_PARAMS_NAMED, 'pcdept');
        $parentcampusids = array_merge($parentcampusids, $DB->get_fieldset_sql(
            "SELECT u.campus_id
               FROM {local_organization_dept} d
               JOIN {local_organization_unit} u ON u.id = d.unit_id
              WHERE d.id $insql",
            $inparams
        ));
    }
    $parentcampusids = array_values(array_unique(array_map('intval', $parentcampusids)));
    $add_in_condition('campusctx.id', $parentcampusids, 'campusparent', $params, $conditions, $paramindex);
    $add_in_condition('unitctx.id', $direct_unitids, 'unitexact', $params, $conditions, $paramindex);
    $add_in_condition('deptctx.id', $direct_deptids, 'deptexact', $params, $conditions, $paramindex);

    // Only show UNIT templates in user's campus if they have direct CAMPUS assignment
    // This prevents UNIT-assigned users from seeing all units in their parent campus
    $add_in_condition('unitctx.campus_id', $direct_campusids, 'campusunit', $params, $conditions, $paramindex);
    $add_in_condition('deptunit.campus_id', $direct_campusids, 'campusdept', $params, $conditions, $paramindex);
    $add_in_condition('deptctx.unit_id', $direct_unitids, 'unitdept', $params, $conditions, $paramindex);

     // Only filter by shortnames for campus_course templates
     // to avoid matching campus/unit/dept context templates at the wrong level
     if (!empty($campusshortnames) || !empty($facultyshortnames) || !empty($deptshortnames)) {
         $shortname_conditions = [];

         if (!empty($campusshortnames)) {
             [$insql, $inparams] = $DB->get_in_or_equal($campusshortnames, SQL_PARAMS_NAMED, 'campusshort');
             $shortname_conditions[] = "(e.template_type = 'campus_course' AND e.campus $insql)";
             $params = array_merge($params, $inparams);
         }

         if (!empty($facultyshortnames)) {
             [$insql, $inparams] = $DB->get_in_or_equal($facultyshortnames, SQL_PARAMS_NAMED, 'facultyshort');
             $shortname_conditions[] = "(e.template_type = 'campus_course' AND e.faculty $insql)";
             $params = array_merge($params, $inparams);
         }

         if (!empty($deptshortnames)) {
             [$insql, $inparams] = $DB->get_in_or_equal($deptshortnames, SQL_PARAMS_NAMED, 'deptshort');
             $shortname_conditions[] = "(e.template_type = 'campus_course' AND e.course $insql)";
             $params = array_merge($params, $inparams);
         }

         if (!empty($shortname_conditions)) {
             $conditions[] = '(' . implode(' OR ', $shortname_conditions) . ')';
         }
     }

     if (!empty($conditions)) {
         $sql .= ' AND (' . implode(' OR ', $conditions) . ')';
     }
    }
}

if (!empty($term_filter)) {
    $sql .= ' AND ' . $DB->sql_like('e.name', ':term_filter', false);
    $params['term_filter'] = '%' . $DB->sql_like_escape($term_filter) . '%';
}
// Define the SQL query to fetch data.
$table->set_sql($fields, $from, $sql, $params);

// Define the base URL for the table.
// Include active and search term filters in the base URL for pagination to work correctly.
$table->define_baseurl(new moodle_url('/local/etemplate/email_templates.php', ['active' => $active, 'q' => $term]));

base::page(
    new moodle_url('/local/etemplate/email_templates.php'),
    $page_header,
    $page_header
);

echo $OUTPUT->header();
echo get_string('email_template_header', 'local_etemplate');
// Set up the table.
$mform->display();
$table->out(20, true);
echo $OUTPUT->footer();

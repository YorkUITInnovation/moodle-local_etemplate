<?php
require_once('../../config.php');
include_once('classes/tables/email_table.php');
include_once('classes/forms/email_templates_filter_form.php');

use local_etemplate\base;
use local_etemplate\tables\email_table;
use local_etemplate\forms\email_templates_filter_form;

global $CFG, $OUTPUT, $PAGE, $DB, $USER;


require_login(1, false);

$context = context_system::instance();

$active = optional_param('active', 1, PARAM_INT);

// Capability to view/edit page
$has_capability_view_edit = has_capability('local/etemplate:view', $PAGE->context, $USER->id);
if (!$has_capability_view_edit) {
    redirect($CFG->wwwroot . '/my');
}

$page_header = get_string('all_email_templates', 'local_etemplate');
// Load AMD module
$PAGE->requires->js_call_amd('local_etemplate/email_templates', 'init');
// Load CSS file
$PAGE->requires->css('/local/etemplate/css/general.css');

$term = optional_param('q', '', PARAM_TEXT);

$formdata = new stdClass();
$formdata->name = $term;
$formdata->active = $active;
//
//
$mform = new email_templates_filter_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present
    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} else if ($data = $mform->get_data()) { // form is submitted with filter
    // Process validated data
    $term_filter = $data->q;
    $campus_id = $data->campus_id;
} else {
    // Display the form
    $mform->display();
}

$table = new email_table('local_etemplate_email_table');
$params = array();
// Define the SQL query to fetch data
//retrieve campus id from form data when submit

$fields = "e.id,
    e.parent_id,
    e.name,
    e.subject,
    e.lang,
    e.message,
    e.unit,
    e.context,
    e.active,
    e.message_type,
    e.system_reserved,
    e.revision,
    e.deleted,
    e.faculty,
    e.course,
    e.coursenumber,
    e.hascustommessage,
    e.template_type,
    e.usermodified,
    FROM_UNIXTIME(e.timecreated, '%Y-%m-%d %H:%i') as timecreated,
    FROM_UNIXTIME(e.timemodified, '%Y-%m-%d %H:%i') as timemodified,
    Case
    When e.message_type = 0
    Then 'Low Grade'
    When e.message_type = 1
    Then 'Missed assignment'
    When e.message_type = 2
    Then 'Missed exam'
    When e.message_type = 3
    Then 'Catch all'
    End As message_type_name,
    Case
        When e.context = 'CAMPUS'
        Then (Select
                 id
             From
                 {local_organization_campus}
             Where
                 id = e.unit)
        When e.context = 'UNIT'
        Then (Select
                 id
             From
                 {local_organization_unit}
             Where
                 id = e.unit)
        When e.context = 'DEPT'
        Then (Select
                 id
             From
                 {local_organization_dept}
             Where
                 id = e.unit)
    End As organization_id,
     Case
        When e.context = 'CAMPUS'
        Then (Select
                 name
             From
                 {local_organization_campus}
             Where
                 id = e.unit)
        When e.context = 'UNIT'
        Then (Select
   concat(c.name, '/', unit.name) as name
From
    {local_organization_campus} c Inner Join
    {local_organization_unit} unit On c.id = unit.campus_id
             Where
                 unit.id = e.unit)
        When e.context = 'DEPT'
        Then (Select
    Concat(ocampus.name, '/', ounit.name, '/', odept.name) As name
From
    {local_organization_campus} ocampus Inner Join
    {local_organization_unit} ounit On ocampus.id = ounit.campus_id Inner Join
    {local_organization_dept} odept On odept.unit_id = ounit.id
             Where
                 odept.id = e.unit)
    End As department_name";

$sql = 'e.deleted = 0 ';
$sql .= 'AND e.active = ' . $active;

$advisor_roles = base::get_adivsor_roles();

$where_clause = '';
if ($advisor_roles) {
    $conditions = [];
    $user_unit_ids = [];

    foreach ($advisor_roles as $context => $instances) {
        $instance_ids = array_column($instances, 'instance_id');

        // Collect user's unit IDs for course-based templates
        if ($context == 'UNIT') {
            $user_unit_ids = array_merge($user_unit_ids, $instance_ids);
        }

        // Convert DEPARTMENT to DEPT
        if ($context == 'DEPARTMENT') {
            $context = 'DEPT';
        }
        // Templates for advisor's specific organizational contexts
        $conditions[] = "(unit IN (" . implode(',', $instance_ids) . ") AND context = '$context')";
    }

    // Show course-based alert templates (context IS NULL) for user's faculty/unit
    if (!empty($user_unit_ids)) {
        $conditions[] = "(faculty IN (SELECT shortname FROM {local_organization_unit} WHERE id IN (" . implode(',', $user_unit_ids) . ")) AND context IS NULL)";
    }

    $where_clause = ' AND (' . implode(' OR ', $conditions) . ')';

    $sql .= $where_clause;
}

if (!empty($term_filter)) {
    $sql .= " AND ((LOWER(e.name) LIKE '%$term_filter%'))";
}
// Define the SQL query to fetch data
$table->set_sql($fields, '{local_et_email} e', $sql);

// Define the base URL for the table
$table->define_baseurl(new moodle_url('/local/etemplate/email_templates.php'));

base::page(
    new moodle_url('/local/etemplate/email_templates.php'),
    $page_header,
    $page_header
);

echo $OUTPUT->header();
echo get_string('email_template_header', 'local_etemplate');
// Set up the table
$mform->display();
$table->out(20, true);
echo $OUTPUT->footer();

<?php
require_once("../../config.php");

use local_etemplate\base;
use local_etemplate\emails;
use local_organization\unit;
use local_organization\units;
use local_organization\department;
use local_organization\departments;

$context = context_system::instance();
$PAGE->set_context($context);
$page_header = get_string('all_email_templates', 'local_etemplate');
$emails = new emails();
$alltemplates = $emails->get_records();
$units = new units();
$allunits = $units->get_records();
$table = new html_table();
$table->id = 'local_etemplates_email_list';
$content = "";

//pre-table content
$content .= "";

$table->head = ['Unit', 'Name', 'Subject', 'Language', 'Active', 'Time Created', 'Time Modified', 'Actions'];
foreach ($alltemplates as $template){
    //deletion checks
    if ($template->deleted == 1 && !(has_capability('local/etemplate:view_system_reserved', $context) || is_siteadmin($USER->id))) {
        continue;
    }
    //permissions checks
    $viewbutton = $OUTPUT->single_button(new moodle_url('/local/etemplate/view_email.php', ['id' => $template->id]), get_string('view'), 'get');
    $editbutton = $OUTPUT->single_button(new moodle_url('/local/etemplate/edit_email.php', ['id' => $template->id]), get_string('edit'), 'get', ['id' => $template->id]);
    $deleteinfo = new stdClass();
    $deleteinfo->unit = $template->unit;
    $deleteinfo->name = $template->name;
    if ($template->deleted == 1){
        $deletebutton = $OUTPUT->single_button('#', get_string('undelete', 'local_etemplate'), 'get', [
            'data-modal' => 'confirmation',
            'data-modal-title-str' => json_encode(['undelete', 'local_etemplate']),
            'data-modal-content-str' => json_encode(['confirm_undelete_email','local_etemplate', $deleteinfo]),
            'data-modal-yes-button-str' => json_encode(['undelete', 'local_etemplate']),
            'data-modal-destination' => new moodle_url('/local/etemplate/undelete_email.php', ['id' => $template->id])
        ]);
    } else {
        $deletebutton = $OUTPUT->single_button('#', get_string('delete'), 'get', [
            'data-modal' => 'confirmation',
            'data-modal-title-str' => json_encode(['delete', 'core']),
            'data-modal-content-str' => json_encode(['confirm_delete_email', 'local_etemplate', $deleteinfo]),
            'data-modal-yes-button-str' => json_encode(['delete', 'core']),
            'data-modal-destination' => new moodle_url('/local/etemplate/delete_email.php', ['id' => $template->id])
        ]);
    }
    $actions = $viewbutton . $editbutton . $deletebutton;
    if (is_numeric($template->unit)) {
        $unit = new unit($template->unit);
	$unitinfo = $unit->get_name();
    } else {
        $explodedUnit = explode("_", $template->unit);
        if (count($explodedUnit) == 2) {
            $newUnit = new unit($explodedUnit[0]);
            $newDepartment = new department($explodedUnit[1]);
	    $unitinfo = $newUnit->get_name() . $newDepartment->get_name();
        } else {
            // handle error case where unit is not numeric and does not have two parts separated by "_"
        }
    }

    $row = new html_table_row();
    if ($template->deleted == 1) {
        $row->attributes['class'] = 'disabled';
    }
    $row->cells = array($unitinfo, $template->name, $template->subject, $template->lang, $template->active, $template->timecreated, $template->timemodified,$actions);
    $table->data[] = $row;
}
$content .= html_writer::table($table);

//post-table content
$content .= $OUTPUT->single_button(new moodle_url('/local/etemplate/edit_email.php'), get_string('add_email_template', 'local_etemplate'));




echo base::page(
    new moodle_url('/local/etemplate/email_templates.php'),
    $page_header,
    $page_header,
    $context
);

//**********************
echo $OUTPUT->header();
//*** DISPLAY HEADER ***
//
echo $content;
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();

?>

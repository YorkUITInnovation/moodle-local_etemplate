<?php
require_once("../../config.php");

use local_etemplate\base;
use local_etemplate\emails;
use local_organization\unit;
use local_organization\units;
use local_organization\department;
use local_organization\departments;
use local_organization\advisors;

$context = context_system::instance();
$PAGE->set_context($context);
//define a required capability to be able to access this page
require_capability('local/etemplate:view', $context);

$errmsg = optional_param('errormsg','',PARAM_TEXT);
if (!empty($errmsg)){
    $notification = new \core\notification();
    $messagetext = get_string('message_' . $errmsg, 'local_etemplate');
    $errormessage = $notification->error($messagetext, '');
} else {
    $errormessage = '';
}

$page_header = get_string('all_email_templates', 'local_etemplate');
$emails = new emails();
$alltemplates = $emails->get_active_records();
$units = new units();
$allunits = $units->get_records();
$table = new html_table();
$table->id = 'local_etemplates_email_list';
$content = "";

//pre-table content
$content .= "";

//get user advisor info
$advisors = new advisors();
$alladvisors = $advisors->get_records();
$userperms = [];
foreach ($alladvisors as $advisor){
    if ($advisor->user_id == $USER->id){
        $userperms[] = $advisor;
    }
}

$table->head = ['Unit', 'Type', 'Name', 'Subject', 'Language', 'Time Created', 'Time Modified', 'Actions'];
foreach ($alltemplates as $template){
    $permissioninfo = \local_etemplate\base::getTemplatePermissions($template->unit, $context, $USER->id);
    $candelete = $permissioninfo['canDelete'];
    $canundelete = $permissioninfo['canUndelete'];
    $canedit = $permissioninfo['canEdit'];
    $canview = $permissioninfo['canView'];
    $canviewsystem = $permissioninfo['canViewSystemReserved'];
    //skip checks
    if (is_siteadmin($USER->id)){
        //let 'em see all the things
        $candelete = true;
        $canundelete = true;
        $canedit = true;
        $canview = true;
        $canviewsystem = true;
    } elseif ($template->deleted == 1 && !(has_capability('local/etemplate:view_system_reserved', $context))) {
        continue;
    } elseif (!$canview || ($template->deleted == 1 && $canundelete !== true)){
        continue;
    }
    //build table based off data
    if ($canview === true){
        $viewbutton = $OUTPUT->single_button(new moodle_url('/local/etemplate/view_email.php', ['id' => $template->id]), get_string('view'), 'get');
    } else {
        $viewbutton = '';
    }
    if ($canedit === true && $template->system_reserved == 0){
        $editbutton = $OUTPUT->single_button(new moodle_url('/local/etemplate/edit_email.php', ['id' => $template->id]), get_string('edit'), 'get', ['id' => $template->id]);
    } else {
        $editbutton = '';
    }
    if ($candelete && $template->system_reserved == 1){
        $deletebutton = '';
    } elseif ($candelete && $template->deleted == 0){
        $deleteinfo = new stdClass();
        $deleteinfo->unit = $permissioninfo['unitInfo'];
        $deleteinfo->name = $template->name;
        $deletebutton = $OUTPUT->single_button('#', get_string('delete'), 'get', [
            'data-modal' => 'confirmation',
            'data-modal-title-str' => json_encode(['delete', 'core']),
            'data-modal-content-str' => json_encode(['confirm_delete_email', 'local_etemplate', $deleteinfo]),
            'data-modal-yes-button-str' => json_encode(['delete', 'core']),
            'data-modal-destination' => new moodle_url('/local/etemplate/delete_email.php', ['id' => $template->id])
        ]);
    } elseif ($canundelete && $template->deleted == 1){
        $deleteinfo = new stdClass();
        $deleteinfo->unit = $permissioninfo['unitInfo'];
        $deleteinfo->name = $template->name;
        $deletebutton = $OUTPUT->single_button('#', get_string('undelete', 'local_etemplate'), 'get', [
            'data-modal' => 'confirmation',
            'data-modal-title-str' => json_encode(['undelete', 'local_etemplate']),
            'data-modal-content-str' => json_encode(['confirm_undelete_email','local_etemplate', $deleteinfo]),
            'data-modal-yes-button-str' => json_encode(['undelete', 'local_etemplate']),
            'data-modal-destination' => new moodle_url('/local/etemplate/undelete_email.php', ['id' => $template->id])
        ]);
    } else {
        $deletebutton = '';
    }

    $actions = $viewbutton . $editbutton . $deletebutton;

    $row = new html_table_row();

    if ($template->deleted == 1) {
        $row->attributes['class'] = 'disabled deleted';
    } elseif ($template->active == 0 && $template->deleted == 0) {
        $row->attributes['class'] = 'disabled deactivated';
    } elseif ($template->active == 1 && $template->deleted == 0) {
        $row->attributes['class'] = 'active';
    }

    $row->cells = array($permissioninfo['unitInfo'], \local_etemplate\email::get_messagetype_nicename($template->message_type), $template->name, $template->subject, $template->lang, date('m/d/Y H:i', $template->timecreated), date('m/d/Y H:i', $template->timemodified),$actions);
    $table->data[] = $row;
}
$content .= html_writer::table($table);

//post-table content

if (has_capability('local/etemplate:create', $context)) {
    //can create email templates
    $cancreate = true;
} else {
    $cancreate = false;
}
if ($cancreate === true) {
    $content .= $OUTPUT->single_button(new moodle_url('/local/etemplate/edit_email.php'), get_string('add_email_template', 'local_etemplate'));
}

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
echo $errormessage;
echo $content;
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();

?>

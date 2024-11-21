<?php
require_once("../../config.php");

use local_etemplate\base;
use local_etemplate\email;

$id = required_param('id', PARAM_INT);

$errmsg = optional_param('errormsg','',PARAM_TEXT);
if (!empty($errmsg)){
    $notification = new \core\notification();
    $messagetext = get_string('message_' . $errmsg, 'local_etemplate');
    $errormessage = $notification->error($messagetext, '');
} else {
    $errormessage = '';
}

$context = context_system::instance();
$PAGE->set_context($context);
$page_header = get_string('email_template', 'local_etemplate');
$email = new email($id);

$table = new html_table();
$table->id = 'local_etemplates_email_list';
$content = "";

//pre-table content
$content .= "";

$table->head = ['Name', 'Active', 'Subject', 'Message', 'Message Demo', 'Language', 'Message Type', 'System Reserved', 'Deleted', 'Time Created', 'Time Modified'];
$row = new html_table_row();
$row->cells = array($email->get_name(), $email->get_active(), $email->get_subject(), $email->get_message(), $email->preload_template(), $email->get_lang(), $email->get_messagetype_nicename($email->get_messagetype()), $email->get_system_reserved(), $email->get_deleted(), date('m/d/Y H:i', $email->get_timecreated()), date('m/d/Y H:i', $email->get_timemodified()));

$table->data[] = $row;
$content .= html_writer::table($table);

//post-table content
$content .= $OUTPUT->single_button(new moodle_url('/local/etemplate/email_templates.php'), get_string('return_to_templates', 'local_etemplate'));




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

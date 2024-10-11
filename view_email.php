<?php
require_once("../../config.php");

use local_etemplate\base;
use local_etemplate\email;

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$page_header = get_string('email_template', 'local_etemplate');
$email = new email($id);

$table = new html_table();
$table->id = 'local_etemplates_email_list';
$content = "";

//pre-table content
$content .= "";

$table->head = ['id', 'Name', 'Unit', 'Subject', 'Message', 'Language', 'Message Type', 'System Reserved', 'Active', 'Deleted', 'Time Created', 'Time Modified'];
$row = new html_table_row();
$row->cells = array($email->get_id(), $email->get_name(), $email->get_unit(), $email->get_subject(), $email->get_message(), $email->get_lang(), $email->get_messagetype(), $email->get_systemreserved(), $email->get_active(), $email->get_deleted(), $email->get_timecreated(), $email->get_timemodified());
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
echo $content;
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();

?>

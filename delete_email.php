<?php

require_once('../../config.php');

use local_etemplate\base;
use local_etemplate\email;

require_login(1, false);

global $CFG, $DB, $USER, $OUTPUT, $PAGE;

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);
$undelete = optional_param('undelete', 0, PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/etemplate/delete_email.php', ['id' => $id]);

$template = new email($id);
if (!$template->get_id()) {
    print_error('email_template_not_found', 'local_etemplate', $CFG->wwwroot . '/local/etemplate/email_templates.php');
}

if ($undelete) {
    $capability = 'local/etemplate:undelete';
    $page_header = get_string('undelete');
} else {
    $capability = 'local/etemplate:delete';
    $page_header = get_string('delete');
}

if (!has_capability($capability, $context)) {
    print_error('nopermissions', 'error', $CFG->wwwroot . '/local/etemplate/email_templates.php');
}

if ($confirm && confirm_sesskey()) {
    if ($undelete) {
        $template->undelete_email($id);
        \core\notification::success(get_string('undeletesuccess', 'local_etemplate', $template->get_name()));
    } else {
        $template->delete_email();
        \core\notification::success(get_string('deletesuccess', 'local_etemplate', $template->get_name()));
    }
    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} else {
    base::page(
        new moodle_url('/local/etemplate/delete_email.php', ['id' => $id]),
        $page_header,
        $page_header,
        $context
    );

    echo $OUTPUT->header();

    if ($undelete) {
        $message = get_string('confirm_undelete_email', 'local_etemplate', $template->get_record());
        $url = new moodle_url('/local/etemplate/delete_email.php', ['id' => $id, 'confirm' => 1, 'undelete' => 1, 'sesskey' => sesskey()]);
    } else {
        $message = get_string('confirm_delete_email', 'local_etemplate', $template->get_record());
        $url = new moodle_url('/local/etemplate/delete_email.php', ['id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]);
    }

    echo $OUTPUT->confirm(
        $message,
        $url,
        new moodle_url('/local/etemplate/email_templates.php')
    );
    echo $OUTPUT->footer();
}

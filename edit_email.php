<?php

require_once("../../config.php");
require_once ($CFG->dirroot . "/local/etemplate/classes/forms/email.php");

use local_etemplate\base;
use local_etemplate\email;

require_login(1, FALSE);

global $CFG, $OUTPUT, $USER, $PAGE, $DB, $SITE;

$id = optional_param(
    'id',
    0,
    PARAM_INT
);

$context = context_system::instance();

if ($id) {
    $EMAIL = new email($id);
    $formdata = $EMAIL->get_record();

    $draftid = file_get_submitted_draft_itemid('messagebodyeditor');
    $current_text = file_prepare_draft_area(
        $draftid,
        $context->id,
        'local_etemplate',
        'emailtemplate',
        $formdata->id,
        base::get_editor_options($context),
        $formdata->message)
    ;
    $formdata->messagebodyeditor = [
        'text' => $current_text,
        'format' => FORMAT_HTML,
        'itemid' => $draftid
    ];

    $page_header = get_string('edit_email_template', 'local_etemplate');
} else {
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->parentid = 0;
    $page_header = get_string('add_email_template', 'local_etemplate');
}

$mform = new local_etemplate\email_form(
    null,
    ['formdata' => $formdata]
);
//    $PAGE->requires->js_call_amd('', 'init');
$context = CONTEXT_SYSTEM::instance();

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} else if ($data = $mform->get_data()) {
    $EMAIL = new email($data->id);
    if ($data->id == 0) {
        $data->id = $EMAIL->insert_record($data);
    } else {
        //update
        $EMAIL->update_record($data);
    }

    //save editor text
    $draftid = file_get_submitted_draft_itemid('messagebodyeditor');
    $message_text = file_save_draft_area_files(
        $draftid,
        $context->id,
        'local_etemplate',
        'emailtemplate',
        $data->id,
        base::get_editor_options($context),
        $data->messagebodyeditor['text']
    );
    $data->message = $message_text;
    $DB->update_record($EMAIL->get_table(), $data);

    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} else {
    // this emailtemplate is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $mform->set_data($mform);
}



echo base::page(
    new moodle_url('/local/etemplate/edit_email.php', ['id' => $id]),
    $page_header,
    $page_header,
    $context
);

//**********************
echo $OUTPUT->header();
//*** DISPLAY HEADER ***
//
$mform->display();
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();
?>
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

$view = optional_param(
    'view',
    0,
    PARAM_INT
);


$context = context_system::instance();

if ($id) {
    $EMAIL = new email($id);

    $formdata = $EMAIL->get_record();
    $formdata->view = $view;
    if ($EMAIL->get_context()) {
        $formdata->unit = $EMAIL->get_unit() . '_' . $EMAIL->get_context();
    }

    // Determine association_type for the form.
    if (!empty($formdata->campus_id) && !empty($formdata->faculty)) {
        $formdata->association_type = 'org_unit_course';
    } else if (!empty($formdata->unit)) {
        $formdata->association_type = 'org_unit';
    } else {
        $formdata->association_type = 'course';
    }

    // Ensure hascustommessage is set for the form
    $formdata->hascustommessage = isset($formdata->hascustommessage) ? $formdata->hascustommessage : 0;

    $unit = $EMAIL->get_unit();
    $context = context_system::instance();

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
    $formdata->view = 0;
    $formdata->id = 0;
    $formdata->parentid = 0;
    $formdata->hascustommessage = 0;
    $page_header = get_string('add_email_template', 'local_etemplate');
}

$mform = new local_etemplate\email_form(
    null,
    ['formdata' => $formdata]
);

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} else if ($data = $mform->get_data()) {
    $EMAIL = new email($data->id);

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

    // Save hascustommessage
    $data->hascustommessage = isset($data->hascustommessage) ? $data->hascustommessage : 0;

    if ($data->id == 0) {
        $data->id = $EMAIL->insert_record($data);
    } else {
        //update
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        $EMAIL->update_record($data);
    }

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

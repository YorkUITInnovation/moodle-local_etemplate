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
 * Edit email template page.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once(__DIR__ . '/classes/forms/email_form.php');

use local_etemplate\base;
use local_etemplate\email;
use local_etemplate\forms\email_form;

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

    // Determine template_type for the form.
  //  if (!empty($formdata->course) || !empty($formdata->coursenumber) || !empty($formdata->section)) {
    if (!empty($formdata->course) || !empty($formdata->coursenumber)) {
        $formdata->template_type = email::TEMPLATE_TYPE_CAMPUS_COURSE; // case where faculty staff is responsible for specific course

        // For course templates, we need to reconstruct the 'unit' value from campus/faculty/department.
        if (empty($formdata->unit)) {
            $formdata->unit = base::get_unit_value_from_template_data($formdata);
        }
    } else {
        $formdata->template_type = email::TEMPLATE_TYPE_CAMPUS_FACULTY; // normal case where campus and faculty are responsible  for multiple courses
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

// Debug: Verify class exists
if (!class_exists('\local_etemplate\forms\email_form')) {
    debugging('Class local_etemplate\forms\email_form does not exist', DEBUG_DEVELOPER);
    $class_file = __DIR__ . '/classes/forms/email_form.php';
    debugging('Looking for file: ' . $class_file, DEBUG_DEVELOPER);
    debugging('File exists: ' . (file_exists($class_file) ? 'YES' : 'NO'), DEBUG_DEVELOPER);
}

$mform = new \local_etemplate\forms\email_form(
    null,
    ['formdata' => $formdata]
);

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form dd
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

    $success = false;
    if ($data->id == 0) {
        $newid = $EMAIL->insert_record($data);
        if ($newid) {
            $data->id = $newid;
            $success = true;
        }
    } else {
        //update
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        if ($EMAIL->update_record($data)) {
            $success = true;
        }
    }

    if ($success) {
        \core\notification::success(get_string('savessuccess', 'local_etemplate'));
    } else {
        \core\notification::error(get_string('saveerror', 'local_etemplate'));
    }

    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
} else {
    // this emailtemplate is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $mform->set_data($formdata);
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

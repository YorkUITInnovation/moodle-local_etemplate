<?php

require_once("../../config.php");

use local_etemplate\base;
use local_etemplate\email;

require_login(1, FALSE);

global $CFG, $OUTPUT, $USER, $PAGE, $DB, $SITE;

$id = required_param('id', PARAM_INT);

$context = context_system::instance();

// Capability to view/edit page - same as delete button permissions
$has_capability_view_edit = has_capability('local/etemplate:view', $PAGE->context, $USER->id);
if (!$has_capability_view_edit) {
    redirect($CFG->wwwroot . '/my');
}

if ($id) {
    // Load the original email template
    $original_email = new email($id);
    $original_data = $original_email->get_record();

    if (!$original_data) {
        redirect($CFG->wwwroot . '/local/etemplate/email_templates.php',
                 get_string('email_template_not_found', 'local_etemplate'),
                 null,
                 \core\output\notification::NOTIFY_ERROR);
    }

    // Create a new email template instance for cloning
    $cloned_email = new email(0);

    // Prepare data for the cloned template
    $clone_data = clone $original_data;
    unset($clone_data->id); // Remove ID so a new one will be generated
    $clone_data->name = $clone_data->name . ' (Copy)';
    $clone_data->timecreated = time();
    $clone_data->timemodified = time();
    $clone_data->usermodified = $USER->id;
    $clone_data->revision = 1; // Reset revision for new template

    // CRITICAL FIX: The email class expects unit in combined format "unit_id_context"
    // Instead of setting unit and context separately, combine them as the class expects
    $clone_data->unit = $original_data->unit . '_' . $original_data->context;

    // Ensure all other critical fields are preserved
    $clone_data->parent_id = isset($original_data->parent_id) ? $original_data->parent_id : 0;
    $clone_data->subject = $original_data->subject;
    $clone_data->message = $original_data->message;
    $clone_data->active = isset($original_data->active) ? $original_data->active : 1;
    $clone_data->message_type = $original_data->message_type;
    $clone_data->system_reserved = 0; // New cloned template should not be system reserved
    $clone_data->deleted = 0;
    $clone_data->faculty = isset($original_data->faculty) ? $original_data->faculty : '';
    $clone_data->course = isset($original_data->course) ? $original_data->course : '';
    $clone_data->coursenumber = isset($original_data->coursenumber) ? $original_data->coursenumber : '';
    $clone_data->lang = isset($original_data->lang) ? $original_data->lang : 'en';

    // Debug: Log the values being cloned and verify data integrity
    error_log("Cloning template - Original unit: " . $original_data->unit . ", context: " . $original_data->context);
    error_log("Clone data combined unit: " . $clone_data->unit);

    // Verify that the unit and context values are not null or empty
    if (empty($original_data->unit) || empty($original_data->context)) {
        error_log("ERROR: Original unit or context is empty - unit: '" . $original_data->unit . "', context: '" . $original_data->context . "'");
        redirect($CFG->wwwroot . '/local/etemplate/email_templates.php',
                 'Clone failed: Missing unit or context data in original template',
                 null,
                 \core\output\notification::NOTIFY_ERROR);
    }

    // Additional debug: Check if the original template shows correctly in the list
    error_log("Original template message_type: " . $original_data->message_type);
    error_log("Clone template message_type: " . $clone_data->message_type);

    // Handle file area cloning for the message content
    $original_context = context_system::instance();

    // Insert the cloned record
    $new_id = $cloned_email->insert_record($clone_data);

    if ($new_id) {
        // Copy files from original template to cloned template
        $fs = get_file_storage();
        $original_files = $fs->get_area_files(
            $original_context->id,
            'local_etemplate',
            'emailtemplate',
            $original_data->id,
            'id',
            false
        );

        foreach ($original_files as $file) {
            $file_record = array(
                'contextid' => $original_context->id,
                'component' => 'local_etemplate',
                'filearea' => 'emailtemplate',
                'itemid' => $new_id,
                'filepath' => $file->get_filepath(),
                'filename' => $file->get_filename()
            );
            $fs->create_file_from_storedfile($file_record, $file);
        }

        // Redirect to edit the cloned template
        redirect($CFG->wwwroot . '/local/etemplate/edit_email.php?id=' . $new_id);
    } else {
        redirect($CFG->wwwroot . '/local/etemplate/email_templates.php',
                 get_string('clone_failed', 'local_etemplate'),
                 null,
                 \core\output\notification::NOTIFY_ERROR);
    }
} else {
    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');
}

?>

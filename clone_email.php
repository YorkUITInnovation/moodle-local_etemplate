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
 * Clone email template page.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use local_etemplate\base;
use local_etemplate\email;

require_login(1, false);

global $CFG, $DB, $USER;

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/etemplate/clone_email.php', ['id' => $id]);

// Capability check
if (!has_capability('local/etemplate:create', $context)) {
    print_error('nopermissions', 'error', $CFG->wwwroot . '/local/etemplate/email_templates.php');
}

$original_template = new email($id);
if (!$original_template->get_id()) {
    print_error('email_template_not_found', 'local_etemplate', $CFG->wwwroot . '/local/etemplate/email_templates.php');
}

if ($confirm) {
    // Get original template data as a raw database object
    $data = $DB->get_record('local_et_email', ['id' => $id]);

    // Unset ID for new record
    unset($data->id);

    // Modify for clone
    $data->name = get_string('copy_of', 'local_etemplate') . ' ' . $data->name;
    $data->active = 0; // Cloned templates are inactive by default
    $data->usermodified = $USER->id;
    $data->timemodified = time();
    $data->timecreated = time();
    $data->system_reserved = 0; // New cloned template should not be system reserved
    $data->deleted = 0;

    // Directly insert the new record into the database, bypassing the email class methods
    $new_id = $DB->insert_record('local_et_email', $data);

    if ($new_id) {
        \core\notification::success(get_string('clonesuccess', 'local_etemplate', $original_template->get_name()));
    } else {
        \core\notification::error(get_string('clone_failed', 'local_etemplate'));
    }

    redirect($CFG->wwwroot . '/local/etemplate/email_templates.php');

} else {
    $page_header = get_string('clone_email_template', 'local_etemplate');
    base::page(
        new moodle_url('/local/etemplate/clone_email.php', ['id' => $id]),
        $page_header,
        $page_header,
        $context
    );

    echo $OUTPUT->header();
    echo $OUTPUT->confirm(
        get_string('confirm_clone_email', 'local_etemplate', $original_template->get_name()),
        new moodle_url('/local/etemplate/clone_email.php', ['id' => $id, 'confirm' => 1]),
        new moodle_url('/local/etemplate/email_templates.php')
    );
    echo $OUTPUT->footer();
}

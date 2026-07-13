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
 * Delete email template page.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

// Unit-scope check: non-siteadmins may only delete/undelete templates within their assigned scope.
if (!is_siteadmin($USER->id) && !base::user_can_access_template_id($id)) {
    print_error('nopermissions', 'error', $CFG->wwwroot . '/local/etemplate/email_templates.php');
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

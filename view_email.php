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
 * View email template page.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use local_etemplate\base;
use local_etemplate\email;

$id = required_param('id', PARAM_INT);

$errmsg = optional_param('errormsg', '', PARAM_TEXT);
if (!empty($errmsg)) {
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

// Prepare template context for rendering email details.
$templatedata = (object) [
    'name' => $email->get_name(),
    'active' => $email->get_active(),
    'subject' => $email->get_subject(),
    'message' => $email->get_message(),
    'lang' => $email->get_lang(),
    'messagetype_nicename' => $email->get_messagetype_nicename($email->get_messagetype()),
    'system_reserved' => $email->get_system_reserved(),
    'deleted' => $email->get_deleted(),
    'timecreated' => date('m/d/Y H:i', $email->get_timecreated()),
    'timemodified' => date('m/d/Y H:i', $email->get_timemodified()),
];

$content = '';
$content .= $OUTPUT->render_from_template('local_etemplate/view_email_details', $templatedata);

// Post-table content.
$content .= $OUTPUT->single_button(new moodle_url('/local/etemplate/email_templates.php'), get_string('return_to_templates', 'local_etemplate'));

echo base::page(
    new moodle_url('/local/etemplate/email_templates.php'),
    $page_header,
    $page_header,
    $context
);

// **********************
echo $OUTPUT->header();
// *** DISPLAY HEADER ***
//
echo $errormessage;
echo $content;
// **********************
// *** DISPLAY FOOTER ***
// **********************
echo $OUTPUT->footer();

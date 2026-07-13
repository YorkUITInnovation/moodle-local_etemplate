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
 * Undelete email template page.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

use local_etemplate\base;
use local_etemplate\email;

require_login(1, false);

global $CFG, $DB, $USER;

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);

require_capability('local/etemplate:undelete', $context);

if (!is_siteadmin($USER->id) && !base::user_can_access_template_id($id)) {
    print_error('nopermissions', 'error', $CFG->wwwroot . '/local/etemplate/email_templates.php');
}

require_sesskey();

$emailobj = new email($id);
$emailobj->undelete_email($id);

redirect(new moodle_url('/local/etemplate/email_templates.php'));

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
 * External web service for email template operations.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_etemplate\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

/**
 * External API for email template operations.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_ws extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function delete_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Email Template ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Delete an email template.
     *
     * @param int $id Email template ID
     * @return array Status and message
     */
    public static function delete($id) {
        global $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::delete_parameters(), [
            'id' => $id
        ]);

        // Context validation.
        $context = \context_system::instance();
        self::validate_context($context);

        // Check capability.
        require_capability('local/etemplate:delete', $context);

        try {
            $email = new \local_etemplate\email($params['id']);
            $email->delete_email();
            $status = true;
            $message = get_string('deletesuccess', 'local_etemplate', $email->get_name());
        } catch (\Exception $e) {
            $status = false;
            $message = get_string('could_not_delete_email_template', 'local_etemplate');
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function delete_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'True if the deletion was successful, false otherwise.'),
            'message' => new external_value(PARAM_TEXT, 'A message describing the outcome of the operation.')
        ]);
    }
}

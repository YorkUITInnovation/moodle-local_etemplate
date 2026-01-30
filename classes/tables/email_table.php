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
 * Email table class for displaying email templates.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_etemplate\tables;

use local_etemplate\base;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Table class for displaying email templates.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_table extends \table_sql
{
    protected $show_create_button = false;
    protected $show_edit_button = false;
    protected $show_delete_button = false;
    protected $show_undelete_button = false;
    protected $show_view_button = false;
    protected $show_view_system_reserved_button = false;
    protected $show_clone_button = false;

    /**
     * unit_table constructor.
     * @param $uniqueid
     */
    public function __construct($uniqueid)
    {
        GLOBAL $USER;
        parent::__construct($uniqueid);

        // Define the columns to be displayed
        $columns = array('department_name', 'message_type_name', 'name', 'lang', 'active', 'timecreated', 'timemodified', 'actions');
        $this->define_columns($columns);

        // Define the headers for the columns
        $headers = array(
            get_string('campus', 'local_etemplate'),
            get_string('type', 'local_etemplate'),
            get_string('name', 'local_etemplate'),
            get_string('lang', 'local_etemplate'),
            get_string('active', 'local_etemplate'),
            get_string('timecreated', 'local_etemplate'),
            get_string('timemodified', 'local_etemplate'),
            get_string('actions', 'local_etemplate'),
            '',
        );
        //Capabilities
        $system_context = \context_system::instance();
        if (has_capability('local/etemplate:edit', $system_context, $USER->id)) {
            $this->show_edit_button = true;
        }
        if (has_capability('local/etemplate:create', $system_context, $USER->id)) {
            $this->show_create_button = true;
            $this->show_clone_button = true;
        }
        if (has_capability('local/etemplate:delete', $system_context, $USER->id)) {
            $this->show_delete_button = true;
        }
        if (has_capability('local/etemplate:delete', $system_context, $USER->id)) {
            $this->show_delete_button = true;
        }
        if (has_capability('local/etemplate:undelete', $system_context, $USER->id)) {
            $this->show_undelete_button = true;
        }
        if (has_capability('local/etemplate:view', $system_context, $USER->id)) {
            $this->show_view_button = true;
        }
        if (has_capability('local/etemplate:view_system_reserved', $system_context, $USER->id)) {
            $this->show_view_system_reserved_button = true;
        }

        $this->define_headers($headers);
    }

    public function col_department_name($values)
    {
        global $DB;
        if (empty($values->department_name)) {
            return get_string('course_based_alert', 'local_etemplate');
        } else {
            return $values->department_name;
        }
    }

    public function col_active($values) {
        return $values->active == 1 ? get_string('yes') : get_string('no');
    }

    /**
     * Function to define the actions column
     *
     * @param $values
     * @return string
     */
    public function col_actions($values)
    {
        global $OUTPUT, $CFG, $USER;

        $actions = [
            'edit_url' => $CFG->wwwroot . '/local/etemplate/edit_email.php?id=' . $values->id,
            'clone_url' => $CFG->wwwroot . '/local/etemplate/clone_email.php?id=' . $values->id,
            'id' => $values->id,
            'user_id' => $USER->id,
            'name' => $values->name,
            'show_create_button' => $this->show_create_button,
            'show_edit_button' => $this->show_edit_button,
            'show_delete_button' => $this->show_delete_button,
            'show_undelete_button' => $this->show_undelete_button,
            'show_view_system_reserved_button' => $this->show_view_system_reserved_button,
            'show_view_button' => $this->show_view_button,
            'show_clone_button' => $this->show_clone_button,
        ];
        return $OUTPUT->render_from_template('local_etemplate/email_table_action_buttons', $actions);
    }
}

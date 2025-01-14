<?php

namespace local_etemplate\tables;

use local_etemplate\base;

require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . "/externallib.php");

class email_table extends \table_sql
{
    protected $show_create_button = false;
    protected $show_edit_button = false;
    protected $show_delete_button = false;
    protected $show_undelete_button = false;
    protected $show_view_button = false;
    protected $show_view_system_reserved_button = false;

    /**
     * unit_table constructor.
     * @param $uniqueid
     */
    public function __construct($uniqueid)
    {
        GLOBAL $USER;
        parent::__construct($uniqueid);

        // Define the columns to be displayed
        $columns = array('department_name', 'message_type_name', 'name', 'lang', 'timecreated', 'timemodified', 'actions');
        $this->define_columns($columns);

        // Define the headers for the columns
        $headers = array(
            get_string('campus', 'local_etemplate'),
            get_string('type', 'local_etemplate'),
            get_string('name', 'local_etemplate'),
            get_string('lang', 'local_etemplate'),
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
            'id' => $values->id,
            'user_id' => $USER->id,
            'show_create_button' => $this->show_create_button,
            'show_edit_button' => $this->show_edit_button,
            'show_delete_button' => $this->show_delete_button,
            'show_undelete_button' => $this->show_undelete_button,
            'show_view_system_reserved_button' => $this->show_view_system_reserved_button,
            'show_view_button' => $this->show_view_button,
        ];
        return $OUTPUT->render_from_template('local_etemplate/email_table_action_buttons', $actions);
    }
}

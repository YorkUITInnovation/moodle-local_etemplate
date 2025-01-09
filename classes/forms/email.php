<?php

namespace local_etemplate;

use local_etemplate\base;
use local_etemplate\email;
use local_organization\unit;
use local_organization\units;
use local_organization\department;
use local_organization\departments;


defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/lib/formslib.php");
require_once("$CFG->dirroot/config.php");

class email_form extends \moodleform
{

    protected function definition()
    {
        global $USER, $CFG, $DB, $OUTPUT;

        $formdata = $this->_customdata['formdata'];
        $mform = &$this->_form;

        $context = \context_system::instance();

        $messageTypes = \local_etemplate\email::get_messagetype_nicename();

// Get all departments with unit and campus information
        $unit_sql = "Select
                        od.id,
                        od.unit_id,
                        od.name As department,
                        ou.name As unit,
                        oc.name As campus
                    From
                        {local_organization_dept} od Inner Join
                        {local_organization_unit} ou On ou.id = od.unit_id Inner Join
                        {local_organization_campus} oc On oc.id = ou.campus_id
                    Order By
                        campus,
                        unit,
                        department";
        $units = $DB->get_records_sql($unit_sql);

        $unit_select = [];

        foreach($units as $unit) {
            $unit_select[$unit->id] = $unit->campus .  ' / ' . $unit->unit . ' / ' . $unit->department;
        }


        $langs = get_string_manager()->get_list_of_translations();

        $mform->addElement(
            'hidden',
            'id'
        );

        $mform->addElement(
            'hidden',
            'parentid'
        );
        $mform->addElement(
            'hidden',
            'revision'
        );
        $mform->addElement(
            'header',
            'general',
            get_string('general')
        );
        $mform->addElement(
            'text',
            'name',
            get_string('name', 'local_etemplate')
        );

        $mform->addElement(
            'select',
            'message_type',
            get_string('messagetype', 'local_etemplate'),
            $messageTypes
        );
        $mform->addHelpButton(
            'message_type',
            'messagetype',
            'local_etemplate'
        );

        $mform->addElement(
            'select',
            'unit',
            get_string('unit', 'local_etemplate'),
	    $unit_select
        );
        $mform->addHelpButton(
            'unit',
            'unit',
            'local_etemplate'
        );

        $mform->addElement(
            'text',
            'subject',
            get_string('subject', 'local_etemplate')
        );
        $mform->addHelpButton(
            'subject',
            'subject',
            'local_etemplate'
        );
        $mform->addElement(
            'editor',
            'messagebodyeditor',
            get_string('message', 'local_etemplate'),
            null,
            base::get_editor_options($context)
        );
        $mform->addElement(
            'select',
            'lang',
            get_string('lang', 'local_etemplate'),
            $langs
        );
        $mform->addElement(
            'selectyesno',
            'active',
            get_string('active', 'local_etemplate')
        );
        if (has_capability(
                'local/etemplate:view_system_reserved',
                $context
            ) || is_siteadmin($USER->id)
        ) {
            $mform->addElement(
                'selectyesno',
                'system_reserved',
                get_string(
                    'system_reserved',
                    'local_etemplate')
            );
            $mform->addHelpButton(
                'system_reserved',
                'system_reserved',
                'local_etemplate'
            );
            $mform->setType(
                'system_reserved',
                PARAM_INT
            );
        }

        $mform->setType(
            'id',
            PARAM_INT
        );
        $mform->setType(
            'name',
            PARAM_TEXT
        );
        $mform->setType(
            'parentid',
            PARAM_INT
        );
        $mform->setType(
            'revision',
            PARAM_INT
        );
        $mform->setType(
            'unit',
            PARAM_TEXT
        );
        $mform->setType(
            'subject',
            PARAM_TEXT
        );
        $mform->setType(
            'messagebodyeditor',
            PARAM_RAW
        );
        $mform->setType(
            'lang',
            PARAM_TEXT
        );
        $mform->setType(
            'active',
            PARAM_TEXT
        );
        $mform->setType(
            'message_type',
            PARAM_TEXT
        );
        $mform->setType(
            'context',
            PARAM_TEXT
        );

/*        if (!$formdata->parentid) {
            $mform->addRule(
                'name',
                get_string('error_name', 'local_etemplate'),
                'required'
            );
        }*/
        $mform->addRule(
            'subject',
            get_string('error_subject', 'local_etemplate'),
            'required'
        );
        $mform->addRule(
            'messagebodyeditor',
            get_string('error_message_body', 'local_etemplate'),
            'required'
        );
        $mform->addRule(
            'active',
            get_string('error_active', 'local_etemplate'),
            'required'
        );
        $mform->addRule(
            'lang',
            get_string('error_language', 'local_etemplate'),
            'required'
        );
        $mform->disabledIf(
            'name',
            'parentid',
            'neq',
            0
        );


        $this->add_action_buttons();
        $this->set_data($formdata);
    }

}

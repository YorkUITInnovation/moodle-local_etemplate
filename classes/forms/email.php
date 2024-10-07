<?php

namespace local_etemplate;

use local_etemplate\base;
use local_etemplate\email;

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

        $messageTypes = [
            email::MESSAGE_TYPE_EMAIL => get_string(
                'email',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_INTERNAL => get_string(
                'internal',
                'local_etemplate'
            )
        ];

        $langs = get_string_manager()->get_list_of_translations();

        $sysAdminRole = $DB->get_record('role', ['shortname' => 'yulearn_sys_admin']);

        // $DB->get_record('yulearn_', ['id' => $formdata->id]);

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
            'messagetype',
            email::MESSAGE_TYPE_EMAIL
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
                'systemreserved',
                get_string(
                    'system_reserved',
                    'local_etemplate')
            );
            $mform->addHelpButton(
                'systemreserved',
                'system_reserved',
                'local_etemplate'
            );
            $mform->setType(
                'systemreserved',
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
            'messagetype',
            PARAM_TEXT
        );
        $mform->setType(
            'context',
            PARAM_TEXT
        );

        if (!$formdata->parentid) {
            $mform->addRule(
                'name',
                get_string('error_name', 'local_etemplate'),
                'required'
            );
        }
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

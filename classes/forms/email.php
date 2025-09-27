<?php

namespace local_etemplate;

use local_etemplate\base;
use local_etemplate\email;
use local_organization\campuses;
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
        global $USER, $CFG, $DB, $OUTPUT, $PAGE;

        $formdata = $this->_customdata['formdata'];
        $mform = &$this->_form;

        $context = \context_system::instance();

        $messageTypes = \local_etemplate\email::get_messagetype_nicename();
        // Prepare all select options which will be divided by groups
        $unit_select = base::get_unit_options();

        // Get campus dropdown data for campus_only field
        $campus_sql = "SELECT id, name, shortname FROM {local_organization_campus} ORDER BY name";
        $campus_results = $DB->get_records_sql($campus_sql);
        $campus_only_options = ['' => get_string('select', 'local_etemplate')];
        foreach ($campus_results as $campus) {
            $campus_only_options[$campus->shortname] = $campus->name;
        }

        $langs = get_string_manager()->get_list_of_translations();

        $mform->addElement(
            'hidden',
            'id'
        );

        $mform->addElement(
            'hidden',
            'view'
        );
        // Set Type
        $mform->setType(
            'view',
            PARAM_INT
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

        // Add template type radio buttons
        $template_types = [
            email::TEMPLATE_TYPE_CAMPUS_FACULTY => get_string('campus_faculty_level_template', 'local_etemplate', 'Campus and Faculty level template'),
            email::TEMPLATE_TYPE_CAMPUS_COURSE => get_string('campus_course_level_template', 'local_etemplate')
        ];

        $radio_array = [];
        foreach ($template_types as $value => $label) {
            $radio_array[] = $mform->createElement('radio', 'template_type', '', $label, $value);
        }
        $mform->addGroup($radio_array, 'template_type_group', get_string('template_type', 'local_etemplate'), '<br />', false);
        $mform->setDefault('template_type', email::TEMPLATE_TYPE_CAMPUS_FACULTY);
        $mform->addRule('template_type_group', get_string('error_template_type', 'local_etemplate'), 'required');
        $mform->disabledIf('template_type_group', 'view', 'eq', 1);
        $mform->addHelpButton('template_type_group', 'template_type', 'local_etemplate');

        $mform->addElement(
            'text',
            'name',
            get_string('name', 'local_etemplate')
        );

        // disable if view = 1
        $mform->disabledIf(
            'name',
            'view',
            'eq',
            1
        );

        $mform->addElement(
            'select',
            'message_type',
            get_string('messagetype', 'local_etemplate'),
            $messageTypes
        );

        // disable if view = 1
        $mform->disabledIf(
            'message_type',
            'view',
            'eq',
            1
        );
        $mform->addHelpButton(
            'message_type',
            'messagetype',
            'local_etemplate'
        );

        $mform->addElement(
            'selectgroups',
            'unit',
            get_string('unit', 'local_etemplate'),
            $unit_select
        );
        // disable if view = 1
        $mform->disabledIf(
            'unit',
            'view',
            'eq',
            1
        );
        $mform->addHelpButton(
            'unit',
            'unit',
            'local_etemplate'
        );

        $course_group=array();
        $course_group[] = $mform->createElement('text', 'course', get_string('course_code', 'local_etemplate'), ['placeholder' => get_string('course_code', 'local_etemplate')]);
        $course_group[] = $mform->createElement('text', 'coursenumber', get_string('course_number', 'local_etemplate'), ['placeholder' => get_string('course_number', 'local_etemplate')]);
        $course_group[] = $mform->createElement('text', 'section', get_string('section', 'local_etemplate'), ['placeholder' => get_string('section', 'local_etemplate')]);

        $mform->addGroup($course_group, 'course_group', get_string('course', 'local_etemplate'), ' ', false);
        // disable if view = 1
        $mform->disabledIf(
            'course_group',
            'view',
            'eq',
            1
        );

        $mform->hideIf('course_group', 'template_type', 'eq', email::TEMPLATE_TYPE_CAMPUS_FACULTY);

        // Set type for each element
        $mform->setType(
            'course',
            PARAM_TEXT
        );
        $mform->setType(
            'coursenumber',
            PARAM_TEXT
        );
        $mform->setType(
            'section',
            PARAM_TEXT
        );
        $mform->setType('template_type', PARAM_TEXT);

        $mform->addElement(
            'text',
            'subject',
            get_string('subject', 'local_etemplate')
        );
        // disable if view = 1
        $mform->disabledIf(
            'subject',
            'view',
            'eq',
            1
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
        // disable if view = 1
        $mform->disabledIf(
            'messagebodyeditor',
            'view',
            'eq',
            1
        );
        $mform->addElement(
            'select',
            'lang',
            get_string('lang', 'local_etemplate'),
            $langs
        );
        // disable if view = 1
        $mform->disabledIf(
            'lang',
            'view',
            'eq',
            1
        );
        $mform->addElement(
            'selectyesno',
            'active',
            get_string('active', 'local_etemplate')
        );
        // disable if view = 1
        $mform->disabledIf(
            'active',
            'view',
            'eq',
            1
        );

        // Add custom message field under 'Active'
        $mform->addElement(
            'selectyesno',
            'hascustommessage',
            get_string('hascustommessage', 'local_etemplate')
        );
        $mform->setDefault('hascustommessage', 0);
        $mform->addHelpButton(
            'hascustommessage',
            'hascustommessage',
            'local_etemplate'
        );
        $mform->setType('hascustommessage', PARAM_INT);
        $mform->disabledIf(
            'hascustommessage',
            'view',
            'eq',
            1
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
            // disable if view = 1
            $mform->disabledIf(
                'system_reserved',
                'view',
                'eq',
                1
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

        $mform->addRule(
            'unit',
            get_string('error_unit_required', 'local_etemplate'),
            'required', null, 'client'
        );

        $this->add_action_buttons();
        $this->set_data($formdata);
    }

    // Add custom validation for custom message placeholder
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['template_type'] == email::TEMPLATE_TYPE_CAMPUS_COURSE) {
            if (empty(trim($data['course']))) {
                $errors['course_group[course]'] = get_string('error_course_code_required', 'local_etemplate');
            }
            if (empty(trim($data['coursenumber']))) {
                $errors['course_group[coursenumber]'] = get_string('error_course_number_required', 'local_etemplate');
            }
//            if (empty(trim($data['section']))) {
//                $errors['course_group[section]'] = get_string('error_section_required', 'local_etemplate');
//            }
        }

        if (!empty($data['hascustommessage'])) {
            $message = '';
            if (isset($data['messagebodyeditor']['text'])) {
                $message = $data['messagebodyeditor']['text'];
            }
            if (stripos($message, '[custommessage]') === false) {
                $errors['messagebodyeditor'] = get_string('error_custommessage_missing', 'local_etemplate');
            }
        }
        return $errors;
    }
}

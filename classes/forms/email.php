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
        // Prepare all select options which will be devidied by groups
        $CAMPUSES = new campuses();
        $campuses_array = $CAMPUSES->get_select_array();
        $campuses = [
            '' => get_string('select', 'local_etemplate')
        ];
        foreach ($campuses_array as $campus_id => $campus_name) {
            $campuses[$campus_id . '_CAMPUS'] = $campus_name;
        }

        $faculties_sql = "SELECT
                            ou.id,
                            ou.name,
                            ou.shortname,
                            oc.name As campus
                        From
                            {local_organization_unit} ou Inner Join
                            {local_organization_campus} oc On oc.id = ou.campus_id
                        Order By
                            campus,
                            ou.name";
        $faculties_results = $DB->get_records_sql($faculties_sql);
        $faculties = [];
        foreach ($faculties_results as $faculty) {
            $faculties[$faculty->id . '_UNIT'] = $faculty->campus . ' / ' . $faculty->name;
        }

// Get all departments with unit and campus information
        $major_sql = "SELECT
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
        $majors = $DB->get_records_sql($major_sql);

        $major_select = [];

        foreach($majors as $major) {
            $major_select[$major->id . '_DEPT'] = $major->campus .  ' / ' . $major->unit . ' / ' . $major->department;
        }

        $unit_select = [
            get_string('campus', 'local_etemplate') => $campuses,
            get_string('faculty', 'local_etemplate') => $faculties,
            get_string('major', 'local_etemplate') => $major_select
        ];

        // Get campus dropdown data for campus_only field
        $campus_sql = "SELECT id, name, shortname FROM {local_organization_campus} ORDER BY name";
        $campus_results = $DB->get_records_sql($campus_sql);
        $campus_only_options = ['' => get_string('select', 'local_etemplate')];
        foreach ($campus_results as $campus) {
            $campus_only_options[$campus->id] = $campus->name;
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
            'campus' => get_string('campus_level_template', 'local_etemplate'),
            'faculty' => get_string('faculty_level_template', 'local_etemplate'),
            'course' => get_string('course_level_template', 'local_etemplate'),
            'campus_course' => get_string('campus_course_level_template', 'local_etemplate')
        ];

        $radio_array = [];
        foreach ($template_types as $value => $label) {
            $radio_array[] = $mform->createElement('radio', 'template_type', '', $label, $value);
        }
        $mform->addGroup($radio_array, 'template_type_group', get_string('template_type', 'local_etemplate'), '<br/>', false);
        $mform->setDefault('template_type', 'course');
        $mform->addRule('template_type_group', get_string('error_template_type', 'local_etemplate'), 'required');
        $mform->disabledIf('template_type_group', 'view', 'eq', 1);

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

        $association_types = [
            'course' => get_string('course_template', 'local_etemplate'),
            'org_unit' => get_string('org_unit_template', 'local_etemplate'),
            'org_unit_course' => get_string('org_unit_course_template', 'local_etemplate'),
        ];
        $mform->addElement('select', 'association_type', get_string('template_association', 'local_etemplate'), $association_types);
        $mform->setDefault('association_type', 'course');
        $mform->addHelpButton('association_type', 'template_association', 'local_etemplate');
        $mform->disabledIf('association_type', 'view', 'eq', 1);

       $faculties_sql = "Select
                            ou.name,
                            ou.shortname
                        From
                            {local_organization_unit} ou
                        Group By
                            ou.name,
                            ou.shortname
                        Order By
                            ou.name";

        $faculties_results = $DB->get_records_sql($faculties_sql);

        $faculties = [
            '' => get_string('select', 'local_etemplate')
        ];
        foreach ($faculties_results as $faculty) {
            $faculties[$faculty->shortname] = $faculty->name;
        }

        // Set type for each element
        $mform->setType(
            'faculty',
            PARAM_TEXT
        );
        $mform->setType(
            'course',
            PARAM_TEXT
        );
        $mform->setType(
            'coursenumber',
            PARAM_TEXT
        );
        $mform->setType('campus_id', PARAM_INT);

        $mform->addElement(
            'selectgroups',
            'unit',
            get_string('unit', 'local_etemplate'),
	    $unit_select
        );
        $mform->disabledIf('unit', 'view', 'eq', 1);
        $mform->addHelpButton('unit', 'unit', 'local_etemplate');
        $mform->hideIf('unit', 'association_type', 'neq', 'org_unit');

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

        $mform->addElement(
            'select',
            'campus_only',
            get_string('campus', 'local_etemplate'),
            $campus_only_options
        );
        $mform->hideIf('campus_only', 'template_type', 'neq', 'campus_course');
        $mform->disabledIf('campus_only', 'view', 'eq', 1);

        $mform->addElement(
            'text',
            'faculty',
            get_string('faculty', 'local_etemplate')
        );
        $mform->hideIf('faculty', 'template_type', 'eq', 'campus');
        $mform->hideIf('faculty', 'template_type', 'eq', 'campus_course');
        $mform->disabledIf('faculty', 'view', 'eq', 1);

        $mform->addElement(
            'text',
            'course',
            get_string('course', 'local_etemplate')
        );
        $mform->hideIf('course', 'template_type', 'eq', 'campus');
        $mform->hideIf('course', 'template_type', 'eq', 'faculty');
        $mform->disabledIf('course', 'view', 'eq', 1);

        $mform->addElement(
            'text',
            'coursenumber',
            get_string('coursenumber', 'local_etemplate')
        );
        $mform->hideIf('coursenumber', 'template_type', 'eq', 'campus');
        $mform->hideIf('coursenumber', 'template_type', 'eq', 'faculty');
        $mform->disabledIf('coursenumber', 'view', 'eq', 1);

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
        $mform->setType(
            'campus_only',
            PARAM_INT
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

        $this->add_action_buttons();
        $this->set_data($formdata);
    }

    // Add custom validation for custom message placeholder
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

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

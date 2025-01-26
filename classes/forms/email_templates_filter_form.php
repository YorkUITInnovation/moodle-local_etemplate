<?php

namespace local_etemplate\forms;
use moodleform;

require_once("$CFG->libdir/formslib.php");

class email_templates_filter_form extends moodleform
{
    public function definition()
    {
        GLOBAL $USER;
        $formdata = $this->_customdata['formdata'];
        $mform = $this->_form;


        $context = \context_system::instance();

        // Conditionally show button groups based on capability .. there might be a better way to do this with just an element but I'm not sure yet
        $system_context = \context_system::instance();
        if (has_capability('local/etemplate:edit', $system_context, $USER->id)) {
            if ($formdata->active) {
                $active_element = $mform->createElement(
                    'button',
                    'viewactive',
                    get_string('view_inactive', 'local_etemplate'),
                    array('onclick' => 'window.location.href = \'email_templates.php?active=0' . '\';', 'class' => 'ml-1')
                );
            } else {
                $active_element = $mform->createElement(
                    'button',
                    'viewinactive',
                    get_string('view_active', 'local_etemplate'),
                    array('onclick' => 'window.location.href = \'email_templates.php?' . '\';', 'class' => 'ml-1')
                );
            }
            // Group the text input and submit button
            $mform->addGroup(array(
                $mform->createElement(
                    'text',
                    'q',
                    get_string('name', 'local_etemplate')
                ),
                $mform->createElement(
                    'submit',
                    'submitbutton',
                    get_string('filter', 'local_etemplate'),
                    array('onclick' => 'window.location.href = \'edit_email.php' . '\';')
                ),
                $mform->createElement(
                    'cancel',
                    'resetbutton',
                    get_string('reset', 'local_etemplate')
                ),
                $mform->createElement(
                    'button',
                    'addemail',
                    get_string('new', 'local_etemplate'),
                    array('onclick' => 'window.location.href = \'edit_email.php' . '\';')
                ),
                $active_element

            ), 'filtergroup', '', array(' '), false);
        }
        else {
            $mform->addGroup(array(
                $mform->createElement(
                    'text',
                    'q',
                    get_string('name', 'local_etemplate')
                ),
                $mform->createElement(
                    'submit',
                    'submitbutton',
                    get_string('filter', 'local_etemplate'),
                    array('onclick' => 'window.location.href = \'edit_email.php' . '\';')
                ),
                $mform->createElement(
                    'cancel',
                    'resetbutton',
                    get_string('reset', 'local_etemplate')
                ),

            ), 'filtergroup', '', array(' '), false);
        }
        $mform->setType('q', PARAM_NOTAGS);

        $this->set_data($formdata);
    }
}
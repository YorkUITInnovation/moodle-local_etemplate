<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('etemplate', get_string('pluginname', 'local_etemplate')));

    $settings = new admin_externalpage('local_etemplate_settings',
        get_string('all_email_templates', 'local_etemplate', null, true),
        new moodle_url('/local/etemplate/email_templates.php'));

    $ADMIN->add('etemplate', $settings);

    $settings = null;
}

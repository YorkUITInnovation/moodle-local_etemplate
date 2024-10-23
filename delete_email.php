<?php
require_once("../../config.php");

use local_etemplate\email;

global $CFG, $DB, $USER;

$context = context_system::instance();

$id = required_param('id', PARAM_INT);
$email = new email($id);
$unit = $email->get_unit();
$permissioninfo = \local_etemplate\base::getTemplatePermissions($unit, $context, $USER->id);
if ($permissioninfo['canDelete'] !== true){
    redirect(new moodle_url('/local/etemplate/email_templates.php', array('errormsg' => 'nodel')));
} else {
    $email->delete_email($id);
}

redirect(new moodle_url('/local/etemplate/email_templates.php'));

?>
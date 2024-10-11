<?php
require_once("../../config.php");

use local_etemplate\email;

global $CFG, $DB;

$id = required_param('id', PARAM_INT);

$email = new email($id);
$email->undelete_email($id);

redirect(new moodle_url('/local/etemplate/email_templates.php'));

?>
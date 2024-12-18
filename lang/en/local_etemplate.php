<?php
$string['active'] = 'Active';
$string['add_email_template'] = 'Add email template';
$string['all_email_templates'] = 'All email templates';
$string['assignment'] = 'Missed Assignment';
$string['catch_all'] = 'Catch All';
$string['confirm_delete_email'] = 'Are you sure you want to delete email template: {$a->name}, from unit: {$a->unit}?';
$string['confirm_undelete_email'] = 'Are you sure you want to undelete email template: {$a->name}, from unit: {$a->unit}?';
$string['edit_email_template'] = 'Edit email template';
$string['email'] = 'Email';
$string['email_template'] = "Email template";
$string['error_name'] = 'Name is required';
$string['error_subject'] = 'Subject is required';
$string['error_message_body'] = 'Message body is required';
$string['error_active'] = 'Active is required';
$string['error_language'] = 'Language is required';
$string['exam'] = 'Missed Exam';
$string['grade'] = 'Low Grade';
$string['internal'] = 'Signature';
$string['lang'] = 'Language';
$string['message'] = 'Message';
$string['message_nodel'] = 'You don\'t have permission to delete this email template.';
$string['message_noed'] = 'You don\'t have permission to edit this email template.';
$string['message_nound'] = 'You don\'t have permission to undelete this email template.';
$string['message_noview'] = 'You don\'t have permission to view this email template.';
$string['messagetype'] = 'Message Type';
$string['messagetype_help'] = '<strong>Low Grade</strong> is the email content that gets used in templates for users with low grades.<br /><strong>Missed Assignment</strong> is used in the email content that gets used in templates for users that have missed an assignment.<br /><strong>Missed Exam</strong> is used in the email content that gets used in templates for users that have missed an exam.<br /><strong>Catch All</strong> is used when no faculty/department specific template is found for a student.<br /><strong>Unit Signature</strong> is content that is appended to the end of an existing Low Grade/Missed Assignment/Missed Exam email, multiple can be set and they will all append sequentially.<br /><br />[Note 1]: Department-level signatures will always preceed faculty-level signatures<br />[Note 2]: Signature message types will not be applied unless System Reserved is set to Yes.';
$string['name'] = 'Name';
$string['pluginname'] = 'Email Templates';
$string['return_to_templates'] = 'Return to email templates list';
$string['signature'] = 'Unit Signature';
$string['subject'] = 'Subject';
$string['subject_help'] = 'The subject of the email';
$string['system_reserved'] = 'System reserved';
$string['system_reserved_help'] = 'System reserved templates cannot be deleted or modified';
$string['undelete'] = 'Undelete';
$string['unit'] = 'Unit';
$string['unit_help'] = 'Enter the unit this template belongs to, this can be your faculty or department';

/**
 * Capabilities
 */
$string['etemplate:create'] = 'Create email templates';
$string['etemplate:delete'] = 'Delete email templates';
$string['etemplate:edit'] = 'Edit email templates';
$string['etemplate:undelete'] = 'Undelete email templates';
$string['etemplate:view'] = 'View email templates';
$string['etemplate:view_system_reserved'] = 'View system reserved templates';

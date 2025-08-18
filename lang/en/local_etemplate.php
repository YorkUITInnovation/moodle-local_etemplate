<?php
$string['actions'] = 'Actions';
$string['active'] = 'Active';
$string['add_email_template'] = 'Add email template';
$string['all_email_templates'] = 'All email templates';
$string['assignment'] = 'Missed Assignment';
$string['campus'] = 'Campus';
$string['cancel'] = 'Cancel';
$string['catch_all'] = 'Catch All';
$string['could_not_delete_email_template'] = 'Error: Could not delete email template';
$string['confirm_delete_email'] = 'Are you sure you want to delete email template: {$a->name}, from unit: {$a->unit}?';
$string['confirm_undelete_email'] = 'Are you sure you want to undelete email template: {$a->name}, from unit: {$a->unit}?';
$string['course'] = 'Course';
$string['course_based_alert'] = 'Course based alert';
$string['course_number'] = 'Course number';
$string['delete'] = 'Delete';
$string['delete_email_template'] = 'Delete email template';
$string['edit_email_template'] = 'Edit email template';
$string['email'] = 'Email';
$string['email_template'] = "Email template";
$string['error_name'] = 'Name is required';
$string['error_subject'] = 'Subject is required';
$string['error_message_body'] = 'Message body is required';
$string['error_active'] = 'Active is required';
$string['error_language'] = 'Language is required';
$string['exam'] = 'Missed Exam';
$string['faculty'] = 'Faculty';
$string['filter'] = 'Filter';
$string['grade'] = 'Low Grade';
$string['internal'] = 'Signature';
$string['lang'] = 'Language';
$string['major'] = 'Major';
$string['message'] = 'Message';
$string['message_nodel'] = 'You don\'t have permission to delete this email template.';
$string['message_noed'] = 'You don\'t have permission to edit this email template.';
$string['message_nound'] = 'You don\'t have permission to undelete this email template.';
$string['message_noview'] = 'You don\'t have permission to view this email template.';
$string['messagetype'] = 'Message Type';
$string['messagetype_help'] = '<strong>Low Grade</strong> is the email content that gets used in templates for users with low grades.<br /><strong>Missed Assignment</strong> is used in the email content that gets used in templates for users that have missed an assignment.<br /><strong>Missed Exam</strong> is used in the email content that gets used in templates for users that have missed an exam.<br /><strong>Catch All</strong> is used when no faculty/department specific template is found for a student.<br /><strong>Unit Signature</strong> is content that is appended to the end of an existing Low Grade/Missed Assignment/Missed Exam email, multiple can be set and they will all append sequentially.<br /><br />[Note 1]: Department-level signatures will always preceed faculty-level signatures<br />[Note 2]: Signature message types will not be applied unless System Reserved is set to Yes.';
$string['name'] = 'Name';
$string['new'] = 'New';
$string['pluginname'] = 'Email Templates';
$string['reset'] = 'Reset';
$string['return_to_templates'] = 'Return to email templates list';
$string['signature'] = 'Unit Signature';
$string['select'] = 'Select';
$string['subject'] = 'Subject';
$string['subject_help'] = 'The subject of the email';
$string['system_reserved'] = 'System reserved';
$string['system_reserved_help'] = 'System reserved templates cannot be deleted or modified';
$string['undelete'] = 'Undelete';
$string['timecreated'] = 'Time Created';
$string['timemodified'] = 'Time Modified';
$string['type'] = 'Type';
$string['unit'] = 'Unit';
$string['unit_help'] = 'Enter the unit this template belongs to, this can be your faculty or department';
$string['view_active'] = 'View active';
$string['view_email_template'] = 'View email template';
$string['view_inactive'] = 'View inactive';

/**
 *  Clone email template
 */
$string['clone_email_template'] = 'Clone email template';
$string['email_template_not_found'] = 'Email template not found';
$string['clone_failed'] = 'Failed to clone email template';


/**
 * Capabilities
 */
$string['etemplate:create'] = 'Create email templates';
$string['etemplate:delete'] = 'Delete email templates';
$string['etemplate:edit'] = 'Edit email templates';
$string['etemplate:undelete'] = 'Undelete email templates';
$string['etemplate:view'] = 'View email templates';
$string['etemplate:view_system_reserved'] = 'View system reserved templates';

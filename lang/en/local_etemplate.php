<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English language strings for local_etemplate.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actions'] = 'Actions';
$string['active'] = 'Active';
$string['add_email_template'] = 'Add email template';
$string['all_email_templates'] = 'All email templates';
// deprecated $string['assignment']
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
$string['deletesuccess'] = 'Template "{$a}" deleted successfully.';
$string['delete_email_template'] = 'Delete email template';
$string['edit_email_template'] = 'Edit email template';
$string['email'] = 'Email';
$string['email_template'] = "Email template";
$string['error_name'] = 'Name is required';
$string['error_subject'] = 'Subject is required';
$string['error_message_body'] = 'Message body is required';
$string['error_active'] = 'Active is required';
$string['error_language'] = 'Language is required';
// deprecated $string['exam']
$string['exam'] = 'Missed Exam';

/*
 *  Updated Nice names for message types and must be changed in local_earlyalert.php as well since it is being maintained there too.
 *   */
$string['missed_assignment'] = 'Missed Assignment';
$string['missed_exam'] = 'Missed Test/Quiz';
$string['low_grade'] = 'Low grade';

$string['faculty'] = 'Faculty';
$string['filter'] = 'Filter';

// deprecated $string['grade']
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
$string['select'] = 'Select';
$string['select_campus'] = 'Select Campus';
$string['section'] = 'Section';
$string['course_code'] = 'Course Code eg. MATH';
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
$string['hascustommessage'] = 'Custom message';
$string['hascustommessage_help'] = 'Enable this option if you want to include a custom message placeholder in this template. When enabled, the [custommessage] tag must be present in the message body. This allows instructors to add personalized content when sending alerts to students.';
$string['error_custommessage_missing'] = 'Custom message is required in the form. Please add [custommessage] to the template..';
$string['savessuccess'] = 'Template saved successfully';
$string['saveerror'] = 'Sorry there was an error saving the template, Please contact your administrator and notify them of the issue.';
/**
 * Template types
 */

$string['template_type'] = 'Template Types';
$string['campus_faculty_level_template'] = 'Campus and/or Faculty template';
$string['campus_course_level_template'] = 'Campus and Course template';
$string['error_template_type']  = 'Please select a template type';
$string['template_type_help'] = 'Select the type of template you are creating. <br />';
$string['error_unit_required'] = 'Unit is required.';
$string['error_course_code_required'] = 'Course code is required.';
$string['error_course_number_required'] = 'Course number is required.';
$string['error_section_required'] = 'Section is required.';
/**
 *  Clone email template
 */
$string['clone_email_template'] = 'Clone email template';
$string['confirm_clone_email'] = 'Are you sure you want to clone the template "{$a}"? The new template will be created as inactive.';
$string['clonesuccess'] = 'Template "{$a}" cloned successfully.';
$string['email_template_not_found'] = 'Email template not found';
$string['clone_failed'] = 'Failed to clone email template';
$string['copy_of'] = 'Copy of';


/**
 * Capabilities
 */
$string['etemplate:create'] = 'Create email templates';
$string['etemplate:delete'] = 'Delete email templates';
$string['etemplate:edit'] = 'Edit email templates';
$string['etemplate:undelete'] = 'Undelete email templates';
$string['etemplate:view'] = 'View email templates';
$string['etemplate:view_system_reserved'] = 'View system reserved templates';

// Email template header
$string['email_template_header'] = '<div class="early-alert-template-guidelines">
    <h2>Early Alert Template Guidelines</h2>
    
    <p>These institutional templates have been designed to ensure students receiving them:</p>
    
    <ul>
        <li>feel noticed and cared for as individuals</li>
        <li>understand the impact of NOT taking action (Grades alert)</li>
        <li>feel supported and empowered to recognize and take the next step</li>
        <li>are connected to faculty-specific resources that meet their needs.</li>
    </ul>
    
    <p>By standardizing the format and tone and including a singular call to action to "meet with an advisor/success coach", we aim to minimize confusion and ensure all York students receive a consistent message, regardless of their Faculty. This approach helps students experience York\'s care as coordinated and integrated. Changing the messages substantively will also complicate evaluation, and so we ask that template managers adhere to the following guidelines:</p>
    
    <ul>
        <li>Verify that all information listed applies to students that also may be taking courses outside of your Faculty (Example: not all courses have TAs, so this should not appear in a faculty template – consider course-level templates if this is critical information that must be included)</li>
        <li>Make only minor adjustments where there are faculty-specific details that are not accurately captured</li>
        <li>Refrain from editing content in square brackets, as this is pulling in data from our LMS</li>
        <li>Review content regularly for updates and accuracy</li>
    </ul>
</div>';

/**
 * Privacy metadata
 */
$string['privacy:metadata:local_et_email'] = 'Information about email templates, including who modified them and when.';
$string['privacy:metadata:local_et_email:name'] = 'The internal name of the email template.';
$string['privacy:metadata:local_et_email:subject'] = 'The subject line of the email template.';
$string['privacy:metadata:local_et_email:message'] = 'The email template message body which may contain administrator-provided content.';
$string['privacy:metadata:local_et_email:active'] = 'Indicates whether the email template is active.';
$string['privacy:metadata:local_et_email:usermodified'] = 'The user who last modified the email template.';
$string['privacy:metadata:local_et_email:timecreated'] = 'The time when the email template was created.';
$string['privacy:metadata:local_et_email:timemodified'] = 'The time when the email template was last modified.';

$string['privacy:metadata:local_et_access'] = 'Information about email template access permissions, including who modified them and when.';
$string['privacy:metadata:local_et_access:email_id'] = 'The identifier of the associated email template.';
$string['privacy:metadata:local_et_access:context'] = 'The access restriction context for the email template.';
$string['privacy:metadata:local_et_access:instance_id'] = 'The instance identifier associated with the access restriction.';
$string['privacy:metadata:local_et_access:usermodified'] = 'The user who last modified the email template access record.';
$string['privacy:metadata:local_et_access:timecreated'] = 'The time when the email template access record was created.';
$string['privacy:metadata:local_et_access:timemodified'] = 'The time when the email template access record was last modified.';

$string['privacy:metadata:local_et_filters'] = 'Information about email template filters, including who modified them and when.';
$string['privacy:metadata:local_et_filters:email_id'] = 'The identifier of the associated email template.';
$string['privacy:metadata:local_et_filters:context'] = 'The filter context applied to the email template.';
$string['privacy:metadata:local_et_filters:value'] = 'The filter value associated with the context.';
$string['privacy:metadata:local_et_filters:usermodified'] = 'The user who last modified the email template filter.';
$string['privacy:metadata:local_et_filters:timecreated'] = 'The time when the email template filter was created.';
$string['privacy:metadata:local_et_filters:timemodified'] = 'The time when the email template filter was last modified.';

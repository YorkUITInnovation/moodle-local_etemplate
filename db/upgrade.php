<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     local_etemplate
 * @category    upgrade
 * @copyright   2024 York University <itinnovation@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute local_etemplate upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_etemplate_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024112609) {

        // Changing type of field body on table local_etemplate_report_log to text.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('lang', XMLDB_TYPE_CHAR, 6, null, null, null, null, 'subject');

        // Launch change of type for field body.
        $dbman->change_field_type($table, $field);

        // etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2024112609, 'local', 'etemplate');
    }

    if ($oldversion < 2025011100) {

        // Define field context to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('context', XMLDB_TYPE_CHAR, '8', null, null, null, 'CAMPUS', 'unit');

        // Conditionally launch add field context.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2025011100, 'local', 'etemplate');
    }

    if ($oldversion < 2025011101) {

        // Define field faculty to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('faculty', XMLDB_TYPE_CHAR, '4', null, null, null, null, 'deleted');

        // Conditionally launch add field faculty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field course to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('course', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'faculty');

        // Conditionally launch add field course.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field coursenumber to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('coursenumber', XMLDB_TYPE_INTEGER, '8', null, null, null, null, 'course');

        // Conditionally launch add field coursenumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2025011101, 'local', 'etemplate');
    }

    if ($oldversion < 2025082801) {

        // Define field hascustommessage to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('hascustommessage', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'coursenumber');

        // Conditionally launch add field hascustommessage.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2025082801, 'local', 'etemplate');
    }

    if ($oldversion < 2025092500) {

        // Define field hascustommessage to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('template_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, \local_etemplate\email::TEMPLATE_TYPE_CAMPUS_FACULTY, 'hascustommessage');

        // Conditionally launch add field hascustommessage.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2025092500, 'local', 'etemplate');
    }
    if ($oldversion < 2025092502) {

        // Define field campus to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('campus', XMLDB_TYPE_CHAR, '2', null, null, null, '', 'deleted');

        // Conditionally launch add field campus_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2025092502, 'local', 'etemplate');
    }

    if ($oldversion < 2025092700) {

        // Define field department to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('department', XMLDB_TYPE_CHAR, '16', null, null, null, null, 'faculty');

        // Conditionally launch add field department.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field section to be added to local_et_email.
        $table = new xmldb_table('local_et_email');
        $field = new xmldb_field('section', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'coursenumber');

        // Conditionally launch add field section.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Etemplate savepoint reached.
        upgrade_plugin_savepoint(true, 2025092700, 'local', 'etemplate');
    }


    return true;
}

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

namespace local_etemplate\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for local_etemplate.
 *
 * @package     local_etemplate
 * @copyright   2026 Carlos Arce <carlosarcelopera@catalyst-ca.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    use \core_privacy\local\legacy_polyfill;

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $collection a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection): collection {
        // Add metadata for local_et_email table.
        $collection->add_database_table(
            'local_et_email',
            [
                'name' => 'privacy:metadata:local_et_email:name',
                'subject' => 'privacy:metadata:local_et_email:subject',
                'message' => 'privacy:metadata:local_et_email:message',
                'active' => 'privacy:metadata:local_et_email:active',
                'usermodified' => 'privacy:metadata:local_et_email:usermodified',
                'timecreated' => 'privacy:metadata:local_et_email:timecreated',
                'timemodified' => 'privacy:metadata:local_et_email:timemodified',
            ],
            'privacy:metadata:local_et_email'
        );

        // Add metadata for local_et_access table.
        $collection->add_database_table(
            'local_et_access',
            [
                'email_id' => 'privacy:metadata:local_et_access:email_id',
                'context' => 'privacy:metadata:local_et_access:context',
                'instance_id' => 'privacy:metadata:local_et_access:instance_id',
                'usermodified' => 'privacy:metadata:local_et_access:usermodified',
                'timecreated' => 'privacy:metadata:local_et_access:timecreated',
                'timemodified' => 'privacy:metadata:local_et_access:timemodified',
            ],
            'privacy:metadata:local_et_access'
        );

        // Add metadata for local_et_filters table.
        $collection->add_database_table(
            'local_et_filters',
            [
                'email_id' => 'privacy:metadata:local_et_filters:email_id',
                'context' => 'privacy:metadata:local_et_filters:context',
                'value' => 'privacy:metadata:local_et_filters:value',
                'usermodified' => 'privacy:metadata:local_et_filters:usermodified',
                'timecreated' => 'privacy:metadata:local_et_filters:timecreated',
                'timemodified' => 'privacy:metadata:local_et_filters:timemodified',
            ],
            'privacy:metadata:local_et_filters'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user data for the specified user.
     *
     * @param int $userid the user id.
     * @return contextlist the list of contexts containing user data.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Check if user has data in any table.
        if (!self::user_has_data($userid)) {
            return $contextlist;
        }

        // All data is stored at system context level.
        $contextlist->add_system_context();

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist the userlist to add users to.
     */
    public static function get_users_in_context(userlist $userlist) {
        if (!($userlist->get_context() instanceof \context_system)) {
            return;
        }

        $sql = "SELECT DISTINCT usermodified AS userid FROM {local_et_email}
                WHERE usermodified != 0
                UNION
                SELECT DISTINCT usermodified AS userid FROM {local_et_access}
                WHERE usermodified != 0
                UNION
                SELECT DISTINCT usermodified AS userid FROM {local_et_filters}
                WHERE usermodified != 0";

        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * Export all user data for the specified user within the contexts.
     *
     * @param approved_contextlist $contextlist the approved contexts list.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }

            // Export email templates modified by user.
            $sql = "SELECT id, name, subject, message, active, timecreated, timemodified
                    FROM {local_et_email}
                    WHERE usermodified = :userid
                    ORDER BY id";
            $records = $DB->get_records_sql($sql, ['userid' => $userid]);

            if (!empty($records)) {
                $data = [];
                foreach ($records as $record) {
                    $data[] = (object) [
                        'id' => $record->id,
                        'name' => $record->name,
                        'subject' => $record->subject,
                        'message' => $record->message,
                        'active' => \core_privacy\local\request\transform::yesno($record->active),
                        'timecreated' => \core_privacy\local\request\transform::datetime($record->timecreated),
                        'timemodified' => \core_privacy\local\request\transform::datetime($record->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_etemplate'), get_string('email_template', 'local_etemplate')],
                    (object) ['email_templates' => $data]
                );
            }

            // Export access records modified by user.
            $sql = "SELECT id, email_id, context, instance_id, timecreated, timemodified
                    FROM {local_et_access}
                    WHERE usermodified = :userid
                    ORDER BY id";
            $records = $DB->get_records_sql($sql, ['userid' => $userid]);

            if (!empty($records)) {
                $data = [];
                foreach ($records as $record) {
                    $data[] = (object) [
                        'id' => $record->id,
                        'email_id' => $record->email_id,
                        'context' => $record->context,
                        'instance_id' => $record->instance_id,
                        'timecreated' => \core_privacy\local\request\transform::datetime($record->timecreated),
                        'timemodified' => \core_privacy\local\request\transform::datetime($record->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_etemplate'), get_string('filter', 'local_etemplate')],
                    (object) ['access_records' => $data]
                );
            }

            // Export filter records modified by user.
            $sql = "SELECT id, email_id, context, value, timecreated, timemodified
                    FROM {local_et_filters}
                    WHERE usermodified = :userid
                    ORDER BY id";
            $records = $DB->get_records_sql($sql, ['userid' => $userid]);

            if (!empty($records)) {
                $data = [];
                foreach ($records as $record) {
                    $data[] = (object) [
                        'id' => $record->id,
                        'email_id' => $record->email_id,
                        'context' => $record->context,
                        'value' => $record->value,
                        'timecreated' => \core_privacy\local\request\transform::datetime($record->timecreated),
                        'timemodified' => \core_privacy\local\request\transform::datetime($record->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_etemplate'), get_string('filter', 'local_etemplate')],
                    (object) ['filter_records' => $data]
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        // Anonymize all records by setting usermodified to 0.
        $DB->set_field('local_et_email', 'usermodified', 0);
        $DB->set_field('local_et_access', 'usermodified', 0);
        $DB->set_field('local_et_filters', 'usermodified', 0);
    }

    /**
     * Delete all user data for the specified users in the approved userlist.
     *
     * @param approved_userlist $userlist the list of users to delete data for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        if (!($userlist->get_context() instanceof \context_system)) {
            return;
        }

        // Get the list of user IDs.
        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        [$sql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        // Anonymize all records for these users.
        $DB->set_field_select('local_et_email', 'usermodified', 0, "usermodified $sql", $params);
        $DB->set_field_select('local_et_access', 'usermodified', 0, "usermodified $sql", $params);
        $DB->set_field_select('local_et_filters', 'usermodified', 0, "usermodified $sql", $params);
    }

    /**
     * Delete all user data for the specified user in the approved contextlist.
     *
     * @param approved_contextlist $contextlist the list of contexts to delete data from.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }

            // Anonymize all records modified by this user.
            $DB->set_field('local_et_email', 'usermodified', 0, ['usermodified' => $userid]);
            $DB->set_field('local_et_access', 'usermodified', 0, ['usermodified' => $userid]);
            $DB->set_field('local_et_filters', 'usermodified', 0, ['usermodified' => $userid]);
        }
    }

    /**
     * Check if a user has data in any of the plugin's tables.
     *
     * @param int $userid the user id.
     * @return bool true if user has any data, false otherwise.
     */
    private static function user_has_data(int $userid): bool {
        global $DB;

        $sql = "SELECT 1 FROM {local_et_email} WHERE usermodified = :u1
                UNION
                SELECT 1 FROM {local_et_access} WHERE usermodified = :u2
                UNION
                SELECT 1 FROM {local_et_filters} WHERE usermodified = :u3
                LIMIT 1";

        $result = $DB->get_record_sql($sql, ['u1' => $userid, 'u2' => $userid, 'u3' => $userid]);
        return $result !== false;
    }
}

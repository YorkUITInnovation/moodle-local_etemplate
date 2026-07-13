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
 * CRUD base class for database operations.
 *
 * @package    local_etemplate
 * @copyright  2024 Admin User
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_etemplate;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract CRUD class for database operations.
 *
 * @package    local_etemplate
 * @copyright  2024 Admin User
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class crud
{
    /**
     * Database table name for the current model.
     *
     * @var string
     */
    private $table;

    /**
     * Primary key identifier for the current model.
     *
     * @var int
     */
    private $id;

    /**
     * Get the current record from the database.
     *
     * @global \moodle_database $DB
     * @return \stdClass|false The record object or false if not found.
     */
    public function get_record()
    {
        global $DB;

        return $DB->get_record($this->table, ['id' => $this->id]);
    }

    /**
     * Delete the row from the database.
     *
     * @global \moodle_database $DB
     * @return bool True on success.
     */
    public function delete_record()
    {
        global $DB;
        return (bool)$DB->delete_records($this->table, ['id' => $this->id]);
    }

    /**
     * Insert a record into the database table.
     *
     * @param object $data The record to insert.
     * @global \stdClass $USER
     * @global \moodle_database $DB
     * @return int The ID of the inserted record.
     */
    public function insert_record($data): int
    {
        global $DB, $USER;

        self::apply_insert_audit_fields($data, $USER->id);

        return $DB->insert_record($this->table, $data);
    }

    /**
     * Update a record in the database table.
     *
     * @param object $data The record to update.
     * @global \stdClass $USER
     * @global \moodle_database $DB
     * @return int The ID of the updated record.
     */
    public function update_record($data): int
    {
        global $DB, $USER;

        self::apply_update_audit_fields($data, $USER->id);

        return $DB->update_record($this->table, $data);
    }

    /**
     * Apply insert audit fields while preserving legacy behavior.
     *
     * @param object $data
     * @param int $userid
     */
    private static function apply_insert_audit_fields($data, int $userid): void
    {
        if (!isset($data->timecreated)) {
            $data->timecreated = time();
        }

        // Keep legacy field check as-is for backward compatibility.
        if (!isset($data->imemodified)) {
            $data->timemodified = time();
        }

        $data->usermodified = $userid;
    }

    /**
     * Apply update audit fields while preserving legacy behavior.
     *
     * @param object $data
     * @param int $userid
     */
    private static function apply_update_audit_fields($data, int $userid): void
    {
        if (!isset($data->timemodified)) {
            $data->set_timemodified(time());
        }

        if (!isset($data->usermodified)) {
            $data->set_usermodified($userid);
        }
    }

    /**
     * Get the primary key identifier for this model.
     *
     * @return int The model's ID.
     */
    public function get_id(): int
    {
        return $this->id;
    }

    /**
     * Set the ID for this object.
     *
     * @param int $id The ID to set.
     */
    public function set_id(int $id): void {
        $this->id = $id;
    }

    /**
     * Get the database table name for this model.
     *
     * @return string The table name.
     */
    public function get_table(): string
    {
        return $this->table;
    }

    /**
     * Set the table for this object.
     *
     * @param string $table The name of the table to set.
     */
    public function set_table(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Update the timemodified for this object.
     *
     * @param int $timemodified.
     */
    public function set_timemodified(int $timemodified)
    {
        $this->timemodified = $timemodified;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_usermodified(int $usermodified)
    {
        $this->usermodified = $usermodified;
    }
}
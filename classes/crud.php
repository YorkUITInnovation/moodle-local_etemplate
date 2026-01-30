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
     * /* string
     **/
    private $table;

    /**
     * /* int
     **/
    private $id;

    /**
     * Get record
     *
     * @global \moodle_database $DB
     *
     */
    public function get_record()
    {
        global $DB;
        $result = $DB->get_record($this->table, ['id' => $this->id]);
        return $result;

    }

    /**
     * Delete the row
     *
     * @global \moodle_database $DB
     *
     */
    public function delete_record()
    {
        global $DB;
        $DB->delete_records($this->table, ['id' => $this->id]);
    }

    /**
     * Insert record into selected table
     * @param object $data
     * @global \stdClass $USER
     * @global \moodle_database $DB
     */
    public function insert_record($data): int
    {
        global $DB, $USER;

        if (!isset($data->timecreated)) {
            $data->timecreated = time();
        }

        if (!isset($data->imemodified)) {
            $data->timemodified = time();
        }

        //Set user
        $data->usermodified = $USER->id;

        $id = $DB->insert_record($this->table, $data);

        return $id;
    }

    /**
     * Update record into selected table
     * @param object $data
     * @global \stdClass $USER
     * @global \moodle_database $DB
     */
    public function update_record($data): int
    {
        global $DB, $USER;

        if (!isset($data->timemodified)) {
            $data->set_timemodified(time());
        }

        //Set user
        if (!isset($data->usermodified)){
            $data->set_usermodified($USER->id);
        }

        $id = $DB->update_record($this->table, $data);

        return $id;
    }

    /**
     * /* get id
     **/
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
     * /* get table
     **/
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
<?php
/*
 * Author: Admin User
 * Create Date: 3-01-2024
 * License: LGPL 
 * 
 */

namespace local_etemplate;

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
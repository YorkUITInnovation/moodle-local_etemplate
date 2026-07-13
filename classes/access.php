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
 * Legacy model class.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_etemplate;

use local_etemplate\crud;
use local_etemplate\base;

class access extends crud
{
    /**
     * Unique identifier for the access record.
     *
     * @var int
     */
    private $id;

    /**
     * Email template ID this access rule applies to.
     *
     * @var int
     */
    private $emailid;

    /**
     * Context string for the access rule.
     *
     * @var string
     */
    private $context;

    /**
     * Permission value for this access record (0 or 1).
     *
     * @var int
     */
    private $permission;

    /**
     * User ID of the last user to modify this access record.
     *
     * @var int
     */
    private $usermodified;

    /**
     * Unix timestamp when this access record was created.
     *
     * @var int
     */
    private $timecreated;

    /**
     * Human-readable formatted creation time.
     *
     * @var string
     */
    private $timecreated_hr;

    /**
     * Unix timestamp when this access record was last modified.
     *
     * @var int
     */
    private $timemodified;

    /**
     * Human-readable formatted modification time.
     *
     * @var string
     */
    private $timemodified_hr;

    /**
     * Database table name for this model.
     *
     * @var string
     */
    private $table;


    /**
     *
     *
     */
    /**
     * Constructor to initialize access object.
     *
     * @param int $id The access ID to load (optional, defaults to 0 for new record).
     */
    public function __construct($id = 0)
    {
        global $CFG, $DB;

        $this->table = 'local_et_access';

        parent::set_table($this->table);

        if ($id) {
            $this->id = $id;
            parent::set_id($this->id);
            $result = $this->get_record($this->table, $this->id);
        } else {
            $result = new \stdClass();
            $this->id = 0;
            parent::set_id($this->id);
        }

        $this->emailid = $result->emailid ?? 0;
        $this->context = $result->context ?? '';
        $this->permission = $result->permission ?? 0;
        $this->usermodified = $result->usermodified ?? 0;
        $this->timecreated = $result->timecreated ?? 0;
        $this->timecreated_hr = '';
        if ($this->timecreated) {
            $this->timecreated_hr = base::strftime(get_string('strftimedate'), $result->timecreated);
        }
        $this->timemodified = $result->timemodified ?? 0;
        $this->timemodified_hr = '';
        if ($this->timemodified) {
            $this->timemodified_hr = base::strftime(get_string('strftimedate'), $result->timemodified);
        }
    }

    /**
     * @return id - bigint (18)
     */
    public function get_id(): int
    {
        return $this->id;
    }

    /**
     * @return emailid - bigint (18)
     */
    public function get_emailid(): int
    {
        return $this->emailid;
    }

    /**
     * @return context - varchar (5)
     */
    public function get_context(): string
    {
        return $this->context;
    }

    /**
     * @return permission - tinyint (2)
     */
    public function get_permission(): int
    {
        return $this->permission;
    }

    /**
     * @return usermodified - bigint (18)
     */
    public function get_usermodified(): int
    {
        return $this->usermodified;
    }

    /**
     * @return timecreated - bigint (18)
     */
    public function get_timecreated(): int
    {
        return $this->timecreated;
    }

    /**
     * @return timemodified - bigint (18)
     */
    public function get_timemodified(): int
    {
        return $this->timemodified;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_emailid($emailid)
    {
        $this->emailid = $emailid;
    }

    /**
     * @param Type: varchar (5)
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     * @param Type: tinyint (2)
     */
    public function set_permission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_usermodified($usermodified)
    {
        $this->usermodified = $usermodified;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_timecreated($timecreated)
    {
        $this->timecreated = $timecreated;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_timemodified($timemodified)
    {
        $this->timemodified = $timemodified;
    }

}
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

class access_member extends crud
{
    /**
     * Unique identifier for the access member.
     *
     * @var int
     */
    private $id;

    /**
     * Access record ID this member belongs to.
     *
     * @var int
     */
    private $accessid;

    /**
     * Value or identifier for this access member.
     *
     * @var int
     */
    private $value;

    /**
     * User ID of the last user to modify this access member record.
     *
     * @var int
     */
    private $usermodified;

    /**
     * Unix timestamp when this access member record was created.
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
     * Unix timestamp when this access member record was last modified.
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
     *
     * @var string
     */
    private $table;


    /**
     *
     *
     */
    /**
     * Constructor to initialize access member object.
     *
     * @param int $id The access member ID to load (optional, defaults to 0 for new record).
     */
    public function __construct($id = 0)
    {
        global $CFG, $DB;

        $this->table = 'local_et_access_member';

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

        $this->accessid = $result->accessid ?? 0;
        $this->value = $result->value ?? 0;
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
     * @return accessid - bigint (18)
     */
    public function get_accessid(): int
    {
        return $this->accessid;
    }

    /**
     * @return value - bigint (18)
     */
    public function get_value(): int
    {
        return $this->value;
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
    public function set_accessid($accessid)
    {
        $this->accessid = $accessid;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_value($value)
    {
        $this->value = $value;
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
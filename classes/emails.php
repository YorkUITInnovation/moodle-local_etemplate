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

class emails
{
    /**
     * Query used to fetch the latest template revision per template name.
     *
     * @var string
     */
    private const ACTIVE_RECORDS_SQL =
        'select * from {local_et_email} where id in (select max(id) from {local_et_email} group by name)';

    /**
     * Array of all email template records retrieved from the database.
     *
     * @var array
     */
    private $results;

    /**
     * Most recent record for each template name.
     *
     * @var array
     */
    private $activeresults;

    /**
     * Constructor to initialize emails collection.
     *
     * @global \moodle_database $DB
     */
    public function __construct()
    {
        global $DB;

        $this->results = $DB->get_records('local_et_email', [], 'timemodified');
        $this->activeresults = $DB->get_records_sql(self::ACTIVE_RECORDS_SQL, []);
    }

    /**
     * Get records
     */
    public function get_records()
    {
        return $this->results;
    }

    /**
     * Get the most recent version of each email template
     */
    public function get_active_records()
    {
        return $this->activeresults;
    }

    /**
     * Array to be used for selects
     * Defaults used key = record id, value = name
     * Modify as required.
     */
    public function get_select_array()
    {
        $array = [
            '' => get_string('select', 'local_etemplate')
        ];
        foreach ($this->results as $r) {
            $array[$r->id] = $r->name;
        }
        return $array;
    }

}
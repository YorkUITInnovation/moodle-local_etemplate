<?php
/*
 * Author: Admin User
 * Create Date: 3-01-2024
 * License: LGPL 
 * 
 */

namespace local_etemplate;

class emails
{

    /**
     *
     * @var string
     */
    private $results;

    /**
     *
     * @global \moodle_database $DB
     */
    public function __construct()
    {
        global $DB;
        $this->results = $DB->get_records('local_et_email', [], 'timemodified');
        $this->activeresults = $DB->get_records_sql("select * from {local_et_email} where id in (select max(id) from {local_et_email} group by name)", []);
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
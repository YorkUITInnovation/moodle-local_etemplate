<?php
/*
 * Author: Admin User
 * Create Date: 3-01-2024
 * License: LGPL 
 * 
 */

namespace local_etemplate;

use local_etemplate\crud;
use local_etemplate\base;

class email extends crud
{

    const MESSAGE_TYPE_EMAIL = 0;
    const MESSAGE_TYPE_EXAM = 2;
    const MESSAGE_TYPE_INTERNAL = 1;


    /**
     *
     * @var int
     */
    private $id;

    /**
     *
     * @var int
     */
    private $parentid;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $subject;

    /**
     *
     * @var string
     */
    private $message;

    /**
     *
     * @var string
     */
    private $lang;

    /**
     *
     * @var int
     */
    private $active;

    /**
     *
     * @var int
     */
    private $messagetype;

    /**
     *
     * @var int
     */
    private $systemreserved;

    /**
     *
     * @var int
     */
    private $deleted;

    /**
     *
     * @var int
     */
    private $usermodified;

    /**
     *
     * @var int
     */
    private $timecreated;

    /**
     *
     * @var string
     */
    private $timecreated_hr;

    /**
     *
     * @var int
     */
    private $timemodified;

    /**
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
     * @var \stdClass
     */
    private $record;

    /**
     *
     * @var int
     */
    private $unit;

    /**
     *
     *
     */
    public function __construct($id = 0)
    {
        global $CFG, $DB, $DB;

        $this->table = 'local_et_email';

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

        $this->record = $result;

        $this->parentid = $result->parentid ?? 0;
        $this->name = $result->name ?? '';
        $this->subject = $result->subject ?? '';
        $this->message = $result->message ?? '';
        $this->lang = $result->lang ?? '';
        $this->active = $result->active ?? 0;
        $this->revision = $result->revision ?? 0;
        $this->messagetype = $result->message_type ?? 0;
        $this->systemreserved = $result->systemreserved ?? 0;
        $this->deleted = $result->deleted ?? 0;
        $this->unit = $result->unit ?? 0;
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
     * @return \stdClass
     */
    public function get_record(): \stdClass
    {
        return parent::get_record();
    }

    /**
     * @return id - bigint (18)
     */
    public function get_id(): int
    {
        return $this->id;
    }

    /**
     * @return parentid - bigint (18)
     */
    public function get_parentid(): int
    {
        return $this->parentid;
    }

    /**
     * @return name - varchar (255)
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * @return subject - varchar (255)
     */
    public function get_subject(): string
    {
        return $this->subject;
    }

    /**
     * @return message - longtext (-1)
     */
    public function get_message(): string
    {
        return $this->message;
    }

    /**
     * @return lang - varchar (4)
     */
    public function get_lang(): string
    {
        return $this->lang;
    }

    /**
     * @return active - tinyint (2)
     */
    public function get_active(): int
    {
        return $this->active;
    }

    public function get_revision(): int
    {
        return $this->revision;
    }

    /**
     * @return messagetype - tinyint (2)
     */
    public function get_messagetype(): int
    {
        return $this->messagetype;
    }

    public static function get_messagetype_nicename($id = null)
    {
        $messageTypes = [
            email::MESSAGE_TYPE_EMAIL => get_string(
                'email',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_EXAM => get_string(
                'exam',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_INTERNAL => get_string(
                'internal',
                'local_etemplate'
            )
        ];
        if (is_numeric($id)){
            return $messageTypes[$id];
        } else {
            return $messageTypes;
        }
    }
    /**
     * @return systemreserved - tinyint (2)
     */
    public function get_systemreserved()
    {
        return $this->systemreserved;
    }

    /**
     * @return deleted - tinyint (2)
     */
    public function get_deleted(): int
    {
        return $this->deleted;
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
    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_parentid($parentid)
    {
        $this->parentid = $parentid;
    }

    /**
     * @param Type: varchar (255)
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @param Type: varchar (255)
     */
    public function set_subject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param Type: longtext (-1)
     */
    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     * @param Type: varchar (4)
     */
    public function set_lang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param Type: tinyint (2)
     */
    public function set_active($active)
    {
        $this->active = $active;
    }

    /**
     * @param Type: tinyint (2)
     */
    public function set_messagetype($messagetype)
    {
        $this->messagetype = $messagetype;
    }

    /**
     * @param Type: tinyint (2)
     */
    public function set_systemreserved($systemreserved)
    {
        $this->systemreserved = $systemreserved;
    }

    /**
     * @param Type: tinyint (2)
     */
    public function set_deleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @param Type: bigint (18)
     */
    public function set_usermodified(int $usermodified)
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
    public function set_timemodified(int $timemodified)
    {
        $this->timemodified = $timemodified;
    }

    public function delete_email(int $id){
        global $USER;
        $data = $this->get_record($id);
        $data->deleted = 1;
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        $this->update_record($data);
    }
    public function undelete_email(int $id){
        global $USER;
        $data = $this->get_record($id);
        $data->deleted = 0;
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        $this->update_record($data);
    }
    public function set_unit(int $unit)
    {
        $this->unit = $unit;
    }
    public function get_unit(){
        return $this->unit;
    }
    public function process_email($courseid = null, $userid = null){
        global $DB;
        $baseemail = $this->get_message();
        //define text replacements
        $textreplace = array(
            '[coursename]',
            '[firstname]',
            '[teacherfirstname]',
            '[teacherlastname]',
            '[defaultgrade]',
            '[customgrade]'
        );

        //build replacement info
        $unique_matches = array();
        foreach ($textreplace as $key => $value) {
            if (strpos($baseemail, $value) !== false && !isset($unique_matches[$value])) {
                // Perform action for each unique match found
                switch ($key) {
                    case 0:
                        // coursename action
                        if ($courseid === null && $userid === null){
                            $coursenametext = 'YO/UNIV 1234 - York University and YU (Full Year 0000-0001)';
                        } else {
                            $coursenametext = '{replacedcoursename}';
                        }
                        $textreplace[$value] = $coursenametext;
                        break;
                    case 1:
                        // firstname action
                        if ($courseid === null && $userid === null){
                            $firstnametext = 'August';
                        } else {
                            $firstnametext = '{replacedfirstname}';
                        }
                        $textreplace[$value] = $firstnametext;
                        break;
                    case 2:
                        // teacherfirstname action
                        if ($courseid === null && $userid === null) {
                            $teacherfirstnametext = 'River';
                        } else {
                            $teacherfirstnametext = '{replacedteacherfirstname}';
                        }
                        $textreplace[$value] = $teacherfirstnametext;
                        break;
                    case 3:
                        // teacherlastname action
                        if ($courseid === null && $userid === null) {
                            $teacherlastnametext = 'Song';
                        } else {
                            $teacherlastnametext = '{replacedteacherlastname}';
                        }
                        $textreplace[$value] = $teacherlastnametext;
                        break;
                    case 4:
                        // defaultgrade action
                        if ($courseid === null && $userid === null) {
                            $defaultgradetext = '55';
                        } else {
                            $defaultgradetext = '{replaceddefaultgrade}';
                        }
                        $textreplace[$value] = $defaultgradetext;
                        break;
                    case 5:
                        // customgrade action
                        if ($courseid === null && $userid === null) {
                            $customgradetext = '75';
                        } else {
                            $customgradetext = '{replacedcustomgrade}';
                        }
                        $textreplace[$value] = $customgradetext;
                        break;
                }
                $unique_matches[$value] = true; // mark as unique match found
            }
        }
        //replace the text with the matched values
        foreach ($textreplace as $key => $value) {
            if (isset($unique_matches[$value])) {
                $baseemail = str_replace($value, $textreplace[$value], $baseemail);
            }
        }
        return $baseemail;
    }

}
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
use local_organization\department;
use local_organization\unit;

class email extends crud
{

    const MESSAGE_TYPE_GRADE = 0;
    const MESSAGE_TYPE_ASSIGNMENT = 1;
    const MESSAGE_TYPE_EXAM = 2;
    const MESSAGE_TYPE_CATCHALL = 3;
    const MESSAGE_TYPE_SIGNATURE = 4;

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
    private $system_reserved;

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
        $this->system_reserved = $result->system_reserved ?? 0;
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
            email::MESSAGE_TYPE_GRADE => get_string(
                'grade',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_ASSIGNMENT => get_string(
                'assignment',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_EXAM => get_string(
                'exam',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_CATCHALL => get_string(
                'catch_all',
                'local_etemplate'
            ),
            email::MESSAGE_TYPE_SIGNATURE => get_string(
                'signature',
                'local_etemplate'
            )
        ];
        if (is_numeric($id)) {
            return $messageTypes[$id];
        } else {
            return $messageTypes;
        }
    }

    /**
     * @return system_reserved - tinyint (2)
     */
    public function get_system_reserved()
    {
        return $this->system_reserved;
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
    public function set_system_reserved($system_reserved)
    {
        $this->system_reserved = $system_reserved;
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

    public function delete_email(int $id)
    {
        global $USER;
        $data = $this->get_record($id);
        $data->deleted = 1;
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        $this->update_record($data);
    }

    public function undelete_email(int $id)
    {
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

    public function get_unit()
    {
        return $this->unit;
    }

    public function preload_template($courseid = null, $student_record, $teacherid)
    {
        global $DB, $USER;
        $basesubject = $this->get_subject();
        $baseemail = $this->get_message();

        $teacher = $DB->get_record('user', array('id' => $teacherid), 'id, firstname,lastname,email');

        $signature = '';
        //get any faculty/department level signatures
        $contactunit = '';
        $facultyname = '';

        if (is_number($this->unit)) {
            $faculty = $DB->get_record('local_organization_unit', ['id' => $this->unit]);
            $facultyname = $faculty->name;
            if ($facsigs = $DB->get_records($this->get_table(), array('unit' => $this->unit, 'system_reserved' => 1, 'active' => 1, 'message_type' => 4))) {
                foreach ($facsigs as $sig) {
                    $unit = new unit($this->unit);
                    $facultyname = $unit->get_name();
                    if ($sig->id == $this->id) {
                        //don't want to duplicate here...
                    } else {
                        $signature .= $sig->message;
                    }

                }
            }
        } else {
            $explodedUnit = explode("_", $this->unit);
            if (count($explodedUnit) == 2) {
                if ($deptsigs = $DB->get_records($this->get_table(), array('unit' => $this->unit, 'system_reserved' => 1, 'active' => 1, 'message_type' => 4))) {
                    $department = new department($explodedUnit[1]);
                    $contactunit = $department->get_name();
                    foreach ($deptsigs as $sig) {
                        $signature .= $sig->message;
                    }
                }
                if ($facsigs = $DB->get_records($this->get_table(), array('unit' => $explodedUnit[0], 'system_reserved' => 1, 'active' => 1, 'message_type' => 4))) {
                    $unit = new unit($explodedUnit[0]);
                    $facultyname = $unit->get_name();
                    foreach ($facsigs as $sig) {
                        $signature .= $sig->message;
                    }
                }
            }
        }

        $baseemail .= $signature;

        //define text replacements
        $textreplace = array(
            '[coursename]',
            '[teacherfirstname]',
            '[teacherlastname]',
            '[facultyname]',
            '[contactunit]',
            '[firstname]'
        );

        //build replacement info
        $unique_matches = array();
        foreach ($textreplace as $key => $value) {
            if (strpos($baseemail, $value) !== false && !isset($unique_matches[$value])) {
                // Perform action for each unique match found
                switch ($key) {
                    case 0:
                        // coursename action
                        if (!empty($courseid)) {
                            if ($course = $DB->get_record('course', array('id' => $courseid))) {
                                $basesubject = str_replace('[coursename]', $course->fullname, $basesubject);
                                $baseemail = str_replace('[coursename]', $course->fullname, $baseemail);
                            } else {
                                //do something else here?
                                $basesubject = str_replace('[coursename]', "{COURSE NOT FOUND}", $basesubject);
                                $baseemail = str_replace('[coursename]', "{COURSE NOT FOUND}", $baseemail);
                            }
                        } else {
                            $coursenametext = '{replacedcoursename}';
                        }
                        break;
                    case 1:
                        // teacherfirstname action
                        if (!empty($courseid)) {
                            //find a random user
                            if ($teacher) {
                                $basesubject = str_replace('[teacherfirstname]', $teacher->firstname, $basesubject);
                                $baseemail = str_replace('[teacherfirstname]', $teacher->firstname, $baseemail);
                            } else {
                                $teacherfirstnametext = '{INSTRUCTOR NOT FOUND}';
                            }
                        } else {
                            $teacherfirstnametext = '{COURSE_NOT_PROVIDED}';
                        }
                        break;
                    case 2:
                        // teacherlastname action
                        if (!empty($courseid)) {
                            //find a random user
                            if ($teacher) {
                                $basesubject = str_replace('[teacherlastname]', $teacher->lastname, $basesubject);
                                $baseemail = str_replace('[teacherlastname]', $teacher->lastname, $baseemail);
                            } else {
                                $teacherlastnametext = '{INSTRUCTOR NOT FOUND}';
                            }
                        } else {
                            $teacherlastnametext = '{replacedteacherlastname}';
                        }
                        break;
                    case 3:
                        //facultyname action
                        $basesubject = str_replace('[facultyname]', $facultyname, $basesubject);
                        $baseemail = str_replace('[facultyname]', $facultyname, $baseemail);
                        break;
                    case 4:
                        //contactunit action
                        $basesubject = str_replace('[contactunit]', $student_record->firstname, $basesubject);
                        $baseemail = str_replace('[contactunit]', $student_record->firstname, $baseemail);
                        break;
                    case 5:
                        //Student first name action
                        $basesubject = str_replace('[firstname]', $student_record->firstname, $basesubject);
                        $baseemail = str_replace('[firstname]', $student_record->firstname, $baseemail);
                        break;
                }
            }
        }

        //target_user_id (after triggered from), assignment_id
        $data = new \stdClass();
        $data->subject = $basesubject;
        $data->message = $baseemail;
        $data->templateid = $this->get_id();
        $data->revision_id = $this->get_revision();
        $data->triggered_from_user_id = $USER->id;
        $data->course_id = $courseid;
        $data->instructor_id = $teacherid;
        return $data;
    }
}
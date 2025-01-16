<?php

require_once($CFG->libdir . "/externallib.php");
require_once("$CFG->dirroot/config.php");


class local_etemplate_email_ws extends external_api
{
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function delete_parameters()
    {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Email Template ID', false, 0)
            )
        );
    }

    /**
     * @param $id
     * @return true
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function delete($id)
    {
        global $CFG, $USER, $DB, $PAGE;

        //Parameter validation
        $params = self::validate_parameters(self::delete_parameters(), array(
                'id' => $id
            )
        );

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = \context_system::instance();
        self::validate_context($context);
        $data = new \stdClass;
        $data->id = $id;
        $data->deleted = 1;
        $data->active = 0;
        $data->timemodified = time();
        $data->usermodified = $USER->id;
file_put_contents('/var/www/moodledata/temp/log.txt', print_r($data, true));
        $DB->update_record('local_et_email', $data);

        return true;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function delete_returns()
    {
        return new external_value(PARAM_INT, 'Boolean');
    }
}
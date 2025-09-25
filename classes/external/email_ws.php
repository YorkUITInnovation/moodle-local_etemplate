<?php

require_once($CFG->libdir . "/externallib.php");
require_once("$CFG->dirroot/config.php");
require_once("$CFG->dirroot/local/etemplate/classes/email.php");


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
     * @return array
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

        try {
            $email = new \local_etemplate\email($id);
            $email->delete_email();
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        if ($status) {
            $message = get_string('deletesuccess', 'core');
        } else {
            $message = get_string('deleteerror', 'core');
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function delete_returns()
    {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'True if the deletion was successful, false otherwise.'),
            'message' => new external_value(PARAM_TEXT, 'A message describing the outcome of the operation.')
        ]);
    }
}

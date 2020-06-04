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
 * Tool broadcast web Service.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

/**
 * Tool broadcast web Service.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_broadcast_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function submit_create_form_parameters() {
        return new external_function_parameters(
            array(
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the create copy form, encoded as a json array')
            )
            );
    }

    /**
     * Submit the broadcast create form.
     *
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int new group id.
     */
    public static function submit_create_form($jsonformdata) {

        // Release session lock.
        \core\session\manager::write_close();

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(
            self::submit_create_form_parameters(),
            array('jsonformdata' => $jsonformdata)
            );

        $formdata = json_decode($params['jsonformdata']);

        $data = array();
        parse_str($formdata, $data);

        $context = context::instance_by_id($data['contextid']);
        self::validate_context($context);
        require_capability('tool/broadcast:createbroadcasts', $context);

        $mform = new \tool_broadcast\output\create_form(
            null,
            array('contextid' => $context->id),
            'post', '', ['class' => 'ignoredirty'], true, $data);

        $mdata = $mform->get_data();

        if ($mdata) {
            $broadcast = new \tool_broadcast\broadcast();
            $result = $broadcast->create_broadcast($mdata);
        } else {
            throw new moodle_exception('createbroadcastfail', 'tool_broadcast');
        }

        return json_encode($result);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function submit_create_form_returns() {
        return new external_value(PARAM_RAW, 'JSON response.');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_broadcasts_parameters() {
        return new external_function_parameters(
            array(
                'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_REQUIRED, null, NULL_NOT_ALLOWED)
            )
            );
    }

    /**
     * Submit the broadcast create form.
     *
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int new group id.
     */
    public static function get_broadcasts($contextid) {
        global $USER;

        // Release session lock.
        \core\session\manager::write_close();

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(
            self::get_broadcasts_parameters(),
            array('contextid' => $contextid)
            );


        $context = context::instance_by_id($contextid);
        self::validate_context($context);
        require_capability('tool/broadcast:viewbroadcasts', $context);

        $broadcast = new \tool_broadcast\broadcast();
        $broadcasts = $broadcast->get_broadcasts($context->id, $USER->id);

        return json_encode($broadcasts);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function get_broadcasts_returns() {
        return new external_value(PARAM_RAW, 'JSON response.');
    }

}
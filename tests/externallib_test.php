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
 * Tool Broadcast webservice tests.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tool Broadcast webservice tests.
 *
 * @package    core_backup
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_broadcast_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up tasks for all tests.
     */
    protected function setUp() {
        global $CFG;

        $this->resetAfterTest(true);
    }

    /**
     * Test ajax submission of broadcast creation form.
     */
    public function test_submit_create_form() {
        global $DB;

        $this->setAdminUser();

        $course = get_site();
        $context = context_course::instance($course->id);

        // Moodle form requires this for validation.
        $sesskey = sesskey();
        $_POST['sesskey'] = $sesskey;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $context->id;
        $formdata->sesskey = $sesskey;
        $formdata->_qf__tool_broadcast_output_create_form = 1;
        $formdata->title = 'foo';
        $formdata->message = 'bar';

        $urlform = http_build_query($formdata, '', '&'); // Take the form data and url encode it.
        $jsonformdata = json_encode($urlform); // Take form string and JSON encode.

        $returnvalue = tool_broadcast_external::submit_create_form($jsonformdata);

        $returnjson = external_api::clean_returnvalue(tool_broadcast_external::submit_create_form_returns(), $returnvalue);
        $response = json_decode($returnjson, true);

        $record = $DB->get_record('tool_broadcast', array('id' => $response));

        $this->assertEquals($formdata->title, $record->title);
        $this->assertEquals($formdata->message, $record->body);

    }

    /**
     * Test ajax webservice to get broadcast messages.
     */
    public function test_get_broadcasts() {
        global $DB;

        // Create a course with activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $assignrow = $generator->create_module('assign', array(
            'course' => $course->id,
            'duedate' => 1585359375
        ));

        $assign = new assign(context_module::instance($assignrow->cmid), false, false);
        $user = $generator->create_user();
        $user->lastlogin = time() - 1000;
        $this->setUser($user);

        // Enrol user into the course.
        $generator->enrol_user($user->id, $course->id, 'student');

        $contextcourse = context_course::instance($course->id);
        $contextassignid = $assign->get_context()->id;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $contextcourse->id;
        $formdata->title = 'foo';
        $formdata->message = 'bar';

        // Create the broadcast
        $broadcast = new \tool_broadcast\broadcast();
        $broadcastid = $broadcast->create_broadcast($formdata);

        $returnvalue = tool_broadcast_external::get_broadcasts($contextassignid);

        $returnjson = external_api::clean_returnvalue(tool_broadcast_external::get_broadcasts_returns(), $returnvalue);
        $response = json_decode($returnjson, true);

        $this->assertEquals($formdata->title, $response[$broadcastid]['title']);
        $this->assertEquals($formdata->message, $response[$broadcastid]['body']);

    }
}
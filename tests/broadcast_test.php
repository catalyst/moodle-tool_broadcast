<?php
use repository_contentbank\browser\contentbank_browser_context_course;

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
 * Tool Broadcast class tests.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tool Broadcast class tests.
 *
 * @package    core_backup
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_broadcast_broadcast_testcase extends advanced_testcase {

    /**
     * Set up tasks for all tests.
     */
    protected function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test broadcast message record creation.
     */
    public function test_create_broadcast() {
        global $DB;

        $this->setAdminUser();

        $course = get_site();
        $context = context_course::instance($course->id);

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $context->id;
        $formdata->title = 'foo';
        $formdata->message = 'bar';

        $broadcast = new \tool_broadcast\broadcast();
        $result = $broadcast->create_broadcast($formdata);

        $record = $DB->get_record('tool_broadcast', array('id' => $result));

        $this->assertEquals($formdata->title, $record->title);
        $this->assertEquals($formdata->message, $record->body);
    }

    /**
     * Test getting broadcast messages.
     */
    public function test_get_broadcasts() {

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

        $broadcasts = $broadcast->get_broadcasts($contextassignid, $user->id);

        $this->assertEquals($formdata->title, $broadcasts[$broadcastid]->title);
        $this->assertEquals($formdata->message, $broadcasts[$broadcastid]->body);

    }
}
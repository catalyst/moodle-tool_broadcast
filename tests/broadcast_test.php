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
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_broadcast_broadcast_testcase extends advanced_testcase {

    /**
     * Set up tasks for all tests.
     */
    protected function setUp(): void {
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
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = $course->id;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        $broadcast = new \tool_broadcast\broadcast();
        $result = $broadcast->create_broadcast($formdata);

        $record = $DB->get_record('tool_broadcast', array('id' => $result));

        $this->assertEquals($formdata->title, $record->title);
        $this->assertEquals($formdata->message['text'], $record->body);
    }

    /**
     * Test broadcast message record updating.
     */
    public function test_update_broadcast() {
        global $DB;

        $this->setAdminUser();

        $course = get_site();
        $context = context_course::instance($course->id);

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $context->id;
        $formdata->title = 'foo';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = $course->id;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        $broadcast = new \tool_broadcast\broadcast();
        $result = $broadcast->create_broadcast($formdata);

        $record = $DB->get_record('tool_broadcast', array('id' => $result));

        $this->assertEquals($formdata->title, $record->title);
        $this->assertEquals($formdata->message['text'], $record->body);

        $formdata->broadcastid = $result;
        $formdata->title = 'bar';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>four </strong>threee<br></p>',
            'format' => 1
        );

        $broadcast->update_broadcast($formdata);

        $record = $DB->get_record('tool_broadcast', array('id' => $result));

        $this->assertEquals($formdata->title, $record->title);
        $this->assertEquals($formdata->message['text'], $record->body);
    }

    /**
     * Test getting broadcast messages.
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
        $user->lastlogin = 1591842950;

        $DB->update_record('user', $user);

        // Enrol user into the course.
        $generator->enrol_user($user->id, $course->id, 'student');

        $contextcourse = context_course::instance($course->id);
        $contextassignid = $assign->get_context()->id;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $contextcourse->id;
        $formdata->title = 'foo';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = $course->id;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        // Create the broadcast.
        $broadcast = new \tool_broadcast\broadcast();
        $broadcastid = $broadcast->create_broadcast($formdata);

        $now = 1591842970;
        $broadcasts = $broadcast->get_broadcasts($contextassignid, $user->id, $now);

        $this->assertEquals($formdata->title, $broadcasts[$broadcastid]->title);
        $this->assertEquals($formdata->message['text'], $broadcasts[$broadcastid]->body);

        $now = 1591842960;
        $broadcasts = $broadcast->get_broadcasts($contextassignid, $user->id, $now);
        $this->assertEmpty($broadcasts); // No broadcasts as start time is in the future.

        $now = 1591846570;
        $broadcasts = $broadcast->get_broadcasts($contextassignid, $user->id, $now);
        $this->assertEmpty($broadcasts); // No broadcasts as end time is in the past.

        $user->lastlogin = 1591842970;
        $DB->update_record('user', $user);
        $now = 1591842970;
        $broadcasts = $broadcast->get_broadcasts($contextassignid, $user->id, $now);
        $this->assertEmpty($broadcasts); // No broadcasts as user login time is after broadcast is active.

    }

    /**
     * Test checking for broadcast messages.
     */
    public function test_check_broadcasts() {
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
        $user->lastlogin = 1591842950;

        $DB->update_record('user', $user);

        // Enrol user into the course.
        $generator->enrol_user($user->id, $course->id, 'student');

        $contextcourse = context_course::instance($course->id);
        $contextassignid = $assign->get_context()->id;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $contextcourse->id;
        $formdata->title = 'foo';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = $course->id;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        // Create the broadcast.
        $broadcast = new \tool_broadcast\broadcast();

        $broadcasts = $broadcast->check_broadcasts($contextassignid, $user->id);
        $this->assertFalse($broadcasts);

        $broadcast->create_broadcast($formdata);

        $now = 1591842970;
        $broadcasts = $broadcast->check_broadcasts($contextassignid, $user->id, $now);
        $this->assertTrue($broadcasts);

        $now = 1591842950;
        $broadcasts = $broadcast->check_broadcasts($contextassignid, $user->id, $now);
        $this->assertFalse($broadcasts); // False as time start is in the future.

        $now = 1591846570;
        $broadcasts = $broadcast->check_broadcasts($contextassignid, $user->id, $now);
        $this->assertFalse($broadcasts); // False as time end is in the past.

        $user->lastlogin = 1591842970;
        $DB->update_record('user', $user);
        $now = 1591842970;
        $broadcasts = $broadcast->check_broadcasts($contextassignid, $user->id, $now);
        $this->assertFalse($broadcasts); // No broadcasts as user login time is after broadcast is active.

    }

    /**
     * Test checking for broadcast messages when an admin has already acknowledged them.
     */
    public function test_get_broadcasts_as_student_after_admin_ack() {
        // Create a course with activity.
        $generator = $this->getDataGenerator();

        $admin = get_admin();
        $student = $generator->create_user();

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = context_system::instance()->id;
        $formdata->title = 'New broadcast';
        $formdata->message = [
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        ];
        $formdata->activefrom = 1628664900;
        $formdata->expiry = 1630302840;
        $formdata->loggedin = 0;
        $formdata->mode = 1;

        $broadcast = new \tool_broadcast\broadcast();
        $broadcast->create_broadcast($formdata);

        $now = 1629875917;
        $broadcasts = $broadcast->get_broadcasts(context_system::instance()->id, $admin->id, $now);
        $this->assertCount(1, $broadcasts);

        $broadcastdbdata = reset($broadcasts);

        $broadcast = new \tool_broadcast\broadcast();
        $broadcast->acknowledge_broadcast($broadcastdbdata->id, context_system::instance()->id, $admin->id);

        $broadcasts = $broadcast->get_broadcasts(context_system::instance()->id, $student->id, $now);

        $this->assertCount(1, $broadcasts);
    }

    /**
     * Test acknowledging broadcast messages.
     */
    public function test_acknowledge_broadcast() {

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
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = $course->id;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        // Create the broadcast.
        $broadcast = new \tool_broadcast\broadcast();
        $broadcastid = $broadcast->create_broadcast($formdata);

        $now = 1591842970;
        $broadcasts = $broadcast->get_broadcasts($contextassignid, $user->id, $now);

        $this->assertEquals($formdata->title, $broadcasts[$broadcastid]->title);
        $this->assertEquals($formdata->message['text'], $broadcasts[$broadcastid]->body);

        // Acknowledge message.
        $broadcast->acknowledge_broadcast($broadcastid, $contextassignid, $user->id);

        $broadcasts = $broadcast->check_broadcasts($contextassignid, $user->id);
        $this->assertFalse($broadcasts);

    }

    /**
     * Test getting broadcast as formdata.
     */
    public function test_get_broadcast_formdata() {

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
        $assign->get_context()->id;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = $contextcourse->id;
        $formdata->title = 'foo';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = $course->id;
        $formdata->activefrom = 1628664900;
        $formdata->expiry = 1630302840;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        // Create the broadcast.
        $broadcast = new \tool_broadcast\broadcast();
        $broadcastid = $broadcast->create_broadcast($formdata);

        $broadcast = $broadcast->get_broadcast_formdata($broadcastid);

        $this->assertEquals($formdata->title, $broadcast['title']);
        $this->assertEquals($formdata->message['text'], $broadcast['message']['text']);
        $this->assertEquals($formdata->scopesite, $broadcast['scopesite']);
        $this->assertEquals($course->id, $broadcast['courses']);
        $this->assertEquals(date('d', $formdata->activefrom), $broadcast['activefrom']['day']);
        $this->assertEquals(date('n', $formdata->activefrom), $broadcast['activefrom']['month']);
        $this->assertEquals(date('Y', $formdata->activefrom), $broadcast['activefrom']['year']);
        $this->assertEquals(date('d', $formdata->expiry), $broadcast['expiry']['day']);
        $this->assertEquals(date('n', $formdata->expiry), $broadcast['expiry']['month']);
        $this->assertEquals(date('Y', $formdata->expiry), $broadcast['expiry']['year']);
        $this->assertEquals($formdata->loggedin, $broadcast['loggedin']);
        $this->assertEquals($formdata->mode, $broadcast['mode']);
    }

    /**
     * Test getting broadcast name.
     */
    public function test_get_broadcast_name() {

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = 1;
        $formdata->title = 'foo';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = 1;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        // Create the broadcast.
        $broadcast = new \tool_broadcast\broadcast();
        $broadcastid = $broadcast->create_broadcast($formdata);

        $broadcast = $broadcast->get_broadcast_name($broadcastid);

        $this->assertEquals($formdata->title, $broadcast);
    }

    /**
     * Test getting broadcast name.
     */
    public function test_get_broadcast_names() {

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->contextid = 1;
        $formdata->title = 'foo';
        $formdata->message = array(
            'text' => '<p dir="ltr" style="text-align: left;">one <strong>two </strong>threee<br></p>',
            'format' => 1
        );
        $formdata->scopesite = 2;
        $formdata->courses = 1;
        $formdata->activefrom = 1591842960;
        $formdata->expiry = 1591846560;
        $formdata->loggedin = 1;
        $formdata->mode = 1;

        // Create the broadcast.
        $broadcast = new \tool_broadcast\broadcast();
        $broadcastid = $broadcast->create_broadcast($formdata);

        $broadcast = $broadcast->get_broadcast_names();

        $this->assertEquals($formdata->title, $broadcast[$broadcastid]);
    }
}

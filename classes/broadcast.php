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
 * Broadcast class.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_broadcast;

use core\access\get_user_capability_course_helper;

defined('MOODLE_INTERNAL') || die;

/**
 * Broadcast class.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class broadcast {

    /**
     * Create a broadcast message record in the database.
     *
     * @param \stdClass $formdata The data from the broadcast create form to save in the database.
     * @return int $insertid The record ID from the newly created broadcast message record.
     */
    public function create_broadcast(\stdClass $formdata): int {
        global $DB;

        if (!empty($formdata->categories)) {
            $contextid = \context_coursecat::instance($formdata->categories)->id;
        } else if (!empty($formdata->courses)) {
            $contextid = \context_course::instance($formdata->courses)->id;
        } else {
            $contextid = 1;
        }

        $record = new \stdClass();
        $record->contextid = $contextid;
        $record->title = $formdata->title;
        $record->body = $formdata->message['text'];
        $record->bodyformat = $formdata->message['format'];
        $record->loggedin = (bool)$formdata->loggedin;
        $record->timecreated = time();
        $record->timestart = $formdata->activefrom;
        $record->timeend = $formdata->expiry;

        $insertid = $DB->insert_record('tool_broadcast', $record);

        return $insertid;
    }

    public function get_broadcasts(int $contextid, int $userid): array {
        global $DB;

        $context = \context::instance_by_id($contextid);
        $parentcontexts = $context->get_parent_context_ids(true);

        // A broadcast message for this context or any of it's parents are valid.
        // TODO: add filtering for dates
        // TODO: add filtering for logged in user switch.

        list($insql, $inparams) = $DB->get_in_or_equal($parentcontexts);
        $sql = "SELECT b.id, b.title, b.body, b.loggedin, u.lastlogin
                  FROM {tool_broadcast} b
             LEFT JOIN {tool_broadcast_users} bu ON b.id = bu.broadcastid
             LEFT JOIN {user} u ON bu.userid = u.id
                 WHERE b.contextid $insql
                       AND bu.userid is NULL
                   ";
        $records = $DB->get_records_sql($sql, $inparams);

        return $records;
    }

    public function check_broadcasts(int $contextid, int $userid): bool {
        global $DB;

        $context = \context::instance_by_id($contextid, MUST_EXIST);
        $parentcontexts = $context->get_parent_context_ids(true);

        // A broadcast message for this context or any of it's parents are valid.
        // TODO: add filtering for dates
        // TODO: add filtering for logged in user switch.

        list($insql, $inparams) = $DB->get_in_or_equal($parentcontexts);
        $sql = "SELECT b.id, b.title, b.body, b.loggedin, u.lastlogin
                  FROM {tool_broadcast} b
             LEFT JOIN {tool_broadcast_users} bu ON b.id = bu.broadcastid
             LEFT JOIN {user} u ON bu.userid = u.id
                 WHERE b.contextid $insql
                       AND bu.userid is NULL
                   ";
        $exists = $DB->record_exists_sql($sql, $inparams);

        return $exists;
    }

    public function acknowledge_broadcast(int $broadcastid, int $contextid, int $userid): void {
        global $DB;

        \context::instance_by_id($contextid, MUST_EXIST); // Confirm context exists, throw error if not.

        $exists = $DB->record_exists('tool_broadcast', array('id' => $broadcastid)); // Confirm broadcast exists.

        if ($exists) {
            $ackrecord = new \stdClass();
            $ackrecord->broadcastid = $broadcastid;
            $ackrecord->userid = $userid;
            $ackrecord->contextid = $contextid;
            $ackrecord->acktime = time();

            $DB->insert_record('tool_broadcast_users', $ackrecord);
        } else {
            new \moodle_exception('Broadcast does not exist.');
        }
    }

    public function get_courses(): array {

        $courses = array();
        $allcourses = get_courses('all', 'c.sortorder ASC', 'c.id, c.fullname, c.visible');
        unset($allcourses[SITEID]);

        foreach ($allcourses as $course) {
            $context = \context_course::instance($course->id);
            if (!$course->visible || !has_capability('tool/broadcast:createbroadcasts', $context)) {
                continue;
            }

            $courses[$course->id] = $course->fullname;

        }

        return $courses;
    }

    public function delete_broadcast(int $broadcastid): void {
        global $DB;

        $DB->delete_records('tool_broadcast', array('id' => $broadcastid));
        $DB->delete_records('tool_broadcast_users', array('broadcastid' => $broadcastid));
    }

}

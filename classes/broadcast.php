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

    /**
     * Create a broadcast message record in the database.
     *
     * @param \stdClass $formdata The data from the broadcast create form to save in the database.
     * @return int $insertid The record ID from the newly created broadcast message record.
     */
    public function update_broadcast(\stdClass $formdata): void {
        global $DB;

        if (!empty($formdata->categories)) {
            $contextid = \context_coursecat::instance($formdata->categories)->id;
        } else if (!empty($formdata->courses)) {
            $contextid = \context_course::instance($formdata->courses)->id;
        } else {
            $contextid = 1;
        }

        $record = new \stdClass();
        $record->id = $formdata->broadcastid;
        $record->contextid = $contextid;
        $record->title = $formdata->title;
        $record->body = $formdata->message['text'];
        $record->bodyformat = $formdata->message['format'];
        $record->loggedin = (bool)$formdata->loggedin;
        $record->timecreated = time();
        $record->timestart = $formdata->activefrom;
        $record->timeend = $formdata->expiry;

        $DB->update_record('tool_broadcast', $record);

    }

    /**
     * Delete a broadcast from the database.
     *
     * @param int $broadcastid The ID of the broadcast to delete.
     */
    public function delete_broadcast(int $broadcastid): void {
        global $DB;

        $DB->delete_records('tool_broadcast', array('id' => $broadcastid));
        $DB->delete_records('tool_broadcast_users', array('broadcastid' => $broadcastid));
    }

    /**
     * Get the an exisitng broadcast in a format that can be fed back into the create form.
     *
     * @param int $broadcastid The ID of the broadcast to delete.
     * @return array $formdata The broadcast form data.
     */
    public function get_broadcast_formdata(int $broadcastid): array {
        global $DB;

        $broadcast = $DB->get_record('tool_broadcast', array('id' => $broadcastid), '*', MUST_EXIST);
        $context = \context::instance_by_id($broadcast->contextid);

        $formdata = array (
            'contextid' => $broadcast->contextid,
            'sesskey' => sesskey(),
            '_qf__tool_broadcast_output_create_form' => 1,
            'title' => $broadcast->title,
            'message' => array(
                'text' => $broadcast->body,
                'format' => $broadcast->bodyformat
            ),
            'activefrom ' => array(
                'day' => date('d', $broadcast->timestart),
                'month' => date('n', $broadcast->timestart),
                'year' => date('Y', $broadcast->timestart),
                'hour' => date('h', $broadcast->timestart),
                'minute' => date('i', $broadcast->timestart)
            ),
            'expiry' => array(
                'day' => date('d', $broadcast->timeend),
                'month' => date('n', $broadcast->timeend),
                'year' => date('Y', $broadcast->timeend),
                'hour' => date('H', $broadcast->timeend),
                'minute' => date('i', $broadcast->timeend)
            ),
            'loggedin' => $broadcast->loggedin
        );

        if ($context->contextlevel == CONTEXT_COURSECAT) {
            $formdata['scopesite'] = 1;
            $formdata['categories'] = $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $formdata['scopesite'] = 2;
            $formdata['courses'] = $context->instanceid;
        } else {
            $formdata['scopesite'] = 0;
        }

        return $formdata;
    }

    /**
     * Get a list of broadcasts.
     *
     * @param int $contextid The context ID to get broadcasts for.
     * @param int $userid The user ID that the broadcasts relate to.
     * @return array $records The broadcast records.
     */
    public function get_broadcasts(int $contextid, int $userid, int $now=0): array {
        global $DB;

        $context = \context::instance_by_id($contextid);
        $parentcontexts = $context->get_parent_context_ids(true);

        if ($now == 0) {
            $now = time();
        }

        list($insql, $inparams) = $DB->get_in_or_equal($parentcontexts);
        $sql = "SELECT b.id, b.title, b.body, b.loggedin, b.timestart
                  FROM {tool_broadcast} b
             LEFT JOIN {tool_broadcast_users} bu ON b.id = bu.broadcastid
                 WHERE b.contextid $insql
                       AND bu.userid is NULL
                       AND b.timestart < ?
                       AND b.timeend > ?
                   ";
        $inparams[] = $now; // Timestart var.
        $inparams[] = $now; // Timeend var.

        $records = $DB->get_records_sql($sql, $inparams);

        foreach ($records as $record) {
            // Filter broadcasts limited to users who are logged in at the time of the message becoming active.
            if ($record->loggedin == 1) {
                $lastlogin = $DB->get_field('user', 'lastlogin', array('id' => $userid), MUST_EXIST);
                if ($lastlogin > $record->timestart) {
                    unset ($records[$record->id]);
                }
            }
        }

        return $records;
    }

    /**
     * Check if there are any broadcasts applicable.
     *
     * @param int $contextid The context ID to get broadcasts for.
     * @param int $userid The user ID that the broadcasts relate to.
     * @return bool
     */
    public function check_broadcasts(int $contextid, int $userid, int $now=0): bool {
        global $DB;

        $context = \context::instance_by_id($contextid);
        $parentcontexts = $context->get_parent_context_ids(true);

        if ($now == 0) {
            $now = time();
        }

        list($insql, $inparams) = $DB->get_in_or_equal($parentcontexts);
        $sql = "SELECT b.id, b.title, b.body, b.loggedin, b.timestart
                  FROM {tool_broadcast} b
             LEFT JOIN {tool_broadcast_users} bu ON b.id = bu.broadcastid
                 WHERE b.contextid $insql
                       AND bu.userid is NULL
                       AND b.timestart < ?
                       AND b.timeend > ?
                   ";
        $inparams[] = $now; // Timestart var.
        $inparams[] = $now; // Timeend var.

        $records = $DB->get_records_sql($sql, $inparams);

        foreach ($records as $record) {
            // Filter broadcasts limited to users who are logged in at the time of the message becoming active.
            if ($record->loggedin == 1) {
                $lastlogin = $DB->get_field('user', 'lastlogin', array('id' => $userid), MUST_EXIST);
                if ($lastlogin > $record->timestart) {
                    unset ($records[$record->id]);
                }
            }
        }

        if (empty($records)) {
            $exists = false;
        } else {
            $exists = true;
        }

        return $exists;
    }

    /**
     * Process user acknowledgement of the broadcast.
     *
     * @param int $broadcastid The broadcast ID to acknowledge.
     * @param int $contextid The context ID to get broadcasts for.
     * @param int $userid The user ID that the broadcasts relate to.
     */
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

    /**
     * Helper method to get a list of courses that the user can create a broadcast in.
     *
     * @return array $courses The list of courses.
     */
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

    /**
     * Get the name of a broadcast message.
     *
     * @param int $broadcastid The id of the broadcast to get the name for.
     * @return string $broadcastname The name of the retrieved broadcast.
     */
    public function get_broadcast_name(int $broadcastid): string {
        global $DB;

        $broadcastname = $DB->get_field('tool_broadcast', 'title', array('id' => $broadcastid));

        return $broadcastname;
    }

    /**
     * Get the list of broadcast names.
     *
     * @return array $broadcastname The list of the retrieved broadcasts.
     */
    public function get_broadcast_names(): array {
        global $DB;

        $broadcastnames = $DB->get_records_menu('tool_broadcast', array(), 'title ASC', 'id, title');

        return $broadcastnames;
    }
}

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

        $record = new \stdClass();
        $record->contextid = $formdata->contextid;
        $record->title = $formdata->title;
        $record->body = $formdata->message;
        $record->loggedin = false;
        $record->timecreated = time();
        $record->timestart = 0;
        $record->timeend = 0;

        $insertid = $DB->insert_record('tool_broadcast', $record);

        // TODO: invalidate broadcast cache and make new cache.

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
}

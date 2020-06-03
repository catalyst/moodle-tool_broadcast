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

        return $insertid;
    }
}

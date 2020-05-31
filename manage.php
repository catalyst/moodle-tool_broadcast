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
 * Broadcast message management.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');

defined('MOODLE_INTERNAL') || die();

$courseid = optional_param('id', 1, PARAM_INT); // Generic navigation return page switch.

require_login();

$url = new moodle_url('/admin/tool/broadcast/manage.php', array('id' => $courseid));
$course = get_course($courseid);


// Security and access checks.
require_login($course, false);
$context = context_course::instance($course->id);
require_capability('tool/broadcast:createbroadcasts', $context);

// Load the javascript.
//$PAGE->requires->js_call_amd('tool_broadcast/manage_broadcast', 'init', array($context->id));

// Build the page output.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebroadcast', 'tool_broadcast'));

echo $OUTPUT->footer();

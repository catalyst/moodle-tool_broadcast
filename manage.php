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
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 50, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$broadcastid = optional_param('broadcastid', 0, PARAM_INT);

$baseurl = $CFG->wwwroot . '/admin/tool/broadcast/manage.php';
$url = new moodle_url($baseurl, array('id' => $courseid));
$course = get_course($courseid);

// Security and access checks.
require_login($course, false);
$context = context_course::instance($course->id);
require_capability('tool/broadcast:createbroadcasts', $context);

if ($action == 'delete') {
    $broadcast = new \tool_broadcast\broadcast();
    $broadcast->delete_broadcast($broadcastid);
    redirect($url);
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('manage', 'tool_broadcast'));
$PAGE->set_heading(get_string('manage', 'tool_broadcast'));

// Load the javascript.
$PAGE->requires->js_call_amd('tool_broadcast/broadcast', 'init', array($context->id));

// Build the page output.
echo $OUTPUT->header();
$output = $PAGE->get_renderer('tool_broadcast');
echo $output->render_content($courseid, $baseurl, $page, $perpage);

echo $OUTPUT->footer();

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

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login(null, false);

$baseurl = $CFG->wwwroot . "/admin/tool/broadcast/acknowledgereport.php";
$broadcastid = optional_param('broadcastid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);

if ($broadcastid != 0) {
    $broadcast = new \tool_broadcast\broadcast();
    $broadcastname = $broadcast->get_broadcast_name($broadcastid);
    $title = get_string('acknowledgereportbroadcast', 'tool_broadcast', $broadcastname);
    $context = $broadcast->get_broadcast_context($broadcastid);
} else {
    $title = get_string('acknowledgereporttitle', 'tool_broadcast');
    $context = context_system::instance();
}

$url = new moodle_url($baseurl);
require_capability('tool/broadcast:createbroadcasts', $context);

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Build the page output.
echo $OUTPUT->header();

$mform = new \tool_broadcast\output\ackreport_form(null, array('broadcastid' => $broadcastid), 'get');

$output = $PAGE->get_renderer('tool_broadcast');

echo $mform->render();
echo $output->render_ackreport($broadcastid, $context->id, $baseurl, $page);

echo $OUTPUT->footer();

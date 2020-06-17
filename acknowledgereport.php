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

$baseurl = $CFG->wwwroot . "/admin/tool/broadcast/acknowledgereport.php";

// Calls require_login and performs permissions checks for admin pages.
admin_externalpage_setup('tool_broadcast_report', '', null, '',
    array('pagelayout' => 'admin'));

$broadcastid = optional_param('broadcastid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);

$url = new moodle_url($baseurl);
$context = context_system::instance();

require_capability('tool/broadcast:createbroadcasts', $context);

if ($broadcastid != 0) {
    $broadcast = new \tool_broadcast\broadcast();
    $broadcastname = $broadcast->get_broadcast_name($broadcastid);
    $title = get_string('acknowledgereportbroadcast', 'tool_broadcast', $broadcastname);
} else {
    $title = get_string('acknowledgereporttitle', 'tool_broadcast');
}

$PAGE->set_url($url);
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

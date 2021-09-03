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

$contextid = required_param('id', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

$pageurl = new moodle_url('/admin/tool/broadcast/manage.php', ['id' => $contextid]);

// Security and access checks.
$context = context::instance_by_id($contextid);

// Pass the course id to require_login() if we are in a course to set up the navbar appropriately.
if ($context->contextlevel == CONTEXT_COURSE) {
    require_login($context->instanceid);
} else {
    require_login();
}
require_capability('tool/broadcast:createbroadcasts', $context);

if ($action == 'delete' && confirm_sesskey()) {
    $broadcastid = required_param('broadcastid', PARAM_INT);

    $broadcast = new \tool_broadcast\broadcast();

    \core\notification::add(
        get_string('broadcastdeleted', 'tool_broadcast', [
            'name' => $broadcast->get_broadcast_name($broadcastid),
        ]),
      \core\notification::INFO
    );

    $broadcast->delete_broadcast($broadcastid);
    redirect($pageurl);
}

$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title(get_string('manage', 'tool_broadcast'));
$PAGE->set_heading(get_string('manage', 'tool_broadcast'));

// Load the javascript.
$PAGE->requires->js_call_amd('tool_broadcast/broadcast', 'init', array($contextid));

// Build the page output.
echo $OUTPUT->header();
$output = $PAGE->get_renderer('tool_broadcast');
echo $output->render_content($pageurl, $page);

echo $OUTPUT->footer();

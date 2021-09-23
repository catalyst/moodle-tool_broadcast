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
 * Broadcast message lib functions.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds a manage broadcast link to the course admin menu.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $coursecontext The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function tool_broadcast_extend_navigation_course($navigation, $course, $coursecontext) {
    global $PAGE;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course || $PAGE->course->id == SITEID) {
        return null;
    }

    if (!has_capability('tool/broadcast:createbroadcasts', $coursecontext)) {
        return null;
    }

    $url = new moodle_url('/admin/tool/broadcast/manage.php', ['id' => $coursecontext->id]);
    $pluginname = get_string('pluginname', 'tool_broadcast');
    $node = navigation_node::create(
        $pluginname,
        $url,
        navigation_node::NODETYPE_LEAF,
        'tool_broadcast',
        'tool_broadcast',
        new pix_icon('i/bullhorn', get_string('pluginname', 'tool_broadcast'))
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $node->make_active();
    }

    $navigation->add_node($node);
}

/**
 * Adds a manage broadcast link to the category admin menu.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $context The context of the category
 * @return void|null return null if we don't want to display the node.
 */
function tool_broadcast_extend_navigation_category_settings($navigation, $context) {
    global $PAGE;

    if (!has_capability('tool/broadcast:createbroadcasts', $context)) {
        return null;
    }

    $url = new moodle_url('/admin/tool/broadcast/manage.php', ['id' => $context->id]);
    $pluginname = get_string('pluginname', 'tool_broadcast');
    $node = navigation_node::create(
        $pluginname,
        $url,
        navigation_node::NODETYPE_LEAF,
        'tool_broadcast',
        'tool_broadcast',
        new pix_icon('i/bullhorn', get_string('pluginname', 'tool_broadcast'))
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $node->make_active();
    }

    $navigation->add_node($node);
}

/**
 * Renders the broadcast form for the modal on the broadcast management screen.
 *
 * @param array $args
 * @return string $o Form HTML.
 */
function tool_broadcast_output_fragment_new_base_form($args): string {

    $context = $args['context'];
    require_capability('tool/broadcast:createbroadcasts', $context);
    $contextid = $context->id;

    if (!empty($args['action'])) {
        $broadcast = new \tool_broadcast\broadcast();
        $formdata = $broadcast->get_broadcast_formdata($args['broadcastid']);
        $action = $args['action'];
        $contextid = (int)$formdata['contextid'];
        $broadcastid = $args['broadcastid'];
    } else {
        $serialiseddata = json_decode($args['jsonformdata'], true);
        $formdata = [];
        $action = '';
        $broadcastid = 0;
    }

    if (!empty($serialiseddata)) {
        parse_str($serialiseddata, $formdata);
    }

    $customdata = array('contextid' => $contextid, 'action' => $action, 'broadcastid' => $broadcastid);
    $mform = new \tool_broadcast\output\create_form(null, $customdata,
        'post', '', array('class' => 'ignoredirty'), true, $formdata);

    if (!empty($serialiseddata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o = ob_get_contents();
    ob_end_clean();

    return $o;
}

/**
 * Renders the broadcast table on the broadcast management screen.
 * We update the table via ajax, when a broadcast is created or changed.
 *
 * @param array $args
 * @return string $o Form HTML.
 */
function tool_broadcast_output_fragment_table($args): string {
    global $CFG, $PAGE;

    $context = $args['context'];
    require_capability('tool/broadcast:createbroadcasts', $context);

    $baseurl = new moodle_url('admin/tool/broadcast/manage.php', ['id' => $context->id]);
    $output = $PAGE->get_renderer('tool_broadcast');

    $o = $output->render_message_table($context, $baseurl, 0);

    return $o;
}

/**
 * Adds required JS and startup values for broadcast Modals.
 */
function tool_broadcast_before_footer(): void {
    global $PAGE, $USER;
    $context = $PAGE->context;
    $createurl = new moodle_url('/admin/tool/broadcast/manage.php', ['id' => context_system::instance()->id]);
    $createpage = $PAGE->url->compare($createurl);

    // Only include the required JS on this page if user has the required capability to view messages.
    if (has_capability('tool/broadcast:viewbroadcasts', $context) && !$createpage) {
        $PAGE->requires->js_call_amd('tool_broadcast/broadcast_modal', 'init', array($context->id, $USER->id));
    }

}

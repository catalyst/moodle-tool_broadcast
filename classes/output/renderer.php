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
 * Renderer class for manage broadcast page.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_broadcast\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer class for broadcast rules page.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render the HTML for the message management table.
     *
     * @param int $broadcastid The brodcast id to get the acknowledments from.
     * @param int $contextid The context id.
     * @param string $baseurl the base url to render the table on.
     * @param int $page the page number for pagination.
     *
     * @return string $output HTML for the table.
     */
    public function render_ackreport(int $broadcastid, int $contextid, string $baseurl, int $page = 0) {
        $renderable = new ackreport_table('tool_broadcast_ackreport', $broadcastid, $contextid, $baseurl, $page);
        $perpage = 50;

        ob_start();
        $renderable->out($perpage, true);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Render the HTML for the message management table.
     *
     * @param \context $context The context we are displaying broadcasts for
     * @param string $baseurl the base url to render the table on.
     * @param int $page the page number for pagination.
     *
     * @return string $output HTML for the table.
     */
    public function render_message_table(\context $context, string $baseurl, int $page = 0) {
        $renderable = new broadcast_table($context, 'tool_broadcast', $baseurl, $page);
        $perpage = 50;
        ob_start();
        $renderable->out($perpage, true);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Html to add a button for adding a new broadcast.
     *
     * @return string html for the button.
     */
    private function render_add_button(): string {

        $button = \html_writer::tag(
            'button',
            get_string('addbroadcast', 'tool_broadcast'),
            array('class' => 'btn btn-primary mb-3 d-flex ml-auto mr-auto', 'id' => 'local-broadcast-add-broadcast'));

        return $button;
    }

    /**
     * Render the HTML for the loading spinner.
     *
     * @return string The rendered HTML.
     */
    private function get_loader(): string {

        return $this->render_from_template('tool_broadcast/loader', array());
    }

    /**
     * Main method that renders page content.
     *
     * @param \context $context The context we are displaying broadcasts for
     * @param string $baseurl Base url for table.
     * @param int $page The page to display.
     * @return string Rendered HTML.
     */
    public function render_content(\context $context, string $baseurl, int $page = 0): string {
        $html = $this->render_add_button();
        $html .= \html_writer::start_div('tool-broadcast-table-container', array('id' => 'tool-broadcast-table-container'));
        $html .= $this->get_loader();
        $html .= \html_writer::start_div('tool-broadcast-table', array('id' => 'tool-broadcast-table'));
        $html .= $this->render_message_table($context, $baseurl, $page);
        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }
}

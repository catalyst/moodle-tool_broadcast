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
     * Render the html for the message management table.
     *
     * @param string $baseurl the base url to render the table on.
     * @param int $page the page number for pagination.
     * @param int $perpage amount of records per page for pagination.
     * @param string $download dataformat type. One of csv, xhtml, ods, etc
     *
     * @return string $output html for display
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function render_message_table(string $baseurl, int $page = 0) {
        $renderable = new broadcast_table('tool_broadcast', $baseurl, $page);
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

    private function get_loader(bool $hidden): string {
        $context = array('hidden' => $hidden);

        return $this->render_from_template('tool_broadcast/loader', $context);
    }

    /**
     *
     * @param int $courseid
     * @param string $baseurl
     * @param int $page
     * @param int $perpage
     * @param string $download
     * @return string
     */
    public function render_content(string $baseurl, int $page = 0): string {

        $html = $this->render_add_button();
        $html .= \html_writer::start_div('tool-broadcast-table-container', array('id' => 'tool-broadcast-table-container'));
        $html .= $this->get_loader(true);
        $html .= \html_writer::start_div('tool-broadcast-table', array('id' => 'tool-broadcast-table'));
        $html .= $this->render_message_table($baseurl, $page);
        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }
}

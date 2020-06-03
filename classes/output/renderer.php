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
    private function render_message_table(int $courseid, string $baseurl, int $page = 0, int $perpage = 50) {
        $url = new \moodle_url($baseurl, array('id' => $courseid));
        $renderable = new broadcast_table('tool_broadcast', $url, $perpage, $page);
        ob_start();
        $renderable->out($renderable->pagesize, true);
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
     *
     * @param int $courseid
     * @param string $baseurl
     * @param int $page
     * @param int $perpage
     * @param string $download
     * @return string
     */
    public function render_content(int $courseid, string $baseurl, int $page = 0,
        int $perpage = 50, string $download = ''): string {

        $html = $this->render_add_button();
        $html .= $this->render_message_table($courseid, $baseurl, $page, $perpage);

        return $html;
    }
}

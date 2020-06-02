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

    public function render_content(): string {
        $html = $this->render_add_button();

        return $html;
    }
}

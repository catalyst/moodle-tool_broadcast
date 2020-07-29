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
 * Form to select broadcast message acknowledge report.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_broadcast\output;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Form to select broadcast message acknowledge report.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ackreport_form extends \moodleform {

    /**
     * Build form for the broadcast message.
     *
     * {@inheritDoc}
     * @see \moodleform::definition()
     */
    public function definition() {

        $mform = $this->_form;
        $mform->disable_form_change_checker();

        if (!empty($this->_customdata['broadcastid'])) {
            $broadcastid = $this->_customdata['broadcastid'];
        } else {
            $broadcastid = 0;
        }

        // Form heading.
        $mform->addElement('html', \html_writer::div(get_string('selectbroadcastdesc', 'tool_broadcast'), 'form-description mb-3'));

        // Broadcast selector.
        $broadcast = new \tool_broadcast\broadcast();
        $broadcasts = array(0 => get_string('selectbroadcast', 'tool_broadcast')) + $broadcast->get_broadcast_names();

        $options = array('onchange' => 'javascript:this.form.submit();');

        $mform->addElement('select', 'broadcastid', get_string('selectbroadcast', 'tool_broadcast'), $broadcasts, $options);
        $mform->setDefault('broadcastid', $broadcastid);

    }
}

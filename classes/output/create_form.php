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
 * Form to create broadcast message.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_broadcast\output;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Form to create broadcast message.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_form extends \moodleform {

    /**
     * Build form for the broadcast message.
     *
     * {@inheritDoc}
     * @see \moodleform::definition()
     */
    public function definition() {

        $mform = $this->_form;
        $contextid = $this->_customdata['contextid'];

        // Context ID.
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->setType('contextid', PARAM_INT);

        // Form heading.
        $mform->addElement('html', \html_writer::div(get_string('createbroadcastdesc', 'tool_broadcast'), 'form-description mb-3'));

        // Course fullname.
        $mform->addElement('text', 'title', get_string('broadcasttitle', 'tool_broadcast'), 'maxlength="254" size="50"');
        $mform->addHelpButton('title', 'broadcasttitle', 'tool_broadcast');
        $mform->addRule('title', get_string('missingbroadcasttitle', 'tool_broadcast'), 'required', null, 'client');
        $mform->setType('title', PARAM_TEXT);

        // Course shortname.
        $mform->addElement('text', 'message', get_string('broadcastmessage', 'tool_broadcast'), 'maxlength="100" size="20"');
        $mform->addHelpButton('message', 'broadcastmessage', 'tool_broadcast');
        $mform->addRule('message', get_string('missingbroadcastmessage', 'tool_broadcast'), 'required', null, 'client');
        $mform->setType('message', PARAM_TEXT);

        $this->add_action_buttons();

    }
}

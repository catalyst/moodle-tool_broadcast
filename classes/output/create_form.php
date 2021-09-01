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
        $mform->disable_form_change_checker();

        $canaddatsite = has_capability('tool/broadcast:createbroadcasts', \context_system::instance());
        $categories = \core_course_category::make_categories_list('tool/broadcast:createbroadcasts');

        $contextid = $this->_customdata['contextid'];

        if (!empty($this->_customdata['action'])) {
            $action = $this->_customdata['action'];
        } else {
            $action = '';
        }

        if (!empty($this->_customdata['broadcastid'])) {
            $broadcastid = $this->_customdata['broadcastid'];
        } else {
            $broadcastid = 0;
        }

        // Context ID.
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->setType('contextid', PARAM_INT);

        // Action.
        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_ALPHA);

        // Broadcast ID.
        $mform->addElement('hidden', 'broadcastid', $broadcastid);
        $mform->setType('broadcastid', PARAM_INT);

        // Form heading.
        $mform->addElement('html', \html_writer::div(get_string('createbroadcastdesc', 'tool_broadcast'), 'form-description mb-3'));

        // Broadcast message title.
        $mform->addElement('text', 'title', get_string('broadcasttitle', 'tool_broadcast'), 'maxlength="254" size="58"');
        $mform->addHelpButton('title', 'broadcasttitle', 'tool_broadcast');
        $mform->addRule('title', get_string('missingbroadcasttitle', 'tool_broadcast'), 'required', null, 'client');
        $mform->setType('title', PARAM_TEXT);

        // Broadcast message body.
        $bodyoptions = array(
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 0,
            'context' => null,
            'noclean' => 0,
            'trusttext' => 0,
            'enable_filemanagement' => false,
            'autosave' => false);
        $bodysize = array('rows' => 5, 'cols' => 30);
        $mform->addElement('editor', 'message', get_string('broadcastmessage', 'tool_broadcast'), $bodysize, $bodyoptions);
        $mform->addHelpButton('message', 'broadcastmessage', 'tool_broadcast');
        $mform->addRule('message', get_string('missingbroadcastmessage', 'tool_broadcast'), 'required', null, 'client');
        $mform->setType('message', PARAM_RAW);

        // Scope settings.
        $scopesite = [];
        if ($canaddatsite) {
            $scopesite[0] = get_string('scopesite:site', 'tool_broadcast');
        }
        if (!empty($categories)) {
            $scopesite[1] = get_string('scopesite:category', 'tool_broadcast');
        }
        $scopesite[2] = get_string('scopesite:course', 'tool_broadcast');

        $mform->addElement('select', 'scopesite', get_string('scopesite', 'tool_broadcast'), $scopesite);
        $mform->addHelpButton('scopesite', 'scopesite', 'tool_broadcast');

        if (!empty($categories)) {
            $categoryoptions = array(
                'multiple' => false,
                'placeholder' => get_string('findcategory', 'tool_broadcast'),
                'noselectionstring' => get_string('findcategory', 'tool_broadcast'),
            );
            $mform->addElement('autocomplete', 'categories', get_string('categories', 'tool_broadcast'),
                $categories, $categoryoptions);
            $mform->hideIf('categories', 'scopesite', 'ne', 1);
        }

        $broadcast = new \tool_broadcast\broadcast();
        $courses = $broadcast->get_courses();

        $courseoptions = array(
            'multiple' => false,
            'placeholder' => get_string('findcourse', 'tool_broadcast'),
            'noselectionstring' => get_string('findcourse', 'tool_broadcast'),
        );
        $mform->addElement('autocomplete', 'courses', get_string('courses', 'tool_broadcast'), $courses, $courseoptions);
        $mform->hideIf('courses', 'scopesite', 'ne', 2);

        // Mode setting.
        $scopesite = array(
            1 => get_string('mode:modal', 'tool_broadcast'),
            2 => get_string('mode:bootstrap', 'tool_broadcast'),
            3 => get_string('mode:both', 'tool_broadcast')
        );
        $mform->addElement('select', 'mode', get_string('mode', 'tool_broadcast'), $scopesite);
        $mform->addHelpButton('mode', 'mode', 'tool_broadcast');

        // Active date.
        $activeoptions = array(
            'startyear' => date("Y"),
            'stopyear'  => 2030,
        );
        $mform->addElement('date_time_selector', 'activefrom', get_string('activefrom', 'tool_broadcast'), $activeoptions);
        $mform->addHelpButton('activefrom', 'activefrom', 'tool_broadcast');

        // Expiry date.
        $expiryoptions = array(
            'startyear' => date("Y"),
            'stopyear'  => 2030,
            'defaulttime' => time() + HOURSECS
        );
        $mform->addElement('date_time_selector', 'expiry', get_string('expiry', 'tool_broadcast'), $expiryoptions);
        $mform->addHelpButton('expiry', 'expiry', 'tool_broadcast');

        // Logged in users.
        $mform->addElement('advcheckbox', 'loggedin', get_string('loggedin', 'tool_broadcast'),
            get_string('loggedinonly', 'tool_broadcast'), array(), array(0, 1));
        $mform->addHelpButton('loggedin', 'loggedin', 'tool_broadcast');

        // Action buttons.
        if ($action == 'edit') {
            $btnstring = get_string('updatebtn', 'tool_broadcast');
        } else {
            $btnstring = get_string('createbtn', 'tool_broadcast');
        }
        $this->add_action_buttons(true, $btnstring);

    }
}

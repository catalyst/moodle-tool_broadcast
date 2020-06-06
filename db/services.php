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
 * Tool broadcast web service external functions and service definitions.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define the web service functions to install.
$functions = array(
    'tool_broadcast_submit_create_form' => array(
        'classname' => 'tool_broadcast_external',
        'methodname' => 'submit_create_form',
        'classpath' => '',
        'description' => 'Handles broadcast message ajax form submission.',
        'type' => 'write',
        'ajax' => true
    ),
    'tool_broadcast_get_broadcasts' => array(
        'classname' => 'tool_broadcast_external',
        'methodname' => 'get_broadcasts',
        'classpath' => '',
        'description' => 'Handles broadcast message fetching.',
        'type' => 'read',
        'ajax' => true
    ),
    'tool_broadcast_check_broadcasts' => array(
        'classname' => 'tool_broadcast_external',
        'methodname' => 'check_broadcasts',
        'classpath' => '',
        'description' => 'checks if the user has a broadcast message.',
        'loginrequired' => false,
        'type' => 'read',
        'ajax' => true
    ),
    'tool_broadcast_acknowledge_broadcast' => array(
        'classname' => 'tool_broadcast_external',
        'methodname' => 'acknowledge_broadcast',
        'classpath' => '',
        'description' => 'Handles user acknowledgement of a broadcast.',
        'type' => 'write',
        'ajax' => true
    ),
);

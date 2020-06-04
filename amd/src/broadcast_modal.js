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
 * This module provides the broadcast message modal that displays messages to users.
 *
 * @module     broadcast
 * @package    tool
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
        function() {

    /**
     * Module level variables.
     */
    var BroadcastModal = {};
    var contextid;

    BroadcastModal.init = function(context) {
        contextid = context;

        // TODO: setup a setinterval that poles ajax with a variable initial.
        // TODO: pass in a trigger that grabs messages instantly if they exist.

        window.console.log(contextid);

    };

    return BroadcastModal;
});

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
 * Plugin strings are defined here.
 *
 * @package     tool_broadcast
 * @category    string
 * @copyright   2020 Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Broadcast message';

$string['acknowledgereport'] = 'Broadcast acknowledgement';
$string['acknowledgereportbroadcast'] = '{$a} acknowledgement report';
$string['acknowledgereporttitle'] = 'Broadcast acknowledgement report';
$string['activefrom'] = 'Active from';
$string['activefrom_help'] = 'The time and date from which the message will be active .';
$string['addbroadcast'] = 'Add new broadcast';
$string['categories'] = 'Select category';
$string['categories_help'] = 'Select the category for the message. The messge will be displayed to all courses and pages in this category';
$string['courses'] = 'Select course';
$string['courses_help'] = 'Select the course for the message. The messge will be displayed to all users and pages in this course';
$string['createbroadcastdesc'] = 'Create a new broadcast message.';
$string['createbroadcastfail'] = 'Broadcast message creation failed';
$string['createbtn'] = 'Create broadcast';
$string['updatebtn'] = 'Update broadcast';
$string['broadcast:createbroadcasts'] = 'Can create broadcast messages';
$string['broadcast:viewbroadcasts'] = 'Can view broadcast messages';
$string['broadcastdetails'] = 'Enter broadcast details';
$string['broadcastmessage'] = 'Broadcast message';
$string['broadcastmessage_help'] = 'This is the body of the message used in the broadcast shown to users.';
$string['broadcasttitle'] = 'Broadcast title';
$string['broadcasttitle_help'] = 'This is the title used in the broadcast message shown to users.';
$string['deletebroadcast'] = 'Delete broadcast';
$string['duplicatebroadcast'] = 'Copy broadcast';
$string['editbroadcast'] = 'Edit broadcast';
$string['expiry'] = 'Expiry';
$string['expiry_help'] = 'The time and date the messages expires and will not be shown to users anymore.';
$string['findcategory'] = 'Find category';
$string['findcourse'] = 'Find course';
$string['loading'] = 'Loading...';
$string['loggedin'] = 'Logged in users';
$string['loggedin_help'] = 'When enabled only users who are logged in at the time of the message becoming active will see the message.';
$string['loggedinonly'] = 'Logged in only';
$string['manage'] = 'Manage broadcasts';
$string['missingbroadcastmessage'] = 'Missing broadcast title';
$string['missingbroadcasttitle'] = 'Missing broadcast title';
$string['privacy:metadata:tool_broadcast'] = 'Data relating users for the broadcast plugin';
$string['privacy:metadata:tool_broadcast_user'] = 'Data relating users with broadcast events';
$string['privacy:metadata:tool_broadcast_user:id'] = 'Record ID';
$string['privacy:metadata:tool_broadcast_user:userid'] = 'The ID of the user that is effected by the broadcast';
$string['privacy:metadata:tool_broadcast_user:contextid'] = 'The context ID that relates to the broadcast';
$string['privacy:metadata:tool_broadcast_user:acktime'] = 'The time the user acknowledged the broadcast';
$string['report:acktime'] = 'Time acknowledged';
$string['report:location'] = 'Location';
$string['report:title'] = 'Title';
$string['report:scope'] = 'Scope';
$string['report:loggedin'] = 'Logged in users';
$string['report:created'] = 'Created';
$string['report:start'] = 'Start';
$string['report:end'] = 'End';
$string['report:actions'] = 'Actions';
$string['viewackreport'] = 'View user acknowledgements';
$string['scopesite'] = 'Scope';
$string['scopesite_help'] = 'The settings define the scope of the message and where it will be displayed.';
$string['scopesite:site'] = 'Site';
$string['scopesite:category'] = 'Category';
$string['scopesite:course'] = 'Course';
$string['selectbroadcast'] = 'Select Broadcast';
$string['selectbroadcastdesc'] = 'This reports shows which users have acknowledged a broadcast.';

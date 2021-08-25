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
 * Tool broadcast DB upgrade definition.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_broadcast_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2020061800) {

        // Define field mode to be added to tool_broadcast.
        $table = new xmldb_table('tool_broadcast');
        $field = new xmldb_field('mode', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'loggedin');

        // Conditionally launch add field mode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Broadcast savepoint reached.
        upgrade_plugin_savepoint(true, 2020061800, 'tool', 'broadcast');
    }

    if ($oldversion < 2021082500) {
        global $DB;

        // Define table to be modified.
        $table = new xmldb_table('tool_broadcast');

        // Ok, the 'loggedin' column is a byte and we really want this to be an int. However using 'change_field_type'
        // directly results in "ERROR: column "loggedin" cannot be cast automatically to type bigint". So, let's create
        // a field we can store the data of 'loggedin' in.
        $addfield = new xmldb_field('loggedin2', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        if (!$dbman->field_exists($table, $addfield)) {
            $dbman->add_field($table, $addfield);
        }

        if ($broadcasts = $DB->get_records('tool_broadcast')) {
            foreach ($broadcasts as $broadcast) {
                $updatedata = new stdClass();
                $updatedata->id = $broadcast->id;
                $updatedata->loggedin2 = (int) $broadcast->loggedin;

                $DB->update_record('tool_broadcast', $updatedata);
            }
        }

        // Delete the 'loggedin' field.
        $deletefield = new xmldb_field('loggedin');
        $dbman->drop_field($table, $deletefield);

        // Rename the 'loggedin2' field to 'loggedin'.
        $dbman->rename_field($table, $addfield, 'loggedin');

        // Broadcast savepoint reached.
        upgrade_plugin_savepoint(true, 2021082500, 'tool', 'broadcast');
    }

    return true;
}

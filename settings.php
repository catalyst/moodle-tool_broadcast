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
 * Plugin administration pages are defined here.
 *
 * @package     tool_broadcast
 * @copyright   2020 Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('root', new admin_category('tool_broadcast', get_string('pluginname', 'tool_broadcast')), 'security');

    $workflowsettings = new admin_externalpage('tool_broadcast_broadcastsettings',
        get_string('manage', 'tool_broadcast'),
        new moodle_url('/admin/tool/broadcast/manage.php', ['id' => context_system::instance()->id]));

    $ADMIN->add('tool_broadcast', $workflowsettings);

    // Report link.
    $ADMIN->add('reports', new admin_externalpage('tool_broadcast_report',
        get_string('acknowledgereport', 'tool_broadcast'), "$CFG->wwwroot/admin/tool/broadcast/acknowledgereport.php"));
}

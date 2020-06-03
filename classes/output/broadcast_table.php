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
 * Renderable table for broadcast messages.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_broadcast\output;

require_once($CFG->libdir . '/tablelib.php');

defined('MOODLE_INTERNAL') || die;

use \table_sql;
use \renderable;

/**
 * Renderable table for broadcast messages.
 *
 * @package    tool_broadcast
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class broadcast_table extends table_sql implements renderable {

    /**
     * The required fields from the DB for this table.
     *
     * @var string
     */
    const FIELDS = 'id, contextid, title, body, loggedin, timecreated, timestart, timeend';

    /**
     * The default WHERE clause.
     *
     * @var string
     */
    const DEFAULT_WHERE = 'id > 0';


    /**
     * report_table constructor.
     *
     * @param string $uniqueid Unique id of table.
     * @param string $baseurl the base url to render this report on.
     * @param int $page the page number for pagination.
     * @param int $perpage amount of records per page for pagination.
     * @param string $download dataformat type. One of csv, xhtml, ods, etc
     *
     * @throws \coding_exception
     */
    public function __construct(string $uniqueid, string $baseurl, int $page = 0,
                                int $perpage = 50) {
        parent::__construct($uniqueid);

        $this->set_attribute('id', 'tool_broadcast_broadcast_table');
        $this->set_attribute('class', 'generaltable generalbox');
        $this->downloadable = false;
        $this->define_baseurl($baseurl);
        $this->define_columns(
            array(
                'title',
                'scope',
                'loggedin',
                'created',
                'start',
                'end',
                'status',
                'actions'
            ));
        $this->define_headers(array(
            get_string('report:title', 'tool_broadcast'),
            get_string('report:scope', 'tool_broadcast'),
            get_string('report:loggedin', 'tool_broadcast'),
            get_string('report:created', 'tool_broadcast'),
            get_string('report:start', 'tool_broadcast'),
            get_string('report:end', 'tool_broadcast'),
            get_string('report:status', 'tool_broadcast'),
            get_string('report:actions', 'tool_broadcast'),
        ));
        $this->column_class('created', 'mdl-right');
        $this->column_class('start', 'mdl-right');
        $this->column_class('end', 'mdl-right');

        // Setup pagination.
        $this->currpage = $page;
        $this->pagesize = $perpage;
        $this->sortable(true);
        $this->set_sql(self::FIELDS, '{tool_broadcast}', self::DEFAULT_WHERE);

    }

    /**
     * Get content for videostreams column.
     * We use `videostreams` field for sorting, requires `videostreams` and
     * `audiostreams` fields.
     *
     * @param \stdClass $row
     *
     * @return string html used to display the video field.
     *
     * @throws \moodle_exception
     */
    public function col_title($row) {
        return $this->format_text($row->title);
    }

    /**
     * Get content for format column.
     * Requires `metadata` field.
     *
     * @param \stdClass $row
     *
     * @return string html used to display the type field.
     */
    public function col_scope($row) {
        return $this->format_text($row->contextid);
    }

    /**
     * Get content for width column.
     * We use `width` for sorting purposes, requires `width` and `height` fields.
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_loggedin($row) {
        return $this->format_text($row->loggedin);
    }


    /**
     * Get content for created column.
     * Displays when the conversion was started
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_created($row) {
        $date = userdate($row->timecreated, get_string('strftimedatetime', 'langconfig'));
        return $this->format_text($date);
    }

    /**
     * Get content for created column.
     * Displays when the conversion was started
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_start($row) {
        $date = userdate($row->timestart, get_string('strftimedatetime', 'langconfig'));
        return $this->format_text($date);
    }

    /**
     * Get content for created column.
     * Displays when the conversion was started
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_end($row) {
        $date = userdate($row->timeend, get_string('strftimedatetime', 'langconfig'));
        return $this->format_text($date);
    }

    /**
     * Get content for width column.
     * We use `width` for sorting purposes, requires `width` and `height` fields.
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_status($row) {
        return $this->format_text('Active');
    }

    /**
     * Get content for width column.
     * We use `width` for sorting purposes, requires `width` and `height` fields.
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_actions($row) {
        return $this->format_text('some actions');
    }

}


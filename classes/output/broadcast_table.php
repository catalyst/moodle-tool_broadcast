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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

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
     * report_table constructor.
     *
     * @param string $uniqueid Unique id of table.
     * @param string $baseurl the base url to render this report on.
     * @param int $page the page number for pagination.
     *
     * @throws \coding_exception
     */
    public function __construct(string $uniqueid, string $baseurl, int $page = 0) {
        parent::__construct($uniqueid);

        $this->set_attribute('id', 'tool_broadcast_broadcast_table');
        $this->set_attribute('class', 'generaltable generalbox');
        $this->downloadable = false;
        $this->define_baseurl($baseurl);
        $this->define_columns(
            array(
                'title',
                'contextid',
                'loggedin',
                'timecreated',
                'timestart',
                'timeend',
                'actions'
            ));
        $this->define_headers(array(
            get_string('report:title', 'tool_broadcast'),
            get_string('report:scope', 'tool_broadcast'),
            get_string('report:loggedin', 'tool_broadcast'),
            get_string('report:created', 'tool_broadcast'),
            get_string('report:start', 'tool_broadcast'),
            get_string('report:end', 'tool_broadcast'),
            get_string('report:actions', 'tool_broadcast'),
        ));
        $this->column_class('created', 'mdl-right');
        $this->column_class('start', 'mdl-right');
        $this->column_class('end', 'mdl-right');

        // Setup pagination.
        $this->currpage = $page;
        $this->sortable(true);
        $this->column_nosort = array('contextid', 'actions');

    }

    /**
     * Get any extra classes names to add to this row in the HTML.
     *
     * @param array $row the data for this row.
     * @return string added to the class="" attribute of the tr.
     */
    public function get_row_class($row) {
        if (time() > $row->timeend) {
            return 'dimmed_text';
        } else {
            return 'font-weight-bold';
        }
    }

    /**
     * Get content for title column.
     *
     * @param \stdClass $row
     * @return string html used to display the video field.
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
     * @return string html used to display the type field.
     */
    public function col_contextid($row) {

        $context = \context::instance_by_id($row->contextid);
        $name = $context->get_context_name();
        $url = $context->get_url();

        $link = \html_writer::link($url, $name);

        return $this->format_text($link);
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

        if ($row->loggedin) {
            $loggedin = get_string('yes');
        } else {
            $loggedin = get_string('no');
        }

        return $this->format_text($loggedin);
    }


    /**
     * Get content for created column.
     * Displays when the conversion was started
     *
     * @param \stdClass $row
     *
     * @return string html used to display the column field.
     */
    public function col_timecreated($row) {
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
    public function col_timestart($row) {
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
    public function col_timeend($row) {
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
    public function col_actions($row) {
        global $OUTPUT;

        $manage = '';

        $icon = $OUTPUT->render(new \pix_icon('t/edit', get_string('editbroadcast', 'tool_broadcast')));
        $manage .= \html_writer::link('#', $icon, array('class' => 'action-icon edit', 'id' => 'tool-broadcast-edit-' . $row->id));

        $icon = $OUTPUT->render(new \pix_icon('t/copy', get_string('duplicatebroadcast', 'tool_broadcast')));
        $manage .= \html_writer::link('#', $icon, array('class' => 'action-icon copy', 'id' => 'tool-broadcast-copy-' . $row->id));

        $deleteurl = new \moodle_url('/admin/tool/broadcast/manage.php', array('broadcastid' => $row->id,
            'action' => 'delete', 'sesskey' => sesskey()));
        $icon = $OUTPUT->render(new \pix_icon('t/delete', get_string('deletebroadcast', 'tool_broadcast')));
        $manage .= \html_writer::link($deleteurl, $icon, [
            'class' => 'action-icon delete',
            'data-confirmation-cancel-target' =>
                (new \moodle_url('/admin/tool/broadcast/manage.php', ['id' => $row->contextid]))->out(false),
            'data-confirmation-title' => get_string('deleteconfirm', 'tool_broadcast'),
            'data-confirmation-question' => get_string('deletebroadcastconfirm', 'tool_broadcast'),
            'data-confirmation-yes-text' => get_string('yes'),
            'data-confirmation-no-text' => get_string('no'),
        ]);

        $reporturl = new \moodle_url('/admin/tool/broadcast/acknowledgereport.php', array('broadcastid' => $row->id));
        $icon = $OUTPUT->render(new \pix_icon('i/report', get_string('viewackreport', 'tool_broadcast')));
        $manage .= \html_writer::link($reporturl, $icon, array('class' => 'action-icon'));

        return $manage;
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        $sort = $this->get_sql_sort();

        $countsql = "SELECT COUNT(1) FROM {tool_broadcast}";
        $sql = "SELECT * FROM {tool_broadcast}";

        if (!empty($sort)) {
            $sql .= " ORDER BY $sort";
        }

        $total = $DB->count_records_sql($countsql);
        $this->pagesize($pagesize, $total);

        $records = $DB->get_records_sql($sql, array(), $this->get_page_start(), $this->get_page_size());

        $this->rawdata = $records;

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars(true);
        }
    }

}

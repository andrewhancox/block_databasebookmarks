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
 * @package block_databasebookmarks
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @author Open Source Learning <enquiries@opensourcelearning.co.uk>
 * @link https://opensourcelearning.co.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2024, Andrew Hancox
 */

namespace block_databasebookmarks;

use html_writer;
use mod_data\event\template_updated;
use stdClass;

class lib {
    public static function handleplaceholders(template_updated $event) {
        global $DB;
        $dataid = $event->other['dataid'];

        $data = $DB->get_record('data', ['id' => $dataid]);

        $templates = ['singletemplate', 'listtemplate', 'asearchtemplate'];

        foreach ($templates as $template) {
            $data->$template = self::replacebookmarkplaceholder($data->$template, $dataid);
        }

        $DB->update_record('data', $data);
    }

    public static function getbookmarks() {
        global $USER, $DB;

        $sql = "SELECT bm.id, bm.datarecordid AS recordid, d.id AS instanceid, cm.id as cmid, c.id as courseid, bm.bookmarkname as bookmarkname
                FROM {block_databasebookmarks} bm
                INNER JOIN {data_records} dr on bm.datarecordid = dr.id
                INNER JOIN {data} d on d.id = dr.dataid
                INNER JOIN {course_modules} cm on cm.instance = d.id
                INNER JOIN {modules} m on m.id = cm.module
                INNER JOIN {course} c on c.id = cm.course
                WHERE dr.approved = 1 AND m.name = 'data' AND m.visible = true AND c.visible = true AND bm.userid = :userid";

        $bookmarks = $DB->get_records_sql($sql, ['userid' => $USER->id]);

        return $bookmarks;
    }

    public static function createbookmark($rid, $bookmarkname) {
        global $USER, $DB;
        $bookmark = new stdClass();
        $bookmark->userid = $USER->id;
        $bookmark->datarecordid = $rid;
        $bookmark->bookmarkname = $bookmarkname;

        $DB->insert_record('block_databasebookmarks', $bookmark);
    }

    public static function deletebookmark($rid) {
        global $USER, $DB;

        return $DB->delete_records('block_databasebookmarks', ['userid' => $USER->id, 'datarecordid' => $rid]);
    }

    private static function replacebookmarkplaceholder($template, $dataid) {
        $bookmarklink = html_writer::link(
            '#',
            get_string('bookmark', 'block_databasebookmarks'),
            [
                'class' => 'data_bookmark_link',
                'data-moreurl' => '##moreurl##',
            ]
        );
        $bookmarkspan = html_writer::span($bookmarklink, 'data_bookmark_wrapper');
        $template = str_replace('##bookmark##', $bookmarkspan, $template);
        return $template;
    }
}

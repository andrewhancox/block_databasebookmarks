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
 * Main code for local plugin cohortthemes
 *
 * @package   local_cohortthemes
 * @copyright 2015 Andrew Hancox
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_databasebookmarks;

class lib {
    public static function handleplaceholders(\mod_data\event\template_updated $event) {
        global $DB;
        $dataid = $event->other['dataid'];

        $data = $DB->get_record('data', array('id' => $dataid));

        $templates = array('singletemplate', 'listtemplate', 'asearchtemplate');

        foreach ($templates as $template) {
            $data->$template = self::replacebookmarkplaceholder($data->$template, $dataid);
        }

        $DB->update_record('data', $data);
    }

    public static function getbookmarks() {
        global $USER, $DB;

        $sql = "SELECT bm.datarecordid AS recordid, d.id AS instanceid
                FROM mdl_block_databasebookmarks bm
                INNER JOIN mdl_data_records dr on bm.datarecordid = dr.id
                INNER JOIN mdl_data d on d.id = dr.dataid
                INNER JOIN mdl_course_modules cm on cm.instance = d.id
                INNER JOIN mdl_modules m on m.id = cm.module
                INNER JOIN mdl_course c on c.id = cm.course
                WHERE dr.approved = 1 AND m.name = 'data' AND m.visible = true AND c.visible = true AND bm.userid = :userid";

        return $DB->get_records_sql($sql, array('userid' => $USER->id));
    }

    public static function createbookmark($rid) {
        global $USER, $DB;
        $bookmark = new \stdClass();
        $bookmark->userid = $USER->id;
        $bookmark->datarecordid = $rid;

        $DB->insert_record('block_databasebookmarks', $bookmark);
    }

    public static function deletebookmark($rid) {
        global $USER, $DB;

        return $DB->delete_records('block_databasebookmarks', array('userid' => $USER->id, 'datarecordid' => $rid));
    }

    private static function replacebookmarkplaceholder($template, $dataid) {
        $bookmarklink = \html_writer::link(
            '#',
            get_string('bookmark', 'block_databasebookmarks'),
            array(
                'class' => 'data_bookmark_link',
                'data-moreurl' => '##moreurl##'
            )
        );
        $bookmarkspan = \html_writer::span($bookmarklink, 'data_bookmark_wrapper');
        $template = str_replace('##bookmark##', $bookmarkspan, $template);
        return $template;
    }
}
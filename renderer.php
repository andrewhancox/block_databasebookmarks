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
 * Print private files tree
 *
 * @package    block_databaseboomkarks
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_databasebookmarks_renderer extends plugin_renderer_base {
    public function render_databasebookmarks($bookmarks) {
        $output = '';

        $bookmarkslinks = array();
        foreach ($bookmarks as $bookmark) {
            $bookmarkmarkup = '';

            $url = new moodle_url(
                "/mod/data/view.php",
                array('d' => $bookmark->instanceid, 'rid' => $bookmark->recordid)
            );
            $label = $bookmark->recordid;
            $bookmarklink = html_writer::link($url, $label);
            $bookmarkmarkup .= html_writer::span($bookmarklink, 'bookmarklink');

            $iconenrolremove = $this->output->pix_url('t/delete');
            $iconimg = html_writer::img($iconenrolremove, get_string('delete'));
            $deletelink = html_writer::link('#', $iconimg, array('data-rid' => $bookmark->recordid, 'data-action' => 'delete', 'class' => 'data_deletebookmark_link'));
            $bookmarkmarkup .= html_writer::span($deletelink, 'deletelink');

            $bookmarkslinks[] = $bookmarkmarkup;
        }
        $output .= html_writer::alist($bookmarkslinks, array('class' => 'block_databasebookmarks_bookmarklist'));

        return $output;
    }
}
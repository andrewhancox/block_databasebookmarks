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

defined('MOODLE_INTERNAL') || die();

class block_databasebookmarks_renderer extends plugin_renderer_base {
    public function render_databasebookmarks($bookmarks) {
        $output = '';

        $bookmarkslinks = [];
        foreach ($bookmarks as $bookmark) {
            $bookmarkmarkup = '';

            $url = new moodle_url(
                "/mod/data/view.php",
                ['d' => $bookmark->instanceid, 'rid' => $bookmark->recordid]
            );
            $label = $bookmark->bookmarkname;
            $bookmarklink = html_writer::link($url, $label);
            $bookmarkmarkup .= html_writer::span($bookmarklink, 'bookmarklink');

            $bookmarkmarkup .= $this->output->action_icon('#', new pix_icon('t/delete', get_string('delete')), null,
                ['data-rid' => $bookmark->recordid, 'data-action' => 'delete', 'class' => 'data_deletebookmark_link']);

            $bookmarkslinks[] = $bookmarkmarkup;
        }
        $output .= html_writer::alist($bookmarkslinks, ['class' => 'block_databasebookmarks_bookmarklist']);

        return $output;
    }
}

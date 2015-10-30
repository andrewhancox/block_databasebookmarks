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
 * @package    block_databasebookmarks
 * @subpackage tag
 * @copyright  2015 onwards Andrew Hancox (andrewdchancox@googlemail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_databasebookmarks extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_databasebookmarks');
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function has_config() {
        return false;
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_config() {
        return true;
    }

    public function specialization() {
        // Load userdefined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_databasebookmarks');
        } else {
            $this->title = $this->config->title;
        }
    }

    public function get_required_javascript() {
        global $PAGE;

        $PAGE->requires->yui_module('moodle-block_databasebookmarks-bookmark', 'M.block_databasebookmarks.bookmark.init');
        $PAGE->requires->strings_for_js(array('deletebookmark', 'bookmark'), 'block_databasebookmarks');
        return parent::get_required_javascript();
    }

    public function get_content() {
        global $PAGE;

        $renderer = $PAGE->get_renderer('block_databasebookmarks');
        $bookmarks = \block_databasebookmarks\lib::getbookmarks();

        $this->content = new stdClass;
        $this->content->text = $renderer->render_databasebookmarks($bookmarks);

        return $this->content;
    }
}

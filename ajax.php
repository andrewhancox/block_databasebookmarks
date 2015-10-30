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
 * This file processes AJAX enrolment actions and returns JSON
 *
 * The general idea behind this file is that any errors should throw exceptions
 * which will be returned and acted upon by the calling AJAX script.
 *
 * @package    core_enrol
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../../config.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);

$PAGE->set_url(new moodle_url('/blocks/databasebookmarks/ajax.php', array('action'=>$action)));

require_login();
require_sesskey();

switch ($action) {
    case 'create':
        $rid  = required_param('rid', PARAM_INT);
        $bookmarkname  = required_param('bookmarkname', PARAM_ALPHANUMEXT);
        block_databasebookmarks\lib::createbookmark($rid, $bookmarkname);
        $bookmarks = \block_databasebookmarks\lib::getbookmarks();
        $renderer = $PAGE->get_renderer('block_databasebookmarks');
        echo $renderer->render_databasebookmarks($bookmarks);
        break;
    case 'delete':
        $rid  = required_param('rid', PARAM_INT);
        block_databasebookmarks\lib::deletebookmark($rid);
        $bookmarks = \block_databasebookmarks\lib::getbookmarks();
        $renderer = $PAGE->get_renderer('block_databasebookmarks');
        echo $renderer->render_databasebookmarks($bookmarks);
        break;
    case 'getids':
        $bookmarks = \block_databasebookmarks\lib::getbookmarks();
        echo json_encode(array_keys($bookmarks));
        break;
}


die();

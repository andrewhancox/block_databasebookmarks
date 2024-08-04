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

use block_databasebookmarks\lib;

define('AJAX_SCRIPT', true);

require('../../config.php');

$action = required_param('action', PARAM_ALPHANUMEXT);

$PAGE->set_url(new moodle_url('/blocks/databasebookmarks/ajax.php', ['action' => $action]));
$PAGE->set_context(context_system::instance());

require_login();
require_sesskey();

switch ($action) {
    case 'create':
        $rid = required_param('rid', PARAM_INT);
        $bookmarkname = required_param('bookmarkname', PARAM_TEXT);
        block_databasebookmarks\lib::createbookmark($rid, $bookmarkname);
        $bookmarks = lib::getbookmarks();
        $renderer = $PAGE->get_renderer('block_databasebookmarks');
        echo $renderer->render_databasebookmarks($bookmarks);
        break;
    case 'delete':
        $rid = required_param('rid', PARAM_INT);
        block_databasebookmarks\lib::deletebookmark($rid);
        $bookmarks = lib::getbookmarks();
        $renderer = $PAGE->get_renderer('block_databasebookmarks');
        echo $renderer->render_databasebookmarks($bookmarks);
        break;
    case 'getids':
        $bookmarks = lib::getbookmarks();
        echo json_encode(array_keys($bookmarks));
        break;
}

die();

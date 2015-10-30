/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @package totara
 * @subpackage facetoface
 */

M.block_databasebookmarks = M.block_databasebookmarks || {};
M.block_databasebookmarks.bookmark = {
    updateallbookmarklinks: function () {
        Y.io(M.cfg.wwwroot + '/blocks/databasebookmarks/ajax.php', {
            method: 'POST',
            data: 'action=getids&sesskey=' + M.cfg.sesskey,
            on: {
                complete: function (tid, response) {
                    data = Y.JSON.parse(response.responseText);
                    for (i = 0; i < data.length; i++) {
                        var rid = data[i];
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').set('text', M.util.get_string('deletebookmark', 'block_databasebookmarks'));
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').setAttribute('data-action', 'delete');
                    }
                }
            }
        });
    },
    init: function() {
        Y.all('.data_bookmark_link').each(function() {
            var moreurl = this.getAttribute('data-moreurl');
            var recordid = moreurl.match(/rid=([0-9])/i)[1];
            this.setAttribute('data-rid', recordid);
            this.setAttribute('data-action', 'create');
        });
        this.updateallbookmarklinks();
        Y.one('body').delegate('click', M.block_databasebookmarks.bookmark.handlebookmark, '.data_bookmark_link, .data_deletebookmark_link');
    },
    handlebookmark: function(e) {
        e.preventDefault();
        var rid = e.currentTarget.getAttribute('data-rid');
        var action = e.currentTarget.getAttribute('data-action');

        Y.io(M.cfg.wwwroot+'/blocks/databasebookmarks/ajax.php', {
            method:'POST',
            data:'rid='+rid+'&action='+action+'&sesskey='+M.cfg.sesskey,
            on: {
                complete: function(tid, response) {
                    Y.one('ul.block_databasebookmarks_bookmarklist').replace(response.responseText);
                    if (action == 'create') {
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').set('text', M.util.get_string('deletebookmark', 'block_databasebookmarks'));
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').setAttribute('data-action', 'delete');
                    } else {
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').set('text', M.util.get_string('bookmark', 'block_databasebookmarks'));
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').setAttribute('data-action', 'create');
                    }
                }
            }
        });
    },
};
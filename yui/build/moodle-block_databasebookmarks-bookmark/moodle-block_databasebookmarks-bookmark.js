YUI.add('moodle-block_databasebookmarks-bookmark', function (Y, NAME) {

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
    addbookmarktoavailabletags: function () {
        var actionsgrouplabel = M.util.get_string('buttons', 'mod_data');
        var actionsoptgroup = Y.one('select#availabletags optgroup[label="' + actionsgrouplabel + '"]');

        if (actionsoptgroup) {
            var label = M.util.get_string('bookmark', 'block_databasebookmarks')
            actionsoptgroup.append('<option value="##bookmark##">' + label + ' ##bookmark##</span>');
        }
    },
    init: function() {
        Y.all('.data_bookmark_link').each(function() {
            var moreurl = this.getAttribute('data-moreurl');
            var recordid = moreurl.match(/rid=([0-9]+)/i)[1];
            this.setAttribute('data-rid', recordid);
            this.setAttribute('data-action', 'create');
        });
        this.updateallbookmarklinks();
        this.addbookmarktoavailabletags();

        Y.one('body').delegate('click', M.block_databasebookmarks.bookmark.handlebookmark, '.data_bookmark_link, .data_deletebookmark_link');
    },
    deletedialog: function (rid) {
        var deletestring = M.util.get_string('deletebookmark', 'block_databasebookmarks');
        confirmation = {
            modal: true,
            title: deletestring,
            question: deletestring
        };
        dialog = new M.core.confirm(confirmation).on(
            'complete-yes',
            function (tid, response) {
                Y.io(M.cfg.wwwroot+'/blocks/databasebookmarks/ajax.php', {
                    method:'POST',
                    data:'rid='+rid+'&action=delete&sesskey='+M.cfg.sesskey,
                    on: {
                        complete: function(tid, response) {
                            Y.one('ul.block_databasebookmarks_bookmarklist').replace(response.responseText);
                            Y.all('.data_bookmark_link[data-rid="' + rid + '"]').set('text', M.util.get_string('bookmark', 'block_databasebookmarks'));
                            Y.all('.data_bookmark_link[data-rid="' + rid + '"]').setAttribute('data-action', 'create');
                        }
                    }
                });
            }
        );
    },
    handlebookmark: function(e) {
        e.preventDefault();
        var rid = e.currentTarget.getAttribute('data-rid');
        var action = e.currentTarget.getAttribute('data-action');

        if (action == 'create') {
            M.block_databasebookmarks.bookmark.createdialog(rid)
        } else {
            M.block_databasebookmarks.bookmark.deletedialog(rid);
        }
    },
    createdialog: function(rid) {
        var namelabel = M.util.get_string('bookmarkname', 'block_databasebookmarks');
        var title = M.util.get_string('bookmarkheader', 'block_databasebookmarks');
        var bodyContent = '<form><label for="bookmarkname_' + rid + '">' + namelabel + '</label><input name="bookmarkname_' + rid + '" id="bookmarkname_' + rid + '"/><div><input type="submit" value ="' + title + '" id="btncreatebookmark_' + rid + '"/></div></form>';

        var dialog = new M.core.dialogue ({
            headerContent: title,
            bodyContent  : bodyContent,
            width        : 300,
            zIndex       : 5,
            centered     : true,
            modal        : true,
            render       : true
        });
        dialog.show();


        var buttonselector = '#' + dialog.get('id') + ' #btncreatebookmark_' + rid + '';
        var textselector = '#' + dialog.get('id') + ' #bookmarkname_' + rid + '';

        Y.one(textselector).focus();

        Y.one(buttonselector).on('click', function(e) {
            e.preventDefault();
            var bookmarkname = Y.one(textselector).get('value');

            Y.io(M.cfg.wwwroot+'/blocks/databasebookmarks/ajax.php', {
                method:'POST',
                data:'rid='+rid+'&action=create&sesskey='+M.cfg.sesskey+'&bookmarkname='+bookmarkname,
                on: {
                    complete: function(tid, response) {
                        Y.one('ul.block_databasebookmarks_bookmarklist').replace(response.responseText);
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').set('text', M.util.get_string('deletebookmark', 'block_databasebookmarks'));
                        Y.all('.data_bookmark_link[data-rid="' + rid + '"]').setAttribute('data-action', 'delete');
                        dialog.destroy();
                    }
                }
            });
        });
    }
};

}, '@VERSION@', {"requires": ["node", "json", "moodle-core-notification-confirm", "io"]});

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
 * This module provides the broadcast message create modal.
 *
 * @module     broadcast
 * @package    tool
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/modal_factory', 'core/modal_events', 'core/ajax', 'core/fragment', 'core/notification'],
function(Str, ModalFactory, ModalEvents, Ajax, Fragment, Notification) {

    /**
     * Module level variables.
     */
    var Broadcast = {};
    var contextid;
    var modalObj;
    var spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';

    /**
     * Update the broadcast overview table with latest data.
     */
    var updateBroadcastTable = function() {
        var tableContainer = document.getElementById('tool-broadcast-table-container');
        var loader = tableContainer.getElementsByClassName('overlay-icon-container')[0];
        var tableElement = document.getElementById('tool-broadcast-table');

        loader.classList.remove('hide'); // Show loader if not already shown.

        Fragment.loadFragment('tool_broadcast', 'table', contextid)
        .done(function(response) {
            tableElement.innerHTML = response;
            loader.classList.add('hide');
            tableEventListeners(); // Re-add table event listeners.

        }).fail(function() {
            Notification.exception(new Error('Failed to update table.'));
        });
    };

    /**
     * Updates the body of the modal window.
     *
     * @param {Object} formdata
     * @private
     */
    var updateModalBody = function(formdata, broadcastid, action) {
        if (typeof formdata === "undefined") {
            formdata = {};
        }

        if (typeof broadcastid === "undefined") {
            broadcastid = 0;
        }

        if (typeof action === "undefined") {
            action = '';
        }

        var params = {
            'jsonformdata': JSON.stringify(formdata),
            'broadcastid': broadcastid,
            'action': action
        };

        Str.get_string('broadcastdetails', 'tool_broadcast').then(function(title) {
            modalObj.setTitle(title);
            modalObj.setBody(Fragment.loadFragment('tool_broadcast', 'new_base_form', contextid, params));
            return;
        }).catch(function() {
            Notification.exception(new Error('Failed to load string: broadcastdetails'));
        });
    };

    /**
     * Updates Moodle form with selected information.
     *
     * @param {Object} e
     * @private
     */
    var processModalForm = function(e) {
        e.preventDefault(); // Stop modal from closing.

        // Form data.
        var copyform = modalObj.getRoot().find('form').serialize();
        var formjson = JSON.stringify(copyform);

        // Handle invalid form fields for better UX.
        var ariainvalid = modalObj.getRoot().find('[aria-invalid="true"]');
        var errorclasses = modalObj.getRoot().find('.error');
        var invalid;

        if (ariainvalid.length) {
            invalid = ariainvalid.concat(errorclasses);
        }

        if (invalid !== undefined && invalid.length) {
            invalid.first().focus();
            return;
        }

        // Submit form via ajax.
        Ajax.call([{
            methodname: 'tool_broadcast_submit_create_form',
            args: {jsonformdata: formjson}
        }])[0].done(function() {
            // For submission succeeded.
            modalObj.setBody(spinner);
            modalObj.hide();
            updateBroadcastTable();
        }).fail(function() {
            // Form submission failed server side, redisplay with errors.
            updateModalBody(copyform);
        });

    };

    /**
     * Create the modal window.
     *
     * @private
     */
    var createModal = function() {
        Str.get_string('loading', 'tool_broadcast').then(function(title) {
            // Create the Modal.
            ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: title,
                body: spinner,
                large: true
            })
            .done(function(modal) {
                modalObj = modal;
                // Explicitly handle form click events.
                modalObj.getRoot().on('click', '#id_submitbutton', processModalForm);
                modalObj.getRoot().on('click', '#id_cancel', function(e) {
                    e.preventDefault();
                    modalObj.setBody(spinner);
                    modalObj.hide();
                });
            });
            return;
        }).catch(function() {
            Notification.exception(new Error('Failed to load string: loading'));
        });
    };

    var displayModalForm = function() {
        updateModalBody();
        modalObj.show();
    };

    var copyBroadcast = function(event) {
        event.preventDefault();
        var broadcastid = event.target.parentElement.id.substring(20);
        if (broadcastid != '') {
            updateModalBody({}, broadcastid, 'copy');
            modalObj.show();
        }
    };

    var editBroadcast = function(event) {
        event.preventDefault();
        var broadcastid = event.target.parentElement.id.substring(20);
        if (broadcastid != '') {
            updateModalBody({}, broadcastid, 'edit');
            modalObj.show();
        }
    };

    var tableEventListeners = function() {
        var edits = document.getElementsByClassName('action-icon edit');
        var copies = document.getElementsByClassName('action-icon copy');

        for (var i = 0; i < edits.length; i++) {
            edits[i].addEventListener('click', editBroadcast);
        }

        for (var i = 0; i < copies.length; i++) {
            copies[i].addEventListener('click', copyBroadcast);
        }
    };

    Broadcast.init = function(context) {
        contextid = context;
        createModal(); // Setup the initial Modal.
        tableEventListeners(); // Add the event listeners to action buttons in the table.

        var createBroadcastButton = document.getElementById('local-broadcast-add-broadcast');
        createBroadcastButton.addEventListener('click', displayModalForm);

    };

    return Broadcast;
});

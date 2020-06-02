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
    var CreateModal = {};
    var contextid;
    var modalObj;
    var spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';

    /**
     * Updates the body of the modal window.
     *
     * @param {Object} formdata
     * @private
     */
    const updateModalBody = (formdata) => {
        if (typeof formdata === "undefined") {
            formdata = {};
        }

        let params = {
                'jsonformdata': JSON.stringify(formdata),
        };

        Str.get_string('broadcastdetails', 'tool_broadcast').then((title) => {
            modalObj.setTitle(title);
            modalObj.setBody(Fragment.loadFragment('tool_broadcast', 'new_base_form', contextid, params));
            return;
        }).catch(() =>{
            Notification.exception(new Error('Failed to load string: broadcastdetails'));
        });
    };

    /**
     * Updates Moodle form with selected information.
     *
     * @param {Object} e
     * @private
     */
    const processModalForm = (e) => {
        e.preventDefault(); // Stop modal from closing.

        // Form data.
        let copyform = modalObj.getRoot().find('form').serialize();
        let formjson = JSON.stringify(copyform);

        // Handle invalid form fields for better UX.
        let ariainvalid = modalObj.getRoot().find('[aria-invalid="true"]');
        let errorclasses = modalObj.getRoot().find('.error');
        let invalid;

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
        }])[0].done(() => {
            // For submission succeeded.
            modalObj.setBody(spinner);
            modalObj.hide();
        }).fail(() => {
            // Form submission failed server side, redisplay with errors.
            updateModalBody(copyform);
        });

    };

    /**
     * Updates the body of the modal window.
     *
     * @param {Object} formdata
     * @private
     */
    const createModal = () => {
        Str.get_string('loading', 'tool_broadcast').then((title) => {
            // Create the Modal.
            ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: title,
                body: spinner,
                large: true
            })
            .done((modal) => {
                modalObj = modal;
                // Explicitly handle form click events.
                modalObj.getRoot().on('click', '#id_submitbutton', processModalForm);
                modalObj.getRoot().on('click', '#id_cancel', (e) => {
                    e.preventDefault();
                    modalObj.setBody(spinner);
                    modalObj.hide();
                });
            });
            return;
        }).catch(() => {
            Notification.exception(new Error('Failed to load string: loading'));
        });
    };

    const displayModalForm = () => {
        updateModalBody();
        modalObj.show();
    };

    CreateModal.init = function(context) {
        contextid = context;
        createModal(); // Setup the initial Modal.

        let createBroadcastButton = document.getElementById('local-broadcast-add-broadcast');
        createBroadcastButton.addEventListener('click', displayModalForm);

    };

    return CreateModal;
});

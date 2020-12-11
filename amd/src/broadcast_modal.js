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
 * This module provides the broadcast message modal that displays messages to users.
 *
 * @module     broadcast
 * @package    tool
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/modal_factory', 'core/modal_events', 'core/ajax', 'core/notification'],
function(Str, ModalFactory, ModalEvents, Ajax, Notification) {

    /**
     * Module level variables.
     */
    var BroadcastModal = {};
    var contextid;
    var modalObj;
    var spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';
    var messageQueue = {};

    /**
     * Get broadcast messages for this user.
     * This is only called if the check messages method returns true.
     */
    var getMessages = function() {
        Ajax.call([{
            methodname: 'tool_broadcast_get_broadcasts',
            args: {contextid: contextid}
        }])[0].done(function(response) {
            var messages = JSON.parse(response);
            for (var message in messages) {
                var messageId = messages[message].id;
                messageQueue[messageId] = messages[message];
            }
        }).fail(function() {
            window.console.error(new Error('Failed to get broadcast messages'));
        });
    };

    /**
     * Check to see if there are messages available.
     * This is done as a discrete ajax call, so we don't update the user session
     * everytime we poll, and prevent the user from being logged out due to inactivity.
     */
    var checkMessages = function() {
        if (document.hasFocus()) {
            Ajax.call([{
                methodname: 'tool_broadcast_check_broadcasts',
                args: {contextid: contextid}
            }], true, false)[0].done(function(response) {
                var responseObj = JSON.parse(response);
                if (responseObj) { // We have messages.
                    getMessages();
                }
            }).fail(function() {
                window.console.error(new Error('Failed to check broadcast messages'));
            });
        }
    };

    /**
     * Display message to user as a modal.
     *
     * @param {object} message The message to display.
     */
    var displayMessageModal = function(message) {
        if (!modalObj.getRoot()[0].classList.contains('show')) {
            modalObj.setTitle(message.title);
            modalObj.setBody(message.body);
            modalObj.footer[0].dataset.id = message.id;
            modalObj.show();

            // Remove the message from the queue.
            delete messageQueue[message];
        }
    };

    /**
     * Display message to user as a boostrap notification.
     *
     * @param {object} message The message to display.
     */
    var displayMessageNotification = function(message) {
        var containerid = 'tool-broadcast-notification-' + message.id;
        var existingContainer = document.getElementById(containerid);

        if (existingContainer === null) {
            var container = document.createElement('span');
            var header = document.createElement('h4');
            var body = document.createElement('span');

            container.id = containerid;
            header.classList.add('alert-heading');
            header.innerHTML = message.title;
            body.innerHTML = message.body;

            container.appendChild(header);
            container.appendChild(body);

            Notification.addNotification({
                message: container.outerHTML,
                type: 'warn'
            });

            // Remove the message from the queue.
            delete messageQueue[message];
        }

    };

    /**
     * Display the message to the user.
     */
    var displayMessages = function() {
        if (document.hasFocus()) {
            // If modal window is not currently displayed, check for queue messages.
            for (var message in messageQueue) {
                if (messageQueue[message].mode == 1) { // Display the message in a modal.
                    displayMessageModal(messageQueue[message]);
                } else if (messageQueue[message].mode == 2) { // Display the message as notification.
                    displayMessageNotification(messageQueue[message]);
                } else if (messageQueue[message].mode == 3) { // Display the message both ways.
                    displayMessageModal(messageQueue[message]);
                    displayMessageNotification(messageQueue[message]);
                }

                // Exit the loop after showing one message.
                break;
            }
        }
    };

    /**
     * Process user acknowledging the message.
     */
    var acceptMessageModal = function() {
        modalObj.setBody(spinner);
        var broadcastid = modalObj.footer[0].dataset.id;
        delete messageQueue[broadcastid];

        Ajax.call([{
            methodname: 'tool_broadcast_acknowledge_broadcast',
            args: {contextid: contextid, broadcastid: broadcastid}
        }])[0].fail(function() {
            Notification.exception(new Error('Failed to acknowledge broadcast messages'));
        });
    };

    /**
     * Process user acknowledging the message.
     */
    var acceptMessageNotification = function(event) {
        var elementName = event.target.tagName.toLowerCase();
        // Check correct thing was clicked.
        if (elementName === 'button' && event.target.parentElement.parentElement.id == 'user-notifications') {
            // Get the ID of the notification
            var notificationChildren = event.target.parentElement.childNodes;
            for (var i = 0; i < notificationChildren.length; i++) {
                if ((notificationChildren[i].tagName !== undefined) && (notificationChildren[i].tagName.toLowerCase() === 'span')) {
                    var broadcastid = notificationChildren[i].id.substring(28);
                    delete messageQueue[broadcastid];
                    Ajax.call([{
                        methodname: 'tool_broadcast_acknowledge_broadcast',
                        args: {contextid: contextid, broadcastid: broadcastid}
                    }])[0].fail(function() {
                        Notification.exception(new Error('Failed to acknowledge broadcast messages'));
                    });
                }
            }
        }
    };

    /**
     * Create the modal window.
     *
     * @private
     */
    var createModal = function() {
        return new Promise(function(resolve, reject) {
            Str.get_string('loading', 'tool_broadcast').then(function(title) {
                // Create the Modal.
                var footerBtn = document.createElement('input');
                footerBtn.type = 'button';
                footerBtn.value = 'Acknowledge';
                footerBtn.id = 'tool-broadcast-accept-broadcast';
                footerBtn.classList.add('btn', 'btn-primary', 'd-flex', 'ml-auto', 'mr-auto');

                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: title,
                    body: spinner,
                    footer: footerBtn.outerHTML, // Mooodle 3.5 compatibility fix.
                    large: true
                })
                .done(function(modal) {
                    modalObj = modal;
                    modalObj.getRoot().on('click', '#tool-broadcast-accept-broadcast', function(e) {
                        e.preventDefault();
                        modalObj.hide();
                    });
                    modalObj.getRoot().on(ModalEvents.hidden, acceptMessageModal);
                    resolve();

                });
            }).catch(function() {
                reject(new Error('Failed to load string: loading'));
            });
        });
    };

    /**
     * Initiliase the modal display and associated listeners.
     */
    BroadcastModal.init = function(context) {
        contextid = context;

        // We don't want every user making ajax requests at the same time.
        // So we add some randomness to the check interval at creation time.
        var min = 45000;
        var max = 60000;
        var interval = Math.floor(Math.random() * (max - min + 1)) + min;

        // TODO: add chain for create modal then to check messages.
        createModal()
        .then(function() {
            checkMessages(); // Do an initial broadcast message check once things are loaded.
            setInterval(checkMessages, interval); // Check messages at a regular interval.
            setInterval(displayMessages, 5000); // Display messages at a regular interval, can be more frequent.
        })
        .catch(function() {
            Notification.exception(new Error('Failed to create broadcast modal'));
        });

        // Add event listener that will handle bootstrap click.
        document.addEventListener('click', acceptMessageNotification);

    };

    return BroadcastModal;
});

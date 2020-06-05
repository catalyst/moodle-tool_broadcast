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
    const spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';
    var messageQueue = {};

    const checkMessages = () => {
        Ajax.call([{
            methodname: 'tool_broadcast_get_broadcasts',
            args: {contextid: contextid}
        }])[0].done((response) => {
            let messages = JSON.parse(response);
            for (const message in messages) {
                let messageId = messages[message].id;
                messageQueue[messageId] = messages[message];
            }
        }).fail(() => {
            Notification.exception(new Error('Failed to get broadcast messages'));
        });

    };

    const displayMessages = () => {
         //Check the queue for messages.

        // If modal window is not currently displayed, check for queue messages.
        if (!modalObj.getRoot()[0].classList.contains('show')) {
            for (const message in messageQueue) {
                // Display the message in the modal.
                modalObj.setTitle(messageQueue[message].title);
                modalObj.setBody(messageQueue[message].body);
                modalObj.show();

                // remove the message from the queue
                delete messageQueue[message];

                // Exit the loop after showing one message.
                break;
            }
        }
    };

    const acceptMessage = () => {
        modalObj.setBody(spinner);
        window.console.log('message accepted');
    };

    /**
     * Create the modal window.
     *
     * @private
     */
    const createModal = () => {
        return new Promise((resolve, reject) => {
            Str.get_string('loading', 'tool_broadcast').then((title) => {
                // Create the Modal.
                let footerBtn = document.createElement('input');
                footerBtn.type = 'button';
                footerBtn.value = 'Acknowledge';
                footerBtn.id = 'tool-broadcast-accept-broadcast';

                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: title,
                    body: spinner,
                    footer: footerBtn,
                    large: true
                })
                .done((modal) => {
                    modalObj = modal;
                    modalObj.getRoot().on('click', '#tool-broadcast-accept-broadcast', (e) => {
                        e.preventDefault();
                        modalObj.hide();
                    });
                    modalObj.getRoot().on(ModalEvents.hidden, acceptMessage);
                    resolve();

                });
            }).catch(() => {
                reject(new Error('Failed to load string: loading'));
            });
        });
    };

    BroadcastModal.init = function(context) {
        contextid = context;

        // We don't want every user making ajax requests at the same time.
        // So we add some randomness to the check interval at creation time.
        let min = 10000;
        let max = 20000;
        let interval = Math.floor(Math.random() * (max - min + 1)) + min;

        // TODO: add chain for create modal then to check messages.
        createModal()
        .then(() => {
            checkMessages(); // Do an initial broadcast message check once things are loaded.
            setInterval(checkMessages, interval); // Check messages at a regular interval.
            setInterval(displayMessages, 5000); // Display messages at a regular interval, can be more frequent.
        })
        .catch(() => {
            Notification.exception(new Error('Failed to create broadcast modal'));
        });
    };

    return BroadcastModal;
});

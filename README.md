![Build Status](https://github.com/catalyst/moodle-tool_broadcast/actions/workflows/moodle-ci.yml/badge.svg?branch=master)

# Broadcast message #

This plugin allows sending of broadcast messages to users.

## Supported Moodle Versions

This plugin currently supports and is tested against the following versions of Moodle:

* 3.9
* 3.10
* 3.11

## Installation

You can install this plugin from the plugin directory or get the latest version
on GitHub.

```bash
git clone https://github.com/catalyst/moodle-tool_broadcast admin/tool/broadcast
```

# Using the plugin

The following sections outline how to use the plugin.

## Creating a broadcast

The following outlines how to create a broadcast message for students.

1. Once the plugin is installed log into Moodle as an administrator.
2. Navigate to: *Site administration > Broadcast Message > Manage broadcasts*.
3. Click on the *Add new broadcast* button. You will then be presented with a modal window form to create the broadcast.

![create modal](/pix/create_modal.png?raw=true)

4. Fill out the *broadcast title* and the *broadcast message* which will be displayed to users.
5. Choose the scope of where you want the message to appear, either: *Site*, *Category* or *Course*. These are the contexts that you want the message to appear in. The message will display in that context and **all contexts below**. For example, a message defined at category level will appear in all courses in that category; a message defined at course level will appear in all activities in that course and a message at site level will appear everywhere.
6. Choose the *Active from* and *Expiry* dates and times for the broadcast. The message will only display to users between those times.
7. Select if you want the message to only apply to logged in users. When enabled only users who are logged in at the time of the message becoming active will see the message.
8. Click the *Create broadcast* button. The broadcast details will show in the summary table on the manage broadcasts screen.

![broadcast table](/pix/broadcast_table.png?raw=true)

The broadcast summary table provides details on the broadcasts for the system. It also includes the ability to edit, copy, and delete the broadcast. A report showing which users have acknowledged the broadcast can also be accessed for each broadcast from this table.

## Editing a broadcast

To edit a broadcast message:

1. Navigate to: *Site administration > Broadcast Message > Manage broadcasts*.
2. From the broadcast summary table click the *edit* (cog) icon on the table row that corresponds to the broadcast you want to edit.

![actions](/pix/actions.png?raw=true)

3. The edit broadcast modal window form is displayed.
4. Change any or all of the details of the broadcast.
5. Click the *Update broadcast* button.
6. The broadcast summary table will show the updated broadcast details.

## Copying a broadcast

To copy a broadcast message:

1. Navigate to: *Site administration > Broadcast Message > Manage broadcasts*.
2. From the broadcast summary table click the *copy* (two pages) icon on the table row that corresponds to the broadcast you want to copy.

![actions](/pix/actions.png?raw=true)

3. The copy broadcast modal window form is displayed.
4. Change any or all of the details of the broadcast.
5. Click the *Create broadcast* button.
6. The broadcast summary table will show the new copied broadcast details.

## Viewing the Broadcast acknowledgement report

When a user acknowledges a broadcast message a record is kept of where the user saw the message, the user that acknowledged the message and the time that it was acknowledged. These details can be viewed for every broadcast in the *Broadcast acknowledgement* report.

![acknowledgement report](/pix/ack_report.png?raw=true)

There are two ways to view the Broadcast acknowledgement report. The first way is from the broadcast summary table. The second is via the site administration menu.

To view the Broadcast acknowledgement report from the broadcast summary table:

1. Navigate to: *Site administration > Broadcast Message > Manage broadcasts*.
2. From the broadcast summary table click the *report* icon on the table row that corresponds to the broadcast you want to view the report for.

![actions](/pix/actions.png?raw=true)

3. The Broadcast acknowledgement report will be displayed.

To view the Broadcast acknowledgement report directly:

1. Navigate to: *Site administration > Reports > Broadcast acknowledgement*.
2. The Broadcast acknowledgement report will be displayed.

# User Broadcast display

Broadcast messages are displayed to users as a modal window. With an acknowledgement button.

![actions](/pix/broadcast_modal.png?raw=true)

It doesn't matter how a user closes a broadcast modal (clicking escape etc.). All actions that close the modal count as an acknowledgement. All user acknowledgements are recorded and displayed in the report.

# Crafted by Catalyst IT

This plugin was developed by Catalyst IT Australia:

https://www.catalyst-au.net/

![Catalyst IT](/pix/catalyst-logo.png?raw=true)

# Contributing and Support

Issues, and pull requests using github are welcome and encouraged! 

https://github.com/catalyst/moodle-tool_broadcast/issues

If you would like commercial support or would like to sponsor additional improvements
to this plugin please contact us:

https://www.catalyst-au.net/contact-us
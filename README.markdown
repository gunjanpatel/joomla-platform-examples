Joomla Platform Examples
========================

These examples are provided to help you learn how to structure and build applications with the Joomla Platform from the cli side.

Installation
============

Clone or download the https://github.com/joomla/joomla-platform and
https://github.com/joomla/joomla-platform-examples under the same parent folder.


For example, if your parent folder is called ``platform-test``, you would have the following
folders under the ``platform-test`` folder: ``cli, joomla-platform``, and ``web``.
You would also have a file called ``bootstrap.php`` in that folder.

Running Examples
================

All the examples in the ``cli`` folder are run from the command line or terminal.
All you need is PHP configured to run from the command line.



Command Line Applications
=========================

The examples found in the ``cli`` folder are all based on the new ``JApplicationCli`` class.
The is a base level class purpose built for running applications from the command line.

Download and Unzip Joomla Package
---------------

This is a simple example application that download and unzip a Joomla Package .

Install Joomla Package
----

This application install a Joomla Package.

Install Joomla extensions
---------

This application install joomla extensions. It provides an example of how to use the ``JHttp`` class.

Overload cli
------------

This application show you how you could use JCli to build a cron manager for Joomla CMS plugins.
The plugins would be configured via parameters in the CMS itself, but run via this command line
application. It makes use of JLog for logging activity in rolling daily log files. The
application would simply be added to any available scheduling software and run at appropriate
intervals.

While this example shows how to run all the plugins at the same time, it would not be difficult
to add an additional database table to support staggered running of individual plugins.

database
--------

This application shows how to override the constructor and connect to the database.

show-config
-----------

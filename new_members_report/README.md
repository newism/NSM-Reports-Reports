NSM Reports: New Members
========================

This addon requires [NSM Reports](http://ee-garage.com/nsm-reports) to function.

Overview
--------

The New Members report returns details for the new members that have signed up to the website inside of a specified date range. Member details output can be altered to return the data including basic member details such as screen name, email address, join date, entries counts, etc and custom member fields.

This data can be grouped by hour/day/week/month to generate graphs powered by the Google Charts API.

Graph 1 is a line graph showing the increase in total memberships over the timeframe on one axis and the number of signups that occurred at each interval on another axis. This overlay helps visualise the increase in memberships against the total number of members in the website.

Graph 2 is a pie chart showing the breakdown of member groups that the new members belong to. This is a fast way to see what percentage of members have purchased something if using Simple Commerce or a similar E-Commerce module that moves purchasing members into their own group.

Installation
------------

* Copy the new_members_report directory (this directory) into your NSM Reports report path. By default this will be the system/expressionengine/third_party/nsm_reports/reports directory unless it has been set in the NSM Reports [extension settings](http://ee-garage.com/nsm-reports/user-guide#toc-configuration:extension_settings:general_settings) or the site's config_bootstrap.php
* The report will now appear in the NSM Reports [dashboard](http://ee-garage.com/nsm-reports#toc-take_a_peek_:multiple_reports)
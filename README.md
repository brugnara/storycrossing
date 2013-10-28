StoryCrossing
=============

Info about old-version branch
=============================
This branch is the old site, currently online. I'll drop any pushing request on this branch, only security patch will be merged.
Our goal should be improve master letting dead this one.

Goal
====
The main goal of this open source project, is to obtain a very nice collaborative writing web site.
The version 0.3 is the original, HTML4 version. No AJAX at all. I want to build a frontend with AngularJS + Bootstrap that uses REST SC already has.

How deploy
==========
- Write your mysql configuration in /application/configs/application.ini.
- For using facebook login, you have to create a facebook application and add api keys in application.ini
- Mail: application/api/Mailer.php contains all data for sending email from site (notifications..). Write your own email data for testing this.

Mobile
======

I think that mobile is the key for victory. 

*Android*
There's also an Android apk that uses web apis.

*IOS*
Not implemented yet.


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<style>

h4 {margin-bottom:2px;}

</style>

<?php  



/**

* 

* This file is fetched by the server admin 

*/



$test_version=!empty($_GET['version'])?$_GET['version']:0;



$version = '2006031601';

$release = '2.2B1';



$downloadURL='https://sourceforge.net/project/showfiles.php?group_id=69681&package_id=68702';



$full_list=$test_version?'<p><a href="'.$_SERVER['SCRIPT_NAME'].'">See full changelog</a></p>':'';



$download_link="<br /><a target='_top' href='$downloadURL'>Interact Download page</a>";



if($test_version>=$version) {

echo "<body>Your Interact version is up to date (Release $release or higher).".$download_link.$full_list;exit;

}



echo "<body style='border:1px solid #F00;padding:5px;'>Current Interact Release: $release - $version.".$download_link;



echo'<h3>'.($test_version?'Changelog since your version':'Full changelog').'</h3>';



if ($test_version<2006031401) {

echo

<<<HTML

<ul><h4>Release 2.2:</h4>

<li>New Language translation system including web-interface editor</li>

<li>Single codebase for multiple installations</li>

<li>Improved short urls for links and IIS support</li>

<li>Server Admin version-check</li>

<li>Minor display changes, such as:<ul>

	<li>Display of invisible members on admin-view of Members pages simplified</li>

	<li>'Remove me' link moved and warning added to prevent admins accidentally removing themselves (and forfeiting their admin rights)</li>

	<li>Hide/Show Admin tools toggle button</li>

</ul></li>

<li>'Sites' are now called 'spaces' throughout Interact.  This was done to avoid confusion with websites or domains.</li>

<li>Bug Fixes and 2.1.x patches</li></ul>

HTML;

}



if ($test_version<2006011201) {

echo

<<<HTML

<ul><h4>Release 2.1:</h4>

<li>New Scorm/IMS import component</li>

<li>Significant improvements to calendar</li>

<li>Indication of current online users</li>

<li>Simple messaging system for messaging other online users</li>

<li>Revamped Journal component with more 'blog-like' look and functionality</li>

<li>Improvements to 'link' dialog in html editor - allows easy linking to uploaded files and other components</li>

<li>Sites renamed to Spaces in string files</li>

<li>Personal Space option - creates a personal space for all users on the server</li>

<li>Portfolio option - allows users to add portfolio spaces within their personal space</li>

<li>Collapsable headings in the left navigation</li>

</ul>

HTML;

}



if ($test_version<2005030401) {

echo

<<<HTML

<ul><h4>Release 2.0</h4><li>Much simplified installation and upgrade procedures</li>

<li>Most config settings moved into database and local/ folder so they don't get overwritten with upgrades</li>

<li>All file access now happens through a php wrapper file for added security. You can move all of your users files out of the web server root, or keep them in the webserver root but block browser access with .htaccess files

</li><li>A greatly improved html editor

</li><li>Sites converted to components so they can be added anywhere just like any other component</li>

<li>Subsites block on site homepages can now take any component</li>

<li>CSS skins interface. Skins can be easily modified by server admins, site admins or users with no need to manually modify html templates</li></ul>

HTML;

}



if(!$test_version) {

echo

<<<HTML

<ul><h4>Release 1.9.1</h4>

<li>Incorporates all patches for 1.9</li>

<li>New Flash interactives module for inserting flash components via the wysiwyg editor</li>

<li>New 'navigation mode' added to folders which allows folders to display content via a top horizontal navigation bar, and bottom 'previous|next' links, instead of in the standard linear mode.</li>

<li>Accesslevels changed to Superadmin, Admin, User, Guest, Permanent Guest, to avoid confusion with Usergroups.</li>

<li>Incorporates new Server Admin and Site Admin manuals</li></ul>



<ul><h4>Release 1.9</h4>

<li>New KnowledgeBase component<ul>

<li>Categories replaced by Sites - Subsites</li>

<li>Embedded mp3 streaming option added to html editor</li>

<li>Changes to breadcrumb navigation</li>

<li>Latest item and new posts display at site level as well as homepage</li>

<li>Ability to set default site in config file to bypass homepage</li>

<li>Admin text replaced by icons in numerous places</li></ul></li>



<li>Enhancements to Forum posting options</li>

</ul>



<ul><h4>Release 1.8.7</h4>

<li>New site status of public added so a site can be accessed without login even if rest of server is restricted</li>

<li>Improved installation and upgrade functions. Database installation and upgrade now handled from web interface</li>

<li>Update all option now added to grade book pages for marking by item and by user. The spreadsheet option is now for viewing only as there was too much system overhead in generating this as a modify view</li>

<li>New cross browser compatible editor now available in most text input areas. User can specify if editor automatically loaded</li>

<li>New config option to turn off display of email addresses in members lists</li>

<li>Quiz module added which enables setting of multichoice, true/false and multianswer test questions. Includes automatic marking and display of feedback on completion</li>

<li>User photos now appear on posts when viewed via new posts page</li>

<li>Any module edit rights assigned to users are now copied across to new module when a module is copied</li>

<li>Chat module now has option to save text of chat session</li>

<li>Chat module changed from 'arc chat' to 'phpmychat' to fix some perfomance and stability issues</li>

<li>After modifying a component you are now returned to the component rather than to its parent in most cases</li>

<li>Select all option now available on forum and thread pages</li>

<li>Search option added to forum page</li>

<li>Forum page no only shows 15 threads at a time so older posts automatically archived</li>

<li>Threads now automatically collapse after five days of no new posts</li>

<li>Attachments can now be added to forum postings</li>

<li>Calendar now has an option to specify an 'open' calendar to which all site members can add events</li>

<li>Added sort order field to spaces and categories to allow sorting of category tree by number rather than by name/code</li>

<li>Added 'external link' option to allow the adding of links to external websites into the category tree</li>

<li>Space and group member lists now display the number of members at the top of the list</li>

</ul>



<ul><h4>Release 1.8.6</h4>

<li>New gradebook module - adds gradebook to a site and

also allows students to view all assignments/grades

from homepage. Sytem wide scales can be added

from admin pages, or custom scales can be added by users

to each gradebook</li>



<li>New usernotes function which enables users to add

postit type notes to any page within a site</li>



<li>Bulk upload function in admin section allows bulk account

creation via a tab delimited file. This function can also be devolved to

allow individual site admins to bulk upload accounts. There is also a

sample dbupload.php file in /admin/users which can be modfied to upload

account details from an external database.</li>



<li>User delete function now available in 'User lookup'

Config file setting also specifies if users can delete own

account. New config setting also specifies the number

of days to keep stale accounts.</li>



<li>Short and long date formats can now be set for overall

server and at individual site level</li>



<li>Time can now be added to a calendar event</li>



<li>Turkish language version now available - although latest modules still

need translating.</li>

</ul>





<ul><h4>Release 1.8.5</h4>



<li>A variable database table prefix is now used so Interact can co-habit in

a single database, or you can run more than one copy of Interact in a

single database.



upgrade script adds prefix to all tables</li>



<li>

Automated functions can be set to run with each page load if cron

unavailable on hosted servers,etc.



Automated functions now integrated into one file.</li>



<li>

Chat now requires only one table for chat entries rather than separate

table for each chat room - so need for separate chat database removed

</li>



<li>

Removed need to have register globals set to on.

</li>



<li>

Multilingual setup finished for user interface.

</li>



<li>

Noticeboard module added to enable news/notice type functions within

groups.

</li>



<li>

User ID number field added to User table so institutional ID

can be recorded and users can be added and removed

from sites using this id

</li>



<li>

New look ready for themes functions in next release.

</li>

</ul>



<ul><h4>Release 1.8.4</h4>

<li>

Fixed bug in Journal component.

</li>



<li>

Added email functionality for windows platform

</li>



<li>

Added database server variable to config file

</li></ul>



<ul><h4>Release 1.8.3</h4>

<li>

Fixes some minor bugs:

<ul><li>

Viewing usergroups template error fixed

</li>



<li>Viewing site memberships error fixed

</li>



<li>Viewing full user details from members list fixed

</li>



<li>Incorrect wording on admin homepage fixed

</li></ul></li>



<li>All functions, except email now working on Windows platform.

</li></ul>



<ul><h4>Release 1.8.2</h4>

<li>

Fixes a problem with adding new users from the admin interface. The

previous releases contained some site specific scripts for creating new

accounts from the admin interface. These have been removed and replaced

with a generic account creation script.

</li>



<li>

Added some more context sensitive help when adding a new component.

</li>



<li>

Includes patch to allow new account creation with php4.1

</li>

</ul>



<ul><h4>Release 1.8.1</h4>

<li>

Mainly a tidy up/fix release.

</li>



<li>

Fixes bug with adding links in sharing area in 1.8 release

</li>



<li>

Tidy up of admin interface with some context sensitive help

added (this is the start of the context sensitive help that will

slowly be added throughout the system)

</li>



<li>

Fixed up User Group functions and Default-User-Site links.

Users now select a User group when adding/modifying

account. The user is then made a member of any default sites

for this user group. This is useful if you have some default

sites that you want all users in a particular group to access.

</li>

</ul>





<ul><h4>Release 1.8</h4>

<li>

Added reflective journal module.

</li>



<li>

Added ability to upload zip file to File componet and have it unzipped

on the server.

</li>



<li>

Added ability to add and manage additional files to file component.

</li>



<li>

Add same functions as above two to file option in peer review areas.

</li>

</ul>





<ul><h4>Release 1.7.3</h4>

<li>

Fixed problems with some modules when running in subdirectory rather

than webserver root.

</li></ul>



<ul><h4>Release 1.7.2</h4>

<li>

Fixed some minor problems with database creation sql

</li>



<li>

Added user subdirectories for two default users so image upload will

work with default accounts

</li>



<li>

Removed references to College of Ed in default headers and footers

</li>

</ul>



<ul><h4>Release 1.7.1 &mdash; First public release</h4></ul>

HTML;

}



echo $full_list;

?>



</body></html>
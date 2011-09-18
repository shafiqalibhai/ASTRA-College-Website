<?php
// +------------------------------------------------------------------------+
// | This file is part of Interact.											|
// |																	  	| 
// | This program is free software; you can redistribute it and/or modify 	|
// | it under the terms of the GNU General Public License as published by 	|
// | the Free Software Foundation (version 2)							 	|
// |																	  	|	 
// | This program is distributed in the hope that it will be useful, but  	|
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   		|
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 	|
// | General Public License for more details.							 	|
// |																	  	|	 
// | You should have received a copy of the GNU General Public License		|
// | along with this program; if not, you can view it at				  	|
// | http://www.opensource.org/licenses/gpl-license.php				   		|
// +------------------------------------------------------------------------+

/**
* Remove all news items
*
*
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key = $_GET['space_key'];
	
if (!isset($space_key) || $space_key=='') {

	$message = urlencode("There was a problem removing all news items");
	header("Location: {$CONFIG['FULL_URL']}/spaceadmin/admin.php?message=$message");
	exit;
	
}
//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');

$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}news WHERE space_key='$space_key'";
$CONN->Execute($sql);
$message = urlencode("Your news items have been deleted");
header("Location: {$CONFIG['FULL_URL']}/spaceadmin/admin.php?space_key=$space_key&message=$message");
exit;	
?>
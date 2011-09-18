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
* Saves a text file of usernames of all members of a space
*
* 
* 
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: membersave.php,v 1.5 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key	= $_GET['space_key'];

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 

if($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
	$rs = $CONN->Execute("SELECT username FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' ORDER BY last_name");
} else {
	$rs = $CONN->Execute("SELECT username FROM {$CONFIG['DB_PREFIX']}users ORDER BY last_name");
}
if ($rs->EOF) {

	$message = urlencode('There are no members to save');
	header("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key");
	
} else {

		header("Content-disposition: inline; filename=members.txt");
		header("Content-type: text/plain");
		echo "Save this page as a .txt file, or copy and paste the usernames below into a text file.\n\n Close this window to return to the members page.\n\n";
		while (!$rs->EOF) {
			echo $rs->fields[0]."\n";
			$rs->MoveNext();
		}
		$rs->Close();
		exit;
}

?>

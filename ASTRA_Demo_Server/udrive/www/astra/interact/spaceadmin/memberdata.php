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
* Member data
*
* Displays data about membership - date added, etc. 
* by module and by user 
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: memberdata.php,v 1.3 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');


$space_key  = $_GET['space_key'];
$module_key = $_GET['module_key'];

//check we have the required variables
check_variables(true,false);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels["accesslevel_key"];
authenticate_admins($level="space_only");

if (!class_exists('InteractDate')) {

	require_once('../includes/lib/date.inc.php');

}
$dates = new InteractDate();

header("Content-Type: application/vnd.ms-excel");



echo "name	Date Account Added	Member Since\n";



$sql = "SELECT first_name, last_name, {$CONFIG['DB_PREFIX']}users.date_added, {$CONFIG['DB_PREFIX']}space_user_links.date_added FROM {$CONFIG['DB_PREFIX']}space_user_links,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key  AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' ORDER BY {$CONFIG['DB_PREFIX']}space_user_links.date_added DESC";
	//echo $sql;
	$rs=$CONN->Execute($sql);
	echo $CONN->ErrorMsg();
	if ($rs->EOF) {
		?>
		There are no members yet for this space
		
		<?php

	} else {
		while (!$rs->EOF) {
			$username = $rs->fields[0].' '.$rs->fields[1];
			$account_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'short', true);
			$became_member = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[3]),'short', true);
			echo "$username	$account_added	$became_member\n";
			
			$rs->MoveNext();
			 
			}

			
   }
		
		
  
?>
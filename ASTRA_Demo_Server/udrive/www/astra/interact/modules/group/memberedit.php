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
* Edit members
*
* Edit the members of a group
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: memberedit.php,v 1.9 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


if ($_SERVER['REQUEST_METHOD']=='GET') {

   	$group_key	= $_GET['group_key'];
	$user_key	= $_GET['user_key'];
	$action		= $_GET['action'];

} else {

	$group_key	= $_POST['group_key'];
	$user_key	= $_POST['user_key'];
	$user_list	= $_POST['user_list'];
	$file	   = $_FILES['file'];
	$action	 = $_POST['action'];

}
$module_key = $group_key;
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

if (!class_exists(InteractGroup)) {
			
	require_once('lib.inc.php');
				
}
			
if (!is_object($groupObject)) {
			
	$groupObject = new InteractGroup();
			
}
$group_data = $groupObject->getGroupData($group_key);

switch($action) {
	
	case add_single:
	
		//if this is not a self registering group then do not allow the user to join
		if ($group_data['access_key']!=2 && $group_accesslevel!=1 && $_SESSION['userlevel_key']!=1 && $accesslevel_key!=1) {
		
			$message = urlencode($general_strings['not_allowed']);
			header ("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
			exit;
		
		} else {
		
			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links(group_key, user_key, access_level_key, date_added) VALUES('$group_key','{$_SESSION['current_user_key']}','2', $date_added)");
		
			header ("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
			exit;
			
		}
	
	break;
	case remove_single:
	

		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE user_key='{$_SESSION['current_user_key']}' AND group_key='$group_key'");
		
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
		exit;
	
	break;
	case delete:
	
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key='$group_key' AND user_key='$user_key'";
		$CONN->Execute($sql);
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
		exit;
	
	break;
	
	case add:

		$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
		$num_selected = count($user_key);
		if ($num_selected) {

			for ($c=0; $c < $num_selected; $c++) {
		
				$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links(group_key, user_key, access_level_key, date_added) VALUES('$group_key','$user_key[$c]','2',$date_added)";
				$CONN->Execute($sql);
		
			}
		
		}
	
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
		exit;
	
	break;

	case demote:
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}group_user_links SET access_level_key='2' WHERE user_key='$user_key' AND group_key='$group_key'";
		$CONN->Execute($sql);

		header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
	
		exit;
	
	break;

	case promote:
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}group_user_links SET access_level_key='1' WHERE  user_key='$user_key' AND group_key='$group_key'";
		$CONN->Execute($sql);
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
		exit;
	
	break;

	case add_by_username:
	
		$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
		if (isset($user_list)) {
		
			$usernames_array=explode(',',$user_list);
			$count=count($usernames_array);
			
			for ($i=0; $i<$count; $i++) {
			   
			   $username=trim($usernames_array[$i]);
			   
			   $check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='$username'";
			   
			   $check_user_rs = $CONN->Execute($check_user_sql);
			   
			   if (!$check_user_rs->EOF) {
			   
					while (!$check_user_rs->EOF) {
				   
						$user_key = $check_user_rs->fields[0];
						$check_user_rs->MoveNext();
					   
					}
			   
					$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links(group_key, user_key, access_level_key, date_added) VALUES('$group_key','$user_key','2',$date_added)";
					$CONN->Execute($sql);
			   
			   }
			
			}
		
		}
		
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key&link_key=$link_key");
		exit;
			
	break;
		
	case add_by_number:
	
	   $date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
	   if (isset($user_list)) {
		
			$user_ids_array=explode(',',$user_list);
			$count=count($user_ids_array);
			
			for ($i=0; $i<$count; $i++) {
			   
			   $user_id=trim($user_ids_array[$i]);
			   
			   $check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE user_id_number='$user_id'";
			   
			   $check_user_rs = $CONN->Execute($check_user_sql);
			   
			   if (!$check_user_rs->EOF) {
			   
					while (!$check_user_rs->EOF) {
				   
						$user_key = $check_user_rs->fields[0];
						$check_user_rs->MoveNext();
					   
					}
			   
					$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links(group_key, user_key, access_level_key, date_added) VALUES('$group_key','$user_key','2',$date_added)";
					$CONN->Execute($sql);
			   
			   }
			
			}
		
		}
		
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key&link_key=$link_key");
		exit;
		break;
		
		case upload_by_username:

			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			if ($file && $file!='none') {
		
				$fcontents = file ($file['tmp_name']);
			
				while (list ($line_num, $line) = each ($fcontents)) {
			   
					$username=trim($line);
			   
					$check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='$username'";
			   
					$check_user_rs = $CONN->Execute($check_user_sql);
			   
					if (!$check_user_rs->EOF) {
			   
						while (!$check_user_rs->EOF) {
				   
							$user_key = $check_user_rs->fields[0];
							$check_user_rs->MoveNext();
					   
						}
			   
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links (group_key, user_key, access_level_key, date_added) VALUES('$group_key','$user_key','2',$date_added)";
						$CONN->Execute($sql);
						
									  
					}
					
				}
			
			}
		
			header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&module_key=$module_key&link_key=$link_key");
			exit;
		
		break;
		
		case upload_by_number:

			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			if ($file && $file!='none') {
		
				$fcontents = file ($file['tmp_name']);
			
				while (list ($line_num, $line) = each ($fcontents)) {
			   
					$user_id=trim($line);
			   
					$check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE user_id_number='$user_id'";
			   
					$check_user_rs = $CONN->Execute($check_user_sql);
			   
					if (!$check_user_rs->EOF) {
			   
						while (!$check_user_rs->EOF) {
				   
							$user_key = $check_user_rs->fields[0];
							$check_user_rs->MoveNext();
					   
						}
			   
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links(group_key, user_key, access_level_key, date_added) VALUES ('$group_key','$user_key','2',$date_added)";
						$CONN->Execute($sql);
			   
				   }
			
				}
				
			}
			   
			header ("Location: {$CONFIG['FULL_URL']}/modules/group/members.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key&link_key=$link_key");
			exit;
		
		break;
	
}
?>
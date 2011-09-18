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
* Group module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* group module
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: group.inc.php,v 1.15 2007/01/29 01:34:37 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new group module
*
* @param  int $module_key  key of new group module
* @return true if details added successfully
*/

function add_group($module_key) {

	global $CONN, $CONFIG;
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET group_key='$module_key' WHERE module_key='$module_key'";
	$CONN->Execute($sql);
	
	if ($_POST['start_date_month']) { 
		$start_date = $_POST['start_date_year'].'-'.$_POST['start_date_month'].'-'.$_POST['start_date_day'].' '.$_POST['start_date_hour'].':'.$_POST['start_date_minute'];
		$start_date = $CONN->DBDate(date($start_date));
	} else {
		$start_date = "''";
	}
	if ($_POST['finish_date_month']) { 
		$finish_date = $_POST['finish_date_year'].'-'.$_POST['finish_date_month'].'-'.$_POST['finish_date_day'].' '.$_POST['finish_date_hour'].':'.$_POST['finish_date_minute'];
		$finish_date = $CONN->DBDate(date($finish_date));
	} else {
		$finish_date = "''";
	}

	$sort_order_key = isset($_POST['sort_order_key'])? $_POST['sort_order_key'] : 1;
	$access_key	 = isset($_POST['access_key'])? $_POST['access_key'] : '1';
	$access_code	= $_POST['access_code'];
	$maximum_users  = $_POST['maximum_users'];
	$minimum_users  = $_POST['minimum_users'];
	$group_management  = $_POST['group_management'];
	$visibility_key = isset($_POST['visibility_key'])? $_POST['visibility_key'] : 1;		
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_settings(module_key,sort_order_key, access_key, access_code, visibility_key, maximum_users, minimum_users, start_date, finish_date, group_management) VALUES ('$module_key','$sort_order_key','$access_key','$access_code', '$visibility_key', '$maximum_users', '$minimum_users', $start_date, $finish_date, '$group_management')";

	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your group: '.$CONN->ErrorMsg().' <br />';
		echo $message;
		return $message;
		
	} else {	  
	
		//If person adding group is not site admim then make them a 
		//a group leader
	
		//first check that they are not a superadmin
	
		$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='{$_SESSION["current_user_key"]}' AND level_key='1'";
		$is_superadmin_rs = $CONN->Execute($sql);
	
		if ($is_superadmin_rs->EOF) { 
	
			$sql = "SELECT {$CONFIG['DB_PREFIX']}space_user_links.user_key FROM {$CONFIG['DB_PREFIX']}space_user_links,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND (space_key='{$_POST["space_key"]}' AND {$CONFIG['DB_PREFIX']}space_user_links.user_key='{$_SESSION['current_user_key']}') AND (access_level_key='1' OR access_level_key='3')";
	
			$rs = $CONN->Execute($sql);

			if ($rs->EOF) {
	
				$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links(group_key,user_key,access_level_key) VALUES('$module_key','{$_SESSION['current_user_key']}','1')";
				$CONN->Execute($sql);
		
			}
	
			$rs->Close();
		
		}
	
		$is_superadmin_rs->Close();
	
		return true;
		
	}

}
/**
* Function called by Module class to get exisiting group data 
*
* @param  int $module_key  key of group module
* @return true if data retrieved
*/

function get_group_data($module_key) {

   global $CONN,$module_data, $CONFIG;
   $sql = "SELECT sort_order_key, access_key, access_code, visibility_key, maximum_users, minimum_users, start_date, finish_date, group_management FROM {$CONFIG['DB_PREFIX']}group_settings WHERE module_key='$module_key'";	
   $rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_data['sort_order_key'] = $rs->fields[0];
		$module_data['access_key'] = $rs->fields[1];		
		$module_data['access_code'] = $rs->fields[2];
		$module_data['visibility_key'] = $rs->fields[3];	
		$module_data['maximum_users'] = $rs->fields[4];
		$module_data['minimum_users'] = $rs->fields[5];
		$module_data['start_date_unix'] = $CONN->UnixTimestamp($rs->fields[6]);
		$module_data['finish_date_unix'] = $CONN->UnixTimestamp($rs->fields[7]);
		$module_data['group_management'] = $rs->fields[8];				
		
		$rs->MoveNext();
	
	}

}

/**
* Function called by Module class to modify exisiting group data 
*
* @param  int $module_key  key of group module
* @param  int $link_key  link key of group module being modified
* @return true if successful
*/

function modify_group($module_key,$link_key) {

	global $CONN,$status_key, $CONFIG;
	
	$status_key = $_POST['status_key'];
	// avoid setting invalid status!!
	if(empty($status_key)){$status_key=1;}

	change_group_child_status($link_key,$status_key);

	if ($_POST['start_date_month']) { 
		$start_date = $_POST['start_date_year'].'-'.$_POST['start_date_month'].'-'.$_POST['start_date_day'].' '.$_POST['start_date_hour'].':'.$_POST['start_date_minute'];
		$start_date = $CONN->DBDate(date($start_date));
	} else {
		$start_date = "''";
	}
	if ($_POST['finish_date_month']) { 
		$finish_date = $_POST['finish_date_year'].'-'.$_POST['finish_date_month'].'-'.$_POST['finish_date_day'].' '.$_POST['finish_date_hour'].':'.$_POST['finish_date_minute'];
		$finish_date = $CONN->DBDate(date($finish_date));
	} else {
		$finish_date = "''";
	}
	$minimum_users  = $_POST['minimum_users'];
	$group_management  = $_POST['group_management'];
	$sort_order_key = $_POST['sort_order_key'];	
	$access_key	 = $_POST['access_key'];
	$access_code	= $_POST['access_code'];
	$maximum_users  = $_POST['maximum_users'];	
	$visibility_key = $_POST['visibility_key'];			
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}group_settings SET sort_order_key='$sort_order_key', access_key='$access_key', access_code='$access_code', visibility_key='$visibility_key', maximum_users='$maximum_users', minimum_users='$minimum_users', start_date=$start_date, finish_date=$finish_date, group_management='$group_management' WHERE module_key='$module_key'";	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your group: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}	
} //end modify_group

/**
* Change child status   
* 
*  
* @return true
*/
function change_group_child_status($link_key,$status_key) 
{
	global $CONN, $CONFIG;
	$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND parent_key='$link_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";
	$rs=$CONN->Execute($sql);

	while (!$rs->EOF) {

		$child_link_key = $rs->fields[0];
		$type_code = $rs->fields[1];
	   $status_sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='$status_key' WHERE link_key='$child_link_key'";
		$CONN->Execute($status_sql);

		if ($type_code=='folder' || $type_code=='group') {

			change_group_child_status($child_link_key,$status_key);

		}

		$rs->MoveNext();

	}

	return true;

} //end change_group_child_status

/**
* Function called by Module class to delete exisiting group data 
*
* @param  int $module_key  key of group module
* @param  int $space_key  space key of group module
* @param  int $link_key  link key of group module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_group($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;	 
  
	$sql = "DELETE from {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key='$module_key'";
	$CONN->Execute($sql);
	 
	$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}group_settings WHERE module_key='$module_key'";

	if ($CONN->Execute($sql) === false) {   

			$message = 'There was an error deleting some group settings - '.$CONN->ErrorMsg();
			email_error($message);
			return $message;

	} else {

			return true;
			
	}
	

} //end delete_group	

/**
* Function called by Module class to flag a group for deletion 
*
* @param  int $module_key  key of group module
* @param  int $space_key  space key of group module
* @param  int $link_key  link key of dropbox group being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_group_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN,$modules, $CONFIG;
	
	if ($delete_action=='link_only' || $delete_action=='all') {

		$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  (parent_key='$link_key' AND space_key='$space_key')";

		$rs=$CONN->Execute($sql);

		if (!$rs->EOF) {
	
			while (!$rs->EOF) {

				$child_module_key = $rs->fields[0];
				$child_link_key = $rs->fields[1];			
				$child_module_code = $rs->fields[2];
				$modules->flag_module_for_deletion($child_module_key,$space_key,$child_link_key,$delete_action,$child_module_code,false,true);
				$rs->MoveNext();

			}
		
		return true;

		} else {
	 
			return true;
	
		}
		
	}
		
} //end flag_group_for_deletion

/**
* Function called by Module class to copy a group 
*
* @param  int $existing_module_key  key of group being copied
* @param  int $existing_link_key  link key of group module being copied
* @param  int $new_module_key  key of group being created
* @param  int $new_link_key  link key of group module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of group module
* @param  int $new_group_key  group key of new group
* @return true if successful
*/
function copy_group($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data, $space_key,$new_group_key) 
{
	global $CONN,$modules, $CONFIG;
	
	//insert values into GroupSettings table

	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}group_settings(module_key,sort_order_key, access_key, access_code, visibility_key,maximum_users) VALUES ('$new_module_key','{$module_data['sort_order_key']}','{$module_data['access_key']}','{$module_data['access_code']}','{$module_data['visibility_key']}','{$module_data['maximum_users']}')");
 
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET group_key='$new_module_key' WHERE link_key='$new_link_key'";
	$CONN->Execute($sql);

	$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  (parent_key='$existing_link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";

	$rs=$CONN->Execute($sql);

	if (!$rs->EOF) {
	
			while (!$rs->EOF) {

				$child_module_key = $rs->fields[0];
				$child_link_key = $rs->fields[1];			
				$child_module_code = $rs->fields[2];
				$modules->copy_module($child_module_key,$child_link_key,$space_key,$new_link_key,$new_module_key);
				$rs->MoveNext();

			}
		
	}

	return true;

	

} //end copy_group


/**
* Function called by Module class to add new group link
*
* @param  int $module_key  key of group module
* @return true if successful
*/

function add_group_link($module_key,$existing_link_key,$new_link_key,$module_data) 
{

	global $CONN, $modules, $CONFIG;

	$sql="SELECT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.sort_order,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.change_status_date,{$CONFIG['DB_PREFIX']}module_space_links.change_status_to_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key  AND ({$CONFIG['DB_PREFIX']}module_space_links.parent_key='$existing_link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'  AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";

	$rs=$CONN->Execute($sql);

	if (!$rs->EOF) {
	
		while (!$rs->EOF) {
	
			$child_module_key = $rs->fields[0];
			$child_link_key = $rs->fields[1];		
			$child_module_data['code']			   = $rs->fields[2];
			$child_module_data['sort_order']		 = $rs->fields[3];
			$child_module_data['target']			 = $rs->fields[4];
			$child_module_data['status_key']		 = $rs->fields[5];
			$child_module_data['change_status_date'] = $rs->fields[6];
			$child_module_data['change_status_to']   = $rs->fields[7];
			$child_module_data['space_key']		  = $module_data['space_key'];				
			$child_module_data['parent_key']		 = $new_link_key;
			$child_module_data['group_key']		  = $module_data['group_key'];				
			$modules->add_module_link($child_module_key,$child_link_key,$child_module_data);
			$rs->MoveNext();

		}
		
		$rs->Close();		
	}

	return true;

}	
/**
* Move group to another space   
* 
*  
* @return true
*/
function move_space_group($link_key,$space_key) 
{
	global $CONN, $CONFIG;
	$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.link_key, {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND parent_key='$link_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";
	
	$rs=$CONN->Execute($sql);

	while (!$rs->EOF) {

		$child_link_key = $rs->fields[0];
		$type_code = $rs->fields[1];
		$parent_space_sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET space_key='$space_key' WHERE link_key='$child_link_key'";
		
		$CONN->Execute($parent_space_sql);

		if ($type_code=='folder' || $type_code=='group') {

			move_space_group($child_link_key,$space_key);

		}

		$rs->MoveNext();

	}

	return true;

} //end move_space_group

?>
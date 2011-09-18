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
* Folder module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* folder module
*
* @package Folder
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: folder.inc.php,v 1.17 2007/01/05 01:05:37 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new folder module
*
* @param  int $module_key  key of new folder module
* @return true if details added successfully
*/
function add_folder($module_key) {

	global $CONN, $CONFIG;
	$sort_order_key   = isset($_POST['sort_order_key'])?$_POST['sort_order_key']:'3';
	$navigation_mode = isset($_POST['navigation_mode'])? $_POST['navigation_mode'] : '0';
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}folder_settings(module_key,sort_order_key,navigation_mode) VALUES ('$module_key','$sort_order_key', '$navigation_mode')";

	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your folder: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}
	

}

/**
* Function called by Module class to get exisiting folder data 
*
* @param  int $module_key  key of folder module
* @return true if data retrieved
*/
function get_folder_data($module_key) {

   global $CONN,$module_data, $CONFIG;
   $sql = "SELECT sort_order_key, navigation_mode FROM {$CONFIG['DB_PREFIX']}folder_settings WHERE module_key='$module_key'";	
   $rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_data['sort_order_key'] = $rs->fields[0];
		$module_data['navigation_mode'] = $rs->fields[1];
		
		$rs->MoveNext();
	
	}

	return true;

}

/**
* Function called by Module class to modify exisiting folder data 
*
* @param  int $module_key  key of folder module
* @param  int $link_key  link key of folder module being modified
* @return true if successful
*/

function modify_folder($module_key,$link_key) {

	global $CONN, $status_key, $CONFIG;
	
	$navigation_mode = isset($_POST['navigation_mode'])? $_POST['navigation_mode'] : '0';

	$status_key = $_POST['status_key'];
	// avoid setting invalid status!!
	if(empty($status_key)){$status_key=1;}

	change_folder_child_status($link_key,$status_key);


	
	$sort_order_key = $_POST['sort_order_key'];	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}folder_settings SET sort_order_key='$sort_order_key', navigation_mode='$navigation_mode' WHERE module_key='$module_key'";	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your folder: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}	
	

} //end modify_folder

/**
* Change child status   
* 
*  
* @return true
*/
function change_folder_child_status($link_key,$status_key) 
{
	global $CONN, $CONFIG;
	$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.link_key, {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND parent_key='$link_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";
	
	$rs=$CONN->Execute($sql);

	while (!$rs->EOF) {

		$child_link_key = $rs->fields[0];
		$type_code = $rs->fields[1];
		$status_sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='$status_key' WHERE link_key='$child_link_key'";
		
		$CONN->Execute($status_sql);

		if ($type_key=='folder' || $type_key=='group') {

			change_folder_child_status($child_link_key,$status_key);

		}

		$rs->MoveNext();

	}

	return true;

} //end change_child_status

/**
* Function called by Module class to delete exisiting folder data 
*
* @param  int $module_key  key of folder module
* @param  int $space_key  space key of folder module
* @param  int $link_key  link key of folder module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_folder($module_key,$space_key,$link_key,$delete_action) 
{
  
	global $CONN, $CONFIG;
	
	$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}folder_settings WHERE module_key='$module_key'";

	if ($CONN->Execute($sql) === false) {   

			$message = 'There was an error deleting some folder settings - '.$CONN->ErrorMsg();
			email_error($message);
			return $message;

	} else {

			return true;
			
	}

} //end delete_folder

/**
* Function called by Module class to flag a folder for deletion 
*
* @param  int $module_key  key of folder module
* @param  int $space_key  space key of folder module
* @param  int $link_key  link key of dropbox folder being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_folder_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN,$modules, $CONFIG;
 
	if ($delete_action=='link_only' || $delete_action=='all') {

		$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key  AND  (parent_key='$link_key' AND space_key='$space_key')";

		$rs=$CONN->Execute($sql);

		if (!$rs->EOF) {
	
			while (!$rs->EOF) {

				$child_module_key = $rs->fields[0];
				$child_link_key = $rs->fields[1];			
				$child_module_code = $rs->fields[2];
				$modules->flag_module_for_deletion($child_module_key,$space_key,$child_link_key,$delete_action,$child_module_code,false,true);
				$rs->MoveNext();

			}
		
		}

		return true;

	} else {
	 
	 return true;
	
	}

} //end flag_folder_for_deletion

/**
* Function called by Module class to copy a folder 
*
* @param  int $existing_module_key  key of folder being copied
* @param  int $existing_link_key  link key of folder module being copied
* @param  int $new_module_key  key of folder being created
* @param  int $new_link_key  link key of folder module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of folder module
* @param  int $new_group_key  group key of new folder
* @return true if successful
*/
function copy_folder($existing_module_key,$existing_link_key,$new_module_key,$new_link_key,$module_data, $space_key,$new_group_key) 
{
	global $CONN,$modules, $CONFIG;

	//insert values into FolderSettings table
		
	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}folder_settings(module_key,sort_order_key) VALUES ('$new_module_key','{$module_data['sort_order_key']}')");
 
	$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  (parent_key='$existing_link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";

	$rs=$CONN->Execute($sql);

	if (!$rs->EOF) {
	
			while (!$rs->EOF) {

				$child_module_key = $rs->fields[0];
				$child_link_key = $rs->fields[1];			
				$child_module_code = $rs->fields[2];
				$modules->copy_module($child_module_key,$child_link_key,$space_key,$new_link_key,$new_group_key);
				$rs->MoveNext();

			}
		
	}

	return true;

	

} //end copy_folder

/**
* Function called by Module class to add new folder link
*
* @param  int $module_key  key of folder module
* @return true if successful
*/

function add_folder_link($module_key,$existing_link_key,$new_link_key,$module_data) {

	global $CONN, $modules, $CONFIG;

	$sql="SELECT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.sort_order,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.change_status_date,{$CONFIG['DB_PREFIX']}module_space_links.change_status_to_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.parent_key='$existing_link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}modules.status_key!='4')";

	$rs=$CONN->Execute($sql);

	if (!$rs->EOF) {
	
		while (!$rs->EOF) {
	
			$child_module_key = $rs->fields[0];
			$child_link_key = $rs->fields[1];		
			$child_module_data['code'] = $rs->fields[2];
			$child_module_data['sort_order'] = $rs->fields[3];
			$child_module_data['target'] = $rs->fields[4];
			$child_module_data['status_key'] = $rs->fields[5];
			$child_module_data['change_status_date'] = $rs->fields[6];
			$child_module_data['change_status_to'] = $rs->fields[7];
			$child_module_data['space_key'] = $module_data['space_key'];				
			$child_module_data['parent_key'] = $new_link_key;
			$child_module_data['group_key'] = $module_data['group_key'];				
			$modules->add_module_link($child_module_key,$child_link_key,$child_module_data);
			$rs->MoveNext();

		}
		
	}

	return true;

}	

/**
* Move folder to another space   
* 
*  
* @return true
*/
function move_space_folder($link_key,$space_key) 
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

			move_space_folder($child_link_key,$space_key);

		}

		$rs->MoveNext();

	}

	return true;

} //end move_space_folder

?>
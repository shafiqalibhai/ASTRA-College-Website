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
* Noticeboard module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* noticeboard module
*
* @package Noticeboard
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: noticeboard.inc.php,v 1.11 2007/01/29 01:34:38 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new noticeboard module
*
* @param  int $module_key  key of new noticeboard module
* @return true if details added successfully
*/
function add_noticeboard($module_key) {

	global $CONN, $CONFIG;
	$type_key	 = $_POST['type_key'];
	$days_to_keep = $_POST['days_to_keep'];
		
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}noticeboard_settings(module_key,type_key, days_to_keep) VALUES ('$module_key','$type_key', '$days_to_keep')";

	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your noticeboard: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}
	

}

/**
* Function called by Module class to get exisiting noticeboard data 
*
* @param  int $module_key  key of noticeboard module
* @return true if data retrieved
*/
function get_noticeboard_data($module_key) {

   global $CONN,$module_data, $CONFIG;
   $sql = "SELECT type_key, days_to_keep FROM {$CONFIG['DB_PREFIX']}noticeboard_settings WHERE module_key='$module_key'";	
   $rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_data['type_key']	 = $rs->fields[0];
		$module_data['days_to_keep'] = $rs->fields[1];
		
		$rs->MoveNext();
	
	}

	return true;

}

/**
* Function called by Module class to modify exisiting noticeboard data 
*
* @param  int $module_key  key of noticeboard module
* @param  int $link_key  link key of noticeboard module being modified
* @return true if successful
*/

function modify_noticeboard($module_key,$link_key) {

	global $CONN,$status_key, $CONFIG;
	
	$type_key	 = $_POST['type_key'];
	$days_to_keep = $_POST['days_to_keep'];	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}noticeboard_settings SET type_key='$type_key', days_to_keep='$days_to_keep' WHERE module_key='$module_key'";	
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your noticeboard: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}	
	

} //end modify_noticeboard


/**
* Function called by Module class to delete exisiting noticeboard data 
*
* @param  int $module_key  key of noticeboard module
* @param  int $space_key  space key of noticeboard module
* @param  int $link_key  link key of noticeboard module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_noticeboard($module_key,$space_key,$link_key,$delete_action) 
{
  
	global $CONN, $CONFIG;
	
	$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}noticeboard_settings WHERE module_key='$module_key'";

	if ($CONN->Execute($sql) === false) {   

			$message = 'There was an error deleting some noticeboard settings - '.$CONN->ErrorMsg();
			email_error($message);
			return $message;

	} else {

			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}notices WHERE module_key='$module_key'";

			$rs=$CONN->Execute($sql);
			
			return true;
			
	}

} //end delete_noticeboard

/**
* Function called by Module class to flag a noticeboard for deletion 
*
* @param  int $module_key  key of noticeboard module
* @param  int $space_key  space key of noticeboard module
* @param  int $link_key  link key of dropbox noticeboard being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_noticeboard_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN,$modules;
 
	return true;

} //end flag_noticeboard_for_deletion

/**
* Function called by Module class to copy a noticeboard 
*
* @param  int $existing_module_key  key of noticeboard being copied
* @param  int $existing_link_key  link key of noticeboard module being copied
* @param  int $new_module_key  key of noticeboard being created
* @param  int $new_link_key  link key of noticeboard module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of noticeboard module
* @param  int $new_group_key  group key of new noticeboard
* @return true if successful
*/
function copy_noticeboard($existing_module_key,$existing_link_key,$new_module_key,$new_link_key,$module_data, $space_key,$new_group_key) 
{
	global $CONN,$modules, $CONFIG;

	//insert values into FolderSettings table
		
	$type_key = $CONN->qstr($module_data['type_key']);
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}noticeboard_settings(module_key,type_key) VALUES ('$new_module_key',$type_key)";

	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error copying your noticeboard: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}	

	return true;

	

} //end copy_noticeboard

/**
* Function called by Module class to add new noticeboard link
*
* @param  int $module_key  key of noticeboard module
* @return true if successful
*/

function add_noticeboard_link($module_key,$existing_link_key,$new_link_key,$module_data) {

	global $CONN, $modules;

	return true;

}	

/**
* Function called by auto.php to run any automated functions
*
* @return true if successful
*/

function autofunctions_noticeboard($last_cron) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("DELETE from {$CONFIG['DB_PREFIX']}notices WHERE remove_date < CURDATE() AND remove_date > '0000-00-00'");
		
	return true;

}

/**
* Function called by deleteUser to run any functions related to deleting
* a user from the system
*
* @param int $user_key key of user being deleted
* @param int $deleted_user key of deleteduser user account
* @return true if successful
*/

function user_delete_noticeboard($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}notices SET user_key='$deleted_user' WHERE user_key='$user_key'");
			
	return true;

}
?>
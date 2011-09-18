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
* Calendar module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* calendar module
*
* @package Calendar
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: calendar.inc.php,v 1.17 2007/01/29 01:34:35 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new calendar module
*
* @param  int $module_key  key of new calendar module
* @return true if details added successfully
*/

function add_calendar($module_key) {

	global $CONN, $CONFIG;
	$parent_calendar_key = $_POST['parent_calendar_key'];
	$type = isset($_POST['type'])?$_POST['type']:'';

	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}calendars(module_key,parent_calendar_key, type) values ('$module_key','$parent_calendar_key', '$type')";
	
	if ($CONN->Execute($sql) === false) {
		$message =  'There was an error adding your calendar: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {	  
		return true;  
	}
}

/**
* Function called by Module class to get exisiting Calendar data 
*
* @param  int $module_key  key of calendar module
* @return true if data retrieved
*/

function get_calendar_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	
	$sql = "SELECT parent_calendar_key, type FROM {$CONFIG['DB_PREFIX']}calendars WHERE module_key='$module_key'";	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		$module_data['parent_calendar_key'] = $rs->fields[0];
		$module_data['type'] = $rs->fields[1];
		$rs->MoveNext();
	}
	$rs->Close();	

	return true;

}

/**
* Function called by Module class to modify exisiting Calendar data 
*
* @param  int $module_key  key of calendar module
* @param  int $link_key  link key of calendar module being modified
* @return true if successful
*/

function modify_calendar($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	$parent_calendar_key = $_POST['parent_calendar_key'];
	$type = isset($_POST['type'])?$_POST['type']:'';
	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}calendars SET parent_calendar_key='$parent_calendar_key', type='$type' WHERE module_key='$module_key'";	

	if ($CONN->Execute($sql) === false) {
		$message =  'There was an error modifying your calendar: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {	  
		return true;  
	}

} //end modify_calendar

/**
* Function called by Module class to delete exisiting Calendar data 
*
* @param  int $module_key  key of calendar module
* @param  int $space_key  space key of calendar module
* @param  int $link_key  link key of calendar module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_calendar($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;

	if ($delete_action=='all' || $delete_action=='last') {
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}calendars WHERE module_key='$module_key'";
		$CONN->Execute($sql);
		$rows_affected = $CONN->Affected_Rows();
		if ($rows_affected < '1') {	
			$message = "There was an problem deleting a $module_code link during a module link deletion module_key=$module_key".$CONN->ErrorMsg();
			email_error($message);
			return $message;
		} else { 
			$sql="DELETE FROM {$CONFIG['DB_PREFIX']}calendar_events WHERE module_key='$module_key'";
			if ($CONN->Execute($sql) === false) {   
				$message = 'There was an error deleting calendar events - '.$CONN->ErrorMsg();
				email_error($message);
				return $message;
			} else {	
				$sql="DELETE FROM {$CONFIG['DB_PREFIX']}event_types WHERE parent='$module_key'";
				if ($CONN->Execute($sql) === false) {   
					$message = 'There was an error deleting event types - '.$CONN->ErrorMsg();
					email_error($message);
					return $message;
				} else {	
					return true;
				}
			}
		}
	} else {	
		return true;
	}
} //end delete_calendar 

/**
* Function called by Module class to flag a calendar for deletion 
*
* @param  int $module_key  key of calendar module
* @param  int $space_key  space key of calendar module
* @param  int $link_key  link key of calendar module being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_calendar_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
	return true;
} //end flag_calendar_for_deletion	

/**
* Function called by Module class to copy a calendar 
*
* @param  int $existing_module_key  key of calendar being copied
* @param  int $existing_link_key  link key of calendar module being copied
* @param  int $new_module_key  key of calendar being created
* @param  int $new_link_key  link key of calendar module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of calendar module
* @param  int $new_group_key  group key of new calendar
* @return true if successful
*/
function copy_calendar($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	
	$parent_calendar_key = $CONN->qstr($module_data['parent_calendar_key']);
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}calendars(module_key,parent_calendar_key) values ('$new_module_key',$parent_calendar_key)";
	
	if ($CONN->Execute($sql) === false) {
		$message =  'There was an error copying your calendar: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {	  
		return true;  
	}

} //end copy_calendar

/**
* Function called by Module class to add new Calendar link
*
* @param  int $module_key  key of calendar module
* @return true if successful
*/

function add_calendar_link($module_key) {
	return true;
}

/**
* Function called by auto.php to run any automated functions
*
* @return true if successful
*/

function autofunctions_calendar($last_cron) {
	global $CONN, $CONFIG;
	
	$CONN->Execute("DELETE from {$CONFIG['DB_PREFIX']}calendar_events WHERE remove_date < CURDATE() AND remove_date > '0'");
		
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

function user_delete_calendar($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}CalenderEvents SET user_key='$deleted_user' WHERE user_key='$user_key'");
		
	return true;

}
?>

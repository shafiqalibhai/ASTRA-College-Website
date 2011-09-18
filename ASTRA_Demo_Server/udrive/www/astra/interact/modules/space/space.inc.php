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
* Space  add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* space
*
* @package Space
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: space.inc.php,v 1.12 2007/04/24 04:03:28 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new space
*
* @param  int $module_key  key of new space
* @return true if details added successfully
*/


function add_space($module_key) {
 	
	global $CONN, $CONFIG, $objSpaceAdmin;

	foreach($_POST as $key => $value ) {
	
		$space_data[$key] = $value;
	
	}			
	
	$space_data['module_key'] = $module_key;
	//if user not super admin set current user as space admin
	if ($_SESSION['userlevel_key']!=1) {
		$space_data['space_admin_key'] = $_SESSION['current_user_key'];
	}
	if (!isset($objSpaceAdmin) || !is_object($objSpaceAdmin)) {
		if (!class_exists('InteractSpaceAdmin')) {
			require_once('../../spaceadmin/lib.inc.php');
		}
		$objSpaceAdmin = new InteractSpaceAdmin();
	}
	
	$message = $objSpaceAdmin->addSpace($space_data);
	
	return $message;
	
}

/**
* Function called by Module class to get exisiting space data 
*
* @param  int $module_key  key of space
* @return true if data retrieved
*/

function get_space_data($module_key) {

	 
	global $CONN, $module_data, $CONFIG;
	 
	if (!class_exists(InteractSpaceAdmin)) {

		require_once('../../spaceadmin/lib.inc.php');
		
	
	}
	
	$objSpaceAdmin = new InteractSpaceAdmin();
	$space_data   = $objSpaceAdmin->getSpaceData($module_key);
	$module_data = array_merge($module_data, $space_data);	
	return true;


}

/**
* Function called by Module class to modify exisiting space data 
*
* @param  int $module_key  key of space
* @param  int $link_key  link key of space  being modified
* @return true if successful
*/

function modify_space($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	foreach($_POST as $key => $value ) {
	
		$space_data[$key] = $value;
	
	}			
	
	$space_data['module_key'] = $module_key;
	
	if (!class_exists(InteractSpaceAdmin)) {

		require_once('../../spaceadmin/lib.inc.php');
		
	
	}
	
	$objSpaceAdmin = new InteractSpaceAdmin();
	
	$message = $objSpaceAdmin->modifySpace($space_data);
	
	return $message;

} //end modify_space


/**
* Function called by Module class to delete exisiting space
*
* @param  int $module_key  key of space
* @param  int $space_key  parent space key of space
* @param  int $link_key  link key of space 
* @param  int $delete_action 
* @return true if successful
*/
function delete_space($module_key,$space_key,$link_key,$delete_action) 
{

	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {
		//first of all get this spaces space key
		$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
	
		while(!$rs->EOF) {
	
			$space_key2 = $rs->fields[0];
			$rs->MoveNext();
	
		}
		$rs->Close();
	
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key2'";
		$CONN->Execute($sql);

		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key2'";
		$CONN->Execute($sql);

		$sql="DELETE FROM {$CONFIG['DB_PREFIX']}statistics WHERE space_key='$space_key2'";
		$CONN->Execute($sql);

		$sql="DELETE FROM {$CONFIG['DB_PREFIX']}default_space_user_links WHERE space_key='$space_key2'";
		$CONN->Execute($sql);

		//now delete any user notes attached to this space
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}UserNotes WHERE url like '%space_key=$space_key2%'");
	
	}
			
		return true;

} //end delete_space

/**
* Function called by Module class to flag a space for deletion 
*
* @param  int $module_key  key of space
* @param  int $space_key  space key of space parent
* @param  int $link_key  link key of dropbox space
* @param  int $delete_action 
* @return true if successful
*/
function flag_space_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	global $CONN, $CONFIG, $modules;
	
	//first of all get this spaces space key
	$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
	
	
	while(!$rs->EOF) {
	
		$space_key2 = $rs->fields[0];
		$rs->MoveNext();
	
	}
	$rs->Close();
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key  AND  ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key2' AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key=0)";


		$rs=$CONN->Execute($sql);

		if (!$rs->EOF) {
	
			while (!$rs->EOF) {

				$child_module_key = $rs->fields[0];
				$child_link_key = $rs->fields[1];			
				$child_module_code = $rs->fields[2];
				
				$modules->flag_module_for_deletion($child_module_key,$space_key2,$child_link_key,'link_only',$child_module_code,false,true);
				$rs->MoveNext();

			}
		
		}
		
	}

	return true;


} //end flag_space_for_deletion   

/**
* Function called by Module class to copy a space 
*
* @param  int $existing_module_key  key of space being copied
* @param  int $existing_link_key  link key of space being copied
* @param  int $new_module_key  key of space being created
* @param  int $new_link_key  link key of space being created
* @param  int $module_data  array of existing space data
* @param  int $space_key  parent space key of space
* @param  int $new_group_key  group key of new space
* @return true if successful
*/
function copy_space($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	global $CONN, $CONFIG, $modules;
	 
	//first of all get this spaces space key
	$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$existing_module_key'");
	
	while(!$rs->EOF) {
	
		$existing_space_key = $rs->fields[0];
		$rs->MoveNext();
	
	}
	$rs->Close();
	$sql = "Select link_key,{$CONFIG['DB_PREFIX']}module_space_links.module_key from {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key  AND (space_key='$existing_space_key' AND parent_key='0') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'";

	$rs = $CONN->Execute($sql);

	$module_data['module_key'] = $new_module_key;
	
	if (!class_exists(InteractSpaceAdmin)) {

		require_once('../../spaceadmin/lib.inc.php');
		
	
	}
	
	$objSpaceAdmin = new InteractSpaceAdmin();
	$module_data['code'] = $objSpaceAdmin->generateSpaceCode();
	$objSpaceAdmin->addSpace($module_data);
	
	//now get the space key of new space
	$rs_new_space = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$new_module_key'");
	
	while(!$rs_new_space->EOF) {
	
		$new_space_key = $rs_new_space->fields[0];
		$rs_new_space->MoveNext();
	
	}
	$rs_new_space->Close();
	
		
	while (!$rs->EOF) {
	
		$existing_link_key	= $rs->fields[0];
		$existing_module_key  = $rs->fields[1];
		$modules->copy_module($existing_module_key,$existing_link_key,$new_space_key,'0');
		$rs->MoveNext();
	
	}
	 
	return true;
} //end copy_space


/**
* Function called by Module class to add new space link
*
* @param  int $module_key  key of space
* @return true if successful
*/

function add_space_link($module_key,$existing_link_key,$new_link_key) {

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

function user_delete_space($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
			
	return true;

}

?>

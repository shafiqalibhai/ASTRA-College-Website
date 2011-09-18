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
* gradebook module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* gradebook
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: gradebook.inc.php,v 1.6 2007/01/04 22:09:04 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new gradebook module
*
* @param  int $module_key  key of new gradebook module
* @return true if details added successfully
*/


function add_gradebook($module_key) {
 	
		return true;

}

/**
* Function called by Module class to get exisiting gradebook data 
*
* @param  int $module_key  key of gradebook module
* @return true if data retrieved
*/

function get_gradebook_data($module_key) {

	 
	 return true;

}

/**
* Function called by Module class to modify exisiting gradebook data 
*
* @param  int $module_key  key of gradebook module
* @param  int $link_key  link key of gradebook module being modified
* @return true if successful
*/

function modify_gradebook($module_key,$link_key) {

	global $CONN,$status_key, $CONFIG;
	
	
			return true;  


} //end modify_gradebook


/**
* Function called by Module class to delete exisiting gradebook data 
*
* @param  int $module_key  key of gradebook module
* @param  int $space_key  space key of gradebook module
* @param  int $link_key  link key of gradebook module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_gradebook($module_key,$space_key,$link_key,$delete_action) 
{

	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT item_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE module_key='$module_key'");
	
	while (!$rs->EOF) {
	
		$item_key = $rs->fields[0];
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE item_key='$item_key'");		
		$rs->MoveNext();
		
	}
	
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE module_key='$module_key'");	
	 
	return true;

} //end delete_gradebook

/**
* Function called by Module class to flag a gradebook for deletion 
*
* @param  int $module_key  key of gradebook module
* @param  int $space_key  space key of gradebook module
* @param  int $link_key  link key of dropbox gradebook being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_gradebook_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_gradebook_for_deletion   

/**
* Function called by Module class to copy a gradebook 
*
* @param  int $existing_module_key  key of gradebook being copied
* @param  int $existing_link_key  link key of gradebook module being copied
* @param  int $new_module_key  key of gradebook being created
* @param  int $new_link_key  link key of gradebook module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of gradebook module
* @param  int $new_group_key  gradebook key of new folder
* @return true if successful
*/
function copy_gradebook($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	return true;  
	
} //end copy_gradebook


/**
* Function called by Module class to add new gradebook link
*
* @param  int $module_key  key of gradebook module
* @return true if successful
*/

function add_gradebook_link($module_key,$existing_link_key,$new_link_key) {

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

function user_delete_gradebook($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE user_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_items SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_items SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");		
			
	return true;

}

?>

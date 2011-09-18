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
* Sharing module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* sharing module
*
* @package Sharing
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: sharing.inc.php,v 1.15 2007/05/18 07:09:43 websterb4 Exp $
* 
*/

/**
* Function called by Module class when adding a new sharing module
*
* @param  int $module_key  key of new group module
* @return true if details added successfully
*/

function add_sharing($module_key) {

	global $CONN, $CONFIG;
	

	   
		//create a directory to store sharing files in
		
		mt_srand ((float) microtime() * 1000000);
		$subdirectory = mt_rand(1,100);
		$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$subdirectory;

		if (!Is_Dir($subdirectory_path)) {

			mkdir($subdirectory_path,0777);
		
		}   
		$sharing_path = $subdirectory.'/'.$module_key;
		$full_sharing_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$sharing_path;
		if (!Is_Dir($full_sharing_path)) {

			mkdir($full_sharing_path,0777);
		
		}   
   
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}sharing_settings(module_key,file_path) VALUES ('$module_key','$sharing_path')";
		if ($CONN->Execute($sql) === false) {
 
			$message =  'There was an error adding your sharing area: '.$CONN->ErrorMsg().' <br />';
			return $message;
 
		} else { 
	 
			return true;  

		}
	

}

/**
* Function called by Module class to get exisiting sharing data 
*
* @param  int $module_key  key of sharing module
* @return true if data retrieved
*/
function get_sharing_data($module_key) {

	return true;

}

/**
* Function called by Module class to modify exisiting sharing data 
*
* @param  int $module_key  key of sharing module
* @param  int $link_key  link key of sharing module being modified
* @return true if successful
*/
function modify_sharing($module_key,$link_key) {

	return true;

} //end modify_sharing



/**
* Function called by Module class to delete exisiting sharing data 
*
* @param  int $module_key  key of sharing module
* @param  int $space_key  space key of sharing module
* @param  int $link_key  link key of sharing module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_sharing($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "select file_path from {$CONFIG['DB_PREFIX']}sharing_settings where module_key='$module_key'";

		$rs = $CONN->Execute($sql);
		$file_path = $rs->fields[0];
		
		$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$file_path;

		if (Is_Dir($directory_path)) {
			delete_directory($directory_path);
		}		

		if(isset($CONFIG['FLASH_COM'])) {
			
	$domname=ereg_replace("https?://([^/]+)","\\1",$CONFIG['SERVER_URL']);
	$directory_path=$CONFIG['FLASH_COM'].'recordings/streams/'.$domname.'/modules/sharing/'.$file_path;
			if (Is_Dir($directory_path)) {
				delete_directory($directory_path);
			}		
		}
		
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}sharing_settings WHERE module_key='$module_key'";
		
		$CONN->Execute($sql);		
		   $rows_affected = $CONN->Affected_Rows();

		   if ($rows_affected < '1') {	

			   $message = "There was an problem deleting a $module_code during a module  deletion module_key=$module_key".$CONN->ErrorMsg();
			email_error($message);
			return $message;
		
		} else { 
			
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}shared_item_comments WHERE module_key='$module_key'";
			$CONN->Execute($sql);
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}shared_items WHERE module_key='$module_key'";
			$CONN->Execute($sql);		
			 return true;
		}
	
	} else {	
	
		return true;
		
	}

} //end delete_sharing	 

/**
* Function called by Module class to flag a sharing for deletion 
*
* @param  int $module_key  key of sharing module
* @param  int $space_key  space key of sharing module
* @param  int $link_key  link key of dropbox sharing being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_sharing_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_sharing_for_deletion

/**
* Function called by Module class to copy a sharing module
*
* @param  int $existing_module_key  key of sharing being copied
* @param  int $existing_link_key  link key of sharing module being copied
* @param  int $new_module_key  key of sharing being created
* @param  int $new_link_key  link key of sharing module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of sharing module
* @param  int $new_group_key  group key of new sharing
* @return true if successful
*/
function copy_sharing($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data, $space_key,$new_group_key) 
{

	global $CONN,$CONFIG, $SHARING;
	
	//create a directory to store sharing files in
		
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	}   

	$sharing_path = $subdirectory.'/'.$new_module_key;
	$full_sharing_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$sharing_path;

	if (!Is_Dir($full_sharing_path)) {

			mkdir($full_sharing_path,0777);
		
	}   
   
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}sharing_settings(module_key,file_path) VALUES ('$new_module_key','$sharing_path')";

	if ($CONN->Execute($sql) === false) {
 
		$message =  'There was an error copying your sharing area: '.$CONN->ErrorMsg().' <br />';
		return $message;
 
	} else { 
	 
		return true;  

	}

} //end copy_sharing

/**
* Function called by Module class to add new sharing link
*
* @param  int $module_key  key of sharing module
* @return true if successful
*/

function add_sharing_link($module_key) {

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

function user_delete_sharing($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}shared_items SET user_key='$deleted_user' WHERE user_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}shared_item_comments SET user_key='$deleted_user' WHERE user_key='$user_key'");
						
	return true;

}
/**
* Function called to see if there are any new or updated items in sharing areas
* a user from the system
*
* @param int $user_key key of user to check for
* @return true if successful
*/

function updated_items_sharing($user_key, $last_use, $groups_sql, &$updated_items, $module_strings, $space_key='') {

	global $CONN, $CONFIG, $general_strings;
	
	if (isset($space_key) && $space_key!='') {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}shared_items,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}shared_items.module_key={$CONFIG['DB_PREFIX']}modules.module_key  AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key  AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql) AND ({$CONFIG['DB_PREFIX']}shared_items.date_added >='$last_use') AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key')";
		
	} else {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}shared_items,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}space_user_links,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}shared_items.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key  AND {$CONFIG['DB_PREFIX']}space_user_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key  AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql) AND ({$CONFIG['DB_PREFIX']}shared_items.date_added >='$last_use') AND ({$CONFIG['DB_PREFIX']}space_user_links.user_key='$user_key')";
	
	}
	
	$rs = $CONN->Execute($sql);

	if (!$rs->EOF) {

		$updated_items.='<br /><strong>'.$module_strings['sharing'].'</strong><br />';
	
		while (!$rs->EOF) {
	
			$module_key = $rs->fields[0];
			$name = $rs->fields[1];
			$group_key = $rs->fields[2];
			$space_key2 = $rs->fields[3];
			$space_name = $rs->fields[4];		
			
			if (isset($space_key) && $space_key!='') {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/sharing/sharing.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$name</a><br />";
				
			} else {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/sharing/sharing.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$space_name - $name</a><br />";
							
			}
			$rs->MoveNext();
	
		}
	
		$rs->Close();

	}	

	return true;

} //end updated_items_sharing
?>
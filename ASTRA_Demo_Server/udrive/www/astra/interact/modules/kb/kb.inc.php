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
* KnowledgeBase module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* knowledgebase
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: kb.inc.php,v 1.14 2007/05/18 07:09:43 websterb4 Exp $
* 
*/

/**
* Function called by Module class when adding a new kb module
*
* @param  int $module_key  key of new kb module
* @return true if details added successfully
*/


function add_kb($module_key) {
 	
	global $CONN, $CONFIG;

	$access_level_key = $_POST['kbaccess_level_key'];
		
	//create a diretcory to store the any knowledgebase files in
	  
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		if (!mkdir($subdirectory_path,0777)) {
		
			$message = 'There was an error adding your knowledgebase';
			return $message;
						
		}
		
	} 
		
	$file_path = $subdirectory.'/'.$module_key;
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$file_path;

	if (!Is_Dir($full_file_path)) {

		if (!mkdir($full_file_path,0777)) {
			
			$message = 'There was an error adding your knowledgebase';
			return $message;
				
		}
		
	}
	   
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}kb_settings (module_key,access_level_key, file_path) VALUES ('$module_key','$access_level_key', '$file_path')";

	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your kb: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
		//add default templates
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_module_template_links(module_key, template_key) VALUES ('$module_key','1')");
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_module_template_links(module_key, template_key) VALUES ('$module_key','2')");
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_module_template_links(module_key, template_key) VALUES ('$module_key','3')");
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_module_template_links(module_key, template_key) VALUES ('$module_key','4')");
		return true;  
		
	}  
		
}

/**
* Function called by Module class to get exisiting kb data 
*
* @param  int $module_key  key of kb module
* @return true if data retrieved
*/

function get_kb_data($module_key) {

	 
	global $CONN, $module_data, $CONFIG;
	 
	$sql = "SELECT access_level_key, file_path FROM {$CONFIG['DB_PREFIX']}kb_settings WHERE module_key=$module_key";	
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_data['kbaccess_level_key'] = $rs->fields[0];
		$module_data['file_path']		= $rs->fields[1];		
		$rs->MoveNext();
	
	}

	return true;


}

/**
* Function called by Module class to modify exisiting kb data 
*
* @param  int $module_key  key of kb module
* @param  int $link_key  link key of kb module being modified
* @return true if successful
*/

function modify_kb($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	$access_level_key = $_POST['kbaccess_level_key'];
	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}kb_settings SET access_level_key='$access_level_key' WHERE module_key=$module_key";	
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your knowledgebase: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}	

} //end modify_kb


/**
* Function called by Module class to delete exisiting kb data 
*
* @param  int $module_key  key of kb module
* @param  int $space_key  space key of kb module
* @param  int $link_key  link key of kb module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_kb($module_key,$space_key,$link_key,$delete_action) 
{

	global $CONN, $CONFIG;
	if (!class_exists('InteractKB')) {
		require_once($CONFIG['BASE_PATH'].'/modules/kb/lib.inc.php');
	}
	$objKb = new InteractKB($space_key, $module_key, '', '', '');
	$kb_data = $objKb->getKbData($module_key);
	
	if ($kb_data['file_path']!='') {
		
		$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$kb_data['file_path'];
//echo $directory_path;
		if (is_Dir($directory_path)) {
		
			delete_directory($directory_path);
		
		 }  
			
	}  
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_settings WHERE module_key=$module_key");
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_module_template_links WHERE module_key=$module_key");
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE module_key=$module_key");
		
	//now delete all the entries
	
	$rs = $CONN->Execute("SELECT entry_key FROM {$CONFIG['DB_PREFIX']}kb_entries WHERE module_key=$module_key");
	
	while (!$rs->EOF) {
	
		$objKb->deleteEntry($rs->fields[0]);
		$rs->MoveNext();
	
	}

	return true;

} //end delete_kb

/**
* Function called by Module class to flag a kb for deletion 
*
* @param  int $module_key  key of kb module
* @param  int $space_key  space key of kb module
* @param  int $link_key  link key of dropbox kb being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_kb_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_kb_for_deletion   

/**
* Function called by Module class to copy a kb 
*
* @param  int $existing_module_key  key of kb being copied
* @param  int $existing_link_key  link key of kb module being copied
* @param  int $new_module_key  key of kb being created
* @param  int $new_link_key  link key of kb module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of kb module
* @param  int $new_group_key  kb key of new folder
* @return true if successful
*/
function copy_kb($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	 global $CONN, $CONFIG;
	 
	
} //end copy_kb


/**
* Function called by Module class to add new kb link
*
* @param  int $module_key  key of kb module
* @return true if successful
*/

function add_kb_link($module_key,$existing_link_key,$new_link_key) {

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

function user_delete_kb($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_entries SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
	$rs = $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_entries SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");
	$rs = $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_templates SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
	$rs = $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_templates SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");
	$rs = $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_fields SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
	$rs = $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_fields SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");
		
	return true;

}
/**
* Function called to see if there are any new or updated items in kb
* a user from the system
*
* @param int $user_key key of user to check for
* @return true if successful
*/

function updated_items_kb($user_key, $last_use, $groups_sql, &$updated_items, $module_strings, $space_key='') {

	global $CONN, $CONFIG, $general_strings;
	
	if (!empty($space_key)) {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}kb_entries,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}kb_entries.module_key={$CONFIG['DB_PREFIX']}modules.module_key  AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key  AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql) AND ({$CONFIG['DB_PREFIX']}kb_entries.date_added >='$last_use' AND {$CONFIG['DB_PREFIX']}kb_entries.status_key='2') AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key')";
		
	} else {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM 
		{$CONFIG['DB_PREFIX']}kb_entries,
		{$CONFIG['DB_PREFIX']}modules,
		{$CONFIG['DB_PREFIX']}spaces,
		{$CONFIG['DB_PREFIX']}space_user_links,
		{$CONFIG['DB_PREFIX']}module_space_links 
		WHERE 
		{$CONFIG['DB_PREFIX']}kb_entries.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
		AND 
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key
		AND 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key
		AND
		((
		{$CONFIG['DB_PREFIX']}space_user_links.user_key='$user_key' 
		AND 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key
		) OR (
			{$CONFIG['DB_PREFIX']}module_space_links.space_key='{$CONFIG['DEFAULT_SPACE_KEY']}'
		)
		) 
		AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' 
		AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql) 
		AND ({$CONFIG['DB_PREFIX']}kb_entries.date_added >='$last_use'  AND {$CONFIG['DB_PREFIX']}kb_entries.status_key='2')";

	}
	
	$rs = $CONN->Execute($sql);
	
echo $CONN->ErrorMsg();
	if (!$rs->EOF) {

		$updated_items.='<br /><strong>'.$module_strings['kb'].'</strong><br />';
	
		while (!$rs->EOF) {
	
			$module_key = $rs->fields[0];
			$name = $rs->fields[1];
			$group_key = $rs->fields[2];
			$space_key2 = $rs->fields[3];
			$space_name = $rs->fields[4];		
			
			if (!empty($space_key)) {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/kb/kb.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$name</a><br />";
				
			} else {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/kb/kb.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$space_name - $name</a><br />";
							
			}
			$rs->MoveNext();
	
		}
	
		$rs->Close();
		
	}	
	return true;
}
?>

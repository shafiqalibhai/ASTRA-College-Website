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
* Dropbox module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* dropbox module
*
* @package Dropbox
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: dropbox.inc.php,v 1.20 2007/05/18 07:09:43 websterb4 Exp $
* 
*/

/**
* Function called by Module class when adding a new dropbox module
*
* @param  int $module_key  key of new dropbox module
* @return true if details added successfully
*/

function add_dropbox($module_key) {

	global $CONN, $CONFIG;
	
	//create a directory to store dropbox files in
		
	$dropbox_type_key = $_POST['dropbox_type_key'];
	$time_allowed	 = $_POST['time_allowed'];
	$file_name		= $_FILES['file']['name'];
	$file			 = $_FILES['file']['tmp_name']; 
	
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	}   

	$dropbox_path = $subdirectory."/".$module_key;
	$full_dropbox_path=$CONFIG['MODULE_FILE_SAVE_PATH']."/dropbox/".$dropbox_path;

	if (!Is_Dir($full_dropbox_path)) {

		mkdir($full_dropbox_path,0777);
		
	}   
   
	//if this is a timed dropbox and there is a file, get the file details and copy it to the 
	//dropbox directory
	
	if($dropbox_type_key==2 && ($file_name!='' && $file_name!='none')) {
	
		$file_name=ereg_replace("[^a-z0-9A-Z._]","",$file_name);
		$file_name = substr($file_name,-40);
		copy($file,$full_dropbox_path.'/'.$file_name);
		
	}
	
	
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}dropbox_settings(module_key,file_path, type_key,time_allowed, download_file) VALUES ('$module_key','$dropbox_path', '$dropbox_type_key' ,'$time_allowed', '$file_name')";

	if ($CONN->Execute("$sql") === false) {
 
		$message =  'There was an error adding your dropbox area: '.$CONN->ErrorMsg().' <br />';
		return $message;
 
	} else { 
	 
		return true;  

	}
	
} //end add_dropbox

/**
* Function called by Module class to get exisiting dropbox data 
*
* @param  int $module_key  key of dropbox module
* @return true if data retrieved
*/

function get_dropbox_data($module_key) {

	global $CONN, $CONFIG, $module_data;
	
	$sql = "SELECT type_key, time_allowed FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'";	
	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$module_data['dropbox_type_key'] = $rs->fields[0];
		$module_data['time_allowed'] = $rs->fields[1];		
		$rs->MoveNext();
	
	}
	
	return true;

}

/**
* Function called by Module class to modify exisiting dropbox data 
*
* @param  int $module_key  key of dropbox module
* @param  int $link_key  link key of dropbox module being modified
* @return true if successful
*/

function modify_dropbox($module_key,$link_key) {

	global $CONN, $CONFIG;

	$dropbox_type_key = $_POST['dropbox_type_key'];
	$time_allowed	 = $_POST['time_allowed'];
	$file_name		= $_FILES['file']['name'];
	$file			 = $_FILES['file']['tmp_name']; 
	//if this is a timed dropbox and there is a file, get the file details and copy it to the 
	//dropbox directory
	
	if($dropbox_type_key==2 && ($file_name!='' && $file_name!='none')) {
		
		//get the existing filename 
	
		$sql = "SELECT download_file, file_path FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'";	
	
		$rs = $CONN->Execute($sql);
	
		while (!$rs->EOF) {

			$download_file = $rs->fields[0];
			$dropbox_path  = $rs->fields[1];
			$rs->MoveNext();
	
		}
		
		$full_dropbox_path=$CONFIG['MODULE_FILE_SAVE_PATH']."/dropbox/".$dropbox_path;
		
		//delete old file if it exists
	
		if (file_exists($full_dropbox_path.'/'.$download_file)) {

			unlink($full_dropbox_path.'/'.$download_file);

		}

	
		$file_name=ereg_replace("[^a-z0-9A-Z._]","",$file_name);
		$file_name = substr($file_name,-40);
		copy($file,$full_dropbox_path.'/'.$file_name);
		
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}dropbox_settings SET time_allowed='$time_allowed',download_file='$file_name', type_key='$dropbox_type_key' WHERE module_key='$module_key'"; 
	
	} else {
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}dropbox_settings SET time_allowed='$time_allowed', type_key='$dropbox_type_key' WHERE module_key='$module_key'"; 
	
	}
	
	//now update the the dropbox settings data
   
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your page: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}	
	
	
} //end modify_dropbox



/**
* Function called by Module class to delete exisiting dropbox data 
*
* @param  int $module_key  key of dropbox module
* @param  int $space_key  space key of dropbox module
* @param  int $link_key  link key of dropbox module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_dropbox($module_key,$space_key,$link_key,$delete_action) 
{

	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}dropbox_download_links WHERE module_key='$module_key'";
		$CONN->Execute($sql);
		
		$sql = "select file_path from {$CONFIG['DB_PREFIX']}dropbox_settings where module_key='$module_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$file_path = $rs->fields[0];
			$rs->MoveNext();
		
		}
		
		$rs->Close();
		
		if ($file_path!='') {
		
			$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$file_path;

			if (is_Dir($directory_path)) {
		
				delete_directory($directory_path);
		
			}
			
			if(isset($CONFIG['FLASH_COM'])) {
			$domname=ereg_replace("https?://([^/]+)","\\1",$CONFIG['SERVER_URL']);
$directory_path=$CONFIG['FLASH_COM'].'recordings/streams/'.$domname.'/modules/dropbox/'.$file_path;
			if (Is_Dir($directory_path)) {
				delete_directory($directory_path);
			}		
			}
			
		}		
		
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'";
		
		$CONN->Execute($sql);		
		$rows_affected = $CONN->Affected_Rows();

		if ($rows_affected < '1') {	

			$message = "There was an problem deleting a $module_code during a module  deletion module_key=$module_key".$CONN->ErrorMsg();
			email_error($message);
			return $message;
		
		} else { 
			
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}dropbox_files WHERE module_key='$module_key'";
			$CONN->Execute($sql);
			return true;
		}
	
	} else {	
	
		return true;
		
	}

} //end delete_dropbox	 

/**
* Function called by Module class to flag a dropbox for deletion 
*
* @param  int $module_key  key of dropbox module
* @param  int $space_key  space key of dropbox module
* @param  int $link_key  link key of dropbox module being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_dropbox_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_dropbox_for_deletion	

/**
* Function called by Module class to copy a dropbox 
*
* @param  int $existing_module_key  key of dropbox being copied
* @param  int $existing_link_key  link key of dropbox module being copied
* @param  int $new_module_key  key of dropbox being created
* @param  int $new_link_key  link key of dropbox module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of dropbox module
* @param  int $new_group_key  group key of new dropbox
* @return true if successful
*/
function copy_dropbox($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	
	//create a directory to store dropbox files in
		
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	}   

	$dropbox_path = $subdirectory."/".$new_module_key;
	$full_dropbox_path=$CONFIG['MODULE_FILE_SAVE_PATH']."/dropbox/".$dropbox_path;

	if (!Is_Dir($full_dropbox_path)) {

		mkdir($full_dropbox_path,0777);
		
	}   
   
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}dropbox_settings(module_key,file_path) VALUES ('$new_module_key','$dropbox_path')";

	if ($CONN->Execute("$sql") === false) {
 
		$message =  'There was an error copying your dropbox area: '.$CONN->ErrorMsg().' <br />';
		return $message;
 
	} else { 
	 
		return true;  

	}

	

} //end copy_dropbox


/**
* Function called by Module class to add new Dropbox link
*
* @param  int $module_key  key of dropbox module
* @return true if successful
*/

function add_dropbox_link($module_key) {

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

function user_delete_dropbox($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}dropbox_files SET user_key='$deleted_user' WHERE user_key='$user_key'");
		
	return true;

}

/**
* Function called to see if there are any new or updated items in dropboxes
* a user from the system
*
* @param int $user_key key of user to check for
* @return true if successful
*/

function updated_items_dropbox($user_key, $last_use, $groups_sql, &$updated_items, $module_strings, $space_key='') {

	global $CONN, $CONFIG, $general_strings, $sql1, $sql2;

	if (isset($space_key) && $space_key!='') {
	
		$sql = "SELECT DISTINCT 
			{$CONFIG['DB_PREFIX']}modules.module_key,
			{$CONFIG['DB_PREFIX']}modules.name,
			{$CONFIG['DB_PREFIX']}module_space_links.group_key,
			{$CONFIG['DB_PREFIX']}module_space_links.space_key,
			{$CONFIG['DB_PREFIX']}spaces.name 
		FROM 
			{$CONFIG['DB_PREFIX']}dropbox_files,
			{$CONFIG['DB_PREFIX']}modules,
			{$CONFIG['DB_PREFIX']}space_user_links,
			{$CONFIG['DB_PREFIX']}spaces,
			{$CONFIG['DB_PREFIX']}module_space_links LEFT JOIN {$CONFIG['DB_PREFIX']}group_user_links 				
			ON 
	{$CONFIG['DB_PREFIX']}module_space_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key
		WHERE 
			{$CONFIG['DB_PREFIX']}dropbox_files.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
			AND 
	{$CONFIG['DB_PREFIX']}space_user_links.space_key={$CONFIG['DB_PREFIX']}module_space_links.space_key
			AND 
			{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key 
			AND 
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key 
			AND 
			({$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1' OR {$CONFIG['DB_PREFIX']}group_user_links.access_level_key='1')
			AND 
			({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR 	{$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql)
			AND
			{$CONFIG['DB_PREFIX']}dropbox_files.date_added >='$last_use' 
			AND 
			{$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key'"; 
		
	} else {
	
		$sql = "SELECT DISTINCT 
			{$CONFIG['DB_PREFIX']}modules.module_key,
			{$CONFIG['DB_PREFIX']}modules.name,
			{$CONFIG['DB_PREFIX']}module_space_links.group_key,
			{$CONFIG['DB_PREFIX']}module_space_links.space_key,
			{$CONFIG['DB_PREFIX']}spaces.name 
		FROM 
			{$CONFIG['DB_PREFIX']}dropbox_files,
			{$CONFIG['DB_PREFIX']}modules,
			{$CONFIG['DB_PREFIX']}space_user_links,
			{$CONFIG['DB_PREFIX']}spaces,
			{$CONFIG['DB_PREFIX']}module_space_links LEFT JOIN {$CONFIG['DB_PREFIX']}group_user_links 				
			ON 
	{$CONFIG['DB_PREFIX']}module_space_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key
		WHERE 
			{$CONFIG['DB_PREFIX']}dropbox_files.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
			AND 
	{$CONFIG['DB_PREFIX']}space_user_links.space_key={$CONFIG['DB_PREFIX']}module_space_links.space_key
			AND 
			{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key 
			AND 
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key 
			AND 
			({$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1' OR {$CONFIG['DB_PREFIX']}group_user_links.access_level_key='1')
			AND 
			({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR 	{$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql)
			AND
			{$CONFIG['DB_PREFIX']}dropbox_files.date_added >='$last_use' 
			AND 
			{$CONFIG['DB_PREFIX']}space_user_links.user_key='$user_key'";
	
	}

	$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
	if (!$rs->EOF) {

		$updated_items.='<br /><strong>'.$module_strings['dropbox'].'</strong><br />';
		$heading = true;
		while (!$rs->EOF) {
		
			$module_key = $rs->fields[0];
			$name = $rs->fields[1];
			$group_key = $rs->fields[2];
			$space_key2 = $rs->fields[3];
			$space_name = $rs->fields[4];		
			
			if (isset($space_key) && $space_key!='') {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/dropbox/dropbox.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$name</a><br />";
				
			} else {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/dropbox/dropbox.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$space_name - $name</a><br />";			
			
			} 
			$rs->MoveNext();
	
		}
	
		$rs->Close();

	}
	
	if (isset($space_key) && $space_key!='') {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}dropbox_files.module_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}dropbox_files.description,{$CONFIG['DB_PREFIX']}dropbox_file_status.name,{$CONFIG['DB_PREFIX']}spaces.short_name FROM {$CONFIG['DB_PREFIX']}dropbox_files,{$CONFIG['DB_PREFIX']}dropbox_file_status,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}dropbox_files.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND  {$CONFIG['DB_PREFIX']}dropbox_files.status={$CONFIG['DB_PREFIX']}dropbox_file_status.status_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND user_key='$user_key' AND (date_status_changed >='$last_use') AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key')";

	} else {

		$sql = "SELECT {$CONFIG['DB_PREFIX']}dropbox_files.module_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}dropbox_files.description,{$CONFIG['DB_PREFIX']}dropbox_file_status.name,{$CONFIG['DB_PREFIX']}spaces.short_name FROM {$CONFIG['DB_PREFIX']}dropbox_files,{$CONFIG['DB_PREFIX']}dropbox_file_status,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}dropbox_files.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND  {$CONFIG['DB_PREFIX']}dropbox_files.status={$CONFIG['DB_PREFIX']}dropbox_file_status.status_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND user_key='$user_key' AND (date_status_changed >='$last_use')";	
	
	}
	
	$rs = $CONN->Execute($sql);

	if (!$rs->EOF) {

		if ($heading!=true) {
		
			$updated_items.='<br /><strong>'.$module_strings['dropbox'].'</strong><br />';
			
		}
			
		while (!$rs->EOF) {
	
			$module_key = $rs->fields[0];
			$space_key2 = $rs->fields[1];
			$description = $rs->fields[2];
			$status = $rs->fields[3];
			$space_short_name = $rs->fields[4];
			
			if (isset($space_key) && $space_key!='') {
				$updated_items.="<a href=\"{$CONFIG['PATH']}/modules/dropbox/dropbox.php?space_key=$space_key&module_key=$module_key\">$description</a> - <span>$status</span><br />";
				
			} else {
			
				$updated_items.="<a href=\"{$CONFIG['PATH']}/modules/dropbox/dropbox.php?space_key=$space_key2&module_key=$module_key\">$description</a> - <span>$status</span><br />";			
			
			}
			$rs->MoveNext();
	
		}
	
		$rs->Close();

	}
		
	return true;

} //end updated_items_dropbox
?>
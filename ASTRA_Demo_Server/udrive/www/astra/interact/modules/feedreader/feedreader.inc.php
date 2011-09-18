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
* Feedreader Module functions
*
* Contains the functions for adding/modifying/deleting a feedreader
* module
*
* @package Feedreader
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: feedreader.inc.php,v 1.7 2007/01/29 01:34:35 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new feedreader module
*
* @param  int $module_key  key of new feedreader module
* @return true if details added successfully
*/

function add_feedreader($module_key) {

	global $CONN, $CONFIG;
	
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/feedreader/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	}  
	$feedreader_path = $subdirectory."/".$module_key;
	$full_feedreader_path=$CONFIG['MODULE_FILE_SAVE_PATH']."/feedreader/".$feedreader_path;

	if (!is_dir($full_feedreader_path)) {

		mkdir($full_feedreader_path,0777);
		
	}   
	$url = $_POST['feed_url'];
	$item_count = $_POST['item_count'];
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}feedreader_settings(module_key,url, file_path, item_count) values ('$module_key','$url','$feedreader_path', '$item_count')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your feedreader: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}
	

}

/**
* Function called by Module class to get exisiting feedreader data 
*
* @param  int $module_key  key of feedreader module
* @return true if data retrieved
*/

function get_feedreader_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT url, file_path, item_count FROM {$CONFIG['DB_PREFIX']}feedreader_settings WHERE module_key='$module_key'";	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$module_data['feed_url'] = $rs->fields[0];
		$module_data['file_path'] = $rs->fields[1];
		$module_data['item_count'] = $rs->fields[2];
		$rs->MoveNext();
	
	}
	
	return true;

}

/**
* Function called by Module class to get exisiting feedreader data 
*
* @param  int $module_key  key of feedreader module
* @return true if data retrieved
*/

function modify_feedreader($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	$url = $_POST['feed_url'];
	$item_count = $_POST['item_count'];
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}feedreader_settings SET url='$url', item_count='$item_count' WHERE module_key='$module_key'";	
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your feedreader: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}

} //end modify_feedreader



/**
* Function called by Module class to delete exisiting feedreader data 
*
* @param  int $module_key  key of feedreader module
* @param  int $space_key  space key of feedreader module
* @param  int $link_key  link key of feedreader module being deleted
* @param  int $delete_action 
* @return true if successful
*/
function delete_feedreader($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {
		
		$file_path = $CONN->GetOne("SELECT file_path FROM {$CONFIG['DB_PREFIX']}feedreader_settings WHERE module_key='$module_key'");
		
		if ($file_path!='') {
			$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/feedreader/'.$file_path;
			if (is_dir($directory_path)) {
				delete_directory($directory_path);
			}  
		}  
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}feedreader_settings WHERE module_key='$module_key'";
		
		$CONN->Execute($sql);		
		   $rows_affected = $CONN->Affected_Rows();

		   if ($rows_affected < '1') {	

			   $message = "There was an problem deleting a $module_code link during a module link deletion module_key=$module_key".$CONN->ErrorMsg();
			   email_error($message);
			   return $message;
		
		} else { 
	
			return true;
						
		}
	
	} else {	
	
		return true;
		
	}

} //end delete_feedreader	 

/**
* Function called by Module class to flag a feedreader for deletion 
*
* @param  int $module_key  key of feedreader module
* @param  int $space_key  space key of feedreader module
* @param  int $link_key  link key of feedreader being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_feedreader_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_feedreader_for_deletion

/**
* Function called by Module class to copy a weblik 
*
* @param  int $existing_module_key  key of weblik  being copied
* @param  int $existing_link_key  link key of weblik  module being copied
* @param  int $new_module_key  key of weblik  being created
* @param  int $new_link_key  link key of weblik  module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of weblik  module
* @param  int $new_group_key  heading key of new weblik 
* @return true if successful
*/
function copy_feedreader($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data, $space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	$url = $CONN->qstr($module_data['link_url']);
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/feedreader/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	}  
	$feedreader_path = $subdirectory."/".$new_module_key;
	$full_feedreader_path=$CONFIG['MODULE_FILE_SAVE_PATH']."/feedreader/".$feedreader_path;

	if (!is_dir($full_feedreader_path)) {

		mkdir($full_feedreader_path,0777);
		
	}   

	$sql = "insert into {$CONFIG['DB_PREFIX']}feedreader_settings(module_key,url,file_path) values ('$new_module_key',$url, '$feedreader_path')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error copying your feedreader: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}

	

} //end copy_feedreader

/**
* Function called by Module class to add new feedreader link
*
* @param  int $module_key  key of feedreader module
* @return true if successful
*/

function add_feedreader_link($module_key) {

	return true;

}

?>
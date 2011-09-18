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
* Heading module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* group heading
*
* @package Heading
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: heading.inc.php,v 1.4 2005/12/13 23:54:51 websterb4 Exp $
* 
*/

/**
* Function called by Module class when adding a new heading module
*
* @param  int $module_key  key of new heading module
* @return true if details added successfully
*/


function add_heading($module_key) {
	global $CONN, $CONFIG;
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}headings(module_key,initial_state,level) values ('$module_key',{$_POST['initial_state']},{$_POST['level']})";
	
	if ($CONN->Execute($sql) === false) {
		return 'There was an error adding your heading: '.$CONN->ErrorMsg().' <br />';
	} else {	  
		return true;  
	}
}

/**
* Function called by Module class to get exisiting heading data 
*
* @param  int $module_key  key of heading module
* @return true if data retrieved
*/

function get_heading_data($module_key) {
	global $CONN,$module_data, $CONFIG;
	$rs = $CONN->Execute("SELECT initial_state, level FROM {$CONFIG['DB_PREFIX']}headings WHERE module_key='$module_key'");
	
	if (!$rs->EOF) {
		$module_data['initial_state'] = $rs->fields[0];	
		$module_data['level'] = $rs->fields[1];
	}

	return true;

}

/**
* Function called by Module class to modify exisiting heading data 
*
* @param  int $module_key  key of heading module
* @param  int $link_key  link key of heading module being modified
* @return true if successful
*/

function modify_heading($module_key,$link_key) {
	global $CONN,$status_key,$CONFIG;
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}headings SET initial_state='{$_POST['initial_state']}', level='{$_POST['level']}' WHERE module_key=$module_key";
	
	if ($CONN->Execute($sql) === false) {
		return 'There was an error modifying your heading: '.$CONN->ErrorMsg().' <br />';
	} else {	  
		return true;  
	}

} //end modify_heading


/**
* Function called by Module class to delete exisiting heading data 
*
* @param  int $module_key  key of heading module
* @param  int $space_key  space key of heading module
* @param  int $link_key  link key of heading module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_heading($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;

	$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}headings WHERE module_key='$module_key'";
	
	$CONN->Execute($sql);		
	   $rows_affected = $CONN->Affected_Rows();

	   if ($rows_affected < '1') {	

		   $message = "There was a problem deleting heading (module_key=$module_key).  ".$CONN->ErrorMsg();
		email_error($message);
		return $message;
	} else { 
		return true;
	}
} //end delete_heading

/**
* Function called by Module class to flag a heading for deletion 
*
* @param  int $module_key  key of heading module
* @param  int $space_key  space key of heading module
* @param  int $link_key  link key of dropbox heading being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_heading_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_heading_for_deletion   

/**
* Function called by Module class to copy a heading 
*
* @param  int $existing_module_key  key of heading being copied
* @param  int $existing_link_key  link key of heading module being copied
* @param  int $new_module_key  key of heading being created
* @param  int $new_link_key  link key of heading module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of heading module
* @param  int $new_group_key  heading key of new folder
* @return true if successful
*/
function copy_heading($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{
	global $CONN, $CONFIG;
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}headings(module_key,initial_state,level) values ('$new_module_key',{$module_data['initial_state']},{$module_data['level']})";
	
	if ($CONN->Execute($sql) === false) {
		return 'There was an error copying your heading: '.$CONN->ErrorMsg().' <br />';
	} else {	  
		return true;  
	}
} //end copy_heading


/**
* Function called by Module class to add new heading link
*
* @param  int $module_key  key of heading module
* @return true if successful
*/

function add_heading_link($module_key,$existing_link_key,$new_link_key) {

	return true;

}	


?>

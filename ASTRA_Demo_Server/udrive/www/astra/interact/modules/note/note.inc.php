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
* Note Module functions
*
* Contains the functions for adding/modifying/deleting a note
* module
*
* @package Note
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: note.inc.php,v 1.9 2007/01/29 01:34:37 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new note module
*
* @param  int $module_key  key of new note module
* @return true if details added successfully
*/

function add_note($module_key) {

	global $CONN, $CONFIG;
	$body = $_POST['body'];
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}notes(module_key,note) values ('$module_key','$body')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your note: '.$CONN->ErrorMsg().' <br />';

		return $message;
		
	} else {	  
	
			return true;  
	}
	

}

/**
* Function called by Module class to get exisiting note data 
*
* @param  int $module_key  key of note module
* @return true if data retrieved
*/

function get_note_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT note FROM {$CONFIG['DB_PREFIX']}notes WHERE module_key='$module_key'";	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$module_data['body'] = $rs->fields[0];
		$rs->MoveNext();
	
	}
	
	return true;

}

/**
* Function called by Module class to get exisiting note data 
*
* @param  int $module_key  key of note module
* @return true if data retrieved
*/

function modify_note($module_key,$link_key) {

	global $CONN, $CONFIG;
	$body = $_POST['body'];
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}notes SET note='$body' WHERE module_key='$module_key'";	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your note: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}

} //end modify_note



/**
* Function called by Module class to delete exisiting note data 
*
* @param  int $module_key  key of note module
* @param  int $space_key  space key of note module
* @param  int $link_key  link key of note module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_note($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}notes WHERE module_key='$module_key'";
		
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

} //end delete_note  

/**
* Function called by Module class to flag a note for deletion 
*
* @param  int $module_key  key of note module
* @param  int $space_key  space key of note module
* @param  int $link_key  link key of note being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_note_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_note_for_deletion   

/**
* Function called by Module class to copy a note 
*
* @param  int $existing_module_key  key of note being copied
* @param  int $existing_link_key  link key of note module being copied
* @param  int $new_module_key  key of note being created
* @param  int $new_link_key  link key of note module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of note module
* @param  int $new_group_key  heading key of new note
* @return true if successful
*/
function copy_note($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	$body = $CONN->qstr($module_data['body']);
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}notes(module_key,note) VALUES ('$new_module_key',$body)";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error copying your note: '.$CONN->ErrorMsg().' <br />';

		return $message;
		
	} else {	  
	
			return true;  
	}

} //end copy_note

/**
* Function called by Module class to add new note link
*
* @param  int $module_key  key of note module
* @return true if successful
*/

function add_note_link($module_key) {

	return true;

}

?>
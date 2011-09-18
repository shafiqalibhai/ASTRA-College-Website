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
* Weblink Module functions
*
* Contains the functions for adding/modifying/deleting a weblink
* module
*
* @package Weblink
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: weblink.inc.php,v 1.12 2007/01/29 01:34:39 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new weblink module
*
* @param  int $module_key  key of new weblink module
* @return true if details added successfully
*/

function add_weblink($module_key) {

	global $CONN, $CONFIG;
	
	$url = $_POST['link_url'];

	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}weblinks(module_key,url) values ('$module_key','$url')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your weblink: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}
	

}

/**
* Function called by Module class to get exisiting weblink data 
*
* @param  int $module_key  key of weblink module
* @return true if data retrieved
*/

function get_weblink_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT url FROM {$CONFIG['DB_PREFIX']}weblinks WHERE module_key='$module_key'";	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$module_data['link_url'] = $rs->fields[0];
		$rs->MoveNext();
	
	}
	
	return true;

}

/**
* Function called by Module class to get exisiting weblink data 
*
* @param  int $module_key  key of weblink module
* @return true if data retrieved
*/

function modify_weblink($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	$url = $_POST['link_url'];
	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}weblinks SET url='$url' WHERE module_key='$module_key'";	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your weblink: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}

} //end modify_weblink



/**
* Function called by Module class to delete exisiting weblink data 
*
* @param  int $module_key  key of weblink module
* @param  int $space_key  space key of weblink module
* @param  int $link_key  link key of weblink module being deleted
* @param  int $delete_action 
* @return true if successful
*/
function delete_weblink($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}weblinks WHERE module_key='$module_key'";
		
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

} //end delete_weblink	 

/**
* Function called by Module class to flag a weblink for deletion 
*
* @param  int $module_key  key of weblink module
* @param  int $space_key  space key of weblink module
* @param  int $link_key  link key of weblink being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_weblink_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_weblink_for_deletion

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
function copy_weblink($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data, $space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	$url = $CONN->qstr($module_data['link_url']);
	$sql = "insert into {$CONFIG['DB_PREFIX']}weblinks(module_key,url) values ('$new_module_key',$url)";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error copying your weblink: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}

	

} //end copy_weblink

/**
* Function called by Module class to add new weblink link
*
* @param  int $module_key  key of weblink module
* @return true if successful
*/

function add_weblink_link($module_key) {

	return true;

}

?>
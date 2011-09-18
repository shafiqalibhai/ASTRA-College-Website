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
* Notel Module functions
*
* Contains the functions for adding/modifying/deleting a page
* module
*
* @package Page
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: page.inc.php,v 1.15 2007/07/17 23:24:58 websterb4 Exp $
* 
*/

/**
* Function called by Module class when adding a new page module
*
* @param  int $module_key  key of new page module
* @return true if details added successfully
*/

function add_page($module_key) {

	global $CONN, $CONFIG;
	
	if ($_FILES['file']['name']) {
	 
	   $handle = fopen($_FILES['file']['tmp_name'], "r");
	   $body=fread($handle,filesize($_FILES['file']['tmp_name']));
	   fclose($handle);
	   escape_strings($body);

	} else {
	
		$body = $_POST['body'];
	
	} 
	$versions = isset($_POST['versions'])?$_POST['versions']:1;
	$page_edit_rights = isset($_POST['page_edit_rights'])?$_POST['page_edit_rights']:0;
	$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));
	$added_by_key = $_SESSION['current_user_key'];
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}pages(module_key,body,added_by_key, date_added) values ('$module_key','$body', '$added_by_key', $date_added)";
	
	if ($CONN->Execute($sql) === false) {
  
		$message =  'There was an error adding your page: '.$CONN->ErrorMsg().' <br />';

		return $message;
		
	} else {	  
	
		if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}page_settings(module_key,versions,edit_rights) values ('$module_key','$versions', '$page_edit_rights')") === false) {
  
			$message =  'There was an error adding your page: '.$CONN->ErrorMsg().' <br />';
		}else {	
			return true;  
		}
	}
	

}

/**
* Function called by Module class to get exisiting page data 
*
* @param  int $module_key  key of page module
* @return true if data retrieved
*/

function get_page_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$rs = $CONN->SelectLimit("SELECT body,versions, edit_rights FROM {$CONFIG['DB_PREFIX']}pages,{$CONFIG['DB_PREFIX']}page_settings WHERE {$CONFIG['DB_PREFIX']}pages.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}page_settings.module_key='$module_key' ORDER By page_key DESC",1);
	echo $CONN->ErrorMsg();
	$body = preg_replace("/(<[^>]*)(form[ >])/si", "$1embedded-$2", $rs->fields[0]);
	$body = preg_replace("/(<[^>]*)(textarea[ >])/si", "$1embedded-$2", $body);		
	$module_data['body'] = $body;
	$module_data['versions'] = $rs->fields[1];
	$module_data['page_edit_rights'] = $rs->fields[2];
	
	return true;

}

/**
* Function called by Module class to get exisiting page data 
*
* @param  int $module_key  key of page module
* @return true if data retrieved
*/

function modify_page($module_key,$link_key) {

	global $CONN, $CONFIG;

	if ($_FILES['file']['name']) {
	 
	   $handle = fopen($_FILES['file']['tmp_name'], "r");
	   $body=fread($handle,filesize($_FILES['file']['tmp_name']));
	   fclose($handle);
	   $body = $CONN->qstr($body);

	} else {

	   $body = $_POST['body'];
	   
	}
	
	$body = preg_replace("/(<[^>]*)embedded-(form[ >])/", "$1$2", $body);
  	$body = preg_replace("/(<[^>]*)embedded-(textarea[ >])/", "$1$2", $body);
	$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));
	$added_by_key = $_SESSION['current_user_key'];
	$versions = isset($_POST['versions'])?$_POST['versions']:1;
	$page_edit_rights = isset($_POST['page_edit_rights'])?$_POST['page_edit_rights']:0;
	if ($versions>0) {
		$version_count = $CONN->GetOne("SELECT COUNT(page_key) FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key'");
	
		if ($version_count>=$versions) {
			$page_to_delete = $CONN->GetOne("SELECT page_key FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key' ORDER BY page_key");
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}pages WHERE page_key='$page_to_delete'");
		}
	}
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}pages(module_key,body,added_by_key, date_added) values ('$module_key','$body', '$added_by_key', $date_added)";
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your page: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
		if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}page_settings SET versions='$versions',edit_rights='$page_edit_rights' WHERE module_key='$module_key'") === false) {
  
			$message =  'There was an error modifying your page: '.$CONN->ErrorMsg().' <br />';
			echo $message;
		}else {	
			return true;  
		} 
	}

} //end modify_page



/**
* Function called by Module class to delete exisiting page data 
*
* @param  int $module_key  key of page module
* @param  int $space_key  space key of page module
* @param  int $link_key  link key of page module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_page($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key'";
		
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

} //end delete_page	 

/**
* Function called by Module class to flag a page for deletion 
*
* @param  int $module_key  key of page module
* @param  int $space_key  space key of page module
* @param  int $link_key  link key of page being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_page_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_page_for_deletion

/**
* Function called by Module class to copy a page 
*
* @param  int $existing_module_key  key of page being copied
* @param  int $existing_link_key  link key of page module being copied
* @param  int $new_module_key  key of page being created
* @param  int $new_link_key  link key of page module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of page module
* @param  int $new_group_key  heading key of new page
* @return true if successful
*/
function copy_page($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	$body = $CONN->qstr($module_data['body']);
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}pages(module_key,body) values ('$new_module_key',$body)";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error copying your page: '.$CONN->ErrorMsg().' <br />';

		return $message;
		
	} else {	  
	
			return true;  
	}

} //end copy_page

/**
* Function called by Module class to add new page link
*
* @param  int $module_key  key of page module
* @return true if successful
*/

function add_page_link($module_key) {

	return true;

}

?>
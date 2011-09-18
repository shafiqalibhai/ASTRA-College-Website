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
* Move module
*
* Displays page for moving an existing module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: modulemove.php,v 1.18 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings
//error_reporting(E_ALL);
require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');

$link_key	= $_POST['link_key'];
$space_key   = $_POST['space_key'];
$move_to_key = $_POST['move_to_key'];
$module_key  = $_POST['module_key'];
$space_code  = $_POST['space_code'];
//check that we have the required variables
check_variables(true,true);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
if ($is_admin!=true) {

	$message =  urlencode($module_strings['no_admin_rights']);
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	exit;

}


$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.type_code  FROM {$CONFIG['DB_PREFIX']}modules WHERE  {$CONFIG['DB_PREFIX']}modules.module_key='$module_key'");

while (!$rs->EOF) {
	
	$module_code = $rs->fields[0];
	$rs->MoveNext();
	
}
	
$rs->Close();

//see if we have a space code, if so, check it is a real space
if (isset($space_code) && $space_code!='') {
	
	
	$move_to_space_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$space_code'");
	if ($move_to_space_key!=false) {
		//now lets make sure we are not moving a space down within itself
		if ($module_code=='space') {
			$space_to_move_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
			if ($move_to_space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
				check_space_below($space_to_move_key, $move_to_space_key);
			} else {
				$is_below = false;	
			}
			if ($is_below===true || $space_to_move_key==$move_to_space_key) {
				$message = urlencode("You cannot move a space down within itself");
				header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_to_move_key&message=$message");
				exit;	
			}
		}
		
		//update modulespace links
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET space_key='$move_to_space_key', parent_key='0' WHERE link_key='$link_key'");
		
		//now see if there are any module specific functions for moving to another space
		$module_file = $CONFIG['BASE_PATH'].'/modules/'.$module_code.'/'.$module_code.'.inc.php';
	
		if (file_exists($module_file)) {
	
			include_once($module_file);
			$move_space_function = 'move_space_'.$module_code;
	   		
			if (function_exists($move_space_function)) {
				$move_space_function($link_key,$move_to_space_key);				
			}
		}
		$message = urlencode("Your {$general_strings['module_text']} has been moved");
		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$move_to_space_key&message=$message");
		exit;
	
	} else {
		$message =  urlencode('There is no such '.$general_strings['space_text']);
		header('Location: '.$CONFIG['FULL_URL'].'/modules/'.$module_code.'/'.$module_code.'_input.php?space_key='.$space_key.'&module_key='.$module_key.'&link_key='.$link_key.'&action=modify&message='.$message);
		exit;
	}

}
//check if parent module has any syblings. If so item can't be moved
	
$sql = "SELECT parent_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$link_key'";
$rs = $CONN->Execute($sql);
	
while (!$rs->EOF) {

	$parent_key = $rs->fields[0];
	$rs->MoveNext();
		
} 
	
$rs->Close();
	
if ($parent_key!='0') {

	$sql = "SELECT module_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {
	
		$parent_module_key = $rs->fields[0];
		$rs->MoveNext();
		
	} 
	
	$rs->Close();	
		
	$sql = "SELECT link_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$parent_module_key' AND link_key!='$parent_key' AND status_key!='4'";
	$rs = $CONN->Execute($sql);
		
	if (!$rs->EOF) {
		
		$message = urlencode(sprintf($module_strings['move_fail'], $general_strings['module_text'], $general_strings['module_text']));
		$back_url = $CONFIG['FULL_URL']."/spaces/space.php?space_key=$space_key&message=".$message;
		header("Location: $back_url");
		exit;
			
	}
		
}	

if ($move_to_key!='') {

	//check if new parent has any syblings. If so item can't be moved
	
	$sql = "SELECT module_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$move_to_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$parent_module_key = $rs->fields[0];
		$rs->MoveNext();
		
	} 
	
	$rs->Close();
		
	$sql = "SELECT link_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$parent_module_key' AND link_key!='$move_to_key' AND status_key!='4'";
	$rs = $CONN->Execute($sql);
		
	if (!$rs->EOF) {
		
		   $message = urlencode(sprintf($module_strings['move_fail'], $general_strings['module_text'], $general_strings['module_text']));
		   $back_url = $CONFIG['FULL_URL']."/spaces/space.php?space_key=$space_key&message=".$message;
		header("Location: $back_url");
		   exit;
			
	}
		
	//if it is a folder or group being moved make sure we aren't trying to move it under one of its own children

	if ($module_code=='folder' || $module_code=='group') {

		check_is_below($move_to_key,$link_key);

		if ($is_below=='true') {

			$message = urlencode($module_strings['within_self']);
			$back_url = $HTTP_REFERER.'&message='.$message;
			header("Location: $back_url");
			exit;

		}

	}
	

	$sql = "SELECT group_key,type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND (link_key='$move_to_key')";
	$rs = $CONN->Execute($sql);

	if ($rs->EOF) {

		$message = urlencode($module_strings['no_such_module']);
		$back_url = $HTTP_REFERER.'&message='.$message;
		header("Location: $back_url");
		exit;

	} else {

		while (!$rs->EOF) {

			$group_key = $rs->fields[0];
			$move_to_type_code = $rs->fields[1];
			$rs->MoveNext();

		}

		if ($module_type_code=='group' && $move_to_type_code=='group') {

			$message = urlencode($module_strings['group_to_group']);
			$back_url = $HTTP_REFERER.'&message='.$message;
			header("Location: $back_url");
			exit;

		}

	}

} else {

	if ($module_type_code!='group') {

		$move_to_key='0';
		$group_key='0';

	} else {

		$move_to_key='0';

	}

}

if ($module_type_code!='group') {

	$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET parent_key='$move_to_key',group_key='$group_key', block_key='0' where link_key='$link_key'";
	$CONN->Execute($sql);
	update_children($link_key,$group_key);


} else {

	$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET parent_key='$move_to_key' where link_key='$link_key'";

	$CONN->Execute($sql);

}

$message = urlencode(sprintf($module_strings['move_success'], $general_strings['module_text']));
header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
exit;

function update_children($link_key,$group_key)
{
	global $CONN, $CONFIG;
	$sql = "SELECT link_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE parent_key='$link_key'";
	$rs = $CONN->Execute($sql);
	while (!$rs->EOF) {

		$link_key2 = $rs->fields[0];
		$sql2 = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET group_key='$group_key' WHERE link_key='$link_key2'";
		$CONN->Execute($sql2);
		update_children($link_key2,$group_key);
		$rs->MoveNext();
	
	}
	
	$rs->Close();

}

function check_is_below($move_to_key,$link_key) 
{
	
	global $CONN, $is_below, $CONFIG;
	$sql = "SELECT parent_key FROM {$CONFIG['DB_PREFIX']}module_space_links where link_key='$move_to_key'";

	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {
	
		$parent_key2 = $rs->fields[0];
		
		if ($parent_key2==$link_key) {

			$is_below = true;
			return;

		} else {

			if ($parent_key2=='0') {
  
				return;

			} else {
			
				check_is_below($parent_key2,$link_key);
			
			}
		
		}
	
	$rs->MoveNext();
	
	}

	$rs->Close();
}
function check_space_below($space_to_move_key, $move_to_space_key) 
{
	global $CONN, $is_below, $CONFIG;
	

	$parent_space_key = $CONN->GetOne("SELECT {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  {$CONFIG['DB_PREFIX']}spaces.space_key='$move_to_space_key'");

	if ($parent_space_key==$space_to_move_key) {
		$is_below = true;
		return;
	}
	if ($parent_space_key==$CONFIG['DEFAULT_SPACE_KEY']) {
		return;
	} else {
		check_space_below($space_to_move_key,$parent_space_key);
	}
	
}
?>
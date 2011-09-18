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
* Makes a user a member of a space
*
* Makes a user a member of a space if they select the 'Add to my homepage link'
* on a spaces homepage
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: makemember.php,v 1.11 2007/01/04 22:09:18 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');
$space_key = $_GET['space_key'];
$action = isset($_GET['action'])? $_GET['action']: '';
$referer = isset($_GET['referer'])? $_GET['referer']: '';

$current_user_key = $_SESSION['current_user_key'];
//check to see if user is logged in. If not refer to Login page.
authenticate();	 

if (!isset($action) || $action=='') {

	$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES ('$space_key','$current_user_key','2', $date_added)");
	
	$new_user_alert = $CONN->GetOne("SELECT new_user_alert FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'");
	
	if ($new_user_alert=='true') {
		$rs = $CONN->Execute("SELECT first_name, last_name, details FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$current_user_key'");
		$name = $rs->fields[0].' '.$rs->fields[1];
		$details = ereg_replace( 10, "\n", $rs->fields[2]);
		$rs->Close();
		$member_keys = array();
		$n = 0;
			
		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links,{$CONFIG['DB_PREFIX']}spaces WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND ({$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1' AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key')";

		$rs = $CONN->Execute($sql);
			
		while (!$rs->EOF) {
			
			$member_keys[$n] = $rs->fields[0];
			$space_name = $rs->fields[1];
			$n++;
			$rs->MoveNext();
				
		}
			
		$mailbody = sprintf($space_strings['new_user_alert'], $name, $space_name, $general_strings['space_text']);
		$mailbody .= "\n\n";
		$mailbody .= sprintf($space_strings['details'], $name);
				
		if ($details!='') {
					
			$mailbody = $mailbody."\n$details";
				
		} else {
			   
			$mailbody = $mailbody."\n".$space_strings['no_details'];
				
		}
			
		require_once('../includes/email.inc.php');
		$subject = sprintf($space_strings['new_member'], $space_name);
		email_users($subject, $mailbody, $member_keys, '', '', '');
	}

} else {

	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE user_key='$current_user_key' AND space_key='$space_key'");
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}");
	exit;

}

statistics('read'); 
header("Location: {$CONFIG['FULL_URL']}/spaces/welcome.php?space_key=$space_key");
exit;
?>
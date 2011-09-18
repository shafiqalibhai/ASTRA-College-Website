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
* Edit members of a space
*
* Removes a member from a space, or promotes or demotes them to/from
* a space administrator
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: memberedit.php,v 1.21 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/messaging_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {

   	$space_key	= $_GET['space_key'];
	$user_key	= $_GET['user_key'];
	$action		= $_GET['action'];
	$access_level_string=$_GET['access_level'];
} else {

	$space_key	= $_POST['space_key'];
	$user_key	= $_POST['user_key'];
	$user_keys	= $_POST['user_keys'];
	$user_message	= $_POST['user_message'];
	$user_list	= $_POST['user_list'];
	$submit	= $_POST['submit'];
	$file	   = $_FILES['file'];
	$action	 = $_POST['action'];
	$access_level_string=$_POST['access_level'];
}
//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];	 

if (isset($submit) && $submit==$messaging_strings['send']) {

	if (isset($user_keys) && is_array($user_keys)) {
		require_once($CONFIG['BASE_PATH'].'/messaging/lib.inc.php');
		$messagingObj = new InteractMessaging();
		foreach($user_keys as $user_key) {
			$messagingObj->addMessage($user_key, $user_message,$_SESSION['current_user_key'], time());
		}
	}	
	$message = urlencode($messaging_strings['message_added']);
	header("Location: ".$CONFIG['FULL_URL'].'/spaces/members.php?space_key='.$space_key.'&message='.$message.'&online_only=1');
	exit;	
}
authenticate_admins($level='space_only');
if ($action) {
	$message='';
	$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
	
	if($access_level_string=='delete') {
		$action='delete';

		if (isset($user_keys) && is_array($user_keys)) {
			foreach($user_keys as $user_key) {
				$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$user_key'";
				$CONN->Execute($sql);
				$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE space_key='$space_key' AND user_key='$user_key'";
				$CONN->Execute($sql);
			}
		} else if (isset($user_key)) {
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$user_key'";
			$CONN->Execute($sql);
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE space_key='$space_key' AND user_key='$user_key'";
			$CONN->Execute($sql);
		}
		header ("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key");
		exit;
	}

	$access_keys=array('promote'=>1,'demote'=>2,'make_inv_admin'=>3,'make_inv_member'=>4);
	
	$new_access_level=$access_keys[$access_level_string];

	switch($action) {
		case 'action_members';
			if (isset($user_keys) && is_array($user_keys)) {
				foreach($user_keys as $user_key) {	
					//see if they are a member already
					if ($CONN->GetOne("SELECT user_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE user_key='$user_key' AND space_key='$space_key'")){
						
						$sql = "UPDATE {$CONFIG['DB_PREFIX']}space_user_links SET access_level_key='$new_access_level' WHERE space_key='$space_key' AND user_key='$user_key'";
					} else {
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES('$space_key','$user_key','$new_access_level', $date_added)";
					}
					$CONN->Execute($sql);
				}
			}
			header ("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key");
			exit;
		
		break;
		
		case add_by_number:
	
			if (isset($user_list)) {
		
				$user_ids_array=explode(',',$user_list);
				$count=count($user_ids_array);
			
				for ($i=0; $i<$count; $i++) {
			   
					$user_id=trim($user_ids_array[$i]);
			   
					$check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE user_id_number='$user_id'";
			   
					$check_user_rs = $CONN->Execute($check_user_sql);
					
					if (!$check_user_rs->EOF) {
			   
						while (!$check_user_rs->EOF) {
				   
							$user_key = $check_user_rs->fields[0];
							$check_user_rs->MoveNext();
					   
						}
			   
						
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES('$space_key','$user_key','$new_access_level', $date_added)";
						if(!($CONN->Execute($sql))) {
							$message.=$space_strings['member_already'].': '.$user_id.'<br />';}
				   } else {$message.=$user_strings['userID_not_exist'].': '.$user_id.'<br />';}
			
				}
		
			}
			
			header ("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key".(strlen($message)?'&message='.$message:''));
			exit;
		
		break;

		case add_by_username:
	
			if (isset($user_list)) {
		
				$usernames_array=explode(',',$user_list);
				$count=count($usernames_array);
			
				for ($i=0; $i<$count; $i++) {
			   
					$username=trim($usernames_array[$i]);
			   
					$check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='$username'";
			   
					$check_user_rs = $CONN->Execute($check_user_sql);
			   
					if (!$check_user_rs->EOF) {
			   
						while (!$check_user_rs->EOF) {
				   
							$user_key = $check_user_rs->fields[0];
							$check_user_rs->MoveNext();
					   
						}
			   
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES('$space_key','$user_key','$new_access_level', $date_added)";
						
						if(!($CONN->Execute($sql))) {
							$message.=$space_strings['member_already'].': '.$username.'<br />';}
				   } else {$message.=$user_strings['username_not_exist'].': '.$username.'<br />';}
			
				}
		
			}
		
			header ("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key".(strlen($message)?'&message='.$message:''));
			exit;
			
		break;

		case upload_by_username:

			if ($file && $file!='none') {
		
				$fcontents = file ($file['tmp_name']);
			
				while (list ($line_num, $line) = each ($fcontents)) {
			   
					$username=trim($line);
			   
					$check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='$username'";
			   
					$check_user_rs = $CONN->Execute($check_user_sql);
			   
					if (!$check_user_rs->EOF) {
			   
						while (!$check_user_rs->EOF) {
				   
							$user_key = $check_user_rs->fields[0];
							$check_user_rs->MoveNext();
					   
						}
			   
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES('$space_key','$user_key','2', $date_added)";
						$CONN->Execute($sql);
						
									  
					}
					
				}
			
			}
		
 			header("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key");
			exit;
					
		break;

		case upload_by_number:

			if ($file && $file!='none') {
		
				$fcontents = file ($file['tmp_name']);
			
				while (list ($line_num, $line) = each ($fcontents)) {
			   
					$user_id=trim($line);
			   
					$check_user_sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE user_id_number='$user_id'";
			   
					$check_user_rs = $CONN->Execute($check_user_sql);
			   
					if (!$check_user_rs->EOF) {
			   
						while (!$check_user_rs->EOF) {
				   
							$user_key = $check_user_rs->fields[0];
							$check_user_rs->MoveNext();
					   
						}
			   
						$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES('$space_key','$user_key','2', $date_added)";
						$CONN->Execute($sql);
			   
				   }
			
				}
				
			}
			   
 			header("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key");
			exit;
		
		break;		
	
	}

}
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'		   => 'header.ihtml',
	'navigation'	   => 'navigation.ihtml',
	'body'			 => 'spaces/memberedit.ihtml',
	'footer'		   => 'footer.ihtml'

));
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$breadcrumbs = '<a href="'.$CONFIG['PATH']."/spaces/space.php?space_key=$space_key\" class=\"spaceHeadinglink\">".$page_details['space_name']."</a> &raquo; <a href=\"members.php?space_key=$space_key\">".$group_strings['members2'].'</a> &raquo; ';
$t->set_var('BREADCRUMBS',$breadcrumbs);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('ADD_STRING',$general_strings['add']);
$t->set_var('ADD_MEMBERS_STRING',$group_strings['add_members']);
$t->set_var('ADD_BY_USERNAME',$group_strings['add_by_username']);
$t->set_var('ADD_BY_NUMBER',$group_strings['add_by_number']);

if (!isset($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}

$t->set_var('PRIV_MENU',$objHtml->arrayToMenu(array('promote'=>$space_strings['promote'],'demote'=>$space_strings['demote'], 'make_inv_admin'=>$space_strings['make_inv_admin'],'make_inv_member'=>$space_strings['make_inv_member']),'access_level','demote',false,'1',false));

$t->set_var('UPLOAD_BY_USERNAME',$group_strings['upload_by_username']);
$t->set_var('UPLOAD_BY_NUMBER',$group_strings['upload_by_number']);
$t->set_var('BY_NAME_NUMBER',$group_strings['by_name_number']);
$t->set_var('OR_STRING',$general_strings['or']);
$t->set_var('USERNAME_INSTRUCTIONS',$group_strings['username_instructions']);
$t->set_var('UPLOAD_MEMBERS',$group_strings['upload_members']);
$t->set_var('ADD_MEMBER_HEADING',$space_strings['add_members']);
$t->set_var('UPLOAD_INSTRUCTIONS',$group_strings['upload_instructions']);
$t->parse('CONTENTS', 'header', true);
get_navigation();

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;
?>

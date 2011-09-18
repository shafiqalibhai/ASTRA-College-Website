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
* Group module
*
* Displays details of any new members in a group 
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: newmembers.php,v 1.10 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
//now find out access level of group and if user is allowed in

if (!class_exists(InteractGroup)) {
			
	require_once('lib.inc.php');
				
}
			
if (!is_object($groupObject)) {
			
	$groupObject = new InteractGroup();
			
}
$group_data	  = $groupObject->getGroupData($group_key);
$membership_data = $groupObject->checkMembership($group_key,$_SESSION['current_user_key']);

if ($group_data['access_key']!=2 && $membership_data===false && $is_admin==false && $group_accesslevel!=1) {

   echo "You do not appear to be a member!";
   exit;

}

if (!isset($_SESSION['group_'.$group_key.'_last_access'])) {

	$new_members = $groupObject->countNewMembers($group_key, $_SESSION['current_user_key']);
	
}

//update statistics 
statistics('read');

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'members'		   => 'groups/newmembers.ihtml',
	'footer'		  => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true); 

$t->set_var('TITLE','');
$t->set_var('HEADING_STRING',$general_strings['new_members']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('BACK_LINK','group.php');
$t->set_var('MODULE_KEY',$group_key);
$t->set_var('GROUP_KEY',$group_key);

get_navigation();

//get any members details
$sql = "SELECT first_name, last_name,email,details,file_path,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.date_added>{$_SESSION['group_'.$group_key.'_last_access']} AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key' ORDER BY last_name";
$rs = $CONN->Execute($sql);

$t->set_block('members', 'MembersBlock', 'MBlock');

while (!$rs->EOF) {
	
	$name = $rs->fields[0].' '.$rs->fields[1];
	$email = $rs->fields[2];
	if (!eregi('(<p|<br)', $rs->fields[3])) {

		$details=nl2br($rs->fields[3]);
	
	} else {
	
		$details=$rs->fields[3];
	
	}
	
	$file_path = $rs->fields[4];
	$user_key = $rs->fields[5];
	$email_username=urlencode($name);
	$t->set_var('EMAIL_USER_NAME',$email_username);
	$t->set_var('FULL_NAME',$name);
	
	if ($CONFIG['SHOW_EMAILS']==1) {
			
		$t->set_var('EMAIL',$email);
				
	} else {
			
		$t->set_var('EMAIL',$general_strings['email']);
				
	}
	
	$t->set_var('DETAILS',$details);
	$t->set_var('USER_KEY',$user_key);

	//if we have a photo display it.
	$photo = $CONFIG['USERS_PATH'].'/'.$file_path.'/'.$user_key.'.jpg'; 
	$photo_gif = $CONFIG['USERS_PATH'].'/'.$file_path.'/'.$user_key.'.gif';
	$photopath = $CONFIG['USERS_VIEW_PATH'].'/'.$file_path.'/'.$user_key.'.jpg';
	$photopath_gif = $CONFIG['USERS_VIEW_PATH'].'/'.$file_path.'/'.$user_key.'.gif';

	if (is_file($photo)) {

		$photo_tag="<img src='$photopath'>";
		$t->set_var('PHOTO',$photo_tag);

	} else if (is_file($photo_gif)){
	
		$photo_tag="<img src='$photopath_gif'>";
		$t->set_var('PHOTO',$photo_tag);

	} else {

		$t->set_var('PHOTO','');

	}
	$t->parse('MBlock', 'MembersBlock', true);
	$rs->MoveNext();
		
}   
$rs->Close();


$t->parse('CONTENTS', 'members', true); 
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
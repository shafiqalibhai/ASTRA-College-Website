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
* Group members
*
* Displays a list of group members
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: members.php,v 1.19 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

//set variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];

} else {

	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];

}
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables

check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$can_edit_module = check_module_edit_rights($module_key);

if (!class_exists(InteractGroup)) {
			
	require_once('lib.inc.php');
				
}
			
if (!is_object($groupObject)) {
			
	$groupObject = new InteractGroup();
			
}
$group_data	  = $groupObject->getGroupData($group_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'memberadd'	   => 'groups/memberadd.ihtml',
	'membertable'	 => 'groups/membertable.ihtml',
	'members'		 => 'groups/members.ihtml',
	'footer'		  => 'footer.ihtml'
));

$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('ADMIN_NOTE',$group_strings['admin_note']);

//get group name for breadcrumbs
$sql = "SELECT name FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$group_key'";
$rs = $CONN->Execute($sql);

while (!$rs->EOF) {
	
	$name = $rs->fields[0];
	$rs->MoveNext();

}

$rs->Close();

$breadcrumbs = "<a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\">Home</a> -> <a href=\"{$CONFIG['PATH']}/modules/group/group.php?space_key=$space_key&module_key=$group_key&link_key=$link_key\">$name</a> -> <b>".$group_strings['group_members'].'</b>';


$t->set_var('BREADCRUMBS',$breadcrumbs);
$t->parse('CONTENTS', 'header', true); 
$t->set_var('TITLE','');

//get any members for this group

$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}group_user_links.user_key, first_name, last_name, email, access_level_key, {$CONFIG['DB_PREFIX']}group_user_links.date_added FROM {$CONFIG['DB_PREFIX']}group_user_links LEFT JOIN {$CONFIG['DB_PREFIX']}users ON {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key WHERE {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key' ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	


$rs = $CONN->Execute($sql);
$n=0;
while (!$rs->EOF) {

	$no_account='';
	$name='';
	$user_key = $rs->fields[0];
	
	if (!isset($rs->fields[1])) {
	 
		$no_account = sprintf($group_strings['no_account'],$CONFIG['SERVER_NAME'],$user_key);;
	
	} else {
			 
		$name = $rs->fields[1].' '.$rs->fields[2];
		
	}
	
	if (check_space_membership($space_key,$user_key)!=true) {
		
		$name = "<span class=\"red\">X</span>".$name;
		
	}
	
	$t->set_var('MEMBER_SINCE',$date_added);
	$email = $rs->fields[3];
	$group_accesslevel_key = $rs->fields[4];
	$t->set_var('USER_KEY',$user_key);
	$t->set_var('NO_ACCOUNT',$no_account);
	$t->set_var('NAME',$name);
	$t->set_var('EMAIL_USER_NAME',urlencode($name));	
	if ($CONFIG['SHOW_EMAILS']==1) {
			
		$t->set_var('EMAIL',$email);
				
	} else {
			
		$t->set_var('EMAIL',$general_strings['email']);
				
	}
		
	if ($can_edit_module==true) {
	
		$delete_warning = $group_strings['delete_warning'];
		$member_admin="<a href=\"{$CONFIG['PATH']}/modules/group/memberedit.php?space_key=$space_key&module_key=$group_key&group_key=$group_key&user_key=$user_key&action=delete\" onClick=\"return confirmDelete('$delete_warning')\">".$group_strings['remove']."</a>";
		
	}
	
			
	if ($group_accesslevel_key=='1') {

		if ($can_edit_module==true) {
		
			$demote_warning = $group_strings['demote_warning'];
			$member_admin = $member_admin." | <a href=\"{$CONFIG['PATH']}/modules/group/memberedit.php?space_key=$space_key&module_key=$group_key&group_key=$group_key&user_key=$user_key&action=demote\" onClick=\"return confirmDelete('$demote_warning')\">Demote</a>"; 
		 
		} else {
		 
			 $members_admin = '';
			 
		}  

		$t->set_var('MEMBER_ADMIN',$member_admin);
		$t->parse('GROUP_LEADERS', 'members', true);

	} else {
		
		if ($can_edit_module==true) {
		
			$promote_warning = $group_strings['promote_warning'];
			$member_admin = $member_admin." | <a href=\"{$CONFIG['PATH']}/modules/group/memberedit.php?space_key=$space_key&module_key=$group_key&group_key=$group_key&user_key=$user_key&action=promote\" onClick=\"return confirmDelete('$promote_warning')\">Promote</a>";
				  
		} else {
		
			if ($user_key==$_SESSION['current_user_key'] && $group_data['access_key']!=1) {
			
				$delete_warning = $general_strings['check'];
				$member_admin="<a href=\"{$CONFIG['PATH']}/modules/group/memberedit.php?space_key=$space_key&module_key=$group_key&group_key=$group_key&user_key=$user_key&action=delete\" onClick=\"return confirmDelete('$delete_warning')\">".$group_strings['remove_me']."</a>";			
			
			} else {
			
				$member_admin='';
				
			}
			
		}
		
		$n++;
		$t->set_var('MEMBER_ADMIN',$member_admin);	  
		$t->parse('MEMBERS', 'members', true);	
			 
	}
		
	$rs->MoveNext();

}


$rs->Close();
$t->set_var('NUMBER_OF_MEMBERS',sprintf($group_strings['number_of_members'], $n));
get_navigation();

if ($can_edit_module==true) {
	
	$users_menu = create_user_menu($group_key,$space_key);
	$t->set_var('MEMBERS_MENU',$users_menu);
	$t->set_var('GROUP_KEY',$group_key);
	$t->set_var('ADD_STRING',$general_strings['add']);
	$t->set_var('ADD_MEMBERS_STRING',$group_strings['add_members']);
	$t->set_var('ADD_BY_USERNAME',$group_strings['add_by_username']);
	$t->set_var('ADD_BY_NUMBER',$group_strings['add_by_number']);
	$t->set_var('UPLOAD_BY_USERNAME',$group_strings['upload_by_username']);
	$t->set_var('UPLOAD_BY_NUMBER',$group_strings['upload_by_number']);
	$t->set_var('BY_NAME_NUMBER',$group_strings['by_name_number']);
	$t->set_var('OR_STRING',$general_strings['or']);
	$t->set_var('SELECT_MEMBERS',$group_strings['select_members']);
	$t->set_var('USERNAME_INSTRUCTIONS',$group_strings['username_instructions']);
	$t->set_var('UPLOAD_MEMBERS',$group_strings['upload_members']);
	$t->set_var('UPLOAD_INSTRUCTIONS',$group_strings['upload_instructions']);			
	$t->parse('ADD_MEMBERS', 'memberadd', true);
	
}

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('LEADERS_STRING',$group_strings['group_leaders']);
$t->set_var('MEMBERS_STRING',$group_strings['group_members']);
$t->parse('CONTENTS', 'membertable', true);


$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Create a select menu of users not already in the selected group 
* 
* @param [group_key] array of categories
* @param [space_key] name of the menu
* @return html code for select menu
*/
function create_user_menu($group_key,$space_key)
{
	global $CONN, $group_key, $CONFIG;

	//get an array of users already in the group
	$group_users=array();
	$n=1;
	$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key='$group_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$user_key = $rs->fields[0];
		$group_users[$n]=$user_key;
		$n++;
		$rs->MoveNext();
	
	}

	//check to see if this is a sub group

	$sql = "SELECT parent_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$group_key' AND status_key!='4'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$parent_key = $rs->fields[0];
		$rs->MoveNext();
	
	}

	$sql = "SELECT group_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$parent_group_key = $rs->fields[0];
		$rs->MoveNext();
	
	}	

	//create a menu of space users not already in this group
 
	if ($parent_group_key==0|$parent_group_key=='') {
	
		$sql = "SELECT last_name, first_name, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND (space_key = '$space_key') ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	
	} else {
	
		$sql = "SELECT last_name, first_name, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND (group_key = '$parent_group_key') ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	
	}

	$rs = $CONN->Execute($sql);
	$menu = "<select size=\"10\" name=\"user_key[]\" multiple>";
	
	while (!$rs->EOF) {
	
		$name = $rs->fields[0].' '.$rs->fields[1];
		$user_key = $rs->fields[2];

		if (!in_array($user_key,$group_users)) {
	
			$menu = $menu."<option value=\"$user_key\">$name</option>";
	
		}
	
		$rs->MoveNext();
	
	}
   
   $menu = $menu."</select>";
   
   return $menu;

} //end create_user_menu

function check_space_membership($space_key,$user_key) {

	global $CONN, $CONFIG;

	$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE user_key='$user_key' AND space_key='$space_key'");	

	if (!$rs->EOF) {

		return true;
		
	} else {
	
		return false;
		
	}

	$rs->Close();	
	
}
?>
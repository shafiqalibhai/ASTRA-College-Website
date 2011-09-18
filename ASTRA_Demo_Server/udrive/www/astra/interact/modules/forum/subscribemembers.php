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
* Forum home page
*
* Display the start page for the forum module 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: subscribemembers.php,v 1.2 2007/07/19 00:29:32 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
//get language strings

if(!empty($_POST)) {

	$module_key = $_POST['module_key'];
	$space_key = $_POST['space_key'];
	$member_keys = $_POST['member_keys'];
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}module_subscription_links WHERE module_key='$module_key'");
	if(is_array($member_keys)) {
		foreach($member_keys as $user_key) {
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}module_subscription_links(module_key, user_key, type_key) VALUES ('$module_key','$user_key','1')");

		}
	} 

	header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=Subscriptions+updated");
	exit;
}
require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$current_user_key	= $_SESSION['current_user_key'];


//check we have the required variables
check_variables(true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);


require_once('lib.inc.php');
$forum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_strings);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'				=> 'forums/subscribemembers.ihtml',
	'footer'		  => 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->parse('CONTENTS', 'header', true);
get_navigation();
$group_key = $CONN->GetOne("SELECT group_key FROM interact_module_space_links WHERE link_key='$link_key'");
$subscribed_members_array = array();
$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}module_subscription_links WHERE module_key='$module_key'");
while(!$rs->EOF) {
	array_push($subscribed_members_array, $rs->fields[0]);
	$rs->MoveNext();
}
$rs->Close();
$concat = $CONN->Concat('last_name', '\', \'','first_name');
if (!empty($group_key)) {
	$members_sql="SELECT $concat,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key and {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key' AND {$CONFIG['DB_PREFIX']}users.account_status='1' ORDER BY last_name";
} else {
	$members_sql="SELECT $concat,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key and {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}users.account_status='1' ORDER BY last_name";
	
}
$t->set_var('SUBSCRIBED_MEMBERS_MENU', make_menu($members_sql,'member_keys[]',$subscribed_members_array,'8','true'));
$t->set_var('MODULE_KEY', $module_key);
$t->set_var('SPACE_KEY', $space_key);
$t->set_var('SUBSCRIBED_MEMBERS_MENU', make_menu($members_sql,'member_keys[]',$subscribed_members_array,'8','true'));
$t->set_strings('form',  $forum_strings);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

?>
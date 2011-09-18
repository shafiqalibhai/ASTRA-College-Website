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
* @version $Id: forum.php,v 1.34 2007/07/26 22:10:52 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('forum.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$archived	= $_GET['archived'];
$message	= $_GET['message'];
$expand_post	= $_GET['expand_post'];
$collapse_post  = $_GET['collapse_post'];
$offset		 = $_GET['offset'];
$display		= $_GET['display'];
$expand_all	 = $_GET['expand_all'];
$current_user_key	= $_SESSION['current_user_key'];



//check we have the required variables
check_variables(true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

//update statistics 
if (!$message) {

	statistics('read');

}

require_once('lib.inc.php');
$forum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_strings);
$module_data = $forum->getForumStats();
if (!isset($objUser) || !is_object($objUser)) {
	if (!class_exists('InteractUser')) {
		require_once('../../includes/lib/user.inc.php');
	}
	$objUser = new InteractUser();
}

$total_threads = $forum->getTotalThreads($module_key);
$post_statuses = $forum->getPostStatusArray($module_key, $current_user_key);
$forum_data = array('archived'		 => $archived,
					'readposts_array'  => $readposts_array,
					'expand_post'	  => $expand_post,
					'expand_all'	   => $expand_all,
					'collapse_post'	=> $collapse_post,
					'offset'		   => $offset,
					'total_threads'	=> $total_threads,
					'post_statuses'	=> $post_statuses,
					'number_to_display'=> $number_to_display);



//get the required template files
if ($is_admin==true) {
	
	$table = 'stafflisttable.ihtml';
	$listposts = 'stafflistposts.ihtml';

} else  {

	$table = 'listtable.ihtml';
	$listposts = 'listposts.ihtml'; 

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'table'		   => 'forums/'.$table,
	'listposts'	   => 'forums/'.$listposts,
	'footer'		  => 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);


$t->parse('CONTENTS', 'header', true);
$t->set_var('NAME',$name);

//see if user subscribed
if (isset($current_user_key) && $current_user_key!='') {
	if ($objUser->isSubscribed($module_key,$current_user_key)) {
		$t->set_var('SUBSCRIBE_LINK','<a href="../general/subscribe.php?space_key='.$space_key.'&module_key='.$module_key.'&action=unsubscribe&referer=modules/forum/forum.php">'.$general_strings['unsubscribe'].'</a>');
	} else {
		$t->set_var('SUBSCRIBE_LINK','<a href="../general/subscribe.php?space_key='.$space_key.'&module_key='.$module_key.'&action=subscribe&referer=modules/forum/forum.php">'.$general_strings['subscribe'].'</a>');
	}
} else {
	$t->set_var('SUBSCRIBE_LINK','');
}

$forum_data = $forum->getThreads($module_key,'0',$space,$forum_data);



if ($forum_data['offset']>=$forum_data['display']) {

	$previous_offset = $forum_data['offset']-$forum_data['display'];
	$t->set_var('PREVIOUS_LINK',"<a href=\"{$CONFIG['PATH']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&offset=$previous_offset\">{$general_strings['previous']}</a>");

}

$start_thread = $forum_data['offset']+1;


$new_offset = $forum_data['offset']+$forum_data['display'];

if ($forum_data['total_threads']>$forum_data['display'] && $forum_data['total_threads']!=$new_offset && $forum_data['total_threads']>$new_offset) {


	$end_thread = $forum_data['offset']+$forum_data['display'];   

	$t->set_var('NEXT_LINK',"<a href=\"{$CONFIG['PATH']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&offset=$new_offset\">{$general_strings['next']}</a>");

} else {

	$end_thread = $forum_data['total_threads'];

}

$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('THREAD_NUMBERS_STRING',sprintf($forum_strings['thread_numbers'], $start_thread, $end_thread, $forum_data['total_threads']));
$t->set_var('POST_MESSAGE_STRING',$forum_strings['post_message']);
$t->set_var('DISPLAY_STRING',$general_strings['display']);
$t->set_var('SEARCH_STRING',$general_strings['search']);
$t->set_var('DAYS_STRING',$general_strings['days']);
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var('DELETE_STRING',$general_strings['delete']);
$t->set_var('POSTS_STRING',$forum_strings['posts']);
$t->set_var('SUBJECT_STRING',$forum_strings['subject']);
$t->set_var('AUTHOR_STRING',$forum_strings['author']);
$t->set_var('ACTION_SELECTED_STRING',$forum_strings['action_selected']);
$t->set_var('FLAG_READ_STRING',$forum_strings['flag_as_read']);
$t->set_var('FLAG_NOT_READ_STRING',$forum_strings['flag_as_not_read']);
$t->set_var('PRINT_SAVE_STRING',$forum_strings['print_save']);
$t->set_var('BY_THREAD_STRING',$forum_strings['by_thread']);
$t->set_var('BY_NAME_STRING',$forum_strings['by_name']);
$t->set_var('MOVE_STRING',$forum_strings['move']);
$t->set_var('MOVE_TO_STRING',$forum_strings['move_to']);
$t->set_var('VIEW_SPREADSHEET_STRING',$forum_strings['view_spreadsheet']);
$t->set_var('ADMIN_NOTE_STRING',$forum_strings['admin_note']);
$t->set_var('AUTOPROMPTING_ON_STRING',$forum_strings['autoprompting_on']);
$t->set_var('AUTOPROMPTING_OFF_STRING',$forum_strings['autoprompting_off']);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);
$t->set_var('ALL_STRING',$general_strings['all']);
$t->set_var('READ_STRING',$forum_strings['read']);
$t->set_var('NOT_READ_STRING',$forum_strings['not_read']);
$t->set_var('FOLLOW_UP_STRING',$forum_strings['follow_up']);
$t->set_var('FINISHED_STRING',$forum_strings['finished']);
$t->set_var('REMOVE_FLAGS_STRING',$forum_strings['remove_flags']);
$t->set_var('MONITOR_POST_STRING',$forum_strings['monitor_post']);
$t->set_var('REMOVE_MONITOR_POST_STRING',$forum_strings['remove_monitor_post']);
$t->set_var('OFFSET',$offset);
$t->set_var('GO_STRING',$general_strings['go']);


if ($expand_all==1) {

	$t->set_var('EXPAND_ALL','2');
	$t->set_var('EXPAND_ALL_STRING',$forum_strings['collapse_all']);

} else {

	$t->set_var('EXPAND_ALL','1');
	$t->set_var('EXPAND_ALL_STRING',$forum_strings['expand_all']);

}
get_navigation();

if ($is_admin==1) {
	$t->set_var('ADMIN_IMAGE',get_admin_tool("forum_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key&parent_key=$parent_key&action=modify"));
	
}

$t->set_strings('table',  $forum_strings, $module_data);
$t->parse('CONTENTS', 'table', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

?>
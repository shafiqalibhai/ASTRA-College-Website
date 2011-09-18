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
* Embed forum
*
* Displays forum posts within a folder
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: embedforum.php,v 1.16 2007/07/26 22:10:52 glendavies Exp $
* 
*/

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
require_once($CONFIG['BASE_PATH'].'/modules/forum/forum.inc.php');

//see if user is admin for this forum

$is_admin = check_module_edit_rights($module_key2);


if ($is_admin==true) {
	
	$table = 'stafflisttable.ihtml';
	$listposts = 'stafflistposts.ihtml';

} else  {

	$table = 'listtable.ihtml';
	$listposts = 'listposts.ihtml'; 

}

$t->set_file(array(
	'table'		   => 'forums/'.$table,
	'listposts'	   => 'forums/'.$listposts,
));
//see if user subscribed
require_once('../../includes/lib/user.inc.php');
$objUser = new InteractUser();
if (isset($_SESSION['current_user_key']) && $_SESSION['current_user_key']!='') {
	if ($objUser->isSubscribed($module_key,$current_user_key)) {
		$t->set_var('SUBSCRIBE_LINK','<a href="../general/subscribe.php?space_key='.$space_key.'&module_key='.$module_key.'&action=unsubscribe&referer=modules/forum/forum.php">'.$general_strings['unsubscribe'].'</a>');
	} else {
		$t->set_var('SUBSCRIBE_LINK','<a href="../general/subscribe.php?space_key='.$space_key.'&module_key='.$module_key.'&action=subscribe&referer=modules/forum/forum.php">'.$general_strings['subscribe'].'</a>');
	}
} else {
	$t->set_var('SUBSCRIBE_LINK','');
}


$t->set_var('NAME',$name);
require_once($CONFIG['BASE_PATH'].'/modules/forum/lib.inc.php');
$forum = new InteractForum($space_key, $module_key2, $group_key, $is_admin, $forum_strings);
$total_threads = $forum->getTotalThreads($module_key2);
$post_statuses = $forum->getPostStatusArray($module_key2, $_SESSION['current_user_key']);

$module_data = $forum->getForumStats();

$forum_data = array('archived'		 => $archived,
					'readposts_array'  => $readposts_array,
					'expand_post'	  => $expand_post,
					'collapse_post'	=> $collapse_post,
					'offset'		   => $offset,
					'total_threads'	=> $total_threads,
					'post_statuses'	=> $post_statuses,
					'number_to_display'=> $number_to_display);
$forum_data = $forum->getThreads($module_key2,'0',$space,$forum_data);
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
$t->set_var('MODULE_KEY',$module_key2);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('POST_MESSAGE_STRING',$forum_strings['post_message']);
$t->set_var('DISPLAY_STRING',$general_strings['display']);
$t->set_var('GO_STRING',$general_strings['go']);
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
$t->set_var('ARCHIVE_STRING',$forum_strings['archive']);
$t->set_var('UNARCHIVE_STRING',$forum_strings['un_archive']);
$t->set_var('AUTOPROMPTING_ON_STRING',$forum_strings['autoprompting_on']);
$t->set_var('AUTOPROMPTING_OFF_STRING',$forum_strings['autoprompting_off']);
$t->set_var('ALL_STRING',$general_strings['all']);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);
$t->set_var('SEARCH_STRING',$general_strings['search']);
$t->set_var('READ_STRING',$forum_strings['read']);
$t->set_var('NOT_READ_STRING',$forum_strings['not_read']);
$t->set_var('FOLLOW_UP_STRING',$forum_strings['follow_up']);
$t->set_var('FINISHED_STRING',$forum_strings['finished']);
$t->set_var('REMOVE_FLAGS_STRING',$forum_strings['remove_flags']);
$t->set_strings('table',  $forum_strings, $module_data);
$t->parse('CONTENTS', 'table', true);	

?>
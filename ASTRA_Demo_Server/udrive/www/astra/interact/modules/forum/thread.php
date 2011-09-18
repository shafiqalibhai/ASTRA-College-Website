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
* Display selected thread
*
* Displays a thread of posts
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: thread.php,v 1.37 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('forum.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

$space_key 	= get_space_key();
$module_key	= isset($_GET['module_key'])?$_GET['module_key']:'';
$thread_key	= isset($_GET['thread_key'])?$_GET['thread_key']:'';
$post_key   = $_GET['post_key'];
$link_key 	= get_link_key($module_key,$space_key);
$current_user_key = $_SESSION['current_user_key'];

if ($module_key=='') {
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key, {$CONFIG['DB_PREFIX']}posts.thread_key FROM {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND  {$CONFIG['DB_PREFIX']}posts.post_key='$post_key'");	
	while (!$rs->EOF) {
		$module_key  = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$rs->MoveNext();
	}
}
//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

//update statistics 
statistics('read');
require_once('lib.inc.php');
$forum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_strings);

//get array of read posts
$post_statuses = $forum->getPostStatusArray($module_key,$current_user_key);

//get required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'thread'		  => 'forums/fullthread.ihtml',
	'forumheader'	 => 'forums/forumheader.ihtml',
	'fullposts'	   => 'forums/showfullpost.ihtml',
	'footer'		  => 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
	$t->set_block('fullposts', 'EditPostBlock', 'EDPBlock');
//work out next and previous links
$previous_thread_key = $forum->getNextThread($thread_key, 'previous');
if ($previous_thread_key===false) {

	$t->set_block('thread', 'PreviousLinkBlock', 'PLBlock');
	$t->set_var('PLBlock','');
	
} else {
	
	$t->set_var('PREVIOUS_THREAD_KEY',$previous_thread_key);

}
$next_thread_key = $forum->getNextThread($thread_key, 'next');
if ($next_thread_key===false) {

	$t->set_block('thread', 'NextLinkBlock', 'NLBlock');
	$t->set_var('NLBlock','');
	
} else {
	
	$t->set_var('NEXT_THREAD_KEY',$next_thread_key);

}


$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('THREAD_KEY',$thread_key);
$t->set_var('PATH',$CONFIG['PATH']);
//$t->set_var('REFERER','forum/thread.php');
$t->set_var('ACTION_SELECTED_STRING',$forum_strings['action_selected']);
$t->set_var('FLAG_AS_READ_STRING',$forum_strings['flag_as_read']);
$t->set_var('FLAG_AS_NOT_READ_STRING',$forum_strings['flag_as_not_read']);
$t->set_var('PRINT_SAVE_STRING',$forum_strings['print_save']);
$t->set_var('POSTED_BY_STRING',$forum_strings['posted_by']);
$t->set_var('BACK_TO_STRING',$general_strings['back_to']);
$t->set_var('HOME_STRING',$general_strings['home']);
$t->set_var('SUBJECT_STRING',$general_strings['subject']);
$t->set_var('ON_STRING',$general_strings['on']);
$t->set_var('AT_STRING',$general_strings['at']);
$t->set_var('SELECT_STRING',$general_strings['select']);
$t->set_var('REPLY_STRING',$general_strings['reply']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('PREVIOUS_STRING',$general_strings['previous']);
$t->set_var('NEXT_STRING',$general_strings['next']);
$t->set_var('EDIT_POST_STRING',$forum_strings['edit_post']);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);
$t->set_var('SUBMIT_CHANGES_STRING',$forum_strings['submit_changes']);
$t->set_var('READ_STRING',$forum_strings['read']);
$t->set_var('NOT_READ_STRING',$forum_strings['not_read']);
$t->set_var('FOLLOW_UP_STRING',$forum_strings['follow_up']);
$t->set_var('FINISHED_STRING',$forum_strings['finished']);
$t->set_var('STATUS_STRING',$general_strings['status']);
$t->set_var('MONITOR_POST_STRING',$forum_strings['monitor_post']);
$print_link = " - <a href=\"print.php?space_key=$space_key&module_key=$module_key&sort_by=ThreadKey&thread_key=$thread_key&number=all\">".$general_strings['print_display'].'</a>';

$t->set_var('PRINT_LINK',$print_link);

$t->parse('CONTENTS', 'header', true);
if (!isset($objUser)) {
	if (!class_exists('InteractUser')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');
	}
	$objUsers = new InteractUser();
}



get_navigation();
$t->parse('CONTENTS', 'forumheader', true);
get_thread('0',$space);
$t->parse('CONTENTS', 'thread', true);	
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;
 
function get_thread($parent_key,$space, $indent='0')
{
	global $CONN, $t,$userlevel_key,$accesslevel_key,$space_key,$module_key,$post_key,$thread_key,$current_user_key,$group_accesslevel,$post_statuses, $general_strings, $forum_strings, $is_admin, $CONFIG, $forum, $objHtml, $objDates, $objUsers;
	
	$forum_settings = get_forum_settings($module_key);


	$sql = "SELECT post_key, thread_key, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.attachment, prefered_name FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND (thread_key='$thread_key' AND parent_key='$parent_key')ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added ASC";


	$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}

		$objDates = new InteractDate();

	}

	
	if (!class_exists('InteractHtml')) {
	
		require_once('../../includes/lib/html.inc.php');
	
	}
	$objHtml = new InteractHtml();

	while (!$rs->EOF) {
	
		$t->set_var('PHOTO','');
		$t->set_var('PHOTO_WIDTH','');
		$t->set_var('MONITOR_CHECKED','');
		$t->set_var('EDPBlock','');
		$post_key2 = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$type_key = $rs->fields[3];
		$subject = $rs->fields[4];
		$subject_url = urlencode($subject);
		$body = $objHtml->parseText($rs->fields[5]);
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short');		
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
		$unix_date_added = $CONN->UnixTimestamp($rs->fields[6]);
		$date_now = mktime();
		$editable_date = $date_now-1800;				
		$full_name = (!empty($rs->fields[15]))?$rs->fields[15]:$rs->fields[7].' '.$rs->fields[8];
		$email = $rs->fields[9];
		$user_key = $rs->fields[10];
		$file_path = $rs->fields[11];
		$photo = $rs->fields[12];
		$show_photo = $rs->fields[13];
		$attachment = $rs->fields[14];
		$attachment_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$forum_settings['file_path'].'/'.$attachment;
		if (is_file($attachment_path)){
		
			$attachment_view_path = $CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/forum/'.$forum_settings['file_path'].'/'.$attachment;			
			$t->set_var('VIEW_ATTACHMENT','<a href="'.$attachment_view_path.'">'.$forum_strings['view_attachment'].'</a>');
	
		} else {
		
			$t->set_var('VIEW_ATTACHMENT','');
		
		}
				
		
		if ($show_photo=='1') {
		
			$t->set_var('PHOTO',$objUsers->getUserPhotoTag($user_key, '60', $space_key));
			
		}
					
 		if ((isset($post_statuses['read'][$post_key2]) && $post_statuses['read'][$post_key2]==1) || $current_user_key==$user_key) {
		
			$t->set_var('READ_CHECKED','checked');
			$t->set_var('READ_TAG','span class="small"');
			
			if ($post_key2==$post_key) {
		
				$inner_cell_class='activeForumPostingReadInner';
				$outer_cell_class='activeForumPostingReadOuter';
	   
			} else {
	   
			   $inner_cell_class='ForumPostingReadInner';
			   $outer_cell_class='ForumPostingReadOuter';
	   
			}  	
			
		} else {
		
		
			if ($post_key2==$post_key) {
		
				$inner_cell_class='activeForumPostingInner';
				$outer_cell_class='activeForumPostingOuter';
					  
			} else {
	   
				$inner_cell_class='ForumPostingInner';
				$outer_cell_class='ForumPostingOuter';
	   
			}  
				
			$t->set_var('READ_TAG','strong');
		
		
		}			
			
		
		if (isset($post_statuses['flags'][$post_key2])) {
		
			switch ($post_statuses['flags'][$post_key2]) {
			
				case 1:
				
					$t->set_var('FLAG_CHECKED','checked');
					$t->set_var('STATUS_IMAGE','<img src="../../images/modules/forum/red_flag.gif">');					
					
				break;
				
				case 2:
				
					$t->set_var('FLAG_CHECKED','');
					$t->set_var('FINISHED_CHECKED','checked');
					$t->set_var('STATUS_IMAGE','<img src="../../images/modules/forum/white_flag.gif">');
								
				break;
				
				default:
				
					$t->set_var('STATUS_IMAGE','');
					$t->set_var('FLAG_CHECKED','');
					$t->set_var('STATUS_IMAGE','');
					$t->set_block('fullposts', 'FinishedBlock', 'FBlock');
					$t->set_var('FBlock','');
					
				break;
				
			}
			
			if (isset($post_statuses['monitor'][$post_key2])  && $post_statuses['monitor'][$post_key2]==1) {
			
				$t->set_var('MONITOR_CHECKED',checked);
			
			} else {
			
				$t->set_var('MONITOR_CHECKED','');
			
			}
	
		
		} else {
		
			$t->set_var('STATUS_IMAGE','');
			$t->set_var('FLAG_CHECKED','');
			$t->set_block('fullposts', 'FinishedBlock', 'FBlock');
			$t->set_var('FBlock','');
		
		}
		
		$t->set_var('SPACE',$space);
		$t->set_var('INDENT',$indent.'px');
		$t->set_var('SUBJECT',$subject);
		$t->set_var('SUBJECT_URL',$subject_url); 
		$t->set_var('POST_KEY',$post_key2);
		$t->set_var('THREAD_KEY',$thread_key);
		$t->set_var('PARENT_KEY',$parent_key);
		$t->set_var('FULL_NAME',$full_name);
		$t->set_var('FULL_NAME_URL',$full_name_url);
		$t->set_var('USER_KEY',$user_key);
		$t->set_var('DATE_ADDED',$date_added);
		$t->set_var('TIME_ADDED',$time_added);
		$t->set_var('EMAIL',$email);
		$t->set_var('BODY',$body);
		$t->set_var('TYPE',$forum_strings['post_type_'.$type_key]);
		
		if ($parent_key=='0') {
			
			$t->set_var('NEW_THREAD','<strong>'.$forum_strings['new_thread2'].': '.$general_strings['subject']." = $subject</strong>"); 
		
		} else {
			
			$t->set_var('NEW_THREAD','');	   
		
		}
		

		$t->set_var('OUTER_CELL_CLASS',$outer_cell_class);
		$t->set_var('INNER_CELL_CLASS',$inner_cell_class);

		//if user is an administrator show admin tool and full post details		
		if ($is_admin==true || ($user_key==$current_user_key && $unix_date_added>$editable_date) || ($user_key==$current_user_key && $forum_settings['edit_level']==2)) {

 
			$post_details = $forum_strings['post_no'].' '.$post_key2.', '.$forum_strings['thread_no'].' '.$thread_key.', '.$forum_strings['parent_no'].' '.$parent_key;
			$t->set_var('POST_DETAILS',$post_details);
			$t->parse('EDPBlock','EditPostBlock', true);
			
		} else {
		 
			$t->set_var('EDPBlock','');   
			$post_details = $forum_strings['post_no'].' '.$post_key2;
			$t->set_var('POST_DETAILS',$post_details);			
		
		}
		//if user wants all displayed posts flagged as read do that now
		
		if ($_SESSION['read_posts_flag']==1 && !isset($post_statuses[$post_key2])) {
		
			$t->set_var('READ_CHECKED','checked');
			if ($forum->checkPostStatus($post_key, $current_user_key)===false) {
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, read_status) VALUES ('$module_key', '$post_key2', '$current_user_key','1')");
				
			} else {
			
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='1' WHERE user_key='$current_user_key' AND post_key='$post_key2'");		
			
			}
			
		}
		
		$t->parse('FULL_POSTS', 'fullposts', true);
		get_thread($post_key2,$space."<td rowspan=\"3\" width=\"20\"><img src=\"../../images/tf_last.gif\" width=\"20\" height=\"20\" vspace=\"0\" hspace=\"0\" align=\"top\"></td>",$indent+15);
		$rs->MoveNext();
		
	}
	
	$rs->Close();
	return true;

} //end get_thread

?>
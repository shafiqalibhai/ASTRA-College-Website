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
* My posts 
*
* Displays all posts added by current user 
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: myposts.php,v 1.9 2007/01/07 22:25:30 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

require_once('../modules/forum/forum.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

$current_user_key = $_SESSION['current_user_key'];
$space_key = $_GET['space_key'];

$access_levels = authenticate();

$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];   
$this_space_key = $space_key;


if (!class_exists(InteractForum)) {

	require_once('../modules/forum/lib.inc.php');
	
}
$objForum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $objForum_strings);

//get array of read posts
$post_statuses = $objForum->getPoststatusArray('',$current_user_key);

//get required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'fullposts'	   => 'forums/ShowFullPost.ihtml',
	'body'			=> 'spaces/myposts.ihtml',
	'footer'		  => 'footer.ihtml'
));

$page_details=get_page_details($space_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_block('fullposts', 'FinishedBlock', 'FBlock');
$t->set_var('PAGE_TITLE',$forum_strings['new_postings']);
$t->set_var('SPACE_TITLE',$forum_strings['new_postings']);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('THIS_SPACE_KEY',$this_space_key);
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
$t->set_var('REPLY_QUOTED_STRING',$general_strings['reply_quoted']);
$t->set_var('LOGGED_IN_STRING',$general_strings['logged_in_as']);
$t->set_var('MODIFY_DETAILS_STRING',$general_strings['modify_details']);
$t->set_var('YOUR_LINKS_STRING',$general_strings['your_links']);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);
$t->set_var('NEW_POSTS_HEADING',$forum_strings['new_postings']);
$t->set_var('SUBMIT_CHANGES_STRING',$forum_strings['submit_changes']);
$t->set_var('READ_STRING',$forum_strings['read']);
$t->set_var('NOT_READ_STRING',$forum_strings['not_read']);
$t->set_var('FOLLOW_UP_STRING',$forum_strings['follow_up']);
$t->set_var('FOLLOWED_UP_STRING',$forum_strings['followed_up']);
$t->set_var('STATUS_STRING',$general_strings['status']);
$t->set_var('FINISHED_STRING',$forum_strings['finished']);
$t->set_var('MONITOR_POST_STRING',$forum_strings['monitor_post']);
$t->set_var('VIEW_POSTINGS_STRING',$forum_strings['view_my_posts']);
$t->set_var('TODAY_STRING',$general_strings['today']);
$t->set_var('LAST_LOGIN_STRING',$general_strings['since_last_login']);
$t->set_var('LAST_3_DAYS_STRING',sprintf($general_strings['last_days'],3));
$t->set_var('LAST_14_DAYS_STRING',sprintf($general_strings['last_days'],14));
$t->set_var('LAST_MONTH_STRING',$general_strings['last_month']);
$t->set_var('MY_POSTS_HEADING',$forum_strings['my_posts_heading']);
$t->set_var('DAYS',$days);

if ($show_read_posts=='' || $show_read_posts==0) {

	$t->set_var('SHOW_READ_POSTS_STRING',$forum_strings['show_read']);
	$t->set_var('SHOW_READ_POSTS','1');

} else {

	$t->set_var('SHOW_READ_POSTS_STRING',$forum_strings['show_unread']);
	$t->set_var('SHOW_READ_POSTS','0');

}
if ($show_flagged_posts=='' || $show_flagged_posts==0) {

	$t->set_var('SHOW_FLAGGED_POSTS_STRING',$forum_strings['show_follow_ups']);
	$t->set_var('SHOW_FLAGGED_POSTS','1');

} else {

	$t->set_var('SHOW_FLAGGED_POSTS_STRING',$forum_strings['show_new_posts']);
	$t->set_var('SHOW_FLAGGED_POSTS','0');

}

$t->set_var('NEW_POSTS_HEADING',$forum_strings['new_postings']);

if (!isset($space_key) || $space_key=='') {
 
	$t->set_var('BREADCRUMBS','');
	$t->set_var('PAGE_TITLE',$general_strings['news']);
	$t->set_var('SPACE_TITLE','');
	$t->set_var('MAKE_MEMBER','');
	$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
	$t->set_var('MHBlock','');
	$t->set_block('body', 'SpacenameBlock', 'SPNBlock');
	$t->set_var('SPNBlock','');


} else {

 
}

$t->parse('CONTENTS', 'header', true);

get_navigation();
get_users_posts();
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

$CONN->Close();
exit;
function get_users_posts()
{
	
	global $CONN, $t,$userlevel_key,$accesslevel_key,$space_key,$module_key,$post_key,$thread_key,$user_key, $current_user_key,$last_use,$days,$post_statuses, $forum_strings, $CONFIG, $view, $objForum, $show_read_posts, $show_flagged_posts, $objDates, $objHtml, $current_user_key;
 
	
	if (!class_exists('InteractDate')) {
	
		require_once('../includes/lib/date.inc.php');
		
	}
	
	if (!is_object($objDates)) {
	
		$objDates = new InteractDate();
	
	}
	

	if (!class_exists('InteractHtml')) {
	
		require_once('../includes/lib/html.inc.php');
	
	}
	
	if (!is_object($objHtml)) {
	
		$objHtml = new InteractHtml();
		
	}
		
	$forum_settings = get_forum_settings($module_key);

	//work out which posts to display
	
	if ($days=='last_login' || !$days) {
	
		//if they have logged in before set their last login to today
		if ($_SESSION['last_use']>0) {

			$last_use = $_SESSION['last_use'];
	
		} else {

			$last_use = date('Y-m-d H:i:s');
	
		}
		
		$interval = $CONN->DBDate($last_use);
		
	} else {
	
		$interval="DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)";
		
	}
 
	if ($show_flagged_posts==1) {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}posts.post_key, thread_key, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name,{$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}spaces.short_name, {$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}post_user_links, {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}posts.post_key={$CONFIG['DB_PREFIX']}post_user_links.post_key  AND   {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND   {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND {$CONFIG['DB_PREFIX']}post_user_links.user_key='$current_user_key' $space_sql AND {$CONFIG['DB_PREFIX']}post_user_links.flag_status='1' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' ";
	
	} else {
	
		//$sql = "SELECT DISTINCT post_key, ThreadKey, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, Subject, Body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name,{$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}spaces.code, {$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.Attachment,{$CONFIG['DB_PREFIX']}forum_settings.file_path FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}forum_settings WHERE ({$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key ) AND  {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND   {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND ({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key')AND ({$CONFIG['DB_PREFIX']}posts.date_added >=$interval) AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' $space_sql ORDER BY {$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}posts.module_key,{$CONFIG['DB_PREFIX']}posts.date_added DESC";
		
				$sql = "SELECT DISTINCT post_key, thread_key, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name,{$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}spaces.short_name, {$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}space_user_links WHERE   {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND   {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND ({$CONFIG['DB_PREFIX']}posts.added_by_key='$current_user_key')";
 
	}
	
	$rs = $CONN->Execute($sql);

	if ($rs->EOF) {
	
		$t->set_var('MESSAGE',$forum_strings['no_new_postings']);
		$t->set_block('body', 'ButtonBlock', 'BBlock');
		$t->set_var('BBlock','');
		
	} else {

		while (!$rs->EOF) {

			$t->set_var('FBlock','');
			$post_key2 = $rs->fields[0];
			$thread_key = $rs->fields[1];
			$parent_key = $rs->fields[2];
			$type_key = $rs->fields[3];
			$subject = $rs->fields[4];
			$subject_url = urlencode($subject);
			$body = $objHtml->parseText($rs->fields[5]);
		   	$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short');
			$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
			$full_name = $rs->fields[7].' '.$rs->fields[8];
			$email = $rs->fields[9];
			$user_key = $rs->fields[10];
			$group_key = $rs->fields[11];
			$module_key = $rs->fields[12];
			$space_key2 = $rs->fields[13];
			$space_name = $rs->fields[14];
			$module_name = $rs->fields[15];
			$space_short_name = $rs->fields[16];
			$file_path = $rs->fields[17];
			$photo = $rs->fields[18];
			$show_photo = $rs->fields[19];			
			$attachment = $rs->fields[20];
			$forum_file_path = $rs->fields[21];			
			$attachment_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$forum_file_path.'/'.$attachment;
			
			if (1==1) {
			
				$posts_to_display=true;
				if (is_file($attachment_path)){
		
					$attachment_view_path = $CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/forum/'.$forum_file_path.'/'.$attachment;
					$t->set_var('VIEW_ATTACHMENT','<a href="'.$attachment_view_path.'">'.$forum_strings['view_attachment'].'</a>');
	
				} else {
		
					$t->set_var('VIEW_ATTACHMENT','');
		
				}		

			
				if ($show_photo=='t') {
		
					$photo_path=$CONFIG['USERS_PATH'].'/'.$file_path.'/'.$photo;
		 			$relative_path=$CONFIG['USERS_VIEW_PATH'].'/'.$file_path.'/'.$photo;
			
					if (is_file($photo_path)) {
			
						$image_array = GetImageSize($photo_path); // Get image dimensions
						$image_width = $image_array[0]; // Image width
						$image_height = $image_array[1]; // Image height
				
						if ($image_width>80) {
				
							$factor=80/$image_width; 
							$image_height=round($image_height*$factor);
							$image_width = '80';
					
						}
				
						$image_tag = "<a href=\"{$CONFIG['PATH']}/users/userdetails.php?user_key=$user_key&space_key=$space_key2\" target=\"_$user_key \"><img src=\"$relative_path\" height=\"$image_height\" width=\"$image_width\" border=\"0\"></a>";
						$t->set_var('PHOTO',$image_tag);
						$t->set_var('PHOTO_WIDTH',$image_width);
								
					} else {
				
						$t->set_var('PHOTO','');
				
					}
			
				} else {
			
					$t->set_var('PHOTO','');
			
				}
			
				if ($group_key==0 || in_array($group_key,$group_access)) {
			
					if (isset($space_key) && $space_key!='') {
					
						$new_space_module_name = $module_name;
						
					} else {
					
						$new_space_module_name = "$space_short_name - $space_name ($module_name)";
					
					}
				
					if ($space_module_name!=$new_space_module_name) {
					
						$space_module_name="$new_space_module_name";
						$text = sprintf($forum_strings['postings_from'],$space_module_name);
						$t->set_var('NEW_THREAD',"<b class=\"red\">$text</b>");
				
					} else {
				
						$t->set_var('NEW_THREAD','');
					
					}
				
				}
 		if ((isset($post_statuses['read'][$post_key2]) && $post_statuses['read'][$post_key2]==1) || $current_user_key==$user_key) {
		
			$t->set_var('READ_CHECKED','checked');
			$t->set_var('READ_TAG','span class="small"');
			
			$inner_cell_class='ForumPostingReadInner';
	 		$outer_cell_class='ForumPostingReadOuter';
	   
			
		} else {
		
		
			$inner_cell_class='ForumPostingInner';
			$outer_cell_class='ForumPostingOuter';
			$t->set_var('READ_TAG','strong');
		
		
		}			
		$t->set_var('OUTER_CELL_CLASS',$outer_cell_class);
		$t->set_var('INNER_CELL_CLASS',$inner_cell_class);	
		
		if (isset($post_statuses['flags'][$post_key2])) {
		
			switch ($post_statuses['flags'][$post_key2]) {
			
				case 1:
				
					$t->set_var('FLAG_CHECKED','checked');
					$t->set_var('FINISHED_CHECKED','');
					$t->set_var('STATUS_IMAGE','<img src="../../images/modules/forum/red_flag.gif">');					
					$t->parse('FBlock', 'FinishedBlock', true);
					
				break;
				
				case 2:
				
					$t->set_var('FLAG_CHECKED','');
					$t->set_var('FINISHED_CHECKED','checked');
					$t->set_var('STATUS_IMAGE','<img src="../../images/modules/forum/white_flag.gif">');
					$t->parse('FBlock', 'FinishedBlock', true);
								
				break;
				
				default:
				
					$t->set_var('STATUS_IMAGE','');
					$t->set_var('FLAG_CHECKED','');
					$t->set_var('STATUS_IMAGE','');
					$t->set_var('FBlock','');
					
				break;
				
			}
	
		
		} else {
		
			$t->set_var('STATUS_IMAGE','');
			$t->set_var('FLAG_CHECKED','');
			$t->set_var('FBlock','');
		
		}
		if (isset($post_statuses['monitor'][$post_key2]) && $post_statuses['monitor'][$post_key2]==1) {
			
			$t->set_var('MONITOR_CHECKED',checked);
			
		} else {
			
			$t->set_var('MONITOR_CHECKED','');
			
		}
	   
				
				$t->set_var('SPACE',$space);
				$t->set_var('SUBJECT',$subject);
				$t->set_var('SUBJECT_URL',$subject_url); 
				$t->set_var('POST_KEY',$post_key2);
				$t->set_var('THREAD_KEY',$thread_key);
				$t-> set_var('PARENT_KEY',$parent_key);
				$t->set_var('MODULE_KEY',$module_key);
				$t->set_var('SPACE_KEY',$space_key2);
				$t->set_var('FULL_NAME',$full_name);
				$t->set_var('FULL_NAME_URL',$full_name_url);
				$t->set_var('USER_KEY',$user_key);
				$t->set_var('DATE_ADDED',$date_added);
				$t->set_var('TIME_ADDED',$time_added);
				$t->set_var('EMAIL',$email);
				$t->set_var('BODY',$body);
				$t->set_var('TYPE',$forum_strings['post_type_'.$type_key]);
				$t->set_var('FORUMS_PATH',$CONFIG['PATH'].'/modules/forum/');
				$t->set_var('POST_BACKGROUND_CLASS','forumPosting');
				
				//if user is an administrator show admin tool and full post details		
				if ($userlevel_key=='1' || ($user_key==$current_user_key && $unix_date_added>$editable_date) || ($user_key==$current_user_key && $forum_settings['edit_level']==2)) {

					$admin_image=" - <a href=\"{$CONFIG['PATH']}/modules/forum/editpost.php?space_key=$space_key2&module_key=$module_key&post_key=$post_key2&action=Edit\" class=\"small\">".$forum_strings['edit_post'].'</a>';
		   
					 $t->set_var('ADMIN_IMAGE',$admin_image);
					 $post_details = $forum_strings['post_no'].' '.$post_key2.', '.$forum_strings['thread_no'].' '.$thread_key.', '.$forum_strings['parent_no'].' '.$parent_key;
					 $t->set_var('POST_DETAILS',$post_details);
				}
				
				$context_link = "<a href=\"{$CONFIG['PATH']}/modules/forum/thread.php?space_key=$space_key2&module_key=$module_key&thread_key=$thread_key&post_key=$post_key2#$post_key2\">".$forum_strings['view_in_context'].'</a>';
				$t->set_var('CONTEXT',$context_link);
				
				//if user wants all displayed posts flagged as read do that now
		
				if ($_SESSION['read_posts_flag']==1 && !isset($post_statuses[$post_key2])) {
		
					$t->set_var('READ_CHECKED','checked');
					if ($objForum->checkPoststatus($post_key2, $current_user_key)===false) {
			
						$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, read_status) VALUES ('$module_key', '$post_key2', '$current_user_key','1')");
				
					} else {
			
						$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='1' WHERE user_key='$current_user_key' AND post_key='$post_key2'");		
			
					}

			
				}
				$t->parse('FULL_POSTS', 'fullposts', true);
				
			}
				
			$rs->MoveNext();
		
		}
		
		if ($posts_to_display!=true) {
		
			$t->set_block('body', 'ButtonBlock', 'BBlock');
			$t->set_var('BBlock','');
		
		}

	}
	
	$rs->Close();
	return true;

} //end get_users_posts




?>

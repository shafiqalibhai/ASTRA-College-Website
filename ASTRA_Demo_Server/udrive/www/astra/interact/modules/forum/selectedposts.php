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
* Display selected posts
*
* Displays selected posts from a forum
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: selectedposts.php,v 1.32 2007/07/30 01:56:59 glendavies Exp $
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
$module_key	= $_POST['module_key'];
$sort_by	= $_POST['sort_by'];
$number		= $_POST['number'];
$link_key 	= get_link_key($module_key,$space_key);
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

require_once('lib.inc.php');
$forum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_strings);

//get array of read posts
$post_statuses = $forum->getPostStatusArray($module_key,$current_user_key);

if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}
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
$t->set_block('thread', 'PreviousLinkBlock', 'PLBlock');
$t->set_var('PLBlock','');
$t->set_block('thread', 'NextLinkBlock', 'NLBlock');
$t->set_var('NLBlock','');

$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
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
$t->set_var('REFERER',$_SERVER['HTTP_REFERER']);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);
$t->set_var('SUBMIT_CHANGES_STRING',$forum_strings['submit_changes']);
$t->set_var('READ_STRING',$forum_strings['read']);
$t->set_var('NOT_READ_STRING',$forum_strings['not_read']);
$t->set_var('FOLLOW_UP_STRING',$forum_strings['follow_up']);
$t->set_var('FINISHED_STRING',$forum_strings['finished']);
$t->set_var('STATUS_STRING',$general_strings['status']);
$t->set_var('MONITOR_POST_STRING',$forum_strings['monitor_post']);
$t->parse('CONTENTS', 'header', true);

get_navigation();

$print_link = " - <a href=\"print.php?space_key=$space_key&module_key=$module_key&sort_by=Name&number=$number\">".$general_strings['print_display'].'</a>';

$t->set_var('PRINT_LINK',$print_link);
$t->parse('CONTENTS', 'forumheader', true);

switch ($sort_by) {
	
	case ThreadKey:
		
		if ($number=='all') {
			
			get_full_threads('0',$space,'DESC');
				
		} else {
			
			get_by();
		
		}
	
	break;
	
	case Name:
		
		$sort_by="{$CONFIG['DB_PREFIX']}users.last_name";
		get_by();
	
	break;

}

$t->parse('CONTENTS', 'thread', true);	
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

function get_full_threads($parent_key,$space,$sort_order)
{
	global $CONN, $t,$userlevel_key,$accesslevel_key,$space_key,$module_key,$post_key,$thread_key,$group_accesslevel,$readposts_array, $forum_strings, $general_strings, $is_admin, $CONFIG, $current_user_key, $objDates, $post_statuses;

 	$forum_settings = get_forum_settings($module_key);
	
	$sql = "SELECT post_key, thread_key, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name,{$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users WHERE  {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND (parent_key='$parent_key' AND module_key='$module_key') AND status_key='1' ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added $sort_order";


	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {
	
		$t->set_var('PHOTO','');
		$t->set_var('PHOTO_WIDTH','');
		$post_key2 = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$type_key = $rs->fields[3];
		$subject = $rs->fields[4];
		$subject_url = urlencode($subject);
		
 		if (eregi("(<p>|<br />)", $rs->fields[5])) {
			
			$body = $rs->fields[5];
			
   		} else {
		
			$body = nl2br($rs->fields[5]);
		}
		
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short');
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
		$unix_date_added = $CONN->UnixTimestamp($rs->fields[6]);
		$date_now = mktime();
		$editable_date = $date_now-1800;				
		$full_name = $rs->fields[7].' '.$rs->fields[8];
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
				
				$image_tag = "<img src=\"$relative_path\" height=\"$image_height\" width=\"$image_width\">";
				$t->set_var('PHOTO',$image_tag);
				$t->set_var('PHOTO_WIDTH',$image_width);
								
			}
			
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
		$t->set_var('POST_BACKGROUND_CLASS',$post_background);
		
		//if user is an administrator show admin tool and full post details		
		if ($is_admin==true || ($user_key==$current_user_key && $unix_date_added>$editable_date) || ($user_key==$current_user_key && $forum_settings['edit_level']==2)) {

			$admin_image=" - <a href=\"{$CONFIG['PATH']}/modules/forum/editpost.php?space_key=$space_key&module_key=$module_key&post_key=$post_key2&action=Edit\" class=\"small\">Edit/Delete</a>";
		
			$t->set_var('ADMIN_IMAGE',$admin_image);
			$post_details = $forum_strings['post_no'].' '.$post_key2.', '.$forum_strings['thread_no'].' '.$thread_key.', '.$forum_strings['parent_no'].' '.$parent_key;
			$t->set_var('POST_DETAILS',$post_details);
			
		} else {
		 
			$admin_image='';
			$post_details = $forum_strings['post_no'].' '.$post_key2;
			$t->set_var('POST_DETAILS',$post_details);			
		
		}

		$t->parse('FULL_POSTS', 'fullposts', true);
		get_full_threads($post_key2,$space."<td width=\"20\"><img src=\"../../images/tf_last.gif\" width=\"20\" height=\"20\" vspace=\"0\" hspace=\"0\" align=\"top\"></td>","ASC");
		$rs->MoveNext();
		
	}
	
	$rs->Close();
	
	return true;

} //end get_full_threads

function get_by()
{
	global $CONN, $t,$userlevel_key,$accesslevel_key,$space_key,$module_key,$post_key,$thread_key,$number,$sort_by,$group_accesslevel,$readposts_array, $forum_strings, $general_strings, $is_admin, $CONFIG, $current_user_key, $objDates, $post_statuses;
	
 	$forum_settings = get_forum_settings($module_key);
		
	if ($number!='all') {
		
		$interval="{$CONFIG['DB_PREFIX']}posts.date_added > DATE_SUB(CURRENT_DATE, INTERVAL $number DAY) and ";
	
	}
	
	$sql = "SELECT post_key, thread_key, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added,  {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users WHERE  {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND ($interval module_key='$module_key') AND status_key='1' ORDER BY $sort_by, {$CONFIG['DB_PREFIX']}posts.date_added ASC";
	
	$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
	while (!$rs->EOF) {
		$t->set_var('PHOTO','');
		$t->set_var('PHOTO_WIDTH','');
		$post_key2 = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$type_key = $rs->fields[3];
		$subject = $rs->fields[4];
		$subject_url = urlencode($subject);
		
 		if (eregi("(<p>|<br />)", $rs->fields[5])) {
			
			$body = $rs->fields[5];
			
   		} else {
		
			$body = nl2br($rs->fields[5]);
		}
		
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short');
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
		$unix_date_added = $CONN->UnixTimestamp($rs->fields[6]);
		$date_now = mktime();
		$editable_date = $date_now-1800;
		$full_name = $rs->fields[7].' '.$rs->fields[8];
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
				
				$image_tag = "<img src=\"$relative_path\" height=\"$image_height\" width=\"$image_width\">";
				$t->set_var('PHOTO',$image_tag);
				$t->set_var('PHOTO_WIDTH',$image_width);
								
			}
			
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
		
		$t->set_var('OUTER_CELL_CLASS',$outer_cell_class);
		$t->set_var('INNER_CELL_CLASS',$inner_cell_class);
		$t->set_var('POST_BACKGROUND_CLASS',$post_background);
		
		//if user is an administrator show admin tool and full post details		
		if ($is_admin==true || ($user_key==$current_user_key && $unix_date_added>$editable_date) || ($user_key==$current_user_key && $forum_settings['edit_level']==2)) {

			$admin_image=" - <a href=\"{$CONFIG['PATH']}/modules/forum/editpost.php?space_key=$space_key&module_key=$module_key&post_key=$post_key2&action=Edit\" class=\"small\">Edit/Delete</a>";
		
			$t->set_var('ADMIN_IMAGE',$admin_image);
			$post_details = $forum_strings['post_no'].' '.$post_key2.', '.$forum_strings['thread_no'].' '.$thread_key.', '.$forum_strings['parent_no'].' '.$parent_key;
			$t->set_var('POST_DETAILS',$post_details);
			
		} else {
		 
			$t->set_var('ADMIN_IMAGE','');
			$post_details = $forum_strings['post_no'].' '.$post_key2;
			$t->set_var('POST_DETAILS',$post_details);			
		
		}
	
		$context_link = "<a href=\"thread.php?space_key=$space_key&module_key=$module_key&thread_key=$thread_key&post_key=$post_key#$post_key2\">".$forum_strings['view_in_context'].'</a>';
		$t->set_var('CONTEXT',$context_link);
		$t->parse('FULL_POSTS', 'fullposts', true);
		$rs->MoveNext();
	
	}
	
	$rs->Close();
	return true;

} //end get_by
?>
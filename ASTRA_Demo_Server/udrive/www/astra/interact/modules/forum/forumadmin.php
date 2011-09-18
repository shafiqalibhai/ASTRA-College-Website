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
* Forum admin
*
* Various functions displaying, archiving posts, etc. 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: forumadmin.php,v 1.35 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['BASE_PATH'].'/modules/forum/forum.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

//set required variables
$space_key 			= get_space_key();
$module_key			= $_POST['module_key'];
$marked_action	  = isset($_POST['move_posts'])?$_POST['move_posts']:$_POST['marked_action'];
$selected_post_keys = $_POST['selected_post_keys'];
$move_post		  = $_POST['move_post'];
$move_to_post	   = $_POST['move_to_post'];
$new_posts		  = $_POST['new_posts'];
$print_save_keys	= $_POST['print_save_keys'];
$referer			= $_POST['referer'];
$entry_key			= $_POST['entry_key'];
$thread_key			= $_POST['thread_key'];
$current_user_key   = $_SESSION['current_user_key'];	


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate_home();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}


require_once($CONFIG['BASE_PATH'].'/modules/forum/lib.inc.php');
$forum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_strings);
//find out what action we need to take

if (isset($marked_action)) {

	switch($marked_action) {

		case 'set_flags':

			if (is_array($_POST['post_keys'])) {
			
				foreach ($_POST['post_keys'] as $post_key) {

					if ($new_posts=='y') {
						
						$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'";
						$rs = $CONN->Execute($sql);
						
						while (!$rs->EOF) {
						
							$module_key = $rs->fields[0];
							$rs->MoveNext();
						
						}
					
					}
					
					if(isset($_POST[$post_key.'_read']) || isset($_POST[$post_key.'_flagged']) || isset($_POST[$post_key.'_monitor_post'])) {
						
						$read_status	= isset($_POST[$post_key.'_read'])? $_POST[$post_key.'_read']: '';
						
						$flagged_status = isset($_POST[$post_key.'_flagged'])? $_POST[$post_key.'_flagged'] : '';
						$monitor_post   = isset($_POST[$post_key.'_monitor_post'])? $_POST[$post_key.'_monitor_post']: '';
						
						if ($forum->checkPostStatus($post_key, $current_user_key)===false) {

							$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, read_status, flag_status, monitor_post) VALUES ('$module_key', '$post_key', '$current_user_key','$read_status', '$flagged_status', '$monitor_post')");
							
						} else {
											
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='$read_status', flag_status='$flagged_status', monitor_post='$monitor_post' WHERE user_key='$current_user_key' AND post_key='$post_key'");
						
						}
							
					} else {
					
						$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='', flag_status='', monitor_post='' WHERE user_key='$current_user_key' AND post_key='$post_key'");
					
					}
			
				}
			
			}
		
			if (is_array($print_save_keys) && count($print_save_keys)>0) {

				print_save_posts($print_save_keys, $entry_key);
			
			} else {
			
				if ($new_posts=='y')  {
			
					header("Location: {$CONFIG['FULL_URL']}/spaces/newposts.php?view=follow_ups&space_key=$space_key");
					exit;
			
				}  else if (isset($referer) && $referer!='') {
			
					header("Location: {$CONFIG['FULL_URL']}/modules/$referer?space_key=$space_key&module_key=$module_key&thread_key=$thread_key&entry_key=$entry_key");
					exit;
			
				} else {
				
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
					exit;				
				
				
				}
				
			}
		
		break;
		 
		case 'Delete':
			$num_selected = count($selected_post_keys);
			
			if ($num_selected) {

				for ($c=0; $c < $num_selected; $c++) {
					
					//first delete any attachments
					//get forum file path and existing existing attachment name
					$sql = "SELECT {$CONFIG['DB_PREFIX']}forum_settings.file_path, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}forum_settings,  {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='{$selected_post_keys[$c]}'";

					$rs = $CONN->Execute($sql);
					while (!$rs->EOF) {

						$file_path = $rs->fields[0];
						$existing_attachment = $rs->fields[1];			
						$rs->MoveNext();
		
					}
		
					$existing_attachment_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$file_path.'/'.$existing_attachment;
		
					if (is_file($existing_attachment_path)){
		
						unlink($existing_attachment_path);
					
					}
					
					$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$selected_post_keys[$c]'";
					$rs = $CONN->Execute($sql);
					//delete any entries from ReadPost table
					$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='$selected_post_keys[$c]'";
					$CONN->Execute($sql);
					delete_children($selected_post_keys[$c]);
					
				}
			
			}
			
			$message = urlencode($forum_strings['posts_deleted']);
			
			header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
			exit;
			
		break;


		case 'Move':
				
				if ((!isset($move_post) && !isset($move_to_post)) || (!isset($move_post) && isset($move_to_post)) ) {
					
					$message = urlencode($forum_strings['no_posts_selected']);
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
					exit;
				
				} else if (isset($move_post) && isset($move_to_post)) { 
					
					check_is_below($move_to_post,$move_post);
					
					if ($is_below=='true') {
						
						$message = urlencode($forum_strings['below_self']);
						header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
						exit;
					 
					 } else {
						 
						 $sql = "SELECT thread_key from {$CONFIG['DB_PREFIX']}posts where post_key='$move_to_post'";
						 $rs = $CONN->Execute($sql);
						 
						 while (!$rs->EOF) {
							 
							 $move_to_thread = $rs->fields[0];
							 $rs->MoveNext();
						 
						 }
						 
						 $sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET parent_key='$move_to_post',thread_key='$move_to_thread' WHERE post_key='$move_post'";
						
						 $rs = $CONN->Execute($sql);
						 update_children($move_post, $move_to_thread);
						 
						 $message = urlencode($forum_strings['post_moved']);

						 header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
						 exit;
					 }
					
					
				} else if (isset($move_post) && !isset($move_to_post)) {
				
					$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET parent_key='0',thread_key='$move_post' WHERE post_key='$move_post'";
					$rs = $CONN->Execute($sql);
					update_children($move_post, $move_post);
					$message = urlencode($forum_strings['post_moved']);
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
					exit;
				}
				
			break;

			case 'read':
			
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {

					//if from new_posts page get module key for post
					
						if ($new_posts=='y') {
						
							$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$selected_post_keys[$c]'";
							$rs = $CONN->Execute($sql);
						
							while (!$rs->EOF) {
						
								$module_key = $rs->fields[0];
								$rs->MoveNext();
						
							}
					
						}
		
						if ($forum->checkPostStatus($post_key, $current_user_key)===false) {
						
							$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, read_status) VALUES ('$module_key', '$post_key', '$current_user_key','1')");
							

						} else {
						
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='1' WHERE user_key='$current_user_key' AND post_key='$post_key'");
													
						
						}
						
					}
			
				}
			
				if ($new_posts=='y') {
					 
					 header("Location: {$CONFIG['FULL_URL']}");
				
				} else {
					
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
				
				}
				
				exit;
				
			break;
			
			case 'monitor':
			
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {

							
						if ($forum->checkPostStatus($post_key, $current_user_key)===false) {
						
							$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, monitor_post) VALUES ('$module_key', '$post_key', '$current_user_key','1')");
							

						} else {
						
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET monitor_post='1' WHERE user_key='$current_user_key' AND post_key='$post_key'");
													
						
						}
						
					}
			
				}
			
				header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
				
				exit;
				
			break;
			
			case 'remove_monitor':
			
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {

							
						if ($forum->checkPostStatus($post_key, $current_user_key)===false) {
						
														

						} else {
						
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET monitor_post='0' WHERE user_key='$current_user_key' AND post_key='$post_key'");
													
						
						}
						
					}
			
				}
			
				header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
				
				exit;
				
			break;
			
			case 'follow_up':
			
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {

					//if from new_posts page get module key for post
					
						if ($new_posts=='y') {
						
							$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$selected_post_keys[$c]'";
							$rs = $CONN->Execute($sql);
						
							while (!$rs->EOF) {
						
								$module_key = $rs->fields[0];
								$rs->MoveNext();
						
							}
					
						}
		
						if ($forum->checkPostStatus($post_key, $current_user_key)===false) {
						
  							$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, flag_status) VALUES ('$module_key', '$post_key', '$current_user_key','1')");
							echo $CONN->ErrorMsg();

						} else {
						
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET flag_status='1' WHERE user_key='$current_user_key' AND post_key='$post_key'");
													
						
						}

					}
			
				}
			
				if ($new_posts=='y') {
					 
					 header("Location: {$CONFIG['FULL_URL']}");
				
				} else {
					
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
				
				}
				
				exit;
				
			break;	
			
			case 'followed_up':
			
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {

					//if from new_posts page get module key for post
					
						if ($new_posts=='y') {
						
							$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$selected_post_keys[$c]'";
							$rs = $CONN->Execute($sql);
						
							while (!$rs->EOF) {
						
								$module_key = $rs->fields[0];
								$rs->MoveNext();
						
							}
					
						}
		
						if ($forum->checkPostStatus($post_key, $current_user_key)===false) {
						
  							$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, flag_status) VALUES ('$module_key', '$post_key', '$current_user_key','2')");
							

						} else {
						
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET flag_status='2' WHERE user_key='$current_user_key' AND post_key='$post_key'");
													
						
						}

					}
			
				}
			
				if ($new_posts=='y') {
					 
					 header("Location: {$CONFIG['FULL_URL']}");
				
				} else {
					
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
				
				}
				
				exit;
				
			break;					
			
			case 'remove_flags':
			
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {
	
						$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET flag_status='0' WHERE user_key='$current_user_key' AND post_key='$post_key'");
	

					}
			
				}
			
				if ($new_posts=='y') {
					 
					 header("Location: {$CONFIG['FULL_URL']}");
				
				} else {
					
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
				
				}
				
				exit;
				
			break;									
							
			case 'not_read':
				
				if (is_array($selected_post_keys)) {
				
					foreach ($selected_post_keys as $post_key) {
				
						$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='0' WHERE user_key='$current_user_key' AND post_key='$post_key'");

					}
				
				}
				
				if ($new_posts=='y') {
					 
					header("Location: {$CONFIG['FULL_URL']}");
				
				} else {
					
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
					
				}
				
				exit;
				
			break;
				
			case 'Print/Save':
					
				if (is_array($print_save_keys) && count($print_save_keys)>0) {
				
					print_save_posts($print_save_keys);
					
				} else {
				
					print_save_posts($selected_post_keys);
				
				}
				
			break;
				
			case 'Turn on auto-prompting':
					
				get_forum_data($module_key);
					
				foreach($selected_post_keys as $post_key) {
					
					$rs = $CONN->EXECUTE("SELECT post_key FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'");
						
					if ($rs->EOF) {
						
						add_auto_prompting($post_key);
							
					}
						
				}
					
				$message = urlencode($forum_strings['autoprompting_added']);
				header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");				
				
				exit;
				
			break;
					
			case 'Turn off auto-prompting':
				
				foreach($selected_post_keys as $post_key) {
					
					$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'");
											
				}
					
				$message = urlencode($forum_strings['autoprompting_removed']);
				header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
								
				exit;
			
			break;
				
	}
	
}


function delete_children($post_key) 
{   
	
	global $CONN, $CONFIG;
	
	$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {
	
		$child_post_key = $rs->fields[0];
		
		//first delete any attachments
		//get forum file path and existing existing attachment name
		$sql = "SELECT {$CONFIG['DB_PREFIX']}forum_settings.file_path, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}forum_settings, {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$child_post_key'";

		$rs2 = $CONN->Execute($sql);

		while (!$rs2->EOF) {

			$file_path = $rs2->fields[0];
			$existing_attachment = $rs2->fields[1];			
			$rs2->MoveNext();
		
		}
		
		$existing_attachment_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$file_path.'/'.$existing_attachment;
		
		if (is_file($existing_attachment_path)){
		
			unlink($existing_attachment_path);
	
		}
		$sql2="DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$child_post_key'";
		$CONN->Execute($sql2);
		//delete any entries from ReadPost table
		$sql3 = "DELETE FROM {$CONFIG['DB_PREFIX']}ReadPosts WHERE post_key='$child_post_key'";
		$CONN->Execute($sql3);
		delete_children($child_post_key);
		$rs->MoveNext();
	
	}

}

function archive_children($post_key) 
{   
	
	global $CONN, $CONFIG;
	
	$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$child_post_key = $rs->fields[0];
		$sql2="UPDATE {$CONFIG['DB_PREFIX']}posts SET status_key='2' WHERE post_key='$child_post_key'";
		$CONN->Execute($sql2);
		delete_children($child_post_key);
		$rs->MoveNext();
	
	}

}

function unarchive_children($post_key) 
{   
	
	global $CONN, $CONFIG;
	
	$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$child_post_key = $rs->fields[0];
		$sql2="UPDATE {$CONFIG['DB_PREFIX']}posts SET status_key='1' WHERE post_key='$child_post_key'";
		$CONN->Execute($sql2);
		delete_children($child_post_key);
		$rs->MoveNext();
		
	}

}


function check_is_below($move_to_post,$move_post) 
{
	
	global $CONN, $is_below, $CONFIG;
	
	$sql = "SELECT parent_key FROM {$CONFIG['DB_PREFIX']}posts where post_key='$move_to_post'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		
		$parent_key = $rs->fields[0];
		
		if ($parent_key==$move_post) {
			
			$is_below = true;
			return;
		
		} else {
			
			if ($parent_key==0) {
				
				return;
			
			} else {
			
				check_is_below($parent_key,$move_post);
			
			}
		
		}
	
		$rs->MoveNext();
	
	}

}

function update_children($move_post, $move_to_thread) 
{   
	
	global $CONN, $CONFIG;
	
	$sql="SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$move_post'";
	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$child_post_key = $rs->fields[0];
		$sql2="UPDATE {$CONFIG['DB_PREFIX']}posts SET thread_key='$move_to_thread' WHERE post_key='$child_post_key'";
		$CONN->Execute($sql2);
		update_children($child_post_key,$move_to_thread);
		$rs->MoveNext();
	
	}

}

function add_auto_prompting($post_key) {

	global $CONN,$module_data, $CONFIG;

	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}forum_thread_management(post_key,DaysToWait,NumberToPrompt,PassesAllowed,ResponseTime,MinimumReplies) VALUES ('$post_key','{$module_data['days_to_wait']}','{$module_data['number_to_prompt']}','{$module_data['passes_allowed']}','{$module_data['response_time']}','{$module_data['minimum_replies']}')");
	echo $CONN->ErrorMsg();
	   
} //end add_auto_prompting

function print_save_posts($selected_post_keys) {

	global $CONN, $CONFIG, $t, $objDates, $forum_strings;

				require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
				$t = new Template($CONFIG['TEMPLATES_PATH']);  
					
				$t->set_file(array(
					'printheader' => 'forums/printselectedheader.ihtml',
					'fullposts'   => 'forums/printfullpost.ihtml', 
				));
					
				$t->set_var('PAGE_TITLE',$CONFIG['SERVER_NAME'].' - Print/Save Select Posts');
				$t->set_var('POSTED_BY_STRING',$forum_strings['posted_by']);
				$t->set_var('BACK_STRING',$general_strings['back']);
				$t->set_var('BACK_LINK',$_SERVER['HTTP_REFERER']);
				$t->set_var('SUBJECT_STRING',$general_strings['subject']);
				$t->set_var('ON_STRING',$general_strings['on']);
				$t->set_var('AT_STRING',$general_strings['at']);				
					
				$num_selected = count($selected_post_keys);
				
				if ($num_selected) {
					
					for ($c=0; $c < $num_selected; $c++) {
						
						if (!isset($entry_key) || $entry_key='') {
						
							$sql = "SELECT post_key, thread_key, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added,  {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}spaces.Name,{$CONFIG['DB_PREFIX']}modules.Name  FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND (post_key='$selected_post_keys[$c]')";
							
						} else {
						
						
							$sql = "SELECT post_key, thread_key, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added,  {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}spaces.Name,{$CONFIG['DB_PREFIX']}modules.Name FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND (post_key='$selected_post_keys[$c]')";						
						
						}
				
						$rs = $CONN->Execute($sql);

						while (!$rs->EOF) {

							$post_key2 = $rs->fields[0];
							$thread_key = $rs->fields[1];
							$parent_key = $rs->fields[2];
							$type_key = $rs->fields[3];
							$subject = $rs->fields[4];
							$subject_url = urlencode($subject);
							$body = nl2br($rs->fields[5]);
							$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short');
							$time_added = date('H:i', $rs->fields[6]);
							$full_name = $rs->fields[7].' '.$rs->fields[8];
							$email = $rs->fields[9];
							$user_key = $rs->fields[10];
							$space_name = $rs->fields[11];
							$module_name = $rs->fields[12];							  							  
							$type_name = $rs->fields[13];
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
							$t->set_var('TYPE',$type_name);
							$t->set_var('SPACE_FORUM_DETAILS',sprintf($forum_strings['postings_from'], $module_name, $space_name));
							$t->set_var('POST_BACKGROUND','');
							$t->parse('FULL_POSTS', 'fullposts', true);
							$rs->MoveNext();
						  
						}
						
						$rs->Close();
						
					 }

				}		
				$t->parse('CONTENTS', 'printheader', true);
				print_headers();
				$t->p('CONTENTS');
				exit;
	   
} //end add_auto_prompting
?>
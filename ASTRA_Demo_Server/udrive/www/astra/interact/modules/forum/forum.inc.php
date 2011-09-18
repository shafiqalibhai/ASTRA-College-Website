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
* Forum module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* forum module
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: forum.inc.php,v 1.36 2007/04/10 23:39:52 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new forum module
*
* @param  int $module_key  key of new forum module
* @return true if details added successfully
*/

function add_forum($module_key) {

	global $CONN, $CONFIG;
	
	$forum_type	   = $_POST['forum_type'];
	$forum_edit_level = $_POST['forum_edit_level'];
	$auto_prompting   = $_POST['auto_prompting'];
	$days_to_wait	 = $_POST['days_to_wait'];
	$number_to_prompt = $_POST['number_to_prompt'];
	$passes_allowed   = $_POST['passes_allowed'];
	$response_time	= $_POST['response_time'];
	$minimum_replies  = $_POST['minimum_replies'];
							
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	}  
	$forum_path = $subdirectory."/".$module_key;
	$full_forum_path=$CONFIG['MODULE_FILE_SAVE_PATH']."/forum/".$forum_path;

	if (!Is_Dir($full_forum_path)) {

		mkdir($full_forum_path,0777);
		
	}   
	 
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}forum_settings(module_key,forum_type,edit_level,auto_prompting, days_to_wait, number_to_prompt, passes_allowed, response_time, minimum_replies, file_path) VALUES ('$module_key','$forum_type','$forum_edit_level','$auto_prompting','$days_to_wait','$number_to_prompt','$passes_allowed','$response_time', '$minimum_replies','$forum_path')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your forum: '.$CONN->ErrorMsg().' <br />';
	echo $message;
		return $message;
		
	} else {	  
	
			return true;  
	}
	

}

/**
* Function called by Module class to get exisiting forum data 
*
* @param  int $module_key  key of forum module
* @return true if data retrieved
*/
function get_forum_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT forum_type, edit_Level, auto_prompting, days_to_wait, number_to_prompt, passes_allowed, response_time, minimum_replies FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE module_key='$module_key'";   
	
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_data['forum_type']	   = $rs->fields[0];
		$module_data['forum_edit_level'] = $rs->fields[1];
		$module_data['auto_prompting']   = $rs->fields[2];
		$module_data['days_to_wait']	 = $rs->fields[3];
		$module_data['number_to_prompt'] = $rs->fields[4];
		$module_data['passes_allowed']   = $rs->fields[5];
		$module_data['response_time']	= $rs->fields[6];
		$module_data['minimum_replies']  = $rs->fields[7];				
												
		$rs->MoveNext();
	
	}
	
	return true;

}

/**
* Function called by Module class to modify exisiting forum data 
*
* @param  int $module_key  key of forum module
* @param  int $link_key  link key of forum module being modified
* @return true if successful
*/

function modify_forum($module_key,$link_key) {

	global $CONN, $CONFIG;
	$forum_type	   = $_POST['forum_type'];
	$forum_edit_level = $_POST['forum_edit_level'];
	$auto_prompting   = $_POST['auto_prompting'];
	$days_to_wait	 = $_POST['days_to_wait'];
	$number_to_prompt = $_POST['number_to_prompt'];
	$passes_allowed   = $_POST['passes_allowed'];
	$response_time	= $_POST['response_time'];
	$minimum_replies  = $_POST['minimum_replies'];
			
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}forum_settings SET forum_type='$forum_type',edit_level='$forum_edit_level',auto_prompting='$auto_prompting', days_to_wait='$days_to_wait', number_to_prompt='$number_to_prompt', passes_allowed='$passes_allowed', response_time='$response_time', minimum_replies='$minimum_replies' WHERE module_key='$module_key'"; 
	   
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your forum: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}

} //end modify_forum



/**
* Function called by Module class to delete exisiting forum data 
*
* @param  int $module_key  key of forum module
* @param  int $space_key  space key of forum module
* @param  int $link_key  link key of forum module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_forum($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN, $CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "select file_path from {$CONFIG['DB_PREFIX']}forum_settings where module_key='$module_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$file_path = $rs->fields[0];
			$rs->MoveNext();
		
		}
		
		$rs->Close();
		
		if ($file_path!='') {
		
			$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$file_path;

			if (is_Dir($directory_path)) {
		
				delete_directory($directory_path);
		
			}  
			
		}  
		
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key'");
	   
		while (!$rs->EOF) {
	   
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='{$rs->fields[0]}'");
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}forum_auto_prompts WHERE post_key='{$rs->fields[0]}'");
			$rs->MoveNext();
		   
	   }		   
	   
	   $rs->Close();
	   
	   $sql="DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key'";

	   if ($CONN->Execute($sql) === false) {   

			$message = 'There was an error deleting all posting from a Forum - '.$CONN->ErrorMsg();
			email_error($message);
			return $message;

		} else {

			$sql="DELETE FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE module_key='$module_key'";

			if ($CONN->Execute($sql) === false) {   

				$message = 'There was an error deleting forum settings - '.$CONN->ErrorMsg();
				email_error($message);
				return $message;

			} else {

				$sql="DELETE FROM {$CONFIG['DB_PREFIX']}statistics WHERE module_key='$module_key'";

				if ($CONN->Execute($sql) === false) {   

				$message = 'There was an error deleting forum statistics - '.$CONN->ErrorMsg();
				email_error($message);
				return $message;

			} else { 

				$sql="DELETE FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE module_key='$module_key'";

				if ($CONN->Execute($sql) === false) {   

					$message = 'There was an error deleting read post info for a forum - '.$CONN->ErrorMsg();
					email_error($message);
					return $message;
					
				} else {
					
					return true;
					
				}

		   }
		   
		}
		}				
	} else {
	
		return true;
		
	}

} //end delete_forum	 

/**
* Function called by Module class to flag a forum for deletion 
*
* @param  int $module_key  key of forum module
* @param  int $space_key  space key of forum module
* @param  int $link_key  link key of forum module being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_forum_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_forum_for_deletion   

/**
* Function called by Module class to copy a forum 
*
* @param  int $existing_module_key  key of forum being copied
* @param  int $existing_link_key  link key of forum module being copied
* @param  int $new_module_key  key of forum being created
* @param  int $new_link_key  link key of forum module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of forum module
* @param  int $new_group_key  group key of new forum
* @return true if successful
*/
function copy_forum($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data, $space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	$forum_type = $module_data['forum_type'];
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}forum_settings(module_key,forum_type) VALUES ('$new_module_key','$forum_type')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding copying forum: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}
	
} //end copy_forum


/**
* Function called by Module class to add new Dropbox link
*
* @param  int $module_key  key of forum module
* @return true if successful
*/
function add_forum_link($module_key) {

	return true;

}

/**
* Function to get forum settings
*
* @param  int $module_key  key of forum module
* @return array $forum_settings 
*/
function get_forum_settings($module_key) {

	global $CONN, $CONFIG;
	
	$forum_settings = array();
	$sql = "SELECT forum_type,edit_level, file_path FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE module_key='$module_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$forum_settings['forum_type'] = $rs->fields[0];
		$forum_settings['edit_level'] = $rs->fields[1];
		$forum_settings['file_path'] = $rs->fields[2];
		$rs->MoveNext();
		
	}
	
	$rs->Close();
		
	return $forum_settings;

}


function get_threads($module_key, $parent_key,$space,$sort_order)
{
	global $CONN, $t,$current_user_key,$post_key,$archived,$readposts_array,$is_admin,$space_key, $forum_strings, $CONFIG, $objDates;

	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}

		$objDates = new InteractDate();

	}

	if ($archived==1) {
	
		$archive = "AND (StatusKey='1' OR StatusKey='2')";
		
	} else {
	
		$archive = "AND StatusKey='1'";
		
	}

	$sql = "SELECT post_key, thread_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}post_type.name, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.user_key,parent_key FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}post_type, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.type_key={$CONFIG['DB_PREFIX']}post_type.type_key AND {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND (module_key='$module_key' AND parent_key='$parent_key') $archive ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added $sort_order";

	$rs = $CONN->Execute($sql);
	$record_count=$rs->RecordCount();

	while (!$rs->EOF) {
	
		$current_row=$rs->CurrentRow();
		$pict='tf_out.gif';
		$pictnext='tf_down.gif';
		
		if(++$current_row==$record_count){
			
			$pict='tf_last.gif';
			$pictnext='pix.gif';
		
		}

		$post_key = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$type_key = $rs->fields[2];
		$subject = $rs->fields[3];
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[4]),'short', true);
		$type_name = $rs->fields[5];
		$full_name = $rs->fields[6].' '.$rs->fields[7];
		$user_key = $rs->fields[8];
		$parent_key = $rs->fields[9];

		if ($parent_key==0) {
	
			$class='sandybackground';
		
		} else {
	
			$class='';
		
		} 
		
		if (check_auto_prompting($post_key) === true && ($is_admin==true || $current_user_key==$user_key)) {
		
			$t->set_var("AUTO_PROMPTING","<a href=\"{$CONFIG['PATH']}/modules/forum/threadmanagement.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&parent_key=$parent_key&thread_key=$thread_key\" title=\"".$forum_strings['autoprompting_on']."\">*</a>"); 
			
		} else {
		
			$t->set_var('AUTO_PROMPTING',''); 
		
		}
	
		if (in_array($post_key,$readposts_array)) {
	 
			$t->set_var('POST_CLASS','smallred');
		
		} else {
	
			$t->set_var('POST_CLASS','small');
   
		}

		$t->set_var('SPACE',$space);
		$t->set_var('IMAGE',$pict);
		$t->set_var('CLASS',$class);
		$t->set_var('SUBJECT',$subject);
		$t->set_var('SUBJECT_URL',$subject_url);
		$t->set_var('POST_KEY',$post_key);
		$t->set_var('PARENT_KEY',$parent_key);
		$t->set_var('THREAD_KEY',$thread_key);
		$t->set_var('FULL_NAME',$full_name);
		$t->set_var('FULL_NAME_URL',$full_name_url);
		$t->set_var('DATE_ADDED',$date_added);
		$t->set_var('TYPE',$type_name);
		$t->set_var('USER_KEY',$user_key);
		$t->parse('LIST_POSTS', 'listposts', true);

		get_threads($module_key, $post_key,$space.'<img src="../../images/'.$pictnext.'" width="20" height="20" align="top">',"ASC");
		$rs->MoveNext();
	}
	
	$rs->Close();
	return true;

} //end get_threads

function check_auto_prompting($post_key) {

	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'");
	
	if ($rs->EOF) {
	
		return false;
		
	} else {
	
		return true;
	  
	 
	}
} //end check_auto_prompting

/**
* Function called by auto.php to run any automated functions
*
* @return true if successful
*/

function autofunctions_forum($last_cron) {

	global $CONN, $CONFIG;
	process_subscriptions($last_cron);
	return true;

} //end autofunctions_forum

/**
* Function called by deleteUser to run any functions related to deleting
* a user from the system
*
* @param int $user_key key of user being deleted
* @param int $deleted_user key of deleteduser user account
* @return true if successful
*/

function user_delete_forum($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}posts SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}forum_auto_prompts SET user_key='$deleted_user' WHERE user_key='$user_key'");
		
	return true;

}
/**
* Function called to process any subscriptions to a given forum
* 
* @param date $date_time date and time to process from
* @return true if successful
*/

function process_subscriptions($last_cron) {

	global $CONN, $CONFIG, $forum_strings, $general_strings;
	
	if (!function_exists('email_users')) {
		require_once($CONFIG['BASE_PATH'].'/includes/email.inc.php');
	}
	require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
	
	//$delay_time = time()-1800;
	$delay_time = time()-1;
	$cron_interval = time()-$last_cron;
	$cron_interval = $delay_time-$cron_interval;
	$delay_time = $CONN->DBDate(date('Y-m-d H:i:s',$delay_time));
	$cron_interval = $CONN->DBDate(date('Y-m-d H:i:s',$cron_interval));

	//first see if there are any new posts
	$rs_newposts = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}posts.module_key, post_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key, {$CONFIG['DB_PREFIX']}spaces.Name, {$CONFIG['DB_PREFIX']}modules.Name, {$CONFIG['DB_PREFIX']}posts.subject, {$CONFIG['DB_PREFIX']}posts.body, {$CONFIG['DB_PREFIX']}posts.thread_key, {$CONFIG['DB_PREFIX']}posts.added_by_key, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}posts.date_added FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND ({$CONFIG['DB_PREFIX']}posts.date_added<$delay_time AND {$CONFIG['DB_PREFIX']}posts.date_added>$cron_interval) ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}posts.module_key");
	
	if (!class_exists('InteractForum')) {
		require_once($CONFIG['BASE_PATH'].'/modules/forum/lib.inc.php');		
	}
	$objForum  = new InteractForum();
	
	//now run through new posts and see if anyone subscribed to forum
	while(!$rs_newposts->EOF) {
		$rs_subs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}module_subscription_links WHERE module_key='{$rs_newposts->fields[0]}'");
		if (!$rs_subs->EOF) {
			$user_keys = array();
			while(!$rs_subs->EOF) {
				array_push($user_keys,$rs_subs->fields[0]);
				$rs_subs->MoveNext();
			}
			$rs_subs->Close();
			$post_data['email_subject'] = $CONFIG['SERVER_NAME'].'-'.$forum_strings['new_subscribed_post'].'-'.$rs_newposts->fields[5];
			$post_data['module_key'] 	= $rs_newposts->fields[0];
			$post_data['post_key'] 		= $rs_newposts->fields[1];
			$post_data['space_key'] 	= $rs_newposts->fields[2];
			$post_data['space_name'] 	= $rs_newposts->fields[3];
			$post_data['module_name'] 	= $rs_newposts->fields[4];
			$post_data['post_subject'] 	= $rs_newposts->fields[5];
			$post_data['post_body'] 	= $rs_newposts->fields[6];
			$post_data['thread_key'] 	= $rs_newposts->fields[7];
			$post_data['added_by_key'] 	= $rs_newposts->fields[8];
			$post_data['first_name'] 	= $rs_newposts->fields[9];
			$post_data['last_name'] 	= $rs_newposts->fields[10];
			$post_data['date_added'] 	= $CONN->UnixTimestamp($rs_newposts->fields[11]);
			
			$post_email = $objForum->formatPostEmail($post_data);
			email_users($post_data['email_subject'], $post_email['html'], $user_keys,'','','',$CONFIG['NO_REPLY_EMAIL'],$post_email['plain_text']);
		}
		$rs_newposts->MoveNext();
	}
	$rs_newposts->Close();
		
	return true;

} //end process_subscriptions
?>
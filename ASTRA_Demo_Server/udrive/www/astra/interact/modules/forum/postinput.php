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
* Input post
*
* Input a new post 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: postinput.php,v 1.51 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
	
$space_key 	= get_space_key();

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$action  	= $_GET['action'];
	$subject  	= $_GET['subject'];
	$parent_key	= $_GET['parent_key'];
	$thread_key	= $_GET['thread_key'];
	$entry_key  = isset($_GET['entry_key'])? $_GET['entry_key']:'';
	$referer	= isset($_GET['referer'])? $_GET['referer']:'';						
	
} else {

	$module_key		= $_POST['module_key'];
	$post_key		= $_POST['post_key'];
	$thread_key		= $_POST['thread_key'];
	$body			= $_POST['body'];
	$subject		= $_POST['subject'];
	$type_key		= $_POST['type_key'];		
	$parent_key		= $_POST['parent_key'];
	$show_photo		= $_POST['show_photo'];
	$user_autoprompt= $_POST['user_autoprompt'];   
	$monitor_post	= isset($_POST['monitor_post'])? $_POST['monitor_post']:'';
	$entry_key		= isset($_POST['entry_key'])? $_POST['entry_key']:'';
	$referer		= isset($_POST['referer'])? $_POST['referer']:'';		
	$file_name		= $_FILES['file']['name'];
	$file			= $_FILES['file']['tmp_name']; 
	$referer		= isset($_POST['referer'])? $_POST['referer']:'';
	$email_all		= isset($_POST['email_all'])? $_POST['email_all']:'';		
	$action			= $_POST['action'];
	$submit			= $_POST['submit'];	

}
$current_user_key=$_SESSION['current_user_key'];

$link_key 	= get_link_key($module_key,$space_key);
//check we have the required variables
check_variables(true,false,true);
require_once('lib.inc.php');
$objForum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_strings);
//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

if (!isset($current_user_key)) {

	$request_uri = urlencode($REQUEST_URI);
	$message = urlencode($forum_strings['login_to_post']);
	Header ("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=$request_uri&message=$message");
	exit;
	
} 

$page_details = get_page_details($space_key,$link_key);

// See if the form has been submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Initialize the errors array

	$errors = array();
 
	// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}

	//check to see if we have all the information we need
	if(!$subject) {

		$errors['subject'] = $forum_strings['no_subject'];

	}

	if(!$body) {

		$errors['body'] = $forum_strings['no_message'];

	}


	if(count($errors) == 0) {

	// if we have all the information we need check to see if this is an add or a modify


		switch($action) {

			case add:
			
				$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));
				$message = add_posting();
							
				if ($message=='true') {
				
					//update statistics 
					statistics('post');
					
					if (isset($referer) && $referer!='') {
					
						$message = urlencode($general_strings['comment_added']);
						header("Location: {$CONFIG['FULL_URL']}/modules/$referer?space_key=$space_key&module_key=$module_key&link_key=$link_key&entry_key=$entry_key&message=$message");
						exit;
					
					} else {
					
						$message = urlencode($forum_strings['post_added']);
						get_forum_type($module_key);
					
						if ($forum_type=='embedded') {
					
							if ($parent_module_type=='folder') {
						
								header("Location: {$CONFIG['FULL_URL']}/modules/folder/folder.php?space_key=$space_key&module_key=$parent_module_key&link_key=$parent_key&message=$message");
						
							} else {
					   
								header("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&module_key=$parent_module_key&link_key=$parent_key&message=$message");
							
							}
				
						} else {
					
							header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&message=$message");
						}
			
					}
					
				} 
			
			break;

			case reply2:
			
				$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));
				$message = add_reply();
			
				if ($message=='true') {
				
					$message = urlencode($forum_strings['post_added']);
					//update statistics 
					statistics('post');
					if (isset($referer) && $referer!='') {
					
						$message = urlencode($general_strings['comment_added']);
						header("Location: {$CONFIG['FULL_URL']}/modules/$referer?space_key=$space_key&module_key=$module_key&link_key=$link_key&entry_key=$entry_key&message=$message");
						exit;
					
					} else {
						
						$message = urlencode($forum_strings['post_added']);
						get_forum_type($module_key);
				
						if ($forum_type=='embedded') {
					
							if ($parent_module_type=='folder') {
						
								header("Location: {$CONFIG['FULL_URL']}/modules/folder/folder.php?space_key=$space_key&module_key=$parent_module_key&link_key=$parent_key&message=$message");
					
							} else {
					   
						   		header("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&module_key=$parent_module_key&link_key=$parent_key&message=$message");
							}
				
						} else {
					
							header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&link_key=$parent_key&message=$message");
						}
			
					} 
						
					exit;
					
				}
			
			break;

			case edit:

				switch($submit) {
				
					case $general_strings['delete']:
					
						delete_post($post_key);
						$message = urlencode($general_strings['comment_deleted']);
						header("Location: {$CONFIG['FULL_URL']}/modules/$referer?space_key=$space_key&module_key=$module_key&link_key=$link_key&entry_key=$entry_key&message=$message");
						exit;
					
					break;
					
					case $general_strings['modify']:
				
						$message = edit_post();
				
						if ($message=='true') {
				
						if (isset($referer) && $referer!='') {
					
							$message = urlencode($general_strings['comment_modified']);
							header("Location: {$CONFIG['FULL_URL']}/modules/$referer?space_key=$space_key&module_key=$module_key&link_key=$link_key&entry_key=$entry_key&message=$message");
							exit;
					
						} else {
					
							get_forum_type($module_key);
				
							$message = $forum_strings['post_modified'];
							if ($forum_type=='embedded') {
					
								if ($parent_module_type=='folder') {
						
									header("Location: {$CONFIG['FULL_URL']}/modules/folder/folder.php?space_key=$space_key&module_key=$parent_module_key&link_key=$parent_key&message=$message");
					
								} else {
						
									header("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&module_key=$parent_module_key&link_key=$parent_key&message=$message");
						
								}
						
							} else {
					
								header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key&message=$message");
				
							}
			
						}	 
							
					}
			
					exit;
					
				break;
				
			}
					
			break;

		}

	} else {
		
		$message = $general_strings['problem_below'];
		$button = $general_strings['add'];		
	
	}

}

if (!isset($action)) {
	
	$action = 'add';
	$button = $general_strings['add'];

}




if ($action == 'reply') {

	$button  = $general_strings['add'];
	if (strpos($subject, 'Re:')===false) {
	
		$subject = 'Re: '.$subject;
		
	}
	$action  = 'reply2';
	
} 

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$objHtml = new InteractHtml();

$type_sql = "SELECT Name, type_key FROM {$CONFIG['DB_PREFIX']}post_type ORDER BY Name";
$type_array = array(1  => $forum_strings['post_type_1'],
					2  => $forum_strings['post_type_2'],
					3  => $forum_strings['post_type_3'],					
					4  => $forum_strings['post_type_4'],
					5  => $forum_strings['post_type_5'],
					6  => $forum_strings['post_type_6'],
					7  => $forum_strings['post_type_7'],
					8  => $forum_strings['post_type_8'],
					9  => $forum_strings['post_type_9'],
					11 => $forum_strings['post_type_11'],
					12 => $forum_strings['post_type_12'],
					13 => $forum_strings['post_type_13'],
					14 => $forum_strings['post_type_14'],
					15 => $forum_strings['post_type_15'],
					16 => $forum_strings['post_type_16']);			
asort($type_array);																																																														
				  
$type_menu = $objHtml->arrayToMenu($type_array,'type_key',$type_key, false, 5);

$subject_error = sprint_error($errors['subject']);
$type_error = sprint_error($errors['type_key']);
$body_error = sprint_error($errors['body']);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	  => 'header.ihtml',
	'navigation'  => 'navigation.ihtml',
	'form'		=> 'forums/postinput.ihtml',
	'footer'	  => 'footer.ihtml'));
	
$t->set_block('form', 'PostEditBlock', 'PEBlock');
$t->set_var('PEBlock','');

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//if not a forum post, or nature of post option turned off, then remove nature of post
//sectiion

if ($page_details['module_type_key']!=3) {

	$t->set_var('HEADING',$general_strings['add_comment']);
	$t->set_var('MESSAGE_STRING',$general_strings['comment']);	
	$t->set_block('form', 'AttachmentBlock', 'ATTBlock');
	$t->set_var('ATTBlock','');
	$t->set_block('form', 'post_typeBlock', 'PSTBlock');
	$t->set_var('PSTBlock','');


} else {

	if ($action!='reply2') {
	
		$t->set_var('HEADING',$forum_strings['add_post']);
		
	} else {
	
		$t->set_var('HEADING',$forum_strings['add_reply']);
	
	}
	
	$t->set_var('MESSAGE_STRING',$forum_strings['message']);

}
//get value of for 'photo on' from last post
$rs = $CONN->SelectLimit("SELECT settings FROM {$CONFIG['DB_PREFIX']}posts WHERE user_key='{$_SESSION['current_user_key']}' ORDER BY date_added DESC",1);

while(!$rs->EOF) {
	
	if ($rs->fields[0]=='1') {
	
		$show_photo = 'checked';
				
	}
	
	$rs->MoveNext();
	
}

		//if user has admin rights show thread management link;
		if (check_module_edit_rights($module_key)==true) {
	
			$t->set_var('THREAD_MANAGEMENT',"<a href=\"threadmanagement.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&parent_key=$parent_key&thread_key=$thread_key\">".$forum_strings['autoprompt_settings'].'</a>');
	
			$t->set_var('EMAIL_ALL_STRING', $forum_strings['email_all']);
			
		} else {
		
		    //if not admin then remove email all option
			$t->set_block('form', 'EmailAllBlock', 'EALBlock');
			$t->set_var('EALBlock', '');
					
		
		}
   

$t->set_var('TITLE',$title);
$t->set_var('SUBJECT_ERROR',$subject_error);
$t->set_var('TYPE_ERROR',$type_error);
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('SUBJECT',$subject);
$t->set_var('TYPE_MENU',$type_menu);
$t->set_var('FORUMS_MENU',$forums_menu);
$t->set_var('BODY',$body);
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('POST_KEY',$post_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('ENTRY_KEY',$entry_key);
$t->set_var('THREAD_KEY',$thread_key);
$t->set_var('REFERER',$referer);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('DELETE_STRING',$general_strings['delete']);
$t->set_var('MODIFY_STRING',$general_strings['modify']);
$t->set_var('BACK_TO_STRING',$general_strings['back_to']);
$t->set_var('HOME_STRING',$general_strings['home']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('SUBJECT_STRING',$forum_strings['subject']);
$t->set_var('NATURE_STRING',$forum_strings['nature']);
$t->set_var('PHOTO_STRING',$forum_strings['photo']);
$t->set_var('ADD_PHOTO_STRING',$forum_strings['add_photo']);
$t->set_var('NATURE_STRING',$forum_strings['nature_of_post']);
$t->set_var('ACTIVATE_PROMPTING_SRING',$forum_strings['activate_autoprompting']);
$t->set_var('CHANGE_PARENT_STRING',$forum_strings['change_parent']);
$t->set_var('CHNAGE_THREAD_STRING',$forum_strings['change_thread']);
$t->set_var('POST_NO_STRING',$forum_strings['post_no']);
$t->set_var('PARENT_NO_STRING',$forum_strings['parent_no']);
$t->set_var('THREAD_NO_STRING',$forum_strings['thread_no']);
$t->set_var('FORUMS_STRING',$forum_strings['forums']);		
$t->set_var('CHANGE_PARENT_STRING',$forum_strings['change_parent']);
$t->set_var('CHANGE_THREAD_STRING',$forum_strings['change_thread']);
$t->set_var('CHANGE_FORUM_STRING',$forum_strings['change_forum']);
$t->set_var('NEW_THREAD_STRING',$forum_strings['new_thread']);		
$t->set_var('CHANGE_FORUM_STRING',$forum_strings['change_forum']);
$t->set_var('ATTACHMENT_STRING',$forum_strings['attachment']);
$t->set_var('CONFIRM_STRING',$general_strings['check']);
$t->set_var('MONITOR_POST_STRING',$forum_strings['monitor_post']);
$t->set_var('YOUR_REPLY_STRING',$forum_strings['your_reply']);

if (isset($monitor_post) && $monitor_post==1) {

	$t->set_var('MONITOR_POST_CHECKED','checked');

}

$t->set_var('SHOW_PHOTO',$show_photo);										
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);

//if replying get parent post details

if($action=='reply2') {

	//get parent post data
	$post_data = $objForum->getPostData($parent_key);
	$t->set_var('POSTED_BY_STRING',$forum_strings['posted_by']);
	$t->set_var('SUBJECT_STRING',$general_strings['subject']);
	$t->set_var('ON_STRING',$general_strings['on']);
	$t->set_var('AT_STRING',$general_strings['at']);
	$t->set_var('FULL_NAME',$post_data['added_by']);
	$t->set_var('PARENT_BODY',$post_data['body']);
	$t->set_var('PARENT_SUBJECT',$post_data['subject']);
	

} else {

	$t->set_block('form', 'ParentPostBlock', 'PPBlock');
	$t->set_var('PPBlock', '');
	
}


//generate the editor components

$objHtml->setTextEditor($t, $_SESSION['auto_editor'], 'body');


$t->parse('CONTENTS', 'header', true); 
get_navigation();

//see if user should have autoprompting option

$rs = $CONN->Execute("SELECT auto_prompting FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE module_key='$module_key'");

while(!$rs->EOF) {
	
	$allow_prompting = $rs->fields[0];
	$rs->MoveNext();
	
}

if (!isset($allow_prompting) || $allow_prompting!=2) {

	$t->set_block('form', 'AutoPromptBlock', 'APBlock');
	$t->set_var('APBlock', '');
	
}

$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);

$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Add a posting to a Forum  
* 
*  
* @return true
*/
function add_posting()
{
	global $CONN, $current_user_key, $date_added, $subject,$body, $type_key, $module_key, $space_key,$show_photo,$user_autoprompt, $CONFIG, $file, $file_name, $monitor_post, $entry_key, $email_all;
	
	
$full_name_esc = $CONN->qstr($full_name); 
	$user_email_esc = $CONN->qstr($user_email);
	$subject_esc = $CONN->qstr(strip_tags($subject,"<a>,<i>,<b>")); 
	$body_esc = $CONN->qstr(strip_tags($body,"<a>,<i>,<b>,<img>,<p>,<font>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<table>,<tr>,<td>,<ul>,<ol>,<li>,<strong>,<em>,<sup>,<sub>,<blockquote>,<br>,<span>,<hr>")); 
	
	if (!$show_photo) {
	
		$show_photo='0';
	
	}
	
	$sql = "INSERT into {$CONFIG['DB_PREFIX']}posts(module_key, entry_key, type_key, added_by_key, subject, body, date_added, status_key, settings)  VALUES ('$module_key','$entry_key','$type_key','$current_user_key',$subject_esc,$body_esc,$date_added,'1','$show_photo')";
	
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error adding your Posting: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {
		
	$post_key = $CONN->Insert_ID();
		
	//if user wants notification add to post_user_links table
		
		if (isset($monitor_post) && $monitor_post==1) {

		$CONN->Execute("INSERT into {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, monitor_post) VALUES ('$module_key','$post_key','$current_user_key','$monitor_post')");

		}

		//if there is an attachment move to forum directory, etc.
				
	if($file_name!='' && $file_name!='none') {
	
		$file_name=ereg_replace("[^a-z0-9A-Z._]","",$file_name);
			$file_name = substr($file_name,-20);
		$file_name=str_replace("php","html",$file_name);
		$file_name = $post_key.$file_name;
			
		//get forum file path
		$sql = "select file_path from {$CONFIG['DB_PREFIX']}forum_settings where module_key='$module_key'";

			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {

				$file_path = $rs->fields[0];
				$rs->MoveNext();
		
			}
			$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$file_path;
			copy($file,$directory_path.'/'.$file_name);
		
		}
		
		$sql="UPDATE {$CONFIG['DB_PREFIX']}posts SET thread_key='$post_key', attachment='$file_name' WHERE post_key='$post_key'";
		
		if ($CONN->Execute($sql) === false) {
			
			$message =  'There was an error adding your Posting: '.$CONN->ErrorMsg().' <br />';
			return $message;
		
		} 
		
		add_auto_prompting($post_key,$module_key,$user_autoprompt);
		
		if (isset($email_all) && $email_all==1) {
		
			email_all($subject, $body, $_FILES['file']);
		
		}
		return true;
		  
	}

}

/**
* Add a reply to the database  
* 
*  
* @return true
*/
function add_reply()
{
	
	global $CONN, $current_user_key, $date_added, $subject,$body, $type_key,$parent_key, $module_key,$thread_key,$module_key, $space_key,$show_photo,$user_autoprompt, $CONFIG,  $file, $file_name, $monitor_post, $forum_strings, $entry_key, $objForum;
	
	$full_name_esc = $CONN->qstr($full_name); 
	$user_email_esc = $CONN->qstr($user_email);
	$subject_esc = $CONN->qstr(strip_tags($subject,"<a>,<i>,<b>")); 
	$body_esc = $CONN->qstr(strip_tags($body,"<a>,<i>,<b>,<img>,<p>,<font>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<table>,<tr>,<td>,<ul>,<ol>,<li>,<strong>,<em>,<sup>,<sub>,<br>,<blockquote>,<span>,<hr><div>"));
	
	if (!$show_photo) {
		
		$show_photo='0';
	
	}
	
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}posts(module_key, parent_key, thread_key, entry_key, type_key, added_by_key, subject, body, date_added, status_key, settings) VALUES ('$module_key','$parent_key','$thread_key','$entry_key','$type_key','$current_user_key',$subject_esc,$body_esc,$date_added,'1','$show_photo')";

	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error adding your Posting: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {
		
		$post_key = $CONN->Insert_ID();
	
	//if user wants notification add to post_user_links table
		
		if (isset($monitor_post) && $monitor_post==1) {

			$CONN->Execute("INSERT into {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, monitor_post) VALUES ('$module_key','$post_key','$current_user_key','$monitor_post')");

		}


	//if there is an attachment move to forum directory, etc.
				
		if($file_name!='' && $file_name!='none') {
	
			$file_name=ereg_replace("[^a-z0-9A-Z._]","",$file_name);
			$file_name=str_replace("php","html",$file_name);
			$file_name = substr($file_name,-20);
			$file_name = $post_key.$file_name;
			
			//get forum file path
			$sql = "select file_path from {$CONFIG['DB_PREFIX']}forum_settings where module_key='$module_key'";

			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {

				$file_path = $rs->fields[0];
				$rs->MoveNext();
		
			}
			$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$file_path;
			copy($file,$directory_path.'/'.$file_name);
			
		   $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}posts SET attachment='$file_name' where post_key='$post_key'");
		
		}
		
		$objForum->emailPostMonitors($parent_key, $post_key);
		add_auto_prompting($post_key,$module_key,$user_autoprompt);
		return true;
		  
	}

} //end add_reply()

/**
* Edit a post  
* 
*  
* @return true
*/
function edit_post()
{
	
	global $CONN, $subject,$body, $type_key,$post_key,$show_photo,$user_autoprompt,$module_key, $CONFIG, $file, $file_name, $monitor_post;
	
	$subject_esc = $CONN->qstr($subject); 
	$body_esc = $CONN->qstr($body);
	$subject_esc = $CONN->qstr(strip_tags($subject,"<a>,<i>,<b>")); 
	$body_esc = $CONN->qstr(strip_tags($body,'<a>,<i>,<b>,<img>,<p>,<font>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<table>,<tr>,<td>,<ul>,<ol>,<li>,<strong>,<em>,<sup>,<sub>,<br>,<blockquote>,<strong>,<span>,<hr>,<div>'));
	
	if (!$show_photo) {
	
		$show_photo='0';
	
	}
	
	//if there is an attachment move to forum directory, etc.
				
	if($file_name!='' && $file_name!='none') {
	
		$file_name=ereg_replace("[^a-z0-9A-Z._]","",$file_name);
		$file_name=str_replace("php","html",$file_name);
		$file_name = substr($file_name,-20);
		$file_name = $post_key.$file_name;
			
		//get forum file path and existing existing attachment name
		$sql = "SELECT {$CONFIG['DB_PREFIX']}forum_settings.file_path, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}forum_settings,  {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$post_key'";

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
		
		$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$file_path;
		copy($file,$directory_path.'/'.$file_name);
			

	$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET subject=$subject_esc,body=$body_esc,type_key='$type_key',settings='$show_photo', attachment='$file_name' WHERE post_key='$post_key'";

	} else {
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET subject=$subject_esc,body=$body_esc,type_key='$type_key',settings='$show_photo' WHERE post_key='$post_key'";
	
	}
		
	
	
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error editing your Posting: '.$CONN->ErrorMsg().' <br />';
		return $message;
	
	} else {
		
 		//if user wants notification add to post_user_links table
		
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='$post_key' AND user_key='{$_SESSION['current_user_key']}'");
		
		if (isset($monitor_post) && $monitor_post==1) {

			if ($rs->EOF) {
			
				$CONN->Execute("INSERT into {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, monitor_post) VALUES ('$module_key','$post_key','{$_SESSION['current_user_key']}','$monitor_post')");
				
			} else {
			
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET monitor_post='1' WHERE post_key='$post_key' AND user_key='{$_SESSION['current_user_key']}'");
				
			}
			

		} else {
		
			if (!$rs->EOF) {
			
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET monitor_post='0' WHERE post_key='$post_key' AND user_key='{$_SESSION['current_user_key']}'");
			
			}
				
		}
		
		if ($user_autoprompt==true) {
		
			add_auto_prompting($post_key,$module_key,$user_autoprompt);
			
		}
		
		return true;
		  
	}

} //end edit_reply()

function get_forum_type($module_key)
{
	
	global $CONN,$parent_key, $group_key,$forum_type,$parent_module_key,$parent_module_type, $CONFIG;
	
	$sql = "SELECT forum_type,parent_key,group_key FROM {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}forum_settings WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}forum_settings.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key='$module_key'";
	
	$rs=$CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$forum_type = $rs->fields[0];
		$parent_key = $rs->fields[1];
		$group_key = $rs->fields[2];
		$rs->MoveNext(); 
	
	}
	
	$rs->Close();
	
	//get parent module_key
	
	$sql = "SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}modules.type_code From {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$parent_key'";
		
	$rs=$CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$parent_module_key = $rs->fields[0];
		$parent_module_type = $rs->fields[1];		
		$rs->MoveNext(); 
	
	}
	
	$rs->Close();

}

function add_auto_prompting($post_key,$module_key,$user_autoprompt=false) {

	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT auto_prompting, days_to_wait, number_to_prompt, passes_allowed, response_time, minimum_replies FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE module_key='$module_key'");

	while(!$rs->EOF) {
	
		if ($rs->fields[0]=='1' || ($rs->fields[0]=='2' && $user_autoprompt==true)) {
		
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}forum_thread_management(post_key,days_to_wait,number_to_prompt,passes_allowed,response_time,_minimum_replies) VALUES ('$post_key','{$rs->fields[1]}','{$rs->fields[2]}','{$rs->fields[3]}','{$rs->fields[4]}','{$rs->fields[5]}')");
			
		
		}
		
		$rs->MoveNext();
		
	}	
	
}

function delete_post($post_key) {


		global $CONN, $CONFIG;
		//first delete any attachments
		//get forum file path and existing existing attachment name
		$sql = "SELECT {$CONFIG['DB_PREFIX']}forum_settings.file_path, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}forum_settings,  {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$post_key'";

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
		
		if ($post_key==$thread_key) {
	
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE thread_key='$post_key'";
			$rs = $CONN->Execute($sql);
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}read_posts WHERE post_key='$post_key'";
			$CONN->Execute($sql);

		} else {
 
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'";
			$rs = $CONN->Execute($sql);
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}read_posts WHERE post_key='$post_key'";
			$CONN->Execute($sql);
			delete_children($post_key);

		}
		
		return true;

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
		
		$sql2="DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$child_post_key'";
		$CONN->Execute($sql2);
	
		$sql3 = "DELETE FROM {$CONFIG['DB_PREFIX']}read_posts WHERE post_key='$child_post_key'";
		$CONN->Execute($sql3);
		delete_children($child_post_key);
		$rs->MoveNext();
	
	}
	
	return true;
	
} //end delete_children

function email_all($subject, $body, $attachment) {

	global $CONFIG, $CONN, $group_key, $space_key;
	
	if (isset($group_key) && $group_key!='0') {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND (group_key='$group_key' AND {$CONFIG['DB_PREFIX']}users.user_key!='$current_user_key')";
		
	} else {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND (space_key='$space_key' AND {$CONFIG['DB_PREFIX']}users.user_key!='$current_user_key')";
	
	}
	
	$member_keys = get_userkey_array($sql);
	require_once($CONFIG['BASE_PATH'].'/includes/email.inc.php');
	email_users($subject, $body, $member_keys, '', $attachment);
	
	return true;

}
?>
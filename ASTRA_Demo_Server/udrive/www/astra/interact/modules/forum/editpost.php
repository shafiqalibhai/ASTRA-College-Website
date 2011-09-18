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
* Edit post
*
* Various functions for moving/modifying posts 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: editpost.php,v 1.35 2007/07/30 01:56:59 glendavies Exp $
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
	$post_key	= $_GET['post_key'];
	$entry_key	= $_GET['entry_key'];	
	$action  	= $_GET['action'];
	
} else {

	$module_key		 = $_POST['module_key'];
	$new_module_key	 = $_POST['new_module_key'];	
	$post_key		 = $_POST['post_key'];
	$thread_key		 = $_POST['thread_key'];
	$thread_key2	 = $_POST['thread_key2'];
	$parent_key	  = $_POST['parent_key'];
	$entry_key	  = $_POST['entry_key'];				
	$action  		 = $_POST['action'];

}

$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

switch($action) {
	
	case $forum_strings['change_parent']:
		
		//make sure not moving parent to child or lower down in same thread
		check_is_below($parent_key,$post_key);
		
		if ($is_below=='true') {
		
			  errors($forum_strings['below_self']);
		
		}
 
		$sql = "SELECT ThreadKey FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$parent_key' and module_key='$module_key'";
		$rs = $CONN->Execute($sql);
		
		if ($rs->EOF) {
		
		   errors($forum_strings['no_parent']);
		
		} else { 
			
			while (!$rs->EOF) {
				
				$thread_key = $rs->fields[0];
				$rs->MoveNext();
			
			}
			
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET parent_key='$parent_key',ThreadKey='$thread_key' WHERE post_key='$post_key'";
			$rs = $CONN->Execute($sql);
			update_children($post_key, $thread_key);
			
			header("Location: {$CONFIG['FULL_URL']}/modules/forum/thread.php?space_key=$space_key&module_key=$module_key&thread_key=$thread_key");
			exit;
		
		}
		
	break;
	
	case $forum_strings['change_thread']:
	
		if ($parent_key==0 && $post_key!=$thread_key2) {
	
			$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE ThreadKey='$thread_key2'";
			$rs = $CONN->Execute($sql);
		
			if ($rs->EOF) {
			
				errors($forum_strings['no_thread']);
		
			} else {
		 
				$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET ThreadKey='$thread_key2',parent_key='$thread_key2' WHERE post_key='$post_key'";
		
			}
	
		} else if ($post_key==$thread_key2) {
	 
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET ThreadKey='$thread_key2',parent_key='0' WHERE post_key='$post_key'";
	
		} else {
	
			$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE ThreadKey='$thread_key2'";
			$rs = $CONN->Execute($sql);
		
			if ($rs->EOF) {
			
				errors($forum_strings['no_thread']);
			
			} else { 
			
				$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$parent_key' AND ThreadKey='$thread_key2'";
				$rs = $CONN->Execute($sql);
			
				if (!$rs->EOF) {
			
					$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET ThreadKey='$thread_key2' WHERE post_key='$post_key'";
			
				} else {
			
					$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts SET ThreadKey='$thread_key',parent_key='$thread_key2' WHERE post_key='$post_key'";
			
				}
		
			}
	
		}
	
		$rs = $CONN->Execute($sql);
		update_children($post_key, $thread_key2);
		header("Location: {$CONFIG['FULL_URL']}/modules/forum/thread.php?space_key=$space_key&module_key=$module_key&thread_key=$thread_key2");
		exit;
	
	break;

	case $forum_strings['change_forum']:
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}posts set module_key='$new_module_key', thread_key='$post_key',parent_key='0' where post_key='$post_key'";
		$CONN->Execute($sql);
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}post_user_links set module_key='$new_module_key' where post_key='$post_key'";
		$CONN->Execute($sql);		
		update_children($post_key, $post_key, $new_module_key);
		header("Location: {$CONFIG['FULL_URL']}/modules/forum/thread.php?space_key=$space_key&module_key=$new_module_key&thread_key=$post_key");
		exit;
		
	break;
	
	case $general_strings['delete']:
	
		
		//first delete any attachments
		//get forum file path and existing existing attachment name
		$sql = "SELECT {$CONFIG['DB_PREFIX']}forum_settings.FilePath, {$CONFIG['DB_PREFIX']}posts.Attachment FROM {$CONFIG['DB_PREFIX']}forum_settings,  {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$post_key'";

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
	
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE ThreadKey='$post_key'";
			$rs = $CONN->Execute($sql);
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}ReadPosts WHERE post_key='$post_key'";
			$CONN->Execute($sql);
			header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
			exit;

		} else {
 
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'";
			$rs = $CONN->Execute($sql);
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}ReadPosts WHERE post_key='$post_key'";
			$CONN->Execute($sql);
			delete_children($post_key);

			header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?space_key=$space_key&module_key=$module_key");
			exit;
		}
		
	break;
	
	case $general_strings['edit']:
	
		$title = $forum_strings['edit_post'];
		$sql = "SELECT post_key, thread_key, parent_key, subject, body,type_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'";
		$rs = $CONN->Execute($sql);
	
		while (!$rs->EOF) {

			$post_key = $rs->fields[0];
			$thread_key = $rs->fields[1];
			$parent_key = $rs->fields[2];
			$subject = $rs->fields[3];
			$body = $rs->fields[4];
			$type_key = $rs->fields[5];
			$rs->MoveNext();
		}
		
		$rs->Close();
	
		$forums_sql = "SELECT name, {$CONFIG['DB_PREFIX']}modules.module_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND type_code='forum' AND space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' ORDER BY name";

		if (!class_exists('InteractHtml')) {

			require_once('../../includes/lib/html.inc.php');
	
		}
		$objHtml = new InteractHtml();
		
		$forums_menu = make_menu($forums_sql,'new_module_key',$module_key,'2');
		
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
		
		require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
		$t = new Template($CONFIG['TEMPLATES_PATH']);  
		$t->set_file(array(
			'header'	  => 'header.ihtml',
			'form'		=> 'forums/postinput.ihtml',
			'navigation'  => 'navigation.ihtml',
			'footer'	  => 'footer.ihtml'));
			
		$page_details = get_page_details($space_key,$link_key);
		//if not a forum post, or nature of post option turned off, then remove nature of post
//sectiion

		$t->set_block('form', 'ParentPostBlock', 'PPBlock');
		$t->set_var('PPBlock', '');

		if ($page_details['module_type_key']!=3) {

			$t->set_var('HEADING',$general_strings['add_comment']);
			$t->set_var('MESSAGE_STRING',$general_strings['comment']);	
			$t->set_block('form', 'AttachmentBlock', 'ATTBlock');
			$t->set_var('ATTBlock','');
			$t->set_block('form', 'post_typeBlock', 'PSTBlock');
			$t->set_var('PSTBlock','');
 			$t->set_block('form', 'AutoPromptBlock', 'APBlock');
			$t->set_var('APBlock', '');
 			$t->set_block('form', 'PostEditBlock', 'PEDBlock');
			$t->set_var('PEDBlock', '');						

		} else {

 			$t->set_block('form', 'DeleteBlock', 'DLBlock');
			$t->set_var('DLBlock', '');
			$t->set_var('HEADING',$forum_strings['add_post']);
			$t->set_var('MESSAGE_STRING',$forum_strings['message']);

		}
		set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);		
	$t->parse('CONTENTS', 'forumheader', true);
		//if user has admin rights show thread management link;
		if (check_module_edit_rights($module_key)==true) {
	
			$t->set_var('THREAD_MANAGEMENT',"<a href=\"threadmanagement.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&parent_key=$parent_key&thread_key=$thread_key\">".$forum_strings['autoprompt_settings'].'</a>');
			$t->set_var('EMAIL_ALL_STRING', $forum_strings['email_all']);
	
		}	else {
		
		    //if not admin then remove email all option
			$t->set_block('form', 'EmailAllBlock', 'EALBlock');
			$t->set_var('EALBlock', '');
					
		
		}
   
		
		$t->set_var('HEADING',$forum_strings['edit_post']); 
		$t->set_var('REFERER',$_GET['referer']); 
		$t->set_var('TITLE',$title); 
		$t->set_var('SUBJECT',$subject);
		$t->set_var('TYPE_MENU',$type_menu);
		$t->set_var('FORUMS_MENU',$forums_menu);
		$t->set_var('BODY',$body);
		$t->set_var('PARENT_KEY',$parent_key);
		$t->set_var('POST_KEY',$post_key);
		$t->set_var('SPACE_KEY',$space_key);
		$t->set_var('MODULE_KEY',$module_key);
		$t->set_var('THREAD_KEY',$thread_key);
		$t->set_var('ENTRY_KEY',$entry_key);		
		$t->set_var('CANCEL_STRING',$general_strings['cancel']);
		$t->set_var('DELETE_STRING',$general_strings['delete']);
		$t->set_var('MODIFY_STRING',$general_strings['modify']);
		$t->set_var('BACK_TO_STRING',$general_strings['back_to']);
		$t->set_var('HOME_STRING',$general_strings['home']);
		$t->set_var('MODULE_NAME',$page_details['module_name']);
		$t->set_var('SUBJECT_STRING',$forum_strings['subject']);
		$t->set_var('NATURE_STRING',$forum_strings['nature']);
		$t->set_var('MESSAGE_STRING',$forum_strings['message']);
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
		
		$objHtml->setTextEditor($t, $_SESSION['auto_editor'], 'body');
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='$post_key' AND user_key='{$_SESSION['current_user_key']}' AND monitor='1'");
		
		if (!$rs->EOF) {

			$t->set_var('MONITOR_POST_CHECKED','checked');

		}
									
		$t->set_var('ACTION','edit');
		$t->set_var('BUTTON',$general_strings['modify']);
	
		//get value of for 'photo on' 
		$rs = $CONN->Execute("SELECT settings FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'");

		while(!$rs->EOF) {
	
			if ($rs->fields[0]=='1') {

				$show_photo = 'checked';	
		
			}
	
			$rs->MoveNext();
	
		}
		
		$t->set_var('SHOW_PHOTO',$show_photo);	
  
		
	
		$rs = $CONN->Execute("SELECT auto_prompting FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE module_key='$module_key'");

		while(!$rs->EOF) {
	
			if ($rs->fields[0]!='2') {
	
				$t->set_block('form', 'AutoPromptBlock', 'APBlock');
				$t->set_var('APBlock', '');
		
			} 

			$rs->MoveNext();
	
		}
		$t->parse('CONTENTS', 'header', true); 
		get_navigation();
		$t->parse('CONTENTS', 'form', true);

		$t->parse('CONTENTS', 'footer', true);
		print_headers();
		$t->p('CONTENTS');

		exit;
	   $CONN->Close();
	
	break;
} //end switch($action)

function delete_children($post_key) 
{   
	global $CONN, $CONFIG;
	
	$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$child_post_key = $rs->fields[0];
		//first delete any attachments
		//get forum file path and existing existing attachment name
		$sql = "SELECT {$CONFIG['DB_PREFIX']}forum_settings.FilePath, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}forum_settings, {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}forum_settings.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$child_post_key'";

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
	
		$sql3 = "DELETE FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='$child_post_key'";
		$CONN->Execute($sql3);
		delete_children($child_post_key);
		$rs->MoveNext();
	
	}

} //end delete_children

function update_children($post_key, $thread_key,$new_module_key="") 
{   
	global $CONN, $CONFIG;
	
	$sql="SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		$child_post_key = $rs->fields[0];
	
		if ($new_module_key=='') {
		
			$sql2="UPDATE {$CONFIG['DB_PREFIX']}posts SET thread_key='$thread_key' WHERE post_key='$child_post_key'";
	
		} else {
 
			$sql2="UPDATE {$CONFIG['DB_PREFIX']}posts SET thread_key='$thread_key', module_key='$new_module_key' WHERE post_key='$child_post_key'";
			$sql3 = "UPDATE {$CONFIG['DB_PREFIX']}post_user_links set module_key='$new_module_key' where post_key='$child_post_key'";
			$CONN->Execute($sql3);
		}
	
		$CONN->Execute($sql2);

		update_children($child_post_key,$thread_key,$new_module_key);
		$rs->MoveNext();
	
	}

} //end update children

function check_is_below($parent_key,$post_key) 
{
	global $CONN, $is_below , $CONFIG;
	
	$sql = "SELECT parent_key FROM {$CONFIG['DB_PREFIX']}posts where post_key='$parent_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		
		$parent_key2 = $rs->fields[0];
		
		if ($parent_key2==$post_key) {
		
			$is_below = true;
			return;
		
		} else {
			
			if ($parent_key2=="0") {
			
				return;
			
			} else {
			
				check_is_below($parent_key2,$post_key);
			
			}
		}
		
	$rs->MoveNext();
	
	}

} //end check_is_below

function errors($message) 
{ 
	
	global $CONFIG;

	$HTTP_REFERER = $_SERVER['HTTP_REFERRER'];
	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array(
		'header' => 'header.ihtml',
		'errors' => 'errors.ihtml',
 		'footer' => 'footer.ihtml'
	));

	
	$t->set_var('PATH',$CONFIG['PATH']);
	$t->set_var('BACK',$HTTP_REFERER);
	$t->set_var('TITLE','Interact - Error');
	$t->set_var('MESSAGE',$message);	
	$t->parse('CONTENTS', 'header', true); 
	$t->parse('CONTENTS', 'errors', true);
	$t->parse('CONTENTS', 'footer', true);
	$t->p('CONTENTS');
	exit;

} //end errors
?>
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
* Comment input
*
* Input a new comment in a module that allows comments
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: commentinput.php,v 1.22 2007/07/16 03:20:41 websterb4 Exp $
* 
*/

$no_safehtml=array(); // safehtml everything in comment.
/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {
	foreach($_GET as $key => $value) $post_data[$key] = $value;
 	$module_key = $_GET['module_key'];
 	$action     = isset($_GET['action']) ? $_GET['action'] : '';	
} else if($_SERVER['REQUEST_METHOD']=='POST'){
	foreach($_POST as $key => $value) $post_data[$key] = $value;
	$module_key = $_POST['module_key'];
 	$action     = isset($_POST['action']) ? $_POST['action'] : '';
}

$space_key 	= get_space_key();

$current_user_key=$_SESSION['current_user_key'];
$link_key 	= get_link_key($module_key,$space_key);
//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

$unauth=false;

// if not logged in && it's a journal && not-logged-in comments allowed, 
//	then set current_user_key to the special unauthorised user.
if (!isset($current_user_key) && $CONN->GetOne("SELECT type_code FROM {$CONFIG['DB_PREFIX']}modules WHERE  module_key=$module_key")=='journal' && ($CONN->GetOne("SELECT options FROM {$CONFIG['DB_PREFIX']}journal_settings WHERE module_key=$module_key")&64) && ($_POST['submit'] == $general_strings['add'])) {
	$current_user_key=$CONN->GetOne("SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='_interact_unauth'");
	$unauth=true;
} 

if (!isset($current_user_key)) {
	$request_uri = urlencode($_SERVER['REQUEST_URI']);
	require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
	
	$message = urlencode($forum_strings['login_to_post']);
	header("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=$request_uri&message=$message");
	exit;
}


if (!isset($objPosts) || !is_object($objPosts)) {
	if (!class_exists('InteractPosts')) {
		require_once('../../includes/lib/posts.inc.php');
	}
	$objPosts = new InteractPosts();
	$objPosts->setVars($module_key, $space_key);
}
// See if the form has been submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

	if($_POST['KABLOOEY']===md5($_COOKIE['PHPSESSID'])) {
		
		
		switch($_POST['submit']) {
		
			case $general_strings['add']:
				$post_data['added_by_key'] = $current_user_key;	
				$post_data['user_key']     = !empty($post_data['user_key'])?$post_data['user_key']:$current_user_key;
				$post_data['status_key'] = 1;
				
				$fields=array('module_key', 'parent_key','thread_key','body','user_key','added_by_key', 'date_added', 'subject', 'date_published', 'status_key');
				
				if($unauth) {
					$unauth_fields=array('unauth_name','unauth_email','unauth_url');
					foreach($unauth_fields as $k) {
						$post_data[$k]=strip_tags($post_data[$k]);
						array_push($fields,$k);
					}
				}
				
				$message = $objPosts->addPost($fields,$post_data);
				
				header('Location: '.$CONFIG['SERVER_URL'].urldecode($post_data['referer']));
				exit;
			break;
			case $general_strings['modify']:
				$post_data['modifed_by_key'] = $current_user_key;	
				$message = $objPosts->modifyPost(array('body','modified_by_key','date_modifed', 'subject'),$post_data);
				header('Location: '.$CONFIG['SERVER_URL'].urldecode($post_data['referer']));
			break;
			case $general_strings['delete']:
				$message = $objPosts->deletePost($post_data);
				$referer = urldecode($post_data['referer']);
				header('Location: '.$CONFIG['SERVER_URL'].urldecode($post_data['referer']));
			break;
		}
	} else {header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=".urlencode($general_strings['need_cookies_to_post'].'.'));}

}
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "header.ihtml",
	"navigation"	  => "navigation.ihtml",
	"comments"		 => "modules/comments.ihtml",
	"footer"		  => "footer.ihtml"
));
// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
$t->set_var('KABLOOEY',md5($_COOKIE['PHPSESSID']));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
if (!isset($objHtml) || !is_object($objHtml)){
	if (!class_exists('InteractHtml')) {
		require_once('../../includes/lib/html.inc.php');
	}
	$objHtml= new InteractHtml();
}
$objHtml->setTextEditor($t, $_SESSION['auto_editor'], 'body');
$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->set_var('COMMENT_INPUT_HEADING', $general_strings['reply_to_comment']);
$t->set_var('MODULE_KEY', $module_key);
$t->set_var('SPACE_KEY', $space_key);
$t->set_var('PARENT_KEY', $post_data['parent_key']);
$t->set_var('POST_KEY', $post_data['post_key']);
$t->set_var('THREAD_KEY', $post_data['thread_key']);
$t->set_var('REFERER', $post_data['referer']);
$t->set_var('USER_KEY', isset($post_data['user_key'])?$post_data['user_key']:'');
$t->set_block('comments', 'ReplyBlock', 'RepBlock');
switch($action) {
	case 'Reply':
	
		$t->set_block('comments','CommentNotLoggedInBlock','CNLBlock');$t->set_var('CNLBlock',' ');

		$t->set_block('comments', 'DeleteBlock', 'DelBlock');
		$t->set_var('ADD_COMMENT', $general_strings['reply']);
		$t->set_var('SUBMIT_BUTTON', $general_strings['add']);
		$limits = array('module_key' => $module_key, 'post_key' => $post_data['parent_key']);
		$parent_data = $objPosts->getPostData($limits,true);
		$objPosts->formatPost($parent_data);

	break;
		
	case 'Modify':

	$t->set_block('comments','CommentNotLoggedInBlock','CNLBlock');$t->set_var('CNLBlock',' ');

		$t->set_var('ADD_COMMENT', $general_strings['modify_comment']);
		$t->set_var('SUBMIT_BUTTON', $general_strings['modify']);
		$limits = array('module_key' => $module_key, 'post_key' => $post_data['post_key']);
		$post_data = $objPosts->getPostData($limits,true);	
		$t->set_block('comments', 'CommentBlock', 'CmmntBlock');
	break;
}					

//now set values for any undefined variables in templates
$t->set_strings('comments',  $journal_strings, $post_data[0], $errors);

$t->parse('CONTENTS', 'comments', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	

?>
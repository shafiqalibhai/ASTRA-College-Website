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
* Displays form for inputting a comment in a ahring module 
*
* @package Sharing
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: commentinput.php,v 1.23 2007/07/30 01:57:05 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/sharing_strings.inc.php');

//set variables
if ($_SERVER['REQUEST_METHOD']=='POST') {
	
	
	$module_key	 = $_POST['module_key'];
	$shareditem_key = $_POST['shareditem_key'];		
	$parent_key	 = $_POST['parent_key'];
	$shareditem_key = $_POST['shareditem_key'];
	$subject		= $_POST['subject'];
	$body		   = $_POST['body'];				
	$action		 = $_POST['action'];   		
	
} else {
 
	$module_key	 = $_GET['module_key'];
	$shareditem_key = $_GET['shareditem_key'];
	$parent_key	 = $_GET['parent_key'];			
	$subject		= urldecode($_GET['subject_url']);	
}
$current_user_key = $_SESSION['current_user_key'];
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];


if (!isset($_SESSION['current_user_key'])) {

	$request_uri = urlencode($_SERVER['REQUEST_URI']);
	$message = urlencode($sharing_strings['login_to_post']);
	Header ("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=$request_uri&message=Sorry!$message");
	exit;

}
 
// See if the form has been submitted

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Initialize the errors array

	$errors = array();

	// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}

	//check to see if we have all the information we need
	
	if(!$_POST['subject']) {

		$errors['subject'] = $general_strings['no_subject'];

	}
	if(!$_POST['body']) {

		$errors['body'] = $sharing_strings['no_comment'];

	}


	if(count($errors) == 0) {

	// if we have all the information we need check to see if this is an add or a modify

		switch($_POST['action']) {

			case add:
			
				$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			
				$message = add_comment();
			
				if ($message=='true') {
				
					//update statistics 
					statistics('post');
					$message = urlencode($sharing_strings['comment_success']);
					Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");
			
				}  
			
			break;

			case reply2:
			
				$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
				$message = add_reply();
				
				if ($message=='true') {
				
				//update statistics 
				statistics('read');
				$message = urlencode($sharing_strings['comment_success']);				
				Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");
				}
				 
			break;

			case edit:
			
				$message = edit_comment();
			
				if ($message=='true') {
				
					$message = urlencode($sharing_strings['comment_changed']);
					Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");
				} 
			
			break;

		}

	} else {
	
		$message = $general_strings['problem_below'];
		
	}

}

if (!isset($_POST['action'])) {

	$action = 'add';
	$title = $sharing_strings['add_comment'];
	$full_name = "$current_user";
	$user_email = "$current_user_email";
	$button = $general_strings['add'];
	
}

if ($_GET['action']=='reply_quoted') {

	$rs=$CONN->Execute("SELECT body FROM {$CONFIG['DB_PREFIX']}shared_item_comments WHERE comment_key='$parent_key'");
	
	while (!$rs->EOF) {

		$body = $rs->fields[0];
		$body = wordwrap($body,32,"\n> ");
		$body = ">" . $body;
		$rs->MoveNext(); 
	
	}
	
	$rs->Close();
	$title   = $sharing_strings['reply_to_comment'];
	$subject = 'Re: '.$subject;
	$button  = $general_strings['reply'];
	$action  ='reply2';
}

if ($_GET['action'] == 'reply') {

	$title	  = $sharing_strings['reply_to_comment'];
	$full_name  = $current_user;
	$user_email = $current_user_email;
	$subject	= 'Re: '.$subject;
	$button	 = $general_strings['reply'];
	$action	 = 'reply2';

} 

$subject_error = sprint_error($errors['subject']);
$body_error = sprint_error($errors['body']);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'sharingheader'   => 'sharing/sharingheader.ihtml',
	'form'			=> 'sharing/commentinput.ihtml',
	'footer'		  => 'footer.ihtml'));

$sql = "SELECT name,description, first_name, last_name, url,{$CONFIG['DB_PREFIX']}sharing_settings.file_path,filename,{$CONFIG['DB_PREFIX']}shared_items.date_added, {$CONFIG['DB_PREFIX']}shared_items.file_path FROM {$CONFIG['DB_PREFIX']}shared_items,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}sharing_settings WHERE {$CONFIG['DB_PREFIX']}shared_items.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}shared_items.module_key={$CONFIG['DB_PREFIX']}sharing_settings.module_key AND shared_item_key='$shareditem_key'";

$rs = $CONN->Execute($sql);

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

while (!$rs->EOF) {

	$name = $rs->fields[0];
	$description = $rs->fields[1];
	$username = $rs->fields[2].' '.$rs->fields[3];
	$url = $rs->fields[4];
	$file_path = $rs->fields[5];
	$file_name = $rs->fields[6];
	$date_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[7]),'short', true);	
	$item_file_path = $rs->fields[8];	

	$t->set_var('NAME',$name);
	$t->set_var('DESCRIPTION',$description);
	$t->set_var('USER_NAME',$username);
	$t->set_var('DATE_ADDED',$date_added);
	$t->set_var('PATH',$CONFIG['PATH']);

	if (!$item_file_path) {
		
		$t->set_var('URL',$url);
  
	} else {
		$url=$CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/sharing/'.$file_path.'/'.$item_file_path.'/'.$file_name;
		$t->set_var('URL',$url);
  
	}
	
	$rs->MoveNext();
}

$rs->Close();

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);


$t->set_var('SUBJECT_ERROR',$subject_error);
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('FULL_NAME',$full_name);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('SUBJECT',$subject);
$t->set_var('BODY',$body);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('SHAREDITEM_KEY',$shareditem_key);
$t->set_var('COMMENT_KEY',$comment_key);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('ADD_COMMENT_FOR_STRING',$sharing_strings['add_comment_for']);
$t->set_var('COMMENT_STRING',$sharing_strings['comment']);
$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);
$t->set_var('SUBJECT_STRING',$general_strings['subject']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('SUBMIT_STRING',$general_strings['submit']);

//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'body');
$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->parse('CONTENTS', 'sharingheader', true);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Add a comment to the database  
* 
*  
* @return true
*/
function add_comment()
{
	
	global $CONN, $current_user_key, $date_added, $module_key, $space_key, $CONFIG;
	
	$subject = strip_tags($_POST['subject'],'<a>,<i>,<b>,<img><strong>'); 
	$body = strip_tags($_POST['body'],'<a>,<i>,<b>,<img>,<p>,<font>,<br />,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<table>,<tr>,<td>,<ul>,<ol>,<li>,<strong>,<em>,<sup>,<sub>,<br>,<blockquote><span>');
	$shareditem_key = $_POST['shareditem_key'];
	 
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}shared_item_comments VALUES ('','$shareditem_key','','$module_key','$current_user_key','$subject','$body',$date_added)";
	
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error adding your comment: '.$CONN->ErrorMsg().' <br />';
		return $message;
	
	} else {
		
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
	global $CONN, $current_user_key, $date_added, $module_key, $space_key, $CONFIG;
	
	$full_name = $full_name; 
	$user_email = $user_email;
	$subject = strip_tags($_POST['subject'],'<a>,<i>,<b>,<img>,<strong>'); 
	$body = strip_tags($_POST['body'],'<a>,<i>,<b>,<img>,<p>,<font>,<br />,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<table>,<tr>,<td>,<ul>,<ol>,<li>,<strong>,<em>,<sup>,<sub>,<br>,<blockquote><span>');
	$shareditem_key = $_POST['shareditem_key'];
	$parent_key = $_POST['parent_key'];
	
	
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}shared_item_comments VALUES ('','$shareditem_key','$parent_key','$module_key','$current_user_key','$subject','$body',$date_added)";
	
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error adding your reply: '.$CONN->ErrorMsg().' <br />';
		return $message;
	
	} else {
		
		return true;  
	}

} //end add_reply()

/**
* Edit a comment  
* 
*  
* @return true
*/
function edit_comment()
{
	global $CONN, $CONFIG;
	
	$subject = strip_tags($_POST['subject'],'<a>,<i>,<b>'); 
	$body = strip_tags($_POST['body'],'<a>,<i>,<b>,<img>,<p>,<font>,<br />,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<table>,<tr>,<td>,<ul>,<ol>,<li>,<strong>,<em>,<sup>,<sub>,<br>,<blockquote><span>');
	$comment_key = $_POST['comment_key'];	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}shared_item_comments SET subject='$subject',body='$body' WHERE comment_key='$comment_key'";
	
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error editing your comment: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {
		
		return true;  
	}

} //end edit_comment()

?>
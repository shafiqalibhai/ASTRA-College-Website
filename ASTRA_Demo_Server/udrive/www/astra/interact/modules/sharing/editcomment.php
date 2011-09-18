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
* Edit comment
*
* Displays an existing comment for editing 
*
* @package Sharing
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: editcomment.php,v 1.15 2007/07/30 01:57:05 glendavies Exp $
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
	$comment_key	= $_POST['comment_key'];
	$parent_key	 = $_POST['parent_key'];
	$shareditem_key = $_POST['shareditem_key'];
	$subject		= $_POST['subject'];
	$body		   = $_POST['body'];				
	$action		 = $_POST['action'];	
	
} else {
 
	$module_key  = $_GET['module_key'];
	$comment_key = $_GET['comment_key'];	 
	$action	  = $_GET['action'];	   
	
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

switch($action) {
	
	case $sharing_strings['change_parent']:
		
		// make sure parent to be changed to exists
		
		if ($parent_key>0) {
		
			$sql = "SELECT comment_key FROM {$CONFIG['DB_PREFIX']}shared_item_comments WHERE comment_key='$parent_key' and shared_item_key='$shareditem_key'";
			
			$rs = $CONN->Execute($sql);
			
			if ($rs->EOF) {
			
				$message= $sharing_strings['no_such_parent'];
				errors($message);
				$rs->Close();
			
			} 
		
		}
		
		//make sure not moving parent to child or lower down in same thread

		   check_is_below($parent_key,$comment_key);
		   
		   if ( $is_below=='true') {
			   
			   $message = $sharing_strings['no_demote'];
			   errors($message);
		   
		   }
 

		$sql = "UPDATE {$CONFIG['DB_PREFIX']}shared_item_comments SET parent_key='$parent_key' WHERE comment_key='$comment_key'";
		$rs = $CONN->Execute($sql);
		header("Location: {$CONFIG['FULL_URL']}/modules/sharing/comments.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key");
		exit;
	   
	break;
	
	case $sharing_strings['change_item']:
		
		// check that shareditem exists
		$sql = "SELECT shared_item_key FROM {$CONFIG['DB_PREFIX']}shared_items where shared_item_key='$shareditem_key' and module_key='$module_key'";

		$rs = $CONN->Execute($sql);
		
		if ($rs->EOF) {
			
			$message= $sharing_strings['no_such_item'];
			errors($message);
			$rs->Close();
		
		}
		
		$sql="UPDATE {$CONFIG['DB_PREFIX']}shared_item_comments set parent_key='0',shared_item_key='$shareditem_key' WHERE comment_key='$comment_key'";
		$CONN->Execute($sql);

		update_children($comment_key,$shareditem_key);
		header("Location: {$CONFIG['FULL_URL']}/modules/sharing/comments.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key");
		exit;
	
	break;
	
	case $general_strings['delete']:
	
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}shared_item_comments where comment_key='$comment_key'";
		$rs = $CONN->Execute($sql);
		delete_children($comment_key);

		header("Location: {$CONFIG['FULL_URL']}/modules/sharing/comments.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key");
		exit;
	
	break;
	
	case 'Edit':
	
	$title = $sharing_strings['edit_title'];
	$sql = "SELECT comment_key, shared_item_key, parent_key, subject, body from {$CONFIG['DB_PREFIX']}shared_item_comments where comment_key='$comment_key'";
	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$comment_key = $rs->fields[0];
		$shareditem_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$subject = $rs->fields[3];
		$body = $rs->fields[4];
		$rs->MoveNext();
	
	}

	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	
	$t->set_file(array(
	
		'header'	 => 'header.ihtml',
		'form'	   => 'sharing/editcomment.ihtml',
		'navigation' => 'navigation.ihtml',
		'thread'	 => 'sharing/thread.ihtml',
		'footer'	 => 'footer.ihtml'));
	
	$page_details = get_page_details($space_key,$link_key);
	set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
	
	$t->set_var('SUBJECT',$subject);
	$t->set_var('TYPE_MENU',$type_menu);
	$t->set_var('BODY',$body);
	$t->set_var('PARENT_KEY',$parent_key);
	$t->set_var('COMMENT_KEY',$comment_key);
	$t->set_var('SPACE_KEY',$space_key);
	$t->set_var('MODULE_KEY',$module_key);
	$t->set_var('SHAREDITEM_KEY',$shareditem_key);
	$t->set_var('ACTION','edit');
	$t->set_var('BUTTON',$button);
	$t->set_var('ADD_COMMENT_FOR_STRING',$sharing_strings['add_comment_for']);
	$t->set_var('COMMENT_STRING',$sharing_strings['comment']);
	$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);
	$t->set_var('SUBJECT_STRING',$general_strings['subject']);
	$t->set_var('CANCEL_STRING',$general_strings['cancel']);
	$t->set_var('SUBMIT_STRING',$general_strings['submit']);
	$t->set_var('DELETE_STRING',$general_strings['delete']);
	$t->set_var('COMMENT_NO_STRING',$sharing_strings['comment_no']);
	$t->set_var('PARENT_NO_STRING',$sharing_strings['parent_no']);
	$t->set_var('SHARED_ITEM_NO_STRING',$sharing_strings['shared_item_no']);
	$t->set_var('CHANGE_PARENT_STRING',$sharing_strings['change_parent']);
	$t->set_var('CHANGE_PARENT_STRING',$sharing_strings['change_parent']);
	$t->set_var('CHANGE_ITEM_STRING',$sharing_strings['change_item']);	
	//generate the editor components

	if (!class_exists('InteractHtml')) {

		require_once('../../includes/lib/html.inc.php');
	
	}
	$html = new InteractHtml();
	$html->setTextEditor($t, $_SESSION['auto_editor'], 'body');
	$t->parse('CONTENTS', 'header', true); 
	get_navigation();

	$t->parse('CONTENTS', 'form', true);

	$t->parse('CONTENTS', 'footer', true);
	print_headers();
	$t->p('CONTENTS');

exit;
break;
}
function delete_children($comment_key) 
{   
	global $CONN, $CONFIG;
	$sql = "select comment_key from {$CONFIG['DB_PREFIX']}shared_item_comments where parent_key='$comment_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$child_comment_key = $rs->fields[0];
		$sql2="delete from {$CONFIG['DB_PREFIX']}shared_item_comments where comment_key='$child_comment_key'";
		$CONN->Execute($sql2);
		delete_children($child_comment_key);
		$rs->MoveNext();
	}

}

function update_children($comment_key,$shareditem_key) 
{   
	global $CONN, $CONFIG;
	$sql="select comment_key from {$CONFIG['DB_PREFIX']}shared_item_comments where parent_key='$comment_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		
		$child_comment_key = $rs->fields[0];
		$sql2="update {$CONFIG['DB_PREFIX']}shared_item_comments set shared_item_key='$shareditem_key' where comment_key='$child_comment_key'";
		$CONN->Execute($sql2);
		update_children($child_comment_key,$shareditem_key);
		$rs->MoveNext();
	
	}

}

function get_top_parent($parent_key) 
{   
	global $CONN,$top_parent, $CONFIG;
	$sql="select parent_key,comment_key from {$CONFIG['DB_PREFIX']}shared_item_comments where comment_key='$parent_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		
		$parent_key = $rs->fields[0];
		$comment_key2 = $rs->fields[1];
		
		if ($parent_key==0) {
			
			$top_parent=$comment_key2;
			return;
		
		} else {
			
			get_top_parent($parent_key);
		
		}
		
		$rs->MoveNext();
	
	}

}

function check_is_below($parent_key,$comment_key) 
{
	
	global $CONN, $is_below, $CONFIG;
	
	$sql = "SELECT parent_key FROM {$CONFIG['DB_PREFIX']}shared_item_comments where comment_key='$parent_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		
		$parent_key2 = $rs->fields[0];
		
		if ($parent_key2==$comment_key) {
		
			$is_below = true;
			return;
		
		} else {
			
			if ($parent_key2==0) {
				
				return;
			
			} else {
				
				check_is_below($parent_key2,$comment_key);
			
			}
		
		}
	
	$rs->MoveNext();
	
	}

}

function errors($message) 
{

	global $space_key,$CONN,$CONFIG,$module_key,$HTTP_REFERER;
	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array(
		'header'		  => 'header.ihtml',
		'errors'		  => 'errors.ihtml',
		'navigation'	  => 'navigation.ihtml',
		'footer'		  => 'footer.ihtml'
	));

	$page_details = get_page_details($space_key,$link_key);
	set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
	
	$t->set_var('BACK',$_SERVER['HTTP_REFERER']);
	$t->set_var('TITLE','Error');

	$t->parse('CONTENTS', 'header', true); 
	$t->parse('CONTENTS', 'errors', true);
	$t->parse('CONTENTS', 'footer', true);
	$t->p('CONTENTS');
	exit;
}
?>
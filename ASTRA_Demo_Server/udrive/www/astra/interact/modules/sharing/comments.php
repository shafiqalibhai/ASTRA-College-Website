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
* Display comments 
*
* Displays comments attached to a shared item
*
* @package Sharing
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: comments.php,v 1.22 2007/07/30 01:57:05 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/sharing_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$current_user_key	= $_SESSION['current_user_key'];
$shareditem_key = $_GET['shareditem_key'];
//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);

//get required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);
  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'thread'		  => 'sharing/thread.ihtml',
	'comments'		=> 'sharing/showfullcomment.ihtml',
	'footer'		  => 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();


$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);
$t->set_var('ON_STRING',$general_strings['on']);
$t->set_var('AT_STRING',$general_strings['at']);
$t->set_var('SUBJECT_STRING',$general_strings['subject']);
$t->set_var('REPLY_STRING',$general_strings['reply']);
$t->set_var('REPLY_QUOTED_STRING',$general_strings['reply_quoted']);
$t->set_var('HOME_STRING',$general_strings['home']);
$t->set_var('COMMENTS_FOR_STRING',$sharing_strings['comments_for']);
$t->set_var('ADD_NEW_COMMENT_STRING',$sharing_strings['add_new_comment']);
$t->set_var('SHAREDITEM_KEY',$shareditem_key);
$t->parse('CONTENTS', 'header', true);

get_navigation();

get_thread('0',$space);

$sql = "SELECT name,description, first_name, last_name, url,{$CONFIG['DB_PREFIX']}shared_items.filename,{$CONFIG['DB_PREFIX']}sharing_settings.file_path, {$CONFIG['DB_PREFIX']}shared_items.date_added,{$CONFIG['DB_PREFIX']}shared_items.file_path FROM {$CONFIG['DB_PREFIX']}shared_items, {$CONFIG['DB_PREFIX']}sharing_settings, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}shared_items.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}shared_items.module_key={$CONFIG['DB_PREFIX']}sharing_settings.module_key AND shared_item_key='$shareditem_key'";

$rs = $CONN->Execute($sql);

while (!$rs->EOF) {

	$name = $rs->fields[0];
	$description = $rs->fields[1];
	$username = $rs->fields[2].' '.$rs->fields[3];;
	$url = $rs->fields[4];
	$file_name = $rs->fields[5];
	$file_path = $rs->fields[6];
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
$t->parse('CONTENTS', 'thread', true);	


$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;
 
function get_thread($parent_key,$space)
{
	global $CONN, $t, $space_key,$module_key, $post_key, $current_user_key, $is_admin, $general_strings,$shareditem_key,$sharing_strings, $CONFIG, $dates;

	$sql = "SELECT comment_key, shared_item_key, parent_key, subject, body, {$CONFIG['DB_PREFIX']}shared_item_comments.date_added,{$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}shared_item_comments,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}shared_item_comments.user_key={$CONFIG['DB_PREFIX']}users.user_key and (shared_item_key='$shareditem_key' and parent_key='$parent_key')ORDER BY {$CONFIG['DB_PREFIX']}shared_item_comments.date_added, parent_key";

	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$comment_key2 = $rs->fields[0];
		$shareditem_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$subject = $rs->fields[3];
		$subject_url=urlencode($subject);
		$body = nl2br($rs->fields[4]);
		$date_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[5]),'short', false);
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[5]));
		$unix_date_added = $CONN->UnixTimestamp($rs->fields[5]);
		$date_now = mktime();
		$editable_date = $date_now-1800;
		$full_name = $rs->fields[6].' '.$rs->fields[7];
		$email = $rs->fields[8];
		$user_key = $rs->fields[9];
		$t->set_var('SPACE',$space);
		$t->set_var('SUBJECT',$subject);
		$t->set_var('SUBJECT_URL',$subject_url); 
		$t->set_var('COMMENT_KEY',$comment_key2);
		$t->set_var('PARENT_KEY',$parent_key);
		$t->set_var('FULL_NAME',$full_name);
		$t->set_var('FULL_NAME_URL',$full_name_url);
		$t->set_var('USER_KEY',$user_key);
		$t->set_var('DATE_ADDED',$date_added);
		$t->set_var('TIME_ADDED',$time_added);
		$t->set_var('EMAIL',$email);
		$t->set_var('BODY',$body);
		$post_background='sandybackground';
 
		$t->set_var('POST_BACKGROUND',$post_background);
		//if user is an administrator show admin tool and full post details		

		if ($is_admin==true || ($user_key==$current_user_key && $unix_date_added>$editable_date)) {

			$admin_image=" - <a href=\"{$CONFIG['PATH']}/modules/sharing/editcomment.php?space_key=$space_key&module_key=$module_key&comment_key=$comment_key2&action=Edit\">".$general_strings['edit']."</a>";
			$comment_details=$sharing_strings['comment_no'].'='.$comment_key2.' '.$sharing_strings['shared_item_no'].'='.$shareditem_key.' '.$sharing_strings['parent_no'].'='.$parent_key;
			$t->set_var('COMMENT_DETAILS',$comment_details);
			   
		} else {
		
			$admin_image='';
		}
		
		$t->set_var('ADMIN_IMAGE',$admin_image);
		$t->parse('FULL_POSTS', 'comments', true);
		
		get_thread($comment_key2,$space."<td width=\"20\"><img src=\"../../images/tf_last.gif\" width=\"20\" height=\"20\" vspace=\"0\" hspace=\"0\" align=\"top\"></td>");
		$rs->MoveNext();
	
	}
	
	$rs->Close();
	
	return true;

} //end get_thread

?>
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
* Print thread
*
*  Display a thread for printing
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: print.php,v 1.21 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$sort_by	= $_GET['sort_by'];
$thread_key	= $_GET['thread_key'];
$number		= $_GET['number'];
$link_key 	= get_link_key($module_key,$space_key);


//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

//get required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
  	'printheader' => 'forums/printheader.ihtml',
	'fullposts'   => 'forums/printfullpost.ihtml',
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$module_key);
//set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('BACK_LINK',"forum.php?space_key=$space_key&module_key=$module_key");
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('MODULE_NAME',$page_details[module_name]);

$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('POSTED_BY_STRING',$forum_strings['posted_by']);
$t->set_var('SUBJECT_STRING',$general_strings['subject']);
$t->set_var('ON_STRING',$general_strings['on']);
$t->set_var('AT_STRING',$general_strings['at']);
$t->set_var('SPACE_FORUM_DETAILS',sprintf($forum_strings['postings_from'], $page_details['module_name'], $page_details['space_name']));


switch ($sort_by) {

	case ThreadKey:
	
		if ($number=='all') {
		
			get_full_threads('0',$space);
		
		} else {
		
			get_by();
		
		}
	
	break;
	
	case Name:
	
		$sort_by="{$CONFIG['DB_PREFIX']}users.last_name";
		get_by();
	
	break;

}

print_headers();
$t->parse('CONTENTS', 'printheader', true);
$t->p('CONTENTS');
$CONN->Close();	 
exit;

function get_full_threads($parent_key,$space)
{
	global $CONN, $t,$userlevel_key,$accesslevel_key,$space_key,$module_key,$post_key,$thread_key, $forum_strings, $general_strings, $CONFIG, $thread_key, $objDates;

	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}

		$objDates = new InteractDate();

	}
	
	if (isset($thread_key) && $thread_key!='') {
	
		$thread_limit = "AND thread_key='$thread_key'";
	
	}


	$sql = "SELECT post_key, thread_key, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added,  {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key, prefered_name FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND (parent_key='$parent_key' AND module_key='$module_key') $thread_limit ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added, parent_key";


	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$post_key2 = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$type_key = $rs->fields[3];
		$subject = $rs->fields[4];
		$subject_url = urlencode($subject);
		$body = nl2br($rs->fields[5]);
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short', false);		
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
		
		$full_name = (!empty($rs->fields[11]))?$rs->fields[11]:$rs->fields[7].' '.$rs->fields[8];
		$email = $rs->fields[9];
		$user_key = $rs->fields[10];
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
		
		if ($parent_key==0) {
			
			$t->set_var('NEW_THREAD','<strong>'.$forum_strings['new_thread2'].': '.$general_strings['subject'].' = '.$subject.'</strong>'); 
		
		} else {
			
			$t->set_var('NEW_THREAD','');	   
		
		}
		
		$t->set_var('POST_BACKGROUND','');
		
		$t->parse('FULL_POSTS', 'fullposts', true);
		
		get_full_threads($post_key2,$space.'<td width="20"><img src="../../images/tf_last.gif" width="20" height="20" vspace="0" hspace="0" align="top"></td>');
		$rs->MoveNext();
	
	}
	
	$rs->Close();
	
	return true;

} //end get_full_threads

function get_by()
{
	global $CONN, $t,$userlevel_key,$accesslevel_key,$space_key,$module_key,$post_key,$thread_key,$number,$sort_by, $forum_strings, $general_strings, $CONFIG, $objDates;
	
	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}

		$objDates = new InteractDate();

	}
	if ($number!='all') {
	
		$interval="{$CONFIG['DB_PREFIX']}posts.date_added > DATE_SUB(CURRENT_DATE, INTERVAL $number DAY) and ";
	
	}
	
	$sql = "SELECT post_key, thread_key, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}post_type.name,{$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name,{$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users WHERE  {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND ($interval module_key='$module_key') ORDER BY $sort_by, {$CONFIG['DB_PREFIX']}posts.date_added desc";
	
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$post_key2 = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$type_key = $rs->fields[3];
		$subject = $rs->fields[4];
		$subject_url = urlencode($subject);
		$body = nl2br($rs->fields[5]);
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short', false);		
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
		
		$full_name = $rs->fields[7].' '.$rs->fields[8];
		$email = $rs->fields[9];
		$user_key = $rs->fields[10];
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

		$t->set_var('POST_BACKGROUND','');
		
		$context_link = "<a href=\"thread.php?space_key=$space_key&module_key=$module_key&thread_key=$thread_key&post_key=$post_key#$post_key2\">".$forum_strings['view_in_context'].'</a>';
		$t->set_var('CONTEXT',$context_link);
		$t->parse('FULL_POSTS', 'fullposts', true);
		$rs->MoveNext();
	}
	$rs->Close();
	return true;

} //end get_by
?>
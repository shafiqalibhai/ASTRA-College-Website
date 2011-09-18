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
* Prompt action
*
* Allows a user to action an invitation to respond to a posting 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: promptaction.php,v 1.18.4.3 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../../local/config.inc.php');
require_once('prompt.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

//set variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$post_key   = $_GET['post_key'];
	$post_data  = get_post_data($post_key);
	$space_key 	= $post_data['space_key'];
	$module_key = $post_data['module_key'];
	$subject	= $post_data['post_subject'];
	$thread_key  = $post_data['thread_key'];
	$group_key  = $post_data['group_key'];	
	$action	 = $_GET['action'];

} else if ($_POST['action']) {

	$post_key   = $_POST['post_key'];
	$module_key = $_POST['module_key'];
	$group_key	= $_POST['group_key'];
	$action	 = $_POST['action'];

}

$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

if (isset($action)) {

	switch($action) {
	
		case reply:
		
			$message = reply_to_post();
			
			if ($message === true) {
			
				exit;
				
			}
			
		break;
		
		case pass:
			
			$message = pass();
			
			if ($message === true) {
			
				header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?action=$action&space_key=$space_key&module_key=$module_key&message=Your+reponse+has+been+actioned");
				exit;
				   				
			}
			
		break;

		case pass_on:
			
			$message = pass_on();
			
			if ($message === true) {
			
				header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum.php?action=$action&space_key=$space_key&module_key=$module_key&message=Your+reponse+has+been+actioned");
				exit;
				   				
			}
			
		break;				
	
	}
}
		
			
//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'Header.ihtml',
	'navigation'	  => 'Navigation.ihtml',
	'body'			=> 'forums/promptaction.ihtml',
	'footer'		  => 'Footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//get list of users for 'pass_on' option

$post_data = get_post_data($post_key);
$prompted_users_sql = get_prompted_users($post_key);
$posted_users_sql = get_posted_users($post_key);	   
$sql_limit1 = "AND {$CONFIG['DB_PREFIX']}users.user_key NOT IN ".$prompted_users_sql." AND {$CONFIG['DB_PREFIX']}users.user_key NOT IN ".$posted_users_sql;
$sql_limit2 = " AND {$CONFIG['DB_PREFIX']}users.user_key NOT IN ".$posted_users_sql;

if ($post_data['group_key']=='0') {
	
		$users_sql = "SELECT DISTINCT FirstName, LastName, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links LEFT JOIN {$CONFIG['DB_PREFIX']}postsAutoPrompts ON {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}postsAutoPrompts.user_key WHERE  {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='{$post_data['space_key']}' AND ({$CONFIG['DB_PREFIX']}space_user_links.AccessLevelKey!='1' && {$CONFIG['DB_PREFIX']}space_user_links.AccessLevelKey!='3') AND (({$CONFIG['DB_PREFIX']}postsAutoPrompts.post_key IS NULL) OR ({$CONFIG['DB_PREFIX']}postsAutoPrompts.post_key!='$post_key' $sql_limit1)) $sql_limit2 AND {$CONFIG['DB_PREFIX']}users.user_key!='{$post_data['poster_key']}'";
	
} else {
	
		$concat = $CONN->Concat('LastName','\', \'','FirstName');
		$users_sql = "SELECT DISTINCT $concat, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links LEFT JOIN {$CONFIG['DB_PREFIX']}postsAutoPrompts ON {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}postsAutoPrompts.user_key WHERE  {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.GroupKey='{$post_data['group_key']}' AND {$CONFIG['DB_PREFIX']}group_user_links.AccessLevelKey!='1' AND (({$CONFIG['DB_PREFIX']}postsAutoPrompts.post_key IS NULL) OR ({$CONFIG['DB_PREFIX']}postsAutoPrompts.post_key!='$post_key' $sql_limit1)) $sql_limit2 AND {$CONFIG['DB_PREFIX']}users.user_key!='{$post_data['poster_key']}'";
		
}

$rs = $CONN->Execute($users_sql);


if ($rs->EOF) {

	$t->set_block('body', 'PassOnBlock', 'POBlock');	
	$t->set_var('POBlock', '<strong>All available members have already been asked to respond to this post. Please choose option 1 or 2.</strong>');

} else { 

	$user_menu= make_menu($users_sql,'user_key','','5');
	$t->set_var('USER_MENU',$user_menu);	 
	
}

$post_data = get_post_data($post_key);
$replies = get_replies($post_key,'html');
$body = nl2br($post_data['post_body']);
$post_and_replies .= "<strong>Subject:</strong> {$post_data['post_subject']}<br /><br />$body<br /><br />$replies";
$t->set_var('PATH',$CONFIG['PATH']);
$t->set_var('TOP_BREADCRUMBS',$page_details['top_breadcrumbs']);
$t->set_var('BREADCRUMBS',$page_details['breadcrumbs']);
$t->parse('CONTENTS', 'header', true); 

$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('POST_KEY',$post_key);
$t->set_var('POST_AND_REPLIES',$post_and_replies);
$t->set_var('THREAD_KEY',$thread_key);
$t->set_var('INTRODUCTION_STRING',$forum_strings['prompt_action_intro']);
$t->set_var('REPLY_STRING',$general_strings['reply']);
$t->set_var('REPLY_QUOTED_STRING',$general_strings['reply_quoted']);
$t->set_var('PASS_STRING',$forum_strings['pass']);
$t->set_var('PASS_ON_STRING',$forum_strings['pass_on']);
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var('OR_STRING',$general_strings['or']);
$t->set_var('SUBJECT',$subject);
get_navigation();
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

function reply_to_post() 
{

	global $CONN, $CONFIG;
	
	$date_actioned = $CONN->DBDate(date('Y-m-d H:i:s'));
	
	if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}postsAutoPrompts SET DateActioned=$date_actioned, ActionTakenKey='1' WHERE post_key='{$_GET['post_key']}' AND user_key='{$_SESSION['current_user_key']}'") === false) {
			
		$message = 'There was an error actioning your response'.$CONN->ErrorMsg().' <br />';
		return $message;
				
	} else {
	
		if ($_GET['quoted']=='true') {
				
			$_GET['action'] = 'reply_quoted';
					
		}
				
		header("Location: {$CONFIG['FULL_URL']}/modules/forum/postinput.php?action={$_GET['action']}&space_key={$_GET['space_key']}&module_key={$_GET['module_key']}&parent_key={$_GET['post_key']}&thread_key={$_GET['thread_key']}&subject={$_GET['subject']}");
		return true;
				
	}
			
} //end function reply_to_post

function pass() {

	global $CONN, $CONFIG;
	
	$date_actioned = $CONN->DBDate(date('Y-m-d H:i:s'));
	
	if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}postsAutoPrompts SET DateActioned=$date_actioned, ActionTakenKey='2' WHERE post_key='{$_GET['post_key']}' AND user_key='{$_SESSION['current_user_key']}'") === false) {
			
		$message = 'There was an error actioning your response'.$CONN->ErrorMsg().' <br />';
		return $message;
				
	} else {
			
		if (check_max_passes($_GET['post_key']) === true) {
		
			//maximum passes reached, stop process and email thread owner
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}postsAutoPrompts SET DateActioned=$date_actioned, ActionTakenKey='5' WHERE post_key='{$_GET['post_key']}' AND ActionTaken='0'");			
			email_post_owner($_GET['post_key'],'The maximum number of passes has been reached');
			return true;
			
		} else {
		
			//pass on to someone else if minimum replies not already reached
			
			$post_data = get_post_data($_GET['post_key']);

			//removed minimum replies setting 29/07/03
			
			//if (check_minimum_reached($post_data['post_key'],$post_data['minimum_replies']) === true) {
			
				//$date_actioned = $CONN->DBDate(date('Y-m-d H:i:s'));
				//$CONN->Execute("UPDATE forum_auto_prompts SET DateActioned=$date_actioned, ActionTakenKey='4' WHERE post_key='{$post_data['post_key']}' AND ActionTakenKey='0'");
				//email_post_owner($_GET['post_key'],'The minimum number of replies has been reached');				
				//return true;
				
			//} else {
			
				$post_data['number_to_prompt'] = 1;
				prompt_users($post_data,'pass');
				return true;
				
			//}
									

		}
		
	}
				
} //end function pass

function pass_on() {

	global $CONN, $CONFIG;
	
	$date_actioned = $CONN->DBDate(date('Y-m-d H:i:s'));
	$current_user_key = $_SESSION['current_user_key'];
	
	if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}postsAutoPrompts SET DateActioned=$date_actioned, ActionTakenKey='3' WHERE post_key='{$_GET['post_key']}' AND user_key='{$_SESSION['current_user_key']}'") === false) {
			
		$message = 'There was an error actioning your response'.$CONN->ErrorMsg().' <br />';
		return $message;
				
	} else {
			
		$post_data = get_post_data($_POST['post_key']);
		
		//create user class so we can get user details for given user_key
		require_once('../../../includes/lib/user.inc.php');
		$user = new InteractUser();
		$user_data = $user->getUserData($_POST['user_key']);
		
		$url_enc_subject = urlencode($post_data['post_subject']);


		$postaddress = $CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH']."prompt/".$post_data['post_key'];

		
		$message['body'] = "*****Important Note***** - Do not click reply to respond to this email.\n\n To action this email go to the web address below.\n\n<".$postaddress.">\n\nGreetings\n\n{$_SESSION['current_user_firstname']} {$_SESSION['current_user_lastname']} was randomly selected to reply to the following post in space '{$post_data['space_name']}'. They have passed this on to you as they felt that you would have something to contribute. You have 3 options.\n\n1. Reply\n2. Pass\n3. Pass on to someone else\n\nPlease take one of these actions by going to the link below.\n\n<".$postaddress.">\n\nThe post you are being asked to respond to is:\n\nSubject: {$post_data['post_subject']}\n\n{$post_data['post_body']}";		

		$message['email'] = $user_data['email'];
		$message['subject'] = "Invitation to respond to posting in {$post_data['space_name']}";			

		if (send_message($message)===true) {
			
			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}postsAutoPrompts(post_key,user_key,DatePrompted,DateActioned,ActionTakenKey,PromptedByKey) VALUES ('{$post_data['post_key']}','{$_POST['user_key']}',$date_added,'','','$current_user_key')");

		}

		return true;					
		
	}
				
} //end function pass_on  
?>
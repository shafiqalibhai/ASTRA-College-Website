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
* Autoprompting 
*
* Contains main autoprompt related functions
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: prompt.inc.php,v 1.25.2.1 2007/04/10 23:39:55 glendavies Exp $
* 
*/
if (!function_exists(init_config)){
	exit;
}
/**
* Include mail class file 
*/
require_once($CONFIG['INCLUDES_PATH'].'/pear/Mail.php');

	if ($CONFIG['EMAIL_TYPE']=='sendmail') {
	
		$params['sendmail_path'] = $CONFIG['EMAIL_SENDMAIL_PATH'];
		$params['sendmail_args'] = $CONFIG['EMAIL_SENDMAIL_ARGS'];
 		
	} else if ($CONFIG['EMAIL_TYPE']=='smtp') {
	
		$params['host']	 = $CONFIG['EMAIL_HOST']; 
		$params['port']	 = $CONFIG['EMAIL_PORT'] ; 
		$params['auth']	 = $CONFIG['EMAIL_AUTH'];  
		$params['username'] = $CONFIG['EMAIL_USERNAME']; 
		$params['password'] = $CONFIG['EMAIL_PASSWORD'];
		
		
	}

$mail_object =& Mail::factory($CONFIG['EMAIL_TYPE'], $params);
	
if (!class_exists('InteractDate')) {

	require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

function prompt_users($post_data,$type='new') {

	global $CONN, $forum_strings, $CONFIG;

	if ($type=='pass') {

	   $prompted_users_sql = get_prompted_users($post_data['post_key']);
	   $posted_users_sql = get_posted_users($post_data['post_key']);	   
	   $sql_limit = "AND {$CONFIG['DB_PREFIX']}users.user_key NOT IN ".$prompted_users_sql." AND {$CONFIG['DB_PREFIX']}users.user_key NOT IN ".$posted_users_sql;
	   
	} else {

	   $posted_users_sql = get_posted_users($post_data['post_key']);	   
	   $sql_limit = "AND {$CONFIG['DB_PREFIX']}users.user_key NOT IN ".$posted_users_sql;
	
	}
	
	if ($post_data['group_key']=='0') {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key, {$CONFIG['DB_PREFIX']}users.email FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE  {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='{$post_data['space_key']}' AND ({$CONFIG['DB_PREFIX']}space_user_links.AccessLevelKey!='1' && {$CONFIG['DB_PREFIX']}space_user_links.AccessLevelKey!='3') $sql_limit AND {$CONFIG['DB_PREFIX']}users.user_key!='{$post_data['poster_key']}' ORDER BY RAND()";
	
	} else {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key, {$CONFIG['DB_PREFIX']}users.email FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE  {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.GroupKey='{$post_data['group_key']}' AND {$CONFIG['DB_PREFIX']}group_user_links.AccessLevelKey!='1' $sql_limit AND {$CONFIG['DB_PREFIX']}users.user_key!='{$post_data['poster_key']}' ORDER BY RAND()";
		
	}	 

	$rs_random_users = $CONN->SelectLimit($sql,$post_data['number_to_prompt']);
	
	if ($rs_random_users->EOF) {
	
		//check to see if anybody has been prompted, if not add entry to prevent
		//post owner getting endless notifications
		$rs_prompts = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}postsAutoPrompts WHERE post_key='{$post_data['post_key']}'");
		if ($rs_prompts->EOF) {
			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}postsAutoPrompts(post_key, DatePrompted, DateActioned) VALUES ('{$post_data['post_key']}', $date_added,$date_added)");
		}
		$rs_prompts->Close();
		//all users prompted or no users to prompt, email post owner
		email_post_owner($post_data['post_key'],$forum_strings['no_users_to_prompt']);		
		
	} else {
	


	 	$postaddress = $CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH']."prompt/".$post_data['post_key'];


		$replies = get_replies($post_data['post_key']);
		$url_enc_subject = urlencode($post_data['post_subject']);
		$message['body'] = $forum_strings['prompt_message1']."\n\n";
		$message['body'] .= $forum_strings['prompt_message2']."\n\n";
		$message['body'] .= "<".$postaddress.">\n\n";
		$message['body'] .= sprintf($forum_strings['prompt_message3'], $post_data['space_name']);
		$message['body'] .= "\n\n";
		$message['body'] .= sprintf($forum_strings['prompt_message4'], $post_data['space_name']);
		$message['body'] .= $forum_strings['prompt_message5']."\n\n";
		$message['body'] .= $forum_strings['prompt_message6']."\n";
		$message['body'] .= $forum_strings['prompt_message7']."\n";
		$message['body'] .= $forum_strings['prompt_message8']."\n\n";
		$message['body'] .= $forum_strings['prompt_message9']."\n\n";
		$message['body'] .= "<".$postaddress.">\n\n";
		$message['body'] .= $forum_strings['prompt_message10']."\n\n";
		$message['body'] .= $forum_strings['subject'].': '.$post_data['post_subject']."\n\n";
		$message['body'] .= $post_data['post_body']."\n\n".$replies;
				
		while(!$rs_random_users->EOF) {
			
			$user_key = $rs_random_users->fields[0];
			
			//first check to make sure they haven't already posted
			
			$message['email'] = $rs_random_users->fields[1];
			$message['subject'] = sprintf($forum_strings['invitation_subject'],$post_data['space_name']);
			
			if (send_message($message)===true) {
	
				$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}postsAutoPrompts(post_key,user_key,DatePrompted,DateActioned,ActionTakenKey) VALUES ('{$post_data['post_key']}','$user_key',$date_added,'','')");
//echo "INSERT INTO {$CONFIG['DB_PREFIX']}postsAutoPrompts(post_key,user_key,DatePrompted,DateActioned,ActionTakenKey) VALUES ('{$post_data['post_key']}','$user_key',$date_added,'','')";
			}
				
			$rs_random_users->MoveNext();
			
		}
		
	}

	return;
			
}



function send_message($message) {

	global $CONFIG, $mail_object; 

	$recipients = $message['email'];
	$headers['From']	= $CONFIG['NO_REPLY_EMAIL'];
	$headers['To']	  = $message['email'];
	$headers['Subject'] = $message['subject'];
	   
	$result = $mail_object->send($recipients, $headers, $message['body']);

	if (PEAR::isError($result)) {
	
		print "mail error: ".$result->getMessage()."<br />\n";
	
	} else {
	
		return true;
		
	}
	
	
}

function get_post_data($post_key) 
{
  
	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}posts.post_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key, {$CONFIG['DB_PREFIX']}module_space_links.GroupKey, {$CONFIG['DB_PREFIX']}forum_thread_management.NumberToPrompt, {$CONFIG['DB_PREFIX']}posts.subject, {$CONFIG['DB_PREFIX']}posts.body,{$CONFIG['DB_PREFIX']}posts.module_key,{$CONFIG['DB_PREFIX']}posts.thread_key,{$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}forum_thread_management.MinimumReplies,{$CONFIG['DB_PREFIX']}spaces.Name, {$CONFIG['DB_PREFIX']}posts.added_by_key FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}forum_thread_management, {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}spaces WHERE {$CONFIG['DB_PREFIX']}forum_thread_management.post_key={$CONFIG['DB_PREFIX']}posts.post_key AND {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$post_key'");

	if ($rs->EOF) {

		return false;

	} else {

		while (!$rs->EOF) {

			$post_data = array( 
				'post_key'		 => $rs->fields[0],
				'space_key'		=> $rs->fields[1],
				'group_key'		=> $rs->fields[2],
				'number_to_prompt' => $rs->fields[3],
				'post_subject'	 => $rs->fields[4],
				'post_body'		=> $rs->fields[5],
				'module_key'	   => $rs->fields[6],
				'thread_key'	   => $rs->fields[7],
				'owners_email'	 => $rs->fields[8],
				'minimum_replies'  => $rs->fields[9],
				'space_name'	   => $rs->fields[10],
				'poster_key'	   => $rs->fields[11]																						
			);
			
			$rs->MoveNext();
			
		}
		
		return $post_data;
		
	}
	
}

function get_prompted_users($post_key) 
{

	global $CONN, $CONFIG;
	$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}postsAutoPrompts WHERE post_key='$post_key'";
	$rs = $CONN->Execute($sql);

	$prompted_users_sql="(";
	$record_count=$rs->RecordCount();
	
	if ($rs->EOF) {
	
			$prompted_users_sql="(1,1)";
			
	} else {
	
		while (!$rs->EOF) {
	
			$current_row=$rs->CurrentRow();
	   		
			if(++$current_row==$record_count) {

				$prompted_users_sql.="{$rs->fields[0]}) ";
			
			} else {

				$prompted_users_sql.="{$rs->fields[0]}, ";
			
			}

			$rs->MoveNext();   
		
		}
		
	}
	
	return $prompted_users_sql;
	
}

function get_posted_users($post_key) 
{

	global $CONN, $CONFIG;
	$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'";
	$rs = $CONN->Execute($sql);

	$posted_users_sql="(";
	$record_count=$rs->RecordCount();
	
	if ($rs->EOF) {
	
			$posted_users_sql="(1,1)";
			
	} else {
	
		while (!$rs->EOF) {
	
			$current_row=$rs->CurrentRow();
	   		
			if(++$current_row==$record_count) {

				$posted_users_sql.="{$rs->fields[0]}) ";
			
			} else {

				$posted_users_sql.="{$rs->fields[0]}, ";
			
			}

			$rs->MoveNext();   
		
		}
		
	}
	
	return $posted_users_sql;
	
} //end get_posted_users

function email_post_owner($post_key,$message) 
{

	global $CONN,$mail_object, $forum_strings, $CONFIG;
	
	$post_data = get_post_data($post_key);  

 	$postaddress = $CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH']."post/".$post_data['space_key'].'/'.$post_key;


	$recipients = $post_data['owners_email'];
	$headers['From']	= $CONFIG['NO_REPLY_EMAIL'];
	$headers['To']	  = $post_data['owners_email'];
	$headers['Subject'] = $message.' for post \''.$post_data['post_subject'].'\'';
	$body = $message.' for post \''.$post_data['post_subject'].'\'';
	$body .= "\n\n".$forum_strings['prompt_reply'];
	$body .= "\n\n$postaddress";
	$result = $mail_object->send($recipients, $headers, $body);
	
	if (PEAR::isError($result)) {
	
		print "mail error: ".$result->getMessage()."<br />\n";
	
	} else {
	
		return true;
		
	}  
	
}
function check_minimum_reached($post_key,$minimum_replies) {

	global $CONN, $CONFIG;
	
	//see if minimum replies already reached
			
	$rs_minimum_replies = $CONN->Execute("SELECT COUNT(post_key) FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'");
			
	while(!$rs_minimum_replies->EOF) { 
			
		$number_of_replies = $rs_minimum_replies->fields[0];
		$rs_minimum_replies->MoveNext();
				
	}
			
	$rs_minimum_replies->Close();

	if ($number_of_replies>=$minimum_replies) {
	
		return true;
		
	} else {
	
		return false;
		
	}

} //end check_minimum_reached

function check_max_passes($post_key) {

	global $CONN, $CONFIG;
	
	$rs_passes_allowed = $CONN->Execute("SELECT PassesAllowed FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'");
				
	while (!$rs_passes_allowed->EOF) {
				
		$passes_allowed = $rs_passes_allowed->fields[0];
		$rs_passes_allowed->MoveNext();
					
	}

	$rs_passes = $CONN->Execute("SELECT COUNT(post_key) FROM {$CONFIG['DB_PREFIX']}postsAutoPrompts WHERE post_key='$post_key' AND (ActionTakenKey='2' OR ActionTakenKey='6')");
			
	while (!$rs_passes->EOF) {
				
		$passes = $rs_passes->fields[0];
		$rs_passes->MoveNext();
					
	}
	
	if ($passes>=$passes_allowed) {
	
		return true;
		
	} else {
	
		return false;
		
	}	

} //end check_max_passes

function check_action_taken($post_key,$user_key,$action)
{

	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}postsAutoPrompts WHERE post_key='$post_key' AND user_key='$user_key' AND ActionTakenKey='$action'");
	
	if ($rs->EOF) {
	
		return false;
		
	} else {
	
		return true;
		
	}

} //end action taken
function get_replies($post_key,$format='email') {

	global $CONN, $forum_strings, $CONFIG, $dates;
	
	
	$rs = $CONN->Execute("SELECT post_key, ThreadKey, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, Body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}post_type.name, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}post_type,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.type_key={$CONFIG['DB_PREFIX']}post_type.type_key AND {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND (parent_key='$post_key')ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added ASC");
	
	if ($rs->EOF) {
	
		$replies = $forum_strings['no_replies'];
		return $replies;
		
	} else {
	
		$c = 1;
		
		while (!$rs->EOF) {
		
			$post_key2 = $rs->fields[0];
			$thread_key = $rs->fields[1];
			$parent_key = $rs->fields[2];
			$type_key = $rs->fields[3];
			$subject = $rs->fields[4];
	
			if ($format==email) {
	
				$body = $rs->fields[5];
				$line_break = "\n";
		
			} else {
	 
  				$line_break = "<br />";
				
				if (eregi("(<p>|<br />)", $rs->fields[5])) {
			
					$body = $rs->fields[5];
   		
				} else {
		
					$body = ereg_replace( 10, "<br />", $rs->fields[5]);
		
				}
		
			}
			
			$date_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short', true);
			$type_name = $rs->fields[7];
			$full_name = $rs->fields[8].' '.$rs->fields[9];
			$email = $rs->fields[10];
			
			$replies .= "---------------------------------$line_break";
			$replies .= "Reply $c from $full_name $date_added - $type_name $line_break $line_break";
			$replies .= "$body $line_break $line_break";						 
			$c++;
			$rs->MoveNext();
			
		}
		
		return $replies;
	}
	
}
?>
<?php
// +----------------------------------------------------------------------+
// |prompter.php														  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education				  |
// +----------------------------------------------------------------------+
// | This file is part of Interact.									   |
// |																	  | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)							 |
// |																	  | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 |
// | General Public License for more details.							 |
// |																	  | 
// | You should have received a copy of the GNU General Public License	|
// | along with this program; if not, you can view it at				  |
// | http://www.opensource.org/licenses/gpl-license.php				   |
// |																	  |
// |																	  |
// | prompt users to contribute to discussion							 |
// |																	  |
// |																	  |
// |																	  |
// |																	  |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz>					 |
// | Last Modified 19/03/03											   |
// +----------------------------------------------------------------------+
if (!function_exists(init_config)){
	exit;
}
require_once($CONFIG['BASE_PATH'].'/modules/forum/autoprompt/prompt.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}posts.post_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key, {$CONFIG['DB_PREFIX']}module_space_links.group_key, {$CONFIG['DB_PREFIX']}forum_thread_management.number_to_prompt, {$CONFIG['DB_PREFIX']}posts.subject, {$CONFIG['DB_PREFIX']}posts.body,{$CONFIG['DB_PREFIX']}posts.module_key,{$CONFIG['DB_PREFIX']}posts.thread_key,{$CONFIG['DB_PREFIX']}forum_thread_management.minimum_replies,{$CONFIG['DB_PREFIX']}spaces.name, {$CONFIG['DB_PREFIX']}posts.added_by_key FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}forum_thread_management,{$CONFIG['DB_PREFIX']}spaces LEFT JOIN {$CONFIG['DB_PREFIX']}posts_auto_prompts ON {$CONFIG['DB_PREFIX']}forum_thread_management.Postkey={$CONFIG['DB_PREFIX']}posts_auto_prompts.post_key WHERE {$CONFIG['DB_PREFIX']}forum_thread_management.post_key={$CONFIG['DB_PREFIX']}posts.post_key AND {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND {$CONFIG['DB_PREFIX']}posts.date_added<DATE_SUB(CURRENT_DATE,INTERVAL {$CONFIG['DB_PREFIX']}forum_thread_management.days_to_wait DAY) AND {$CONFIG['DB_PREFIX']}posts_auto_prompts.post_key IS NULL ORDER BY {$CONFIG['DB_PREFIX']}posts.post_key");

//set post_key variable so we can check for duplicates
//caused by forums linked in more than one space
 
$post_key1 = '';
   
while (!$rs->EOF) {

	if ($post_key1!=$rs->fields[0]) {
	   
	   $post_data = array( 
			'post_key'		 => $rs->fields[0],
			'space_key'		=> $rs->fields[1],
			'group_key'		=> $rs->fields[2],
			'number_to_prompt' => $rs->fields[3],
			'post_subject'	 => $rs->fields[4],
			'post_body'		=> $rs->fields[5],
			'module_key'	   => $rs->fields[6],
			'thread_key'	   => $rs->fields[7],
			'minimum_replies'  => $rs->fields[8],
			'space_name'	   => $rs->fields[9],
			'poster_key'	   => $rs->fields[10]																
		);			
	
		//removed minumum posts setting 29/07/03
		//if (check_minimum_reached($post_data['post_key'],$post_data['minimum_replies']) == true) {
			
			//$date_actioned = $CONN->DBDate(date('Y-m-d H:i:s'));
			//$CONN->Execute("UPDATE ForumAutoPrompts SET date_actioned=$date_actioned, action_taken_key='4' WHERE post_key='{$post_data['post_key']}' AND action_taken_key='0'");
				
		//} else {
			
			prompt_users($post_data);
			
		//}
			
		$post_key1 = $rs->fields[0];
	
	}
		
	$rs->MoveNext();

}
	
$rs->Close();

//now get any prompts that have not been actioned by response wait time

$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}posts_auto_prompts.post_key, {$CONFIG['DB_PREFIX']}posts_auto_prompts.user_key,minimum_replies FROM {$CONFIG['DB_PREFIX']}posts_auto_prompts, {$CONFIG['DB_PREFIX']}forum_thread_management WHERE {$CONFIG['DB_PREFIX']}posts_auto_prompts.post_key={$CONFIG['DB_PREFIX']}forum_thread_management.post_key AND action_taken_key='0' AND date_prompted<DATE_SUB(CURRENT_DATE, INTERVAL {$CONFIG['DB_PREFIX']}forum_thread_management.response_time DAY)");


$date_actioned = $CONN->DBDate(date('Y-m-d H:i:s'));

while (!$rs->EOF) {

	$post_key = $rs->fields[0];
	$user_key = $rs->fields[1];
	$minimum_replies = $rs->fields[2];

	//check action_taken_key is still 0 as may have been updated by previous post

	if (check_action_taken($post_key,$user_key,'0') === true) {
	
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}posts_auto_prompts SET date_actioned=$date_actioned, action_taken_key='6' WHERE post_key='$post_key' AND user_key='$user_key'");
	
		if (check_max_passes($post_key) === true) {
	
			//maximum passes reached, stop process and email thread owner
			
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}posts_auto_prompts SET date_actioned=$date_actioned, action_taken_key='5' WHERE post_key='$post_key' AND action_taken_key='0'"); 
	
			email_post_owner($post_key,'The maximum number of passes has been reached');
	
		
		 //removed minimum posts setting 29/07/03
		//} else if (check_minimum_reached($post_key,$minimum_replies) === true) {
	
		   //$CONN->Execute("UPDATE ForumAutoPrompts SET date_actioned=$date_actioned, action_taken_key='4' WHERE post_key='$post_key' AND action_taken_key='0'");	   
		   //email_post_owner($post_key,'The minimum number of replies has been reached');
		
		} else {
	
			$post_data = get_post_data($post_key);
			$post_data['number_to_prompt'] = 1;
			prompt_users($post_data,'pass');
		
		}
		
	}
		
	$rs->MoveNext();
	
}


?>
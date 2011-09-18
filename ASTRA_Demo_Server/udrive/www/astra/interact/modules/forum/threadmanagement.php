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
* Display selected posts
*
* Displays selected posts from a forum
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: threadmanagement.php,v 1.24 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];
	$post_key   = $_GET['post_key'];
	$thread_key = $_GET['thread_key'];
	$parent_key = $_GET['parent_key'];		
	
} else {

	$space_key		= $_POST['space_key'];
	$module_key	   = $_POST['module_key'];
	$post_key		 = $_POST['post_key'];
	$thread_key	   = $_POST['thread_key'];
	$parent_key	   = $_POST['parent_key'];
	$days_to_wait	 = $_POST['days_to_wait'];
	$number_to_prompt = $_POST['number_to_prompt'];
	$passes_allowed   = $_POST['passes_allowed'];
	$response_time	= $_POST['response_time'];
	$submit		   = $_POST['submit'];					

}

$link_key = get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);
// See if the form has been submitted

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$errors = check_form_input();

	if(count($errors) == 0) {
	
		switch ($submit) {
		
			case $general_strings['add']:
				
				if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}forum_thread_management(post_key,DaysToWait,NumberToPrompt,PassesAllowed,ResponseTime,MinimumReplies) VALUES ('$post_key','$days_to_wait','$number_to_prompt','$passes_allowed','$response_time','$minimum_replies')") === false) {
				
					$message = 'There was an error adding auto-prompting '.$CONN->ErrorMsg().' <br />';
					$button = $button = '<input name="submit" type="submit" id="submit" value="Add" />'; 
					
				} else {
				
					$message = urlencode($forum_strings['autoprompting_added']);
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/editpost.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&thread_key=$thread_key&parent_key=$parent_key&action=Edit&message=$message");
					exit;
				
				}

			break;		
			
			case $general_strings['modify']:
				
				if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}forum_thread_management SET DaysToWait='$days_to_wait',NumberToPrompt='$number_to_prompt',PassesAllowed='$passes_allowed',ResponseTime='$response_time',MinimumReplies='$minimum_replies' WHERE post_key='$post_key'") === false) {
				
					$message = 'There was an error modifying auto-prompting '.$CONN->ErrorMsg().' <br />';
					if ($is_admin==true) {
		 
						$button = '<input name="submit" type="submit" id="submit" value="Modify" />';
			
					} else {
		
						$button = '';
		
					} 
					
				} else {
				
					$message = urlencode($forum_strings['autoprompting_modified']);
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/editpost.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&thread_key=$thread_key&parent_key=$parent_key&action=Edit&message=$message");
					exit;
				
				}

			break;		
			
			case $general_strings['delete']:
				
				if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'") === false) {
				
					$message = 'There was an error deleting auto-prompting '.$CONN->ErrorMsg().' <br />';
					
					if ($is_admin==true) {
		 
						$button = '<input name="submit" type="submit" id="submit" value="Modify" />';
			
					} else {
		
						$button = '';
		
					} 
					
				} else {
				
					//$CONN->Execute("DELETE FROM forum_auto_prompts WHERE post_key='$post_key'");
					$message = urlencode($forum_strings['autoprompting_deleted']);					
					header("Location: {$CONFIG['FULL_URL']}/modules/forum/editpost.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&thread_key=$thread_key&parent_key=$parent_key&action=Edit&message=$message");
					exit;
				
				}

			break;										
				
		}
		
	} else {
	
		$message = $general_strings['problem_below'];
		$button = $submit; 		
		
	}


}

$days_to_wait_error = sprint_error($errors['days_to_wait']);
$number_to_prompt_error = sprint_error($errors['number_to_prompt']);
$passes_allowed_error = sprint_error($errors['passes_allowed']);
$response_time_error = sprint_error($errors['response_time']);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'forumheader'	 => 'forums/forumheader.ihtml',
	'form'			=> 'forums/threadmanagement.ihtml',
	'footer'		  => 'footer.ihtml'));

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('AUTO_PROMPT_DESCRIPTION',$forum_strings['autoprompt_description']);

if ($is_admin==true) {

	$t->set_block('form', 'UserBlock', 'UBlock');
	$t->set_var('UBlock','');
	
} else {

	$t->set_block('form', 'AdminBlock', 'ABlock');
	$t->set_var('ABlock','');
	
}


if (!isset($submit)) {

	$rs = $CONN->Execute("SELECT DaysToWait,NumberToPrompt,PassesAllowed,ResponseTime,MinimumReplies FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'");

	if ($rs->EOF) {
	
		$is_enabled = $forum_strings['prompting_not_enabled']; 
		$button = '<input name="submit" type="submit" id="submit" value="'.$general_strings['add'].'" />';		 		

	} else {
	
		while(!$rs->EOF) {
		
			$days_to_wait = $rs->fields[0];
			$number_to_prompt = $rs->fields[1];
			$passes_allowed = $rs->fields[2];
			$response_time = $rs->fields[3];
			$minimum_replies = $rs->fields[4];												
			$rs->MoveNext();
			
		}

		$is_enabled = $forum_strings['prompting_enabled']; 
		
		if ($is_admin==true) {
		 
			$button = '<input name="submit" type="submit" id="submit" value="'.$general_strings['modify'].'" />';
			
		} else {
		
			$button = '';
		
		}
		
		$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')"/>';
		
	}
	
	$rs->Close();
	
	$post_key   = $_GET['post_key'];
	$thread_key = $_GET['thread_key'];
	$parent_key = $_GET['parent_key'];
		
	
}
get_prompt_stats($post_key);


$t->set_var('IS_ENABLED',$is_enabled); 
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('POST_EDITING_STRING',$forum_strings['post_editing']);
$t->set_var('AUTO_PROMPTING_STRING',$forum_strings['autoprompting']);
$t->set_var('DAYS_TO_WAIT_STRING',$forum_strings['days_to_wait']);
$t->set_var('NUMBER_TO_PROMPT_STRING',$forum_strings['number_to_prompt']);
$t->set_var('PASSES_ALLOWED_STRING',$forum_strings['passes_allowed']);
$t->set_var('RESPONSE_TIME_STRING',$forum_strings['response_time']);
$t->set_var('AUTOPROMPT_STATISTICS_STRING',$forum_strings['autoprompt_statistics']);
$t->set_var('DATE_PROMPTED_STRING',$forum_strings['date_prompted']);
$t->set_var('DATE_ACTIONED_STRING',$forum_strings['date_actioned']);
$t->set_var('ACTION_TAKEN_STRING',$forum_strings['action_taken']);
$t->set_var('DAYS_TO_WAIT_ERROR',$days_to_wait_error);
$t->set_var('NUMBER_TO_PROMPT_ERROR',$number_to_prompt_error);
$t->set_var('PASSES_ALLOWED_ERROR',$passes_allowed_error);
$t->set_var('RESPONSE_TIME_ERROR',$response_time_error);
$t->set_var('MINIMUM_REPLIES_ERROR',$minimum_replies_error);
$t->set_var('DAYS_TO_WAIT',$days_to_wait);
$t->set_var('NUMBER_TO_PROMPT',$number_to_prompt);
$t->set_var('PASSES_ALLOWED',$passes_allowed);
$t->set_var('RESPONSE_TIME',$response_time);
$t->set_var('MINIMUM_REPLIES',$minimum_replies);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('POST_KEY',$post_key);
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('THREAD_KEY',$thread_key);
$t->set_var('DELETE_BUTTON',$delete_button);
$t->set_var('BUTTON',$button);
$t->set_var('REFERER',$HTTP_REFERER);
$t->parse('CONTENTS', 'header', true); 

get_navigation();

$t->parse('CONTENTS', 'forumheader', true);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);

$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Check form input   
* 
*  
* @return $errors
*/
function check_form_input() 
{

	global $forum_strings;
// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($_POST)){

		$_POST[$key] = trim($value);

	}

//check to see if we have all the information we need
	if(!$_POST['days_to_wait']) {

	   
		$errors['days_to_wait'] = $forum_strings['no_days_to_wait'];

	}
	
	if(!$_POST['number_to_prompt']) {

	 
		$errors['number_to_prompt'] = $forum_strings['no_number_to_prompt'];

	}	
	if(!$_POST['passes_allowed']) {

		$errors['passes_allowed'] = $forum_strings['no_passes_allowed'];

	}	
	
	if(!$_POST['response_time']) {
 
		$errors['response_time'] = $forum_strings['no_response_time'];

   }
   
   //if(!$_POST['minimum_replies']) {

		//$errors['minimum_replies'] = 'You didn\'t enter the minimum number of replies.';

   //}   	

	return $errors;
	
} //end check_form_input

function get_prompt_stats($post_key) 
{

	global $CONN,$t, $forum_strings, $CONFIG, $objDates;

	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}
	}
	$objDates = new InteractDate();

	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}PostsAutoPrompts.DatePrompted,{$CONFIG['DB_PREFIX']}PostsAutoPrompts.DateActioned,{$CONFIG['DB_PREFIX']}PostsAutoPromptActions.Name FROM {$CONFIG['DB_PREFIX']}PostsAutoPrompts,{$CONFIG['DB_PREFIX']}PostsAutoPromptActions WHERE {$CONFIG['DB_PREFIX']}PostsAutoPrompts.ActionTakenKey={$CONFIG['DB_PREFIX']}PostsAutoPromptActions.ActionKey  AND {$CONFIG['DB_PREFIX']}PostsAutoPrompts.post_key='$post_key'");
echo $CONN->ErrorMsg();
	   if ($rs->EOF) {
		
			//nothing to display so remove prompt stats table from template
	
			$t->set_block('form', 'PromptStats', 'SBlock');		
			$t->set_var('SBlock', $forum_string['no_statistics']);
			
		} else {
		
			$t->set_block('form', 'PromptStatRows', 'SBlock');
			$c = 1;	
			
			while(!$rs->EOF) {
			
				$t->set_var('NUMBER', $c);
				$t->set_var('DATE_PROMPTED', $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[0]),'short'));

				if ($rs->fields[1]==0) {
				
  					$t->set_var('DATE_ACTIONED', 'Not Actioned Yet');
				
				} else {
				
					$t->set_var('DATE_ACTIONED', $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[1]),'short'));
					
				}
								
				$t->set_var('ACTION_TAKEN', $rs->fields[2]);			
				$t->Parse('SBlock', 'PromptStatRows', true); 
				$c++;
				$rs->MoveNext();	

			}
			
		}
		
		$rs->Close();   	  
} //end prompt_stats
?>
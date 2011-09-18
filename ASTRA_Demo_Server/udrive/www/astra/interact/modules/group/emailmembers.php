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
* email group members
*
* Displays a form to email group members
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: emailmembers.php,v 1.15 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

//set variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];

} else {

	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];

}

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);
$current_user_key = $_SESSION['current_user_key'];
//check we have the required variables

check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];	 
$page_details=get_page_details($space_key,$link_key);

if ($_POST['action']=='email') {
	
	$errors = check_form_input();
	
	if(count($errors) == 0) {
	
		require_once('../../includes/email.inc.php');
		
		if (!$_POST['member_keys'] && $_POST['email_who']=='all' ) {

			$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND group_key='$group_key' AND {$CONFIG['DB_PREFIX']}group_user_links.user_key!='$current_user_key'";
			
			$member_keys = get_userkey_array($sql);
		
		} else if ($_POST['member_keys'] && $_POST['email_who']=='selected' ) {
		
			$member_keys = $_POST['member_keys'];
						
		}
		
		
		$subject = '['.$page_details[space_short_name].'] - '.$_POST['subject'];
		
		email_users($subject, $_POST['body'], $member_keys, $_POST['copyself'], $_FILES['file'], $_POST['carbon_copy']);
		
		$message = urlencode($general_strings['email_sent']);
		header ("Location: {$CONFIG['FULL_URL']}/modules/group/emailmembers.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key&message=$message");
		
		exit;
	
	} else {
	
		$message = $general_strings['problem_below'];
		
	}

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'body'			=> 'groups/Email.ihtml',
	'footer'		  => 'footer.ihtml'
));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true);

get_navigation();

$concat = $CONN->Concat('last_name','\', \'','first_name');
$members_sql="SELECT $concat,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key and {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key' AND  {$CONFIG['DB_PREFIX']}group_user_links.user_key!='$current_user_key' ORDER BY last_name";
$members_menu = make_menu($members_sql,'member_keys[]','','8','true');

$subject_error = sprint_error($errors['subject']);
$body_error = sprint_error($errors['body']);
$file_error = sprint_error($errors['file']);
$email_who_error = sprint_error($errors['email_who']);
$t->set_var('MEMBERS_MENU',$members_menu);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('SUBJECT_ERROR',$subject_error);
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('FILE_ERROR',$file_error);
$t->set_var('EMAIL_WHO_ERROR',$email_who_error);
$t->set_var('SUBJECT',$_POST['subject']);
$t->set_var('BODY',$_POST['body']);

$t->set_var('SUBJECT_STRING',$general_strings['subject']);
$t->set_var('MESSAGE_STRING',$general_strings['message']);
$t->set_var('CC_STRING',$general_strings['carboncopy']);
$t->set_var('CC_INSTRUCTIONS',$general_strings['cc_instructions']);
$t->set_var('ATTACHMENT_STRING',$general_strings['attachment']);
$t->set_var('COPY_SELF_STRING',$general_strings['copy_self']);
$t->set_var('INSTRUCTIONS_STRING',$general_strings['email_instructions']);
$t->set_var('GROUP_MEMBERS_STRING',$group_strings['group_members']);
$t->set_var('ALL_MEMBERS_STRING',$general_strings['email_all']);
$t->set_var('SELECTED_MEMBERS_STRING',$general_strings['email_selected']);
$t->set_var('SEND_STRING',$general_strings['send']);
$t->set_var('EMAIL_MEMBERS_STRING',$general_strings['email_members']);

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;






function check_form_input() 
{

	global $general_strings ; 


	// Initialize the errors array

	$errors = array();

	//check to see if we have all the information we need
	
	if(!$_POST['email_who']) {

		$errors['email_who'] = $general_strings['email_who_error'];

	}
	
	if($_POST['email_who']=='selected' && !$_POST['member_keys']) {

		$errors['email_who'] = $general_strings['email_who_error2'];

	}
	
	if(!$_POST['subject']) {

		$errors['subject'] = $general_strings['no_subject'];
	

	}


	if(!$_POST['body']) {

		$errors['body'] = $general_strings['no_message'];

	}
	
	if( $_FILES['file']['size'] > 200000) {

		$errors['file'] = $general_strings['attachment_to_big'];

	}

	return $errors;

} //end check_form_input
?>
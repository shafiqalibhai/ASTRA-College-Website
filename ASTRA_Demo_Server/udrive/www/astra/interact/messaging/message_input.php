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
* Message input
*
* Displays the message input page to send a new message 
*
* @package Messaging
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: message_input.php,v 1.4 2007/01/05 03:02:54 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/messaging_strings.inc.php');


if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$space_key  = $_GET['space_key'];
	$user_key 	= $_GET['user_key'];
	$message_key 	= $_GET['message_key'];
	$referer 	= urldecode($_GET['referer']);
		
} else {
	
	$space_key  	= $_POST['space_key'];
	$user_key 		= $_POST['user_key'];
	$user_message 	= $_POST['user_message'];	
	$referer 		= urldecode($_POST['referer']);
	
			
}

$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables
//check_variables(false,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

if (isset($_POST['submit'])) {
	if (!isset($user_message) || $user_message=='') {
		$message = $messaging_strings['no_message'];
	} else {
		require_once($CONFIG['BASE_PATH'].'/messaging/lib.inc.php');
		$messagingObj = new InteractMessaging();
		$messagingObj->addMessage($user_key, $user_message,$current_user_key, time());
		//$messagingObj->deleteMessage($current_user_key,$message_key);
		$message = urlencode($messaging_strings['message_added']);
		header('Location: '.$CONFIG['FULL_PATH'].'message_admin.php?action=read&message='.$message);
		exit;
	}
}

//get the required template files
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'form'			=> 'messaging/message_input.ihtml',
));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$module_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!is_object($objUser)) {
	if (!class_exists('InteractUser')){
		require_once('../includes/lib/user.inc.php');	
	}
	$objUser = new InteractUser();
}
$user_details = $objUser->getUserData($user_key);
$t->set_var('MESSAGE_INPUT_HEADING',sprintf($messaging_strings['input_heading'], $user_details['first_name'].' '.$user_details['last_name']));
$t->set_var('CHARACTERS_STRING',$messaging_strings['characters']);
$t->set_var('SEND_STRING',$messaging_strings['send']);
$t->set_var('MAXIMUM_CHAR_STRING',sprintf($messaging_strings['maximum_characters'],255));
$t->set_var('USER_KEY',$user_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('REFERER',$referer);
$t->set_var('USER_MESSAGE',isset($user_message)? $user_message : '');
$t->parse('CONTENTS', 'form', true);


print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();

exit;


?>
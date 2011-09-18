<?php
/**
* email Users
*
* Sends email to selected User of a Space
*
* @package Spaces
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key = get_space_key();
$current_user_key = $_SESSION['current_user_key'];

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'body'			=> 'spaces/emailuser.ihtml',
	'footer'		  => 'footer.ihtml'
));

$unauth_post_key=0;
if ($_SERVER['REQUEST_METHOD']=='GET') {
	if(!empty($_GET['unauth_post_key'])) {$unauth_post_key=$_GET['unauth_post_key'];}
} else {
	if(!empty($_POST['unauth_post_key'])) {$unauth_post_key=$_POST['unauth_post_key'];}
	$action	   = $_POST['action'];
}

//check we have the required variables
check_variables(true,false);

//check to see if user has access...
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups']; 

if($unauth_post_key) {
	$un_detail=$CONN->Execute("SELECT unauth_name,unauth_email FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key = {$unauth_post_key}");
}

if ($action=='email') {
	
	$errors = check_form_input();
	
	if(count($errors) == 0) {
		
		require_once('../includes/email.inc.php');
		if(empty($current_user_key)) {
			$_POST['carbon_copy']='';
			$_POST['copyself']='';
		}

		
//($subject, $body, $member_keys, $copyself='', $attachment='', $carbon_copy='',$from='',$plain_text='') 		
		
email_users($_POST['subject'], $_POST['body'], $_POST['email_user_key'], $_POST['copyself'], $_FILES['file'], $_POST['carbon_copy'],(empty($current_user_key)?$_POST['from_email']:''),'',($unauth_post_key?$un_detail->fields[1]:''));  
		
		$message = urlencode($space_strings['email_success']);
		header ("Location: {$CONFIG['FULL_URL']}/spaces/members.php?space_key=$space_key&message=$message");
		exit;
	
	}

}

if($unauth_post_key) {
	if(empty($current_user_key)) {
		header('Location: '.$CONFIG['PATH']);
		exit;
	} else {
		$t->set_block('body','FromEmailBlock','FBlock');$t->set_var('FBlock','');
		$t->set_var('UNAUTH_POST_KEY',$unauth_post_key);
		
		$t->set_var('TO_NAME',$un_detail->fields[0]);
		
		if(!is_email($un_detail->fields[1])) {
			$t->set_block('body','EmailFormBlock','EBlock');
			$t->set_var('EBlock',sprintf('%s did not enter a valid email address:',$un_detail->fields[0]).'<pre>'.$un_detail->fields[1].'</pre>');
		}
	}
} else {
		if(empty($current_user_key)) {
			$t->set_block('body','CCBlock','CCBlock');$t->set_var('CCBlock','');
		} else {$t->set_block('body','FromEmailBlock','FBlock');$t->set_var('FBlock','');}

		$t->set_var('EMAIL_USER_KEY', $_GET['email_user_key']);
		
		$rs=$CONN->Execute("SELECT first_name,last_name FROM {$CONFIG['DB_PREFIX']}users WHERE user_key = {$_GET['email_user_key']}");
		$t->set_var('TO_NAME',$rs->fields[0].' '.$rs->fields[1]);
//		$email_username  = urldecode($_GET['email_username']);
}





$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$breadcrumbs = "<a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\">".$space_strings['start']."</a> -> <a href=\"members.php?space_key=$space_key\">".$space_strings['members']."</a> - <b>".$space_strings['email_members']."</b>";
$t->set_var('BREADCRUMBS',$breadcrumbs);
//$email_username2=urldecode($email_username);
//$t->set_var('EMAIL_USER_NAME2',$email_username2);
//$t->set_var('EMAIL_USER_NAME',$_GET['email_username']);

$t->parse('CONTENTS', 'header', true);
get_navigation();
$body_error = sprint_error($errors['body']);
$subject_error = sprint_error($errors['subject']);
$t->set_var('FROM_EMAIL_ERROR',$errors['from_email']);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('SUBJECT_ERROR',$subject_error);
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('SUBJECT',$subject);
$t->set_var('BODY',$body);

$t->set_var('EMAIL_USER_STRING',sprintf(ucfirst($general_strings['email'])));
$t->set_var('SUBJECT_STRING',$general_strings['subject']);
$t->set_var('MESSAGE_STRING',$general_strings['message']);
$t->set_var('CC_STRING',$general_strings['carboncopy']);
$t->set_var('CC_INSTRUCTIONS',$general_strings['cc_instructions']);
$t->set_var('ATTACHMENT_STRING',$general_strings['attachment']);
$t->set_var('COPY_SELF_STRING',$general_strings['copy_self']);
$t->set_var('SEND_STRING',$general_strings['send']);
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

function check_form_input() 
{
	
	global $general_strings, $CONFIG; 

	// Initialize the errors array

	$errors = array();


	//check to see if we have all the information we need
	if(!$_POST['subject']) {

		$errors['subject'] = $general_strings['no_subject'];

	}


	if(!$_POST['body']) {

		$errors['body'] = $general_strings['no_message'];

	}

	if(empty($_SESSION['current_user_key']) && !is_email($_POST['from_email'])) {
		require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');
		$errors['from_email'] = $user_strings['no_email'];
	}
	return $errors;
	
} //end check_form_input
?>
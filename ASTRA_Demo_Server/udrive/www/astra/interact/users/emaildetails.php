<?php
// +----------------------------------------------------------------------+
// | emaildetails  1.0													|
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
// | email a user their login details									 |
// |																	  |
// |																	  |
// |																	  |
// |																	  |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz>					 |
// | Last Modified 15/04/03											   |
// +----------------------------------------------------------------------+

require_once('../local/config.inc.php');
$email_address = isset($_GET['email'])? $_GET['email'] : '';
if ($_POST['submit']) {

	$email_address = $_POST['email_address'];
	$last_name = $_POST['last_name'];
	
	$rs = $CONN->Execute("SELECT username,password,first_name,last_name FROM {$CONFIG['DB_PREFIX']}users WHERE  email='$email_address' AND last_name='$last_name'");


	if ($rs->EOF) {
	
	   $message = 'There is no current user account with the details you provided';
		
	} else {

		if (!isset($objUser) || !is_object($objUser)) {
			if (!class_exists('InteractUser')) {
				require_once('../includes/lib/user.inc.php');
			}
			$objUser = new InteractUser();
		}
			
		while (!$rs->EOF) {

			$username  = $rs->fields[0];
			if ($CONFIG['AUTH_TYPE']=='dbplain') {
				$password   = $rs->fields[1];
			} else {
				$password = $objUser->generatepassword();
				$encrypt_password = md5($password);
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}users SET password='$encrypt_password' WHERE username='$username'");				
			}
			
			$first_name = $rs->fields[2];
			$last_name  = $rs->fields[3];						
			
			$headers = '';
			//now email the user their details
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
			emailUser($mail_object, $first_name, $last_name, $email_address, $username, $password);
			$message = "Your login details have been emailed to $email_address";
			$rs->MoveNext();
	
		}
			
	}

	$rs->Close();

}

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
 	'form'			=> 'users/emaildetails.ihtml',
	'footer'		  => 'footer2.ihtml'));


set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('PAGE_TITLE','Get Account details');
$t->set_var('EMAIL_ADDRESS',$email_address);
$t->set_var('LAST_NAME',$last_name);
$t->set_var('MESSAGE',$message);
$t->parse('CONTENTS', 'header', true); 
$t->parse('CONTENTS', 'form', true);
$t->parse("CONTENTS", "footer", true);

print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;

function emailUser($mail_object, $first_name, $last_name, $email, $username, $password) {

	global $CONFIG, $general_strings;

	$subject = $CONFIG['SERVER_NAME'].' '.$general_strings['login_details'];
	$message = sprintf(str_replace("<br />","\n",$general_strings['login_details_email']), $first_name, $last_name, $CONFIG['SERVER_NAME'], $CONFIG['SERVER_URL'].$CONFIG['PATH'], $username, $password);
	
	$headers['From']	= $CONFIG['NO_REPLY_EMAIL'];
	$headers['To']	  = $email;
	$headers['Subject'] = $subject;
	   
	$result = $mail_object->send($email, $headers, $message);

	if (PEAR::isError($result)) {
	
		print "mail error: ".$result->getMessage()."<br />\n";
	
	} else {
	
		return true;
		
	}
	
}//end emailUser
?>
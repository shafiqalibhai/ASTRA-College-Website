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
* Database upload
*
* Uploads user accounts from another database
* 
*
* @package Admin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: dbupload.php,v 1.11 2007/04/24 04:03:24 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
$CONFIG['NO_SESSION'] = 1;
require_once('../../local/config.inc.php');

//set to 1 if you want email address generated based on username@emaildomian
$auto_email	= 0;

//if set to 1 above then specify the email domain
$email_domain  = '';

//set to 1 if you want new account details emailed to users
$email_user	= 1;

//any extra note you want included in the email 
$email_note = '';

//set to 1 if you want password auto generated
$auto_password = 0;

$language_key=1;

//now connect to the database that you will get account info from
//replace the following with details about your central user accounts db server
$CONN2 = &ADONewConnection('mysql'); 
$CONN2->Connect('yourdb.com','dbuser','dbpassword','dbname');	

//edit the following sql to retrieve relevant data from your user accounts db

$sql = "SELECT first_name,last_name,user_key,username, email, password  FROM {$CONFIG['DB_PREFIX']}users";

$accounts_rs = $CONN2->Execute($sql);

if (!$accounts_rs->EOF) {

	if ($email_user==1) {
		
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
			
	}
		
	while (!$accounts_rs->EOF) {
			   
			$first_name_esc  = $CONN->qstr($accounts_rs->fields[0]);
			$last_name_esc   = $CONN->qstr($accounts_rs->fields[1]);
			$id_number_esc   = $CONN->qstr($accounts_rs->fields[2]);
			$username_esc   = $CONN->qstr($accounts_rs->fields[3]);
			$email_esc	   = $CONN->qstr($accounts_rs->fields[4]);			
			$password_esc	= $CONN->qstr($accounts_rs->fields[5]);
			$first_name	  = $accounts_rs->fields[0];
			$last_name	   = $accounts_rs->fields[1];
			$id_number	   = $accounts_rs->fields[2];
			$username	   = $accounts_rs->fields[3];
			$email		   = $accounts_rs->fields[4];			
			$password		= $accounts_rs->fields[5];			
			$account_status  = $accounts_rs->fields[6];
			$usergroup_key   = $accounts_rs->fields[7];
			$accesslevel_key = $accounts_rs->fields[8];
			
			//set any default statuses, etc. rem this out if set 
			//from database fields above
			
			$account_status='1'; 
			$usergroup_key='1'; 
			$accesslevel_key='3';

			if ($auto_email==1) {
			
				$email	 = $users_array[3].'@'.$email_domain;
				$email_esc = $CONN->qstr($email);

			}
			
			if ($auto_password==1) {
			
				$password	 = generatepassword();
				$password_esc = $CONN->qstr($password);
				
			}
			
			if ($first_name=='' || $last_name=='' || $username=='' || ($email=='' && $auto_email!=1) || $account_status=='' || $usergroup_key=='' || $accesslevel_key=='') {
			
				$errors['empty_fields'] = 'Some of the required fields are blank';	
				
			}	
			
						
			if (count($errors)>0) {
			
				$message .= "There was a problem adding the user $first_name $last_name\n";
				
				foreach ($errors as $value) {
				
					$message .= $value."\n";
				
				}
				
			} else {
			
				$sql_check_exists = "SELECT user_key,account_status from {$CONFIG['DB_PREFIX']}users WHERE username=$username_esc";
				$rs_check_exists = $CONN->Execute($sql_check_exists);

				//if user doesn't exist in interact database then create an account

				if ($rs_check_exists->EOF) {
		
				// get random subdirectory number to create user directory

				mt_srand ((float) microtime() * 1000000);
	  			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));

				$sql_add_new = "INSERT INTO {$CONFIG['DB_PREFIX']}users(user_id_number,username,first_name,last_name,password,email,level_key,date_added,account_status, language_key) VALUES ($id_number,$username_esc,$first_name_esc,$last_name_esc,$password_esc,$email_esc,'$accesslevel_key',$date_added,'$account_status', '$language_key')";

				if ($CONN->Execute($sql_add_new)!=false) {

					//get user_key
			
					$user_key = $CONN->Insert_ID(); 
			
					//now create the user directory
					$subdirectory = mt_rand(1,50);
					$users_file_path = $subdirectory.'/'.$user_key;
					$subdirectory_path=$CONFIG['USERS_PATH'].'/'.$subdirectory;
					$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}users SET file_path='$users_file_path' WHERE user_key='$user_key'");
						
					if (!Is_Dir($subdirectory_path)) {
			
						mkdir($subdirectory_path,0777);
						chown($subdirectory_path,nobody);
			
					}
			
					$full_path=$CONFIG['USERS_PATH'].'/'.$users_file_path;
					mkdir($full_path,0777);
					chown($full_path,nobody);
			
					if ($usergroup_key && $usergroup_key>0) {
					
						addToUserGroups($user_key, $usergroup_key);
						
					}
					
					if ($email_user==1) {
					
						emailUser($mail_object, $first_name, $last_name, $email, $username, $password);
					
					}
					
					
				
				} else {
		
					echo $CONN->ErrorMsg();
			
				}

		
			} else {

				//if user account already exists then flag as active and synch any other fields you
				//want to match with central user database
		
				$update_user_sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET account_status='$account_status' WHERE username=$username_esc";
				$CONN->Execute($update_user_sql);
				$message .= "$first_name $last_name on line $n successfully updated<br />";

			}
		
		}
			 
		$n++;
		unset($errors);

		$accounts_rs->MoveNext();
	}
	
}



echo $message;
exit;

function generatepassword ($length = 6)
{

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOP"; 
	
  // set up a counter
  $i = 0; 
	
  // add random characters to $password until $length is reached
  while ($i < $length) { 

	// pick a random character from the possible ones
	$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		
	// we don't want this character if it's already in the password
	if (!strstr($password, $char)) { 
	  $password .= $char;
	  $i++;
	}

  }

  // done!
  return $password;

}

function addToUserGroups($user_key, $usergroup_key) {

	global $CONN, $CONFIG;

	//add to relevant user group table
			
	$user_group_sql = "INSERT INTO {$CONFIG['DB_PREFIX']}user_usergroup_links VALUES('$user_key','$usergroup_key')";
	$CONN->Execute($user_group_sql);
			echo $sql;
	//find default sites for this user group
	
	$default_sites_sql = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}default_space_user_links WHERE user_group_key='$usergroup_key'";
			
	$rs_default_sites = $CONN->Execute($default_sites_sql);

	while (!$rs_default_sites->EOF) {
			
		$space_key=$rs_default_sites->fields[0];
		$sql2="INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links VALUES ('$space_key','$user_key','2')";
		$CONN->Execute($sql2);
		$rs_default_sites->MoveNext();
				
	}

	return true;
	
}//end addToUserGroups

function emailUser($mail_object, $first_name, $last_name, $email, $username, $password) {

	global $CONFIG, $general_strings, $email_note;

	$subject = $CONFIG['SERVER_NAME'].' '.$general_strings['login_details'];
	$message = sprintf($general_strings['login_details_email'], $first_name, $last_name, $CONFIG['SERVER_NAME'], $CONFIG['SERVER_URL'], $username, $password);
	$message .= "\n\n$email_note";	
	
	$headers['From']	= $CONFIG['NO_REPLY_EMAIL'];
	$headers['To']	  = $email;
	$headers['Subject'] = $subject;
	   
	$result = $mail_object->send($email, $headers, $message);

	if (PEAR::isError($result)) {
	
		print "mail error: ".$result->getMessage()."<br />\n";
	
	} else {
	
		return true;
		
	}
	
	return true;
	
}//end emailUser
?>
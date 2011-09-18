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
* Bulk upload
*
* Uploads user accounts from a text file
* 
*
* @package Admin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: bulkupload.php,v 1.20 2007/04/24 04:03:24 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

if ($_SERVER['REQUEST_METHOD']=='POST') {

	$file			  = $_FILES['user_file'];
	$auto_email		= $_POST['auto_email'];
	$email_domain	  = $_POST['email_domain'];	
	$auto_password	 = $_POST['auto_password'];
	$extra_user_groups = $_POST['extra_user_groups'];
	$email_user		= $_POST['email_user'];
	$language_key	  = $_POST['language_key'];
	$email_note		= $_POST['email_note'];
	$spaces			= $_POST['spaces'];		
	
	if (!$language_key) {
	
		$language_key=1;
		
	}		
		
	if (!$file || $file=='none') {
	
		$message = "You did not upload a file - try again!";
		
	} else {
	 
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
		
		$fcontents = file ($file['tmp_name']);
 
		$n = 1;
		
		while (list ($line_num, $line) = each ($fcontents)) {
			   
			$errors = array();
			$line=trim($line);
			$users_array	 = explode('	',$line);
			$line			= trim($line);
			$first_name	  = $users_array[0];
			$last_name	   = $users_array[1];
			$id_number	   = $CONN->qstr($users_array[2]);
			$username	   = $users_array[3];
			$password	   = $users_array[4];
			$email		   = $users_array[5];			
			$first_name_esc  = $CONN->qstr($users_array[0]);
			$last_name_esc   = $CONN->qstr($users_array[1]);
			$username_esc   = $CONN->qstr($users_array[3]);
			if ($auto_password==1) {
				$password = generatepassword();
			} else {
				$password = $users_array[4];
			}
			
			if ($CONFIG['AUTH_TYPE']=='dbplain') {
				$password_esc = $CONN->qstr($password);
			} else {
				$password_esc = $CONN->qstr(md5($password));
			}
			$email_esc	   = $CONN->qstr($users_array[5]);
			$account_status  = isset($users_array[6])?$users_array[6]:'1';
			$email_esc	   = $CONN->qstr($users_array[5]);
			$account_status  = $users_array[6];
			$usergroup_key   = $users_array[7];
			$accesslevel_key = $users_array[8];
			$details_esc = "'".$CONN->qstr($users_array[9],ini_get("magic_quotes_gpc"),true)."'";
			
			if ($auto_email==1) {
			
				$email	 = $users_array[3].'@'.$email_domain;
				$email_esc = $CONN->qstr($email);

			}
			
			$count = count($users_array);
			
			if (count($users_array)<9) {
			
				$errors['column_count'] = 'There are some fields missing for this user';
				
			}
			
			if ($first_name=='' || $last_name=='' || $username=='' || ($email=='' && $auto_email!=1) || $account_status=='' || $usergroup_key=='' || $accesslevel_key=='') {
			
				$errors['empty_fields'] = 'Some of the required fields are blank';	
				
			}	
			
						
			if (count($errors)>0) {
			
				$message .= "<span class=\"message\">There was a problem adding the user on line $n</span> $first_name $last_name<br />";
				
				foreach ($errors as $value) {
				
					$message .= $value.'<br />';
				
				}
				
			} else {
			
				$sql_check_exists = "SELECT user_key,account_status from {$CONFIG['DB_PREFIX']}users WHERE username=$username_esc";
				$rs_check_exists = $CONN->Execute($sql_check_exists);

				//if user doesn't exist in interact database then create an account

				if ($rs_check_exists->EOF) {
		
				// get random subdirectory number to create user directory

				mt_srand ((float) microtime() * 1000000);
	  			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));

				$sql_add_new = "INSERT INTO {$CONFIG['DB_PREFIX']}users(user_id_number,username,first_name,last_name,password,email,level_key,date_added,account_status, language_key, details) VALUES ($id_number,$username_esc,$first_name_esc,$last_name_esc,$password_esc,$email_esc,'$accesslevel_key',$date_added,'$account_status', '$language_key', $details_esc)";

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
			
					addToUserGroups($user_key, $usergroup_key);
					
					if (is_array($extra_user_groups)) {
					
						
						foreach ($extra_user_groups as $extra_group_key) {
						
							addToUserGroups($user_key, $extra_group_key);
							
						}
						
					}
					
					if (is_array($spaces)) {
					
						
						foreach ($spaces as $extra_space_key) {
						
							addToSpaces($user_key, $extra_space_key);
							
						}
						
					}

					if ($email_user==1) {
					
						emailUser($mail_object, $first_name, $last_name, $email, $username, $password);
					
					}
					
					$message .= "$first_name $last_name on line $n successfully added<br />";
				
				} else {
		
					echo $CONN->ErrorMsg();
			
				}

		
			} else {

				//if user account already exists then flag as active and synch any other fields you
				//want to match with central user database
		
				$update_name = !empty($_POST['update_name'])?",first_name=$first_name_esc, last_name=$last_name_esc":'';
				$update_id = !empty($_POST['update_id'])?",user_id_number=$id_number":'';
				$update_email = !empty($_POST['update_email'])?",email='$email'":'';
				$update_password = !empty($_POST['update_password'])?",password=$password_esc":'';
				$update_user_sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET account_status='$account_status' $update_name $update_id $update_email $update_password WHERE user_name=$user_name_esc";
				$CONN->Execute($update_user_sql);
				
				//get user_key
				$rs_user_key = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username=$username_esc");
				
				while(!$rs_user_key->EOF) {
				
					$user_key = $rs_user_key->fields[0];
					$rs_user_key->MoveNext();
					
				}
				
				$rs_user_key->Close();
				
				addToUserGroups($user_key, $usergroup_key);
					
				if (is_array($extra_user_groups)) {
					
						
					foreach ($extra_user_groups as $extra_group_key) {
						
						addToUserGroups($user_key, $extra_group_key );
							
					}
						
				}
				
				if (is_array($spaces)) {
					
					foreach ($spaces as $extra_space_key) {
					
						addToSpaces($user_key, $extra_space_key);
							
					}
						
				}
				
				$message .= "$first_name $last_name on line $n successfully updated<br />";

			}
		
		}
			 
		$n++;
		unset($errors);

	}
	
}

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'form'	   => 'admin/bulkupload.ihtml',
	'footer'	 => 'footer.ihtml'));
$group_sql = "SELECT group_name, user_group_key FROM {$CONFIG['DB_PREFIX']}user_groups ORDER BY group_name";
$group_menu = make_menu($group_sql,"extra_user_groups[]",extra_user_groups,"4",true);
$space_sql = "SELECT name, space_key FROM {$CONFIG['DB_PREFIX']}spaces ORDER BY name";
$space_menu = make_menu($space_sql,"spaces[]",$spaces,"4",true);

$rs = $CONN->Execute("SELECT user_group_key, group_name FROM {$CONFIG['DB_PREFIX']}user_groups ORDER BY group_name");
$user_group_list = '';
while(!$rs->EOF) {
	$user_group_list .= $rs->fields[0].'='.$rs->fields[1].'<br />';
	$rs->MoveNext();
}
set_common_admin_vars('User Lookup', $message);

require_once($CONFIG['INCLUDES_PATH']."/lib/languages.inc.php");
$t->set_var("LANGUAGE_MENU",lang_menu('language_key'));

$t->set_var("USER_GROUP_MENU","$group_menu");
$t->set_var("USER_GROUP_LIST",$user_group_list);
$t->set_var("SPACE_MENU","$space_menu");
$t->set_var('MESSAGE',$message);
$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');
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
	$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));
	//add to relevant user group table
			
	$user_group_sql = "INSERT INTO {$CONFIG['DB_PREFIX']}user_usergroup_links VALUES('$user_key','$usergroup_key')";
	$CONN->Execute($user_group_sql);
  
	//find default sites for this user group
	
	$default_sites_sql = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}default_space_user_links WHERE user_group_key='$usergroup_key'";
			
	$rs_default_sites = $CONN->Execute($default_sites_sql);

	while (!$rs_default_sites->EOF) {
			
		$space_key=$rs_default_sites->fields[0];
		$sql2="INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key,user_key, access_level_key,date_added) VALUES ('$space_key','$user_key','2',$date_added)";
		$CONN->Execute($sql2);
		$rs_default_sites->MoveNext();
				
	}

	return true;
	
}//end addToUserGroups

function addToSpaces($user_key, $space_key) {

	global $CONN, $CONFIG;

	//add to relevant space
	$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));				
	$space_sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key,user_key, access_level_key,date_added) VALUES('$space_key','$user_key','2', $date_added)";
	$CONN->Execute($space_sql);
	return true;
	
}//end addToSpaces

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
	
	} 
	
	return;
	
}//end emailUser
?>
<?php
/**
* Add a new user account to interact from Smartnet routines
* This script is just for use on SmartNet servers
* @param int $user_data array of user data ('username','first_name', 'last_name','email')
* @return true return true if add successful
*/

$CONFIG['NO_SESSION'] = 1;

require_once('../../local/config.inc.php');

//two lines below for testing only
//$user_data = array('username' => 'harryhh','first_name' => 'Harry','last_name' => 'HarHar','email' => 'harrhh@har.com');
//snetAddUser($user_data);
 	
function snetAddUser($user_data)
{
	global $CONN, $CONFIG;

	$username_esc		= $CONN->qstr($user_data['username']);
	$first_name_esc		= $CONN->qstr($user_data['first_name']);
	$last_name_esc		= $CONN->qstr($user_data['last_name']);
	$email_esc			= $CONN->qstr($user_data['email']);
	$date_added 		= $CONN->DBDate(date('Y-m-d H:i:s'));
	$language_key=1;
	$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}users(username, first_name, last_name, email,level_key,date_added, language_key, account_status, read_posts_flag)  VALUES ($username_esc,$first_name_esc,$last_name_esc, $email_esc,'3',$date_added,'$language_key', '1','1')";
	
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error adding your account: '.$CONN->ErrorMsg().' <br />';
		return $message;
	
	} else {
		
		$user_key = $CONN->Insert_ID();
			
		// now create user directories
	   	// get random subdirectory number
		
		mt_srand ((float) microtime() * 1000000);
		$subdirectory = mt_rand(1,50);
		$users_file_path = $subdirectory.'/'.$user_key;
		$subdirectory_path=$CONFIG['USERS_PATH'].'/'.$subdirectory;
		
		if (!is_dir($subdirectory_path)) {
				
			mkdir($subdirectory_path,0777);
			
		}
			
		$full_path=$CONFIG['USERS_PATH'].'/'.$users_file_path;
		mkdir($full_path,0777);
		
		if (!is_dir($full_path)) {
		
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$user_key'");
			$message = 'There was an error adding your account - user directory could not be created';
			return $message;
		
		} else {
		
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}users SET file_path='$users_file_path' WHERE user_key='$user_key'");
			return true;
							
		}
		
	}

} //end addUser	
?>
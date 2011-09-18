<?php
// +----------------------------------------------------------------------+
// | userinput.php  1.0												   |
// +----------------------------------------------------------------------+
require_once('../local/config.inc.php');



if ($_SERVER['REQUEST_METHOD']=='GET') {

	$space_key = $_GET['space_key'];
	
} else {

	$space_key   = $_POST['space_key'];
	
}
$current_user_key = $_SESSION['current_user_key'];

if ($CONFIG['DEVOLVE_ACCOUNT_CREATION']!=1) {

	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message={$general_strings['no_edit_rights']}");

}

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 
$page_details=get_page_details($space_key);

//find out what action we need to take
if ($_SERVER['REQUEST_METHOD']=='POST') {

	$new_username	= $_POST['new_username'];
	$password		 = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];
	$first_name	   = $_POST['first_name'];
	$last_name		= $_POST['last_name'];
	$email			= $_POST['email'];
	$details		  = $_POST['details'];
	$user_group_key   = $_POST['user_group_key'];
	$language_key	 = $_POST['language_key'];		
	$user_id_number   = $_POST['user_id_number'];
	$access_level_key = 3;	
	$photo			= $_FILES['photo'];
	$action		   = $_POST['action'];
	$email_note	   = $_POST['email_note'];	
	$email_user	   = $_POST['email_user'];	

	switch($action) {

		//if we are adding a new folder  form input needs to be checked 
		case add:
		$errors = check_form_input();

		//if there are no errors then add the data
		if(count($errors) == 0) {
			$date_added=$CONN->DBDate(date("Y-m-d H:i:s"));
			$message = add_user();

			//if the add was successful return the browser to space home or parent folder
			if ($message=="true") {
				 
				 Header ("Location: {$CONFIG['FULL_URL']}/spaceadmin/userinput.php?space_key=$space_key&message=The+user+account+has+been+added");
				 exit;
			}
		}else {
			$button = "Add";
			$message = 'There was a problem, see below for details';
		}
		break;
		
	} //end switch $action

} //end if (isset($action))


if (!isset($action) || $action=="add") {
	$action = "add";
	$title = "Add a User Account";
	$button = "Add";
 
}

$group_sql = "SELECT group_name, user_group_key FROM {$CONFIG['DB_PREFIX']}user_groups ORDER BY group_name";
$group_menu = make_menu($group_sql,"user_group_key",$user_group_key,"4",true);
$access_level_menu = make_menu("SELECT name,access_level_key FROM {$CONFIG['DB_PREFIX']}access_levels",'access_level_key',$access_level_key,'2');
//format any errors from form submission

$username_error = sprint_error($errors["username"]);
$password_error = sprint_error($errors["password"]);
$confirm_password_error = sprint_error($errors["confirm_password"]);
$first_name_error = sprint_error($errors["first_name"]);
$last_name_error = sprint_error($errors["last_name"]);
$email_error = sprint_error($errors["email"]);
$user_group_error = sprint_error($errors["user_group"]);
$access_level_error = sprint_error($errors["access_level"]);
$photo_error = sprint_error($errors["photo"]);
//get the required template files
require_once($CONFIG['BASE_PATH'].'/includes/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "header.ihtml",
	'navigation'	  => 'navigation.ihtml',
	"form"			=> "spaceadmin/userinput.ihtml",
	"footer"		  => "footer.ihtml"));

//generate the header,title, 
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var("USER_NAME_ERROR","$username_error");
$t->set_var("USER_GROUP_ERROR","$user_group_error");
$t->set_var("ACCESS_LEVEL_ERROR","$access_level_error");
$t->set_var("PASSWORD_ERROR","$password_error");
$t->set_var("CONFIRM_PASSWORD_ERROR","$confirm_password_error");
$t->set_var("FIRST_NAME_ERROR","$first_name_error");
$t->set_var("LAST_NAME_ERROR","$last_name_error");
$t->set_var("EMAIL_ERROR","$email_error");
$t->set_var("PHOTO_ERROR","$photo_error");
$t->set_var("NEW_USER_NAME","$new_username");
$t->set_var("PASSWORD","$password");
$t->set_var("CONFIRM_PASSWORD","$confirm_password");
$t->set_var("FIRST_NAME","$first_name");
$t->set_var("LAST_NAME","$last_name");
$t->set_var("EMAIL","$email");
$t->set_var("GROUP_MENU","$group_menu");
$t->set_var("SPACE_KEY","$space_key");
$t->set_var("ACCESS_LEVEL_MENU","$access_level_menu");
$t->set_var("DETAILS","$details");
$t->set_var("PHOTO","$photo_tag");
$t->set_var("CURRENT_PHOTO","$current_photo");
$t->set_var("ACTION","$action");
$t->set_var("BUTTON","$button");
$t->set_var("USER_KEY","$user_key");
$t->set_var("REQUEST_URI","$request_uri");
$t->parse("CONTENTS", "header", true); 
get_navigation();
$t->parse("CONTENTS", "form", true);

$t->parse("CONTENTS", "footer", true);
print_headers();

//output page

$t->p("CONTENTS");
$CONN->Close();
exit;

/**
* Add a user  
* 
* @return true
*/
function add_user()
{
	global $CONN,$new_username,$password,$first_name,$last_name,$email,$details,$date_added,$photo,$user_group_key, $access_level_key, $user_id_number, $CONFIG, $space_key, $email_user;

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
	$first_name = $first_name;
	$last_name = $last_name;
	$email = $email;
	$details = $details; 
 		if ($CONFIG['AUTH_TYPE']=='dbplain') {
			$password = $password;
		} else {
			$password = md5($password);
		}

	$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}users(username, password, user_id_number, first_name, last_name, prefered_name, email, details, level_key,date_added, account_status, language_key, read_posts_flag) VALUES ('$new_username','$password','$user_id_number','$first_name','$last_name','','$email','$details','$access_level_key',$date_added,'1','".$CONFIG['DEFAULT_LANGUAGE']."','1')";

	if ($CONN->Execute("$sql") === false) {
	   
		$message =  'There was an error adding your account: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {
		
		//add to selected user groups
		
		$user_key = $CONN->Insert_ID();
		
		//make member of current space
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key) VALUES ('$space_key','$user_key','2')");
		$num_selected = count($user_group_key);
		
		foreach ($user_group_key as $value) {

			$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}user_usergroup_links VALUES('$user_key','$value')";
			$CONN->Execute($sql);
			$sql = "SELECT space_key from {$CONFIG['DB_PREFIX']}default_space_user_links where user_group_key='$value'";
			$rs = $CONN->Execute($sql);
			
			while (!$rs->EOF) {
			
				$space_key=$rs->fields[0];
				$sql2="INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links VALUES ('$space_key','$user_key','2')";
				$CONN->Execute($sql2);
				$rs->MoveNext();
					
			}
			
	   }
	   
	   // now create user diretories
	   
		// get random subdirectory number
		mt_srand ((float) microtime() * 1000000);
		$subdirectory = mt_rand(1,50);
		$users_file_path = $subdirectory."/".$user_key;
		$subdirectory_path=$CONFIG['USERS_PATH']."/".$subdirectory;

		//if subdirectory path doesn't exist create it
		$sql="UPDATE {$CONFIG['DB_PREFIX']}users SET file_path='$users_file_path' WHERE user_key='$user_key'";
		if ($CONN->Execute("$sql") === false) {
	   
			$message =  'There was an error adding your account: '.$CONN->ErrorMsg().' <br />';
			return $message;
		} else {
		
			if (!Is_Dir($subdirectory_path)) {
				mkdir($subdirectory_path,0777);
			}
			$full_path=$CONFIG['USERS_PATH']."/".$users_file_path;
			mkdir($full_path,0777);
		
			if ($photo['name']!='' && $photo!='none') {
				$message = upload_photo($photo,$user_key,$full_path);
				if ($message===true) {
					return true;  
				} else {
					return $message;
				}
			} else {
			
				if ($email_user==1) {
					
					emailUser($mail_object, $first_name, $last_name, $email, $new_username, $password);
					
				}
				
				return true;
			}
		}
	}
} //end add_user


function check_form_input() 
{
global $CONN,$HTTP_POST_VARS, $new_username,$password,$confirm_password,$first_name,$last_name,$email, $photo, $photo_type,$action,$user_group_key, $access_level_key, $CONFIG ;
// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}

//check to see if we have all the information we need
	if ($action=="add"){
		if(!$new_username) {

			$errors["username"] = "You didn't enter a User name.";

		}
		if (!is_alphanumeric($new_username,"5","16")) {
			$errors["username"] = "Your username can only contain letters and numbers, must be at least 5 characters long, and no more the 16 characters long.";
		}
		$sql = "select user_key from {$CONFIG['DB_PREFIX']}users where username='$new_username'";
		$rs = $CONN->Execute("$sql");
		if (!$rs->EOF) {
			$errors["username"] = "That User name is already taken, please try another";		
		}
	}

	if(!$password) {

		$errors["password"] = "You didn't enter a password.";

	}
	if (!is_alphanumeric($password,"5")) {
		$errors["password"] = "Your password can only contain letters and numbers and must be at least 5 characters long.";
	}
	if(!$confirm_password) {

		$errors["confirm_password"] = "You didn't confirm your password.";

	}
	if($confirm_password!=$password) {

		$errors["confirm_password"] = "Your passwords didn't match.";

	}
	if(!$first_name) {

		$errors["first_name"] = "You didn't tell us your first name.";

	}
	if(!$last_name) {

		$errors["last_name"] = "You didn't tell us your last name.";

	}
	
	if(!$user_group_key) {

		$errors["user_group"] = "You didn't select a group.";

	}
	if(!$access_level_key) {

		$errors["access_level"] = "You didn't select an access level.";

	}	

	if(!$email) {

		$errors["email"] = "You didn't tell us your email address.";

	} else {
		if(is_email($email)!=true) {
			$errors["email"] = "You didn't enter a valid email address.";
		}
	}
//if there is a photo check that it is the right format

	 if ($photo['name']!='' & $photo!='none') {   
	
		 $registered_types = array(
		   			'image/bmp'	=> '.bmp, .ico',
					'image/gif'	=> '.gif',
					'image/pjpeg'  => '.jpg, .jpeg',
					'image/jpeg'   => '.jpg, .jpeg'
					); 

		 $allowed_types = array('image/jpeg','image/pjpeg','image/gif');
		 
		 if (!in_array($photo_type,$allowed_types)) {
		 
			 $errors['photo'] = $user_strings['invalid_photo'];
		
		 }

	 }
	 
return $errors;
} //end check_form_input

/**
* Upload photo   
* 
*  
* @return true
*/

function upload_photo($photo,$user_key,$full_path)
{
	global $CONN, $current_photo,$action, $CONFIG;
	$my_max_file_size = '102400'; 
	$image_max_width = '200';
	$image_max_height = '160';


	$size = GetImageSize($photo['tmp_name']);
	list($foo,$width,$bar,$height) = explode("\"",$size[3]);
	
	if ($width > $image_max_width) {
		
		$newwidth=200;
		$factor=$image_max_width/$width;
		$newheight=$height*$factor;
		exec("mogrify -geometry \"$newwidth x $newheight\" \"{$photo['tmp_name']}\"");
	
	}
	
	if(ereg('gif',$photo['type'])){
		
		$extension = '.gif';
	
	} else {
		
		$extension = '.jpg';
	
	}
	
	$photo_name = $user_key.$extension;	
	
	if ($action='modify2') {
		
		if ($current_photo!='') {
			
			$current_photo_path=$full_path.'/'.$current_photo;
			
			if (file_exists($current_photo_path)) {
				
				if ($photo_name!=$current_photo) {
					
					unlink($current_photo_path);
				
				}
			
			}
		
		}
	
	}

	copy($photo['tmp_name'],$full_path.'/'.$photo_name);
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET photo='$photo_name' WHERE user_key='$user_key'";
	$CONN->Execute($sql);
	return true;  

} //end upload_photo

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
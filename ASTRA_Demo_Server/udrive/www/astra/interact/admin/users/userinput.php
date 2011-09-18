<?php
require_once('../../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');


//check to see if user is logged in. If not refer to Login page.
authenticate_admins();
require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');
//find out what action we need to take
if ($_SERVER['REQUEST_METHOD']=='POST') {

	//create user object if it doesn't already exist	
	if (!class_exists('InteractUser')) {
		require_once('../../includes/lib/user.inc.php');
	}
	if (!is_object($objUser)) {
		$objUser = new InteractUser();
	}
	foreach ($_POST as $key => $value) {
	
		$user_data[$key] = $value;
	
	}
	$action		   = $_POST['action'];

	switch($action) {

		//if we are adding a new folder  form input needs to be checked 
		case add:
		$errors = check_form_input($user_data);

		//if there are no errors then add the data
		if(count($errors) == 0) {
			
			$message = $objUser->addUser($user_data,!empty($user_data['email_user'])?true:false);

			//if the add was successful return the browser to space home or parent folder
			if ($message=="true") {
				 
				 Header ("Location: {$CONFIG['FULL_URL']}/admin/users/userinput.php?message=The+user+account+has+been+added");
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

$group_sql = "SELECT group_name, user_group_key FROM {$CONFIG['DB_PREFIX']}user_groups ORDER BY group_name DESC";
$group_menu = make_menu($group_sql,"user_group_keys",$user_data['user_group_keys'],"2",true);
$access_level_menu = make_menu("SELECT name,access_level_key FROM {$CONFIG['DB_PREFIX']}access_levels ORDER BY access_level_key DESC",'level_key',$user_data['level_key'],'4');
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
$phone_no_error = sprint_error($errors["phone_no"]);
$branch_error = sprint_error($errors["branch"]);
$id_no_error = sprint_error($errors["id_no"]);
//get the required template files
require_once($CONFIG['BASE_PATH'].'/includes/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "header.ihtml",
	'navigation' => 'admin/adminnavigation.ihtml',
	"form"			=> "admin/userinput.ihtml",
	"footer"		  => "footer.ihtml"));

//generate the header,title, 
set_common_admin_vars($title, $message);
$t->set_var("USER_NAME_ERROR","$username_error");
$t->set_var("USER_GROUP_ERROR","$user_group_error");
$t->set_var("ACCESS_LEVEL_ERROR","$access_level_error");
$t->set_var("PASSWORD_ERROR","$password_error");
$t->set_var("CONFIRM_PASSWORD_ERROR","$confirm_password_error");
$t->set_var("FIRST_NAME_ERROR","$first_name_error");
$t->set_var("LAST_NAME_ERROR","$last_name_error");
$t->set_var("EMAIL_ERROR","$email_error");
$t->set_var("PHONE_NO_ERROR","$phone_no_error");
$t->set_var("BRANCH_ERROR","$branch_error");
$t->set_var("ID_NO_ERROR","$id_no_error");
$t->set_var("PHOTO_ERROR","$photo_error");
$t->set_var('PASSWORD',isset($user_data['password'])? $user_data['password']: '');
$t->set_var('CONFIRM_PASSWORD',isset($user_data['confirm_password'])? $user_data['confirm_password']: $user_data['password']);
$t->set_var('USER_NAME',isset($user_data['username'])? $user_data['username'] : '');
$t->set_var('FIRST_NAME',isset($user_data['first_name'])? $user_data['first_name'] : '');
$t->set_var('LAST_NAME',isset($user_data['last_name'])? $user_data['last_name'] : '');
$t->set_var('PREFERED_NAME',isset($user_data['prefered_name'])? $user_data['prefered_name'] : '');
$t->set_var('EMAIL',isset($user_data['email'])? $user_data['email'] : '');
$t->set_var('GROUP_MENU',$group_menu);
$t->set_var('LANGUAGE_MENU',$language_menu);
$t->set_var('DETAILS',isset($user_data['details'])? $user_data['details'] : '');
$t->set_var('BRANCH',isset($user_data['branch'])? $user_data['branch'] : '');
$t->set_var('ADDRESS',isset($user_data['address'])? $user_data['address'] : '');
$t->set_var('PHOTO',isset($user_data['photo_tag'])? $user_data['photo_tag'] : '');
$t->set_var('USER_ID_NUMBER',isset($user_data['user_id_number'])? $user_data['user_id_number'] : '');
$t->set_var("GROUP_MENU","$group_menu");
$t->set_var("ACCESS_LEVEL_MENU","$access_level_menu");
$t->set_var("PHONE_NO","$phone_no");
$t->set_var("ATTENDANCE", "$attendance");
$t->set_var("BACKLOGS", "$backlogs");
$t->set_var("CURRENT_PHOTO","$current_photo");
$t->set_var("ACTION","$action");
$t->set_var("BUTTON","$button");
$t->set_var("USER_KEY","$user_key");
$t->set_var("REQUEST_URI","$request_uri");
$t->parse("CONTENTS", "header", true); 
admin_navigation();
$t->parse("CONTENTS", "form", true);

$t->parse("CONTENTS", "footer", true);
print_headers();

//output page

$t->p("CONTENTS");
$CONN->Close();
exit;


function check_form_input($user_data) 
{

	global $CONN, $CONFIG, $user_strings, $action;
	// Initialize the errors array

	$errors = array();

	//check to see if we have all the information we need
	
	if ($action=='add'){
		
		//if only one account per email then check account does not already exist for
		//given email
		
		if ($CONFIG['SINGLE_ACCOUNTS']==1) {
		
			if (isset($user_data['email'])) {
			
				$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE email='{$user_data['email']}'");
				
				
				if (!$rs->EOF) {
				
					$errors['email'] = sprintf($user_strings['email_used'],$user_data['email']);
					
				}
				
				$rs->Close();
			
			}
		
		
		}

		if(!$user_data['username']) {

			$errors['username'] = $user_strings['no_username'];

		}
		
		if (!is_alphanumeric($user_data['username'],'5','16')) {
			
			$errors['username'] = $user_strings['username_invalid'];
		
		}
		
		$sql = "select user_key from {$CONFIG['DB_PREFIX']}users where username='{$user_data['username']}'";
		$rs = $CONN->Execute($sql);
		
		if (!$rs->EOF) {
		
			$errors['username'] = $user_strings['username_taken'];		
		
		}
		
		if (!isset($user_data['auto_password'])) {
	
			if(!$user_data['password']) {

				$errors['password'] = $user_strings['no_password'];

			}
	
			if (!is_alphanumeric($user_data['password'],'5')) {
		
				$errors['password'] = $user_strings['short_password'];
	
			}
	
			if($user_data['confirm_password']!=$user_data['password']) {

				$errors['confirm_password'] = $user_strings['no_password_match'];

			}
		
		}

	}

	
	if(!$user_data['first_name']) {

		$errors['first_name'] = $user_strings['no_firstname'];

	}
	if(!$user_data['last_name']) {

		$errors['last_name'] = $user_strings['no_lastname'];

	}
	
	if(!$user_data['user_group_keys']) {

		$errors['user_group'] = $user_strings['no_group'];

	}

	if(!$user_data['email']) {

		$errors['email'] = $user_strings['no_email'];

	} else {
	
		if(is_email($user_data['email'])!=true) {
		
			$errors['email'] = $user_strings['no_email'];
			
		}
		
	}
	
	//if there is a photo check that it is the right format

	 if ($_FILES['photo']['name']!='') {   
	
		 $registered_types = array(
		   			'image/bmp'	=> '.bmp, .ico',
					'image/gif'	=> '.gif',
					'image/pjpeg'  => '.jpg, .jpeg',
					'image/jpeg'   => '.jpg, .jpeg'
					); 

		 $allowed_types = array('image/jpeg','image/pjpeg','image/gif');
		 
		 if (!in_array($_FILES['photo']['type'],$allowed_types)) {
		 
			 $errors['photo'] = $user_strings['invalid_photo'];
		
		 }

	 }
	
	return $errors;

} //end check_form_input

?>
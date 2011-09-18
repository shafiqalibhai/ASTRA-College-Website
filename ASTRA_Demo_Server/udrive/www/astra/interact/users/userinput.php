<?php
/**
* User input
*
* Add or modify a user account
*
* @package Users
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');

$user_data = array();

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$action = $_GET['action'];
	$user_group_key = isset($_GET['user_group_key'])? $_GET['user_group_key'] : '';
	$request_uri = isset($_GET['request_uri'])? $_GET['request_uri'] : '';
	
} else {

	$action = $_POST['action'];
	$submit = $_POST['submit'];
	$request_uri = isset($_POST['request_uri'])? $_POST['request_uri'] : '';
	$user_group_key = isset($_POST['user_group_key'])? $_POST['user_group_key'] : '';
	
	foreach ($_POST as $key => $value) {
	
		$user_data[$key] = $value;
	
	}


	if ($user_group_key!='') {
	
		array_push($user_data['user_group_keys'],$user_group_key);
	
	}


}

$current_user_key = $_SESSION['current_user_key'];
$referer = $_SERVER['HTTP_REFERER'];

//if they are modifying make sure they are logged in
if ($action=='modify' && empty($current_user_key)) {

	header("Location: {$CONFIG['FULL_URL']}/login.php");
	exit;
	
}
//see if user is allowed to add a new account

if ($CONFIG['SECURE_ACCOUNT_CREATION']==1 && !$_SESSION['current_user_key'] && !strpos($referer, '/users/secureaccounts.php') && !strpos($referer, '/users/userinput.php')) {

	header("Location: {$CONFIG['FULL_URL']}/users/secureaccounts.php?user_group_key=$user_group_key&request_uri=$request_uri");
	exit;
	
}

//create user object if it doesn't already exist
if (!class_exists('InteractUser')) {

	require_once('../includes/lib/user.inc.php');
	
}

if (!is_object($objUser)) {

	$objUser = new InteractUser();
	
}

//find out what action we need to take
if (isset($action)) {

	switch($action) {

		//if we are adding a new folder  form input needs to be checked 
		case add:
		
			$errors = check_form_input($user_data);

			//if there are no errors then add the data
		
			if(count($errors) == 0) {
				require_once($CONFIG['BASE_PATH'].'/includes/lib/auth/'.$CONFIG['AUTH_TYPE'].'.inc.php');
				$user_data['level_key'] = get_default_user_level();
				
				$user_data = $objUser->addUser($user_data, true, true);

				//if the add was successful return the browser to space home or parent folder
				if (is_array($user_data)) {
					
					$_SESSION['current_user_key']		= $user_data['user_key'];
					$_SESSION['current_user_firstname']	= $user_data['first_name'];
					$_SESSION['current_user_lastname']	= $user_data['last_name'];	
					$_SESSION['current_user_email']		= $user_data['email'];
					$_SESSION['userlevel_key']			= $user_data['level_key'];
					$_SESSION['last_use']				= 0;
					$_SESSION['use_count']				= 0;
					$_SESSION['username']				= $user_data['username'];
					$_SESSION['current_username']		= $user_data['username'];
					$_SESSION['language']				= $user_data['language_key'];
					$_SESSION['auto_editor']			= 1;
					$_SESSION['read_posts_flag']		= 1;	
					$_SESSION['file_path']				= $user_data['file_path'];
					$_SESSION['skin_key']				= 0;
					$_SESSION['online_status']			= 1;	
					$_SESSION['address']                = $user_data['address'];
					$_SESSION['id_no']                  = $user_data['id_no'];
					$_SESSION['attendance']				= $user_data['attendance'];
					$_SESSION['backlogs']				= $user_data['backlogs'];
					
					//$request_uri=urlencode($request_uri);
					$message = urlencode($user_strings['add_success']);
					if (!empty($user_data['auto_login'])) {
						
						$permanent_user=md5($user_data['user_key'].$CONFIG['SECRET_HASH']);
						setcookie('permanent_user_key', $user_data['user_key'],time()+63072000,$CONFIG['PATH']);
						setcookie('permanent_user', $permanent_user,time()+63072000,$CONFIG['PATH']);		
					}
					if (!empty($request_uri)) {
						header("Location: {$CONFIG['SERVER_URL']}$request_uri&first_login=1");	
					} else {
						header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}&first_login=1");	
					}
					
					exit;
				 
				}
			
			} else {
		
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
		
		break;
		
		case modify:
		
			$user_data = $objUser->getUserData($current_user_key);
			$user_data['confirm_email'] = $user_data['email'];
		
		break;
	
		case modify2:
	   
			if (empty($_SESSION['current_user_key']) || $_SESSION['current_user_key']==0) {
				header("Location: {$CONFIG['FULL_URL']}");
				exit;
			}
			switch ($submit) {
			
				case $general_strings['modify']:
				
					$errors = check_form_input($user_data);
					
					if(count($errors) == 0) {
			
						$message=$objUser->modifyUser($user_data, $current_user_key);
				
						//if the modify was successful return the browser to space home or parent folder
			
						if ($message=='true') {
				
							//reset session variables
	   						$_SESSION['current_user_email']	 	= $user_data['email'];
	   						$_SESSION['current_user_firstname'] = $user_data['first_name'];
	   						$_SESSION['current_user_lastname']  = $user_data['last_name'];
	   						$_SESSION['current_user_lastname']  = $user_data['last_name'];
	   						$_SESSION['read_posts_flag']		= $user_data['flag_posts'];
	   						$_SESSION['auto_editor']			= $user_data['auto_editor'];
							$_SESSION['skin_key']				= $user_data['skin_key'];
							$_SESSION['address']                = $user_data['address'];															                            $_SESSION['id_no']                  = $user_data['id_no'];
							$_SESSION['phone_no']				= $user_data['phone_no'];
							$_SESSION['branch']					= $user_data['branch'];
							$_SESSION['attendance']				= $user_data['attendance'];
							$_SESSION['backlogs']				= $user_data['backlogs'];
						
							
							if (!empty($user_data['auto_login'])) {
								$permanent_user=md5($current_user_key.$CONFIG['SECRET_HASH']);
								setcookie ('permanent_user_key', $current_user_key,time()+63072000,$CONFIG['PATH']);
								setcookie ('permanent_user', $permanent_user,time()+63072000,$CONFIG['PATH']);		
							}
							$message = urlencode($user_strings['modify_success']);
							Header ("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=1");
							exit;
					
						} 
		
					} else {
		
						$message = $general_strings['problem_below'];
			
					}
					
				break;
				
				case $general_strings['delete']:
				

					$message = $objUser->deleteUser($_SESSION['current_user_key']);
					
					if ($message===true) {
					
						$message = 'Your+account+has+been+deleted';
						header("Location: {$CONFIG['FULL_URL']}/logout.php?message=$message");
						exit;
						
					}
					
				break;
				
			}
		
		break;

	} //end switch $action

} //end if (isset($action))


if (!isset($action) || $action=='add') {
	
	$action = 'add';
	$title = $user_strings['add_account'];
	$button = $general_strings['add'];
	$user_data['username'] = "<input type=\"text\" name=\"username\" value=\"$username\" size=\"20\">";
	$optional_fields = $CONFIG['USER_INPUT_OPTIONAL_FIELDS'];
	$username_value = '';

}

if ($action=='modify' || $action=='modify2') {

	$action = 'modify2';
	$button = $general_strings['modify'];
	$title = $user_strings['modify_account'];
	$user_data['username'] = $user_data['username'].'<input type="hidden" name="username" value="'.$user_data['username'].'" size="20">';
	$optional_fields = $CONFIG['USER_MODIFY_OPTIONAL_FIELDS'];

	//see if user is allowed to delete their own account. If so provide delete button
	
	if ($CONFIG['SELF_DELETE']==1) {
	
		$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')">';
		
	} else {
	
		$delete_button = '';
		
	}
	
	//if we have a photo display it.
	
	if ($current_photo!='') {
		
		$full_photo_path = $CONFIG['USERS_PATH'].'/'.$file_path.'/'.$current_photo; 
		$photopath = $CONFIG['USERS_VIEW_PATH'].'/'.$file_path.'/'.$current_photo;
		
		if (file_exists($full_photo_path)) {
	   
			$photo_tag="<img src='$photopath'>";

		} 
   
   }
   
}


//get the required template files
require_once($CONFIG['BASE_PATH'].'/includes/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		=> 'header.ihtml',
	'navigation'	=> 'navigation.ihtml',
	'form'			=> 'users/userinput.ihtml',
	'footer'		=> 'footer.ihtml'));

//generate the header,title, 
if (!isset($current_user_key) || $current_user_key=='') {

	$t->set_block('header', 'SearchBoxBlock', 'SBXBlock');
	$t->set_var('SBXBlock','');

} 
$t->set_block('form', 'AUTO_PASSWORD_BLOCK', 'APWBlock');
$t->set_var('APWBlock','');
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('PAGE_TITLE',$title);
$t->set_var('SPACE_TITLE',$title);

//format any errors from form submission
foreach ($errors as $key => $value ) {

	$t->set_var(strtoupper($key).'_ERROR',sprint_error($value));

} 

//take out amy optional fields not required

foreach ($optional_fields as $key => $value) {

	if ($value == 0) {
	
		$t->set_block('form', $key.'_BLOCK', $key.'CUT_BLOCK');
		
	}

}

//see if password box required 
require_once($CONFIG['BASE_PATH'].'/includes/lib/auth/'.$CONFIG['AUTH_TYPE'].'.inc.php');
if (!show_password_change(isset($_SESSION['userlevel_key'])? $_SESSION['userlevel_key'] : get_default_user_level())) {
		$t->set_block('form', 'PASSWORD_BLOCK', 'PWORD_BLCK');
		$t->set_var('PWORD_BLCK','');
}
//take out usergroup select if user is not allowed to select own group

if ($CONFIG['USERGROUP_SELF_SELECT']==0) {

	$t->set_block('form', 'USERGROUPS_BLOCK', 'UGBlock');
	$t->set_var('UGBlock', '');
			
} else {

	if (is_array($CONFIG['RESTRICT_USERGROUPS']) && count($CONFIG['RESTRICT_USERGROUPS'])>0) {
	
		$limit = 'WHERE user_group_key NOT IN ('.implode($CONFIG['RESTRICT_USERGROUPS'],',').')';
	
	} else {
	
		$limit = '';
	
	}
	$group_sql = "SELECT group_name, user_group_key FROM {$CONFIG['DB_PREFIX']}user_groups $limit ORDER BY group_name";
	$group_menu = make_menu($group_sql,'user_group_keys',$user_data['user_group_keys'],'4',true);

}
	
if ($CONFIG['USER_SET_SKIN']==1) {
	if (!isset($objSkins)) {
		if (!class_exists('InteractSkins')) {
			require_once('../skins/lib.inc.php');
		}
		$objSkins = new InteractSkins();
	}
	//create list of alt stylesheets
	$skins_array = $objSkins->getSkinArray();
	$skins_array[0] = $general_strings['use_default'];
// 	$alt_style_sheets='';
// 	foreach($skins_array as $key => $value) {
// 		$skin_data = $objSkins->getSkinData($key);
// 		$alt_style_sheets.='<link rel="alternate stylesheet" type="text/css" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key='.$key.'" title="'.$key.'" />';
// 	}
// 	$t->set_var('META_TAGS',$alt_style_sheets);
	$t->set_var('ADD_LINK','<a href="../../skins/skin_select.php?space_key='.$space_key.'&referer='.$CONFIG['PATH'].'/users/userinput.php?action=modify&">'.$general_strings['add'].'/'.$general_strings['modify'].'</a>');
} else {
	$t->set_block('form', 'SKINS_BLOCK', 'SKBlock');
	$t->set_var('SKBlock','');
}
$t->set_var('USER_GROUP_KEYS', '<input type="hidden" name="user_group_key" value="'.$user_group_key.'" />');
$t->set_var('USERNAME', $user_data['username']);
$t->set_var('USERNAME_VALUE', $username_value);
$t->set_var('PASSWORD',isset($user_data['password'])? $user_data['password']: '');
$t->set_var('CONFIRM_PASSWORD',isset($user_data['confirm_password'])? $user_data['confirm_password']: $user_data['password']);
$t->set_var('ADDRESS',$user_data['address']);
$t->set_var('ID_NO',$user_data['id_no']);
if (($action=='modify' || $action=='modify2') && $CONFIG['OPTIONS']&2 && $_SESSION['userlevel_key']!=1) {
	$t->set_var('NAME_INPUT_TYPE','hidden');
	$t->set_var('FIRST_NAME_TEXT',isset($user_data['first_name'])? $user_data['first_name'] : '');
	$t->set_var('LAST_NAME_TEXT',isset($user_data['last_name'])? $user_data['last_name'] : '');
	$t->set_var('ID_NO_TEXT',isset($user_data['id_no'])? $user_data['id_no'] : '');
	$t->set_var('ADDRESS_TEXT',isset($user_data['address'])? $user_data['address'] : '');
	$t->set_var('PREFERED_NAME_TEXT',isset($user_data['prefered_name'])? $user_data['prefered_name'] : '');		



} else {
	$t->set_var('NAME_INPUT_TYPE','text');
}
$t->set_var('FIRST_NAME',isset($user_data['first_name'])? $user_data['first_name'] : '');
$t->set_var('LAST_NAME',isset($user_data['last_name'])? $user_data['last_name'] : '');
$t->set_var('PREFERED_NAME',isset($user_data['prefered_name'])? $user_data['prefered_name'] : '');
$t->set_var('EMAIL',isset($user_data['email'])? $user_data['email'] : '');
$t->set_var('CONFIRM_EMAIL',isset($user_data['confirm_email'])? $user_data['confirm_email'] : '');
$t->set_var('GROUP_MENU',$group_menu);

require_once($CONFIG['INCLUDES_PATH']."/lib/languages.inc.php");
$t->set_var("LANGUAGE_MENU",lang_menu('language_key',(isset($user_data['language_key'])? $user_data['language_key'] : '')));

$t->set_var('DETAILS',isset($user_data['details'])? $user_data['details'] : '');
$t->set_var('PHOTO',isset($user_data['photo_tag'])? $user_data['photo_tag'] : '');
$t->set_var('USER_ID_NUMBER',isset($user_data['user_id_number'])? $user_data['user_id_number'] : '');
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('DELETE_BUTTON',isset($delete_button)? $delete_button : '');
$t->set_var('USER_KEY',isset($user_data['user_key'])? $user_data['user_key'] : '');
$t->set_var('REQUEST_URI',isset($request_uri)? $request_uri : '');
$t->set_var('USER_INPUT_HEADING',$user_strings['input_heading']);
$t->set_var('COMPULSORY_FIELD_STRING',$user_strings['compulsory_field']);
$t->set_var('USERNAME_STRING',$user_strings['username']);
$t->set_var('PASSWORD_STRING',$user_strings['password']);
$t->set_var('CONFIRM_PASSWORD_STRING',$user_strings['confirm_password']);
$t->set_var('EMAIL_STRING',$user_strings['email']);
$t->set_var('FIRST_NAME_STRING',$user_strings['first_name']);
$t->set_var('LAST_NAME_STRING',$user_strings['last_name']);
$t->set_var('GROUP_STRING',$user_strings['group']);
$t->set_var('PREFERRED_LANGUAGE_STRING',$user_strings['preferred_language']);
$t->set_var('SELECT_MULTIPLE_STRING',$general_strings['select_multiple']);
$t->set_var('DETAILS_STRING',$user_strings['details']);
$t->set_var('DETAILS_STRING_2',$user_strings['details_2']);
$t->set_var('PHOTO_STRING',$user_strings['photo']);
$t->set_var('OPTIONAL_STRING',$general_strings['optional']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('ID_NUMBER_STRING',$user_strings['id_number']);
$t->set_var('PREFERED_NAME_STRING',$user_strings['prefered_name']);
$t->set_var('AUTO_PASSWORD_STRING',$user_strings['auto_password']);
$t->set_var('AUTO_LOGIN_STRING',sprintf($user_strings['auto_login'],$CONFIG['SERVER_NAME']));
$t->set_var('USERNAME_HELP',$user_strings['username_help']);
$t->set_var('PASSWORD_HELP',$user_strings['password_help']);
$t->set_var('CONFIRM_EMAIL_STRING',$user_strings['confirm_email']);
$t->set_var('SKIN_STRING',$general_strings['skin']);
$t->set_var('SKIN_MENU',$objHtml->arrayToMenu($skins_array,'skin_key',$user_data['skin_key'],false,'',false,'onChange="changeStyleSheet(this.value)"'));
$t->set_var('ADDRESS',$user_data['address']);
$t->set_var('ID_NO',$user_data['id_no']);
$t->set_var('BRANCH',$user_data['branch']);
$t->set_var('ATTENDANCE',$user_data['attendance']);
$t->set_var('BACKLOGS',$user_data['backlogs']);
$t->set_var('PHONE_NO',$user_data['phone_no']);

//remove any fields not needed at account creation

//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
if (!$action || $action=='add') {


} else {

	$auto_editor_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'auto_editor',$user_data['auto_editor']);
	$displayed_posts_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'flag_posts',$user_data['flag_posts']);
	$t->set_var('AUTO_EDITOR_MENU',$auto_editor_menu); 
	$t->set_var('DISPLAYED_POSTS_MENU',$displayed_posts_menu);
	$t->set_var('AUTO_EDITOR_STRING',$user_strings['auto_editor']);
	$t->set_var('DISPLAYED_POSTS_STRING',$user_strings['flag_posts']);
	
}


$html->setTextEditor($t, $auto_editor, 'details');


$t->parse('CONTENTS','header', true); 
$t->parse('CONTENTS','form', true);

$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
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

	
	if($user_data['email']!=$user_data['confirm_email']) {

		$errors['confirm_email'] = $user_strings['confirm_email_error'];

	}	
	
	if(!$user_data['email']) {

		$errors['email'] = $user_strings['no_email'];

	} else {
	
		if(is_email($user_data['email'])!=true) {
		
			$errors['email'] = $user_strings['no_email'];
			
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

	 }}
	
	return $errors;

} //end check_form_input


?>

<?php
/**
* Login 
*
* Displays login screen 
*
*/

/**
* Include main config file
*/
require_once('local/config.inc.php');
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
require_once($CONFIG['BASE_PATH'].'/includes/lib/auth/'.$CONFIG['AUTH_TYPE'].'.inc.php');
$auth_function = 'auth_'.$CONFIG['AUTH_TYPE'];

$hidden_hash_var=$CONFIG['SECRET_HASH'];   

//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');

if (isset($_COOKIE['permanent_user'])) {
	
	$permanent_user = $_COOKIE['permanent_user'];
}

if (isset($_COOKIE['permanent_user_key'])) {
	$permanent_user_key = $_COOKIE['permanent_user_key'];
}

if (isset($_SESSION['current_user_key'])) {
	$current_user_key = $_SESSION['current_user_key'];
}

if ($_SERVER['REQUEST_METHOD']=='POST') {
	$request_uri = urldecode($_POST['request_uri']);
	$username  = isset($_POST['username'])?$_POST['username']:'';
	$password	= isset($_POST['password'])?$_POST['password']:'';
} else {
	$request_uri = isset($_GET['request_uri']) ? urldecode($_GET['request_uri']) : '';
}

// Has the form been posted?
if ($_SERVER['REQUEST_METHOD']=='POST' || isset($permanent_user) || isset($current_user_key) ) {
	// Initialize the errors array
	$errors = array();
	// Check submitted userid
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql="SELECT user_key,password, first_name,last_name, email, level_key, use_count,last_use, language_key, auto_load_editor, read_posts_flag, account_status, username, file_path, skin_key FROM {$CONFIG['DB_PREFIX']}users WHERE ";
	if (isset($permanent_user_key)) {
		$user_key_hash = md5($permanent_user_key.$hidden_hash_var);
		if ($user_key_hash==$permanent_user) {
			$sql.="user_key='$permanent_user_key'";
		} else {
			$sql.="username='$username'";
			$bad_cookie = true;
		}
	} else if (isset($current_user_key)) {
		$sql.="user_key='$current_user_key'";
	} else {
		$sql.="username='$username'";
	}

	$rs = $CONN->Execute($sql);
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	
	if($rs->EOF) {
		$fields=null;
		if ($_SERVER['REQUEST_METHOD']!='POST' || ($fields=$auth_function($username, $password, '', '', false))===false) {
			$errors['username'] = $user_strings['username_not_exist'];
		}
	} else {
		$fields=$rs->fields;
		if ($_SERVER['REQUEST_METHOD']=='POST' && $auth_function($username, $password, $fields['password'], $fields['level_key'])!=true) {
			$errors['password'] = $user_strings['bad_password'];
		}
	}
	
	if ($fields) {
		$current_user_key = $fields['user_key'];
		$current_user_firstname = $fields['first_name'];
		$current_user_lastname = $fields['last_name'];
		$current_user_email = $fields['email'];
		$userlevel_key = $fields['level_key'];
		$use_count = $fields['use_count'];
		$last_use = $fields['last_use'];
		$language = $fields['language_key'];
		$auto_editor = $fields['auto_load_editor'];
		$read_posts_flag = $fields['read_posts_flag'];
		$account_status = $fields['account_status'];
		$username = $fields['username'];
		$file_path = $fields['file_path'];
		$skin_key = $fields['skin_key'];

		if ($account_status==2 || $account_status==3) {   
			$errors['username'] = $user_strings['account_disabled'];
		}
	}


// Can the form be processed or are there any errors?
if(count($errors) == 0) {
	if (isset($_POST['permanent_cookie']) && $_POST['permanent_cookie']=='yes') {
		$permanent_user=md5($current_user_key.$hidden_hash_var);
		setcookie('permanent_user_key', $current_user_key,time()+63072000,$CONFIG['PATH']);
		setcookie('permanent_user', $permanent_user,time()+63072000,$CONFIG['PATH']);		
	}
	// initialize a session
	// update last login date  and use count
	$use_count++;
	$date = $CONN->DBDate(date('Y-m-d H:i:s'));
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET use_count='$use_count',last_use=$date WHERE user_key='$current_user_key'";
	$CONN->Execute($sql);
	$cookie_site_name=$CONFIG['SERVER_NAME'];
	//session_start();
	// register a session variable
	$_SESSION['current_user_key']	   = $current_user_key;
	$_SESSION['current_user_firstname'] = $current_user_firstname;
	$_SESSION['current_user_lastname']  = $current_user_lastname;	
	$_SESSION['current_user_email']	 = $current_user_email;
	$_SESSION['userlevel_key']		  = $userlevel_key;
	$_SESSION['last_use']			   = $last_use;
	$_SESSION['use_count']			  = $use_count;
	$_SESSION['cookie_site_name']	   = $cookie_site_name;
	$_SESSION['username']			  = $username;
	$_SESSION['current_username']	  = $username;
	$_SESSION['language'] = (is_dir($CONFIG['BASE_PATH'].'/language/'.$language.'/strings/compiled')?$language:$CONFIG['DEFAULT_LANGUAGE']);
	$_SESSION['auto_editor']			= $auto_editor;
	$_SESSION['read_posts_flag']		= $read_posts_flag;	
	$_SESSION['file_path']		= $file_path;
	$_SESSION['skin_key']		= $skin_key;
	$_SESSION['online_status']	= 1;
	//remove one guest entry from the online users table
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}online_users WHERE user_key='0' LIMIT 1");	 	



	if ($request_uri!='' && $request_uri!=$CONFIG['PATH'].'/nologin.php' && $request_uri!='/') {
		header("Location: {$CONFIG['SERVER_URL']}$request_uri");
		exit;
	}  else {
		if ($CONFIG['DEFAULT_SPACE_KEY']==0) {
			header("Location: {$CONFIG['FULL_URL']}/index.php");
			exit;
		} else {
			header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}");
			exit;
		}
	}
} else {
	if (isset($bad_cookie) && $bad_cookie == true) {
		$message = $user_strings['cookie_error'];
	   	$errors['username'] = '';
	} else {		
		$message = $general_strings['problem_below'];
	}
}
}
$username_error = isset($errors['username']) ? sprint_error($errors['username']) : '';
$password_error  = isset($errors['password']) ? sprint_error($errors['password']) : '';
$title = 'Login';
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header' => 'header.ihtml',
	'form'   => 'login.ihtml',
	'footer' => 'footer2.ihtml'
));
$t->set_block('header', 'SearchBoxBlock', 'SBXBlock');
$t->set_var('SBXBlock','');
$space_key='';
$module_key='';
$message = isset($_GET['message'])? $_GET['message'] : '';
set_common_template_vars($space_key,$module_key,'', $message, '', '');
$t->set_var('PAGE_TITLE',$title); 
$t->set_var('USER_NAME_ERROR',$username_error);
$t->set_var('PASSWORD_ERROR',$password_error);
$t->set_var('USER_NAME',isset($username)?$username:'');
$t->set_var('LOGIN_USER_TEXT',$general_strings['login_user_text']);
//$t->set_var('PASSWORD',$password_email);
$t->set_var('REQUEST_URI',$request_uri);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('SCRIPT_BODY','onLoad="document.login_form.username.focus()"');
$t->set_var('USER_NAME_STRING',$user_strings['username']);
$t->set_var('PASSWORD_STRING',$user_strings['password']);
$t->set_var('FORGOT_PASSWORD_STRING',$user_strings['forgot_password']);
$t->set_var('ADD_ACCOUNT_STRING',$user_strings['add_account']);
$t->set_var('KEEP_LOGIN_STRING',$user_strings['keep_logged_in']);
$t->set_var('NEW_USER_STRING',$user_strings['new_user']);
$t->set_var('SUBMIT_STRING',$general_strings['login']);
$t->set_var('PASSWORD_NOTE',$user_strings['password_note']);
$t->parse('CONTENTS', 'header', true); 
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
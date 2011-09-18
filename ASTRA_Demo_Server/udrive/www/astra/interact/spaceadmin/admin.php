<?php
/**
* Space admin homepage
*
* Displays the space admin homepage with links to space administration 
* functions
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$space_key = $_GET['space_key'];
	
} else {

	$space_key   = $_POST['space_key'];
	$email_who   = $_POST['email_who'];
	$subject	 = $_POST['subject'];
	$body		= $_POST['body'];
	$copyself	= $_POST['copyself'];
	$member_keys = $_POST['member_keys'];	
	$carbon_copy = $_POST['carbon_copy'];
	$file		= $_FILES['file'];					
	$action	  = $_POST['action'];	
	
}
$current_user_key = $_SESSION['current_user_key'];
//check we have the required variables
check_variables(true,false);




//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins('space_only');
$group_access = $access_levels['groups'];	 
$page_details=get_page_details($space_key);

if ($action=='email') {

	$errors = check_form_input();

	if(count($errors) == 0) {

		require_once('../includes/email.inc.php');

		 if (!$member_keys && $email_who=='all' ) {

			$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND (space_key='$space_key' AND {$CONFIG['DB_PREFIX']}users.user_key!='$current_user_key' AND {$CONFIG['DB_PREFIX']}users.account_status='1')";
			$member_keys = get_userkey_array($sql);
			
		} else if ($member_keys && $email_who=='selected' ) {
		
			$member_keys = $_POST['member_keys'];
			
		}
		
		if (isset($page_details['space_short_name']) && $page_details['space_short_name']!='') {
		
			$subject = '['.$page_details['space_short_name'].'] - '.$subject;
			
		} else {
		
			$subject = '['.$page_details['space_name'].'] - '.$subject;
		
		}
		
		email_users($subject, $body, $member_keys, $copyself, $file, $carbon_copy);
		
		if ($_POST['save_as_news']==1) {
		
			$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));
			$heading = $_POST['subject']; 
			$body = nl2br($_POST['body']); 
			$CONN->Execute("INSERT into {$CONFIG['DB_PREFIX']}news VALUES ('','$space_key','$heading','$body',$date_added,'','$current_user_key','1')");
			echo $CONN->ErrorMsg();
		
		
		}
		
		$message = urlencode($general_strings['email_sent']);
		header ("Location: {$CONFIG['FULL_URL']}/spaceadmin/admin.php?space_key=$space_key&message=$message");
		exit;
	
	}

} else if ($action=='select_skin') {
	
	$skin_key = $_POST['skin_key'];
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}spaces SET skin_key='$skin_key' WHERE space_key='$space_key'");
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key");
	exit;
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		   => 'header.ihtml',
	'navigation'	   => 'navigation.ihtml',
	'body'			 => 'spaceadmin/admin.ihtml',
	'footer'		   => 'footer.ihtml'
));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//get spaces module_key and link key

$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.module_key, link_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}spaces LEFT JOIN {$CONFIG['DB_PREFIX']}module_space_links ON {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key WHERE {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key'");
$t->set_var('MODULE_KEY',$rs->fields[0]);
$t->set_var('LINK_KEY',$rs->fields[1]);
$parent_space_key=$rs->fields[2];


$t->set_var('PARENT_SPACE_KEY',(!empty($parent_space_key))?$rs->fields[2]:$space_key);

$t->set_var('MEMBERS_STRING',$space_strings['members']);
$t->set_var('PREFERENCES_STRING',$space_strings['preferences']);
if ($page_details['type_key']==1) {

	$t->set_block('body', 'FullOptionsBlock', 'FullOptsBlock');
	$t->set_var('FullOptsBlock','');
	$t->set_var('ADMIN_HEADER',$space_strings['portfolio_admin']);
	if (!isset($objSkins)) {
		if (!class_exists('InteractSkins')) {
			require_once('lib.inc.php');
		}
		$objSkins = new InteractSkins();
	}
	//create list of alt stylesheets
	$skins_array = $objSkins->getSkinArray('view');
	if ($skins_array==false) {
		$t->set_block('form', 'ModifyFormBlock', 'MFBlock');
		$t->set_var('MFBlock','');
	} else {
// 		$alt_style_sheets='';
// 		foreach($skins_array as $key => $value) {
// 			$skin_data = $objSkins->getSkinData($key);
// 			$alt_style_sheets.='<link rel="alternate stylesheet" type="text/css" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key='.$key.'" title="'.$key.'" />';
// 		}
// 		$t->set_var('META_TAGS',$alt_style_sheets);
		$t->set_var('SKINS_MENU',$objHtml->arrayToMenu($skins_array,'skin_key',$page_details['skin_key'],false,'',false,'onChange="changeStyleSheet(this.value)"'));
	}	
	$t->set_var('SKIN_STRING',$general_strings['skin']);
	$t->set_var('SELECT_STRING',$general_strings['select']);
	$t->set_var('MODIFY_SKINS_STRING',$space_strings['modify_skins']);
} else {
	$t->set_block('body', 'LimitedOptionsBlock', 'LimitedOptsBlock');
	$t->set_var('LimitedOptsBlock','');
	$concat = $CONN->Concat('last_name', '\', \'','first_name');
	$members_sql="SELECT $concat,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key and {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}users.account_status='1' ORDER BY last_name";

	$members_menu = make_menu($members_sql,'member_keys[]','','8','true');
	$subject_error = sprint_error($errors['subject']);
	$body_error = sprint_error($errors['body']);
	$file_error = sprint_error($errors['file']);
	$email_who_error = sprint_error($errors['email_who']);
	$t->set_var('SPACE_KEY',$space_key);
	$t->set_var('SUBJECT_ERROR',$subject_error);
	$t->set_var('BODY_ERROR',$body_error);
	$t->set_var('FILE_ERROR',$file_error);
	$t->set_var('EMAIL_WHO_ERROR',$email_who_error);
	$t->set_var('SUBJECT',$subject);
	$t->set_var('BODY',$body);
	$t->set_var('MEMBERS_MENU',$members_menu);

	//if site admins allowed to create accounts show account 
	//creation link

	if ($CONFIG['DEVOLVE_ACCOUNT_CREATION']==1) {

		$t->set_var('ACCOUNT_CREATION',"<a href=\"userinput.php?space_key=$space_key\">".$space_strings['account_creation'].'</a>');

	}
	$t->set_var('ADMIN_HEADER',$space_strings['admin_header']);
	$t->set_var('SHOW_ALL_URLS_STRING',$space_strings['show_all_urls']);

	$t->set_var('CHECK_STRING',$general_strings['check']);
	$t->set_var('ACCESS_CODE_STRING',$space_strings['access_code']);
	$t->set_var('STATISTICS_STRING',$space_strings['statistics']);
	$t->set_var('REMOVE_MEMBERS_STRING',$space_strings['remove_members']);
	$t->set_var('TRASH_STRING',$space_strings['trash']);
	$t->set_var('EDIT_HEADER_STRING',$space_strings['edit_header']);
	$t->set_var('EMAIL_MEMBERS_STRING',$general_strings['email_members']);
	$t->set_var('SUBJECT_STRING',$general_strings['subject']);
	$t->set_var('MESSAGE_STRING',$general_strings['message']);
	$t->set_var('CC_STRING',$general_strings['carboncopy']);
	$t->set_var('CC_INSTRUCTIONS',$general_strings['cc_instructions']);
	$t->set_var('ATTACHMENT_STRING',$general_strings['attachment']);
	$t->set_var('COPY_SELF_STRING',$general_strings['copy_self']);
	$t->set_var('INSTRUCTIONS_STRING',$general_strings['email_instructions']);
	$t->set_var('GROUP_MEMBERS_STRING',$group_strings['group_members']);
	$t->set_var('ALL_MEMBERS_STRING',$general_strings['email_all']);
	$t->set_var('SELECTED_MEMBERS_STRING',$general_strings['email_selected']);
	$t->set_var('SEND_STRING',$general_strings['send']);
	$t->set_var('REMOVE_NEWS_STRING',$space_strings['remove_news']);


	if ($_SESSION['userlevel_key']==1) {

		$t->set_var('ADD_SUBSPACE_STRING',sprintf($space_strings['add_subspace'], $general_strings['space_text']));
	
	}
}
$t->parse('CONTENTS', 'header', true);
get_navigation();
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;



function check_form_input() 
{

	global $general_strings ; 


	// Initialize the errors array

	$errors = array();

	//check to see if we have all the information we need
	
	if(!$_POST['email_who']) {

		$errors['email_who'] = $general_strings['email_who_error'];

	}
	
	if($_POST['email_who']=='selected' && !$_POST['member_keys']) {

		$errors['email_who'] = $general_strings['email_who_error2'];

	}
	
	if(!$_POST['subject']) {

		$errors['subject'] = $general_strings['no_subject'];
	

	}


	if(!$_POST['body']) {

		$errors['body'] = $general_strings['no_message'];

	}
	
	if( $_FILES['file']['size'] > 200000) {

		$errors['file'] = $general_strings['attachment_to_big'];

	}

	return $errors;

} //end check_form_input
?>
<?php
/**
* Space Welcome Page
*
* Welcome message shown to new members
*
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key  = $_GET['space_key'];
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables

check_variables(true,false);
//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = isset($access_levels['accesslevel_key'])?$access_levels['accesslevel_key']:'';
$group_access = $access_levels['groups']; 
$welcome_message = $CONN->GetOne("SELECT welcome_message FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'");
if (empty($welcome_message)) {
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key");
	exit;
}
	
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'welcome' => 'spaces/welcome.ihtml',
	'footer'	 => 'footer.ihtml'
	
));
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,'',$page_details, '', $accesslevel_key, $group_accesslevel);
if (!isset($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once('../../includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}
$t->set_var('WELCOME_MESSAGE',$objHtml->parseText($welcome_message));

$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->parse('CONTENTS', 'welcome', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
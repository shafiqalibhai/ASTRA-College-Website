<?php
/**
* Skin input page
*
* Display the page for adding and modifying new skins 
*
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/skin_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$current_user_key	= $_SESSION['current_user_key'];
$message = isset($_GET['message'])?$_GET['message']:'';
$referer = isset($_GET['referer'])?urldecode($_GET['referer']):'';
//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

if (!isset($objSkins)) {
	if (!class_exists('InteractSkins')) {
		require_once('lib.inc.php');
	}
	$objSkins = new InteractSkins();
}

if ($_SERVER['REQUEST_METHOD']='POST') {
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		=> 'header.ihtml',
	'navigation'	=> 'navigation.ihtml',
	'form'			=> 'skins/skin_select.ihtml',
	'footer'		=> 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
$page_details['breadcrumbs'] = $page_details['breadcrumbs'].' <strong>Skins</strong>';
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//create list of alt stylesheets
$skins_array = $objSkins->getSkinArray('edit');
if ($skins_array==false) {
	$t->set_block('form', 'ModifyFormBlock', 'MFBlock');
	$t->set_var('MFBlock','');
} else {
// 	$alt_style_sheets='';
// 	foreach($skins_array as $key => $value) {
// 		$skin_data = $objSkins->getSkinData($key);
// 		$alt_style_sheets.='<link rel="alternate stylesheet" type="text/css" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key='.$key.'" title="'.$key.'" />';
// 	}
// 	$t->set_var('META_TAGS',$alt_style_sheets);
	$t->set_var('SKINS_MENU',$objHtml->arrayToMenu($skins_array,'skin_key',$space_data['skin_key'],false,'',false,'onChange="changeStyleSheet(this.value)"'));
}
$t->parse('CONTENTS', 'header', true);

get_navigation();
$t->set_var('REFERER',urlencode($referer));
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODIFY_STRING',$general_strings['modify']);
$t->set_var('ADD_NEW_STRING',$skin_strings['add_new']);
$t->set_var('SELECT_MODIFY_STRING',$skin_strings['select_modify']);
$t->set_var('OR_STRING',$general_strings['or']);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

?>
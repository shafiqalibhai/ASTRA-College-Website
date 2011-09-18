<?php
/**
* Modify space header
*
* Allows an space admin to display and modify a space's header text
*
* @package SpaceAdmin
* 
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

	$space_key = $_POST['space_key'];
	$body	  = $_POST['body'];
	//strip any double quoting added by easyedit if html attributes use ' instead of "
	$body = ereg_replace("\"’", "\"", $body);
	$body = ereg_replace("’\"", "\"", $body);						
	$submit	= $_POST['submit'];	
	
}
//check we have the required variables
check_variables(true,false,false);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 

if (!$submit) {

	$sql = "SELECT Header FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$header = $rs->fields[0];
		$rs->MoveNext();

	}

	$rs->Close();

} else {

		$header = $body;
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}spaces SET Header='$header' WHERE space_key='$space_key'";

		$CONN->Execute($sql);
		$message = urlencode($space_strings['header_updated']);
		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'spaceadmin/spaceheader.ihtml',
	'footer'		  => 'footer.ihtml'
));
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'body', false);

$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var('HEADER_INPUT_STRING',sprintf($space_strings['input_header'], $general_strings['space_text']));
$t->set_var('HEADER_STRING',$space_strings['header']);
$t->parse('CONTENTS', 'header', true);
get_navigation();
$t->set_var('HEADER',$header);
$t->set_var('SPACE_KEY',$space_key);

$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
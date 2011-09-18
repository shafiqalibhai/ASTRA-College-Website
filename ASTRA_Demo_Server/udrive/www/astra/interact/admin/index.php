<?php
// |																	  |
// | Home page for Admin functions										|


require_once('../local/config.inc.php');


//check to see if user is logged in. If not refer to Login page.
authenticate_admins();
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');


$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header" => "header.ihtml",
	  "navigation" => "admin/adminnavigation.ihtml",
	"home" => "admin/index.ihtml",
	"footer" => "footer.ihtml"));

set_common_admin_vars($CONFIG['SERVER_NAME'].' - Admin Home');

//get version info


$rs = $CONN->GetRow("SELECT version, release_name FROM {$CONFIG['DB_PREFIX']}version");
$version = $rs[0];
$release = $rs[1];


if(!($CONFIG['OPTIONS']&1)) {
	$t->set_block('home', 'VersionCheckBlock', 'VCBlock');
	$t->set_var('VCBlock','<br />Version Check Disabled.');	
}



$t->set_var("PAGE_TITLE","Admin Home"); 
$t->set_var("PATH",$CONFIG['PATH']);
$t->set_var("SPACE_TITLE","Admin Home");
$t->set_var("RELEASE",$release);
$t->set_var("VERSION",$version);
$t->parse("CONTENTS", "header", true); 
admin_navigation();
$t->parse("CONTENTS", "home", true);
$t->parse("CONTENTS", "footer", true);
$t->p("CONTENTS");

exit;

?>
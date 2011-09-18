<?php
// +----------------------------------------------------------------------+
// | secure accounts  1.0												 |
// +----------------------------------------------------------------------+

require_once('../local/config.inc.php');



//find out what action we need to take
if ($_POST['action']=='check_password') {

	if (isset($_POST['user_group_key']) && $_POST['user_group_key']!='') {
	
		$rs = $CONN->Execute("SELECT account_creation_password FROM {$CONFIG['DB_PREFIX']}user_groups WHERE user_group_key='{$_POST['user_group_key']}'");
				
		if ($rs->EOF) {
		
			$account_creation_password = $CONFIG['ACCOUNT_CREATION_PASSWORD'];
		
		} else {
		
			while (!$rs->EOF) {
		
				$account_creation_password = $rs->fields[0];
				$rs->MoveNext();
		
			}
						
		}
	
		$rs->Close();
	
	} else {
	
		$account_creation_password = $CONFIG['ACCOUNT_CREATION_PASSWORD'];
	
	}
	
	if ($_POST['account_creation_password']==$account_creation_password) {
	
		$user_group_key = isset($_POST['user_group_key'])? $_POST['user_group_key']: '';
		header("Location: {$CONFIG['FULL_URL']}/users/userinput.php?user_group_key=$user_group_key");
	
	} else {
	
		$user_group_key = isset($_POST['user_group_key'])? $_POST['user_group_key']: '';
		$message = "Sorry that password is not correct";

	}

} else {

	$user_group_key = isset($_GET['user_group_key'])? $_GET['user_group_key']: '';

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	"header" => "header.ihtml",
	"body"   => "users/secureaccounts.ihtml",
	"footer" => "footer.ihtml"
));
$t->set_block('header', 'SearchBoxBlock', 'SBXBlock');
$t->set_var('SBXBlock','');
set_common_template_vars('','','', '', '', '');
$t->set_var("PAGE_TITLE","Create an Account"); 
$t->set_var("SPACE_TITLE","Create an Account");

$t->set_var("USER_GROUP_KEY",$user_group_key);
$t->parse("CONTENTS", "header", true); 
$t->parse("CONTENTS", "body", true);
$t->parse("CONTENTS", "footer", true);
$t->p("CONTENTS");
exit;

?>
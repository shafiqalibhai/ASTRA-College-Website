<?php
/**
* Look up a users details
*
* Retrieve a users details by username, firstname, lastname
*
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'form'	   => 'admin/userlookup.ihtml',
	'footer'	 => 'footer.ihtml'));

$t->set_block('form', 'ResultBlock', 'RBlock');
$t->set_block('form', 'SpreadSheetRowBlock', 'SpreadSheetRow');

$user_level_array = $CONN->GetAssoc("SELECT access_level_key, name FROM {$CONFIG['DB_PREFIX']}access_levels");
$status_array = $CONN->GetAssoc("SELECT account_status_key,name FROM {$CONFIG['DB_PREFIX']}user_statuses");

if ($_SERVER['REQUEST_METHOD']=='POST') {
 
	if ($_POST['spreadsheet_view']!=1) {
		$t->set_block('form', 'SpreadSheetBlock', 'SpreadSheet');
		$t->set_var('SpreadSheet','');
	}
	$search_data = $_POST['search_data'];
	
	$rs = $CONN->Execute("select first_name,last_name,email,username,password,level_key,user_key, account_status, user_id_number, address, phone_no, branch, attendance, backlogs from {$CONFIG['DB_PREFIX']}users where (first_name like '$search_data%' or last_name like '$search_data%' or username like '$search_data%') AND (username NOT LIKE '_interact_%') ORDER BY last_name, first_name");
 
	if ($rs->EOF) {
	
		$t->set_var('RBlock', '');
		$t->set_var('SpreadSheet', '');
		$message = "There are no users matching your search criteria of $search_data";	
		
	} else { 
	
		while (!$rs->EOF) {
		
			$t->set_var('FIRST_NAME',$rs->fields[0]);
			$t->set_var('LAST_NAME',$rs->fields[1]);
			$t->set_var('EMAIL',$rs->fields[2]);
			$t->set_var('USER_NAME',$rs->fields[3]);
			$t->set_var('ADDRESS',$rs->fields[9]);
			$t->set_var('PHONE_NO',$rs->fields[10]);
			$t->set_var('BRANCH',$rs->fields[11]);
			$t->set_var('BACKLOGS',$rs->fields[13]);
			$t->set_var('ATTENDANCE',$rs->fields[12]);

			
			
			
			
			if ($CONFIG['AUTH_TYPE']=='dbplain') {
			
				$t->set_var('PASSWORD',"<input name=\"password\" type=\"text\" id=\"password\" value=\"{$rs->fields[4]}\" size=\"30\">"); 
				
			} else {

				$t->set_var('PASSWORD',"<input name=\"password\" type=\"text\" id=\"password\" value=\"\" size=\"30\">"); 
				
			}			

			$level_menu = make_menu("SELECT name,access_level_key FROM {$CONFIG['DB_PREFIX']}access_levels",'level_key',$rs->fields[5],'1');
			$status_menu = make_menu("SELECT name,account_status_key FROM {$CONFIG['DB_PREFIX']}user_statuses",'status_key',$rs->fields[7],'1');
			//now get array of user_groups that user is a member of
			$n=1;
			$rs_user_groups = $CONN->Execute("SELECT user_group_key FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_key='{$rs->fields[6]}'");
			$user_group_keys = array();
			while (!$rs_user_groups->EOF) {
				$user_group_keys[$n]=$rs_user_groups->fields[0];
				$n++;
				$rs_user_groups->MoveNext();
			}
			$rs_user_groups->Close();
			$usergroup_menu = make_menu("SELECT group_name, user_group_key FROM {$CONFIG['DB_PREFIX']}user_groups ORDER BY group_name",'user_group_keys[]',$user_group_keys,'4',true);
			$t->set_var('LEVEL_MENU',(empty($_POST['spreadsheet_view']))?$level_menu:$user_level_array[$rs->fields[5]]);
			$t->set_var('STATUS_MENU',(empty($_POST['spreadsheet_view']))?$status_menu:$status_array[$rs->fields[7]]); 
			
			$t->set_var('USERGROUP_MENU',$usergroup_menu);
			$t->set_var('USER_KEY',$rs->fields[6]);
			$t->set_var('USER_ID_NUMBER',$rs->fields[8]);			
			if ($_POST['spreadsheet_view']==1) {
				$t->Parse('SpreadSheetRow', 'SpreadSheetRowBlock', true);	
			} else {
				$t->Parse('RBlock', 'ResultBlock', true);
			}
			
			$rs->MoveNext();
  
		}
		
	}  

} else {

	$t->set_var('RBlock', '');
	$t->set_var('SpreadSheet', '');
	$t->set_block('form', 'SpreadSheetBlock', 'SpreadSheet');
}

set_common_admin_vars('User Lookup', $message);
$t->set_var('SPREADSHEET_VIEW_CHECKED',(!empty($_POST['spreadsheet_view']))?'checked':'');
$t->set_var('RESULTS',$results);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);

$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

exit;


?>
<?php
// +------------------------------------------------------------------------+
// | This file is part of Interact.											|
// |																	  	| 
// | This program is free software; you can redistribute it and/or modify 	|
// | it under the terms of the GNU General Public License as published by 	|
// | the Free Software Foundation (version 2)							 	|
// |																	  	|	 
// | This program is distributed in the hope that it will be useful, but  	|
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   		|
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 	|
// | General Public License for more details.							 	|
// |																	  	|	 
// | You should have received a copy of the GNU General Public License		|
// | along with this program; if not, you can view it at				  	|
// | http://www.opensource.org/licenses/gpl-license.php				   		|
// +------------------------------------------------------------------------+

/**
* Display and set space preferences
*
* Allows space administrators to displays and modify space preferences 
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: preferences.php,v 1.15 2007/07/30 01:57:07 glendavies Exp $
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

	$space_key	  = $_POST['space_key'];
	$new_user_alert = $_POST['new_user_alert'];
	$status_key	 = $_POST['status_key'];
	$code		   = $_POST['code'];
	$name		   = $_POST['name'];
	$short_date_key = $_POST['short_date_key'];
	$long_date_key  = $_POST['long_date_key'];		
	$submit		 = $_POST['submit'];	
	
}
//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 
$page_details=get_page_details($space_key);


if (!$submit) {

	$sql = "SELECT new_user_alert,status_key,code,name,short_date_format_key, long_date_format_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$new_user_alert = $rs->fields[0];
		$status_key = $rs->fields[1];
		$code = $rs->fields[2];
		$name = $rs->fields[3];
		$short_date_key =$rs->fields[4];
		$long_date_key  =$rs->fields[5];						
		$rs->MoveNext();
	}
	
	$rs->Close();
	
	$alert_menu='<select name="new_user_alert">';
	
	if ($new_user_alert=='true') {
 
		$alert_menu.='<option value="true" selected="selected">'.$general_strings['yes'].'</option>';
		$alert_menu.='<option value="false" >'.$general_strings['no'].'</option></select>';  

	} else {

		$alert_menu.='<option value="true" >'.$general_strings['yes'].'</option>';
		$alert_menu.='<option value="false" selected="selected">'.$general_strings['no'].'</option></select>';

	}

} else {

	$errors = check_form_input();

	if(count($errors) == 0) {

		$name = $name;
		$code = $code;		
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}spaces SET new_user_alert='$new_user_alert',status_key='$status_key',code='$code',name='$name', short_date_format_key='$short_date_key', long_date_format_key='$long_date_key' WHERE space_key='$space_key'";
		$CONN->Execute($sql);

		$message = urlencode($space_strings['preferences_updated']);
		header("Location: {$CONFIG['FULL_URL']}/spaceadmin/admin.php?space_key=$space_key&message=$message");
	
	} else {
		
		$message = $general_strings['problem_below'];
	
	}
	
}
$status_sql = "SELECT name, Spacestatus_key FROM {$CONFIG['DB_PREFIX']}spacestatus ORDER BY name";
$status_menu = make_menu($status_sql,'status_key',$status_key,'4');

$short_date_sql = "select format, format_key from {$CONFIG['DB_PREFIX']}date_formats WHERE type='short' ORDER BY format_key";
$short_date_menu = make_menu($short_date_sql,"short_date_key",$short_date_key,"2");

$long_date_sql = "select format, format_key from {$CONFIG['DB_PREFIX']}date_formats WHERE type='long' ORDER  BY format_key";
$long_date_menu = make_menu($long_date_sql,"long_date_key",$long_date_key,"2");

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'	   => 'header.ihtml',
	'navigation'   => 'navigation.ihtml',
	'form'		 => 'spaceadmin/preferences.ihtml',
	'footer'	   => 'footer.ihtml'
));
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
//format any errors from form submission

$name_error = sprint_error($errors['name']);
$code_error = sprint_error($errors['code']);

$t->parse('CONTENTS', 'header', true);
get_navigation();
$t->set_var('NAME_ERROR',$name_error);
$t->set_var('CODE_ERROR',$code_error);
$t->set_var('ALERT_MENU',$alert_menu);
$t->set_var('STATUS_MENU',$status_menu);
$t->set_var('SHORT_DATE_MENU',$short_date_menu);
$t->set_var('LONG_DATE_MENU',$long_date_menu);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('NAME',$name);
$t->set_var('CODE',$code);
$t->set_var('PREFERENCES_STRING',$space_strings['preferences']);
$t->set_var('SHORT_DATE_STRING',$space_strings['short_date']);
$t->set_var('LONG_DATE_STRING',$space_strings['long_date']);
$t->set_var('NAME_STRING',$space_strings['name']);
$t->set_var('CODE_STRING',$space_strings['code']);
$t->set_var('STATUS_STRING',$space_strings['status']);
$t->set_var('EMAIL_ADMINS_STRING',sprintf($space_strings['email_admins'], $general_strings['space_text']));
$t->set_var('SUBMIT_STRING',$general_strings['submit']);

$t->parse('CONTENTS','form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

function check_form_input() 
{
	
	global $code,$CONN,$space_key,$name, $space_strings, $CONFIG;
	// Initialize the errors array

	$errors = array();



	//check to see if we have all the information we need
	if(!$name) {

		$errors['name'] = $space_strings['no_name'];

	}
	
	if(!$code) {

		$errors['code'] = $space_strings['no_code'];

	} else {

		//check that code is not used by another space
		
		$sql = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$code'";
		$rs = $CONN->Execute($sql);
		if (!$rs->EOF) {
		
			while (!$rs->EOF) {
			
				$space_key2 = $rs->fields[0];
				$rs->MoveNext();
				
			}
			
			$rs->Close();
			
			if ($space_key!=$space_key2) {
			
				$errors['code'] = $space_strings['code_in_use'];
				
			}
			
		} 
	
	}

return $errors;
} //end check_form_input

?>
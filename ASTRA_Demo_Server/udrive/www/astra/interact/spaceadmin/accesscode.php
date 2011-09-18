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
* Set access code
*
* Displays and modifies a space access code 
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: accesscode.php,v 1.11 2007/07/30 01:57:07 glendavies Exp $
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

	$space_key   = $_POST['space_key'];
	$access_code = $_POST['access_code'];
	$submit	  = $_POST['submit'];	
	
}

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 

if (!$submit) {

	$sql = "SELECT access_code FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$access_code = $rs->fields[0];
		$rs->MoveNext();
		
	}
	
	$rs->Close();
	
} else {

	$errors = check_form_input();
	
	if(count($errors) == 0) {
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}spaces SET access_code='$access_code' WHERE space_key='$space_key'";
		$CONN->Execute($sql);
		
		$message = urlencode($space_strings['access_code_changed']);
		header("Location: {$CONFIG['FULL_URL']}/spaceadmin/admin.php?space_key=$space_key&message=$message");
	
	} else {
	
		$message = $general_strings['problem_below'];
	
	}
	
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'spaceadmin/accesscode.ihtml',
	'footer'		  => 'footer.ihtml'
));

//format any errors from form submission

$access_code_error = sprint_error($errors['access_code']);
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->parse('CONTENTS', 'header', true);
get_navigation();
$t->set_var('ACCESS_CODE_ERROR',$access_code_error);
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var('ACCESS_CODE_STRING',$space_strings['access_code']);
$t->set_var('ACCESS_CODE_HEADING',$space_strings['access_code_heading']);
$t->set_var('ACCESS_CODE',$access_code);
$t->set_var('SPACE_KEY',$space_key);

$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;


function check_form_input() 
{
	
	global $access_code, $space_strings;
	// Initialize the errors array

	$errors = array();

	

	//check to see if we have all the information we need
	
	if(!$access_code) {

		$errors['access_code'] = $space_strings['no_access_code'];

	}

	return $errors;

} //end check_form_input
?>
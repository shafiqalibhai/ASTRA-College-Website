<?php
// +----------------------------------------------------------------------+
// | This file is part of Interact.									   |
// |																	  | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)							 |
// |																	  | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 |
// | General Public License for more details.							 |
// |																	  | 
// | You should have received a copy of the GNU General Public License	|
// | along with this program; if not, you can view it at				  |
// | http://www.opensource.org/licenses/gpl-license.php				   |
// |																	  |
// +----------------------------------------------------------------------+

/**
* User Group Add
*
* Add a new user group
*
* @package UserAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: usergroupadd.php,v 1.13 2007/01/25 03:11:24 glendavies Exp $
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

// See if the form has been submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$user_group_name = isset($_POST['user_group_name'])? $_POST['user_group_name'] : '';
	$account_creation_password = isset($_POST['account_creation_password'])? $_POST['account_creation_password'] : '' ;
	
	// Initialize the errors array

	$errors = array();

	// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}

	//check that a category and parent category have been entered
	
	if(!$user_group_name || $user_group_name=='') {

		$errors['user_group_name'] = 'You didn\'t enter a name.';

	}



	if(count($errors) == 0) {
		
		$user_group_name = $user_group_name;
		$account_creation_password = $account_creation_password;
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}user_groups(group_name, account_creation_password) VALUES ('$user_group_name', '$account_creation_password')";
		
		if ($CONN->Execute($sql) === false) {
		
			$message =  'There was an error adding your user group: '.$CONN->ErrorMsg().' <br />';
			
		} else {
		
			$message = 'Your User Group was added successfully.';			
		
		}
	
	}

}


$action = 'usergroupadd.php?action=add'; 
$user_group_error = sprint_error($errors['user_group_name']);

$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header' => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'form' => 'admin/usergroupadd.ihtml',
	'footer' => 'footer.ihtml'));

set_common_admin_vars('Add a User Group', $message);


$t->set_var('USER_GROUP_ERROR',$user_group_error);

$t->set_var('ACTION',$action);
$t->set_var('BUTTON','Add User Group');
$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

$CONN->Close();
exit;
?>
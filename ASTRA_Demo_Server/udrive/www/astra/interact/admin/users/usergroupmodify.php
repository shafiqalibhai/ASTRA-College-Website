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
* User Group Modify
*
* Modify a user group
*
* @package UserAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: usergroupmodify.php,v 1.14 2007/05/06 23:18:02 glendavies Exp $
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}

	$user_group_key  = $_POST['user_group_key'];
	$user_group_name = $_POST['user_group_name'];
	$account_creation_password = $_POST['account_creation_password'];	
	$action		  = $_GET['action'];
	$submit		  = $_POST['submit'];		
	
	//check that a user group was selected
 

	if(!$user_group_key) {

		$errors['user_group'] = 'You didn\'t select a user group.';


	} else {
	
		if($action=='modify') {

			$result = display_user_group ($user_group_key,$CONFIG['TEMPLATES_PATH']);
			
			if ($result==true) {
				
				exit;
			
			}
		
		}
		
		if ($submit=='Modify'){
		
			
			$message = modify_user_group ($user_group_key, $user_group_name,$account_creation_password, $CONFIG['TEMPLATES_PATH']);
		
		} else if ($submit=='Delete'){
			
			delete_user_group ($user_group_key);
			$message = "The User Group was deleted<br />";
		
		}
   
   }

}


$sql	= "select group_name, user_group_key from {$CONFIG['DB_PREFIX']}user_groups";
$menu   = make_menu($sql,'user_group_key','','4');
$action = 'usergroupmodify.php?action=modify'; 
$user_group_error = sprint_error($errors['user_group']);


$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header' => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'form' => 'admin/usergrouplist.ihtml',
	'footer' => 'footer.ihtml'));
	
set_common_admin_vars('Modify a User Group', $message);

$t->set_var('USER_GROUP_MENU',$menu);
$t->set_var('USER_GROUP_ERROR',$user_group_error);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON','Submit');
$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p("CONTENTS");
exit;

function display_user_group($user_group_key,$TEMPLATES_PATH)
{

	global $CONN,$t,$CONFIG;
	$errors = array();

	$sql2 = "select group_name, account_creation_password from {$CONFIG['DB_PREFIX']}user_groups where user_group_key=$user_group_key";
	$rs = $CONN->Execute($sql2);

	while (!$rs->EOF) {

		$group_name = $rs->fields[0];
		$account_creation_password = $rs->fields[1];
		$rs->MoveNext();

	}

	$delete_button = "<input type=\"submit\" name=\"submit\" value=\"Delete\" onClick=\"return confirmDelete('Are you sure?')\">";
	

	$action = 'usergroupmodify.php'; 
	$message =  'Make the required changes and click Modify';

	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array(
	
		  'header'	 => 'header.ihtml',
		  'navigation' => 'admin/adminnavigation.ihtml',
		  'form'	   => 'admin/usergroupadd.ihtml',
		  'footer'	 => 'footer.ihtml'));
	set_common_admin_vars('Modify a User Group', $message);		  
	$t->set_var('SPACE_TITLE','Modify a User Group'); 
	$t->set_var('PAGE_TITLE','Modify a User Group');
	$t->set_var('USER_GROUP_NAME',$group_name);
	$t->set_var('ACCOUNT_CREATION_PASSWORD',$account_creation_password);
	$t->set_var('USER_GROUP_KEY',$user_group_key);
	
	$t->set_var('ACTION',$action);
	$t->set_var('BUTTON','Modify');
	$t->set_var('PATH',$CONFIG['PATH']);	
	$t->set_var('DELETE_BUTTON',$delete_button);
	$t->parse('CONTENTS', 'header', true); 
	admin_navigation();
	$t->parse('CONTENTS', 'form', true);
	$t->parse('CONTENTS', 'footer', true);
	$t->p('CONTENTS');
	return true;
	
} //end display_categories


function modify_user_group($user_group_key, $user_group_name,$account_creation_password,$TEMPLATES_PATH)
{
// Initialize the errors array
	global $CONN, $CONFIG;
	$errors = array();
	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}user_groups SET group_name='$user_group_name', account_creation_password='$account_creation_password' WHERE user_group_key='$user_group_key'";
		
	if ($CONN->Execute($sql) === false) {
		
		$message =  'There was an error modifying your user_group: '.$CONN->ErrorMsg().' <br />';
			
	} else {
		
		$message = 'The User Group was modified successfully.';			
		
	}
	
	return $message;
	
} //end modify_category

function delete_user_group($user_group_key) 
{
	
	global $CONN, $CONFIG;

   //delete any UserUserGrouplinks entries and DefaultSpaceUserlink entries first

   $CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_group_key='$user_group_key'");
   $CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}default_space_user_links WHERE user_group_key='$user_group_key'"); 
	 
   $CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}user_groups WHERE user_group_key='$user_group_key'");   

}

?>
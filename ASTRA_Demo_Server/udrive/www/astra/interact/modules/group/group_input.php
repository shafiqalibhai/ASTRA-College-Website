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
* Group module
*
* Inputs/modifies/deletes a group module 
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: group_input.php,v 1.23 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

//check we have the required variables

if ($_POST['space_key']) {
	
	$space_key  = $_POST['space_key'];
	$module_key = $_POST['module_key']; 
	$link_key   = $_POST['link_key']; 	   
	
} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];
	$managed_group   = isset($_GET['managed_group'])?$_GET['managed_group']:'';	
}

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object

$modules = new InteractModules();
$modules->set_module_type('group');

//find out what action we need to take

if (isset($_POST[submit])) {


	switch($_POST[submit]) {

		//if we are adding a new group form input needs to be checked 

		case $general_strings['add']:
		
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('group');


			//if the add was successful return the browser to space home or parent group
				if ($message=='true') {
					 
					$modules->return_to_parent('group','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {
				
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			$errors = check_form_input();
	
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}

		   if(count($errors) == 0) {

				$message = $modules->modify_module('group',$can_edit_module);

				//return browser to space home or parent group

				   if ($message=='true') {

					  $modules->return_to_parent('group','modified');
					exit;

				}  

			} else {
			
				$message = $general_strings['problem_below'];
			 
			}
			
			break;
			
		case $general_strings['delete']:
		
			$space_key	 = $_POST['space_key'];
			$module_key	= $_POST['module_key'];
			$parent_key	= $_POST['parent_key'];
			$group_key	 = $_POST['group_key'];
			$link_key	  = $_POST['link_key'];
									
			header ("Location: {$CONFIG['FULL_URL']}/modules/general/moduledelete.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key");
			exit;			
			
		default:
			$message = $general_strings['no_action'];
			break;
			
	} //end switch($_POST[submit])			

} //end isset($_POST[submit])

if ($_GET[action]=='modify') {

	$module_data	= $modules->get_module_data('group');
	$sort_order_key = $module_data['sort_order_key'];
	$access_key	 = $module_data['access_key'];
	$access_code	= $module_data['access_code'];	
	$maximum_users  = $module_data['maximum_users'];
	$visibility_key = $module_data['visibility_key'];	
	$minimum_users  = $module_data['minimum_users'];
	$start_date  	= $module_data['start_date'];
	$finish_date 	= $module_data['finish_date'];
	$group_management = $module_data['group_management'];						
 
} //end if ($_GET[action]=="modify")		  

if (!isset($_GET[action]) && !isset($_POST[action])) {
	$action = 'add';
	$title = $group_strings['add_group'];
	$sort_order_key = '2';	
	$button = $general_strings['add'];
	$access_key = 1;
	$visibility_key = 1;
	$maximum_users = 0;
	$minimum_users = 0;
	$group_management = 0;
}
if ($_GET[action]=='modify' || $_POST[submit]==$general_strings['modify']) {

	$action = 'modify2';
	$button = $general_strings['modify'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';

}

//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

$name_error = sprint_error($errors['name']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'groups/group_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'group');
//generate the editor components

if (!class_exists('InteractHtml')) {
	require_once('../../includes/lib/html.inc.php');
}
$objHtml = new InteractHtml();
$objHtml->setTextEditor($t, false, 'description');
//create instance of date object for date functions
if (!class_exists('InteractDate')) {
	require_once('../../includes/lib/date.inc.php');
}
$objDates = new InteractDate();
$start_date_menu  = $objDates->createDateSelect('start_date',$module_data['start_date_unix'], true);
$finish_date_menu  = $objDates->createDateSelect('finish_date',$module_data['finish_date_unix'], true);

$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();


//make sort order menu
$sort_order_sql = "SELECT sort_order,sort_order_key FROM {$CONFIG['DB_PREFIX']}sort_orders ORDER BY sort_order";
$sort_order_menu = make_menu($sort_order_sql,'sort_order_key',$sort_order_key,'4');

$group_management_menu = $objHtml->arrayToMenu(array('0' => ucfirst($general_strings['off']), '1' => ucfirst($general_strings['on'])),'group_management', $module_data['group_management'], false, 2);

//if in group management mode remove access,visibility and sort order options

if ($managed_group==1) {
	
	$t->set_block('form', 'group_managementBlock', 'GMBlock');
	$t->set_var('GMBlock','<input name="access_key" type="hidden" id="access_key" value="2">
              <input name="visibility_key" type="hidden" id="visibility_key" value="1">
              <input name="sort_order_key" type="hidden" id="sort_order_key" value="3">');
}
$t->set_var('SORT_ORDER_MENU',$sort_order_menu);
$t->set_var('SORT_ORDER_STRING',$group_strings['sort_order']);
$t->set_var('ACCESS_STRING',$group_strings['access']);
$t->set_var('ACCESS_CODE_STRING',$group_strings['access_code']);
$t->set_var('ADMIN_REGISTRATION_STRING',$group_strings['admin_registration']);
$t->set_var('SELF_REGISTRATION_STRING',$group_strings['self_registration']);
$t->set_var('RESTRICTED_REGISTRATION_STRING',$group_strings['restricted_registration']);
$t->set_var('MAXIMUM_USERS_STRING',$group_strings['maximum_users']);
$t->set_var('NO_LIMIT_STRING',$group_strings['no_limit']);
$t->set_var('VISIBILITY_STRING',$group_strings['visibility']);
$t->set_var('VISIBLE_TO_ALL_STRING',$group_strings['visible_to_all']);
$t->set_var('VISIBLE_TO_MEMBERS_STRING',$group_strings['visible_to_members']);
$t->set_var('ADVANCED_SETTINGS_STRING',$group_strings['advanced_settings']);
$t->set_var('MINIMUM_USERS_STRING',$group_strings['minimum_users']);
$t->set_var('NO_MINIMUM_STRING',$group_strings['no_minimum']);
$t->set_var('START_DATE_STRING',$group_strings['start_date']);
$t->set_var('FINISH_DATE_STRING',$group_strings['finish_date']);
$t->set_var('GROUP_MANAGMENT_STRING',$group_strings['group_management']);
$t->set_var('GROUP_MANAGEMENT_MENU',$group_management_menu);
$t->set_var('MAXIMUM_USERS',$maximum_users);
$t->set_var('MINIMUM_USERS',$minimum_users);
$t->set_var('START_DATE_MENU',$start_date_menu);
$t->set_var('FINISH_DATE_MENU',$finish_date_menu);
$t->set_var('ACCESS_CODE',$access_code);


$t->set_var($access_key.'_CHECKED','checked');
$t->set_var($visibility_key.'_VISIBILITY_CHECKED','checked');
$t->parse('GENERAL_SETTINGS', 'general', true);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;


/**
* Check form input   
* 
*  
* @return $errors
*/
function check_form_input() 
{

	global $general_strings;
// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($_POST)){

		$_POST[$key] = trim($value);

	}

//check to see if we have all the information we need
	if(!$_POST['name']) {

		$errors['name'] = $general_strings['no_name'];

	}

	return $errors;
	
} //end check_form_input


?>
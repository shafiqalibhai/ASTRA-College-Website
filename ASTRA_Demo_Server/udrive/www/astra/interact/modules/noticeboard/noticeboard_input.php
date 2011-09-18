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
* Noticeboard module
*
* Inputs/modifies/deletes a noticeboard module 
*
* @package Noticeboard
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: noticeboard_input.php,v 1.18 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/noticeboard_strings.inc.php');

//check we have the required variables

if ($_POST['space_key']) {
	
	$space_key  = $_POST['space_key'];
	$module_key = $_POST['module_key']; 
	$link_key   = $_POST['link_key']; 	   
	
} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];	
}

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels	 = authenticate(true);
$accesslevel_key   = $access_levels['accesslevel_key'];
$group_access	  = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object

$modules = new InteractModules();
$modules->set_module_type('noticeboard');

//find out what action we need to take

if (isset($_POST[submit])) {


	switch($_POST[submit]) {

		//if we are adding a new noticeboard form input needs to be checked 

		case $general_strings['add']:
		
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('noticeboard');


			//if the add was successful return the browser to space home or parent noticeboard
				if ($message=='true') {
					 
					$modules->return_to_parent('noticeboard','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {
				
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
	
			if(count($errors) == 0) {

				 $message = $modules->modify_module('noticeboard',$can_edit_module);

				//return browser to space home or parent noticeboard

				   if ($message=='true') {

					$modules->return_to_parent('noticeboard','modified');
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

if ($_GET['action']=='modify') {

	$module_data  = $modules->get_module_data('noticeboard');
	$type_key	 = $module_data['type_key'];
	$days_to_keep = $module_data['days_to_keep'];	

} //end if ($_GET[action]=="modify")		  

if (!isset($_GET['action']) && !isset($_POST['action'])) {
	
	$action = 'add';
	$title = $noticeboard_strings['add_noticeboard'];
	$button = $general_strings['add'];

}

if ($_GET['action']=='modify' || $_POST['submit']==$general_strings['modify']) {
	
	$action = 'modify2';
	$button = $general_strings['modify'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';

}

//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

$name_error = sprint_error($errors['name']);
$days_to_keep_error = sprint_error($errors['days_to_keep']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'noticeboard/noticeboard_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'noticeboard');

$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu

get_navigation();

$t->set_var('DAYS_TO_KEEP_ERROR',$days_to_keep_error);
$t->set_var('OPEN_STRING',$general_strings['open']);
$t->set_var('CLOSED_STRING',$general_strings['closed']);
$t->set_var('TYPE_STRING',$general_strings['type']);
$t->set_var('DAYS_TO_KEEP_STRING',$noticeboard_strings['days_to_keep']);
$t->set_var('DAYS_TO_KEEP_STRING2',$noticeboard_strings['days_to_keep2']);
$t->set_var('DAYS_TO_KEEP',$days_to_keep);
$t->parse('GENERAL_SETTINGS', 'general', true);

if ($type_key==2 || $action=='add') {
	
	$t->set_var('CLOSED_SELECTED','selected=\"selected\"');
	
} else if ($type_key==1){

	$t->set_var('OPEN_SELECTED','selected=\"selected\"');
		
}
$t->set_var('DAYS_TO_KEEP',$days_to_keep);
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

	global $general_strings, $noticeboard_strings;
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

	//check to see if we have all the information we need
	if(!is_numeric($_POST['days_to_keep'])) {

		$errors['days_to_keep'] = $noticeboard_strings['days_to_keep_error'];

	}
	return $errors;
	
} //end check_form_input


?>
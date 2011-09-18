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
* KnowledgeBase module
*
* Inputs/modifies/deletes a KnowledgeBase module 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: kb_input.php,v 1.18 2007/07/30 01:57:03 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/kb_strings.inc.php');

//check we have the required variables

if ($_POST['space_key']) {
	
	$space_key		  = $_POST['space_key'];
	$module_key		 = $_POST['module_key']; 
	$link_key		   = $_POST['link_key'];
	$kbaccess_level_key = $_POST['kbaccess_level_key']; 

	
} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];
		
}

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels   = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access	= $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);
	


//create new modules object

$modules = new InteractModules();
$modules->set_module_type('kb');

//find out what action we need to take
if (isset($_POST['submit'])) {


	switch($_POST['submit']) {

		//if we are adding a new quiz form input needs to be checked 

		case $general_strings['add']:
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('kb');

			//if the add was successful return the browser to space home or parent quiz
				if ($message=='true') {
					 
					$modules->return_to_parent('kb','added');
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

				 $message = $modules->modify_module('kb',$can_edit_module);

				//return browser to space home or parent quiz

				   if ($message=='true') {

					  $modules->return_to_parent('kb','modified');
					exit;

				}  

			} else {
			
				$message = $message = $general_strings['problem_below'];
			 
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

	$module_data  = $modules->get_module_data('kb');
	$kbaccess_level_key = $module_data['kbaccess_level_key'];
 
} //end if ($_GET[action]=="modify")		  

if (!isset($_GET['action']) && !isset($_POST['action'])) {
	$action = 'add';
	$title = $kb_strings['add_kb'];
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

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'kb/kb_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'kb');


//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, false, 'description');

$t->set_var('MESSAGE',$message);

$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();

$access_level_menu = $html->arrayToMenu(array('1' => $kb_strings['access_level_1'], '2' => $kb_strings['access_level_2']),'kbaccess_level_key', $kbaccess_level_key, false, 2);


$t->set_var('TYPE_STRING',$general_strings['type']);
$t->set_var('ACCESS_LEVEL_MENU',$access_level_menu);
$t->set_var('ACCESS_LEVEL_STRING',$kb_strings['access_level']);

  

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

// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($_POST)){

		$_POST[$key] = trim($value);

	}

//check to see if we have all the information we need
	return $errors;
	
} //end check_form_input
		

?>
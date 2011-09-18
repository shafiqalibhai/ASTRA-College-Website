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
* Page module input
*
* Inputs/modifies/deletes a page module 
*
* @package Page
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: page_input.php,v 1.28 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/page_strings.inc.php');

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

$access_levels = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object

$modules = new InteractModules();
$modules->set_module_type('page');

//find out what action we need to take

if (isset($_POST['submit'])) {


	switch($_POST['submit']) {

		//if we are adding a new page form input needs to be checked 

		case $general_strings['add']:
		
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('page');


			//if the add was successful return the browser to space home or parent page
				if ($message=='true') {
					
					$modules->return_to_parent('page','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {

				$body = $_POST['body'];				
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
	
			if(count($errors) == 0) {

				 $message = $modules->modify_module('page',$can_edit_module);

				//return browser to space home or parent page

				   if ($message=='true') {

					$modules->return_to_parent('page','modified');
					exit;

				}  

			} else {
						
				$body = $_POST['body'];
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

	$module_data = $modules->get_module_data('page');
	$body		= $module_data['body'];	
 
} //end if ($_GET[action]=="modify")		  

if (!isset($_GET['action']) && !isset($_POST['action'])) {

	$action = 'add';
	$title  = $page_strings['add_page'];
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
$body_error = sprint_error($errors['body']);
//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'pages/page_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'page');
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('BODY',$body);

$t->set_var('UPLOAD_STRING',$page_strings['upload']);
$t->set_var('HTML_STRING',$page_strings['html_instruction']);
$t->set_var('ENTER_PAGE_STRING',$page_strings['enter_page']);
$t->set_var('INPUT_SUGGESTION',$general_strings['text_input_suggestion']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('VERSION_COUNT',$general_strings['version_count']);
$t->set_var('EDIT_RIGHTS',$general_strings['edit_rights']);
//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'body', true);
$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu

get_navigation();
$t->set_var('VERSION_COUNT_MENU', $objHtml->arrayToMenu(array('1' => '1', '5' => '5', '10' => '10', '0' => $general_strings['infinite']),'versions',isset($module_data['versions'])?$module_data['versions']:1,'','',false));
$t->set_var('PAGE_EDIT_MENU', $objHtml->arrayToMenu(array('0' => $general_strings['admins'], '1' => $general_strings['members'], '2' => $general_strings['logged_in_users'], '3' => $general_strings['anyone']),'page_edit_rights',isset($module_data['page_edit_rights'])?$module_data['page_edit_rights']:0,'','',false));
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
	
	if (!$_FILES['file']['name']) {
	
		if(!$_POST['body']) {

			$errors['body'] = $general_strings['no_body'];

		}
		
	}

	return $errors;
	
} //end check_form_input


?>
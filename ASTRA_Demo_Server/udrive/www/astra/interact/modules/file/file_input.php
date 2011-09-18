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
* File input
*
* Input/modify a file module
*
* @package File
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: file_input.php,v 1.20 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/file_strings.inc.php');

//set the required variables

if ($_POST['space_key']) {
	
	$space_key  = $_POST['space_key'];
	$module_key = $_POST['module_key']; 
	$link_key   = $_POST['link_key']; 
	$action     = isset($_POST['action'])?$_POST['action']:'';	   
	
} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];
	$action     = isset($_GET['action'])?$_GET['action']:'';		
}	


//check we have the required variables
//check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object

$modules = new InteractModules();
$modules->set_module_type('file');

//find out what action we need to take

if (isset($_POST[submit])) {


	switch($_POST[submit]) {

		//if we are adding a new file form input needs to be checked 

		case $general_strings['add']:

			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('file');


			//if the add was successful return the browser to space home or parent file
				if ($message===true) {
					 
					$modules->return_to_parent('file','added');
					exit;
				
				} else {  
			   
					$button = $general_strings['add'];
					 
				}

			//if the add wasn't succesful return to form with error message

			} else {

				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];;
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
	
			if(count($errors) == 0) {

				 $message = $modules->modify_module('file',$can_edit_module);

				//return browser to space home or parent file

				   if ($message=='true') {

					   $modules->return_to_parent('file','modified');
					   exit;

				}  

			} else {
						
				$message = $general_strings['problem_below'];
			 
			}
			
			break;
			
		case $general_strings['delete']:
		
			$space_key	 = $_POST[space_key];
			$module_key	= $_POST[module_key];
			$parent_key	= $_POST[parent_key];
			$group_key	 = $_POST[group_key];
			$link_key	  = $_POST[link_key];
									
			header ("Location: {$CONFIG['FULL_URL']}/modules/general/moduledelete.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key");
			exit;			
			
		default:
		
			$message = $general_strings['no_action'];
			break;
			
	} //end switch($_POST[submit])			

} //end isset($_POST[submit])

if ($_GET[action]=='modify') {

	$module_data = $modules->get_module_data('file');
	$embedded	 = $module_data['embedded'];	
 
} //end if ($_GET[action]=="modify")		  

if (!isset($action) || $action=='') {

	$action = 'add';
	$title = $file_strings['title'];
	$button = $general_strings['add'];

}

if ($_GET[action]=='modify' || $_POST[submit]=='Modify') {

	$action = 'modify2';
	$button = $general_strings['modify'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';

}

//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

$name_error = sprint_error($errors['name']);
$file_error = sprint_error($errors['file']);
$file_type_error = sprint_error($errors['file_type']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'files/file_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'file');

if ($embedded==1) {

	$t->set_var('EMBEDDED_CHECKED','checked');

}
$t->set_var('FILE_ERROR',$file_error);
$t->set_var('FILE_TYPE_ERROR',$file_type_error);
$t->set_var('FILE_STRING',$general_strings['file']);
$t->set_var('FILE_TYPE_STRING',$general_strings['file_type']);
$t->set_var('ZIP_STRING',$file_strings['zip_options']);
$t->set_var('UNZIP_STRING',$file_strings['unzip']);
$t->set_var('START_FILE_STRING',$file_strings['start_file']);
$t->set_var('EMBED_FILE_STRING',$file_strings['embed_file']);
$t->set_var('ASSOCIATED_STRING',$file_strings['associated']);
$t->set_var('ASSOCIATED_EXAMPLE',$file_strings['associated_example']);

$t->set_var('MAX_FILE_UPLOAD_SIZE',$CONFIG['MAX_FILE_UPLOAD_SIZE']);
$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu

get_navigation();
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

	global $general_strings, $file_strings;
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
	
	if ($_POST['action']=='add' || ($_POST['action']=='modify2' && $_FILES['user_file']['name']!='')) {
	
		$check_file_ok = check_file_upload('user_file');
   
		if ($check_file_ok!='true') {
   
		   $errors["file"] = $check_file_ok;
	   
		}
		if ($_FILES['user_file']['name']=='.htaccess') {
   
		   $errors['file'] = $file_strings['htaccess'];
	   
		}
		
		if(!$_POST['file_extension']) {

		$errors['file_type'] = $general_strings['no_file_type'];
	   
		}
   }

	return $errors;
	
} //end check_form_input


?>
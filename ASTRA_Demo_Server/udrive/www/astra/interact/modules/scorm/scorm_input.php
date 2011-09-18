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
* Scorm input
*
* Input/modify a scorm module
*
* @package Scorm
* @author Bruce Webster <bruce.webster@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: scorm_input.php,v 1.20 2007/05/30 13:36:38 websterb4 Exp $
* 
*/
/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');
require_once('scorm.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/scorm_strings.inc.php');


//set the required variables

if ($_POST['space_key']) {
	
	$space_key  = $_POST['space_key'];
	$module_key = $_POST['module_key']; 
	$link_key   = $_POST['link_key']; 	   
	if(isset($_POST['action'])){$action=$_POST['action'];};	   

} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];
	if(isset($_GET['action'])){$action=$_GET['action'];};	   
}

if (!isset($action)) {
	$action = 'add';
	$button = $general_strings['add'];
}

if ($_GET[action]=='modify' || $_POST[submit]=='Modify') {

	$action = 'modify2';
	$button = $general_strings['modify'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';

}

//check we have the required variables
//check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$message='';
$access_levels = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);
//create new modules object

$modules = new InteractModules();
$modules->set_module_type('scorm');

//find out what action we need to take

if (isset($_POST[submit])) {

	switch($_POST[submit]) {

		//if we are adding a new file form input needs to be checked 

		case $general_strings['add']:
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('scorm');


			//if the add was successful return the browser to space home or parent file
				if ($message===true) {
					 
					$modules->return_to_parent('scorm','added');
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

				 $message = $modules->modify_module('scorm',$can_edit_module);

				//return browser to space home or parent file

				   if ($message=='true') {

					   $modules->return_to_parent('scorm','modified');
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

	$module_data = $modules->get_module_data('scorm');
	$embedded	 = $module_data['embedded'];	
 
} //end if ($_GET[action]=="modify")		  

//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

//$name_error = sprint_error($errors['name']);
//$file_error = sprint_error($errors['file']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'scorm/scorm_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'scorm');

$t->set_var('MAX_FILE_UPLOAD_SIZE',$CONFIG['MAX_FILE_UPLOAD_SIZE']);

if (!isset($module_data['width'])) {
	if (isset($_POST['width'])) {
		$module_data['width']=$_POST['width'];
		$module_data['height']=$_POST['height'];
		$module_data['browsemode']=($_POST['browsemode']?1:0);
	} else {
		$module_data['width']=700;
		$module_data['height']=500;  //MINIMUM size default
		$module_data['browsemode']=1;
	}
}


if ($module_data['browsemode']) {
	$t->set_var('BROWSEMODE_CHECKED','checked');
}

//if ($embedded==1) {
//	$t->set_var('EMBEDDED_CHECKED','checked');
//}

$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();

$module_data['message']=$message;
$t->set_strings('form', $scorm_strings,$module_data,$errors);

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

	global $general_strings, $scorm_strings;
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
	if(($_POST['width']<100) || ($_POST['height']<75)) {
		$errors['minsize']="Invalid value for minimum size.";
	}
	if ($_POST['action']=='add' || ($_POST['action']=='modify2' && $_FILES['user_file']['name']!='')) {
		if (strtolower(ereg_replace("^.+\\.([^.]+)$", "\\1", $_FILES['user_file']['name']))!='zip') {
			$errors['file']= "SCORM Package file did not have a .zip extension.";
		}
	
		$check_file_ok = check_file_upload('user_file');
   
		if ($check_file_ok!=true) {
   
		   $errors["file"] = $check_file_ok;
	   
		}

		
   }

	return $errors;
	
} //end check_form_input




/**
* Function called by Module class to modify exisiting file data 
*
* @param  int $module_key  key of file module
* @param  int $link_key  link key of file module being modified
* @return true if successful
*/

function modify_scorm($module_key,$link_key) {
	global $CONN, $CONFIG;
	
	$file_name		= $_FILES['user_file']['name'];
//	$embedded		 = $_POST['embedded'];	
	$name		 = $_POST['name'];  
		
	//get the exisitng file info so we can delete it if need be
	$sql = "select file_path from {$CONFIG['DB_PREFIX']}scorm where module_key='$module_key'";

	$rs = $CONN->Execute($sql);

	$file_path = $rs->fields[0];
				
	if (!$file_name) {   
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}scorm SET width={$_POST['width']},height={$_POST['height']},browsemode=".($_POST['browsemode']?1:0)." WHERE module_key=$module_key";
		if ($CONN->Execute($sql) === false) {
		
			return 'There was an error modifying your '.$module_strings['scorm'].': '.$CONN->ErrorMsg().' <br />';
		} else {
		   return true;  
		}
	} else {   
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE module_key='$module_key'");		
		if($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key='$module_key'")=== false) {return "Error updating records: ".$CONN->ErrorMsg();} else {	
			if($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key='$module_key'")=== false) {return "Error updating records: ".$CONN->ErrorMsg();} else {	
				if ($file_path) {delete_directory($CONFIG['MODULE_FILE_SAVE_PATH'].'/scorm/'.$file_path);}
				return add_scorm($module_key);
			}
		}

	}
		
}
 //end modify_scorm
?>
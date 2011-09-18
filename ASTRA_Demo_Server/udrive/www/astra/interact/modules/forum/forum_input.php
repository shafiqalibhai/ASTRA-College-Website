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
* Input a new forum
*
* Main file for adding/modifying/deleting a forum 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: forum_input.php,v 1.28 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

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

//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);


//create new modules object

$modules = new InteractModules();
$modules->set_module_type('forum');

//find out what action we need to take

if (isset($_POST[submit])) {


	switch($_POST[submit]) {

		//if we are adding a new forum form input needs to be checked 

		case $general_strings['add']:
		
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('forum');


			//if the add was successful return the browser to space home or parent forum
				if ($message=='true') {
					 
					$modules->return_to_parent('forum','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {
			   
				$forum_type = $_POST['forum_type'];			  
				$button  = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
				
			if(count($errors) == 0) {

				 $message = $modules->modify_module('forum',$can_edit_module);

				//return browser to space home or parent forum

				   if ($message=='true') {

					  $modules->return_to_parent('forum','modified');
					  exit;

				}  

			} else {
			
				$forum_type = $_POST['forum_type'];			
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

	$module_data	  = $modules->get_module_data('forum');
	$forum_type	   = $module_data['forum_type'];
	$auto_prompting   = $module_data['auto_prompting'];
	$days_to_wait	 = $module_data['days_to_wait'];
	$number_to_prompt = $module_data['number_to_prompt'];
	$passes_allowed   = $module_data['passes_allowed'];
	$response_time	= $module_data['response_time'];
	$minimum_replies  = $module_data['minimum_replies'];				
 
} //end if ($_GET[action]=="modify")		  

if (!isset($_GET[action]) && !isset($_POST[action])) {

	$action = 'add';
	$button = $general_strings['add'];
	$auto_prompting   = 0;
	$days_to_wait	 = 1;
	$number_to_prompt = 1;
	$passes_allowed   = 5;
	$response_time	= 1;
	$minimum_replies  = 2;	
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
	'form'			=> 'forums/forum_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'forum');
//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, false, 'description');
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('TYPE_STRING',$general_strings['type']);
$t->set_var('POST_EDITING_STRING',$forum_strings['post_editing']);
$t->set_var('AUTO_PROMPTING_STRING',$forum_strings['autoprompting']);
$t->set_var('DAYS_TO_WAIT_STRING',$forum_strings['days_to_wait']);
$t->set_var('RESPONSE_TIME_STRING',$forum_strings['response_time']);
$t->set_var('NUMBER_TO_PROMPT_STRING',$forum_strings['number_to_prompt']);
$t->set_var('PASSES_ALLOWED_STRING',$forum_strings['passes_allowed']);
$t->set_var('DAYS_TO_WAIT_STRING',$forum_strings['days_to_wait']);
$t->set_var('SEPARATE_STRING',$forum_strings['separate']);
$t->set_var('EMBEDDED_STRING',$forum_strings['embedded']);
$t->set_var('30_MINUTE_EDIT_STRING',$forum_strings['30_minute_edit']);
$t->set_var('ANY_TIME_EDIT_STRING',$forum_strings['any_time_edit']);
$t->set_var('OFF_STRING',$forum_strings['off']);
$t->set_var('ON_STRING',$forum_strings['on']);
$t->set_var('USER_ACTIVATED_STRING',$forum_strings['user_activated']);

if ($forum_type=='separate' || $action=='add') {
	
	$t->set_var('SEPARATE_SELECTED','selected=\"selected\"');
	
} else if ($forum_type=='embedded'){

	$t->set_var('EMBEDDED_SELECTED','selected=\"selected\"');
		
}

if ($auto_prompting=='1') {
	
	$t->set_var('AUTO_PROMPT_ON','selected=\"selected\"');
	
} else if ($auto_prompting=='0'){

	$t->set_var('AUTO_PROMPT_OFF','selected=\"selected\"');
		
} else if ($auto_prompting=='2'){

	$t->set_var('AUTO_PROMPT_USER','selected=\"selected\"');
		
}


if ($forum_edit_level=='1') {
	
	$t->set_var('1_SELECTED','selected=\"selected\"');
	
} else if ($forum_edit_level=='2'){

	$t->set_var('2_SELECTED','selected=\"selected\"');
		
}
$t->set_var('DAYS_TO_WAIT',$days_to_wait);
$t->set_var('NUMBER_TO_PROMPT',$number_to_prompt);
$t->set_var('PASSES_ALLOWED',$passes_allowed);
$t->set_var('RESPONSE_TIME',$response_time);
$t->set_var('MINIMUM_REPLIES',$minimum_replies);

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
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
* Journal module
*
* Inputs/modifies/deletes a journal module 
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: journal_input.php,v 1.31 2007/07/30 01:57:02 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php');

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
$modules->set_module_type('journal');

//find out what action we need to take

if (isset($_POST[submit])) {


	switch($_POST[submit]) {

		//if we are adding a new forum form input needs to be checked 

		case $general_strings['add']:

			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {
				$message = $modules->add_module('journal');

			//if the add was successful return the browser to space home or parent forum
				if ($message=='true') {
					 
					$modules->return_to_parent('journal','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {
			   
				$journal_type = $_POST['journal_type'];			  
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
				
			if(count($errors) == 0) {

				 $message = $modules->modify_module('journal',$can_edit_module);

				//return browser to space home or parent forum

				   if ($message=='true') {

					  $modules->return_to_parent('journal','modified');
					exit;

				}  

			} else {
			
				$journal_type = $_POST['journal_type'];			
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

	$module_data	  = $modules->get_module_data('journal');
	$journal_type_key = $module_data['journal_type_key'];
  
} //end if ($_GET[action]=="modify")		  

if (!isset($_GET[action]) && !isset($_POST[action])) {

	$action = 'add';
	$title = $journal_strings['add_journal'];
	$button = $general_strings['add'];
	
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
	'form'			=> 'journal/journal_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'journal');
if (!isset($objHtml) || !is_object($objHtml)){
	if (!class_exists('InteractHtml')) {
		require_once('../../includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}
if (!isset($objUser) || !is_object($objUser)){
	if (!class_exists('InteractUser')) {
		require_once('../../includes/lib/user.inc.php');
	}
	$objUser = new InteractUser();
}
$users_menu = $objHtml->arrayToMenu($objUser->getUserArray($space_key,$group_key),'selected_user_keys[]',$module_data['selected_user_keys'], true,5);

$journal_access_menu = $objHtml->arrayToMenu(array('1' => $journal_strings['open_access'], '0' => $journal_strings['restricted_access']),'options[]',$module_data['options']&1,false,'',false,'','');

$journals_for_menu = $objHtml->arrayToMenu(array('0' => $general_strings['all_members'], '2' => $general_strings['selected_members']),'options[]',$module_data['options']&2,false,'',false,'onChange="document.getElementById(\'users_menu_tr\').style.display=(this.firstChild.selected?\'none\':\'block\')"');
if(!($module_data['options']&2)) {$t->set_var('SELECTED_USERS_CLASS','jsHide');}

$comments_menu = $objHtml->arrayToMenu(array('4' => $general_strings['yes'], '0' => $general_strings['no']),'options[]',$module_data['options']&4, false,1,false,'','');

$default_display_menu = $objHtml->arrayToMenu(array('0' => $journal_strings['separate_journals'],'8' => $journal_strings['all_in_one']),'options[]',$module_data['options']&8, false,1,false,'','');

$edit_rights_menu = $objHtml->arrayToMenu(array('0' => $journal_strings['edit_all'],'16' => $journal_strings['edit_own']),'options[]',$module_data['options']&16, false,1,false,'','');

$enable_rss_menu = $objHtml->arrayToMenu(array('0' => $general_strings['no'],'32' => $general_strings['yes']),'options[]',$module_data['options']&32, false,1,false,'','');
if($module_data['options']&64) {
	$comments_setting = 64;
} else if ($module_data['options']&128) {
	$comments_setting = 128;
} else {
	$comments_setting = 0;
}
$t->set_var('ALLOW_COMMENTS_MENU',$objHtml->arrayToMenu(
	array('0' => $general_strings['from_logged_in_users'],'64' => $general_strings['from_anyone'], '128' => $general_strings['no']),
	'options[]',$comments_setting, false,1));
	
$objHtml->setTextEditor($t, false, 'description');

$t->parse('CONTENTS', 'header', true); 
$t->set_var('ACCESS_MENU',$journal_access_menu);
$t->set_var('JOURNALS_FOR_MENU',$journals_for_menu);
$t->set_var('USERS_MENU',$users_menu);
$t->set_var('COMMENTS_MENU',$comments_menu);
$t->set_var('EDIT_RIGHTS_MENU',$edit_rights_menu);
$t->set_var('DEFAULT_DISPLAY_MENU',$default_display_menu);
$t->set_var('ENABLE_RSS_MENU',$enable_rss_menu);
$t->set_var('ENTRIES_TO_SHOW_VALUE',isset($module_data['entries_to_show'])?$module_data['entries_to_show'] : '15');
//generate the navigation menu

get_navigation();

$t->set_var('ACCESS_STRING',$general_strings['access_rights']);
$t->set_var('JOURNALS_FOR_STRING',$journal_strings['journals_for']);
$t->set_var('SELECTED_USERS_STRING',$journal_strings['selected_users']);
$t->set_strings('form',  $journal_strings, '', $errors);
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



//check to see if we have all the information we need
	if(!$_POST['name']) {

		$errors['name'] = $general_strings['no_name'];

	}

	return $errors;
	
} //end check_form_input


?>
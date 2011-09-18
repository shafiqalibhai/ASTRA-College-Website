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
* Mark user
*
* Displays page for adding marks/comments by user
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: markuser.php,v 1.19 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/gradebook_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key			= $_GET['module_key'];
	$group_key			= $_GET['group_key'];
	$item_key		   = $_GET['item_key'];
	$user_key		   = $_GET['user_key'];
	$marked_item		= $_GET['marked_item'];	
	$action 			= $_GET['action'];
	
} else {

	$module_key	= $_POST['module_key'];
	$item_keys   = $_POST['item_keys'];
	$user_key   = $_POST['user_key'];
	$comments   = $_POST['body'];		
	$grade_key  = $_POST['grade_key'];
	$update_all	= $_POST['update_all'];
	$update_single = $_POST['update_single'];	
	
}

$userlevel_key = $_SESSION['userlevel_key'];

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
//check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

if ($is_admin==false) {

		$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	
}

$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

//see if we are adding a new entry

if ($_SERVER['REQUEST_METHOD']=='POST') {

	if ($is_admin==true) {

		if (!isset($update_single) || $update_all==1) {
		
			foreach($item_keys as $item_key) {
			
				$message = $gradebook->modifygrade($item_key, $user_key, $_POST[$item_key.'_grade'], $_POST[$item_key.'_comments']);
											
				if ($message===true) {
		
					$message = $message = $gradebook_strings['all_modified'];;

				} else {
		
					$message = $gradebook_strings['modify_grade_fail'].' - '.$message;
			
				}
				
			}
			
		} else {
					
			$message = $gradebook->modifygrade($update_single, $user_key, $_POST[$update_single.'_grade'], $_POST[$update_single.'_comments']);
											
			if ($message===true) {
		
				$message = urlencode($gradebook_strings['modify_grade_success']);
				header("Location:  {$CONFIG['FULL_URL']}/modules/gradebook/markuser.php?space_key=$space_key&module_key=$module_key&user_key=$user_key&marked_item=".$update_single.'_'.$user_key.'#'.$update_single.'_'.$user_key);
				exit;

			} else {
		
				$message = $gradebook_strings['modify_grade_fail'].' - '.$message;
			
			}
				
			
			
		}
		
	}
			
}

//create date object for date functions
if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'gradebook'  => 'gradebook/markitem.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($action=='' || $action=='add') {

	$t->set_var('ACTION', 'add');
	$t->set_var('BUTTON', $general_strings['modify']);
	$t->set_var('HEADING',$gradebook_strings['item_input_heading']);				
		
} else if ($action=='modify' || $action=='modify2') {
				
	$t->set_var('ACTION', 'modify2');
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')" />';
			
	$t->set_var('BUTTON', $general_strings['modify']);
	$t->set_var('DELETE_BUTTON', $delete_button);
	$t->set_var('ASSIGNMENT_KEY', $item_key);
	$t->set_var('HEADING',$gradebook_strings['item_modify_heading']);
	$t->set_var('ADDED_BY',$general_strings['added_by'].': '.$item_data['added_by_data']['first_name'].' '.$item_data['added_by_data']['last_name']);
	$t->set_var('DATE_ADDED',$dates->formatDate($item_data['date_added']));
	
	if ($item_data['modified_by']!='0') {
	
		$t->set_var('MODIFIED_BY',$general_strings['modified_by'].': '.$item_data['modified_by_data']['first_name'].' '.$item_data['modified_by_data']['last_name']);
		$t->set_var('DATE_MODIFIED',$dates->formatDate($item_data['date_added']));			
	
	}	
	
}

$name_error = sprint_error($errors['name']);
$weighting_error = sprint_error($errors['weighting']);
$t->set_var('NAME_ERROR',$name_error);
$t->set_var('WEIGHTING_ERROR',$weighting_error);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('ITEM_KEY',$item_key);
$t->set_var('MODIFY_ALL_STRING',$gradebook_strings['modify_all']);
$t->set_var('MODIFY_SINGLE_STRING',$gradebook_strings['modify_single']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('COMMENTS_STRING',$gradebook_strings['comments']);
$t->set_var('GRADE_STRING',$gradebook_strings['grade']);
$t->set_var('RETURN_TO_STRING',$general_strings['return_to']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('MESSAGE',$message);

if(!$user_key) {

   $t->set_var('HEADING_STRING',$gradebook_strings['by_user']);
	
} else {

	if (!class_exists('InteractUser')) {

		require_once('../../includes/lib/user.inc.php');
				
	}

   $user = new InteractUser();
   $user_data = $user->getUserData($user_key);
   $t->set_var('HEADING_STRING',sprintf($gradebook_strings['grade_heading_user'],$user_data['first_name'].' '.$user_data['last_name']));

}

//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}

$t->parse('CONTENTS', 'header', true); 

get_navigation();

//now get create a grading item box for each user
$t->set_block('gradebook', 'MarkItemBlock', 'MIBlock'); 

//if no user_key display list of users
if(!$user_key) {

	$user_data = $gradebook->getUserList();
	$user_list = $user_data['by_name'];
	
	if ($user_list==false) {
	
		$t->set_var('MIBlock',$gradebook_strings['no_users']);
		
	} else {
	
		foreach ($user_list as $key => $value) {
		
			$users .= "<p><a href=\"markuser.php?space_key=$space_key&module_key=$module_key&user_key=$key\">$value</a></p>";
			
		}
		
		$t->set_var('MIBlock',$users);

	}
		
} else {
 
   $gradebook->displayMarkItemBoxes($item_key, $user_key, 'by_user', $marked_item);

}

$t->parse('CONTENTS', 'gradebook', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

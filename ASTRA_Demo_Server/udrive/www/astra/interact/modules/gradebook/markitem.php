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
* Mark item
*
* Displays page for adding marks/comments by item
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: markitem.php,v 1.18 2007/07/30 01:57:00 glendavies Exp $
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

	$module_key	   = $_POST['module_key'];
	$item_key	  = $_POST['item_key'];
	$user_keys	 = $_POST['user_keys'];
	$comments	  = $_POST['body'];		
	$grade_key	 = $_POST['grade_key'];
	$update_all	= $_POST['update_all'];
	$update_single = $_POST['update_single'];
	$action		= $_POST['action'];	
	
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

		if ($action == 'mark_single') {
		
			$user_key = $_POST['user_key'];
			$item_key = $_POST['item_key'];			
			$message = $gradebook->modifygrade($item_key, $user_key, $_POST[$user_key.'_grade'], $_POST[$user_key.'_comments']);
											
			if ($message===true) {
		
				$message = urlencode($gradebook_strings['modify_grade_success']);
				header("Location: {$CONFIG['FULL_URL']}/modules/gradebook/spreadsheetview.php?space_key=$space_key&module_key=$module_key");

			} else {
		
				$message = $gradebook_strings['modify_grade_fail'].' - '.$message;
			
			}		
		
		} else if (!isset($update_single) || $update_all==1) {
		
			foreach($user_keys as $user_key) {
			
				$message = $gradebook->modifygrade($item_key, $user_key, $_POST[$user_key.'_grade'], $_POST[$user_key.'_comments']);
											
				if ($message===true) {
		
					$message = $gradebook_strings['all_modified'];
					
				} else {
		
					$message = $gradebook_strings['modify_grade_fail'].' - '.$message;
			
				}
				
				
			}
			
			$user_key='';
			
		} else {
					
			$message = $gradebook->modifygrade($item_key, $update_single, $_POST[$update_single.'_grade'], $_POST[$update_single.'_comments']);
											
			if ($message===true) {
		
				$message = urlencode($gradebook_strings['modify_grade_success']);
				header("Location:  {$CONFIG['FULL_URL']}/modules/gradebook/markitem.php?space_key=$space_key&module_key=$module_key&item_key=$item_key&marked_item=".$item_key.'_'.$update_single.'#'.$item_key.'_'.$update_single);
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

//get item data

$item_data = $gradebook->getItemData($item_key);

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
	
} else if ($action=='mark_single') {

	$t->set_var('ACTION', 'mark_single');
	$t->set_var('BUTTON', $general_strings['modify']);
	$t->set_var('HEADING',$gradebook_strings['item_input_heading']);				
	$t->set_block('gradebook', 'MarkMultipleBlock', 'MMBlock'); 
	$t->set_var('MMBlock','');			
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
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('COMMENTS_STRING',$gradebook_strings['comments']);
$t->set_var('MODIFY_ALL_STRING',$gradebook_strings['modify_all']);
$t->set_var('MODIFY_SINGLE_STRING',$gradebook_strings['modify_single']);
$t->set_var('GRADE_STRING',$gradebook_strings['grade']);
$t->set_var('RETURN_TO_STRING',$general_strings['return_to']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('MESSAGE',$message);

$t->set_var('HEADING_STRING',sprintf($gradebook_strings['grade_heading'],$item_data['name']));

//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
//$html = new InteractHtml();
//$html->setTextEditor($t, 0, 'body');



//now get create a grading item box for each item
$t->set_block('gradebook', 'MarkItemBlock', 'MIBlock'); 

$gradebook->displayMarkItemBoxes($item_key, $user_key, 'by_item', $marked_item);

$t->parse('CONTENTS', 'header', true); 
get_navigation();

$t->parse('CONTENTS', 'gradebook', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

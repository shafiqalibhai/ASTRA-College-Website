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
* Item input page
*
* Displays the item input/modify page
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: iteminput.php,v 1.19 2007/07/30 01:57:00 glendavies Exp $
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

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];
	$item_key   = $_GET['item_key'];
	$action 	= $_GET['action'];

	
} else {

	$module_key					= $_POST['module_key'];
	$item_key				   = $_POST['item_key'];
	$item_data['name']			= $_POST['name'];
	$item_data['description']   = $_POST['body'];
	$item_data['url']			= $_POST['url'];
	$item_data['item_status_key']		= $_POST['item_status_key'];
	$item_data['sort_order']	= $_POST['sort_order'];	
	$item_data['scale_key']		= $_POST['scale_key'];
	$item_data['due_date']		= $_POST['due_date_year'].'-'.$_POST['due_date_month'].'-'.$_POST['due_date_day'].' '.$_POST['due_date_hour'].':'.$_POST['due_date_minute'];
	$item_data['due_date_unix'] = strtotime($item_data['due_date']);	
	$item_data['maximum_score'] = $_POST['maximum_score'];
	$item_data['weighting']		= $_POST['weighting'];							
	$action 						   = $_POST['action'];	
	$submit							   = $_POST['submit'];							

}

$userlevel_key = $_SESSION['userlevel_key'];

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

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

$current_user_key = $_SESSION['current_user_key'];
$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

//see if we are adding a new entry

if ($action) {

	switch($action) {
	
		case 'add':
		
			if ($is_admin==true) {

				$errors = $gradebook->checkFormInput($item_data['name'], $item_data['weighting']);

				if (count($errors)>0) {
	
					$message = $general_strings['problem_below'];
	
				} else {
	
					$message = $gradebook->addItem($module_key, $item_data);
											
					if ($message===true) {
		
						$message = urlencode($gradebook_strings['add_success']);
						header("Location: {$CONFIG['FULL_URL']}/modules/gradebook/gradebook.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&link_key=$link_key&message=$message");
						exit;
		
					}
		
				} 
			
			}
			
		break;
		
		case 'modify':
		
			$item_data = $gradebook->getItemData($item_key);
		
		break;
		
		case 'modify2':
		
			if ($submit==$general_strings['delete']) {

				if ($is_admin==true) {

					$message = $gradebook->deleteItem($item_key);
					
					if ($message===true) {
		
						$message = urlencode($gradebook_strings['delete_success']);
						header("Location: {$CONFIG['FULL_URL']}/modules/gradebook/gradebook.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&link_key=$link_key&message=$message");
						exit;
		
					} else {
					
						$message = $gradebook_strings['delete_failure'].' - '.$message;
						
					}
							
				}			
				
			} else { 
			
				if ($is_admin==true) {

					$errors = $gradebook->checkFormInput($item_data['name'], $item_data['weighting'], $item_key);

					if (count($errors)>0) {
	
						$message = $general_strings['problem_below'];
	
					} else {
	
						$message = $gradebook->modifyItem($item_key, $item_data);
		
						if ($message===true) {
		
							$message = urlencode($gradebook_strings['modify_success']);
							header("Location: {$CONFIG['FULL_URL']}/modules/gradebook/gradebook.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&link_key=$link_key&gradebook_user_key=$gradebook_user_key&message=$message");
							exit;
		
						} else {
					
							$message = $gradebook_strings['modify_failure'].' - '.$message;
						
						}
		
					} 
			
				}
				
			}
			
		break;		

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
	'gradebook'  => 'gradebook/iteminput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($action=='' || $action=='add') {

	$t->set_var('ACTION', 'add');
	$t->set_var('BUTTON', $general_strings['add']);
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

//generate date selection menus
$due_date_menu = $dates->createDateSelect('due_date',$item_data['due_date_unix'], true);

//generate the scale menu

if (!$item_data['scale_key'] || $item_data['scale_key']=='') {
 
	$item_data['scale_key']=1;
	
}

$scale_sql = "SELECT name, scale_key FROM {$CONFIG['DB_PREFIX']}gradebook_scales WHERE (added_by_key='0' OR added_by_key='$current_user_key' OR space_key='$space_key') ORDER BY name";
$scale_menu = make_menu($scale_sql,'scale_key',$item_data['scale_key'],'1',false);
//generate status menu
$item_status_menu = $objHtml->arrayToMenu(array('1' => ucfirst($general_strings['visible']), '2' => ucfirst($general_strings['hidden']) ),'item_status_key',$item_data['item_status_key'], false, 2);
//generate the editor components

$objHtml->setTextEditor($t, $_SESSION['auto_editor'], 'body');


$name_error = sprint_error($errors['name']);
$weighting_error = sprint_error($errors['weighting']);
$t->set_var('NAME_ERROR',$name_error);
$t->set_var('WEIGHTING_ERROR',$weighting_error);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('ITEM_KEY',$item_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('ASSIGNMENT_KEY',$item_key);
$t->set_var('DUE_DATE_MENU',$due_date_menu);
$t->set_var('SCALE_MENU',$scale_menu);
$t->set_var('ITEM_STATUS_MENU',$item_status_menu);
$t->set_var('URL_STRING',$gradebook_strings['url']);
$t->set_var('DUE_DATE_STRING',$gradebook_strings['due_date']);
$t->set_var('SCALE_STRING',$gradebook_strings['scale']);
$t->set_var('MAX_SCORE_STRING',$gradebook_strings['max_score']);
$t->set_var('WEIGHTING_STRING',$gradebook_strings['weighting']);
$t->set_var('NUMERIC_ONLY_STRING',$gradebook_strings['numeric_only']);
$t->set_var('SORT_ORDER_STRING',$general_strings['sort_order']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('RETURN_TO_STRING',$general_strings['return_to']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('STATUS_STRING',$general_strings['status']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('NAME',$item_data['name']);
$t->set_var('BODY',$item_data['description']);
$t->set_var('URL',$item_data['url']);
$t->set_var('SORT_ORDER',$item_data['sort_order']);
$t->set_var('URL',$item_data['url']);
$t->set_var('MAXIMUM_SCORE',$item_data['maximum_score']);
$t->set_var('WEIGHTING',$item_data['weighting']);
$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->parse('CONTENTS', 'gradebook', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

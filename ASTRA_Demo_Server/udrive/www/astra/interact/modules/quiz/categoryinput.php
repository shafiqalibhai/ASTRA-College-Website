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
* Category input
*
* Displays a category input page. 
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: categoryinput.php,v 1.14 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/quiz_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$action		= $_GET['action'];
	$category_key = $_GET['category_key'];		
		
} else {

	$module_key		  = $_POST['module_key'];
	$category_key	 = $_POST['category_key'];
	$category_name	= $_POST['category_name'];
	$parent_key	   = $_POST['parent_key'];
	$delete_option	= $_POST['delete_option'];
	$move_to_key	  = $_POST['move_to_key'];			
	$action			  =  $_POST['action'];

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

$quiz = new InteractQuiz($space_key, $module_key, $group_key, $is_admin, $quiz_strings);

if ($action) {

	switch ($action) {
	
		case 'add':
		
			$errors = $quiz->checkFormCategory($category_name, $category_key, $parent_key);
			
			if (count($errors)==0) {
			
				$category_key = $quiz->addCategory($category_name, $parent_key);
				
				if (isset($category_key)) {
				
					header("Location: {$CONFIG['FULL_URL']}/modules/quiz/additems.php?space_key=$space_key&module_key=$module_key&category_key=$category_key");
					exit;
					
				} 
				
			}
		
		break;
		
		case 'modify':

			$category_data = $quiz->getCategoryData($category_key);
			$category_name = $category_data['name'];
			$parent_key	= $category_data['parent_key'];
			
					
		break;
		
		case 'modify2':
		
			switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
					$errors = $quiz->checkFormCategory($category_name, $parent_key);
			
					if (count($errors)==0) {
			
						$message = $quiz->modifyCategory($category_key, $category_name, $parent_key);
				
						if ($message===true) {
				
							header("Location: {$CONFIG['FULL_URL']}/modules/quiz/additems.php?space_key=$space_key&module_key=$module_key&category_key=$category_key");
							exit;
					
						} 
				
					}
					
				break;
				
				case $general_strings['delete']:
				
					if ($delete_option=='move' && (!isset($move_to_key) || $move_to_key=='')) {
										
						$message = $quiz_strings['no_move_category'];
					
					} else {
					
						$message = $quiz->deleteCategory($category_key, $delete_option, $move_to_key);
				
						if ($message===true) {
				
							header("Location: {$CONFIG['FULL_URL']}/modules/quiz/additems.php?space_key=$space_key&module_key=$module_key&category_key=$category_key");
							exit;
						
						}
					
					}
						
						 
				
				break;
				
			} //end switch($_POST['submit'])
		
		break;		
		
	} //end switch(action)
		
} //end if ($action)

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'body'	   => 'quiz/categoryinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!$action || $action=='add') {

	$button		= $general_strings['add'];
	$input_heading = $quiz_strings['category_input'];
	$action		= 'add';
	$t->set_block('body', 'DeleteOptionsBlock', 'DOBlock');
	$t->set_var('DOBlock');			
	
} else {

	$button		= $general_strings['modify'];
	$input_heading = $quiz_strings['category_input'];
	$warning=$general_strings['delete_warning'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$warning.'\')">';	$action		= 'modify2';

}

require_once('../../includes/lib/category.inc.php');
$category = new InteractCategory();

$category_sql  = "SELECT name, category_key, parent_key FROM {$CONFIG['DB_PREFIX']}qt_categories WHERE (space_key='$space_key' OR user_key='{$_SESSION['current_user_key']}') ";

$category_array = $category->getCategoryArray($category_sql);

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');

}
$html = new InteractHtml();

$parent_menu  = $html->arrayToMenu($category_array,'parent_key',$parent_key, false, 5);
$move_to_menu = $html->arrayToMenu($category_array,'move_to_key',$move_to_key, false, 5);

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('PARENT_MENU',$parent_menu);
$t->set_var('CATEGORY_KEY',isset($category_key)? $category_key : '');
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('MOVE_TO_CATEGORY',$move_to_menu);
$t->set_var('CATEGORY_NAME_STRING',$quiz_strings['category_name']);
$t->set_var('CATEGORY_PARENT_STRING',$quiz_strings['category_parent']);
$t->set_var('DELETE_OPTIONS_STRING',$quiz_strings['delete_options']);
$t->set_var('DELETE_ITEMS_STRING',$quiz_strings['delete_items']);
$t->set_var('MOVE_ITEMS_STRING',$quiz_strings['move_items']);
$t->set_var('INPUT_HEADING',$input_heading);
$t->set_var('DELETE_BUTTON',isset($delete_button) ? $delete_button : '');
$t->set_var('NAME_ERROR',isset($errors['name']) ? sprint_error($errors['name']) : '');
$t->set_var('PARENT_ERROR',isset($errors['parent']) ? sprint_error($errors['parent']) : '');
$t->set_var('CATEGORY_NAME',isset($category_name) ? $category_name : '');
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);

$t->parse('CONTENTS', 'header', true); 
get_navigation();



//now get a list of an existing items for this gradebook

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>
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
* Multichoice input
*
* Displays a multichoice question input page. 
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id $
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
	if (isset($_GET['category_key'])) {
		$item_data['category_keys'] = array($_GET['category_key']);
		$category_key			   =  $_GET['category_key'];		
	}
	$item_key = isset($_GET['item_key'])? $_GET['item_key'] : '';
		
} else {

	$module_key	   = $_POST['module_key'];
	$category_key  = $_POST['category_key'];
	$category_keys = $_POST['category_keys'];		
	$action		   = $_POST['action'];
	$item_data = array();
	foreach ($_POST as $key => $value) {
		$item_data[$key] = $value;
	}
		
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
		
			$errors = $quiz->checkFormTextAnswer($item_data);
			
			if (count($errors)==0) {
			
				$item_key = $quiz->inputTextAnswer($item_data, 'add');
				
				if ($item_key) {
					if (!empty($_POST['add_to_current']) && $_POST['add_to_current']==1) {
						$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_module_item_links(module_key, item_key) VALUES ('$module_key', '$item_key')");
					}
					header("Location: {$CONFIG['FULL_URL']}/modules/quiz/additems.php?space_key=$space_key&module_key=$module_key&category_key=$category_key");
					exit;
					
				} 
				
			}
		
		break;
		
		case 'modify':

			$item_data = $quiz->getItemData($item_key);
					
		break;
		
		case 'modify2':
		
			switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
					$errors = $quiz->checkFormTextAnswer($item_data);
			
					if (count($errors)==0) {
			
						$item_key = $quiz->inputTextAnswer($item_data, 'modify');
				
						if ($item_key) {
				
							if (!empty($_POST['add_to_current']) && $_POST['add_to_current']==1) {
								$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_module_item_links(module_key, item_key) VALUES ('$module_key', '$item_key')");
							}
							header("Location: {$CONFIG['FULL_URL']}/modules/quiz/additems.php?space_key=$space_key&module_key=$module_key&category_key=$category_key");
							exit;
					
						} 
				
					}
					
				break;
				
				case $general_strings['delete']:
				
					$message = $quiz->deleteTextAnswer($item_data);
				
					if ($message===true) {
				
						header("Location: {$CONFIG['FULL_URL']}/modules/quiz/additems.php?space_key=$space_key&module_key=$module_key&category_key=$category_key");
						exit;
					
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
	'body'	   => 'quiz/textanswerinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!$action || $action=='add') {

	$button		= $general_strings['add'];
	$input_heading = $quiz_strings['add_text_answer'];
	$action		= 'add';		
	
} else {

	$button		= $general_strings['modify'];
	$input_heading = $quiz_strings['modify_text_answer'];
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

$category_menu = $html->arrayToMenu($category_array,'category_keys[]',$item_data['category_keys'], true, 3);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('CATEGORY_MENU',$category_menu);
$t->set_var('CATEGORY_KEY',isset($category_key)? $category_key : '');
$t->set_var('ITEM_KEY',isset($item_key)? $item_key : '');
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('QUESTION_STRING',$quiz_strings['question']);

$t->set_var('CATEGORY_STRING',$quiz_strings['category']);

$t->set_var('ADD_TO_CURRENT_STRING',$quiz_strings['add_to_current']);
$t->set_var('INPUT_HEADING',$input_heading);
$t->set_var('DELETE_BUTTON',isset($delete_button) ? $delete_button : '');
$t->set_var('NAME_ERROR',isset($errors['name']) ? sprint_error($errors['name']) : '');
$t->set_var('QUESTION_ERROR',isset($errors['question']) ? sprint_error($errors['question']) : '');
$t->set_var('CATEGORY_ERROR',isset($errors['category']) ? sprint_error($errors['category']) : '');
if (isset($item_data)) {
	foreach ($item_data as $key => $value) {
		$t->set_var(strtoupper($key),$value);
	}
}
//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, false, 'question');



if (isset($item_data)) {
}


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

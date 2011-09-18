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
* @version $Id: multichoiceinput.php,v 1.25 2007/07/30 01:57:04 glendavies Exp $
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
	$type = isset($_GET['type'])?$_GET['type']:'multichoice';
	
	if (isset($_GET['category_key'])) {
	
		$item_data['category_keys'] = array($_GET['category_key']);
		$category_key			   =  $_GET['category_key'];		
	
	}
	
	$item_key = isset($_GET['item_key'])? $_GET['item_key'] : '';
		
} else {
	$type = isset($_POST['type'])?$_POST['type']:'multichoice';
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
		
			$errors = $quiz->checkFormMultiChoice($item_data);
			
			if (count($errors)==0) {
			
				$item_key = $quiz->inputMultiChoice($item_data, 'add');
				
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
			if ($item_data['response_1']==ucfirst($quiz_strings['true']) && $item_data['response_2']==ucfirst($quiz_strings['false']) && $item_data['response_3']=='') {
				$type='truefalse';	
			}
					
		break;
		
		case 'modify2':
		
			switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
					$errors = $quiz->checkFormMultiChoice($item_data);
			
					if (count($errors)==0) {
			
						$item_key = $quiz->inputMultiChoice($item_data, 'modify');
				
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
				
					$message = $quiz->deleteMultiChoice($item_data);
				
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
	'body'	   => 'quiz/multichoiceinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!$action || $action=='add') {

	$button		= $general_strings['add'];
	if ($type=='truefalse') {
		$input_heading = $quiz_strings['add_truefalse'];
	} else {
		$input_heading = $quiz_strings['add_multichoice'];
	}
	$action		= 'add';		
	
} else {

	$button		= $general_strings['modify'];
	if ($type=='truefalse') {
		$input_heading = $quiz_strings['modify_truefalse'];
	} else {
		$input_heading = $quiz_strings['modify_multichoice'];
	}
	$warning=$general_strings['delete_warning'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$warning.'\')">';	$action		= 'modify2';

}

require_once('../../includes/lib/category.inc.php');
$category = new InteractCategory();

$category_sql  = "SELECT name, category_key, parent_key FROM {$CONFIG['DB_PREFIX']}qt_categories WHERE (space_key='$space_key' OR user_key='{$_SESSION['current_user_key']}') ";

$category_array = $category->getCategoryArray($category_sql);

//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, false, 'question');

for ($i=1;$i<7;$i++) {
	$html->setTextEditor($t, false, 'response_'.$i);
	$html->setTextEditor($t, false, 'feedback_'.$i);
}

$category_menu = $html->arrayToMenu($category_array,'category_keys[]',$item_data['category_keys'], true, 3);
if ($type=='truefalse') {
	$t->set_block('body', 'SettingsBlock', 'SttngsBlock');
	$t->set_var('SttngsBlock','');
	$t->set_block('body', 'MultichoiceBlock', 'MltiChceBlock');
	$t->set_block('body', 'MultichoiceR1', 'MltiChceR1');
	$t->set_block('body', 'MultichoiceR2', 'MltiChceR2');
	$t->set_var('MltiChceBlock','');
	$t->set_var('MltiChceR1','<input name="response_1" type="hidden" id="type" value="'.ucfirst($quiz_strings['true']).'">');
	$t->set_var('MltiChceR2','<input name="response_2" type="hidden" id="type" value="'.ucfirst($quiz_strings['false']).'">');
	$t->set_var('RESPONSE_STRING_1',ucfirst($quiz_strings['true']));
	$t->set_var('RESPONSE_STRING_2',ucfirst($quiz_strings['false']));
	$t->set_var('EDITOR_BUTTONS_response_1','');
	$t->set_var('EDITOR_BUTTONS_response_2','');
} else {
	$t->set_var('RESPONSE_STRING_1',$quiz_strings['response'].' 1');
	$t->set_var('RESPONSE_STRING_2',$quiz_strings['response'].' 2');
}
$t->set_var('TYPE',$type);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('CATEGORY_MENU',$category_menu);
$t->set_var('CATEGORY_KEY',isset($category_key)? $category_key : '');
$t->set_var('ITEM_KEY',isset($item_key)? $item_key : '');
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('QUESTION_STRING',$quiz_strings['question']);
$t->set_var('RESPONSE_STRING',$quiz_strings['response']);
$t->set_var('FEEDBACK_STRING',$quiz_strings['feedback']);
$t->set_var('FEEDBACK_OPTIONS_STRING',$quiz_strings['feedback_options']);
$t->set_var('MULTIPLE_RESPONSE_STRING',$quiz_strings['multiple_response']);
$t->set_var('CORRECT_STRING',$quiz_strings['correct']);
$t->set_var('SETTINGS_STRING',$quiz_strings['settings']);
$t->set_var('SCORE_STRING',$quiz_strings['score']);
$t->set_var('ALL_CORRECT_STRING',$quiz_strings['correct_feedback']);
$t->set_var('ALL_WRONG_STRING',$quiz_strings['wrong_feedback']);
$t->set_var('CATEGORY_STRING',$quiz_strings['category']);
$t->set_var('CLEAR_STRING',$general_strings['clear']);
$t->set_var('ADD_TO_CURRENT_STRING',$quiz_strings['add_to_current']);
$t->set_var('INPUT_HEADING',$input_heading);
$t->set_var('DELETE_BUTTON',isset($delete_button) ? $delete_button : '');
$t->set_var('NAME_ERROR',isset($errors['name']) ? sprint_error($errors['name']) : '');
$t->set_var('QUESTION_ERROR',isset($errors['question']) ? sprint_error($errors['question']) : '');
$t->set_var('NO_RESPONSE_ERROR',isset($errors['response']) ? sprint_error($errors['response']) : '');
$t->set_var('CORRECT_ANSWER_ERROR',isset($errors['correct']) ? sprint_error($errors['correct']) : '');
$t->set_var('CATEGORY_ERROR',isset($errors['category']) ? sprint_error($errors['category']) : '');



//if item_data array set work through array to
//display data in form

if (isset($item_data)) {

	foreach ($item_data as $key => $value) {

		
		$t->set_var(strtoupper($key),$value);
		
		if (strpos($key, 'correct')!=false) {
	
			if ($value == 1) {
			
				$ident = explode('_',$key);
				$ident = $ident[1];
				$t->set_var($ident.'_CORRECT_CHECKED','checked');
			
			}
		
		}
  
	}
	
}

if (isset($item_data['rcardinality']) && ($item_data['rcardinality']==1 || $item_data['rcardinality']=='Multiple')) {

	$t->set_var('MULTI_CHECKED','checked');
	
}

if (isset($item_data['correct_feedback_all'])) {

	$t->set_var($item_data['correct_feedback_all'].'_ALL_CORRECT_CHECKED','checked');
	
}

if (isset($item_data['wrong_feedback_all'])) {

	$t->set_var($item_data['wrong_feedback_all'].'_ALL_WRONG_CHECKED','checked');
	
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

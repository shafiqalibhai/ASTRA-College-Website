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
* Add an item to a quiz
*
* Displays a page to select items for adding to a quiz
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: additems.php,v 1.18 2007/07/30 01:57:04 glendavies Exp $
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

	$space_key	= $_GET['space_key'];
	$module_key	= $_GET['module_key'];
	
	if (isset($_GET['category_key'])) {
	
		$category_key = $_GET['category_key'];
	
	}
		
	$action 	= $_GET['action'];

	
} else {

	$module_key	  = $_POST['module_key'];
	$category_key = $_POST['category_key'];
	$action		  = $_POST['action'];		
							

}

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

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
$quiz = new InteractQuiz($space_key, $module_key, $group_key, $is_admin, $quiz_strings);

//see if we are adding a new entry

if ($action) {

	switch($action) {
	
		case 'add_items':
		
			$item_keys = $_POST['item_keys'];
			
			foreach ($item_keys as $item_key) {
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_module_item_links(module_key, item_key) VALUES ('$module_key', '$item_key')");
			
			}

		break;
		
		case 'modify_items':
		
			$delete_link_keys = isset($_POST['delete_link_keys'])? $_POST['delete_link_keys'] : '';
			$link_keys		= isset($_POST['link_keys'])? $_POST['link_keys'] : '';
			
			if (is_array($delete_link_keys)) {
			
				$quiz->removeItems($delete_link_keys);			 
			
			}
		
			if (is_array($link_keys)) {
			
				foreach($link_keys as $value) {
				
					$sort_order = $_POST[$value.'_sort_order'];
					$score	  = $_POST[$value.'_score'];
					
					$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_module_item_links SET sort_order='$sort_order', score='$score' WHERE link_key='$value' AND module_key='$module_key'");
					
			
				}
				
			}
		
		break;

	}

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'quiz'	   => 'quiz/additems.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//first get an items already in this module
$t->set_block('quiz', 'CurrentItemsBlock', 'CIBlock');
$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}qt_item.item_key, name, sort_order, score, link_key FROM {$CONFIG['DB_PREFIX']}qt_item, {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE {$CONFIG['DB_PREFIX']}qt_item.item_key={$CONFIG['DB_PREFIX']}qt_module_item_links.item_key AND {$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$module_key' ORDER BY sort_order");

if ($rs->EOF) {

	$t->set_var('CIBlock','');
	$t->set_var('CURRENT_ITEMS_STRING',$quiz_strings['no_current_items']);
	$t->set_block('quiz', 'ModifyButtonBlock', 'MBBlock');
	$t->set_var('MBBlock','');

} else {

	$t->set_var('MODIFY_STRING',$general_strings['modify']);
	$t->set_var('REMOVE_STRING',ucfirst($general_strings['remove']));
	$t->set_var('SCORE_STRING',$quiz_strings['score']);
	$t->set_var('SORT_ORDER_STRING',$general_strings['sort_order']);
	$t->set_var('NAME_STRING',$general_strings['name']);				
 
	while (!$rs->EOF) {
	   
		$t->set_var('ITEM_KEY',$rs->fields[0]);
		$t->set_var('ITEM_NAME',$rs->fields[1]);
		$t->set_var('SORT_ORDER',$rs->fields[2]);
		$t->set_var('SCORE',$rs->fields[3]);				   
		$t->set_var('LINK_KEY',$rs->fields[4]);
		$t->parse('CIBlock', 'CurrentItemsBlock', true);
		$rs->MoveNext();
		   
	}	

}
require_once('../../includes/lib/category.inc.php');
$category = new InteractCategory();

$category_sql  = "SELECT name, category_key, parent_key FROM {$CONFIG['DB_PREFIX']}qt_categories WHERE (space_key='$space_key' OR user_key='{$_SESSION['current_user_key']}') ";

$category_array = $category->getCategoryArray($category_sql);

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');

}

$html = new InteractHtml();

$category_menu = $html->arrayToMenu($category_array,'category_key',$category_key, false,'',true,'onChange="document.categoryForm.submit()"');
$t->set_var('CATEGORY_MENU',$category_menu);
// if (!empty($_SESSION['JAVASCRIPT'])) {
// 	$t->set_block('quiz', 'SubmitButtonBlock', 'SbmitButtnBlock');
// 	$t->set_var('SbmitButtnBlock','');
// }
//check if category selected
$t->set_block('quiz', 'ListItemsBlock', 'LIBlock');

if (!$category_key) {

	$t->set_var('SELECT_ITEMS_MESSAGE',$quiz_strings['select_category']);
	$t->set_block('quiz', 'AddButtonBlock', 'ABBlock');
	$t->set_var('ABBlock','');	   	   		
	$t->set_block('quiz', 'EditCategoryBlock', 'ECBlock');
	$t->set_var('ECBlock','');	   	   		
	$t->set_block('quiz', 'AddItemsBlock', 'AddItmsBlock');
	$t->set_var('AddItmsBlock','');	
} else {

	//show create new item link
   
	$t->set_var('CREATE_NEW_STRING',$quiz_strings['create_new_item']); 
	$t->set_var('MULTICHOICE_STRING',$quiz_strings['multichoice']);
	$t->set_var('TEXT_ANSWER_STRING',$quiz_strings['text_answer']);
	$t->set_var('TRUEFALSE_STRING',ucfirst($quiz_strings['true']).'/'.ucfirst($quiz_strings['false']));
	//category is selected so get all items for this category
   
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}qt_item.item_key, name, response_type FROM {$CONFIG['DB_PREFIX']}qt_item, {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE {$CONFIG['DB_PREFIX']}qt_item.item_key={$CONFIG['DB_PREFIX']}qt_category_item_links.item_key AND {$CONFIG['DB_PREFIX']}qt_category_item_links.category_key='$category_key' ORDER BY name");

	if ($rs->EOF) {
   
		$t->set_var('LIBlock','');
		$t->set_var('SELECT_ITEMS_MESSAGE',$quiz_strings['no_items']);
		$t->set_block('quiz', 'AddButtonBlock', 'ABBlock');
		$t->set_var('ABBlock','');	   	   	   
   
	} else {
   
		$t->set_var('SELECT_ITEMS_MESSAGE',$quiz_strings['current_category_items']);
		
		while (!$rs->EOF) {
	   
			$t->set_var('ITEM_KEY',$rs->fields[0]);
			if ($rs->fields[2]=='str') {
				$t->set_var('INPUT_SCRIPT','textanswerinput');					
			} else {
				$t->set_var('INPUT_SCRIPT','multichoiceinput');				
			}
			$t->set_var('ITEM_NAME',$rs->fields[1]);
			$t->parse('LIBlock', 'ListItemsBlock', true);
			$rs->MoveNext();
		   
		}
	   
	}

}

$t->set_var('ADD_ITEM_HEADING', sprintf($quiz_strings['add_item_heading'], $page_details['module_name']));
$t->set_var('SELECT_STRING',$general_strings['select']);
$t->set_var('ADD_STRING',$general_strings['add']);
$t->set_var('FINISHED_STRING',$general_strings['finished']);
$t->set_var('CURRENT_ITEMS_STRING',$quiz_strings['current_items']);
$t->set_var('SELECT_ITEMS_STRING',$quiz_strings['select_items']);
$t->set_var('ADD_CATEGORY_STRING',$quiz_strings['add_category']);
$t->set_var('EDIT_CATEGORY_STRING',$quiz_strings['edit_category']);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('CATEGORY_KEY',$category_key);
$t->parse('CONTENTS', 'header', true); 

get_navigation();
$t->parse('CONTENTS', 'quiz', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

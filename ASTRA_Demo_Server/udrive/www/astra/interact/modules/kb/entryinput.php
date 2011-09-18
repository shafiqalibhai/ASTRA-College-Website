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
* Entry input
*
* Displays entry input page 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: entryinput.php,v 1.24 2007/07/30 01:57:03 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/kb_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$template_key	= isset($_GET['template_key'])? $_GET['template_key'] : '';
	$entry_key		= isset($_GET['entry_key'])? $_GET['entry_key'] : '';
	$module_key		= $_GET['module_key'];
	$category_key	= isset($_GET['category_key'])? $_GET['category_key']: '' ;
	$action			= $_GET['action'];
		
} else {

	$action	 		= $_POST['action'];
	$space_key		= $_POST['space_key'];
	$module_key		= $_POST['module_key'];
	$template_key	= $_POST['template_key'];
	$category_key	= isset($_POST['category_key'])? $_POST['category_key']: '' ;
	$entry_key		= $_POST['entry_key'];		
	$entry_data 	= array();
	
	foreach ($_POST as $key =>$value) {
	
		$entry_data[$key] = $value; 
	
	}
	
}

$userlevel_key = $_SESSION['userlevel_key'];
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
//check_variables(true,true,true);

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

//check to see if user is logged in. 
if (!isset($_SESSION['current_user_key']) || $_SESSION['current_user_key']=='') {
		$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");	

}
$objKb = new InteractKB($space_key, $module_key, $group_key, $is_admin, $kb_strings);

if (isset($entry_key) && $entry_key!='') {

	$added_by = $objKb->getEntryData($entry_key);
	if ($is_admin!=true && $added_by['added_by_key']!=$_SESSION['current_user_key']) {
			$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
			header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");	
	}
}


if (isset($action) && $action!='') {

	switch ($action) {
	
		case 'add_entry':
		
			$message = $objKb->addEntry($entry_data);
		
		break;
		
		case 'modify':

			$template_data = $objKb->getTemplateData($template_key);
			$template_name = $template_data['name'];
			$parent_key	= $template_data['parent_key'];
			$status_key = $objKb->getEntrystatus_key($entry_key);				
			
					
		break;
		
		case 'modify2':
		
			switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
					$message = urlencode($objKb->modifyEntry($entry_data));
				
					header("Location: {$CONFIG['FULL_URL']}/modules/kb/entry.php?space_key=$space_key&module_key=$module_key&entry_key=$entry_key&message=$message");
					exit;
					
				break;
				
				case $general_strings['delete']:
				
					$objKb->deleteEntry($entry_key);
					$message = urlencode($kb_strings['entry_deleted']);
					header("Location: {$CONFIG['FULL_URL']}/modules/kb/kb.php?space_key=$space_key&module_key=$module_key&message=$message");
					exit;
				
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
	'body'	   => 'kb/entryinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}

if (!is_object($objHtml)) {

	$objHtml = new InteractHtml();
	
}

//if only one template for this kb get its template_key

if ($objKb->countTemplates($module_key)==1 && (!isset($template_key) || $template_key=='')) {
	
	$template_key = $objKb->gettemplate_key($module_key);

}

if (!isset($template_key) || $template_key=='') {


	$t->set_block('body', 'TemplateListBlock', 'TLSBlock');
	
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_templates.template_key, name, description FROM {$CONFIG['DB_PREFIX']}kb_module_template_links, {$CONFIG['DB_PREFIX']}kb_templates WHERE {$CONFIG['DB_PREFIX']}kb_templates.template_key={$CONFIG['DB_PREFIX']}kb_module_template_links.template_key AND (module_key='$module_key') ORDER BY name");
	
	while (!$rs->EOF) {
	
		$t->set_var('TEMPLATE_KEY',$rs->fields[0]);
		$t->set_var('TEMPLATE_NAME',$rs->fields[1]);
		$t->set_var('TEMPLATE_DESCRIPTION',$rs->fields[2]);	
		$t->parse('TLSBlock', 'TemplateListBlock', true); 		
		$rs->MoveNext();	
		
	} 
	
	$t->set_var('INPUT_HEADING',$kb_strings['add_entry']);
	$t->set_var('AVAILABLE_TEMPLATES_STRING',$kb_strings['available_templates']);
	$t->set_var('CATEGORY_KEY',$category_key);
	$t->set_var('SELECT_STRING',$general_strings['select']);
	$t->set_block('body', 'TemplateFormBlock', 'TFBlock');
	$t->set_var('TFBlock','');	

	
} else {

	$t->set_block('body', 'ChooseTemplatesBlock', 'CTSBlock');
	$t->set_var('CTSBlock','');
	$t->set_var('CATEGORY_KEY',$category_key);
	$category_keys=array();
	$category_keys[0] = isset($category_key)? $category_key:'';	
	$t->set_block('body', 'HtmlEditorBlock', 'HTEBlock');
	$t->set_block('body', 'TemplateFieldBlock', 'TFBlock');
	$template_data = $objKb->getTemplateData($template_key);
	$t->set_var('NEW_ENTRY_INPUT_STRING',sprintf($kb_strings['new_entry_input'],$template_data['name']));

	if (!class_exists(InteractCategory)) {
	
		require_once('../../includes/lib/category.inc.php');
		
	} 
	
	if (!is_object($objCategory)) {
	
		$objCategory = new InteractCategory();
		
	}

	$t->set_var('SUBMIT_BUTTON',$general_strings['add']);
	$objKb->getTemplateInputForm($template_key, $t, $entry_key, $action);

	if ($action=='modify' || $action=='modify2') {

		$category_keys = $objKb->getEntryCategories($entry_key);
		$t->set_var('INPUT_HEADING',$kb_strings['modify_entry']);
		$t->set_var('SUBMIT_BUTTON',$general_strings['modify']);
		$t->set_var('ACTION','modify2');
		$t->set_var('ENTRY_KEY',$entry_key);		
		$t->set_var('DELETE_BUTTON', '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')"  />');
		$t->set_var('POPUP_STRING',$kb_strings['popup_url']);
		
		$t->set_var('POPUP_URL', $CONFIG['PATH'].$CONFIG['DIRECT_PATH'].'kb/'.$space_key.'/'.$module_key.'/'.$entry_key);

	} else {


		$t->set_var('SUBMIT_BUTTON',$general_strings['add']);
		$t->set_var('INPUT_HEADING',$kb_strings['add_entry']);
		$t->set_var('ACTION','add_entry');

	}

	$category_sql  = "SELECT name, category_key, parent_key FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE (module_key='$module_key')";

	$category_array = $objCategory->getCategoryArray($category_sql);
	
	$category_menu  = $objHtml->arrayToMenu($category_array,'category_keys[]',$category_keys, true, 5);
	$t->set_var('CATEGORY_MENU',$category_menu);
	$status_array = array(1 => $general_strings['draft'], 2 => $general_strings['published']);

	if(empty($status_key)) $status_key=2;
	$status_menu  = $objHtml->arrayToMenu($status_array,'status_key',$status_key, false, 2);
	$t->set_var('STATUS_MENU',$status_menu);
	$t->set_var('STATUS_STRING',$general_strings['status']);		

}

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('CATEGORY_STRING',$general_strings['category']);
$t->set_var('TEMPLATE_KEY',isset($template_key)? $template_key : '');
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('TEMPLATE_MENU',$template_menu);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('TEMPLATE_NAME_STRING',$kb_strings['template_name']);
$t->set_var('ADD_NEW_STRING',$kb_strings['add_new_template']);
$t->set_var('MODIFY_TEMPLATE_STRING',$kb_strings['modify_template']);
$t->set_var('MODIFY_STRING',$general_strings['modify']);
$t->set_var('CURRENT_TEMPLATES_STRING',$kb_strings['current_templates']);
$t->set_var('KNOWLEDGEBASE_TEMPLATES_STRING',$kb_strings['kb_templates']);
$t->set_var('ADD_TEMPLATES_STRING',$kb_strings['add_templates']		);
$t->set_var('ADD_STRING',$general_strings['add']);
$t->set_var('REMOVE_STRING',$kb_strings['remove_templates']);
//$t->set_var('NAME_ERROR',isset($errors['name']) ? sprint_error($errors['name']) : '');
$t->set_var('TEMPLATE_NAME',isset($template_name) ? $template_name : '');
$t->set_var('BUTTON',$button);

$t->set_strings('body', $kb_strings,null,$errors);

$t->parse('CONTENTS', 'header', true); 
get_navigation();
//generate the editor components


if (isset($category_key) && $category_key!='') {

	$objKb->getTrail($category_key, false, $kb_trail);
	
}
$t->set_var('KB_TRAIL',$kb_trail);

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>
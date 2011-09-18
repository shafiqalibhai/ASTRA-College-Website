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
* Template input
*
* Displays a template input page. 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: templateinput.php,v 1.18 2007/07/30 01:57:03 glendavies Exp $
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

	$module_key		= $_GET['module_key'];
	$action			= $_GET['action'];
	$template_key 	= $_GET['template_key'];
	$referer		= isset($_GET['referer'])? $_GET['referer']: '';		
		
} else {

	$action	 	=  $_POST['action'];
	$space_key	=  $_POST['space_key'];
	$module_key	=  $_POST['module_key'];	
	$referer	= isset($_POST['referer'])? $_POST['referer']: '';	
		
	foreach ($_POST as $key =>$value) {
	
		$template_data[$key] = $value; 
	
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

if ($is_admin!=true) {

	$message = urlencode($general_strings['no_edit_rights']);
	header("Location: {$CONFIG['FULL_URL']}/modules/kb/kb.php?space_key=$space_key&module_key=$module_key&message=$message");
	exit;

}

$objKb = new InteractKB($space_key, $module_key, $group_key, $is_admin, $kb_strings);

if (isset($action) && $action!='') {

	switch ($action) {
	
		case 'add':
		
			$errors = $objKb->checkFormTemplate($template_data);
			
			if (count($errors)==0) {
			
				$template_key = $objKb->addTemplate($template_data);
				
			}
		
		break;
		
		case 'modify':

			$template_data = $objKb->getTemplateData($template_key);
			$template_name = $template_data['name'];
			$parent_key	= $template_data['parent_key'];
			
					
		break;
		
		case 'add_to_kb':

			$template_keys = isset($template_data['template_keys'])? $template_data['template_keys'] : '';
			$message = $objKb->addTemplatesToKb($template_keys, $module_key);
					
		break;
		
		case 'remove_from_kb':

			$template_keys = isset($template_data['template_keys'])? $template_data['template_keys'] : '';
			$remove_entries = isset($template_data['remove_entries'])? $template_data['remove_entries'] : '';
			$message = $objKb->removeTemplatesFromKb($template_keys, $module_key, $remove_entries);
					
		break;
		
		case 'modify2':
		
			switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
					$errors = $objKb->checkFormTemplate($template_name, $parent_key);
			
					if (count($errors)==0) {
			
						$message = $objKb->modifyTemplate($template_key, $template_name, $parent_key);
				
						if ($message===true) {
				
							header("Location: {$CONFIG['FULL_URL']}/modules/kb/kb.php?space_key=$space_key&module_key=$module_key&template_key=$template_key");
							exit;
					
						} 
				
					}
					
				break;
				
				case $general_strings['delete']:
				
					if ($delete_option=='move' && (!isset($move_to_key) || $move_to_key=='')) {
										
						$message = $kb_strings['no_move_template'];
					
					} else {
					
						$message = $obKb->deleteTemplate($template_key, $delete_option, $move_to_key);
				
						if ($message===true) {
				
							header("Location: {$CONFIG['FULL_URL']}/modules/kb/kb.php?space_key=$space_key&module_key=$module_key&template_key=$template_key");
							exit;
						
						}
					
					}
						
						 
				
				break;
				
			} //end switch($_POST['submit'])
		
		break;		
		
	} //end switch(action)
		
} //end if ($action)

//check refere, if server admin then show server admin navigation

if ($referer=='admin') {

	$navigation_template = 'admin/adminnavigation.ihtml';
	$back_link = '/admin/index.php';

} else {

	$navigation_template = 'navigation.ihtml';
	$back_link = '/modules/kb/admin.php';

}
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => $navigation_template,
	'body'	   => 'kb/templateinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (isset($referer) && $referer=='admin') {

	$t->set_block('body', 'KBTemplatesBlock', 'KBTBlock');
	$t->set_var('KBTBlock', '');	

}

if (!$action || $action=='add') {

	$button		= $general_strings['add'];
	$action		= 'add';
	$t->set_block('body', 'DeleteOptionsBlock', 'DOBlock');
	$t->set_var('DOBlock','');			
	
} else {

	$button			= $general_strings['modify'];
	$warning		= $general_strings['delete_warning'];
	$delete_button	= '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$warning.'\')">';	$action		= 'modify2';

}

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}

if (!is_object($objHtml)) {

	$objHtml = new InteractHtml();
	
}

$template_array = $objKb->getTemplateArray($space_key, 'all', $module_key, $referer, $_SESSION['userlevel_key']);
$modify_template_menu  = $objHtml->arrayToMenu($template_array,'template_key',$template_key, false, 5);

$template_array = $objKb->getTemplateArray($space_key, 'not_selected', $module_key);
$add_template_menu  = $objHtml->arrayToMenu($template_array,'template_keys[]',$template_key, true, 5);

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('BACK_LINK',$back_link);
$t->set_var('SUMMARY_FIELDS_STRING',$kb_strings['summary_fields']);
$t->set_var('INPUT_HEADING',$kb_strings['add_templates']		);
$t->set_var('REMOVE_ENTRIES_STRING',$kb_strings['remove_entries']);
$t->set_var('TEMPLATE_KEY',isset($template_key)? $template_key : '');
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('MODIFY_TEMPLATE_MENU',$modify_template_menu);
$t->set_var('ADD_TEMPLATE_MENU',$add_template_menu);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('TEMPLATE_NAME_STRING',$kb_strings['template_name']);
$t->set_var('ADD_NEW_STRING',$kb_strings['add_new_template']);
$t->set_var('MODIFY_TEMPLATE_STRING',$kb_strings['modify_template']);
$t->set_var('MODIFY_STRING',$general_strings['modify']);
$t->set_var('SELECT_STRING',$kb_strings['select_modify']);
$t->set_var('AVAILABLE_TEMPLATES_STRING',$kb_strings['available_templates']);
$t->set_var('CURRENT_TEMPLATES_STRING',$kb_strings['current_templates']);
$t->set_var('KNOWLEDGEBASE_TEMPLATES_STRING',$kb_strings['kb_templates']);
$t->set_var('ADD_TEMPLATES_STRING',$kb_strings['add_templates']		);
$t->set_var('ADD_STRING',$general_strings['add']);
$t->set_var('REFERER',$referer);
$t->set_var('REMOVE_STRING',$kb_strings['remove_templates']);
$t->set_var('DELETE_BUTTON',isset($delete_button) ? $delete_button : '');
$t->set_var('NAME_ERROR',isset($errors['name']) ? sprint_error($errors['name']) : '');
$t->set_var('TEMPLATE_NAME',isset($template_name) ? $template_name : '');
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);

$t->parse('CONTENTS', 'header', true); 

if ($referer=='admin') {
	require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');
	set_common_admin_vars($admin_strings['languages'], $message);
	admin_navigation();
	
} else {

	get_navigation();

}


//now get a list of an existing templates for this kb

$t->set_block('body', 'CurrentTemplatesBlock', 'CTSBlock');

$rs = $CONN->Execute("SELECT name, {$CONFIG['DB_PREFIX']}kb_templates.template_key FROM {$CONFIG['DB_PREFIX']}kb_templates, {$CONFIG['DB_PREFIX']}kb_module_template_links WHERE  {$CONFIG['DB_PREFIX']}kb_templates.template_key={$CONFIG['DB_PREFIX']}kb_module_template_links.template_key AND module_key='$module_key'");

if ($rs->EOF) {

	$t->set_var('CTSBlock', $kb_strings['no_templates_selected']);
	$t->set_var('REMOVE_BUTTON_CLASS', 'hidden');	

} else {

	while (!$rs->EOF) {
	
		$t->set_var('TEMPLATE_NAME', $rs->fields[0]);
		$t->set_var('TEMPLATE_KEY', $rs->fields[1]);		
		$t->parse('CTSBlock', 'CurrentTemplatesBlock', true);
		$rs->MoveNext();
	
	}


}

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>
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
* Template modify
*
* Displays a page to modify template details 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: templatemodify.php,v 1.16 2007/07/30 01:57:03 glendavies Exp $
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
		
} else {

	$action	 		=  $_POST['action'];
	$space_key		=  $_POST['space_key'];
	$module_key		=  $_POST['module_key'];
	$template_key	=  $_POST['template_key'];	
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
	
		case 'add_field':
		
			$errors = $objKb->checkFormTemplate($template_data);
			
			if (count($errors)==0) {
			
				$message = $objKb->addField($template_data);
				
			}
		
		break;
		
		case 'modify_fields':

				$message = $objKb->modifyFields($template_data);
					
		break;
		
		case 'modify_template':

	
		   switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
	   				$errors = $objKb->checkFormTemplate($template_data);
			
					if (count($errors)==0) {
			
						$message = $objKb->modifyTemplate($template_data);
				
					}					
				
				break;
				
				case $general_strings['delete']:
				
					$message = $objKb->deleteTemplate($template_key);
				
					if ($message===true) {
				
						header("Location: {$CONFIG['FULL_URL']}/modules/kb/templateinput.php?space_key=$space_key&module_key=$module_key");
						exit;
						
					}
									   				
				break;
				
			} //end switch($_POST['submit'])
		
		break;		
		
	} //end switch(action)
		
} //end if ($action)

//check refere, if server admin then show server admin navigation

if ($referer=='admin') {

	$navigation_template = 'admin/adminnavigation.ihtml';

} else {

	$navigation_template = 'navigation.ihtml';

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => $navigation_template,
	'body'	   => 'kb/templatemodify.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$template_data = $objKb->getTemplateData($template_key);
$t->set_var('WARNING',$general_strings['delete_warning']);
$t->set_var('DELETE_TEMPLATE_WARNING',$kb_strings['delete_template_warning']);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('REFERER',$referer);
$t->set_var('INPUT_HEADING',$kb_strings['modify_template']);
$t->set_var('TEMPLATE_KEY', $template_key);
$t->set_var('NAME',$template_data['name']);
$t->set_var('DESCRIPTION',$template_data['description']);
$t->set_var('SUMMARY_FIELDS',$template_data['summary_fields']);
$t->set_var('SUMMARY_FIELDS_STRING',$kb_strings['summary_fields']);
$t->set_var('REMOVE_FIELD_WARNING',$kb_strings['remove_field_warning']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('DELETE_STRING',$general_strings['delete']);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('TEMPLATE_NAME_STRING',$kb_strings['template_name']);
$t->set_var('MODIFY_TEMPLATE_STRING',$kb_strings['modify_template']);
$t->set_var('MODIFY_STRING',$general_strings['modify']);
$t->set_var('TEMPLATE_DETAILS_STRING',$kb_strings['template_details']);
$t->set_var('ADD_FIELD_STRING',$kb_strings['add_fields']);
$t->set_var('TEXT_FIELD_STRING',$kb_strings['text_field']);
$t->set_var('URL_FIELD_STRING',$kb_strings['url_field']);
$t->set_var('FILE_FIELD_STRING',$kb_strings['file_field']);
$t->set_var('TYPE_STRING',$kb_strings['field_type']);
$t->set_var('CURRENT_FIELDS_STRING',$kb_strings['current_fields']);
$t->set_var('TEMPLATE_FIELDS_STRING',$kb_strings['template_fields']);
$t->set_var('DISPLAY_ORDER_STRING',$kb_strings['display_order']);
$t->set_var('ADD_STRING',$general_strings['add']);
$t->set_var('REMOVE_STRING',$kb_strings['remove_templates']);
$t->set_var('LINES_STRING',$kb_strings['lines']);
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

//now get a list of an existing fields for this template

$t->set_block('body', 'CurrentFieldsBlock', 'CFSBlock');

$rs = $CONN->Execute("SELECT field_key, name, description, display_order, type_key, number_of_lines FROM {$CONFIG['DB_PREFIX']}kb_fields WHERE template_key='$template_key' ORDER By display_order");

if ($rs->EOF) {

	$t->set_var('CFSBlock', $kb_strings['no_fields_selected']);

} else {

	while (!$rs->EOF) {
	
		$t->set_var('FIELD_KEY', $rs->fields[0]);
		$t->set_var('FIELD_NAME', $rs->fields[1]);
		$t->set_var('FIELD_DESCRIPTION', $rs->fields[2]);
		$t->set_var('DISPLAY_ORDER', $rs->fields[3]);
		
		switch ($rs->fields[4]) {
		
			case 1:
			
				$t->set_var('TYPE', $kb_strings['text_field']);
			
			break;
							
			case 2:
			
				$t->set_var('TYPE', $kb_strings['url_field']);
				
			break;
			
			case 3:

				$t->set_var('TYPE', $kb_strings['file_field']);
			
			break;
			
		}
		
		$t->set_var('LINES', $rs->fields[5]);				
		$t->parse('CFSBlock', 'CurrentFieldsBlock', true);
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
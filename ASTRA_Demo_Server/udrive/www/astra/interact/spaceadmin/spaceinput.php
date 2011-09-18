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
* Space input page
*
* Displays a page for adding modifying a new space
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: spaceinput.php,v 1.20 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$referer	= isset($_GET['referer'])? $_GET['referer'] : '';
	$space_key  = isset($_GET['space_key'])? $_GET['space_key'] : '';
	$parent_key = isset($_GET['parent_key'])? $_GET['parent_key'] : '';		
	$action	 = isset($_GET['action'])? $_GET['action'] : '';			

} else {

	$referer   		= isset($_POST['referer'])? $_POST['referer'] : '';
	$space_key 		= isset($_POST['space_key'])? $_POST['space_key'] : '';
	$parent_key		= isset($_POST['parent_key'])? $_POST['parent_key'] : '';	
	$action			= isset($_POST['action'])? $_POST['action'] : '';
	$submit			= isset($_POST['submit'])? $_POST['submit'] : '';
	$delete_subs	= isset($_POST['delete_subs'])? $_POST['delete_subs'] : '';	
	
	$space_data = array();
	
	foreach($_POST as $key => $value ) {
	
		$space_data[$key] = $value;
	
	}			

}

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

if ($_SESSION['userlevel_key']!=1 && $accesslevel_key!=1 && $accesslevel_key!=3) {

	$message = urlencode($general_strings['not_allowed']);
	header("Location: {$CONFIG['FULL_URL']}/index.php?message=$message");
	exit;

}

$group_access = $access_levels['groups'];   

if (!class_exists(InteractSpaceAdmin)) {

	require_once('lib.inc.php');
		
	
}
$objSpaceAdmin = new InteractSpaceAdmin();
if (isset($action) && $action!='') {

	switch($action) {

		case add:
	
			$errors = $objSpaceAdmin->checkInputFormData($space_data);

			//if there are no errors then add the data
		
			if(count($errors) == 0) {
			
				$message = $objSpaceAdmin->addSpace($space_data);
			
				if ($message=='true') {
			
					if ($referer=='home') {
					
						$message = urlencode(sprintf($space_strings['space_added'], $general_strings['space_text']));
						header("Location: {$CONFIG['FULL_URL']}/index.php?message=$message");
						exit;
					
					} else if ($referer=='admin') {
					
						$message = urlencode(sprintf($space_strings['space_added'], $general_strings['space_text']));					
						Header ("Location: {$CONFIG['FULL_URL']}/spaceadmin/spaceinput.php?referer=admin&message=$message");
						exit;
						
					} else {
					
						$message = urlencode(sprintf($space_strings['space_added'], $general_strings['space_text']));					
						header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$parent_key&message=$message");
						
						exit;
						
					}
				
				}
		 
			 } else {
			  
				  $message = $general_strings['problem_below'];
		 
			 }
		 
		 break;
		 
		 case modify:
	
			 $space_data   = $objSpaceAdmin->getSpaceData($space_key);
		 	 $action	   = 'modify2';
			 $button	   = $general_strings['modify'];
			 $title		= sprintf($space_strings['modify_space'],$general_strings['space_text']);
			 $delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')"/>';
	   
		break;   
   
		 case modify2:
		 
			 switch($submit) {
			 
				 case $general_strings['modify']:
				 
					 $errors = $objSpaceAdmin->checkInputFormData($space_data);

					 //if there are no errors then add the data
		 
					 if(count($errors) == 0) {
			
						 $message = $objSpaceAdmin->modifySpace($space_data);
			
						 if ($message===true) {
				
							 $message = urlencode(sprintf($space_strings['space_modified'], $general_strings['space_text']));
							 header("Location: {$CONFIG['FULL_URL']}/spaceadmin/admin.php?space_key=$space_key&message=$message");
							 exit;
		  
						 }
		 
					 } else {
			 
						 $message = $general_strings['problem_below'];
						 $action	   = 'modify2';
						 $button	   = $general_strings['modify'];
						 $title		= sprintf($space_strings['modify_space'],$general_strings['space_text']);
						 $delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')"/>';
						 
						 if (!isset($space_data['parent_keys']) || !is_array($space_data['parent_keys'])) {
						 
							 $space_data['parent_keys'] = array();
						 
						 }
		 
					 }
		 
				 break;

				 case $general_strings['delete']:
			 
					 $objSpaceAdmin->deleteSpace($space_key, $delete_subs);
					 $message = urlencode(sprintf($space_strings['space_deleted'], $general_strings['space_text']));
					 header("Location: {$CONFIG['FULL_URL']}/index.php?message=$message");
		 
				 break;
				 
			}
			
			break;	 

	 } //end switch $action	   

} else {

	$space_key  = isset($_GET['space_key'])? $_GET['space_key'] : '';
	$referer	= isset($_GET['referer'])? $_GET['referer'] : '';		

}

//check refere, if server admin then show server admin navigation

if ($referer=='admin') {

	$navigation_template = 'admin/adminnavigation.ihtml';

} else {

	$navigation_template = 'navigation.ihtml';

}
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		=> 'header.ihtml',
	'navigation' 	=> $navigation_template,
	'form'	  		=> 'spaceadmin/spaceinput.ihtml',
	'footer'	 	=> 'footer.ihtml'));

// get page details for titles and breadcrumb navigation
if (isset($space_key) && $space_key!='') {

	$current_space_key = $space_key;

} else if (isset($parent_key) && $parent_key!=''){

	$current_space_key = $parent_key;


} else {

	$current_space_key = '';

}
$page_details=get_page_details($current_space_key);

set_common_template_vars($current_space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!isset($action) || $action=='' || $action=='add') {

	$action = 'add';
	$space_data['access_level_key'] = 1;
	$space_data['visibility_key'] = 1;	
	
	if (isset($page_details['space_name']) && $page_details['space_name']!='') {
	
		$title = sprintf($space_strings['add_subspace_to'], $general_strings['space_text'], $page_details['space_name']);
	
	} else {
		
		$title = sprintf($space_strings['add_space'], $general_strings['space_text']);
	
	}
	
	$button	   = $general_strings['add'];
	$space_data['show_members'] = 1;
	
	$space_data['parent_keys'] = array();
	
	if (isset($parent_key) && $parent_key!='') {
	
		$space_data['parent_keys'][0]=$parent_key;
	
	} 
	$t->set_block('form', 'DeleteOptionsBlock', 'DOBlock');
	$t->set_var('DOBlock','');

} else {

	$t->set_var('DELETE_SUB_STRING',sprintf($space_strings['delete_sub_spaces'],sprintf($space_strings['sub_sites'],$general_strings['space_plural'])));

}
$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
$t->set_var('MHBlock','');

if (!class_exists('InteractHtml')) {

	require_once('../includes/lib/html.inc.php');
	
}

$html = new InteractHtml();

$show_members_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'show_members',$space_data['show_members']);
$combine_names_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'combine_names',$space_data['combine_names']);

if (!isset($space_key) || $space_key=='') {
 
	$t->set_var('BREADCRUMBS','');
	$t->set_var('PAGE_TITLE',$title);
	$t->set_var('SPACE_TITLE','');
	$t->set_var("MAKE_MEMBER","");	
	
}

$t->parse('CONTENTS', 'header', true);

$t->set_var('NAME_ERROR',isset($errors['name'])? sprint_error($errors['name']):'');
$t->set_var('DESCRIPTION_ERROR',isset($errors['description'])? sprint_error($errors['description']):'');
$t->set_var('COPY_ERROR',isset($errors['copy_space'])? sprint_error($errors['copy_space']):'');
$t->set_var('CODE_ERROR',isset($errors['code'])? sprint_error($errors['code']):'');
$t->set_var('SHORT_NAME',isset($space_data['short_name'])? $space_data['short_name']: '');
$t->set_var('NAME',isset($space_data['name'])? $space_data['name']: '');
$t->set_var('DESCRIPTION',isset($space_data['description'])? $space_data['description']: '');
$t->set_var('ACCESS_LEVEL_'.$space_data['access_level_key'].'_CHECKED','checked');
$t->set_var('VISIBILITY_'.$space_data['visibility_key'].'_CHECKED','checked');
$t->set_var('SHORT_DATE_MENU',$short_date_menu);
$t->set_var('LONG_DATE_MENU',$long_date_menu);
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('STATUS_MENU',$status_menu);
$t->set_var('TYPE_MENU',$type_menu);
$t->set_var('TEMPLATE_MENU',$template_menu);
$t->set_var('CATEGORY_MENU',$category_menu);
$t->set_var('ACCESS_CODE',isset($space_data['access_code'])? $space_data['access_code']: '');
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('COPY_space_short_name',$copy_space_short_name);
$t->set_var('SORT_ORDER',$space_data['sort_order']);
$t->set_var('REFERER',$referer);
$t->set_var('SHOW_MEMBERS_MENU',$show_members_menu);
$t->set_var('SPACE_NAME_STRING',$space_strings['name']);
$t->set_var('SHORT_NAME_STRING',$space_strings['short_name']);
$t->set_var('EXPLAIN_SHORT_NAME_STRING',$space_strings['explain_short_name']);
$t->set_var('COMBINE_NAMES_STRING',sprintf($space_strings['combine_names'], $general_strings['space_text'], $general_strings['space_text']));
$t->set_var('COMBINE_NAMES_MENU',$combine_names_menu);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('EXPLAIN_DESCRIPTION_STRING',$space_strings['explain_description']);
$t->set_var('ACCESS_STRING',$space_strings['access']);
$t->set_var('VISIBILITY_STRING',$space_strings['visibility']);
$t->set_var('OPEN_STRING',$space_strings['open_logged_in']);
$t->set_var('OPEN_PUBLIC_STRING',$space_strings['open_to_public']);
$t->set_var('RESTRICTED_STRING',$space_strings['restrict_to_members']);
$t->set_var('VISIBLE_STRING',$space_strings['visible']);
$t->set_var('HIDDEN_STRING',$space_strings['hidden']);
$t->set_var('OPTIONAL_SETTINGS_STRING',$general_strings['optional_settings']);
$t->set_var('COPY_STRING',sprintf($space_strings['copy'], $general_strings['space_text']));
$t->set_var('COPY_CODE_STRING',sprintf($space_strings['copy_code'], $general_strings['space_text']));
$t->set_var('CODE_STRING',$space_strings['code']);
$t->set_var('CODE',$space_data['code']);
$t->set_var('SHOW_MEMBERS_STRING',$space_strings['show_members']);
$t->set_var('SORT_ORDER_STRING',$general_strings['sort_order']);
$t->set_var('EXPLAIN_SORT_ORDER_STRING',$space_strings['sort_explain']);
$t->set_var('SPACE_INPUT_HEADING',$title);
$t->set_var('DELETE_BUTTON',isset($delete_button)? $delete_button : '');
$t->set_var('PARENT_SPACE_STRING',sprintf($space_strings['parent_space'], $general_strings['space_text']));
$t->set_var('PARENT_SPACE_MENU',$objSpaceAdmin->getSpaceParentMenu(0, $parent_menu, $space_data['parent_keys'], $space_key) );
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);

if ($referer=='admin') {

	admin_navigation();
	
} else {

	get_navigation();

}
	
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

$CONN->Close();
exit;




?>

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
* Space module
*
* Inputs/modifies/deletes a new space
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: space_input.php,v 1.34 2007/07/23 03:39:34 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

//check we have the required variables

if ($_POST['space_key']) {
	
	$space_key	= isset($_POST['space_key'])?$_POST['space_key']:'';
	$module_key	= isset($_POST['module_key'])?$_POST['module_key']:'';
	$type_key	= isset($_POST['type_key'])?$_POST['type_key']:''; 
	$action		= isset($_POST['action'])?$_POST['action']:'';
	if(empty($module_key)) {
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.module_key, link_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key'");

		while (!$rs->EOF) {
			$parent_module_key = $rs->fields[0];
			$link_key = $rs->fields[1];
			$parent_space_key = $rs->fields[2];
			$rs->MoveNext();
		}
		$rs->Close();
	}
	
} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];
	$type_key   = isset($_GET['type_key'])?$_GET['type_key']:'0';
		
}

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels   = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access	= $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
if(empty($module_key)) {
	$module_key = $parent_module_key;
}
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object

$modules = new InteractModules();
$modules->set_module_type('space');

//find out what action we need to take

if (isset($_POST['submit'])) {


	foreach($_POST as $key => $value ) {
	
		$space_data[$key] = $value;

	}
	if(!isset($space_data['skin_key'])) {
		$space_data['skin_key']=$CONFIG['DEFAULT_SKIN_KEY'];
	}
	if (!isset($objSpaceAdmin) || !is_object($objSpaceAdmin)) {
		if (!class_exists('InteractSpaceAdmin')) {
			require_once('../../spaceadmin/lib.inc.php');
		}
		$objSpaceAdmin = new InteractSpaceAdmin();
	}
	switch($_POST['submit']) {

		
		//if we are adding a new space form input needs to be checked 

		case $general_strings['add']:
		
			$errors = $objSpaceAdmin->checkInputFormData($space_data);

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('space');

			//if the add was successful return the browser to space home or parent quiz
				if ($message=='true') {
					 
					$modules->return_to_parent('space','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {
				
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
	
			if(count($errors) == 0) {

				 $message = $modules->modify_module('space',$can_edit_module);

				//return browser to space home or parent quiz

				   if ($message=='true') {

					  $modules->return_to_parent('space','modified');
					exit;

				}  

			} else {
			
				$message = $message = $general_strings['problem_below'];
			 
			}
			
			break;
			
		case $general_strings['delete']:
		
			$space_key	 = $_POST['space_key'];
			$module_key	= $_POST['module_key'];
			$parent_key	= $_POST['parent_key'];
			$group_key	 = $_POST['group_key'];
			$link_key	  = $_POST['link_key'];
									
			header ("Location: {$CONFIG['FULL_URL']}/modules/general/moduledelete.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key");
			exit;			
			
		default:
			$message = $general_strings['no_action'];
			break;
			
	} //end switch($_POST[submit])			

} //end isset($_POST[submit])

$def_skin=$CONFIG['DEFAULT_SKIN_KEY'];
if ($_GET['action']=='modify' || $_POST['submit']=='Modify/Delete') {

	$space_data  = $modules->get_module_data('space', $module_key, $link_key);
	$def_skin=$space_data['skin_key'];

	$kbaccess_level_key = $module_data['access_level_key'];
 
} //end if ($_GET[action]=="modify")		  

if (!isset($_GET['action']) && !isset($_POST['action'])) {
	$action = 'add';
	$title = $space_strings['add_space'];
	$button = $general_strings['add'];
}
if ($_GET['action']=='modify' || $_POST['submit']==$general_strings['modify']||$_POST['submit']=='Modify/Delete') {
	$action = 'modify2';
	$button = $general_strings['modify'];
	if ($space_data['space_key']!=$CONFIG['DEFAULT_SPACE_KEY'] && !empty($space_data['space_key'])) {
		$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';
	}
}

//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

$name_error = sprint_error($errors['name']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'spaces/space_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

$t->set_block('general', 'AccessLevelBlock', 'AccessLvlBlock');
//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);

$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'space');

if (!isset($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once('../../includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}

if ($CONFIG['ADMIN_SET_SKIN']==1) {
	if (!isset($objSkins)) {
		if (!class_exists('InteractSkins')) {
			require_once('../../skins/lib.inc.php');
		}
		$objSkins = new InteractSkins();
	}
	//create list of alt stylesheets
	$skins_array = $objSkins->getSkinArray();
// 	$alt_style_sheets='';
// 	foreach($skins_array as $key => $value) {
// 		$skin_data = $objSkins->getSkinData($key);
// 		$alt_style_sheets.='<link rel="alternate stylesheet" type="text/css" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key='.$key.'" title="'.$key.'" />';
// 	}
// 	$t->set_var('META_TAGS',$alt_style_sheets);
	
$t->set_var('SKIN_MENU',$objHtml->arrayToMenu($skins_array,'skin_key',$def_skin,false,'',false,'onChange="changeStyleSheet(this.value)"'));
	
	$referer = urlencode($CONFIG['PATH'].'/modules/space/space_input.php?space_key='.$space_key.'&module_key='.$module_key.'&link_key='.$link_key.'&action=modify');
	$t->set_var('ADD_LINK','<a href="../../skins/skin_select.php?space_key='.$space_key.'&referer='.$referer.'">'.$general_strings['add'].'/'.$general_strings['modify'].'</a>');
} else {
	$t->set_block('form', 'SkinBlock', 'SKBlock');
	$t->set_var('SKBlock','');
}

$objHtml->setTextEditor($t, false, 'description');
$objHtml->setTextEditor($t, false, 'welcome_message');
$show_members_menu = $objHtml->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'show_members',isset($space_data['show_members'])?$space_data['show_members']:'1');
$show_spacemap_menu = $objHtml->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'spacemap',isset($space_data['spacemap'])?$space_data['spacemap']:'1');
$combine_names_menu = $objHtml->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'combine_names',$space_data['combine_names']);
$email_admins_menu = $objHtml->arrayToMenu(array('true' => $general_strings['yes'], 'false' => $general_strings['no']),'new_user_alert',$space_data['new_user_alert']);

$t->set_var('NAME_ERROR',isset($errors['name'])? sprint_error($errors['name']):'');
$t->set_var('DESCRIPTION_ERROR',isset($errors['description'])? sprint_error($errors['description']):'');
$t->set_var('COPY_ERROR',isset($errors['copy_space'])? sprint_error($errors['copy_space']):'');
$t->set_var('CODE_ERROR',isset($errors['code'])? sprint_error($errors['code']):'');
$t->set_var('SHORT_NAME',isset($space_data['short_name'])? $space_data['short_name']: '');
$t->set_var('NAME',isset($space_data['name'])? $space_data['name']: '');
$t->set_var('DESCRIPTION',isset($space_data['description'])? $space_data['description']: '');
$t->set_var('WELCOME_MESSAGE_DATA',isset($space_data['welcome_message'])? $space_data['welcome_message']: '');
$t->set_var('ACCESS_LEVEL_'.$space_data['access_level_key'].'_CHECKED','checked');
$t->set_var('VISIBILITY_'.$space_data['visibility_key'].'_CHECKED','checked');
$t->set_var('SHORT_DATE_MENU',$short_date_menu);
$t->set_var('LONG_DATE_MENU',$long_date_menu);
$t->set_var('TYPE_MENU',$type_menu);
$t->set_var('TEMPLATE_MENU',$template_menu);
$t->set_var('CATEGORY_MENU',$category_menu);
$t->set_var('ACCESS_CODE',isset($space_data['access_code'])? $space_data['access_code']: '');
$t->set_var('ALT_HOME',isset($space_data['alt_home'])? $space_data['alt_home']: '');
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('COPY_space_short_name',$copy_space_short_name);
$t->set_var('SORT_ORDER',$space_data['sort_order']);
$t->set_var('REFERER',$referer);
$t->set_var('SHOW_MEMBERS_MENU',$show_members_menu);
$t->set_var('SHOW_SPACEMAP_MENU',$show_spacemap_menu);
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
$t->set_var('OPTIONAL_STRING',$general_strings['optional']);
$t->set_var('COPY_STRING',sprintf($space_strings['copy'], $general_strings['space_text']));
$t->set_var('COPY_CODE_STRING',sprintf($space_strings['copy_code'], $general_strings['space_text']));
$t->set_var('CODE_STRING',$space_strings['code']);
$t->set_var('CODE',$space_data['code']);
$t->set_var('SHOW_MEMBERS_STRING',$space_strings['show_members']);
$t->set_var('ALT_HOME_STRING',$space_strings['alt_home']);
$t->set_var('SHOW_SPACEMAP_STRING',$space_strings['show_spacemap']);
$t->set_var('SORT_ORDER_STRING',$general_strings['sort_order']);
$t->set_var('SKIN_STRING',$general_strings['skin']);
$t->set_var('EXPLAIN_SORT_ORDER_STRING',$space_strings['sort_explain']);
$t->set_var('EMAIL_ADMINS_STRING',sprintf($space_strings['email_admins'],$general_strings['space_text']));
$t->set_var('EMAIL_ADMINS_MENU',$email_admins_menu);
$t->set_var('TYPE_KEY',$type_key);
$t->set_var('DELETE_BUTTON',isset($delete_button)? $delete_button : '');

$t->set_var('SKIN_KEY',$def_skin);

$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();

$objHtml->setTextEditor($t, false, 'body');

$access_level_menu = $objHtml->arrayToMenu(array('1' => $kb_strings['access_level_1'], '2' => $kb_strings['access_level_2']),'kbaccess_level_key', $kbaccess_level_key, false, 2);

//create instance of date object for date functions
if (isset($type_key) && $type_key==1) {
	$t->set_block('form', 'FullSettingsBlock', 'FullSettBlock');
	$t->set_var('FullSettBlock','');
	$t->set_var('SPACE_INPUT_HEADING','Add a Portfolio Space');
	$t->set_var('PORTFOLIO_TEMPLATE_STRING',$space_strings['portfolio_template']);
	$t->set_var('COPY_TEMPLATE',$space_strings['copy_template']);
	//create template menu
	
	$sql = "SELECT name,code FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND  {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}space_user_links.user_key='{$_SESSION['current_user_key']}' AND {$CONFIG['DB_PREFIX']}spaces.type_key='1'";
	
	$t->set_var('PORTFOLIO_TEMPLATE_MENU',make_menu($sql,'copy_space_code','',1,false,true));
	
} else {
	$t->set_block('form', 'PortfolioTemplateBlock', 'PortfolioTempBlock');
	$t->set_var('PortfolioTempBlock','');
	$t->set_var('SPACE_INPUT_HEADING',$title);
	$t->parse('GENERAL_SETTINGS', 'general', true);
	
	
}
$t->set_strings('form',  $space_strings, '', '');
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;


/**
* Check form input   
* 
*  
* @return $errors
*/
function check_form_input() 
{

// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($_POST)){

		$_POST[$key] = trim($value);

	}

 

	
} //end check_form_input
		

?>
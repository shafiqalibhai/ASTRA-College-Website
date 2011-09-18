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
* Add a urltag
*
* Displays page for adding a tag to any page in the system
*
* @package urlTags
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2005 
* @version $Id: urltaginput.php,v 1.14 2007/07/18 05:17:45 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/tag_strings.inc.php');

if (!is_object($objurlTags)) {
	if (!class_exists('InteracturlTags')) {
		require_once('lib.inc.php');
	}
	$objurlTags = new InteracturlTags();
}

//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key				= $_GET['module_key'];
	$return_url				= $_GET['tag_url'];
	$tag_data['tag_url']	= $_GET['tag_url'];
	$tag_data['url_key']	= $_GET['url_key'];
	$external_tag			= isset($_GET['external_tag'])? $_GET['external_tag']: '';
	$referer				= isset($_GET['referer'])? $_GET['referer']: '';
	$action					= $_GET['action'];		

} else {

	$module_key						= $_POST['module_key'];
	$action							= $_POST['action'];
	$submit							= $_POST['submit'];
	$referer						= isset($_POST['referer'])? $_POST['referer']: '';		
	$return_url						= $_POST['tag_url'];
	$tag_data['external_tag']		= $_POST['external_tag'];
	$tag_data['external_url']		= isset($_POST['external_url'])? $_POST['external_url']:'';	
	$tag_data['tag_url']			= $_POST['tag_url'];
	$tag_data['heading']			= $_POST['heading'];
	$tag_data['category_keys']		= $_POST['category_keys'];
	$tag_data['note']				= $_POST['body'];
	$tag_data['added_for']			= $_POST['added_for'];
	$tag_data['url_key']			= $_POST['url_key'];			
	$tag_data['selected_user_key']	= $_POST['selected_user_key'];
	$tag_data['added_for_key']		= $_POST['added_for_key'];
	$tag_data['added_by_key']		= $_POST['added_by_key'];		
	$tag_data['tags']				= $_POST['tag_list'];
	
	
}

$userlevel_key	= $_SESSION['userlevel_key'];
$current_user_key = $_SESSION['current_user_key'];

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

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

//see if we are adding a new entry
if(!isset($objTags) || !is_object($objTags)) {
	if (!class_exists('InteractTags')){
		require_once $CONFIG['BASE_PATH'].'/includes/lib/tags.inc.php';
	}
	$objTags = new InteractTags();
}
if ($action) {

	switch ($action) {
	
		case add:
		
			$errors  = $objurlTags->checkFormInput($tag_data['heading'], $tag_data['added_for'], $tag_data['selected_user_key'], $tag_strings);
			
			if (count($errors)==0) {
			
				$message = $objurlTags->addurlTag($tag_data, $current_user_key, $space_key, $page_details['group_key']);
											
				if ($message===true) {
		
					$message = $tag_strings['add_success'];
					$return_url = urldecode($return_url);
					header("Location: {$CONFIG['SERVER_URL']}$return_url?message=$message");

				} else {
		
					$message = $tag_strings['add_fail'].' - '.$message;
			
				}
				
			}
			
		break;
		
		case modify:
		
		   $tag_data = $objurlTags->geturlTagData($tag_data['url_key']);
		   
		   //get any existing tags
			$existing_tags = $objTags->getTags('','','',$tag_data['url_key']);
			if (is_array($existing_tags)) {
				$count = count($existing_tags);
				$tag_data['tags'] = '';
				for ($i=0;$i<$count;$i++) {
					if ($i==$count-1) {
						$tag_data['tags'] .= $existing_tags[$i]['text'];
					} else {
						$tag_data['tags'] .= $existing_tags[$i]['text'].', ';					
					}
				}
			}
		   if ($objurlTags->checkurlTagEditRights($tag_data['url_key'], $tag_data['added_for_key'], $tag_data['added_by_key'],  $current_user_key, $accesslevel_key, $group_accesslevel)!=true) {

			   $message = urlencode($module_strings['no_admin_rights']);
			   header("Location: {$CONFIG['SERVER_URL']}/spaces/space.php?space_key=$space_key&message=$message");
			   exit;
		   
		   } 
		   
		break;
		
		case modify2:
		
			switch ($submit) {
			
				case $general_strings['delete']:
				
					if ($objurlTags->checkurlTagEditRights($tag_data['url_key'], $tag_data['added_for_key'], $tag_data['added_by_key'], $current_user_key, $accesslevel_key, $group_accesslevel)!=true) {

					   $message = urlencode($module_strings['no_admin_rights']);
					   header("Location: {$CONFIG['SERVER_URL']}/spaces/space.php?space_key=$space_key&message=$message");
					   exit;
		   
					} else {
					
						if ($objurlTags->deleteurlTag($tag_data['url_key'])==true) {
						
							$message = urlencode($tag_strings['delete_success']);
							
							if (isset($referer) && $referer=='tag_page') {
							
								$return_url = $CONFIG['PATH'].'/urltags/urltags.php?';
								
							} else {
							
								$return_url = $CONFIG['PATH'].urldecode($return_url);
							
							}
							
							header("Location: {$CONFIG['SERVER_URL']}$return_url&message=$message");
							exit;
							
						} else {
						
							$message = $general_strings['problem_below'];
							
						}
						
					}
				
				break;
				
				case $general_strings['modify']:
				
					
					if ($objurlTags->checkurlTagEditRights($tag_data['url_key'], $tag_data['added_for_key'], $tag_data['added_by_key'],  $current_user_key, $accesslevel_key, $group_accesslevel)!=true) {

					   $message = urlencode($module_strings['no_admin_rights']);
					   header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
					   exit;
		   
					} else {
					
						$errors  = $objurlTags->checkFormInput($tag_data['heading'], $tag_data['added_for'], $tag_data['selected_user_key'], $tag_strings);
						if (count($errors)==0) {
						
							if ($objurlTags->modifyurlTag($tag_data, $current_user_key)==true) {
						
								$message = urlencode($tag_strings['modify_success']);
								
								if (isset($referer) && $referer=='tag_page') {
							
									$return_url = $CONFIG['PATH'].'/urltags/urltags.php?';
								
								} else {
							
									$return_url = $CONFIG['PATH'].urldecode($return_url);
							
								}
								
								header("Location: {$CONFIG['SERVER_URL']}$return_url&message=$message");
								exit;
							
							} else {
						
								$message = $general_strings['problem_below'];
							
							}
							
						}
						
					}
				
				break;
				
			}
			
		break;	

	}		
	
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'taginput'   => 'urltags/urltaginput.ihtml',
	'footer'	 => 'footer.ihtml'

));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($external_tag!='1' && strpos($tag_data['tag_url'], 'http://')===false ) {

	$t->set_block('taginput', 'ExternalTagBlock', 'EXTBlock');
	$t->set_var('EXTBlock','');
	
} else {

	$t->set_var('URL_STRING',$tag_strings['url']);
	$t->set_var('EXTERNAL_URL',$tag_data['tag_url']);
	$t->set_block('taginput', 'ExternallinkBlock', 'EXTBlock');
	$t->set_var('EXTBlock','');	
	
}

if (!$action || $action=='add') {

	$button  = $general_strings['add'];				
	$action  = 'add';
	$heading = $tag_strings['add_tag'];
	$t->set_var('EXTERNAL_URL','');
	$t->set_var('SELF_CHECKED', 'checked');	
	
} else{

	$button = $general_strings['modify'];				
	$action = 'modify2';
	$heading = $tag_strings['modify_tag']; 
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')">';	
 
}

if ($added_for==$_SESSION['current_user_key'] || $added_for=='' || $added_for==1) {
	
	$t->set_var('SELF_CHECKED', 'checked');
		
} else if ($added_for==0) {

	$t->set_var('ADMINS_CHECKED', 'checked');	
	
} else if ($added_for==-1) {

	$t->set_var('ALL_CHECKED', 'checked');	
	
} else {
	
	$t->set_var('SELECTED_CHECKED', 'checked');
	$selected_user_key = $added_for;	
	
}

//get the usermenu
$user_sql = $objurlTags->getUserSql($space_key, $page_details['group_key']);

$users_menu = make_menu($user_sql,"selected_user_key",$selected_user_key,"3");


$body_error = sprint_error($errors['note']);
$selected_user_error = sprint_error($errors['selected_user']);



$t->set_var('BODY_ERROR',$body_error);
$t->set_var('SELECTED_USER_ERROR',$selected_user_error);
$t->set_var('EXTERNAL_TAG',$external_tag);
$t->set_var('RETURN_URL',$return_url);
$t->set_var('REFERER',$referer);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('TAG_URL',urlencode($tag_data['tag_url']));
$t->set_var('ADDED_FOR_KEY',$tag_data['added_for_key']);
$t->set_var('ADDED_BY_KEY',$tag_data['added_by_key']);
$t->set_var('BODY',$tag_data['note']);
$t->set_var('TAG_HEADING',$tag_data['heading']);
$t->set_var('ACTION',$action);
$t->set_var('URL_KEY',$tag_data['url_key']);
$t->set_var('DELETE_BUTTON',$delete_button);
$t->set_var('USERS_MENU',$users_menu);
$t->set_var('MEMBERS_STRING',sprintf($tag_strings['all_members'], $general_strings['space_text']));
$t->set_var('BUTTON',$button);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('MESSAGE',$message);
$t->set_var('OPTIONAL_SETTINGS_STRING',$general_strings['optional_settings']);

//generate the editor components

if (!class_exists('InteractHtml')) {
	require_once('../includes/lib/html.inc.php');
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'body');
//now get a list of existing tags to choose from

$tag_array = $objTags->getTags('',$_SESSION['current_user_key']);
$count = count($tag_array);
$tag_list = '';
for ($i=0;$i<$count;$i++) {
	$escaped_tag_text = addslashes($tag_array[$i]['text']);
	$tag_list .= '<a href="javascript:selectTag(\'tag_list\',\''.$escaped_tag_text.'\')" class="tagSelect">'.$tag_array[$i]['text'].'</a> ';
}
$t->set_var('TAG_LIST',$tag_list);

$t->set_var('HEADING_STRING',$heading);
$t->set_strings('taginput',  $tag_strings, $tag_data, $errors);
$t->parse('CONTENTS', 'header', true); 
get_navigation();

$t->parse('CONTENTS', 'taginput', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>
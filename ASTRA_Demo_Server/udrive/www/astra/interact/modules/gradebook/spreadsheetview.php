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
* Spreadsheet view
*
* Displays spreadsheet view of a gradbook
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: spreadsheetview.php,v 1.13 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/gradebook_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key			= $_GET['module_key'];
	$group_key			= $_GET['group_key'];
	$comments			= isset($_GET['comments'])?$_GET['comments']:0;
	
} else {

	$module_key	= $_POST['module_key'];
	$item_key   = $_POST['item_key'];
	$user_key   = $_POST['user_key'];
	$comments   = $_POST['body'];		
	$grade_key  = $_POST['grade_key'];
	
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

if ($is_admin==false) {

		$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	
}

$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

//see if we are modifying entries

if ($_SERVER['REQUEST_METHOD']=='POST') {

	if ($is_admin==true) {

		foreach ($_POST as $key => $value) {
		
			if (strpos($key, 'rade_it')!=false) {
		
				preg_match("/(item_key_)([0-9]*)/i",
	$key, $matches);
				$item_key1 = $matches[2];
				
				preg_match("/(user_key_)([0-9]*)/i",
	$key, $matches);
				$user_key1 = $matches[2];
		   
				$message = $gradebook->modifygrade($item_key1, $user_key1, $value, '', 'grade_only');
			
			} else  if (strpos($key, 'omment_it')!=false) {
		
				preg_match("/(item_key_)([0-9]*)/i",
	$key, $matches);
				$item_key1 = $matches[2];
				
				preg_match("/(user_key_)([0-9]*)/i",
	$key, $matches);
				$user_key1 = $matches[2];
		   
				$message = $gradebook->modifygrade($item_key1, $user_key1, '', $value, 'comment_only');
			
			}
			
											
			if ($message===true) {
		
				$message = $gradebook_strings['modify_grade_success'];

			} else {
		
				$message = $gradebook_strings['modify_grade_fail'].' - '.$message;
			
			}
		
		}
			
	}

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'gradebook'  => 'gradebook/spreadsheetview.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('HEADING_STRING',$gradebook_strings['spreadsheet_view']);
$t->set_var('EXPORT STRING',$gradebook_strings['export']);
$t->set_var('EXCEL_STRING',$gradebook_strings['excel']);
$t->set_var('TAB_DELIMITED_STRING',$gradebook_strings['tab_delimited']);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('BUTTON',$general_strings['modify']);
$t->set_var('RETURN_TO_STRING',$general_strings['return_to']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('EXPORT_COMMENTS',$comments);
$t->parse('CONTENTS', 'header', true); 
get_navigation();

$t->set_var('SPREADSHEET',$gradebook->getSpreadsheetView($comments));
$t->set_var('COMMENTS_STRING',($comments==0)?$gradebook_strings['include_comments']:$gradebook_strings['hide_comments']);	
$t->set_var('COMMENTS',($comments==0)?1:0);	
$t->parse('CONTENTS', 'gradebook', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

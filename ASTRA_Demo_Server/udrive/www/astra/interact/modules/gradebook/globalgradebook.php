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
* gradebook homepage
*
* Displays a gradebook start page. 
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: globalgradebook.php,v 1.13 2007/07/30 01:57:00 glendavies Exp $
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

	$sort_by = $_GET['sort_by']; 
	
} else {

 
	
}

$userlevel_key = $_SESSION['userlevel_key'];
$space_key = $CONFIG['DEFAULT_SPACE_KEY'];
//check to see if user is logged in. 
authenticate_home();
$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'gradebook'  => 'gradebook/globalgradebook.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('TOP_BREADCRUMBS','');
$t->set_var('YOUR_LINKS_STRING',$general_strings['your_links']);
$t->set_var('SEARCH_STRING',$general_strings['search']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('HEADING_STRING',$general_strings['global_gradebook']);
$t->set_var('DUE_DATE_STRING',$gradebook_strings['due_date']);
$t->set_var('ADD_ITEM_STRING',$gradebook_strings['item_input_heading']);
$t->set_var('YOUR_LINKS_STRING',sprintf($general_strings['your_links'],$general_strings['space_plural']));
$t->parse('CONTENTS', 'header', true); 
get_navigation();

if (!$sort_by || $sort_by=='course') {

	$view_by_string = $gradebook_strings['by_due_date'];
	$new_sort_by = 'due_date';
	$sort_by='course';
	
} else {

	$view_by_string = $gradebook_strings['by_course'];
	$new_sort_by = 'course';
	
}

$t->set_var('VIEW_BY_STRING',$view_by_string);
$t->set_var('SORT_BY',$new_sort_by);

//now get a list of an existing items for this gradebook
$t->set_block('gradebook', 'BreakBlock', 'BBlock'); 
$t->set_block('gradebook', 'ItemListBlock', 'ILBlock'); 
$t->set_var('GRADE_STRING',$gradebook_strings['grade']);

// find out what groups and spaces user is a member of

if (!class_exists('InteractUser')) {

	require_once('../../includes/lib/user.inc.php');
	
}

$user = new InteractUser();

$groups_data  = $user->getGroupsData($_SESSION['current_user_key']);
$groups_sql   = $groups_data['groups_sql'];
$spaces_data  = $user->getSpacesData($_SESSION['current_user_key']);
$spaces_sql   = $spaces_data['spaces_sql'];

$gradebook->displayGlobalItemList($module_key, $spaces_sql, $groups_sql, $sort_by);
$t->parse('CONTENTS', 'gradebook', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

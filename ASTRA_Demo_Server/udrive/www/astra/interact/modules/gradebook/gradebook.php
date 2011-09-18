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
* @version $Id: gradebook.php,v 1.12 2007/07/30 01:57:00 glendavies Exp $
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

$module_key= $_GET['module_key'];
$group_key= $_GET['group_key'];

} else {

$module_key= $_POST['module_key'];
$group_key= $_POST['group_key'];

}

$userlevel_key = $_SESSION['userlevel_key'];

$space_key = get_space_key();
$link_key = get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

//if user is not and admin, and forum is closed refer to users own journal entries


require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

'header' => 'header.ihtml',
'navigation' => 'navigation.ihtml',
'gradebook'  => 'gradebook/gradebook.ihtml',
'footer' => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('DUE_DATE_STRING',$gradebook_strings['due_date']);
$t->set_var('ADD_ITEM_STRING',$gradebook_strings['item_input_heading']);
$t->parse('CONTENTS', 'header', true); 
get_navigation();

//display admin links if user is an admin
if ($is_admin==true) {

$gradebook_admin_links = "- <a href=\"markuser.php?space_key=$space_key&module_key=$module_key\">".$gradebook_strings['by_user']."</a> - <a href=\"spreadsheetview.php?space_key=$space_key&module_key=$module_key\">".$gradebook_strings['spreadsheet_view']."</a> - <a href=\"scaleinput.php?space_key=$space_key&module_key=$module_key\">".$gradebook_strings['custom_scales'].'</a>';
$t->set_var('GRADEBOOK_ADMIN_LINKS',$gradebook_admin_links);

} else {

$t->set_block('gradebook', 'AddItemBlock', 'AIBlock');
$t->set_var('AIBlock','');

}

//now get a list of an existing items for this gradebook
$t->set_block('gradebook', 'ItemListBlock', 'ALBlock'); 
$gradebook->displayBriefItemList($module_key);
$t->parse('CONTENTS', 'gradebook', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();   
exit;
?>

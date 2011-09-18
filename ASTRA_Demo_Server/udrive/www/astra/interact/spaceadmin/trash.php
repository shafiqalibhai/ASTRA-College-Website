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
* Trash
*
* Displays recently deleted items and allows them to be restored 
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: trash.php,v 1.14 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$space_key = $_GET['space_key'];
	
} else {

	$space_key   = $_POST['space_key'];
	$link_key	= $_POST['link_key'];
	$submit	  = $_POST['submit'];

}
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables
check_variables(true,false);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 
$page_details=get_page_details($space_key);


if ($submit) {

 	if ($link_key!='') {
	
		require_once('../includes/modules.inc.php');

		$modules = new InteractModules();
		  
		$message = $modules->restore_link($link_key);
  
				
	} else {
	
		$message = sprintf($space_strings['restore_error'], $general_strings['module_text']);

	}

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'spaceadmin/trash.ihtml',
	'footer'		  => 'footer.ihtml'
));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$sql = "SELECT link_key,{$CONFIG['DB_PREFIX']}spaces.code, {$CONFIG['DB_PREFIX']}spaces.name, {$CONFIG['DB_PREFIX']}modules.name FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key and {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}module_space_links.space_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='4' AND {$CONFIG['DB_PREFIX']}module_space_links.modified_by_key='$current_user_key'";

$rs = $CONN->Execute($sql); 

if ($rs->EOF) {

	$t->set_block('form', 'FormBlock', 'FBlock'); 
	$t->set_var('FBlock', '');
	$message = $space_strings['no_deletes'];

} else {

	$t->set_block('form', 'DeletedModulesBlock', 'DBlock'); 
	
	while (!$rs->EOF) {

		$link_key = $rs->fields[0];
		$deleted_module = "<strong>{$rs->fields[1]} - {$rs->fields[2]}</strong><br />{$rs->fields[3]}<br />";
		$t->set_var('LINK_KEY',$link_key);
		$t->set_var('DELETED_MODULE',$deleted_module);
		$t->Parse('DBlock', 'DeletedModulesBlock', true);	
		$rs->MoveNext();
	 
	}

	$rs->Close();
	
}

$t->parse('CONTENTS', 'header', true);
get_navigation();
$t->set_var('DELETED_ITEMS',$deleted_items);
$t->set_var('SPACE_KEY',$space_key);

$t->set_var('MODULE_TEXT',$general_strings['module_text']);
$t->set_var('TRASH_STRING',$space_strings['trash']);
$t->set_var('DELETED_ITEMS_STRING',$space_strings['recent_deletes']);
$t->set_var('RESTORE_SELECTED_STRING',$space_strings['restore_selected']);
$t->parse('CONTENTS', form, true);
$t->parse('CONTENTS', footer, true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
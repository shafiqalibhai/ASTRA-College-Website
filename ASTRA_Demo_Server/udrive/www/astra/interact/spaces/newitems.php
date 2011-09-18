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
* New posts 
*
* Display new posts in a users spaces 
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: newitems.php,v 1.10 2007/01/07 22:25:30 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

require_once('../modules/forum/forum.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');


$days = $_GET['days'];
$space_key  = isset($_GET['space_key'])? $_GET['space_key'] : '';
$current_user_key = $_SESSION['current_user_key'];
$userlevel_key	= $_SESSION['userlevel_key'];


//check to see if user is logged in. If not refer to Login page.
if (isset($space_key) && $space_key!='') {

	$access_levels = authenticate();
	
} else {

	$access_levels = authenticate_home();

}
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];   

//if they have not logged in before set their last login to today
if ($_SESSION['last_use']>0) {

	$last_use = $_SESSION['last_use'];
	
} else {

	$last_use = date('Y-m-d H:i:s');
	
}
$current_user_key=$_SESSION['current_user_key'];

// find out what groups and spaces user is a member of
if (!class_exists('InteractUser')) {

	require_once('../includes/lib/user.inc.php');
	
}
if (!is_object($objectUser)) {

	$objUser = new InteractUser();
	
}

$groups_data  = $objUser->getGroupsData($_SESSION['current_user_key']);
$groups_sql   = $groups_data['groups_sql'];
$group_access = $groups_data['groups_array'];

//get required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'body'			=> 'spaces/newitems.ihtml',
	'footer'		  => 'footer.ihtml'
));

$page_details=get_page_details($space_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('PAGE_TITLE',$general_strings['new_items']);
$t->set_var('SPACE_TITLE',$general_strings['new_items']);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('NEW_ITEMS_HEADING',$general_strings['new_items']);

if (!isset($space_key) || $space_key=='') {
 
	$t->set_var('BREADCRUMBS','');
	$t->set_var('PAGE_TITLE',$general_strings['news']);
	$t->set_var('SPACE_TITLE','');
	$t->set_var('MAKE_MEMBER','');
	$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
	$t->set_var('MHBlock','');
	$t->set_block('body', 'SpacenameBlock', 'SPNBlock');
	$t->set_var('SPNBlock','');


} 

$t->parse('CONTENTS', 'header', true);

get_navigation();
if (isset($space_key) && $space_key!='' && $space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {

	$space_sql = "AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key'";
	
} 
$sql = "
SELECT DISTINCT 
	{$CONFIG['DB_PREFIX']}module_space_links.module_key, 
	{$CONFIG['DB_PREFIX']}module_space_links.space_key, 
	{$CONFIG['DB_PREFIX']}modules.name,
	{$CONFIG['DB_PREFIX']}modules.type_code
FROM  
	{$CONFIG['DB_PREFIX']}modules, 
	{$CONFIG['DB_PREFIX']}module_space_links,
	{$CONFIG['DB_PREFIX']}space_user_links 
WHERE 
	{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key 
	AND
	{$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
	AND 
	({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key')
	AND 
	({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
	OR 
	{$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) 	 
	AND
	({$CONFIG['DB_PREFIX']}module_space_links.status_key='1')
	AND
	({$CONFIG['DB_PREFIX']}modules.type_code!='heading')
	$space_sql 
	AND
	{$CONFIG['DB_PREFIX']}modules.date_added>'$last_use'
ORDER BY 
	{$CONFIG['DB_PREFIX']}module_space_links.date_added DESC";

$rs = $CONN->Execute($sql);

$t->set_block('body', 'NewItemsBlock', 'NIBlock');

while (!$rs->EOF) {
		
	$t->set_var('MODULE_KEY', $rs->fields[0]);
	$t->set_var('SPACE_KEY', $rs->fields[1]);
	$t->set_var('NAME', $rs->fields[2]);
	$t->set_var('CODE', $rs->fields[3]);			
	$t->parse('NIBlock', 'NewItemsBlock', true);	  
	$rs->MoveNext();
		
}

$rs->Close();

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

$CONN->Close();
exit;

?>

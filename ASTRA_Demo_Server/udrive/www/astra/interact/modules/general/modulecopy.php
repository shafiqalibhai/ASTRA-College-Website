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
* Copy module
*
* Displays page for copying an existing module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: modulecopy.php,v 1.20 2007/07/18 22:05:35 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');
require_once('../../includes/modules.inc.php');
$modules = new InteractModules();		
$space_key 	= get_space_key();

if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$parent_key	= $_GET['parent_key'];
	$group_key	= $_GET['group_key'];
	$module_key	= $_GET['module_key'];
	$space_key	= $_GET['space_key'];
	$link_key	= $_GET['link_key'];		

} else {
	
	if (isset($_POST['module_to_copy_select']) && $_POST['module_to_copy_select']!='') {
		$module_to_copy_key = $_POST['module_to_copy_select'];
	} else {
		$module_to_copy_key	= $_POST['module_to_copy_key'];
	}
	$parent_key			= $_POST['parent_key'];
	$group_key			= $_POST['group_key'];
	$link_key			= $_POST['link_key'];	
	$action				= $_POST['action'];	
 	
}

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels   = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access	= $access_levels['groups'];
$message='';

if ($action=='copy') {

	
	//first check to make sure they aren't trying to copy module down within itself
	//and that user is allowed to copy this module
	
	//first see if it is a space module being copied
	
	$module_type = $CONN->GetOne("SELECT type_code FROM  {$CONFIG['DB_PREFIX']}modules WHERE  {$CONFIG['DB_PREFIX']}modules.module_key='$module_to_copy_key'");
	
	check_is_below($parent_key,$module_to_copy_key, $space_key, $module_type);
	$can_copy = check_module_copy_rights($module_to_copy_key);

	
	

	if ($is_below==true) {
		
		$message = sprintf($module_strings['not_within'],$general_strings['module_text']);
		
	} else if ($can_copy!=true) {
			
		$message = sprintf($module_strings['no_copy_rights'],$general_strings['module_text']);
		
	} else {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key='$module_to_copy_key'";

		$rs=$CONN->SelectLimit($sql,1);

		if ($rs->EOF) {

			$message = sprintf($module_strings['no_such_module'],$general_strings['module_text']);

		} else {
	
			while (!$rs->EOF) {
		
				$existing_link_key = $rs->fields[1];
				$rs->MoveNext();
			
			}
		
			$rs->Close();
		
	

			$message = $modules->copy_module($module_to_copy_key,$existing_link_key,$space_key);
					

			if ($message === true) {
			
				$modules->return_to_parent($general_strings['module_text'],$module_strings['copied']);
				$exit;
				
			}
			
		}

	}
			  
} 
//get the required template files
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'modules/modulecopy.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!$module_key) {

	$module_parent = $module_strings['left_menu'];

} else {

	$module_parent = $page_details[module_name];

}


$t->set_var('COPY_MODULE_STRING',sprintf($module_strings['copy_heading'],$general_strings['module_text'],$module_parent));
$t->set_var('MODULE_NO_STRING',sprintf($module_strings['copy_no'],$general_strings['module_text']));
$t->set_var('COPY_STRING',$module_strings['copy']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);

$t->set_var('SPACE_KEY',$space_key); 
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('LINK_KEY',$link_key);	
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_TEXT',$general_strings['module_text']);
$t->set_var('MODULE_TO_COPY_KEY',$module_to_copy_key);	
$t->set_var('SELECT_COMPONENT_STRING',sprintf($module_strings['select_module'],$general_strings['module_text']));
$t->set_var('OR_STRING',$general_strings['or']);
$t->set_var('MODULE_MENU',$modules->getModuleSelect($_SESSION['current_user_key'], 'module_to_copy_select'));

$t->set_var('MESSAGE',$message); 

$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu

get_navigation();

$t->parse('CONTENTS', 'form', true);

$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;

function check_is_below($parent_key,$module_to_copy_key, $space_key, $module_type) 
{
	global $CONN, $is_below, $CONFIG;
	
	if ($module_type=='space') {
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.module_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key'");
		
		while(!$rs->EOF) {
			$module_key = $rs->fields[0];
			$space_key2 = $rs->fields[1];
			$rs->MoveNext();
		}
		if ($module_key==$module_to_copy_key) {
			$is_below = true;
			return;
		}
		if ($space_key2==0) {
			return;
		} else {
			check_is_below($parent_key2,$module_to_copy_key, $space_key2, $module_type);
		}
	
	} else {
 		$sql = "SELECT parent_key,module_key FROM {$CONFIG['DB_PREFIX']}module_space_links where link_key='$parent_key'";
		$rs = $CONN->Execute($sql);
		while (!$rs->EOF) {
			$parent_key2 = $rs->fields[0];
			$module_key2 = $rs->fields[1];
			if ($module_key2==$module_to_copy_key) {
				$is_below = true;
				return;
			} else {
				if ($parent_key2=='0') {
					return;
				} else {
					check_is_below($parent_key2,$module_to_copy_key, $space_key, $module_type);
				}
			}
			$rs->MoveNext();
		}
		$rs->Close();
	}	
}
?>
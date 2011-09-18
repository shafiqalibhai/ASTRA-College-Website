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
* link module
*
* Displays page for linking to an existing module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: modulelink.php,v 1.24 2007/07/30 01:57:00 glendavies Exp $
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
	$link_key	= $_GET['link_key'];
	$block_key	= $_GET['block_key'];			

} else {
	
	if (isset($_POST['module_to_link_select']) && $_POST['module_to_link_select']!='') {
		$module_to_link_key = $_POST['module_to_link_select'];
	} else {
		$module_to_link_key	= $_POST['module_to_link_key'];
	}
	
	$parent_key			= $_POST['parent_key'];
	$group_key			= $_POST['group_key'];
	$link_key			= $_POST['link_key'];	
	$action				= $_POST['action'];	
	$block_key			= $_POST['block_key'];	
 	
}


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

if ($action=='link') {

	check_is_below($link_key,$module_to_link_key);

		
	$is_unique = check_is_unique($module_to_link_key,$space_key,$group_key,$parent_key);
	
		
	if ($is_below=='true' || $is_unique==false) {
	
		$message = urlencode($module_strings['linking_error']);
		$back_url = $CONFIG['FULL_URL'].'/modules/general/modulelink.php?space_key='.$space_key.'&parent_key='.$parent_key.'&message='.$message;
		header("Location: $back_url");
		exit;
	
	}
	
	//check admin rights

	$can_link = check_module_link_rights($module_to_link_key);
	
		
	if ($can_link!=true) {
	
		$message = urlencode(sprintf($module_strings['no_link_rights'], $general_strings['module_text']));
		$back_url = $CONFIG['FULL_URL'].'/modules/general/modulelink.php?space_key='.$space_key.'&parent_key='.$parent_key.'&message='.$message;
		header("Location: $back_url");
		exit;
	
	}
	
	$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules WHERE   {$CONFIG['DB_PREFIX']}modules.module_key='$module_to_link_key'";

	$rs=$CONN->Execute($sql);
	if ($rs->EOF) {

			$message = sprintf($module_strings['no_such_module'],$general_strings['module_text']);

	} else {
	
		while (!$rs->EOF) {

			$module_data['code'] = $rs->fields[0];
			$rs->MoveNext();
				
		}	
		
		$sql="SELECT {$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}module_space_links.sort_order,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.change_status_date,{$CONFIG['DB_PREFIX']}module_space_links.change_status_to_key, {$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}module_space_links.module_key='$module_to_link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' ORDER BY {$CONFIG['DB_PREFIX']}module_space_links.date_added";

		$rs=$CONN->SelectLimit($sql,1);
		
		if ($rs->EOF) {
		
			$existing_link_key				 = '-99';
			$module_data['sort_order']		 = 0;
			$module_data['target']			 = '';
			$module_data['status_key']		 = 1;
			$module_data['change_status_date'] = '';
			$module_data['change_status_to']   = '';
			$module_data['space_key']		  = $space_key;				
			$module_data['parent_key']		 = $parent_key;
			$module_data['group_key']		  = $group_key;				
			$module_data['block_key']		  = $block_key;
		
		} else {

			while (!$rs->EOF) {
			
				$existing_link_key				 = $rs->fields[0];
				$module_data['sort_order']		 = $rs->fields[1];
				$module_data['target']			 = $rs->fields[2];
				$module_data['status_key']		 = $rs->fields[3];
				$module_data['change_status_date'] = $rs->fields[4];
				$module_data['change_status_to']   = $rs->fields[5];
				$module_data['link_edit_rights']   = $rs->fields[6];				
				$module_data['space_key']		  = $space_key;				
				$module_data['parent_key']		 = $parent_key;
				$module_data['group_key']		  = $group_key;	
				$module_data['block_key']		  = $block_key;			

				$rs->MoveNext();

			}
			
		}
			$message = $modules->add_module_link($module_to_link_key,$existing_link_key,$module_data);
					

			if ($message === true) {

			
				$modules->return_to_parent(urlencode($general_strings['module_text'].' '.$module_strings['link']),$module_strings['added']);
				$exit;
				
			}

	}
			  
} 
//get the required template files
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'modules/modulelink.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!$module_key) {

	$module_parent = $module_strings['left_menu'];

} else {

	$module_parent = $page_details[module_name];

}


$t->set_var('LINK_MODULE_STRING',sprintf($module_strings['link_heading'],$general_strings['module_text'],$module_parent));
$t->set_var('MODULE_NO_STRING',sprintf($module_strings['link_no'],$general_strings['module_text']));
$t->set_var('LINK_STRING',ucfirst($module_strings['link']));
$t->set_var('CANCEL_STRING',$general_strings['cancel']);

$t->set_var('SPACE_KEY',$space_key); 
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('LINK_KEY',$link_key);	
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('BLOCK_KEY',$block_key);
$t->set_var('SORT_ORDER',$sort_order);
$t->set_var('SELECT_COMPONENT_STRING',sprintf($module_strings['select_module'],$general_strings['module_text']));
$t->set_var('OR_STRING',$general_strings['or']);
$t->set_var('MODULE_MENU',$modules->getModuleSelect($_SESSION['current_user_key'], 'module_to_link_select'));
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


function check_is_below($link_to_key,$module_key) 
{
	global $CONN, $is_below, $CONFIG;
	$sql = "SELECT parent_key,module_key FROM {$CONFIG['DB_PREFIX']}module_space_links where link_key='$link_to_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
		
		$parent_key2 = $rs->fields[0];
		$module_key2 = $rs->fields[1];
		
		if ($module_key2==$module_key) {
		
			$is_below = true;
			return;
			exit;
		
		} else {
			
			if ($parent_key2=='0') {
				
				return;

			} else {
			
				check_is_below($parent_key2,$module_key);
			
			}
		
		}
		
	$rs->MoveNext();
	
	}

	$rs->Close();	

}


function check_is_unique($module_key,$space_key,$group_key,$parent_key) 
{

	global $CONN, $CONFIG;

	if ($group_key==0 || $group_key=='') {
	
			$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links where module_key='$module_key' AND space_key='$space_key' AND status_key!='4'";
	
		$rs = $CONN->Execute($sql);

		if (!$rs->EOF) {

			return false;

		} else {

			return true;
		
		}

	} else {
	
		$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND group_key='$group_key' AND status_key!='4'";
		
		$rs = $CONN->Execute($sql);
		
		if (!$rs->EOF) {

			return false;

		} else {

			return true;
		
		}

		
	}
	
	$rs->Close();
	
}
?>
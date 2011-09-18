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
* Edit module rights
*
* Displays page for adding and removing module edit rights
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: editrights.php,v 1.15 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');

if ($_GET['action']) {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];
	$link_key   = $_GET['link_key'];
	
} else {

	$space_key  = $_POST['space_key'];
	$module_key = $_POST['module_key'];
	$link_key   = $_POST['link_key'];
	
}
//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$page_details = get_page_details($space_key,$link_key);
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
//check and see that this user is allowed to edit this module or link

$can_edit_link  = check_link_edit_rights($link_key,$accesslevel_key,$group_accesslevel);

if ($can_edit_link==false) {

	$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	
} else {

	$can_edit_module = check_module_edit_rights($module_key);
	
}

if ($_POST['action']) {

	$action	 = $_POST['action'];
	$username  = $_POST['username'];
	$group_key  = $_POST['group_key'];			
	$edit_level = $_POST['edit_level'];
	$user_keys = $_POST['user_keys'];			

	switch($action) {
	
		case module_rights:
		
			if ($username) {
			
				//get userkey for given username
				
				$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='$username'";
				$rs = $CONN->Execute($sql);
				
				if ($rs->EOF) {
				
					$message = sprintf($general_strings['no_user'], $username);
					
				} else {
				
					while (!$rs->EOF) {
					
						$user_key = $rs->fields[0];
						$rs->MoveNext();
						
					}
					
					$rs->Close();
				
					$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}module_edit_right_links(user_key,module_key,edit_level) VALUES ('$user_key','$module_key','$edit_level')";
					$CONN->Execute($sql);
					$message = sprintf($module_strings['user_edit_rights_success'], $username);
					
				}
				
			} else if ($group_key) {

				//get group name
				
				$sql = "SELECT name FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$group_key'";
				$rs = $CONN->Execute($sql);
				
				if ($rs->EOF) {
				
					$message = sprintf($general_strings['no_group'], $general_strings['module_text'], $group_key);
					
				} else {
				
					while (!$rs->EOF) {
					
						$group_name = $rs->fields[0];
						$rs->MoveNext();
						
					}
					
					$rs->Close();
				
					$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}module_edit_right_links(group_key,module_key,edit_level) VALUES ('$group_key','$module_key','$edit_level')";
					$CONN->Execute($sql);
					$message = sprintf($module_strings['group_edit_rights_success'], $group_name);
					
				}
				
			}
			
		break;

		case link_rights:
		
			if ($username) {
			
				//get userkey for given username
				
				$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='$username'";
				$rs = $CONN->Execute($sql);
				
				if ($rs->EOF) {
				
					$message = sprintf($general_strings['no_user'], $username);
					
				} else {
				
					while (!$rs->EOF) {
					
						$user_key = $rs->fields[0];
						$rs->MoveNext();
						
					}
					
					$rs->Close();
				
					$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}module_edit_right_links(user_key,link_key) VALUES ('$user_key','$link_key')";
					$CONN->Execute($sql);
					$message = sprintf($module_strings['user_edit_rights_success'], $username);
					
				}
				
			} else if ($group_key) {

				//get group name
				
				$sql = "SELECT name FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$group_key'";
				$rs = $CONN->Execute($sql);
				
				if ($rs->EOF) {
				
					$message = sprintf($general_strings['no_group'], $general_strings['module_text'], $group_key);
					
				} else {
				
					while (!$rs->EOF) {
					
						$group_name = $rs->fields[0];
						$rs->MoveNext();
						
					}
					
					$rs->Close();
				
					$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}module_edit_right_links(group_key,link_key) VALUES ('$group_key','$link_key')";
					$CONN->Execute($sql);
					$message = sprintf($module_strings['group_edit_rights_success'], $group_name);;
					
				}
				
			}
			
			break;
			
			case remove_rights:

			$num_selected = count($user_keys);
		
			if ($num_selected) {

				for ($c=0; $c < $num_selected; $c++) {
			
					$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE user_key='$user_keys[$c]' AND (module_key='$module_key' OR link_key='$link_key')";
					
					$CONN->Execute($sql);
			
				}
		
			}			
			
			$num_selected = count($group_keys);
		
			if ($num_selected) {

				for ($c=0; $c < $num_selected; $c++) {
			
					$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE group_key='$group_keys[$c]' AND (module_key='$module_key' OR link_key='$link_key')";
					$CONN->Execute($sql);
			
				}
		
			}			

			$message = $module_strings['rights_removed'];
			break;
						
			default:
			 
				$message = 'Something was missing - try again';
			break;
						
		} //end switch($action)					 
		
} //end if($_POST['action'] 


//get the required template files
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'modules/editrights.ihtml',
	'footer'		  => 'footer.ihtml'));


//if user doesn't have module admin rights just show link edit rights box

if ($can_edit_module==false) {


	$t->set_block('form', 'ModuleEditBlock', 'MEBlock');
	$t->set_var('MEBlock', sprintf($module_strings['no_module_edit_rights'], $general_strings['module_text']));
	
}


//get existing rights


//get user rights first

$t->set_block('form', 'RightsListBlock', 'RBlock'); 

$sql = "SELECT {$CONFIG['DB_PREFIX']}module_edit_right_links.user_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.group_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.module_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.link_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.edit_level,{$CONFIG['DB_PREFIX']}users.first_name,{$CONFIG['DB_PREFIX']}users.last_name FROM {$CONFIG['DB_PREFIX']}module_edit_right_links,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}module_edit_right_links.user_key={$CONFIG['DB_PREFIX']}users.user_key  AND ({$CONFIG['DB_PREFIX']}module_edit_right_links.module_key='$module_key' OR {$CONFIG['DB_PREFIX']}module_edit_right_links.link_key='$link_key') AND {$CONFIG['DB_PREFIX']}module_edit_right_links.user_key!=0";

$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
if ($rs->EOF) {

	$no_user_rights = true;

} else {

	while (!$rs->EOF) {
	
		$user_key	= $rs->fields[0];
		$group_key   = $rs->fields[1];
		$module_key2 = $rs->fields[2];
		$link_key2   = $rs->fields[3];
		$edit_level  = $rs->fields[4];
		$name  = $rs->fields[5].' '.$rs->fields[6];
			
		$t->set_var('NAME',$name);
		$t->set_var('KEY','user_keys[]');
		$t->set_var('KEY_VALUE',$user_key);
		
		if ($link_key2!='0') {
		
			$edit_rights = sprintf($module_strings['can_edit_link'], $general_strings['module_text']);
			
		} else {
		
			if ($edit_level=='1') {
			
				$edit_rights = sprintf($module_strings['can_link_and_edit'], $general_strings['module_text']);
			
			} else if ($edit_level=='2') {
			
			   $edit_rights = sprintf($module_strings['can_link'], $general_strings['module_text']);
			
			} else if ($edit_level=='3') {

			   $edit_rights = sprintf($module_strings['can_link_and_copy'], $general_strings['module_text']);
			
			}			
											
		}		
		
		$t->set_var('EDIT_RIGHTS',$edit_rights);
   		$t->Parse('RBlock', 'RightsListBlock', true);
		$rs->MoveNext();
		
	}
		
	$rs->Close();
	
}										

//get group rights 

$sql = "SELECT {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.module_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.link_key,{$CONFIG['DB_PREFIX']}module_edit_right_links.edit_level,{$CONFIG['DB_PREFIX']}modules.name FROM {$CONFIG['DB_PREFIX']}module_edit_right_links,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE   ({$CONFIG['DB_PREFIX']}module_edit_right_links.group_key={$CONFIG['DB_PREFIX']}module_space_links.group_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_edit_right_links.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key!='0')) OR ({$CONFIG['DB_PREFIX']}module_edit_right_links.group_key={$CONFIG['DB_PREFIX']}module_space_links.group_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_edit_right_links.link_key='$link_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key!='0'))";

$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
if ($rs->EOF && $no_user_rights==true) {

	$t->set_block('form', 'ExistingRightsBlock', 'EBlock'); 
	$t->set_var('EBlock', '');

} else {

	while (!$rs->EOF) {
	
		$group_key   = $rs->fields[0];
		$module_key2 = $rs->fields[1];
		$link_key2   = $rs->fields[2];
		$edit_level  = $rs->fields[3];
		$name		= $rs->fields[4];
			
		$t->set_var('NAME',$name);
		$t->set_var('KEY','group_keys[]');
		$t->set_var('KEY_VALUE',$group_key);
		
		if ($link_key2!='0') {
		
			$edit_rights = sprintf($module_strings['can_edit_link'], $general_strings['module_text']);
			
		} else {
		
			if ($edit_level=='1') {
			
				$edit_rights = sprintf($module_strings['can_link_and_edit'], $general_strings['module_text']);
			
			} else {
			
			   $edit_rights = sprintf($module_strings['can_link'], $general_strings['module_text']);
			
			}
											
		}		
		
		$t->set_var('EDIT_RIGHTS',$edit_rights);
   		$t->Parse('RBlock', 'RightsListBlock', true);
		$rs->MoveNext();
	}
		
	$rs->Close();
}										


//generate the header,title, breadcrumb details
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('SPACE_KEY',$space_key); 
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('LINK_KEY',$link_key);	
$t->set_var('MODULE_KEY',$module_key); 
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_TEXT',$general_strings['module_text']);
$t->set_var('HEADING_STRING',sprintf($module_strings['edit_rights_heading'],$general_strings['module_text']));
$t->set_var('MODULE_RIGHTS_STRING',sprintf($module_strings['module_rights_heading'],$general_strings['module_text']));
$t->set_var('LINK_RIGHTS_STRING',sprintf($module_strings['link_rights_heading'],$general_strings['module_text']));
$t->set_var('GROUP_NO_STRING',sprintf($module_strings['group_no'],$general_strings['module_text']));
$t->set_var('ENTER_USERNAME_STRING',$module_strings['enter_username']);
$t->set_var('OR_STRING',$general_strings['or']);
$t->set_var('EDIT_LEVEL_STRING',$module_strings['edit_level']);
$t->set_var('EXISTING_RIGHTS_STRING',$module_strings['existing_rights']);
$t->set_var('REMOVE_SELECTED_RIGHTS_STRING',$module_strings['remove_rights']);
$t->set_var('USER_GROUP_STRING',$module_strings['user_group']);
$t->set_var('EDIT_RIGHTS_STRING',$module_strings['edit_rights']);
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
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


?>
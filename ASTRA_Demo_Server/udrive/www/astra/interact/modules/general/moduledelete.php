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
* Delete module
*
* Displays page for deleting  an existing module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: moduledelete.php,v 1.22 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');

$space_key 	= get_space_key();

if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$module_key	= $_GET['module_key'];
	$parent_key	= $_GET['parent_key'];
	$group_key	= $_GET['group_key'];
	$link_key	= $_GET['link_key'];		

} else {
	
	$module_key		= $_POST['module_key'];
	$parent_key		= $_POST['parent_key'];
	$group_key		= $_POST['group_key'];
	$delete			= $_POST['delete'];
	$delete_action	= $_POST['delete_action'];
	$link_key	   = $_POST['link_key'];		
 	
}

//check we have the required variables
check_variables(true,true);




//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);

if ($is_admin!=true) {

	$message = urlencode($module_strings['no_admin_rights']);
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	exit;

}

if ($delete==$general_strings['delete']) {

	//get module code
	$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.type_code from {$CONFIG['DB_PREFIX']}modules where module_key='$module_key'";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_code = $rs->fields[0];
		$rs->MoveNext();
	
	}

	$rs->Close();
	if ($module_code=='space') {
		$space_key=$CONN->GetOne("SELECT {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}spaces WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}spaces.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key=$link_key");
	} 
	
	$modules = new InteractModules();
	$message = $modules->flag_module_for_deletion($module_key,$space_key,$link_key,$delete_action,$module_code);

	if ($message===true) {
	
		$modules->return_to_parent($module_code,$module_strings['deleted']);
	
	}

} 

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

//get module name
$sql = "SELECT name FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
$rs = $CONN->Execute($sql);

while (!$rs->EOF) {

	$module_name = $rs->fields[0];
	$rs->MoveNext();
	
}


//get the required template files

$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'modules/moduledelete.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($parent_key=='' || $parent_key==0) {

	$module_parent = $module_strings['left_menu'];

} else {

	//get parent name
	
	$sql = "SELECT name FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND link_key='$parent_key'";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_parent = $rs->fields[0];
		$rs->MoveNext();
	
	}
	$rs->Close();

	$links_warning = sprintf($module_strings['linked_copies_warning'],$module_parent);
}

//see if this is the last link to this component
$sql = "SELECT link_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND status_key!='4'";

$rs = $CONN->Execute($sql);
$record_count = $rs->RecordCount();

if ($record_count==1) {

	$links_warning .= '<br /><strong class="message">'.$module_strings['last_link_warning'].'</strong>';
	$t->set_block('form', 'DeleteAllBlock', 'ABlock');
	$t->set_var('ABlock', '');
	$module_links = sprintf($module_strings['this_space_only'], $general_strings['space_text']);	

} else {

	//get list of spaces that this module is linked in

	$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}spaces.space_key, {$CONFIG['DB_PREFIX']}spaces.code, {$CONFIG['DB_PREFIX']}spaces.name From {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}spaces where {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND ({$CONFIG['DB_PREFIX']}module_space_links.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4')";

	//get list of groups within this space that have links to this module

	$sql2 = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.group_key From {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}modules where {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}module_space_links.group_key!='0') AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.link_key!='$link_key')";

	$rs=$CONN->Execute($sql);
	$rs2=$CONN->Execute($sql2);


	if (($rs->EOF && $rs2->EOF) || $is_admin!=true) {

  
		$t->set_block('form', 'DeleteAllBlock', 'ABlock');
		$t->set_var('ABlock', '');
		$module_links = sprintf($module_strings['this_space_only'], $general_strings['space_text']);
	
	} else {

		$module_links = sprintf($module_strings['this_space'], $general_strings['space_text']);
		$module_links .= '<br />';
		
		while (!$rs2->EOF) {
		
			$group_key = $rs2->fields[0];
			$rs3 = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.name FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$group_key'");

			while (!$rs3->EOF) {
			
				$group_name = $rs3->fields[0];
				$rs3->MoveNext();
				
			}
			
			$module_links .= ' - '.$module_strings['group_also'].' '.$group_name.'<br />';
			$rs2->MoveNext();

		}	
	
		while (!$rs->EOF) {

			$space_key2 = $rs->fields[0];			
			$space_short_name = $rs->fields[1];
			$space_name = $rs->fields[2];
			
			if ($space_key2!=$space_key) {
			
				$module_links .= $space_short_name.' - '.$space_name.'<br />';
				
			}
			
			$rs->MoveNext();

		}
	
	}
		
}

$rs->Close();
   

$t->set_var('MODULE_PARENT',$module_parent);
$t->set_var('SYBLING_LINKS_WARNING',$links_warning);

$t->set_var('DELETE_STRING',$general_strings['delete']);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('MODULE_NAME',$module_name);
$t->set_var('MODULE_LINKS',$module_links);
$t->set_var('DELETE_HEADING_STRING',sprintf($module_strings['delete_heading'],$module_name, $module_parent));
$t->set_var('DELETE_LINK_ONLY_STRING',sprintf($module_strings['delete_link_only'],$module_name, $module_parent));
$t->set_var('DELETE_ALL_STRING',sprintf($module_strings['delete_all'],$module_name));
$t->set_var('SPACE_LIST_HEADING',sprintf($module_strings['space_list_heading'],$general_strings['space_text'], $general_strings['module_text']));
$t->set_var('CHOOSE_OPTION_STRING',$module_strings['choose_option']);
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
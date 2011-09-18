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
* Folder module
*
* Displays the folder module start page 
*
* @package Folder
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: folder.php,v 1.33 2007/07/26 22:10:52 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/folder_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];

check_variables(true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

//update stats
statistics('read');

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'folder'		  => 'folders/folder.ihtml',
	'folderitems'	 => 'folders/folderitems.ihtml',
	'foldernote'	  => 'folders/foldernote.ihtml',
	'footer'		  => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();

$t->set_var('TITLE','');

$t->parse('CONTENTS', 'header', true);

$can_edit_module = check_module_edit_rights($module_key);

if ($can_edit_module==true) {
 
	$add_string = sprintf($folder_strings['add_module'],$general_strings['module_text'], $page_details['module_name']); 
	$admin_links=get_admin_tool("{$CONFIG['PATH']}/modules/general/moduleadd.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$link_key&group_key=$group_key",true,$add_string,'plus');

}
//get default sortorder

$rs = $CONN->Execute("SELECT sort_sql, icon_key, navigation_mode FROM {$CONFIG['DB_PREFIX']}folder_settings,{$CONFIG['DB_PREFIX']}sort_orders, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}folder_settings.sort_order_key={$CONFIG['DB_PREFIX']}sort_orders.sort_order_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}folder_settings.module_key AND {$CONFIG['DB_PREFIX']}folder_settings.module_key='$module_key'");

while (!$rs->EOF) {

	$default_sort_order = $rs->fields[0];
	$icon_key = $rs->fields[1];	
	$navigation_mode = $rs->fields[2];	
	$rs->MoveNext();
	
}

$rs->Close();
$t->set_var('CURRENT_MODULE_ADMIN_LINKS',$admin_links);
get_navigation();

$t->parse('CONTENTS', 'folder', true);



if ($can_edit_module==true) {

	$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name,  description,target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.sort_order,{$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key, {$CONFIG['DB_PREFIX']}module_space_links.owner_key, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4') ORDER BY {$CONFIG['DB_PREFIX']}$default_sort_order";
	
} else {

   $sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name,  description,target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.sort_order,{$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key,{$CONFIG['DB_PREFIX']}module_space_links.owner_key , {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$link_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key='1' OR {$CONFIG['DB_PREFIX']}module_space_links.status_key='3') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4') ORDER BY {$CONFIG['DB_PREFIX']}$default_sort_order";
   
}

$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
while (!$rs->EOF) {

	$icon_tag=='';
	$module_key2 = $rs->fields[0];
	$link_key2 = $rs->fields[1];
	$module_type_code = $rs->fields[2];
	$group_key2 = $rs->fields[3];
	$name = $rs->fields[4];

	if ($rs->fields[5]=='') {
	
		$description = $rs->fields[5];
		
	} else {

		$description = ' - '.$rs->fields[5];

	}

	$description = $html->urlsTolinks($description);
	$code = $module_type_code;
	$target = $rs->fields[6];
	$status_key = $rs->fields[7];
	$sort_order = $rs->fields[8];
	$edit_rights_key = $rs->fields[9];
	$owner_key = $rs->fields[10];
	$icon_key = $rs->fields[11];	
	if ($target=='new_window') {
	
		$target = $module_key2;
	
	}
		
	$can_edit_link = check_link_edit_rights($link_key2,$accesslevel_key,$group_accesslevel,$owner_key,$edit_rights_key);
	
	$url = "/modules/$code/$code.php";
	
	//if in navigation mode take to first item
	if ($navigation_mode==1 && $module_type_code!='heading') {
	
		header("Location: {$CONFIG['FULL_URL']}$url?space_key=$space_key&module_key=$module_key2");
		exit;
	
	}
	
	$admin_url = "$code/".$code."_input.php";
	
	//if forum find out if embedded or separate
	
	if ($module_type_code=='forum') {
	
		$forum_sql = "SELECT forum_type FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE
		module_key='$module_key2'";
		$rs2 = $CONN->Execute($forum_sql);
		
		while (!$rs2->EOF) {
		
		   $forum_type = $rs2->fields[0];
		   $rs2->MoveNext();
		
		}
	
	}
	
	//find out if group is visible or not
	if ($module_type_code=='group') {
		
		if (!class_exists('InteractGroup')) {
			
			require_once($CONFIG['BASE_PATH'].'/modules/group/lib.inc.php');
				
		}
			
		if (!is_object($groupObject)) {
			
			$groupObject = new InteractGroup();
			
		}
				
		$group_data = $groupObject->getGroupData($module_key2);
					
	}
		
	if ($module_type_code!='group' || $_SESSION['userlevel_key']==1 || $accesslevel_key==1 || $accesslevel_key==3 || in_array($module_key2,$group_access) || $group_data['visibility_key']==1) {

		$t->set_var('SPACE_KEY',$space_key);
		$t->set_var('GROUP_KEY',$group_key2);
		$t->set_var('LINK_KEY',$link_key2);
		$t->set_var('NAME',$name);
		$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
		$t->set_var('URL',$code);
		$t->set_var('PATH',$CONFIG['PATH']);
		$t->set_var('IMAGE',$code); 
		$t->set_var('DESCRIPTION',$description);
		
		if ($icon_key==2) {
		
			$icon_tag='';
			 
		} else if ($icon_key>2) {
		
			$icon_tag = $html->getIconTag($icon_key, 'small');
			
		}
		
		if ($icon_key==1 || $icon_tag=='default') {
		
			$icon_tag = "<img src=\"{$CONFIG['PATH']}/images/$code.gif\" width=\"16\" height=\"16\" align=\"middle\">";

		} 
		
		$image = "<td align=\"right\">$icon_tag";
			
		$image .= '</td>';
		$t->set_var('IMAGE',$image);
		
		if ($module_type_code=='space') {
						
			$rs_space_key = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key2'");
						
			while (!$rs_space_key->EOF) {
						
					$space_key2 = $rs_space_key->fields[0];
					$rs_space_key->MoveNext();
							
			}
						
			$rs_space_key->Close();
			if ($status_key==2 || $status_key==5) {
		
				$link = "<td><span class=\"red\">X</span> <a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key2&module_key=$module_key2&link_key=$link_key2&group_key=$group_key2\" target=\"$target\">$name</a>";
		
			} else {
		
				$link = "<td><a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key2&module_key=$module_key2&link_key=$link_key2&group_key=$group_key2\" target=\"$target\">$name</a>";
		
			}
		} else {
		
			if ($status_key==2 || $status_key==5) {
		
				$link = "<td><span class=\"red\">X</span> <a href=\"{$CONFIG['PATH']}$url?space_key=$space_key&module_key=$module_key2&link_key=$link_key2&group_key=$group_key2\" target=\"$target\">$name</a>";
		
			} else {
		
				$link = "<td><a href=\"{$CONFIG['PATH']}$url?space_key=$space_key&module_key=$module_key2&link_key=$link_key2&group_key=$group_key2\" target=\"$target\">$name</a>";
		
			}
		}
		$t->set_var('LINK',$link);
 
		
		if ($module_type_code=='note') {
			
			$sql="select note from {$CONFIG['DB_PREFIX']}notes where module_key='$module_key2'";
			$rs2 = $CONN->Execute($sql);
			
			while (!$rs2->EOF) {
   				
				$note = $html->parseText($rs2->fields[0]);
								
				if ($status_key==2 || $status_key==5) {
				
					$name = "<span class=\"red\" style=\"font-weight: normal\">X </span>".$name;
					$t->set_var('NAME',$name);
				
				}
				if ($name=='') {
					
					$t->set_block('foldernote', 'NoteHeadingBlock', 'NthedingBlock');
				}
				$t->set_var('NOTE',$note);
				$rs2->MoveNext();
			
			}
		
		} else {
		
			$t->set_var('NOTE','');
		
		}

		if ($can_edit_link==true){
 
//			if ($can_edit_link==true) {
				
			
			
//		}		

			$t->set_var('ADMIN_INFO','<span'.get_admin_tool_class().'>'.get_admin_tool("{$CONFIG['PATH']}/modules/$admin_url?space_key=$space_key&module_key=$module_key2&link_key=$link_key2&parent_key=$link_key&group_key=$group_key2&action=modify",false,$general_strings['edit'].' '.$general_strings['module_text']." #$module_key2").
		"<span class=\"smallgrey\">$sort_order</span>".
		" <a href=\"{$CONFIG['PATH']}/modules/general/moduleurl.php?module_key=$module_key2\" class=\"small\">url</a></span>");

		}
	
		if ($module_type_code=='heading') {
			if ($status_key==2 || $status_key==5) {
				$hidden = '<span class="red" style="font-weight: normal">X </span>';
			}
			$heading = "<td colspan=\"2\"><h3>$name$hidden</h3></td>";
			$t->set_var('IMAGE',$heading);
			$t->set_var('LINK','</td>');
			$address='';
			
		}
	

		if ($module_type_code=='note') {

			$t->parse('CONTENTS', 'foldernote', true);

		} else if ($module_type_code=='forum' && $forum_type=='embedded') {

			$t->set_var('LINK',"<td>$name");
			$t->parse('CONTENTS', 'folderitems', true);
			require ('../forum/embedforum.php');
		
		} else {
 
			$t->parse('CONTENTS', 'folderitems', true);
 
		}
	
	}

	$rs->MoveNext();

}
$rs->Close();
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
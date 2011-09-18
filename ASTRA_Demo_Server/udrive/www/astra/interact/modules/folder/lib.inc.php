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
* InteractFolder Class
*
* Contains the Folder class for all methods and datamembers related
* to  folders
*
* @package Folder
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.11 2007/05/18 04:59:49 websterb4 Exp $
* 
*/

/**
* A class that contains methods for working with folders
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for working with folders 
* 
* @package Folder
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractFolder {

	/**
	* Function to get exisiting folder data 
	*
	* @param  int $module_key  key of quiz module
	* @return array $group_data data for selected group
	*/

	function getFolderData($module_key) {

	 
		global $CONN, $CONFIG;
	 
    	$sql = "SELECT sort_order_key, navigation_mode FROM {$CONFIG['DB_PREFIX']}folder_settings WHERE module_key='$module_key'";	
   		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$folder_data['sort_order_key'] = $rs->fields[0];
			$folder_data['navigation_mode'] = $rs->fields[1];
		
			$rs->MoveNext();
	
		}
	
		return $folder_data;

	} //end getFolderData()


	/**
	* Function generate navigation bar for items in given folder 
	*
	* @param  int $folder_key  key of folder
	* @param  int $current_module_key  key of current moduke
	* @return string $navigation navigation bar for folder contents 
	* 
	*/

	function getFolderNavigation($folder_key, $link_key, $space_key, $group_key, $current_module_key) {

		global $CONN, $CONFIG;
	 
	    $sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name,description,{$CONFIG['DB_PREFIX']}module_space_links.sort_order FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$link_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key='1' OR {$CONFIG['DB_PREFIX']}module_space_links.status_key='3') AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='4') AND  {$CONFIG['DB_PREFIX']}modules.type_code!='heading') ORDER BY {$CONFIG['DB_PREFIX']}module_space_links.sort_order";

		
	
	
	$rs = $CONN->Execute($sql);
	$n=0;
	$navigation = array();
	$navigation['top'] = '<div ><ul class="folderNavigation">';
	$code_array = array();
	$modules_array = array();
	while (!$rs->EOF) {

		$module_key2 = $rs->fields[0];
		$link_key2 = $rs->fields[1];
		$code = $rs->fields[2];
		$group_key2 = $rs->fields[3];
		$name = $rs->fields[4];
		$description = strip_tags($rs->fields[5]);
		$sort_order = $rs->fields[6];
		$code_array[$module_key2] = $code;
		array_push($modules_array, $module_key2);
		
		$url = $CONFIG['PATH']."/modules/$code/$code.php?space_key=$space_key&module_key=$module_key2";
		
		if ($module_key2==$current_module_key) {
		
			$navigation['top'] .= '<li id="folderNavActive"><span>'.$name.'</span></li> ';
		
		} else {
		
			$navigation['top'] .= "<li><a href=\"$url\" title=\"$description\" class=\"folderNavItem\">$name</a></li> ";
		
		}
		
		$rs->MoveNext();
		
	}
	
	
	if (check_module_edit_rights($folder_key)==true) {
	
		$navigation['top'] .= get_admin_tool("{$CONFIG['PATH']}/modules/folder/folder_input.php?space_key=$space_key&amp;module_key=$folder_key&link_key=$link_key&action=modify",true,"{$general_strings['edit']} component $folder_key")." ".get_admin_tool("{$CONFIG['PATH']}/modules/general/moduleadd.php?space_key=$space_key&module_key=$folder_key&link_key=$link_key&parent_key=$link_key&group_key=$group_key",true,"Add component",'plus'); 
	
	}
	$navigation['top'] .= '</ul>';
	
	$navigation['top'] .= '</div>';
	
	//now generate next/previous links
	
	$key = array_search($current_module_key, $modules_array);
	$previous = $key-1;
	$next = $key+1;
	if (isset($modules_array[$previous])) {
	
		$code = $code_array[$modules_array[$previous]];
		$previous_link = "<a href=\"".$CONFIG['PATH']."/modules/$code/$code.php?space_key=$space_key&module_key=".$modules_array[$previous]."\">Previous</a>";
	
	
	} 
	if (isset($modules_array[$next])) {
	
		$code = $code_array[$modules_array[$next]];
		$next_link = "<a href=\"".$CONFIG['PATH']."/modules/$code/$code.php?space_key=$space_key&module_key=".$modules_array[$next]."\">Next</a>";
	
	} 
	$navigation['bottom'] = "<ul class=\"folderNavigation\"><li>$previous_link</li> <li> $next_link</li></ul>";
	return $navigation;
	
	} //end getFolderNavigation
	
} //end InteractGroup class	
?>
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
* Sitemap display
*
* Displays a sitemap of all components in a given space
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: spacemap.php,v 1.7 2007/05/06 23:30:11 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');


//check to see if user is logged in. If not refer to Login page.
$space_key  = isset($_GET['space_key'])? $_GET['space_key'] : '';
$current_user_key=$_SESSION['current_user_key'];

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];   

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'body'	   => 'spaces/spacemap.ihtml',
	'footer'	 => 'footer.ihtml'));
// get page details for titles and breadcrumb navigation
$page_details=get_page_details($space_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true);
get_navigation();

$init_links = '';
$module_links = get_site_map($space_key, 0,$init_links);
$t->set_var('MODULE_LINKS','<ul>'.$module_links.'</ul>');
$t->set_var('SPACEMAP',$general_strings['spacemap']);
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

$CONN->Close();
exit;

function get_site_map($space_key, $parent_key, &$module_links) {
	
	global $CONN,$CONFIG, $accesslevel_key, $group_access;
	
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.link_key  FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links where  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' and {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$parent_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1') AND {$CONFIG['DB_PREFIX']}modules.type_code!='heading' AND {$CONFIG['DB_PREFIX']}modules.type_code!='note' ORDER BY sort_order,{$CONFIG['DB_PREFIX']}module_space_links.date_added");
	
	echo $CONN->ErrorMsg();
	while(!$rs->EOF) {
		
		$type_code = $rs->fields[0];
		$module_key = $rs->fields[1];
		$name = $rs->fields[2];
		$description = substr(strip_tags($rs->fields[3],0,20));
		$link_key = $rs->fields[4];
		if ($type_code=='space') {
			
			$module_space_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
			$module_links .= '<li style="list-style-image: url('.$CONFIG['PATH'].'/images/'.$type_code.'.gif);"><a title="'.$description.'" href="'.$CONFIG['PATH'].'/spaces/space.php?space_key='.$module_space_key.'">'.$name.'</li>';
		
		} else {
			
			if ($type_code=='folder' || $type_code=='group') {
				if ($type_code=='group') {
					if (!class_exists('InteractGroup')) {
						require_once($CONFIG['BASE_PATH'].'/modules/group/lib.inc.php');
					}
					if (!is_object($objGroup)) {
						$objGroup = new InteractGroup();
					}
					$group_data = $objGroup->getGroupData($module_key);
					
				}
				
				if ($type_code=='folder' ||$_SESSION['userlevel_key']==1 || $accesslevel_key==1 || $accesslevel_key==3 || in_array($module_key,$group_access) || $group_data['visibility_key']==1) {
						$module_links .= '<li style="list-style-image: url('.$CONFIG['PATH'].'/images/'.$type_code.'.gif);"><a title="'.$description.'" href="'.$CONFIG['PATH'].'/modules/'.$type_code.'/'.$type_code.'.php?space_key='.$space_key.'&module_key='.$module_key.'">'.$name.'</li>';		
						$module_links .= '<ul>';
						if($type_code=='folder' ||$_SESSION['userlevel_key']==1 || $accesslevel_key==1 || $accesslevel_key==3 || in_array($module_key,$group_access) || $group_data['access_key']==2) {
							get_site_map($space_key, $link_key, $module_links);
							
						}
						$module_links .= '</ul>';
				}
				
			} else {
				$module_links .= '<li style="list-style-image: url('.$CONFIG['PATH'].'/images/'.$type_code.'.gif);"><a title="'.$description.'" href="'.$CONFIG['PATH'].'/modules/'.$type_code.'/'.$type_code.'.php?space_key='.$space_key.'&module_key='.$module_key.'">'.$name.'</li>';	
			
			}
		}
		$rs->MoveNext();
		
	}
	return $module_links;
}


?>

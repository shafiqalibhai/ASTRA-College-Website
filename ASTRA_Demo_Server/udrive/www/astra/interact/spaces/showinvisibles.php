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
* Show invisible members
*
* Displays invisible members of a space that don't display on the  
* normal members list
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: showinvisibles.php,v 1.17 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');


//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key	= $_GET['space_key'];

//check we have the required variables
check_variables(true,false);




//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];	 

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		   => 'header.ihtml',
	'navigation'	   => 'navigation.ihtml',
	'invisiblemembers' => 'spaces/showinvisibles.ihtml',
	'footer'		   => 'footer.ihtml'
));

$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$breadcrumbs = '<a href="'.$CONFIG['PATH']."/spaces/space.php?space_key=$space_key\" class=\"spaceHeadinglink\">".$page_details['space_name']."</a> > <a href=\"members.php?space_key=$space_key\">".$group_strings['members2'].'</a> > ';
$t->set_var('BREADCRUMBS',$breadcrumbs);
$t->set_var('INVISIBLE_MEMBERS_HEADING',$space_strings['invisible_members']);

$t->parse('CONTENTS', 'header', true); 

//get any members for this space
$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}users.user_key, first_name, last_name, email,access_level_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND (space_key='$space_key' AND (access_level_key='4' OR access_level_key='3')) ORDER BY last_name";
$rs = $CONN->Execute($sql);

if ($rs->EOF) {
		
		$message = $space_strings['no_invisibles'];
		
} else {

	while (!$rs->EOF) {
	
		$user_key = $rs->fields[0];
		$name = $rs->fields[1].' '.$rs->fields[2];
		$email = $rs->fields[3];
		$member_accesslevel_key = $rs->fields[4];
		$email_username=urlencode($name);
		
		if ($member_accesslevel_key==3) {
		
				$member_type = $space_strings['invisible_admin'] ;
		
			} else {
		
				$member_type = $space_strings['invisible_guest'] ;
		
			}	
			$delete_warning=$general_strings['check'];
			$invisble_members .= "$name <a href=\"mailto:$email\">$email</a> - $member_type (<a  href=\"{$CONFIG['PATH']}/spaces/memberedit.php?space_key=$space_key&user_key=$user_key&action=delete\" onClick=\"return confirmDelete('$delete_warning')\">{$general_strings['delete']}</a> - <a  href=\"{$CONFIG['PATH']}/spaces/memberedit.php?space_key=$space_key&user_key=$user_key&action=make_visible\">{$space_strings['make_visible']}</a>)<br /><br />";
			$rs->MoveNext();
		}
	}

$t->set_var('INVISIBLE_MEMBERS',$invisble_members);
get_navigation();
$t->parse('CONTENTS', 'invisiblemembers', true);


$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
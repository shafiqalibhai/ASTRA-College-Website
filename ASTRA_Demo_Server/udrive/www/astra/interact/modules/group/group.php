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
* Group module
*
* Displays the group module start page 
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: group.php,v 1.31 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
//now find out access level of group and if user is allowed in

if (!class_exists(InteractGroup)) {
			
	require_once('lib.inc.php');
				
}
			
if (!is_object($groupObject)) {
			
	$groupObject = new InteractGroup();
			
}
if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$group_data	  = $groupObject->getGroupData($group_key);
$membership_data = $groupObject->checkMembership($group_key,$_SESSION['current_user_key']);

if ($group_data['access_key']!=2 && $membership_data===false && $is_admin==false && $group_accesslevel!=1) {

   echo "You don't appear to be a member of that group";
   exit;

}
$new_members = $groupObject->countNewMembers($group_key, $_SESSION['current_user_key']);

//update statistics 
statistics('read');

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'group'		   => 'groups/group.ihtml',
	'groupitems'	  => 'groups/group_items.ihtml',
	'groupnote'	   => 'groups/group_note.ihtml',
	'footer'		  => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true); 

$t->set_var('TITLE','');


//find out how many members there are

$total_members = $groupObject->countMembers($group_key);
$t->set_var('TOTAL_MEMBERS',$total_members);

if ($group_data['maximum_users']>0) {

	if ($total_members<$group_data['maximum_users']) {
	
		$places_left = $group_data['maximum_users']-$total_members;
		$t->set_var('PLACES_LEFT_STRING',$group_strings['places_left'].' '.$places_left);
		
	
	} else if ($total_members>=$group_data['maximum_users']){
	
		$t->set_var('PLACES_LEFT_STRING',$group_strings['places_left'].' 0');
		if ($membership_data==false) {
		
			$t->set_block('group', 'AddMeBlock', 'AMBlock');
			$t->set_var('AMBlock','');	
			
		}
	
	}

}

//see if we have any new members, if so show new members links

if ($new_members>0) {

	$t->set_var('NEW_MEMBERS_STRING',$general_strings['new_members'].'('.$new_members.')');	

} else {

	$t->set_block('group', 'NewMembersBlock', 'NMBlock');
	$t->set_var('NMBlock','');	

}

if ($is_admin==true  || $group_accesslevel==1) {
	
	$add_string = sprintf($group_strings['add_module'],$general_strings['module_text'], $page_details['module_name']); 

	$admin_links=get_admin_tool($CONFIG['PATH'].'/modules/general/moduleadd.php?space_key='.$space_key.'&module_key='.$module_key.'&link_key='.$link_key.'&parent_key='.$link_key.'&group_key='.$group_key,true,$add_string,'plus');
	// - <a href=\"statistics.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&link_key=$link_key\" class=\"small\">$statistics_string</a> - ";
	$statistics_link = '<span class="small"><a href="statistics.php?space_key='.$space_key.'&module_key='.$module_key.'&group_key='.$group_key.'" title="'.$group_strings['group_admin'].'"><img src="'.$CONFIG['PATH'].'/images/statistics.gif" alt="'.$group_strings['group_admin'].'" border="0" width="16" height="16" align="middle"></a></span>';

} else {

	$admin_links = '';
	$statistics_link = '';

}

//see if user is member, if not show 'add me' link
//find out the access level of the group

if ($membership_data!=false && $group_data['access_key']!=1 && isset($_SESSION['current_user_key'])) {

	$t->set_var('ADD_ME_IMAGE',$group_strings['remove_me']);
	$t->set_var('ADD_ME_STRING','');
	$t->set_var('MEMBERSHIP_ACTION','remove_single');	

} else if ($membership_data==false && isset($_SESSION['current_user_key'])){

	$t->set_var('ADD_ME_TEXT',$group_strings['add_me']);
	$t->set_var('ADD_ME_STRING','');
	$t->set_var('MEMBERSHIP_ACTION','add_single');		

}

//if user is not an admin and not a member then don't show email members link
if ($membership_data==false && $is_admin==false) { 

	$t->set_block('group', 'emailMembersBlock', 'EMBlock');
	$t->set_var('EMBlock','');	

}
//see if group acting as a group manager - if is display add group link

if ($group_data['group_management']==1) {
	$t->set_var('ADD_GROUP_STRING',$group_strings['add_group']);
	
} else {
	$t->set_block('group', 'NewGroupBlock', 'NGBlock');
	$t->set_var('NGBlock','');	
}
$t->set_var('DESCRIPTION',$page_details['module_description']);
$t->set_var('CURRENT_MODULE_ADMIN_LINKS',$admin_links);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$module_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('MEMBERS_STRING',sprintf($group_strings['members_link'],$page_details['module_name']));
$t->set_var('EMAIL_STRING','<img src="'.$CONFIG['PATH'].'/images/email.gif" border="0" align="middle" title="'.$group_strings['email_link'].'">');
$t->set_var('STATISTICS_LINK',$statistics_link);
get_navigation();
$t->parse('CONTENTS', 'group', true);

//get default sortorder

$rs = $CONN->Execute("SELECT sort_sql FROM {$CONFIG['DB_PREFIX']}group_settings,{$CONFIG['DB_PREFIX']}sort_orders WHERE {$CONFIG['DB_PREFIX']}group_settings.sort_order_key={$CONFIG['DB_PREFIX']}sort_orders.sort_order_key AND {$CONFIG['DB_PREFIX']}group_settings.module_key='$module_key'");

while (!$rs->EOF) {

	$default_sort_order = $rs->fields[0];
	$rs->MoveNext();
	
}

$rs->Close();

if ($is_admin==true) {

	$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name, description,target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.sort_order, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4') ORDER BY {$CONFIG['DB_PREFIX']}$default_sort_order";
	
} else {

	$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name, description,target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.sort_order, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$link_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key='1' OR {$CONFIG['DB_PREFIX']}module_space_links.status_key='3') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4') ORDER BY {$CONFIG['DB_PREFIX']}$default_sort_order";
	
}

$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
while (!$rs->EOF) {

	$module_key2 = $rs->fields[0];
	$link_key2 = $rs->fields[1];	
	$code = $rs->fields[2];
	$group_key2 = $rs->fields[3];
	$name = $rs->fields[4];

	
	if ($rs->fields[5]=='') {
	
		$description = $rs->fields[5];
		
	} else {
	
		$description = ' - '.$rs->fields[5];
		
	}
	
	$target = $rs->fields[6];
	
	if ($target=='new_window') {
	
		$target = $module_key2;
	
	}
	
	$status_key = $rs->fields[7];
	$sort_order = $rs->fields[8];
	$icon_key = $rs->fields[9];
	
	$can_edit_link = check_link_edit_rights($link_key2,$accesslevel_key,$group_accesslevel,$owner_key,$edit_rights_key);
			
	$url = "/modules/$code/$code.php";
	$admin_url = "/modules/$code/".$code."_input.php";
	
	//if forum find out if embedded or separate
	
	if ($code=='forum') {
	
		$forum_sql = "SELECT forum_type FROM {$CONFIG['DB_PREFIX']}forum_settings WHERE
		module_key='$module_key2'";
		$rs2 = $CONN->Execute($forum_sql);
		
		while (!$rs2->EOF) {
		
		   $forum_type = $rs2->fields[0];
		   $rs2->MoveNext();
		
		}
	
	}
	
		//find out if group is visible or not
	if ($code=='group') {
		
		if (!class_exists('InteractGroup')) {
			
			require_once($CONFIG['BASE_PATH'].'/modules/group/lib.inc.php');
				
		}
			
		if (!is_object($groupObject)) {
			
			$groupObject = new InteractGroup();
			
		}
				
		$group_data2 = $groupObject->getGroupData($module_key2);
		//if parent group has group management on display group data
		if ($group_data['group_management']==1) {
			$description .= '<p class="small">';
			if ($group_data2['maximum_users']>0 || $group_data2['minimum_users']>0) {
				$total_members = $groupObject->countMembers($module_key2);
				$description .= $group_strings['members2'].': '.$total_members;
				if ($total_members<$group_data2['minimum_users']) {
					$needed = $group_data2['minimum_users']-$total_members;
					$description .= ' ('.sprintf($group_strings['people_needed'],$needed).')<br />';
				} else {
					$description .= '<br />';
				}
			}
			/*
			if ($group_data2['minimum_users']>0) {
				$description .= $group_strings['people_needed'].': '.$group_data2['minimum_users'].'<br />';
			} 		
			if ($group_data2['maximum_users']>0) {
				$description .= $group_strings['max_people_needed'].': '.$group_data2['maximum_users'].'<br />';
			} 	*/		

			if ($group_data2['start_date_unix']>0) {
				$description .= $group_strings['start_date'].': '.date('d F',$group_data2['start_date_unix']).'<br />';
			} 		
			if ($group_data2['finish_date_unix']>0) {
				$description .= $group_strings['finish_date'].': '.date('d F',$group_data2['finish_date_unix']).'<br />';
			} 					
		}
					
	}
		
	if ($code!='group' || $userlevel_key==1 || $accesslevel_key==1 || $accesslevel_key==3 || in_array($module_key2,$group_access) || $group_data2['visibility_key']==1) {
	 
		$t->set_var('SPACE_KEY',$space_key);
		$t->set_var('GROUP_KEY',$group_key2);
		$t->set_var('MODULE_KEY',$module_key2);
		$t->set_var('NAME',$name);
		$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
		$t->set_var('URL',$url);
		$t->set_var('PATH',$CONFIG['PATH']);
		$t->set_var('IMAGE',$code); 
		$t->set_var('DESCRIPTION',$description);
		
		if ($icon_key==2) {
		
			$icon_tag='';
			 
		} else if ($icon_key>2) {
		
			$icon_tag = $html->getIconTag($icon_key, 'large');
			

		} else 	if ($icon_key==1 || $icon_tag=='default') {
		
			$icon_tag = "<img src=\"{$CONFIG['PATH']}/images/$code.gif\" width=\"16\" height=\"16\" align=\"middle\">";

		} 

		$image = "<td align=\"right\">$icon_tag";
		
		$image .= '</td>';
		
		$t->set_var('IMAGE',$image);
		
		if ($status_key==2 || $status_key==5) {
		
			$link = "<td><span class=\"red\">X</span> <a href=\"{$CONFIG['PATH']}$url?space_key=$space_key&module_key=$module_key2&link_key=$link_key2&group_key=$group_key2\" target=\"$target\">$name</a>";
			
		} else {
		
			$link = "<td><a href=\"{$CONFIG['PATH']}$url?space_key=$space_key&module_key=$module_key2&link_key=$link_key2&group_key=$group_key2\" target=\"$target\">$name</a>";
		
		}
		
		$t->set_var('LINK',$link);
 
		
		if ($code=='note') {
		
			$sql="SELECT note FROM {$CONFIG['DB_PREFIX']}notes WHERE module_key='$module_key2'";
			$rs2 = $CONN->Execute($sql);
			
			while (!$rs2->EOF) {
   				
				if (eregi("(<p|<br)", $rs2->fields[0])) {
				
					$note = $rs2->fields[0];
   				
				} else {
				
					$note = nl2br($rs2->fields[0]);
				
				}
			
				if ($status_key==2 || $status_key==5) {
				
					$name = "<span class=\"red\" style=\"font-weight: normal\">X </span>".$name;
					$t->set_var('NAME',$name);
				}
			
				$t->set_var('NOTE',$note);
				$rs2->MoveNext();
				
			}
			
		} else {
			
			$t->set_var('NOTE','');
		
		}
 
		if ($can_edit_link==true){
			$t->set_var('ADMIN_INFO','<span'.get_admin_tool_class().'>'.get_admin_tool("{$CONFIG['PATH']}$admin_url?space_key=$space_key&module_key=$module_key2&link_key=$link_key2&parent_key=$link_key&group_key=$group_key2&action=modify",false).
			"<span class=\"smallgrey\">$sort_order</span>".
			" <a href=\"{$CONFIG['PATH']}/modules/general/moduleurl.php?module_key=$module_key2\" class=\"admin_tool\">url</a></span>");
		

		
		}
		
		if ($code=='heading') {
		
			$heading = "<td colspan=\"2\"><b>$name</b></td>";
			$t->set_var('IMAGE',$heading);
			$t->set_var('LINK','</td>');
			$address='';
		} 
		
		
		if ($code=='note') {
		
			$t->parse('CONTENTS', 'groupnote', true);
		
		} else if ($code=='forum' && $forum_type=='embedded') {

			$t->set_var('LINK',"<td>$name");
			$t->parse('CONTENTS', 'groupitems', true);
			require ('../forum/embedforum.php');
					
		} else {
		
		$t->parse('CONTENTS', 'groupitems', true);
	   
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
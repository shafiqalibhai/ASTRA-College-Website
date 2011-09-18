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
* InteractSpace Class
*
* Contains the Space class for all methods and datamembers related
* to spaces
*
* @package Space
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.33 2007/06/06 03:05:59 glendavies Exp $
* 
*/

/**
* A class that contains methods for working with spaces
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for working with spaces 
* 
* @package Space
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractSpace {

	/**
	* Function to check if a user is a member of a space 
	*
	* @param  int $space_key  key of group
	* @param  int $user_key  key of user
	* @return array $membership_data data for user in group - access_level, date added. return false 
	* if not member
	*/

	function checkMembership($space_key, $user_key) {

	 
		global $CONN, $CONFIG;
	 
		$sql = "SELECT access_level_key, date_added FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$user_key'";	
		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {

				$membership_data['access_level_key'] = $rs->fields[0];
				$membership_data['date_added'] = $CONN->UnixTimeStamp($rs->fields[1]);		
 			
		
				$rs->MoveNext();
	
			}
			
			return $membership_data;
			
		}
		
		$rs->Close();
		
	} //end checkMembership()
	
	/**
	* Function to count the number of members in a space 
	*
	* @param  int $space_key  key of group
	* @return int $total_members total number of members in a group
	*/

	function countMembers($space_key, $include_leaders=true) {

	 
		global $CONN, $CONFIG;
	 
		if ($include_leaders==true) {
		
			$sql = "SELECT COUNT(user_key) FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key'";
			
		} else {
		
			$sql = "SELECT COUNT(user_key) FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$group_key' AND access_level_key='2'";		
		
		}  
		  
		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {

				$total_members = $rs->fields[0];
				$rs->MoveNext();
	
			}
			
			return $total_members;
			
		}
		
		$rs->Close();
		
	} //end countMembers()
	
	/**
	* Function to count the number of new members in a space 
	*
	* @param  int $space_key  key of space
	* @param  int $user_key  key of user to find new members for	
	* @return int $new_members total number of members in a group
	*/

	function countNewMembers($space_key, $user_key) {

	 
		global $CONN, $CONFIG;

		//first of all find out the last time the user accessed this group
		
		$rs = $CONN->SelectLimit("SELECT date_accessed FROM {$CONFIG['DB_PREFIX']}statistics WHERE space_key='$space_key' AND user_key='$user_key' ORDER BY date_accessed DESC", 1);
		
		if ($rs->EOF || $CONN->ErrorMsg()) {
		
			//user has not accessed space before so don't show new members
			
			return 0;

			
		} else {
		
			if (!isset($_SESSION['space_'.$space_key.'_last_access'])) {
			
				$date_accessed = $CONN->DBDate(date('Y-m-d H:i:s',$CONN->UnixTimestamp($rs->fields[0])));				
				$_SESSION['space_'.$space_key.'_last_access'] = $date_accessed;
				
			} else {
			
				$date_accessed = $_SESSION['space_'.$space_key.'_last_access'];
			
			}
			
			$rs->Close();
				
		} 
		
		$new_members = $CONN->GetOne("SELECT COUNT(user_key) FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE date_added>$date_accessed AND space_key='$space_key'");
		
		return $new_members;
	
		
	} //end countMembers()
		
	/**
	* Function to get component links for a given block 
	*
	* @param  int $space_key  key of space
	* @param  int $block_key  key of block to get component links for	
	* @return string ordered list of components for given block
	*/

	function getBlockComponents($space_key, $block_key, $accesslevel_key) {

	 
		global $CONN, $CONFIG;

		$userlevel_key		  = $_SESSION['userlevel_key'];
		$current_user_key	   = $_SESSION['current_user_key'];
	
		if ($_SESSION['userlevel_key']!=1 && $accesslevel_key!=1 && $accesslevel_key!=3) {
			$sql = "select {$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name,  {$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,  {$CONFIG['DB_PREFIX']}module_space_links.owner_key, {$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key, {$CONFIG['DB_PREFIX']}module_space_links.sort_order, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links where  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' and {$CONFIG['DB_PREFIX']}module_space_links.parent_key='0' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND {$CONFIG['DB_PREFIX']}module_space_links.block_key='$block_key') order by sort_order, {$CONFIG['DB_PREFIX']}modules.name";
		
		} else {

			$sql = "select {$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name,  {$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key, {$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key, {$CONFIG['DB_PREFIX']}module_space_links.owner_key, {$CONFIG['DB_PREFIX']}module_space_links.sort_order, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links where  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' and {$CONFIG['DB_PREFIX']}module_space_links.parent_key='0' and ({$CONFIG['DB_PREFIX']}module_space_links.status_key='1' OR {$CONFIG['DB_PREFIX']}module_space_links.status_key='3' OR {$CONFIG['DB_PREFIX']}module_space_links.status_key='2') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}module_space_links.block_key='$block_key') order by sort_order, {$CONFIG['DB_PREFIX']}modules.name";
  
		}

		$rs = $CONN->Execute($sql);
		$list_open=false;
		$component_list='';
		while (!$rs->EOF) {

			$icon_tag = ''; 
			$link = '';
			$module_type_code = $rs->fields[0];
			$module_key2 = $rs->fields[1];
			$group_key2 = $rs->fields[2];
		
			//find out if group is visible or not
			if ($module_type_key=='group') {
				if (!class_exists(InteractGroup)) {
					require_once($CONFIG['BASE_PATH'].'/modules/group/lib.inc.php');
				}
				if (!is_object($groupObject)) {
					$groupObject = new InteractGroup();
				}
				$group_data = $groupObject->getGroupData($module_key2);
			}
		
			if ($module_type_code!='group' || $userlevel_key==1 || $accesslevel_key==1 || $accesslevel_key==3 || in_array($module_key2,$group_access) || $group_data['visibility_key']==1) {

			
				$nav_name = $rs->fields[3];
				$nav_code = $module_type_code;
				$nav_description = strip_tags(substr($rs->fields[4],0,255));
				$target = $rs->fields[6];
				$status_key = $rs->fields[7];
				$link_key2 = $rs->fields[8];
				$edit_rights_key = $rs->fields[9];
				$owner_key = $rs->fields[10];
				$sort_order = $rs->fields[11];						
				$nav_admin = $nav_code."_input.php";
				$icon_key = $rs->fields[12];
			
				if ($icon_key>2) {
			
					$objHtml = singleton::getInstance('html');
					$nav_image = $objHtml->getIconurl($icon_key, 'small');	
			
				} else if ($icon_key==1){
			
					$nav_image = "{$CONFIG['PATH']}/images/$nav_code.gif";
			
				} else if ($icon_key==2){
			
					$nav_image = '';
			
				}
				// if user has admin rights show edit image
				if (check_link_edit_rights($link_key2,$accesslevel_key,$group_accesslevel,$owner_key,'')==true) {
					$admin_image=get_admin_tool("{$CONFIG['PATH']}/modules/$nav_code/$nav_admin?space_key=$space_key&amp;module_key=$module_key2&amp;link_key=$link_key2&amp;action=modify",true,"edit $MODULE_TEXT $module_key2 - sort order $sort_order");
				} else { 
	 				$admin_image='';
				}
		
 
 				if ($module_type_code=='heading') {
				
					if ($header==true) {
						$link = "</ul></li><li><span class=\"heading\">$nav_name</span> $admin_image<ul>";
						
					} else {
				
						$link = "<li><span class=\"heading\">$nav_name</span> $admin_image<ul>";
					}
					$header=true;

				
				} else {
				
					$hidden = ($status_key==2)?'<span class="smallred">X</span>':'';
	
					if ($nav_code=='space') {
						
						$rs_space_key = $CONN->Execute("SELECT space_key, short_name, combine_names FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key2'");
						
						while (!$rs_space_key->EOF) {
						
							$space_key2 = $rs_space_key->fields[0];
							$short_name = $rs_space_key->fields[1];
							$combine_names = $rs_space_key->fields[2];
							$rs_space_key->MoveNext();
							
						}
						if ($short_name!='' && $combine_names=='1') {
							$nav_name = $short_name.' - '.$nav_name;
						}
						$rs_space_key->Close();
						
						$link = "<li><a  style=\"background-image: url($nav_image)\" href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key2\"  class=\"navlinks\" title=\"$nav_description\" target=\"$target\">$nav_name</a>$hidden $admin_image</li>";
						
					} else if ($nav_code=='note') {
						$note = $CONN->GetOne("SELECT note FROM {$CONFIG['DB_PREFIX']}notes WHERE module_key='$module_key2'");
						if ($list_open) {
							$link = '</ul>';
							$list_open = false;
						}
						$link .= $note.' '.$admin_image;
					} else if ($nav_code=='feedreader') {
						if ($list_open) {
							$link = '</ul>';
							$list_open = false;
						}
						$view_class = ($block_key==1)?'mediumView':'miniView';
						
						$feed_data = $CONN->Execute("SELECT url, item_count, file_path FROM {$CONFIG['DB_PREFIX']}feedreader_settings WHERE module_key='$module_key2'");
						$urls = explode(',',$feed_data->fields[0]);
						$item_count = $feed_data->fields[1];
						$file_path = $feed_data->fields[2];
						$feed_data->Close();
						require_once('../includes/magpie/rss_fetch.inc');
						define('MAGPIE_CACHE_ON', true);
						define('MAGPIE_CACHE_DIR', $CONFIG['BASE_PATH'].'/local/modules/feedreader/'.$file_path);
						define('MAGPIE_CACHE_AGE', '60');
						define('MAGPIE_CONDITIONAL_GET_ON', false);
						$n=0;
						foreach($urls as $value) {
							
							$id =$n.rand(1111,9999);
							$rss = fetch_rss(trim($value));
						
							$rss->items = array_slice($rss->items, 0, $item_count);
							$link.='<br /><div  class="controlBox '.$view_class.'"><div  class="disPaneHeadingOpen" onclick="show_it(\'rss'.$id.'\',this,\'disPaneHeading\')" id="rssHeading'.$id.'"><span class="controlBoxTitle">'.$rss->channel['title'].'</span></div><div class="disPaneContent" id="rss'.$id.'"><ul>'.$admin_image;
							foreach ($rss->items as $item ) {
								$link .= '<li><a href="'.$item['link'].'" class="small">'.$item['title'].'</a></li>';
														
							}
							$link .= '</ul></div></div>';
							$n++;
						}
						

					
					} else {
					
						$link = "<li ><a  style=\"background-image: url($nav_image)\" href=\"{$CONFIG['PATH']}/modules/$nav_code/$nav_code.php?space_key=$space_key&amp;module_key=$module_key2&amp;link_key=$link_key2&amp;group_key=$group_key2\"  class=\"navlinks\" title=\"$nav_description\" target=\"$target\">$nav_name</a>$hidden $admin_image</li>";
					
					}
		
				}
  
	
			
		}
		if (($module_type_code != 'note' && $module_type_code != 'feedreader' && $module_type_code != 'note')) {
 			if ($list_open==false) {
				$component_list .= '<ul>';
				$list_open=true;
			}
 		}
		$component_list .= $link;
		$rs->MoveNext();
	}

	if ($header==true) {

		$component_list .= "</ul></li>";
		
	}
	if($list_open==true) {
		$component_list .= "</ul>";
	}

	
	return $component_list;	
		
	}//end getBlockComponents
	
		/**
	* Function to count the number of members in a space 
	*
	* @param  int $space_key  key of group
	* @param  int $current_user_key  key of current user
	* @return boolean true 
	*/

	function newUserAlert($space_key, $current_user_key) {

	 
		global $CONN, $CONFIG, $space_strings, $general_strings;
	 	$new_user_alert = $CONN->GetOne("SELECT new_user_alert FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'");
	
	if ($new_user_alert=='true') {
		$rs = $CONN->Execute("SELECT first_name, last_name, details FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$current_user_key'");
		$name = $rs->fields[0].' '.$rs->fields[1];
		$details = ereg_replace( 10, "\n", $rs->fields[2]);
		$rs->Close();
		$member_keys = array();
		$n = 0;
			
		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links,{$CONFIG['DB_PREFIX']}spaces WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND ({$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1' AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key')";

		$rs = $CONN->Execute($sql);
			
		while (!$rs->EOF) {
			
			$member_keys[$n] = $rs->fields[0];
			$space_name = $rs->fields[1];
			$n++;
			$rs->MoveNext();
				
		}


		$mailbody = sprintf($space_strings['new_user_alert'], $name, $space_name, $general_strings['space_text']);
		
		$mailbody .= "\n\n";
		$mailbody .= sprintf($space_strings['details'], $name);
				
		if ($details!='') {
					
			$mailbody = $mailbody."\n$details";
				
		} else {
			   
			$mailbody = $mailbody."\n".$space_strings['no_details'];
				
		}
			
		require_once('../includes/email.inc.php');
		$subject = sprintf($space_strings['new_member'], $space_name);
		email_users($subject, $mailbody, $member_keys, '', '', '');
	}
		
	} //end newUserAlert()

} //end InteractSpace class	
?>
<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education				  |
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
* urlTag functions
*
* Contains any common functions related to url tags
*
* @package urlTags
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.8 2007/04/17 06:13:10 websterb4 Exp $
* 
*/

/**
* A class that contains common methods related to url tags 
* 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods related to the adding and viewing of tags 
* 
* @package urlTags
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteracturlTags {

 	/**
	* A method of class InteractTags to add a new url tag
	*
	* @param array $tag_data array of tag data
	* @param int $current_user_key user key of current user
	* @param int $space_key space key of current space
	* @param int $group_key group key if note added in group		
	* @return true		
	*/
	function addurlTag($tag_data, $current_user_key, $space_key, $group_key) {
	
		global $CONN, $CONFIG;
		
		$added_for 			= $tag_data['added_for'];
		$selected_user_key	= $tag_data['selected_user_key'];
		$date	 			= $CONN->DBDate(date('Y-m-d H:i:s'));
		$heading 			= $tag_data['heading'];
		$note	 			= $tag_data['note'];
		
		if ($tag_data['external_tag']==1) {
		
			if (strpos($tag_data['external_url'], 'http://')===false) {
			
				$tag_url = 'http://'.$tag_data['external_url'];
			
			} else {
			
				$tag_url = $tag_data['external_url'];
			
			}
		
		} else {
		
			$tag_url = preg_replace("/(&module_key=[0-9]*[^\"]*)/si", "$1", urldecode($tag_data['tag_url']));
			$tag_url = preg_replace("/&message=.*/si", "", urldecode($tag_url));

			if ($CONFIG['PATH']=='') {
		
				$path = 'x321CvFFgd';
			
			} else {
		
				$path = $CONFIG['PATH'];
			
			}
		
			$tag_url = ereg_replace($path, "", urldecode($tag_url));				
		
			
		}
		
		if ($selected_user_key!='' && $added_for!=3) {
		
			$added_for=3;
			
		}
		
		$note_url = $note_url;
		
		switch ($added_for) {
		
			case 1:
		
				$added_for_key = $current_user_key;
			
			break;
			
			case 3:
			
				$added_for_key = $selected_user_key;
				
			break;			
			
			case 2: 
			
				$added_for_key = '-1';
				$space_key2 = $space_key;
				$group_key2 = $group_key;
				
			break;
			
			case 0:
			
				$space_key2 = $space_key;
				$group_key2 = $group_key;
		   
			break;			
						
		}

		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}tagged_urls(url, heading, note, added_for_key, added_by_key, date_added, space_key, group_key) VALUES ('$tag_url', '$heading', '$note', '$added_for_key', '$current_user_key', $date, '$space_key2', '$group_key2')";		
	
		if ($CONN->Execute($sql)===false) {
		
			return $CONN->ErrorMsg();
			
		} else {
		
			$url_key = $CONN->Insert_ID();
			
			//see if any tags to add
			if (isset($tag_data['tags']) && $tag_data['tags']!='') {
				if(!isset($objTags) || !is_object($objTags)) {
					if (!class_exists('InteractTags')){
						require_once($CONFIG['BASE_PATH'].'/includes/lib/tags.inc.php');
					}
					$objTags = new InteractTags();
				}	
				$objTags->addTags($tag_data['tags'], '',$current_user_key, '',$url_key);
				
			}			
			return true;
			
		}

	
	} //end addTag
	
 	/**
	* A method of class InteracturlTags to modify a url tag
	*
	* @param array $tag_data data for url tag to be modify
	* @param int $current_user_key key of user
	* @return true		
	*/
	
	function modifyurlTag($tag_data, $current_user_key) {
	
		global $CONN, $CONFIG;
		
		$url_key 			= $tag_data['url_key'];
		$added_for 			= $tag_data['added_for'];
		$selected_user_key	= $tag_data['selected_user_key'];
		$date	 			= $CONN->DBDate(date('Y-m-d H:i:s'));
		$heading 			= $tag_data['heading'];
		$note	 			= $tag_data['note'];
		
		if (isset($tag_data['external_url']) && $tag_data['external_url']!='') {
		
			if (strpos($tag_data['external_url'], 'http://')===false ) {
			
				$tag_url = 'http://'.$tag_data['external_url'];
			
			} else {
			
				$tag_url = $tag_data['external_url'];
			
			}
		
		}
		if ($selected_user_key!='' && $added_for!=3) {
		
			$added_for=3;
			
		}
		
		switch ($added_for) {
		
			case 1:
		
				$added_for_key = $current_user_key;
			
			break;
			
			case 3:
			
				$added_for_key = $selected_user_key;
				
			break;			
			
			case 2: 
			
				$added_for_key = '-1';
				$space_key2 = $space_key;
				$group_key2 = $group_key;
				
			break;
			
			case 0:
			
				$space_key2 = $space_key;
				$group_key2 = $group_key;
		   
			break;
			 			
		}

		if (isset($tag_url) && $tag_url!='') {
		
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}tagged_urls SET heading='$heading', note='$note', added_for_key='$added_for_key', modified_by_key='$current_user_key', date_modified=$date, url='$url' WHERE url_key='$url_key'";	
			
		} else {
		
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}tagged_urls SET heading='$heading', note='$note', added_for_key='$added_for_key', modified_by_key='$current_user_key', date_modified=$date WHERE url_key='$url_key'";			
		
		}	

		if ($CONN->Execute($sql)===false) {
		
			return $CONN->ErrorMsg();
			
		} else {
		
			//delete any existing tags
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}tag_links WHERE url_key='{$tag_data['url_key']}'");
			//see if any tags to add
			if (isset($tag_data['tags']) && $tag_data['tags']!='') {
				if(!isset($objTags) || !is_object($objTags)) {
					if (!class_exists('InteractTags')){
						require_once($CONFIG['BASE_PATH'].'/includes/lib/tags.inc.php');
					}
					$objTags = new InteractTags();
				}	
				$objTags->addTags($tag_data['tags'], '',$current_user_key, '',$tag_data['url_key']);
			}
			
			return true;
			
		}

	
	} //end modifyurlTag
		
	/**
	* A method of class InteracturlTags to get tags for given url
	*
	* @param string $request_uri uri of page to get tags for
	* @param int $space_key space_key of current space
	* @param int $current_user_key user_key of current user
	* @return true		
	*/
	
	function geturlTags($request_uri, $space_key, $current_user_key, $accesslevel_key, $group_accesslevel, $module_key) {
	
		global $CONN, $CONFIG, $t, $general_strings;
		
		$request_uri = preg_replace("/(&module_key=[0-9]*[^\"]*)/si", "$1", urldecode($request_uri));
		$request_uri = preg_replace("/&message=.*/si", "", urldecode($request_uri));

		if ($CONFIG['PATH']=='') {
		
			$path = 'x321CvFFgd';
			
		} else {
		
			$path = $CONFIG['PATH'];
			
		}

		$request_uri = ereg_replace($path, "", urldecode($request_uri));

		if ($accesslevel_key==1 || $group_accesslevel==1) {
		
			$sql = "SELECT  url_key, heading, note, added_by_key, added_for_key, date_added FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE url='$request_uri' AND (added_for_key='$current_user_key' OR added_by_key='$current_user_key' OR added_for_key='-1' OR added_for_key='0')";
		
		} else if ($_SESSION['userlevel_key']==1) {

			$sql = "SELECT  url_key, heading, note, added_by_key, added_for_key, date_added FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE url='$request_uri'";		
		
		} else {
		
			$sql = "SELECT url_key, heading, note, added_by_key, added_for_key, date_added FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE url='$request_uri' AND (added_for_key='$current_user_key' OR added_by_key='$current_user_key' OR added_for_key='-1')";
		
		}

		$rs = $CONN->Execute($sql);
		echo $CONN->ErrorMsg();
		if (!$rs->EOF) {
	
			$t->set_var('VIEW_TAGS', '<a href="#tag" title="'.$general_strings['view_tags'].'"><img src="'.$CONFIG['PATH'].'/images/tag.gif" border="0"></a>  ');
			
			//create user object so we can retrieve user details
			if (!class_exists('InteractUser')) {
			
				require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php'); 
				
			}
			$users = new InteractUser();
			
			//create date object for date functions
			if (!class_exists('InteractDate')) {

				require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
			
			}

			$dates = new InteractDate();
			
			while (!$rs->EOF) {
	
				$url_key = $rs->fields[0];
				$added_by_key = $rs->fields[3];
				$added_for_key = $rs->fields[4];								
				$t->set_var('TAG_HEADING', $rs->fields[1]);
				$t->set_var('TAG_NOTE', $rs->fields[2]);
				$user_data = $users->getUserData($added_by_key);
				$t->set_var('TAG_ADDED_BY', $user_data['first_name'].' '.$user_data['last_name']);
				$date_added = $dates->formatDate($CONN->UnixTimestamp($rs->fields[5]));
				$t->set_var('DATE_TAG_ADDED', $date_added);
				
				if ($this->checkurlTagEditRights($tag_key, $added_for_key, $added_by_key, $current_user_key, $accesslevel_key, $group_accesslevel)==true) {
				
					$t->set_var('EDIT_TOOL', get_admin_tool("{$CONFIG['PATH']}/urltags/urltaginput.php?space_key=$space_key&module_key=$module_key&url_key=$url_key&action=modify"));
				
				} else {
				
					$t->set_var('EDIT_TOOL', '');
				
				}
				
				$t->Parse('TGSBlock', 'TagBlock', true);
				
				$rs->MoveNext();
		
			}
		
		} else { 
			$t->set_var('VIEW_TAGS','');
		}
		
		return true;
		
	} //end getTags   
 
	/**
	* A method of class InteracturlTags to get sql for retrieving users for current space or group
	*
	* @param int $space_key space_key of current space
	* @param int $group_key of current module
	* @return string $user_sql sql string to retrieve userlist
	*/
	
	function getUserSql($space_key, $group_key) {
	
		global $CONN, $CONFIG;
		
		$concat = $CONN->Concat("{$CONFIG['DB_PREFIX']}users.last_name",'\', \'',"{$CONFIG['DB_PREFIX']}users.first_name");
		
		if ($group_key=='0' || $group_key=='') {
					
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}space_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND space_key='$space_key' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key!='3' AND {$CONFIG['DB_PREFIX']}users.level_key!='1' ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
			
		} else { 
			
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key' ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
			
		} 

		return $user_sql;
	
	} //end getUserSql

	/**
	* A method of class InteracturlTags to check tag edit rights
	*
	* @param int $tag_key key of current tag
	* @param int $added_for_key key of user tag added for
	* @param int $added_by_key key of user tag added by
	* @param int $current_user_key key of current user
	* @param int $accesslevel_key space access level of current user
	* @param int $group_accesslevel group access level of current user		
	* @return true
	*/
	
	function checkurlTagEditRights($tag_key, $added_for_key, $added_by_key, $current_user_key, $accesslevel_key, $group_accesslevel) {
	
		global $CONN;

		if ($_SESSION['userlevel_key']==1 || $added_by_key==$current_user_key || $added_for_key==$current_user_key || (($added_for_key==-1 || $added_for_key==0) && ($accesslevel_key==1 || $group_accesslevel==1) )) {
		
			return true;
			
		} else {
		
			return false;
			
		}
		
	} //end checkurlTagEditRights
	
	/**
	* A method of class Interacturltags to get data for a given url tag
	*
	* @param int $url_key key for note to retrieve data for
	* @return array $user_note_data array for data for given note		
	*/
	
	function geturlTagData($url_key) {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT url_key, heading, note, added_by_key, added_for_key, date_added, modified_by_key, date_modified, url FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE url_key='$url_key'");
	  
		if ($rs->EOF) {
	
			return false;
			
		} else {
			
			while (!$rs->EOF) {
	
				$tag_data['url_key']   		= $rs->fields[0];
				$tag_data['heading']		= $rs->fields[1];
				$tag_data['note']			= $rs->fields[2];
				$tag_data['added_by_key']	= $rs->fields[3];
				$tag_data['added_for_key']	= $rs->fields[4];
				$tag_data['date_added']		= $CONN->UnixTimestamp($rs->fields[5]);
				$tag_data['modified_by_key']= $rs->fields[6];
				$tag_data['date_modified']	= $CONN->UnixTimestamp($rs->fields[7]);
				$tag_data['tag_url']		= $rs->fields[8];																				
							
				$rs->MoveNext();
		
			}
		
			//now get any tags the url tag is attached to
            //put new tag functions here
			$tag_data['tag_keys'] = $tag_keys;
			
			return $tag_data;
		
		} 
		
	} //end geturlTagData		
	
	/**
	* A method of class InteractTags to delete a tag
	*
	* @param int $tag_key key for tag to delete
	* @return true		
	*/
	
	function deleteurlTag($url_key) {
	
		global $CONN, $CONFIG;
		
		if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE url_key='$url_key'")!=true) {
		
			return false;
			
		} else {
			
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}TagCategorylinks WHERE BookmarkKey='$url_key'");
			return true;
			
		}
		
		
		
	} //end deleteTag	
	
	/**
	* A method of class InteracturlTags to check form input
	*
	* @param string $heading heading of urltag
	* @param int $added_for who tag is added for
	* @param int $selected_user_key if for select user their userkey
	* @param array $tag_strings array of tag strings		
	* @return true		
	*/
	
	function checkFormInput($heading, $added_for, $selected_user_key, $tag_strings) {
	
		$errors = array();
		
		if ($heading=='') {
		
			$errors['heading'] = $tag_strings['no_heading'];
			
		} 
		
		if ($added_for==3 && $selected_user_key=='') {
		
			$errors['selected_user'] = $tag_strings['no_selected_user'];
		
		}
		
		return $errors;
	
	} //end checkFormInput	
		
} //end InteracturlTags
?>
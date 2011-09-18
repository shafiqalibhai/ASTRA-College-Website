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
* Tag  functions
*
* Contains any functions related to adding, modifying, displaying tags
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2005 
* @version $Id: tags.inc.php,v 1.14 2007/05/09 03:02:51 glendavies Exp $
* 
*/

/**
* A class that contains methods related to tag functions 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying tags
* 
* @package Tags
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractTags {

	
	/**
	* A method of class Tags to add a new tag, first checks to see if tag already exists
	* @param  string $tag_string a string of tags separated by character other than space
	* @return array $tag_array an array of individual tags 
	*/
	
	function splitTags($tag_string) {
	
		return preg_split("/,|;/", $tag_string);
		
	} //end splitTags()
	
	/**
	* A method of class Tags to add a new tag, first checks to see if tag already exists
	* @param  string $text text string of tag to add
	* @return int $tag_key key of new tag 
	*/
	
	function addTag($text) {
	
		global $CONN, $CONFIG;

		//first see if tag already exists
		$text = strip_tags($text);
		$tag_key = $this->getTagKey($text);
		if ($tag_key!=false || empty($text)) {
			return $tag_key;	
		} else {
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}tags(text) VALUES ('$text')");
			
			return $CONN->Insert_ID();
		}	
		
	} //end addTag()

	/**
	* A method of class Tags to get tag_key for an existing tag
	* @param  string $text text string to get tag key for
	* @return int $tag_key of tag, or false if tag does not exist 
	*/
	
	function getTagKey($text) {
	
		global $CONN, $CONFIG;

		
		$tag_key = $CONN->GetOne("SELECT tag_key FROM {$CONFIG['DB_PREFIX']}tags WHERE text='$text'");
		return $tag_key;	
		
	} //end getTagKey()
	/**
	* A method of class Tags to get tag_text for an existing tag
	* @param  int $tag_key text key to get tag key for
	* @return string $text of tag, or false if tag does not exist 
	*/
	
	function getTagText($tag_key) {
	
		global $CONN, $CONFIG;
		
		return $CONN->GetOne("SELECT text FROM {$CONFIG['DB_PREFIX']}tags WHERE tag_key='$tag_key'");
		
	} //end getTagKey()	

	/**
	* A method of class Tags to get tag_key for an existing tag
	* @param  int $tag_key tag_key of tag to add link for
	* @param  int $module_key key of module to add tage for
	* @param  int $user_key key of user to add tag for
	* @param  int $entry_key key of individual module entry to add tag for
	* @param  int $url_key key of url to add tag for * 
	* @return true  
	*/
	
	function addTaglink($tag_key, $module_key=0, $user_key=0, $entry_key=0, $url_key=0) {
	
		global $CONN, $CONFIG;

		$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}tag_links(tag_key, module_key, user_key, entry_key, url_key, date_added) VALUES ('$tag_key', '$module_key', '$user_key', '$entry_key', '$url_key', $date_added)");
		return true;	
		
	} //end addTaglink()
	
	/**
	* A method of class Tags to add a string of tags
	* @param  int $tag_string string of tags to add
	* @param  int $module_key key of module to add tage for
	* @param  int $user_key key of user to add tag for
	* @param  int $entry_key key of individual module entry to add tag for	
	* @param  int $url_key key of url to add tag for 
	* @return true  
	*/
	
	function addTags($tag_string, $module_key=0, $user_key=0, $entry_key=0, $url_key=0) {
	
		global $CONN, $CONFIG;

		$tag_array = $this->splitTags($tag_string);
		if (is_array($tag_array)) {
			$count = count($tag_array);
			for ($i=0; $i<$count; $i++) {
				$tag_key = $this->addTag(trim(strtolower($tag_array[$i])));
				$this->addTaglink($tag_key, $module_key, $user_key, $entry_key, $url_key);
			}	
		}
		return true;	
		
	} //end addTags()
	
	/**
	* A method of class Tags to get an array of tags for given user or module
	* @param  int $module_key key of module to get tags for
	* @param  int $user_key key of user to get tags for
	* @param  int $entry_key key of individual module entry to get tags for
	* @return true  
	*/
	
	function getTags($module_key='', $user_key='', $entry_key='', $url_key='') {
	
		global $CONN, $CONFIG;

		if (isset($module_key) && $module_key!='') {
			$module_limit = "AND module_key='$module_key'";	
		} else {
			$module_limit = '';
		}
		if (isset($user_key) && $user_key!='') {
			$user_limit = "AND user_key='$user_key'";	
		} else {
			$user_limit = '';
		}
		if (isset($entry_key) && $entry_key!='') {
			$entry_limit = "AND entry_key='$entry_key'";	
		} else {
			$entry_limit = '';
		}
		if (isset($url_key) && $url_key!='') {
			$url_limit = "AND url_key='$url_key'";	
		} else {
			$url_limit = '';
		}
		$CONN->SetFetchMode('ADODB_FETCH_ASSOC');

		
		return $CONN->GetArray("SELECT {$CONFIG['DB_PREFIX']}tags.tag_key, text, COUNT(*) as count FROM {$CONFIG['DB_PREFIX']}tags, {$CONFIG['DB_PREFIX']}tag_links WHERE {$CONFIG['DB_PREFIX']}tags.tag_key={$CONFIG['DB_PREFIX']}tag_links.tag_key $module_limit $user_limit $entry_limit $url_limit GROUP BY {$CONFIG['DB_PREFIX']}tags.tag_key ORDER BY count DESC");
		
		$CONN->SetFetchMode('ADODB_FETCH_NUM');
				
	} //end getTags()

}
?>
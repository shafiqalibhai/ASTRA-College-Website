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
* Journal Class
*
* Contains the Journal class for all methods and datamembers related
* to adding, modifying and viewing journal entries
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.34 2007/05/15 03:29:24 glendavies Exp $
* 
*/

/**
* A class that contains methods for retieving and displaying and updating 
* journal entries
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying journal entries 
* 
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractJournal {

	/**
	* space key of current journal
	* @access private
	* @var int 
	*/
	var $_space_key = '';

	/**
	* module key of current journal
	* @access private
	* @var int 
	*/
	var $_module_key = '';
	
	/**
	* group key of current journal
	* @access private
	* @var int 
	*/
	var $_group_key = '';
	
	/**
	* user key of current user
	* @access private
	* @var int 
	*/
	var $_user_key = '';
	
	/**
	* admin status of current user
	* @access private
	* @var true/false 
	*/
	var $_is_admin = '';
	
	/**
	* settings for current journal
	* @access private
	* @var array 
	*/
	var $_journal_settings = '';
	
	/**
	* userkey of user whose journal is to be displayed/edited
	* @access private
	* @var int 
	*/
	var $_journal_user_key = '';
	
	/**
	* array of language strings for journal module
	* @access private
	* @var array 
	*/
	var $_journal_strings = '';
	
	/**
	* Constructor for Journal Class. Sets required variables
	*
	* @param  int $space_key  key of current space
	* @param  int $module_key  key of current module	
	* @param  int $group_key  key of current group
	* 
	*/
	
	function InteractJournal($space_key,$module_key,$group_key,$is_admin,$journal_strings) {
	
		$this->_space_key		= $space_key;
		$this->_module_key		= $module_key;
		$this->_group_key		= $group_key;
		$this->_is_admin		= $is_admin;
		$this->_journal_strings = $journal_strings;				
		$this->_user_key   = $_SESSION['current_user_key'];						
		
	} //end InteractJournal
	
	
	/**
	* A method of class Jounnal to get settings for current journal
	*
	*/
	
	function setJournalSettings() {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT options, start_date,finish_date, entries_to_show FROM {$CONFIG['DB_PREFIX']}journal_settings WHERE module_key='$this->_module_key'");
		
		if ($rs->EOF) {
			die("There was a problem retrieving the journals settings");
		} else {
			while (!$rs->EOF) {
				$options=$rs->fields[0];
				$this->_journal_settings['access'] = ($options& 1? 'open':'restricted');
				$this->_journal_settings['members']= ($options& 2? 'selected':'all');
				$this->_journal_settings['show_comments'] = ($options&4? '1':'0');
				$this->_journal_settings['default_display'] = ($options&8?  'show_all':'show_separate');
				$this->_journal_settings['edit_rights'] = ($options&16? 'own':'all');
				$this->_journal_settings['enable_rss'] = ($options&32? 1:0);
				if($options&64) {
					$this->_journal_settings['allow_comments'] = 'from_anyone';
				} else if ($options&128) {
					$this->_journal_settings['allow_comments'] = 'no';
				} else {
					$this->_journal_settings['allow_comments'] = 'from_logged_in_users';
				}
				$this->_journal_settings['start_date']  = $rs->fields[1];
				$this->_journal_settings['finish_date'] = $rs->fields[2];
				$this->_journal_settings['entries_to_show'] = $rs->fields[3];
				$rs->MoveNext();								
			}
			$rs->Close();
			//now get array of users if journal for selected users only
			if ($this->_journal_settings['members']=='selected') {
				$this->_journal_settings['selected_user_keys'] = $CONN->GetCol("SELECT user_key FROM {$CONFIG['DB_PREFIX']}journal_user_links WHERE module_key='$this->_module_key'");
			} else {
				$this->_journal_settings['selected_user_keys'] = array();
			}
		}
	} //end setJournalSettings

	/**
	* A method of class Jounnal to return settings for current journal
	*
	* @return array an array of settings for current journal
	*/
	
	function getJournalSettings() {
	
		return $this->_journal_settings;
	
	}
	
	/**
    * A method of class Journal to get sql for retrieving users for current space or group
	*
    * @return string $user_sql sql string to retrieve userlist
	*/
	
    function getUserSql() {
	
		global $CONN, $CONFIG;
		
		$concat = $CONN->Concat("{$CONFIG['DB_PREFIX']}users.last_name",'\', \'',"{$CONFIG['DB_PREFIX']}users.first_name");
		if ($this->_journal_settings['members']=='all'){
			if ($this->_space_key==$CONFIG['DEFAULT_SPACE_KEY']){ 
				$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}users.account_status='1'";
			} else if ($this->_group_key=='0') {
				$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}space_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND space_key='$this->_space_key' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key!='3'";
			} else { 
		    	$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$this->_group_key'";
			} 
		} else {
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}journal_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}journal_user_links WHERE {$CONFIG['DB_PREFIX']}journal_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}journal_user_links.module_key='$this->_module_key' ";
		} 
		
		return $user_sql;
	
	} //end getUserSql
	
	/**
    * A method of class Journal to get a list of users for current space or group
	*
	* @return array $user_keys an array of user_keys for given journal  
	* 
    */
	
    function getuser_keys() {
	
	    global $CONN;
		
		$user_sql = $this->getUserSql();

		$rs = $CONN->Execute($user_sql);

		if ($rs->EOF) {
		
		    return false;
			
		} else {
		
		    $user_keys = array();
			
			while (!$rs->EOF) {
			
			   array_push($user_keys,$rs->fields[1]);
			   $rs->MoveNext(); 
			
			}
			
			$rs->Close();
			return $user_keys;

        }
	
	} //end getuser_keys
	
	/**
	* A method of class Jounnal return an sql limit for current journal users
	*
	* @return string sql in limit or false if no current users
	*/
	
	function getUserLimit() {
	
		$user_keys = $this->getuser_keys();
		if ($user_keys === false) {
			return false;
		} else {
			$count = count($user_keys);
			$user_limit = '(';
			for ($i=0;$i<$count;$i++) {
				if ($i==0) {
					$user_limit .= $user_keys[$i];
				} else {
					$user_limit .= ','.$user_keys[$i];
				}	
			
			}
			$user_limit .= ')';
			return $user_limit;
		}
	
	} //end getUserLimit()
	
	/**
	* A method of class Jounnal to see if user has right to view other journals
	*
	* @return true if user is admin for current journal, or journal type Open
	*/
	
	function checkShowAll() {
	
		if ($this->_is_admin==true || $this->_journal_settings['access']=='open') {

			return true;
	
		} else {

			return false;
	
		}
	

	} //end checkShowAll()
	
	/**
	* A method of class Jounnal to see if user has right to edit an entry or comment 
	*
	* @return true if user is allowed to edit current post
	*/
	
	function checkEntryEditRights($post_key) {
	
		global $CONN, $CONFIG;
		
		
		$rs = $CONN->Execute("SELECT added_by_key, user_key FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'");
		if ($this->_is_admin==true || $this->_user_key== $rs->fields[0]) {
			return true;
		} else if ($rs->fields[1]==$this->_user_key && $this->_journal_settings['edit_rights'] == 'all'){
			return true;
		} else {
			return false;	
		}
			
				
	

	} //end checkEntryEditRights()	

	/**
	* A method of class Jounnal to see if user has right to add comments 
	*
	* @return true if user is admin for current journal, or journal type Open, or user owns journal
	*/
	
	function checkCommentEditRights($action,$comment_key) {
	
		global $CONN;
		
		if($action=='add' || $action=='' || $action=='reply' || $action=='reply_quoted') {
		
			if ($this->_is_admin==true || $this->_journal_settings['type']=='Open' || $this->_journal_user_key==$this->_user_key) {

				return true;
	
			} else {

				return false;
	
			}
			
		} else if ($action=='modify' || $action=='modify2') {
		
			$this->setCommentData($comment_key);
			$date_now = mktime();
			$editable_date = $date_now-1800;
		
			if ($this->_is_admin==true || ($this->_comment_data['user_key']==$this->_user_key && $CONN->UnixTimeStamp($this->_comment_data['date_added'])>$editable_date)) {
			
				return true;
				
			} else {
			
				return false;
				
			}
				
		} else {
		
			return false;
			
		}
				
	

	} //end checkCommentEditRights()	

	/**
	* A method of class Jounnal to see if user has rights to add entries to
	* current journal
	*
	* @param string $action current action being undertaken add or modify
 	* @return true if user is admin for current journal, journal belongs to user
	*/
	
	function checkJournalEditRights($action,$entry_key='') {
	
	  
		global $CONN;
		
		if ($action=='add' || $action=='') {
		
			if ($this->_is_admin==true || $this->_user_key==$this->_journal_user_key || $this->_user_key==$this->_journal_user_key) {

				return true;
	
			} else {

				return false;
	
			}
			
		} else if ($action=='modify' || $action=='modify2') {
		
			$this->setEntryData($entry_key);
			$date_now = mktime();
			$editable_date = $date_now-1800;
		
			if ($this->_is_admin==true || ($this->_entry_data['user_key']==$this->_user_key && $CONN->UnixTimeStamp($this->_entry_data['date_added'])>$editable_date && $this->_entry_data ['admin_key']=='0')) {
			
				return true;
				
			} else {
			
				return false;
				
			}
				
		} else {
		
			return false;
			
		}
		
	} //end checkJournalEditRights()
	

	/**
	* A method of class Jounnal to set key for journal to be displayed/edited
	*
	* @param int $journal_user_key userkey of user whose journal to be edited
	*/
	
	function setJournaluser_key($journal_user_key) {
	
		$this->_journal_user_key = $journal_user_key;  

	} //end setJournaluser_key()	
	
	/**
	* A method of class Jounnal to get journal entries for current journal
	* @param  int $module_key action being taken, eg. add, modify
	* @return int $journal_entries number of journal entries for display_key user 
	*/
	
	function countJournalEntries($module_key, $user_key) {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key' AND user_key='$user_key'");
		
		return $rs->RecordCount();
		
	} //end countJournalEntries()
	

	/**
	* A method of class Journal to delete multiple journal entries
	*
	* @param string $multiple_add all or selected
	* @param array $user_keys keys journals to delete entry from
	* @return true if successful, false if not
	*/
	
	function multipleDeleteEntry($multiple_add, $user_keys, $multiple_entry_key) {
	
	
		global $CONN, $CONFIG;

			if ($multiple_add=='all') {
		
				$user_keys = $this->getUserList();
	
			}

			foreach($user_keys as $key => $value) {
			
				if ($multiple_add=='selected') {
			
					$user_key = $value;
			
				} else {
		 
					$user_key = $key;
			 
				}			
				
				$rs = $CONN->Execute("SELECT JournalEntryKey FROM {$CONFIG['DB_PREFIX']}JournalEntries WHERE MultiEntryKey='$multiple_entry_key' AND user_key='$user_key'");

				while(!$rs->EOF) {
				
					$this->deleteEntry($rs->fields[0]);
					$rs->MoveNext();
					
				}
					
			}
			
			return true;
		
	} //end multipleDeleteEntry		
	
	
	/**
	* A method of class Journal to check for multiple copies of an entry
	* 
	* @param int $entry_key key of entry to count entries for
	* @return array of userkeys that have this entry and multientrykey, or false if single entry
	*/
	function checkMultipleEntries($entry_key) {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT MultiEntryKey FROM {$CONFIG['DB_PREFIX']}JournalEntries WHERE JournalEntryKey='$entry_key'");
		
		while (!$rs->EOF) {
		
			$multi_entry_key = $rs->fields[0];
			$rs->MoveNext();
			
		}  
		
		if ($multi_entry_key==0) {
		
			return false;
			
		} else {
		
			$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}JournalEntries WHERE MultiEntryKey='$multi_entry_key'");
		
			if ($rs->RecordCount()<=1) {
		
				return false;
			
			} else {
		
				$multi_entry_data = array();
				$user_keys = array();
				$n = 0;
			
				while (!$rs->EOF) {
		
					$user_keys[$n] = $rs->fields[0];
					$n++;
					$rs->MoveNext();
			
				}
			
				$multi_entry_data['multiple_entry_key'] = $multi_entry_key;
				$multi_entry_data['user_keys'] = $user_keys;
			
				return $multi_entry_data;
				
			}
			
		}  		
	
	} // end checkMultipleEntries
	
	/**
	* A method of class Journal to count the number of new entries for a user
	* 
	*
	* @param int $user_key key of entry to count comments for
	* @return int $total total number of new entries
	*/
	
	function countNewEntries($user_key, $module_key) {
	
	
		global $CONN, $CONFIG;

		$last_use=$_SESSION['last_use'];
		
		$rs  = $CONN->Execute("SELECT COUNT(JournalEntryKey) FROM {$CONFIG['DB_PREFIX']}JournalEntries WHERE date_added>'$last_use' AND user_key='$user_key' AND module_key='$module_key'");
		
		if ($rs->EOF) {
		
			return 0;
		
		} else {
		
			while (!$rs->EOF) {
			
				$total = $rs->fields[0];
				$rs->MoveNext();
				
			}
			
			$rs->Close();
			return $total;
		
		}
 		
	} //end countNewEntries	
	
	function getSideBar($module_key, $post_key='',$posts_array, $journal_user_key, $sort_order='DESC') {
	
		global $CONN, $CONFIG, $t, $general_strings;
		
		if (!empty($_SESSION['current_user_key']) && ($this->_is_admin==true || $this->_journal_user_key==$this->_user_key)) {
		
			
			$add_links_link = get_admin_tool($CONFIG['PATH']."/modules/journal/linkinput.php?space_key=$this->_space_key&module_key=$this->_module_key&journal_user_key=$this->_journal_user_key",true,"Add/Modify a link",'plus');
			$t->set_var('ADD_LINK',$add_links_link);
		} else {
			$t->set_var('ADD_LINK','');
		}
		//now get any tags
		$objUser = singleton::getInstance('user');
		$objDate = singleton::getInstance('date');
		$objTags = singleton::getInstance('tags');

		$tag_array = $objTags->getTags($module_key,$this->_journal_user_key);

		$t->set_block('journal', 'JournalTagsBlock', 'JournlTgsBlock');
		$t->set_var('TAG_KEY','0');
		$t->set_var('TAG_NAME','All');
		
		$base_rss=$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH'].'rss/'.$this->_space_key.'/'.$this->_module_key.($journal_user_key?'/'.$journal_user_key:'');
		
		if ($this->_journal_settings['enable_rss']==1) {
			$t->set_var('RSS_LINK','<div class="rssLink"><a href="'.$base_rss.'" class="small" title="'.$general_strings['rss_all'].'"><img src="'.$CONFIG['PATH'].'/images/feedreader.gif" width="16" height="16" border="0" align="bottom"> RSS</a></div>');
		}
		$t->parse('JournlTgsBlock', 'JournalTagsBlock', true);
		
		$count = count($tag_array);
		for ($i=0;$i<$count;$i++) {
	
			$t->set_var('TAG_KEY',$tag_array[$i]['tag_key']);
			$t->set_var('TAG_NAME',$tag_array[$i]['text'].' ('.$tag_array[$i]['count'].')');
			
			$t->parse('JournlTgsBlock', 'JournalTagsBlock', true);
	
		}
		
		//now get any links
		$t->set_block('journal', 'JournalLinksBlock', 'JournalLnksBlock');
 		if (isset($this->_journal_user_key) && $this->_journal_user_key!='') {
 			$journal_user_limit = 'AND journal_user_key=\''.$this->_journal_user_key.'\'';
 		}
		$sql = "SELECT url, name FROM  {$CONFIG['DB_PREFIX']}journal_links WHERE  module_key='$this->_module_key' $journal_user_limit ORDER BY name";
		$rs = $CONN->Execute($sql);
		while (!$rs->EOF) {
	
			if (strpos($rs->fields[0], 'http://')===false) {
				
				$url = 'http://'.$rs->fields[0];
				
			} else {
	
				$url = $rs->fields[0];
	
			}
			
			$t->set_var('LINK_URL',$url);
			$t->set_var('LINK_NAME',$rs->fields[1]);
			$t->parse('JournalLnksBlock', 'JournalLinksBlock', true);
			$rs->MoveNext();
	
		}		

		//now get archives
		$t->set_block('journal', 'ArchiveLinksBlock', 'ArchiveLinks');
 		if (isset($this->_journal_user_key) && $this->_journal_user_key!='') {
 			$journal_user_limit = 'AND user_key=\''.$this->_journal_user_key.'\'';
 		}
		$sql = "SELECT YEAR( date_published ) , MONTH( date_published ) , COUNT( * ) AS count
FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key = '$this->_module_key' AND parent_key=0 $journal_user_limit GROUP BY YEAR( date_published ) , MONTH( date_published ) ORDER BY date_published $sort_order";

		$rs = $CONN->Execute($sql);
		if ($rs->EOF) {
			$t->set_block('journal', 'JournalArchivesBlock', 'JournalArchive');
			$t->set_var('JournalArchive','');
		} else {
			require_once($CONFIG['LANGUAGE_CPATH'].'/calendar_strings.inc.php');
			$months = array('',$calendar_strings['jan_abb'],$calendar_strings['feb_abb'],$calendar_strings['mar_abb'],$calendar_strings['apr_abb'],$calendar_strings['may_abb'],$calendar_strings['jun_abb'],
$calendar_strings['jul_abb'],$calendar_strings['aug_abb'],$calendar_strings['sep_abb'] = 'Sep',$calendar_strings['oct_abb'],$calendar_strings['nov_abb'],$calendar_strings['dec_abb']);
			while (!$rs->EOF) {
				$t->set_var('DATE_LIMIT',$rs->fields[0].'-'.$rs->fields[1]);
				$t->set_var('ARCHIVE_NAME',$objDate->convertMonthNumtoTxt($rs->fields[1]).' '.$rs->fields[0]);
				$t->set_var('ARCHIVE_COUNT',$rs->fields[2]);
				$t->parse('ArchiveLinks', 'ArchiveLinksBlock', true);
				$rs->MoveNext();
	
			}	
		}	

	} //end getSideBar

	/**
	* Check form input for a inputing a link
	*

	* @return array $errors array of any errors found
	* 
	*/
	
	function checkFormlink($link_name, $link_url) {
	
		global $general_strings;
		$errors = array();
		
		//check that we have name
		if (!$link_name | $link_name=='') {
		
			$errors['name'] = $general_strings['no_name'];
		
		}
		//check that we have url
		if (!$link_url | $link_url=='') {
		
			$errors['url'] = 'You did not enter a url';
		
		}		
		
		return $errors;
							
		
	} //end checkFormlink
	
	/**
	* Add a new link
	*
	* @param  int $link_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  int $user_key  key of user to add category for
	* @param  string $category_name  name field of category		
	* @return array $errors array of any errors found
	* 
	*/
	
	function addlink($link_name, $link_url, $type_key) {
	
		global $CONN, $CONFIG;


		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}journal_links(module_key, journal_user_key, name, url) VALUES ('$this->_module_key', '$this->_journal_user_key', '$link_name', '$link_url')");
		echo  $CONN->ErrorMsg();
		$link_key = $CONN->Insert_ID();
			
		return $link_key;
						
	} //end addlink
	
	/**
	* Get name and parent data for a given category
	*
	* @param  int $category_key  key of category
	* @param  string $category_name  name field of category		
	* @return array $category_data array of category data
	* 
	*/
	
	function getlinkData($link_key) {
	
		global $CONN, $CONFIG;

		$rs = $CONN->Execute("SELECT name, url FROM {$CONFIG['DB_PREFIX']}journal_links WHERE link_key='$link_key'");
		
		while (!$rs->EOF) {

			$link_data['name']	= $rs->fields[0];
			$link_data['url']	= $rs->fields[1];
			$rs->MoveNext();
			
		}
		
		$rs->Close();	
		return $link_data;
						
	} //end getlinkData		

	/**
	* modify and existing link
	*
	* @param  int $category_key  key of category
	* @param  string $category_name  name of category to modify
	* @param  int $parent_key  key of parent category
	* @param  int $user_key  key of user to add category for	
	* @param  string $category_name  name field of category
	* @return true
	* 
	*/
	
	function modifylink($link_key, $link_name, $link_url) {
	
		global $CONN, $CONFIG;


		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}journal_links SET name='$link_name', url='$link_url' WHERE link_key='$link_key'");

		return true;
						
	} //end modifylink
	
	/**
	* Delete a link
	*
	* @param  int $link_key  key of link to delete
	* @return true 
	* 
	*/
	
	function deletelink($link_key) {
	
		global $CONN, $CONFIG;
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}journal_links WHERE link_key='$link_key'");
		
		
		return true ;
		
	} //end deletelink()				
		
					
} //end Journal class	
?>
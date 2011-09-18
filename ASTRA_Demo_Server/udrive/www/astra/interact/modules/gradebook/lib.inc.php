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
* Interactgradebook Class
*
* Contains the gradebook class for all methods and datamembers related
* to adding, modifying and viewing journal entries
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.33 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* A class that contains methods for retieving and displaying and updating 
* journal entries
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying journal entries 
* 
* @package gradebooks
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class Interactgradebook {

	/**
	* space key of current gradebook
	* @access private
	* @var int 
	*/
	var $_space_key = '';

	/**
	* module key of current gradebook
	* @access private
	* @var int 
	*/
	var $_module_key = '';
	
	/**
	* group key of current gradebook
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
	* settings for current gradebook
	* @access private
	* @var array 
	*/
	var $_gradebook_settings = '';
	
	/**
	* userkey of user whose gradebook is to be displayed/edited
	* @access private
	* @var int 
	*/
	var $_gradebook_user_key = '';
	
	/**
	* array of language strings for gradebook module
	* @access private
	* @var array 
	*/
	var $_gradebook_strings = '';
	
		
	
	/**
	* Constructor for Interactgradebook Class. Sets required variables
	*
	* @param  int $space_key  key of current space
	* @param  int $module_key  key of current module	
	* @param  int $group_key  key of current group
	* 
	*/
	
	function Interactgradebook($space_key,$module_key,$group_key,$is_admin,$gradebook_strings) {
	
		$this->_space_key		 = $space_key;
		$this->_module_key		= $module_key;
		$this->_group_key		 = $group_key;
		$this->_is_admin		  = $is_admin;
		$this->_gradebook_strings = $gradebook_strings;				
		$this->_user_key   = $_SESSION['current_user_key'];						
		
	} //end gradebook
	
	/**
	* A method of class gradebook to get a list of users for current space or group
	*
	* @return array $user_list key=user_key value=full name or false if no users  
	* 
	*/
	
	function getUserList() {
	
		global $CONN;
		
		$user_sql = $this->getUserSql();

		$rs = $CONN->Execute($user_sql);

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			$user_name = array();
			$user_number = array();
			
			while (!$rs->EOF) {
			
			   $user_name[$rs->fields[1]] = $rs->fields[0];
			   $user_number[$rs->fields[1]] = $rs->fields[2];
			   $rs->MoveNext(); 
			
			}
			
			$rs->Close();
			asort($user_name);
			$user_data['by_name']   = $user_name;
			$user_data['by_number'] = $user_number;			
			return $user_data;

		}
	
	} //end getUserList
	
	/**
	* A method of class gradebook to get sql for retrieving users for current space or group
	*
	* @return string $user_sql sql string to retrieve userlist
	*/
	
	function getUserSql() {
	
		global $CONN, $CONFIG;
		
		$concat = $CONN->Concat("{$CONFIG['DB_PREFIX']}users.last_name",'\', \'',"{$CONFIG['DB_PREFIX']}users.first_name");
		
		if ($this->_group_key=='0') {
					
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}space_user_links.user_key, {$CONFIG['DB_PREFIX']}users.user_id_number FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND space_key='$this->_space_key' AND ({$CONFIG['DB_PREFIX']}space_user_links.access_level_key!='1' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key!='3' AND {$CONFIG['DB_PREFIX']}users.level_key!='1')";
			
		} else { 
			
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}group_user_links.user_key, {$CONFIG['DB_PREFIX']}users.user_id_number  FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$this->_group_key' AND ({$CONFIG['DB_PREFIX']}group_user_links.access_level_key!='1')";
			
		} 
		
		return $user_sql;
	
	} //end getUserSql
	

	/**
	* A method of class gradebook to get an array of gradebook items for current gradebook
	*
	* @return array $item_list key=item_key value=item name or false if no items  
	* 
	*/
	
	function getItemList() {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT item_key, name FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE module_key='$this->_module_key' ORDER BY sort_order, name");

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			$item_list = array();
			
			while (!$rs->EOF) {
			
			   $item_list[$rs->fields[0]] = $rs->fields[1];
			   $rs->MoveNext(); 
			
			}
			
			$rs->Close();
				
			return $item_list;

		}
	
	} //end getItemList
	
	/**
	* A method of class Jounnal to set key for journal to be displayed/edited
	*
	* @param int $journal_user_key userkey of user whose journal to be edited
	*/
	
	function setgradebookuser_key($journal_user_key) {
	
		$this->_journal_user_key = $journal_user_key;  

	} //end setgradebookuser_key()	
	
	
	/**
	* A method of class Jounnal to check form input of entry
	*
	* @param string $name text of journal entry
	* @param array $journal_strings array of journal related strings	
	* @return arrary $errors an array of any errors found
	*/
	
	function checkFormInput($name, $weighting, $item_key='') {
	
	
		$errors = array();
		
		if ($name=='') {
		
		   $errors['name'] = $this->_gradebook_strings['no_name']; 
		
		} 

		//make sure we don't have more than 100% for weightings
		
		$current_weightings = $this->calculateweightingTotal($this->_module_key, $item_key);
		
		if ($weighting+$current_weightings>100) {
		
			$errors['weighting'] = $this->_gradebook_strings['weighting_error']; 
			
		}
		
		return $errors;
	
	}//end checkFormInput
	
	/**
	* A method of class gradebook to add a new item
	*
	* @param int $module_key module key of gradebook item is being added to
	* @param array $item_data name of item - name, body, url, due date
	* scale_key, sort_order, maximum_score, weighting
	* @return true/false
	*/
	
	function addItem($module_key, $item_data) {
	
		global $CONN, $CONFIG;
		
		$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));
		$due_date	  = $CONN->DBDate($item_data['due_date']);		
		$name		  = $item_data['name'];
		$description   = $item_data['description'];
		$url		   = $item_data['url'];
		$maximum_score = $item_data['maximum_score'];
		$weighting	 = $item_data['weighting'];
		$scale_key	 = $item_data['scale_key'];
		$sort_order	= $item_data['sort_order'];		
		$item_status_key	= isset($item_data['item_status_key'])?$item_data['item_status_key']:1;						
		preg_match("/(module_key=)([0-9]*)/i", $url, $matches);
		$content_module_key = $matches[2];
				
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}gradebook_items(module_key, scale_key, name, description, url, content_module_key, due_date, sort_order, maximum_score, weighting, date_added, added_by_key, status_key) VALUES ('$module_key', '$scale_key', '$name', '$description', '$url', '$content_module_key', $due_date, '$sort_order', '$maximum_score', '$weighting', $date_added, '$this->_user_key','$item_status_key')";
		
		if ($CONN->Execute($sql)===false) {
		
			$message =  'There was an error adding your entry'.$CONN->ErrorMsg().' <br />';
			
			return $message;
			
		} else {
		
			return true;
			
		}		
	
	}//end addItem
	
	/**
	* A method of class gradebook to modify an item
	*
	* @param int $item_key item key of gradebook item being modified
	* @param array $item_data name of item - name, body, url, due date
	* scale_key, sort_order, maximum_score, weighting
	* @return true/false
	*/
	
	function modifyItem($item_key, $item_data) {
	
		global $CONN, $CONFIG;
		
		$date_modified = $CONN->DBDate(date('Y-m-d H:i:s'));
		$due_date	  = $CONN->DBDate($item_data['due_date']);		
		$name		  = $item_data['name'];
		$description   = $item_data['description'];
		$url		   = $item_data['url'];
		$maximum_score = $item_data['maximum_score'];
		$weighting	 = $item_data['weighting'];
		$scale_key	 = $item_data['scale_key'];
		$sort_order	= $item_data['sort_order'];		
		$item_status_key	= isset($item_data['item_status_key'])?$item_data['item_status_key']:1;
		preg_match("/(module_key=)([0-9]*)/i", $url, $matches);
		$content_module_key = $matches[2];
				
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}gradebook_items SET scale_key='$scale_key', name='$name', description='$description', url='$url', content_module_key='$content_module_key', due_date=$due_date, sort_order='$sort_order', maximum_score='$maximum_score', weighting='$weighting', date_modified=$date_modified, modified_by_key='$this->_user_key', status_key='$item_status_key' WHERE item_key='$item_key'";
	
		if ($CONN->Execute($sql)===false) {
		
			$message =  'There was an error modifying your item'.$CONN->ErrorMsg().' <br />';
		   	return $message;
			
		} else {
		
			return true;
			
		}		
	
	}//end modifyItem
	
	/**
	* A method of class gradebook to modify an item
	*
	* @param int $item_key item key of gradebook item to delete
	* @return true/false
	*/
	
	function deleteItem($item_key) {
	
		global $CONN, $CONFIG;
		
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE item_key='$item_key'";
		
		if ($CONN->Execute($sql)===false) {
		
			$message =  'There was an error deleting your item'.$CONN->ErrorMsg().' <br />';
		   	return $message;
			
		} else {
		
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE item_key='$item_key'";
		
			if ($CONN->Execute($sql)===false) {
		
				$message =  'There was an error deleting your item'.$CONN->ErrorMsg().' <br />';
		   		return $message;
			
			} else {
			
				return true;
			
			}
			
		}		
	
	}//end deleteItem		
	

	/**
	* A method of class gradebook to get data for an item
	*
	* @param int $item_key key of item
	*/
	
	function getItemData($item_key) {
	
		global $CONN, $CONFIG, $user;
		
		$sql = "SELECT {$CONFIG['DB_PREFIX']}gradebook_items.module_key,{$CONFIG['DB_PREFIX']}gradebook_items.scale_key, {$CONFIG['DB_PREFIX']}gradebook_items.name, {$CONFIG['DB_PREFIX']}gradebook_items.description,{$CONFIG['DB_PREFIX']}gradebook_items.url, {$CONFIG['DB_PREFIX']}gradebook_items.sort_order, {$CONFIG['DB_PREFIX']}gradebook_items.due_date,{$CONFIG['DB_PREFIX']}gradebook_items.maximum_score,{$CONFIG['DB_PREFIX']}gradebook_items.weighting,{$CONFIG['DB_PREFIX']}gradebook_items.added_by_key,{$CONFIG['DB_PREFIX']}gradebook_items.date_added, {$CONFIG['DB_PREFIX']}gradebook_items.modified_by_key, {$CONFIG['DB_PREFIX']}gradebook_items.date_modified, {$CONFIG['DB_PREFIX']}gradebook_items.status_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE {$CONFIG['DB_PREFIX']}gradebook_items.item_key='$item_key'";
			
		$rs = $CONN->Execute($sql);
		
		if ($rs->EOF) {
		
			die('There was a problem retrieving item data');
			
		} else {
		
			while (!$rs->EOF) {
			
				$item_data['item_key']	  = $item_key;
				$item_data['module_key']	= $rs->fields[0];
				$item_data['scale_key']	 = $rs->fields[1];				
				$item_data['name']			= $rs->fields[2];
				$item_data['description']   = $rs->fields[3];
				$item_data['url']			= $rs->fields[4];
				$item_data['sort_order']	= $rs->fields[5];
				$item_data['due_date_unix'] = $CONN->UnixTimestamp($rs->fields[6]);					
				$item_data['maximum_score'] = $rs->fields[7];
				$item_data['weighting']		= $rs->fields[8];
				$item_data['add_by']   		= $rs->fields[9];				
				$item_data['date_added']	= $CONN->UnixTimestamp($rs->fields[10]);
				$item_data['modified_by']	= $rs->fields[11];				
				$item_data['date_modified'] = $CONN->UnixTimestamp($rs->fields[12]);
				$item_data['item_status_key'] = $rs->fields[13];																								
				$rs->MoveNext();								
						
			}
			
			$rs->Close();
			
			//now get name details for added_by and modified_by
			if (!class_exists('InteractUser')) {

				require_once('../../includes/lib/user.inc.php');
				

			}
			$user = new InteractUser();

			$item_data['added_by_data'] = $user->getUserData($item_data['add_by']);
			
			if ($item_data['modified_by']!=0){
			
				$item_data['modified_by_data'] = $user->getUserData($item_data['modified_by']);
				
			}
						
			return $item_data;
			
		}
	
	} //end getItemData
	
	/**
	* A method of class Interactgradebook to display items for given gradebook
	* @param int $module_key module key of grade book to get entries for
	*/
	
	function displayBriefItemList($module_key) {
	
		global $t,$CONN,$general_strings, $CONFIG;
	  
		if ($this->_is_admin==true) {
			$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}gradebook_items.item_key,{$CONFIG['DB_PREFIX']}gradebook_items.name, {$CONFIG['DB_PREFIX']}gradebook_items.due_date, {$CONFIG['DB_PREFIX']}gradebook_items.scale_key, {$CONFIG['DB_PREFIX']}gradebook_items.weighting, {$CONFIG['DB_PREFIX']}gradebook_items.maximum_score, {$CONFIG['DB_PREFIX']}gradebook_items.status_key FROM {$CONFIG['DB_PREFIX']}gradebook_items, {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}gradebook_items.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}gradebook_items.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!=4 ORDER BY {$CONFIG['DB_PREFIX']}gradebook_items.sort_order, {$CONFIG['DB_PREFIX']}gradebook_items.date_added";
		} else {
			$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}gradebook_items.item_key,{$CONFIG['DB_PREFIX']}gradebook_items.name, {$CONFIG['DB_PREFIX']}gradebook_items.due_date, {$CONFIG['DB_PREFIX']}gradebook_items.scale_key, {$CONFIG['DB_PREFIX']}gradebook_items.weighting, {$CONFIG['DB_PREFIX']}gradebook_items.maximum_score, {$CONFIG['DB_PREFIX']}gradebook_items.status_key FROM {$CONFIG['DB_PREFIX']}gradebook_items, {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}gradebook_items.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}gradebook_items.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!=4  AND {$CONFIG['DB_PREFIX']}gradebook_items.status_key=1 ORDER BY {$CONFIG['DB_PREFIX']}gradebook_items.sort_order, {$CONFIG['DB_PREFIX']}gradebook_items.date_added";
		}
		
		$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
		if (!class_exists('InteractDate')) {
		
			require_once('../../includes/lib/date.inc.php');
			
		}
		
		$dates = new InteractDate();
		
		$max_score = 0;
		$weighting=0;
		while (!$rs->EOF) {
			
			$unix_due_date = $CONN->UnixTimeStamp($rs->fields[2]);
			$today = time();
			//if due date is greater than today then work out time until due date
			
			if ($unix_due_date==0) {

				$t->set_var('DATE_DIFF','');			
				$t->set_var('DUE_DATE',$this->_gradebook_strings['not_applicable']);			

			} else {
			
				if ($unix_due_date>$today) {
		
					$date_diff = $dates->dateDiff(date('d M Y H:i:s', time()),date('d M Y H:i:s',$unix_due_date), true);
					$t->set_var('DATE_DIFF','('.$date_diff.')');
			
				} else {
			
					$t->set_var('DATE_DIFF','');
				
				}
			
				$t->set_var('DUE_DATE',$dates->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'long'));
			}
			
			$t->set_var('ITEM_NAME',$hidden.$rs->fields[1]);
			$t->set_var('ITEM_KEY',$rs->fields[0]);
			$t->set_var('HIDDEN',($rs->fields[6]==2)?'<span class="red">X</span>':'');
			//if module admin then show item edit tool
			
			if ($this->_is_admin==true) {
			
				$edit_link = get_admin_tool("iteminput.php?space_key=$this->_space_key&module_key=$module_key&item_key={$rs->fields[0]}&action=modify");
				$grade_link = "<a href=\"markitem.php?space_key=$this->_space_key&module_key=$module_key&item_key={$rs->fields[0]}\" class=\"small\">".$this->_gradebook_strings['grade'].'</a>';
				$t->set_var('EDIT_LINK',$edit_link);
				$t->set_var('GRADE_LINK',$grade_link);
				
			} else {
			
				$t->set_var('EDIT_LINK','');
				$t->set_var('GRADE_STRING',$this->_gradebook_strings['grade']);
				$grade_data = $this->getgradeData($rs->fields[0], $this->_user_key);

				if ($grade_data['comments']!='') {
				
					$t->set_var('COMMENTS_LINK',"<a href=\"itemview.php?space_key=$this->_space_key&module_key=$this->_module_key&item_key={$rs->fields[0]}#comments\" class=\"small\">".$this->_gradebook_strings['comments'].'</a>');
					
				} else {
				
				   $t->set_var('COMMENTS_LINK','');
				
				} 
				
				if (!$grade_data['grade_key']) {
				
					$t->set_var('GRADE',$this->_gradebook_strings['no_grade']);
					
				} else {
				
					if ($rs->fields[3]==1) {
				
						$grade = $grade_data['grade_key'].'/'.$rs->fields[5];
						
						//if numeric scale with weighting work out percentage of total
						if ($rs->fields[4]>0) {
											
							$percentage = ($rs->fields[4]/$rs->fields[5])*$grade;
							$total = $total+$percentage;
										
						} else {
						
							$total = $total+$grade;
							
						}
					
					} else {
				
						$grade = $this->getgrade($grade_data['grade_key']);
					
					}
				
					$t->set_var('GRADE',$grade);
					

					
				}
			 
			}
			$weighting = $weighting+$rs->fields[4];
			$max_score = $max_score+$rs->fields[5];
			$t->Parse('ALBlock', 'ItemListBlock', true);
			$rs->MoveNext();

		}
		
		if ($total>0) {
			if ($weighting>0) {
				$possible_total = '<br /><span class="small">('.$this->_gradebook_strings['max_score'].' 100)</span>';
			} else if ($max_score>0) {
				$possible_total = '<br /><span class="small">('.$this->_gradebook_strings['max_score'].' '.$max_score.')</span>';
			} else {
				$possible_total='';
			}		
			$t->set_var('TOTAL_STRING',$this->_gradebook_strings['running_total'].$possible_total);
			$t->set_var('WEIGHTED_TOTAL',round($total,0));
								
		}
		
		return true;
	
	} //end displayBriefItemList
	
	/**
	* A method of class Interactgradebook to display items for given gradebook
	* @param int $module_key module key of grade book to get entries for
	* @param string $spaces_sql sql limiter for spaces to get items for 
	* @param string $groups_sql sql limiter for groups to get items for 	
	*/
	
	function displayGlobalItemList($module_key, $spaces_sql='', $groups_sql='', $sort_by='course') {
	
		global $t,$CONN,$general_strings, $CONFIG;
	 
		if ($sort_by=='course') {
		
			$sql = "SELECT {$CONFIG['DB_PREFIX']}gradebook_items.item_key,{$CONFIG['DB_PREFIX']}gradebook_items.name, {$CONFIG['DB_PREFIX']}gradebook_items.due_date, {$CONFIG['DB_PREFIX']}gradebook_items.scale_key, {$CONFIG['DB_PREFIX']}gradebook_items.weighting, {$CONFIG['DB_PREFIX']}gradebook_items.maximum_score, {$CONFIG['DB_PREFIX']}spaces.name, {$CONFIG['DB_PREFIX']}gradebook_items.module_key, {$CONFIG['DB_PREFIX']}spaces.space_key FROM {$CONFIG['DB_PREFIX']}gradebook_items, {$CONFIG['DB_PREFIX']}module_space_links,  {$CONFIG['DB_PREFIX']}spaces WHERE  {$CONFIG['DB_PREFIX']}gradebook_items.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND  ({$CONFIG['DB_PREFIX']}module_space_links.space_key IN $spaces_sql AND  ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql)) AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!=4 ORDER BY {$CONFIG['DB_PREFIX']}spaces.name, {$CONFIG['DB_PREFIX']}gradebook_items.module_key, {$CONFIG['DB_PREFIX']}gradebook_items.sort_order, {$CONFIG['DB_PREFIX']}gradebook_items.date_added";
			
		} else { 
	
			$sql = "SELECT {$CONFIG['DB_PREFIX']}gradebook_items.item_key,{$CONFIG['DB_PREFIX']}gradebook_items.name, {$CONFIG['DB_PREFIX']}gradebook_items.due_date, {$CONFIG['DB_PREFIX']}gradebook_items.scale_key, {$CONFIG['DB_PREFIX']}gradebook_items.weighting, {$CONFIG['DB_PREFIX']}gradebook_items.maximum_score, {$CONFIG['DB_PREFIX']}spaces.name, {$CONFIG['DB_PREFIX']}gradebook_items.module_key, {$CONFIG['DB_PREFIX']}spaces.space_key FROM {$CONFIG['DB_PREFIX']}gradebook_items, {$CONFIG['DB_PREFIX']}module_space_links,  {$CONFIG['DB_PREFIX']}spaces WHERE  {$CONFIG['DB_PREFIX']}gradebook_items.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND  ({$CONFIG['DB_PREFIX']}module_space_links.space_key IN $spaces_sql AND  ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql)) AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!=4 ORDER BY  {$CONFIG['DB_PREFIX']}gradebook_items.due_date";
			
		}		
		 

		$rs = $CONN->Execute($sql);

		if (!class_exists('InteractDate')) {
		
			require_once('../../includes/lib/date.inc.php');
			
		}
		
		$dates = new InteractDate();
		
		$space = '';
		
		while (!$rs->EOF) {
			
			$grade_data = $this->getgradeData($rs->fields[0], $this->_user_key);
			$unix_due_date = $CONN->UnixTimeStamp($rs->fields[2]);
			$today = time();
			//if due date is greater than today then work out time until due date
			
			if ($unix_due_date==0) {

				$t->set_var('DATE_DIFF','');			
				$t->set_var('DUE_DATE',$this->_gradebook_strings['not_applicable']);			

			} else {
			
				if ($unix_due_date>$today) {
		
					$date_diff = $dates->dateDiff(date('d M Y H:i:s', time()),date('d M Y H:i:s',$unix_due_date), true);
					$t->set_var('DATE_DIFF','('.$date_diff.')');
			
				} else {
			
					$t->set_var('DATE_DIFF','');
				
				}
			
				$t->set_var('DUE_DATE',$dates->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'long'));
			
			}

			$t->set_var('ITEM_NAME',$rs->fields[1]);
			$t->set_var('ITEM_KEY',$rs->fields[0]);
	
			if ($grade_data['comments']!='') {
				
				$t->set_var('COMMENTS_LINK',"<a href=\"globalitemview.php?space_key=$this->_space_key&module_key=$this->_module_key&item_key={$rs->fields[0]}#comments\" class=\"small\">".$this->_gradebook_strings['comments'].'</a>');
					
			} else {
				
				$t->set_var('COMMENTS_LINK','');
				
			} 
				
	
			if (!$grade_data['grade_key']) {
				
				$t->set_var('GRADE',$this->_gradebook_strings['no_grade']);
					
			} else {

				if ($rs->fields[3]==1) {
				
					$grade = $grade_data['grade_key'];
					
				} else {
				
						$grade = $this->getgrade($grade_data['grade_key']);
					
				}
				
				$t->set_var('GRADE',$grade);
					
			}

			if ($sort_by=='course' && $space!=$rs->fields[6]) {
				
				if ($space!='') {
				
					$t->set_var('SPACE_NAME',"<a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key={$rs->fields[8]}\">{$rs->fields[6]}</a>");
					$t->Parse('BBlock', 'BreakBlock', true);
			 
			 	} else {
			
					$t->set_var('BBlock','');
					$t->set_var('SPACE_NAME',"<a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key={$rs->fields[8]}\">{$rs->fields[6]}</a>");
					
				}
				
				$space = $rs->fields[6];

			} else {
				
  				$t->set_var('BBlock','');
  				$t->set_var('SPACE_NAME','');
					
			}			 

			$t->Parse('ILBlock', 'ItemListBlock', true);

			$rs->MoveNext();
		
		}
	
	} //end displayGlobalItemList
	
	/**
	* A method of class gradebook to display full item details
	*
	* @param int $item_key item key of assignmnet to display details for
	*/
	
	function displayFullItemDetails($item_key) {
	
		global $t,$CONN,$general_strings, $CONFIG;
	  
		$sql = "SELECT {$CONFIG['DB_PREFIX']}gradebook_items.item_key,{$CONFIG['DB_PREFIX']}gradebook_items.name, {$CONFIG['DB_PREFIX']}gradebook_items.due_date, {$CONFIG['DB_PREFIX']}gradebook_items.maximum_score, {$CONFIG['DB_PREFIX']}gradebook_items.weighting, {$CONFIG['DB_PREFIX']}gradebook_items.description, {$CONFIG['DB_PREFIX']}gradebook_items.url, {$CONFIG['DB_PREFIX']}gradebook_items.scale_key   FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE {$CONFIG['DB_PREFIX']}gradebook_items.item_key='$item_key'";
		
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {
		
			$unix_due_date = $CONN->UnixTimeStamp($rs->fields[2]);
			$today = time();
			
			//if due date is greater than today then work out time until due date
			
			if ($unix_due_date>$today) {
		
				if (!class_exists('InteractDate')) {
				
					require_once('../../includes/lib/date.inc.php');
					
				}
				
				$dates = new InteractDate();			
				$date_diff = $dates->dateDiff(date('d M Y H:i:s', time()),date('d M Y H:i:s',$unix_due_date), true);
				$t->set_var('DATE_DIFF','('.$date_diff.')');
			
			} else {
			
				$t->set_var('DATE_DIFF','');
				
			}
			
			$t->set_var('DUE_DATE',date('d M Y h:ia',$CONN->UnixTimeStamp($rs->fields[2])));
			$t->set_var('ITEM_NAME',$rs->fields[1]);
			$t->set_var('VIEW_ITEM_HEADING',sprintf($this->_gradebook_strings['view_item'], $rs->fields[1]));			
			$t->set_var('ITEM_KEY',$rs->fields[0]);
			$t->set_var('DESCRIPTION',$rs->fields[5]);
			
			if ($rs->fields[6]!='') {
			
				 $t->set_var('ITEM_URL','<a href="'.$rs->fields[6].'">'.$this->_gradebook_strings['online_link'].'</a>');
				
			} else { 											 
			
				$t->set_var('ITEM_URL',$this->_gradebook_strings['no_online_content']);
				
			}
			
			//if module admin then show item edit tool
			
			if ($this->_is_admin==true) {
			
				$edit_link = get_admin_tool("iteminput.php?space_key=$this->_space_key&module_key=$this->_module_key&item_key={$rs->fields[0]}&action=modify");
				$t->set_var('EDIT_LINK',$edit_link);
				$t->set_var('GBlock','');				
				
			} else {
			
				$t->set_var('EDIT_LINK','');
				$t->set_var('GRADE_STRING',$this->_gradebook_strings['grade']);
				$t->set_var('COMMENTS_STRING',$this->_gradebook_strings['comments']);				
				$grade_data = $this->getgradeData($item_key, $this->_user_key);

				if (!isset($grade_data['grade_key'])) {
				
					$t->set_var('GRADE',$this->_gradebook_strings['no_grade']);
					
				} else {
				
					if ($rs->fields[7]==1) {
				
						$grade = $grade_data['grade_key'];
					
					} else {
				
						$grade = $this->getgrade($grade_data['grade_key']);
					
					}
				
					$t->set_var('GRADE',$grade);
					$t->set_var('COMMENTS',$grade_data['comments']);
					$t->Parse('GBlock', 'GradeBlock', true);

				}
			
			}
				
			if ($rs->fields[3]>0) {
			
				$t->set_var('MAX_SCORE_STRING',$this->_gradebook_strings['max_score'].':');
				$t->set_var('MAX_SCORE_VALUE',$rs->fields[3]);				
				
			} else {
			
				$t->set_var('MAX_SCORE_STRING','');
				$t->set_var('MAX_SCORE_VALUE','');
				
			}
			
			if ($rs->fields[4]>0) {
			
				$t->set_var('WEIGHTING_STRING',$this->_gradebook_strings['weighting'].':');
				$t->set_var('WEIGHTING_VALUE',$rs->fields[4]);				
				
			} else {
			
				$t->set_var('WEIGHTING_STRING','');
				$t->set_var('WEIGHTING_VALUE','');
				
			}			
			
			$rs->MoveNext();
			
		}
	
	} //end displayFullItemDetails
	
	/**
	* A method of class gradebook to calculate weigting total
	* 
	* @param int $module_key module_key of gradebook to count totals for
	* @return int $total total of weightings for all items 
	*/
	function calculateweightingTotal($module_key, $item_key) {
	
		global $CONN, $CONFIG;
		
		
		$rs = $CONN->Execute("SELECT SUM(weighting) FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE module_key='$module_key' AND item_key!='$item_key'");

		while (!$rs->EOF) {
		
			$total = $rs->fields[0];
			$rs->MoveNext();
			
		}  

		return $total;
	
	} // end calculateweightingTotal
	
	/**
	* A method of class gradebook to create a select menu of possible grades
	* 
	* @param int $item_key item_key of item to be graded
	* @return string $grade_menu html select list of available grades 
	*/
	function makegradeMenu($item_key, $grade_key, $scale_key, $menu_name='grade_key') {
	
		global $CONN, $CONFIG;
		
		if ($scale_key==1) {
		
			$rs = $CONN->Execute("SELECT maximum_score FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE item_key='$item_key'");

			$grade_menu = '<select name="'.$menu_name.'" size=1 class="small"><option value="">---</option>';
			

			while (!$rs->EOF) {
		
				$maximum_score = $rs->fields[0];
				$rs->MoveNext();
			
			}  
			
			for ($i=0; $i<=$maximum_score; $i++) {
		
				if ($i==$grade_key) {
			
					$grade_menu .= '<option value="'.$i.'" selected=\"selected\">'.$i.'/'.$maximum_score.'</option>';
				
				} else {
			
					$grade_menu .= '<option value="'.$i.'" >'.$i.'/'.$maximum_score.'</option>';
			
				}
			
			}
		
			$grade_menu .= '</select>';
		
		} else {

			$rs = $CONN->Execute("SELECT grade_key, grade FROM {$CONFIG['DB_PREFIX']}gradebook_scale_grades WHERE scale_key='$scale_key'");

			$grade_menu = '<select name="'.$menu_name.'" size=1 class="small"><option value="">---</option>';

			while (!$rs->EOF) {
		
				$grade_key2 = $rs->fields[0];
				$grade	 = $rs->fields[1];
				
				if ($grade_key==$grade_key2) {
			
					$grade_menu .= '<option value="'.$grade_key2.'" selected=\"selected\">'.$grade.'</option>';
				
				} else {
			
					$grade_menu .= '<option value="'.$grade_key2.'" >'.$grade.'</option>';
			
				}
				
				$rs->MoveNext();
				
			
			}  
			
			$grade_menu .= '</select>';
		
		}		
		
		$rs->Close();
		return $grade_menu;
		unset($grade_menu);
	
	} // end makegradeMenu
	
	/**
	* A method of class gradebook to display item grading boxes for each user
	* 
	* @param int $item_key item_key of item to be graded
	* @param string $type by_item or by_user
	* @return true/false
	*/
	function displayMarkItemBoxes($item_key, $user_key='', $type='by_item', $marked_item='') {
	
		global $CONN, $CONFIG, $t;
		$html = new InteractHtml();
		if ($type=='by_item') {
		
			$t->set_var('ACTION_FILE','markitem.php');

			$user_data = $this->getUserList();
			
			if ($user_key!='') {
			
				$user_list = array();
				$user_list[$user_key] = $user_data['by_name'][$user_key];
			
			} else {
			
				$user_list = $user_data['by_name'];
				
			}
			
			if ($user_list!=false)  {
			
				$item_data = $this->getItemData($item_key);
				
				

				
				foreach ($user_list as $user_key => $name ) {
		
					if ($marked_item==$item_key.'_'.$user_key) {
					
						$t->set_var('GRADE_MESSAGE',$this->_gradebook_strings['modify_grade_success']);
						
					} else {
					
						$t->set_var('GRADE_MESSAGE','');
					
					}
						
					$t->set_var('USER_ITEM_NAME',$name);
					$t->set_var('USER_KEY',$user_key);
					$grade_data = $this->getgradeData($item_key, $user_key);
					//generate the grade menue
					$grade_menu = $this->makegradeMenu($item_key, $grade_data['grade_key'], $item_data['scale_key'], $user_key.'_grade');
					$t->set_var('MODIFY_SINGLE_KEY',$user_key);
					$t->set_var('GRADE_MENU',$grade_menu);
					
					$t->set_var('FIELD_ID',$user_key.'_comments');
					$html->setTextEditor($t, 0, $user_key.'_comments');
					
					$t->set_var('BODY',$grade_data['comments']);
					$t->set_var('ANCHOR',$item_key.'_'.$user_key);
					$t->Parse('MIBlock', 'MarkItemBlock', true);										
		
				}
				
			} else {
			
				$t->set_var('MIBlock',$this->_gradebook_strings['no_users']);
				
			}
		
		} else if ($type=='by_user') {
		
			$t->set_var('ACTION_FILE','markuser.php');
			$item_list = $this->getItemList();
		
			if ($item_list!=false)  {
			
				foreach ($item_list as $item_key => $name ) {
		
					if ($marked_item==$item_key.'_'.$user_key) {
					
						$t->set_var('GRADE_MESSAGE',$this->_gradebook_strings['modify_grade_success']);
						
					} else {
					
						$t->set_var('GRADE_MESSAGE','');
					
					}
					$t->set_var('USER_ITEM_NAME',$name);
					$t->set_var('USER_KEY',$user_key);
					$t->set_var('MODIFY_SINGLE_KEY',$item_key);					
					$t->set_var('ITEM_KEY',$item_key);
					$item_data = $this->getItemData($item_key);
					$grade_data = $this->getgradeData($item_key, $user_key);
				
					//generate the grade menue
					$grade_menu = $this->makegradeMenu($item_key, $grade_data['grade_key'], $item_data['scale_key'],$item_key.'_grade');
					$t->set_var('GRADE_MENU',$grade_menu);
					$t->set_var('FIELD_ID',$item_key.'_comments');
					$html->setTextEditor($t, 0, $item_key.'_comments');				
					$t->set_var('BODY',$grade_data['comments']);
					$t->set_var('ANCHOR',$item_key.'_'.$user_key);
					$t->Parse('MIBlock', 'MarkItemBlock', true);										
		
				}
				
			} else {
			
				$t->set_var('MIBlock',$this->_gradebook_strings['no_items']);
				
			}
		
		}
		
	
	} // end displayMarkItemBoxes
	
	/**
	* A method of class gradebook to get grade data for a given item and user
	*
	* @param int $item_key key of item
	* @param int $user_key key of user
	* @return string $grade_data array of grade data or false if no grade available		
	*/
	
	function getgradeData($item_key, $user_key) {
	
		global $CONN, $CONFIG, $user;
		
		$sql = "SELECT {$CONFIG['DB_PREFIX']}gradebook_item_user_links.added_by_key,{$CONFIG['DB_PREFIX']}gradebook_item_user_links.date_added, {$CONFIG['DB_PREFIX']}gradebook_item_user_links.modified_by_key, {$CONFIG['DB_PREFIX']}gradebook_item_user_links.date_modified, {$CONFIG['DB_PREFIX']}gradebook_item_user_links.grade_key,  {$CONFIG['DB_PREFIX']}gradebook_item_user_links.comments FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE   {$CONFIG['DB_PREFIX']}gradebook_item_user_links.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}gradebook_item_user_links.user_key='$user_key'";

		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {
			
				$grade_data['add_by']   	 = $rs->fields[0];				
				$grade_data['date_added']	= $CONN->UnixTimestamp($rs->fields[1]);
				$grade_data['modified_by']	 = $rs->fields[2];				
				$grade_data['date_modified'] = $CONN->UnixTimestamp($rs->fields[3]);
				$grade_data['grade_key']	 = $rs->fields[4];	
				$grade_data['comments']	  = $rs->fields[5];																																			
				$rs->MoveNext();								
						
			}
			
			$rs->Close();
			
			//now get name details for added_by and modified_by
						
			if (!class_exists('InteractUser')) {

				require_once('../../includes/lib/user.inc.php');
				

			}
			$user = new InteractUser();

			$grade_data['added_by_data'] = $user->getUserData($grade_data['add_by']);
			
			if ($item_data['modified_by']!=0){
			
				$grade_data['modified_by_data'] = $user->getUserData($grade_data['modified_by']);
				
			}
						
			return $grade_data;
			unset($grade_data);
			
		}
	
	} //end getgradeData
	
	/**
	* A method of class gradebook to modify grade for given item and user
	*
	* @param int $item_key key of item
	* @param int $user_key key of user
	* @return true		
	*/
	
	function modifygrade($item_key, $user_key, $grade_key, $comments, $type='full') {
	
		global $CONN, $CONFIG;

		$date		= $CONN->DBDate(date('Y-m-d H:i:s'));
		$comments	= $comments;
		
		//find out what type of grading scale we are using
		$item_data = $this->getItemData($item_key);
		
		//see if grade already exists
		$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE   {$CONFIG['DB_PREFIX']}gradebook_item_user_links.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}gradebook_item_user_links.user_key='$user_key'");		
		
		if ($rs->EOF) {
			
			$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}gradebook_item_user_links(item_key, user_key, added_by_key,  date_added, grade_key, comments) VALUES ('$item_key', '$user_key', '$this->_user_key', $date, '$grade_key',  '$comments')";		
		
		
		} else {
		
			if ($type=='full') {
			
				$sql = "UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET modified_by_key='$this->_user_key',  date_modified=$date, grade_key='$grade_key', comments='$comments' WHERE   {$CONFIG['DB_PREFIX']}gradebook_item_user_links.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}gradebook_item_user_links.user_key='$user_key'";
				
			} else if ($type=='grade_only') {
			
				$sql = "UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET modified_by_key='$this->_user_key',  date_modified=$date, grade_key='$grade_key' WHERE   {$CONFIG['DB_PREFIX']}gradebook_item_user_links.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}gradebook_item_user_links.user_key='$user_key'";
				
			} else if ($type=='comment_only') {
			
				$sql = "UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET modified_by_key='$this->_user_key',  date_modified=$date, comments='$comments' WHERE   {$CONFIG['DB_PREFIX']}gradebook_item_user_links.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}gradebook_item_user_links.user_key='$user_key'";
				
			}
 	
		}

		if ($CONN->Execute($sql)===false) {
		
			return $CONN->ErrorMsg();
			
		} else {
		
			return true;
			
		}

	
	} //end modifygrade			
	
	/**
	* A method of class gradebook to modify grade for given item and user
	*
	* @return string $spreadsheetview html table of gradebook data		
	*/
	
	function getSpreadSheetView($comments=0) {
	
		global $CONN, $CONFIG;
		
		$item_list = $this->getItemList();
				
		$spreadsheetview_header  = '<table border="0" cellpadding="0" cellspacing="0" class="borderedTable">';
		$n=1;
		
		$spreadsheetview = '';
		if ($item_list!=false)  {
			
			$spreadsheetview_header .= '<tr><th>&nbsp;</th>';
			$spreadsheetview_footer = '<tr><td>&nbsp;</td>';
 			
			$total_weighting = 0;
			$total_score = 0;
			while (list($item_key,$name) = each($item_list)) {
			
							
				//get scale type
				$item_data = $this->getItemData($item_key);
				$scale_data[$item_key]	 = $item_data['scale_key'];
				$weight_data[$item_key]	= $item_data['weighting'];
				$max_score_data[$item_key] = $item_data['maximum_score'];
				$weighting = '';
				$max_score = '';
				
				if ($item_data['weighting']>0) {
				
					$weighting = '<br /><span class="small">'.$this->_gradebook_strings['weighting'].' - '.$item_data['weighting'].'<span>';
					$total_weighting = $item_data['weighting']+$total_weighting;
					
				}
				if ($item_data['maximum_score']>0) {
				
					$max_score = '<span class="small">'.$this->_gradebook_strings['max_score'].' - '.$item_data['maximum_score'].'<span>';
					$total_score = $item_data['maximum_score']+$total_score;
					
				}
							
				$spreadsheetview_header .= '<th style="text-align:left">'.$name.'</th>';
				$spreadsheetview_footer .= '<td style="text-align:left">'.$max_score.$weighting;
				
			}
			
				if ($item_data['scale_key']==1) {
				
					if ($total_weighting>0) {
						$possible_total = '<br /><span class="small">'.$this->_gradebook_strings['max_score'].' (100)<span>';
					} else if ($total_score>0) {
						$possible_total = '<br /><span class="small">'.$this->_gradebook_strings['max_score'].' ('. $total_score.')<span>'; 
					} else {
						$possible_total = '';
					}
					$spreadsheetview_header .= '<th>'.$this->_gradebook_strings['running_total'].$possible_total.'</th></tr>';
					
				}
				
		}
		reset($item_list);

		$user_data = $this->getUserList();
		$user_list = $user_data['by_name'];
		
		if ($user_list!=false)  {

	
			while (list($user_key,$user_name) = each($user_list)) {
				 
				if(is_numeric($n)&($n&1)) {
					$spreadsheetview .= '<tr style="background-color:#F0F0F0">';
				} else {
					$spreadsheetview .= '<tr >';			
				}
				$n++;
				$total = 0;
				$spreadsheetview .= '<td valign="top">'.$user_name.'</td>';
				

				while (list($item_key,$name) = each($item_list)) {
				  			
					//get existing grade data
					$grade_data = $this->getgradeData($item_key, $user_key);
					
					
					if ($scale_data[$item_key]==1) {
					
						$grade = $grade_data['grade_key'];
						
					} else { 
					
					
						$grade = $this->getgrade($grade_data['grade_key']);
						
						
					}
						
 					if ($comments==0) {
 						$grade_data['comments']='';
 					}
 					if (empty($grade)){
 						$grade = $this->_gradebook_strings['grade'];
 					}
					$spreadsheetview .= '<td class="small" valign="top"><div align="center"><a href="markitem.php?space_key='.$this->_space_key.'&module_key='.$this->_module_key.'&item_key='.$item_key.'&user_key='.$user_key.'&action=mark_single">'.$grade.'</a></div><br />'.$grade_data['comments'].'</td>';
					
					//if numeric scale work out total
					
					if ($scale_data[$item_key]==1) {
					
						//if numeric scale with weighting work out percentage of total
						
						if ($scale_data[$item_key]==1 && $weight_data[$item_key]>0) {
											
							$percentage = ($weight_data[$item_key]/$max_score_data[$item_key])*$grade_data['grade_key'];
							$total = $total+$percentage;
										
						} else {
						
							$total = $total+$grade_data['grade_key'];
							
						}
						
					
					}
					
				}
				
				//now get total details
				
				if ($total>0) {
				
					$total = round($total, 0);
					$spreadsheetview .= "<td valign=\"top\" class=\"small\">$total</td>";
					
				} else {
					
					$spreadsheetview .= "<td valign=\"top\" class=\"small\">&nbsp;</td>";
					
				}
				
				$spreadsheetview .= '</tr>';
				reset($item_list);
				unset($grade_data);
			
			}
			
				
				
		}
				
		$spreadsheetview = $spreadsheetview_header.$spreadsheetview.$spreadsheetview_footer.'</table>';
		
		return $spreadsheetview;
	
	} //end getSpreadSheetView	
	
	/**
	* A method of class gradebook to export gradebook data as excel spreadsheet
	*
	* @return string $spreadsheetview html table of gradebook data		
	*/
	
	function getExcelView($comments=0) {
	
		global $CONN, $CONFIG, $general_strings;
		
		$item_list = $this->getItemList();
		
		$spreadsheetview  = '<table border="1" cellpadding="5" cellspacing="1">';
		
		if ($item_list!=false)  {
			$total_weighting = 0;
			$total_score = 0;
			$spreadsheetview .= '<tr><th>'.$general_strings['id_number'].'</th><th>'.$general_strings['name'].'</th>';
			
			foreach ($item_list as $item_key => $name ) {

				//get scale type
				$item_data = $this->getItemData($item_key);
				$scale_data[$item_key]	 = $item_data['scale_key'];
				$weight_data[$item_key]	= $item_data['weighting'];
				$max_score_data[$item_key] = $item_data['maximum_score'];
				$weighting = '';
				$max_score = '';
				
				if ($item_data['weighting']>0) {
				
					$weighting = '<br /><span class="small">'.$this->_gradebook_strings['weighting'].' ('.$item_data['weighting'].')<span>';
					$total_weighting = $item_data['weighting']+$total_weighting;
					
				}
				if ($item_data['maximum_score']>0) {
				
					$max_score = '<br /><span class="small">'.$this->_gradebook_strings['max_score'].' ('.$item_data['maximum_score'].')<span>';
					$total_score = $item_data['maximum_score']+$total_score;
					
				}
							
				$spreadsheetview .= '<th>'.$name.$max_score.$weighting.'</th>';
				if ($comments==1) {
					$spreadsheetview .= '<th>'.$this->_gradebook_strings['comments'].'</th>';
				}
				
			}
			
				if ($item_data['scale_key']==1) {
				
					if ($total_weighting>0) {
						$possible_total = '<br />'.$this->_gradebook_strings['max_score'].' (100)';
					} else if ($total_score>0) {
						$possible_total = '<br />'.$this->_gradebook_strings['max_score'].' ('. $total_score.')'; 
					} else {
						$possible_total = '';
					}
					$spreadsheetview .= '<th>'.$this->_gradebook_strings['running_total'].$possible_total.'</th></tr>';
					
				}
				
		}
		
			$user_data = $this->getUserList();
			$user_list = $user_data['by_name'];
			
		
		if ($user_list!=false)  {
			
			$spreadsheetview .= '<tr>';
			
			foreach ($user_list as $user_key => $user_name ) {
			
				$total = 0;
				$spreadsheetview .= '<td>'.$user_data['by_number'][$user_key].'</td>';
				$spreadsheetview .= '<td>'.$user_name.'</td>';
				
				foreach ($item_list as $item_key => $name ) {
			
					//get existing grade data
					$grade_data = $this->getgradeData($item_key, $user_key);
					
					if ($scale_data[$item_key]==1) {
					
						$grade = $grade_data['grade_key'];
						
					} else {
					
						$grade = $this->getgrade($grade_data['grade_key']);
						
					}
					
					$spreadsheetview .= '<td>'.$grade.'</td>';
					if ($comments==1) {
						$spreadsheetview .= '<td>'.$grade_data['comments'].'</td>';
					} 
					//if numeric scale work out total
					
					if ($scale_data[$item_key]==1) {
					
						//if numeric scale with weighting work out percentage of total
						
						if ($scale_data[$item_key]==1 && $weight_data[$item_key]>0) {
											
							$percentage = ($weight_data[$item_key]/$max_score_data[$item_key])*$grade_data['grade_key'];
							$total = $total+$percentage;
										
						} else {
						
							$total = $total+$grade_data['grade_key'];
							
						}
					
					}
				
				}
				
				//now get total details
				
				if ($scale_data[$item_key]==1) {
				
					$total = round($total, 0);
					$spreadsheetview .= "<td>$total</td>";
					
				}
				
				$spreadsheetview .= '</tr>';
			
			}
			
				
				
		}
				
		$spreadsheetview .= '</table>';
		
		return $spreadsheetview;
	
	} //end getExcelView

	/**
	* A method of class gradebook to export gradebook data as tab delimited file
	*
	* @return string $textview tab delimited view of gradebook data		
	*/
	
	function getTextView($comments=0) {
	
		global $CONN, $CONFIG, $general_strings;
		
		$item_list = $this->getItemList();
		
		
		if ($item_list!=false)  {
			$total_weighting = 0;
			$total_score = 0;
			$textview .= $general_strings['id_number']."\t".$general_strings['name']."\t";
			
			foreach ($item_list as $item_key => $name ) {

				//get scale type
				$item_data = $this->getItemData($item_key);
				$scale_data[$item_key]	 = $item_data['scale_key'];
				$weight_data[$item_key]	= $item_data['weighting'];
				$max_score_data[$item_key] = $item_data['maximum_score'];
				$weighting = '';
				$max_score = '';
				
				if ($item_data['weighting']>0) {
				
					$weighting = $this->_gradebook_strings['weighting'].' ('.$item_data['weighting'].')';
					$total_weighting = $item_data['weighting']+$total_weighting;				
				}
				if ($item_data['maximum_score']>0) {
				
					$max_score = '<br /><span class="small">'.$this->_gradebook_strings['max_score'].' ('.$item_data['maximum_score'].')<span>';
					$total_score = $item_data['maximum_score']+$total_score;
					
				}
							
				$textview .= $name.$max_score.$weighting."\t";
				if ($comments==1) {
					$textview .= $this->_gradebook_strings['comments']."\t";
				}
			}
			
			if ($item_data['scale_key']==1) {
				if ($total_weighting>0) {
					$possible_total = $this->_gradebook_strings['max_score'].' (100)';
				} else if ($total_score>0) {
					$possible_total = $this->_gradebook_strings['max_score'].' ('. $total_score.')>'; 
				} else {
					$possible_total = '';
				}
				$textview .= $this->_gradebook_strings['running_total'].' '.$possible_total;
					
			}
			
			$textview .= "\n";
				
				
		}
		
		$user_data = $this->getUserList();
		$user_list = $user_data['by_name'];
			
		
		if ($user_list!=false)  {
			
				
			foreach ($user_list as $user_key => $user_name ) {
			
				$total = 0;
				$textview .= $user_data['by_number'][$user_key]."\t";
				$textview .= $user_name.'	';
				
				foreach ($item_list as $item_key => $name ) {
			
					//get existing grade data
					$grade_data = $this->getgradeData($item_key, $user_key);
					
					if ($scale_data[$item_key]==1) {
					
						$grade = $grade_data['grade_key'];
						
					} else {
					
						$grade = $this->getgrade($grade_data['grade_key']);
						
					}
					
					if ($comments==1) {
						$textview .= $grade."\t".$grade_data['comments']."\t";
					}
					
					//if numeric scale work out total
					
					if ($scale_data[$item_key]==1) {
					
						//if numeric scale with weighting work out percentage of total
						
						if ($scale_data[$item_key]==1 && $weight_data[$item_key]>0) {
											
							$percentage = ($weight_data[$item_key]/$max_score_data[$item_key])*$grade_data['grade_key'];
							$total = $total+$percentage;
										
						} else {
						
							$total = $total+$grade_data['grade_key'];
							
						}
					
					}
				
				}			   
				//now get total details
				
				if ($scale_data[$item_key]==1) {
				
					$total = round($total, 0);
					$textview .= "$total";
					
				}
				
				$textview .= "\n";
			
			}
			
				
				
		}
				
		return $textview;
	
	} //end getTextView
		
	/**
	* A method of class gradebook to get grade for given gradekey
	* 
	* @param int $grade_key grade_key of grade to retrieve
	* @return string $grade_data grade for given grade key 
	*/
	function getgrade($grade_key) {
	
		global $CONN, $CONFIG;
		
		
		$rs = $CONN->Execute("SELECT grade FROM {$CONFIG['DB_PREFIX']}gradebook_scale_grades WHERE grade_key='$grade_key'");
		

		while (!$rs->EOF) {
		
			$grade = $rs->fields[0];
			$rs->MoveNext();
			
		}  

		return $grade;
	
	} // end getgrade		
	
	/**
	* A method of class gradebook to add a new scale
	* 
	* @param string $scale_name name of scale
	* @param string $scale_description description of scale
	* @param string $type type of scale - user or system	
	* @return true  
	*/
	function addScale($scale_name, $scale_dscription, $space_key, $type='user') {
	
		global $CONN, $CONFIG;
				
		$date_added	   = $CONN->DBDate(date('Y-m-d H:i:s'));
		$scale_name	   = $scale_name;
		$scale_description = $scale_dscription;
						
		if ($type=='system') {
		
			$user_key = 0;
			
		} else {
		
			$user_key = $this->_user_key;
					
		}
		
		if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}gradebook_scales(name, description, space_key, added_by_key, date_added) VALUES ('$scale_name', '$scale_description', '$space_key', '$user_key', $date_added)")===false) {
		
			return $CONN->ErrorMsg();
		
		} else {
		
			return true;
			
		}
	
	} // end addScale
	
	/**
	* A method of class gradebook to modify scale
	* 
	* @param string $scale_key key of scale to modify
	* @param string $scale_name name of scale
	* @param string $scale_description description of scale
	* @param string $type type of scale - user or system	
	* @return true  
	*/
	function modifyScale($scale_key, $scale_name, $scale_dscription) {
	
		global $CONN, $CONFIG;
				
		$date_modified	 = $CONN->DBDate(date('Y-m-d H:i:s'));
		$scale_name		= $scale_name;
		$scale_description = $scale_dscription;
						
		if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_scales SET name='$scale_name', description='$scale_description', modified_by_key='$this->_user_key', date_modified=$date_modified WHERE scale_key='$scale_key'")===false) {

			return $CONN->ErrorMsg();
		
		} else {
		
			return true;
			
		}
	
	} // end modifyScale
	
	/**
	* A method of class gradebook to delete a scale
	* 
	* @param string $scale_key key of scale to delete
	* @return true  
	*/
	function deleteScale($scale_key) {
	
		global $CONN, $CONFIG;
				
		$scale_data = $this->getScaleData($scale_key);
		
		if ($scale_data['added_by_key']==$this->_user_key || ($scale_data['added_by_key']==0 && $_SESSION['userlevel_key']==1)) {
				
			if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_scales WHERE scale_key='$scale_key'")===false) {

				return $CONN->ErrorMsg();
		
			} else if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_scale_grades WHERE scale_key='$scale_key'")===false) {

				return $CONN->ErrorMsg();
		
			} else{
		
				return true;
			
			}
			
		}
	
	} // end deleteScale		
	
	/**
	* A method of class gradebook to get data for given scale
	* 
	* @param string $scale_key key of scale to get data for
	* @return array $scale_data array of data for given scale scale_name scale_description  
	*/
	function getScaleData($scale_key) {
	
		global $CONN, $CONFIG;
				
		$rs = $CONN->Execute("SELECT name, description, added_by_key FROM {$CONFIG['DB_PREFIX']}gradebook_scales WHERE scale_key='$scale_key'");
		
		while (!$rs->EOF) {
		
			$scale_data['name']		 = $rs->fields[0];
			$scale_data['description']  = $rs->fields[1];
			$scale_data['added_by_key'] = $rs->fields[2];
			$rs->MoveNext();
			
		}
		
		return $scale_data;
			 
	
	} // end getScaleData				
	
	/**
	* A method of class gradebook to add a new grade
	* 
	* @param string $grade_name name of grade
	* @param string $grade_description description of grade
	* @param string $type type of grade - user or system	
	* @return true  
	*/
	function addScalegrade($grade_name, $scale_key) {
	
		global $CONN, $CONFIG;
				
		$date_added	   = $CONN->DBDate(date('Y-m-d H:i:s'));
		$grade_name	   = $grade_name;
						
		if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}gradebook_scale_grades(grade, scale_key, added_by_key, date_added) VALUES ('$grade_name', '$scale_key', '$this->_user_key', $date_added)")===false) {

			return $CONN->ErrorMsg();
		
		} else {
		
			return true;
			
		}
	
	} // end addScalegrade
	
	/**
	* A method of class gradebook to modify grade
	* 
	* @param string $grade_key key of grade to modify
	* @param string $grade_name name of grade
	* @param string $grade_description description of grade
	* @param string $type type of grade - user or system	
	* @return true  
	*/
	function modifyScalegrade($grade_key, $grade_name) {
	
		global $CONN, $CONFIG;
				
		$date_modified	 = $CONN->DBDate(date('Y-m-d H:i:s'));
		$grade_name		= $grade_name;
		$grade_description = $grade_dscription;
					
		if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_scale_grades SET grade='$grade_name',  modified_by_key='$this->_user_key', date_modified=$date_modified WHERE grade_key='$grade_key'")===false) {

			return $CONN->ErrorMsg();
		
		} else {
		
			return true;
			
		}
	
	} // end modifyScalegrade
	
	/**
	* A method of class gradebook to delete a grade
	* 
	* @param string $grade_key key of grade to delete
	* @return true  
	*/
	function deleteScalegrade($grade_key) {
	
		global $CONN, $CONFIG;
	
		$grade_data = $this->getScalegradeData($grade_key);
		
		if ($grade_data['added_by_key']==$this->_user_key || $_SESSION['userlevel_key']==1) {
		
		
			if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}gradebook_scale_grades WHERE grade_key='$grade_key'")===false) {

				return $CONN->ErrorMsg();
		
			} else  if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET grade_key='0' WHERE grade_key='$grade_key'")===false){
		
				return $CONN->ErrorMsg();
			
			} else {
			
				return true;
				
			}
			
		}
	
	} // end deleteScalegrade		
	
	/**
	* A method of class gradebook to get detail for given grade
	* 
	* @param string $grade_key key of grade to get data for
	* @return array $grade_data array of data for given grade grade grade_description  
	*/
	function getScalegradeData($grade_key) {
	
		global $CONN, $CONFIG;
				
		$rs = $CONN->Execute("SELECT grade added_by_key FROM {$CONFIG['DB_PREFIX']}gradebook_scale_grades WHERE grade_key='$grade_key'");
		
		while (!$rs->EOF) {
		
			$grade_data['grade']		= $rs->fields[0];
			$grade_data['added_by_key'] = $rs->fields[2];
			$rs->MoveNext();
			
		}
		
		return $grade_data;
			 
	
	} // end getScalegradeData			
					
} //end gradeboook class	
?>
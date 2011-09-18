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
* InteractQuiz Class
*
* Contains the Quiz class for all methods and datamembers related
* to adding, modifying and viewing Question and test data
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.20 2007/01/25 03:11:32 glendavies Exp $
* 
*/

/**
* A class that contains methods for adding/modifying Qestion and test info
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying question and test data 
* 
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractQuiz {

	/**
	* space key of current quiz
	* @access private
	* @var int 
	*/
	var $_space_key = '';

	/**
	* module key of current quiz
	* @access private
	* @var int 
	*/
	var $_module_key = '';
	
	/**
	* group key of current quiz
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
	* settings for current quiz
	* @access private
	* @var array 
	*/
	var $_quiz_settings = '';
	
	/**
	* userkey of user whose quiz is to be displayed/edited
	* @access private
	* @var int 
	*/
	var $_quiz_user_key = '';
	
	/**
	* array of language strings for quiz module
	* @access private
	* @var array 
	*/
	var $_quiz_strings = '';
	
	/**
	* array of settings for current quiz
	* @access private
	* @var array 
	*/
	var $_quiz_data = '';
	
		
	
	/**
	* Constructor for InteractQuiz Class. Sets required variables
	*
	* @param  int $space_key  key of current space
	* @param  int $module_key  key of current module	
	* @param  int $group_key  key of current group
	* 
	*/
	
	function InteractQuiz($space_key,$module_key,$group_key,$is_admin,$quiz_strings) {
	
		$this->_space_key	= $space_key;
		$this->_module_key   = $module_key;
		$this->_group_key	= $group_key;
		$this->_is_admin	 = $is_admin;
		$this->_quiz_strings = $quiz_strings;				
		$this->_user_key	 = $_SESSION['current_user_key'];						
		
	} //end InteractQuiz
	
	
	/**
	* Function to get exisiting quiz data 
	*
	* @param  int $module_key  key of quiz module
	* @return array $quiz_data data for selected quiz
	*/

	function getQuizData($module_key) {

	 
		global $CONN, $CONFIG;
	 
		$sql = "SELECT type_key, open_date, close_date, attempts, shuffle_questions, shuffle_answers, grading_key, build_on_previous, show_correct, show_feedback, minutes_allowed, answer_attempts, feedback_attempts FROM {$CONFIG['DB_PREFIX']}quiz_settings WHERE module_key='$module_key'";
	
		$rs = $CONN->Execute($sql);
	 
		while (!$rs->EOF) {
	 
			$quiz_data['type_key']		  = $rs->fields[0];
			$quiz_data['open_date_unix']	= $CONN->UnixTimestamp($rs->fields[1]);
			$quiz_data['close_date_unix']   = $CONN->UnixTimestamp($rs->fields[2]);		 
			$quiz_data['attempts']		  = $rs->fields[3];
			$quiz_data['shuffle_questions'] = $rs->fields[4];
			$quiz_data['shuffle_answers']   = $rs->fields[5];
			$quiz_data['grading_key']	   = $rs->fields[6];
			$quiz_data['build']			 = $rs->fields[7];
			$quiz_data['show_correct']	  = $rs->fields[8];
			$quiz_data['show_feedback']	 = $rs->fields[9];
			$quiz_data['minutes_allowed']   = $rs->fields[10];	
			$quiz_data['answer_attempts']   = $rs->fields[11];
			$quiz_data['feedback_attempts']   = $rs->fields[12];								
			
			$rs->MoveNext();
		 
		}

		$this->_quiz_data = $quiz_data;
		return $quiz_data;

	} //end getQuizData()
	
	/**
	* Add a new multichoice/true-false question
	*
	* @param  array $item_data  array of posted form data
	* @return true return true if add successful
	*/
	
	function inputMultichoice($item_data, $action='add') {
	
		global $CONN, $CONFIG;
		
		//see if multi answer or single
		
		if ($item_data['multiple_response']==1) {
		
			$rcardinality = 'Multiple';
			
		} else {
		
			$rcardinality = 'Single';
			
		}
		
		//if new add data to Item table
		
		if ($action=='add') {
		
			$name	   = $item_data['name'];
			$question   = $item_data['question'];
			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));			
				
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_item(name, mattext, response_type, render_type,  rcardinality, added_by_key, date_added) VALUES ('$name', '$question', 'lid','choice','$rcardinality', '$this->_user_key', $date_added)");
		echo $CONN->ErrorMsg();
			$item_key = $CONN->Insert_ID();
			
		} else {

			$name	 = $item_data['name'];
			$question = $item_data['question'];
			$date_modified = $CONN->DBDate(date('Y-m-d H:i:s'));			
			$item_key = $item_data['item_key'];		
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_item SET name='$name', mattext='$question',   rcardinality='$rcardinality', modified_by_key='$this->_user_key', date_modified=$date_modified WHERE item_key='$item_key'");
			
			//now delete any existing response data 
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_response WHERE item_key='$item_key'");
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_resprocessing WHERE item_key='$item_key'");
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_feedback WHERE item_key='$item_key'");
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE item_key='$item_key'");									
		
		}
		
		//step through responses and add to relevant tables
		
		//see if single feedback to be used for all correct
		
		if (isset($item_data['correct_feedback_all']) && $item_data['correct_feedback_all']!='') {
		
			$correct_fblinkrefid = $item_key.'_'.$item_data['correct_feedback_all'];
			$feedback_field = 'feedback_'.$item_data['correct_feedback_all'];
			$mattext		= $item_data['feedback_'.$item_data['correct_feedback_all']];			
			
			//insert common feedback data into feedback table
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_feedback(item_key, feedback_linkref_id,mattext) VALUES ('$item_key', '$correct_fblinkrefid', '$mattext')");
		
		}
		
		//see if single feedback to be used for all wrong
		
		if (isset($item_data['wrong_feedback_all']) && $item_data['wrong_feedback_all']!='') {
		
			$wrong_fblinkrefid = $item_key.'_'.$item_data['wrong_feedback_all'];
			$feedback_field	= 'feedback_'.$item_data['wrong_feedback_all'];
			$mattext		   = $item_data['feedback_'.$item_data['wrong_feedback_all']];			
			
			//insert common feedback data into feedback table
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_feedback(item_key, feedback_linkref_id,mattext) VALUES ('$item_key', '$wrong_fblinkrefid', '$mattext')");		
		
		}
				
		for ($i=1; $i<=6; $i++) {
		
		
			if ($item_data['response_'.$i] && $item_data['response_'.$i]!='') { 
			
				$mattext = $item_data['response_'.$i];
				$ident   = $item_key.'_'.$i;
			
				//insert data into response table
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_response(item_key, mattext, ident) VALUES ('$item_key', '$mattext', '$ident')");
				
				//see if answer correct 
				$correct = isset($item_data['response_'.$i.'_correct'])? 1 : 0;
				
				//see if item has own feeback, or if common feedback
				
				if ($correct==1) {
				
					if (isset($correct_fblinkrefid)) {
					
						 $fb_linkrefid = $correct_fblinkrefid;
					
					} else if (isset($item_data['feedback_'.$i]) && $item_data['feedback_'.$i]!=''){
						$fb_linkrefid   = $item_key.'_'.$i;
						$mattext		= $item_data['feedback_'.$i];			
			
						//insert common feedback data into feedback table
						$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_feedback(item_key, feedback_linkref_id,mattext) VALUES ('$item_key', '$fb_linkrefid', '$mattext')");
						
					} else {
					
						$fb_linkrefid = '';
						
					}
					
				} else {
				
					if (isset($wrong_fblinkrefid)) {
					
						 $fb_linkrefid = $wrong_fblinkrefid;
					
					} else if (isset($item_data['feedback_'.$i]) && $item_data['feedback_'.$i]!=''){
						
						$fb_linkrefid = $item_key.'_'.$i;
						$mattext		= $item_data['feedback_'.$i];			
			
						//insert common feedback data into feedback table
						$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_feedback(item_key, feedback_linkref_id,mattext) VALUES ('$item_key', '$fb_linkrefid', '$mattext')");						
						
					} else {
					
						$fb_linkrefid = '';
						
					}
				
				}
					
				//insert data into response processing table

				$score = isset($item_data['response_score_'.$i])? $item_data['response_score_'.$i] : '';
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_resprocessing(item_key, response_ident, correct, score, feedback_type, feedback_linkref_id) VALUES ('$item_key', '$ident', '$correct', '$score', 'Response','$fb_linkrefid')");
				
				//now insert data into feedback table
				
			}		
		
		}
		
		//finally add the item to selected category links
		
		foreach ($item_data['category_keys'] as $value) {
		
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_category_item_links(category_key, item_key) VALUES ('$value','$item_key')");
		
		}
	
		return $item_key;				
		
	} //end addMultichoice

	/**
	* Add a new text answer question
	*
	* @param  array $item_data  array of posted form data
	* @return true return true if add successful
	*/
	
	function inputTextAnswer($item_data, $action='add') {
	
		global $CONN, $CONFIG;
		
		//if new add data to Item table
		
		if ($action=='add') {
		
			$name	   = $item_data['name'];
			$question   = $item_data['question'];
			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));			
				
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_item(name, mattext, response_type, render_type,  added_by_key, date_added) VALUES ('$name', '$question', 'str','string','$this->_user_key', $date_added)");
		echo $CONN->ErrorMsg();
			$item_key = $CONN->Insert_ID();
			
		} else {

			$name	 = $item_data['name'];
			$question = $item_data['question'];
			$date_modified = $CONN->DBDate(date('Y-m-d H:i:s'));			
			$item_key = $item_data['item_key'];		
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_item SET name='$name', mattext='$question',    modified_by_key='$this->_user_key', date_modified=$date_modified WHERE item_key='$item_key'");
			
			//now delete any existing response data 
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE item_key='$item_key'");									
		
		}
		//finally add the item to selected category links
		
		foreach ($item_data['category_keys'] as $value) {
		
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_category_item_links(category_key, item_key) VALUES ('$value','$item_key')");
		
		}
	
		return $item_key;				
		
	} //end inputTextAnswer

	/**
	* Check form input for a multichoice question
	*
	* @param  array $item_data  array of posted form data
	* @return array $errors array of any errors found
	* 
	*/
	
	function checkFormMultichoice($item_data) {
	
		$errors = array();
		
		//check that we have name
		if (!$item_data['name'] || $item_data['name']=='') {
		
			$errors['name'] = $this->_quiz_strings['no_name'];
		
		}
		
		//check that we have a question
		if (!$item_data['question'] || $item_data['question']=='') {
		
			$errors['question'] = $this->_quiz_strings['no_question'];
		
		} 
		
		//check that we have at least two responses
		if (!$item_data['response_1'] || !$item_data['response_2']) {
		
			$errors['response'] = $this->_quiz_strings['multi_no_response'];
		
		} 
		
		//check that we have at least one correct answer
				
		for ($n=1;$n<=6;$n++) {
		
			if (isset($item_data['response_'.$n.'_correct']) && $item_data['response_'.$n.'_correct']=='1') {
			
			$correct_answer = true;
			
			} 
		   
		}
		   
		if (!$correct_answer) {
		
			$errors['correct'] = $this->_quiz_strings['multi_no_correct'];
			
		}
		$count = count($item_data['category_keys']);
		
		if (count($item_data['category_keys'])==0) {
		
			$errors['category'] = $this->_quiz_strings['no_category'];
			
		}
	
		return $errors;
							
		
	} //end checkFormMultiChoice

	/**
	* Check form input for a text answer question
	*
	* @param  array $item_data  array of posted form data
	* @return array $errors array of any errors found
	* 
	*/
	
	function checkFormTextAnswer($item_data) {
	
		$errors = array();
		
		//check that we have name
		if (!$item_data['name'] || $item_data['name']=='') {
		
			$errors['name'] = $this->_quiz_strings['no_name'];
		
		}
		
		//check that we have a question
		if (!$item_data['question'] || $item_data['question']=='') {
		
			$errors['question'] = $this->_quiz_strings['no_question'];
		
		} 
				
		$count = count($item_data['category_keys']);
		
		if (count($item_data['category_keys'])==0) {
		
			$errors['category'] = $this->_quiz_strings['no_category'];
			
		}
	
		return $errors;
							
		
	} //end checkFormTextAnswer

	/**
	* Check form input for a multichoice question
	*
	* @param  array $item_data  array of posted form data
	* @return true if successful
	* 
	*/
	
	function deleteMultichoice($item_data) {
	
		global $CONN, $CONFIG;
		
		$item_key = $item_data['item_key'];
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_item WHERE item_key='$item_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_response WHERE item_key='$item_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_resprocessing WHERE item_key='$item_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_feedback WHERE item_key='$item_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE item_key='$item_key'");	
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE item_key='$item_key'");								
		
		return true;
							
		
	} //end deleteMultiChoice	
	function deleteTextAnswer($item_data) {
	
		global $CONN, $CONFIG;
		
		$item_key = $item_data['item_key'];
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_item WHERE item_key='$item_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE item_key='$item_key'");	
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE item_key='$item_key'");								
		
		return true;
							
		
	} //end deleteTextAnswer

	/**
	* Retrieve question data for modifying
	*
	* @param  int $item_key key of item to get data for
	* @return array $item_data array of item data
	* 
	*/
	
	function getItemData($item_key) {
	
		global $CONN,$CONFIG;

		$item_data = array();
		
		$rs = $CONN->Execute("SELECT name, mattext, rcardinality, response_type, render_type FROM {$CONFIG['DB_PREFIX']}qt_item WHERE item_key='$item_key'");

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {
			
				$item_data['name']		 = $rs->fields[0];
				$item_data['question']	 = $rs->fields[1];
				$item_data['rcardinality'] = $rs->fields[2];
				$item_data['response_type'] = $rs->fields[3];
				$item_data['render_type']  = $rs->fields[4];												
				$rs->MoveNext();
				
			}
			
		}
		
		//now get the response data for this item
		$this->getResponseData($item_key, $item_data);
		$item_data['category_keys'] = $this->getItemCategories($item_key);
		return $item_data;
				
	} //end getItemData
	
	/**
	* Retrieve response data for a given item
	*
	* @param  int $item_key key of item to get data for
	* @return array $response_data array of response data
	* 
	*/
	
	function getResponseData($item_key, &$item_data) {
	
		global $CONN,$CONFIG;
		
		$rs = $CONN->Execute("SELECT mattext, ident FROM {$CONFIG['DB_PREFIX']}qt_response WHERE item_key='$item_key'");
		
		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {
				
				$ident1 = $rs->fields[1];
				$ident2 = explode('_',$rs->fields[1]);
				$ident2 = $ident2[1];
				$item_data['response_'.$ident2] = $rs->fields[0];
				$this->getResProcessingData($item_data, $ident1, $ident2);
				$rs->MoveNext();
				
			}
			
		}		
		
		return true;
		
				
	} //end getResponseData
	
	/**
	* Retrieve response data for a given item
	*
	* @param  int $item_key key of item to get data for
	* @return array $response_data array of response data
	* 
	*/
	
	function getResProcessingData(&$item_data, $ident, $ident2) {
	
		global $CONN,$CONFIG;
		
		$rs = $CONN->Execute("SELECT correct, score, feedback_linkref_id FROM {$CONFIG['DB_PREFIX']}qt_resprocessing WHERE response_ident='$ident'");

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {
			
				$item_data['response_'.$ident2.'_correct'] = $rs->fields[0];
				$item_data['response_score_'.$ident2] = $rs->fields[1];
				$linkrefid = $rs->fields[2];
				$this->getFeedback($linkrefid, $item_data, $item_data['response_'.$ident2.'_correct']);
				$rs->MoveNext();
				
			}
			
		}		
		
		$rs->Close();

		return true;
		
				
	} //end getResponseData
	
	/**
	* Retrieve feedback for given item
	*
	* @param  int $item_key key of item to get data for
	* @return array $response_data array of response data
	* 
	*/
	
	function getFeedback($linkrefid, &$item_data, $correct) {
	
		global $CONN,$CONFIG;
		
		$rs = $CONN->Execute("SELECT mattext FROM {$CONFIG['DB_PREFIX']}qt_feedback WHERE feedback_linkref_id='$linkrefid'");

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {
			
				$ident = explode('_',$linkrefid);
				$ident = $ident[1];
				$item_data['feedback_'.$ident] = $rs->fields[0];

				//see if feedback for this response is used for other items also
		
				$rs2 = $CONN->Execute("SELECT item_key FROM {$CONFIG['DB_PREFIX']}qt_resprocessing WHERE feedback_linkref_id='$linkrefid'");						
				
				if ($rs2->RecordCount()>1) {

					if ($correct==1) {
					
						$item_data['correct_feedback_all'] = $ident;
						
					} else {
					
						$item_data['wrong_feedback_all'] = $ident;
					
					}
				
				
				}
							
				$rs->MoveNext();
				
			}
			
		}		
		
		return true;
		
				
	} //end getResponseData		
	
	/**
	* Retrieve an array of categories that said item is attached to
	*
	* @param  int $item_key key of item to get data for
	* @return array $item_categories array of categroies item is attached to
	* 
	*/
	
	function getItemCategories($item_key) {
	
		global $CONN,$CONFIG;
		
		$item_categories = array();
		
		$rs = $CONN->Execute("SELECT category_key FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE item_key='$item_key'");

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			$n = 1;
			
			while (!$rs->EOF) {
			
				$item_categories[$n] = $rs->fields[0];
				$n++;
				$rs->MoveNext();
				
			}
			
		}		
		$rs->Close();

		return $item_categories;
		
				
	} //end getItemCategories
	
	
	
	/**
	* Remove items from current module
	*
	* @param  array $delete_item_keys array of items to be deleted
	* @return true
	* 
	*/
	
	function removeItems($link_keys) {
	
		global $CONN,$CONFIG;
		
		foreach ($link_keys as $link_key) {
		
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE link_key='$link_key' AND module_key='$this->_module_key'");
			
		}
		
		return true;
				
	} //end removeItems
	
	/**
	* Check form input for a inputing a category
	*
	* @param  int $category_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  string $category_name  name field of category		
	* @return array $errors array of any errors found
	* 
	*/
	
	function checkFormCategory($category_name, $category_key='', $parent_key='') {
	
		global $general_strings;
		$errors = array();
		
		//check that we have name
		if (!$category_name || $category_name=='') {
		
			$errors['name'] = $general_strings['no_name'];
		
		}
		
		//check that we have a question
		if ((isset($category_key) && $category_key!='') && ($category_key==$parent_key)) {
		
			$errors['parent'] = $this->_quiz_strings['own_parent'];
		
		} 

		return $errors;
							
		
	} //end checkFormCategory
	
	/**
	* Add a new category
	*
	* @param  int $category_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  string $category_name  name field of category		
	* @return array $errors array of any errors found
	* 
	*/
	
	function addCategory($category_name, $parent_key='') {
	
		global $CONN, $CONFIG;

		$name	 = $category_name;
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_categories(name, parent_key, user_key, space_key) VALUES ('$name', '$parent_key', '$this->_user_key', '$this->_space_key')");
		$category_key = $CONN->Insert_ID();	
		return $category_key;
						
	} //end addCategory
	
	/**
	* Get name and parent data for a given category
	*
	* @param  int $category_key  key of category
	* @param  string $category_name  name field of category		
	* @return array $category_data array of category data
	* 
	*/
	
	function getCategoryData($category_key) {
	
		global $CONN, $CONFIG;

		$rs = $CONN->Execute("SELECT name, parent_key FROM {$CONFIG['DB_PREFIX']}qt_categories WHERE category_key='$category_key'");

		while (!$rs->EOF) {
		
			$category_data['name']	   = $rs->fields[0];
			$category_data['parent_key'] = $rs->fields[1];
			$rs->MoveNext();
			
		}
		$rs->Close();	
		return $category_data;
						
	} //end getCategoryData		

	/**
	* modify and existing category
	*
	* @param  int $category_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  string $category_name  name field of category
	* @return true
	* 
	*/
	
	function modifyCategory($category_key, $category_name, $parent_key) {
	
		global $CONN, $CONFIG;

		$name	 = $category_name;
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_categories SET name='$name', parent_key='$parent_key' WHERE category_key='$category_key'");

		return true;
						
	} //end modifyCategory
	
	/**
	* Get data for a given response
	*
	* @param  string $ident  ident of response
	* @param  string $type type of response
	* @return array $response_data array of response data
	* 
	*/
	
	function getResponseData2($item_key, $ident, $type) {
	
		global $CONN, $CONFIG;

		if ($type=='Single') {
		
			$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}qt_response.mattext, {$CONFIG['DB_PREFIX']}qt_resprocessing.score, correct, {$CONFIG['DB_PREFIX']}qt_feedback.mattext, {$CONFIG['DB_PREFIX']}qt_module_item_links.score FROM {$CONFIG['DB_PREFIX']}qt_response,{$CONFIG['DB_PREFIX']}qt_module_item_links, {$CONFIG['DB_PREFIX']}qt_resprocessing LEFT JOIN {$CONFIG['DB_PREFIX']}qt_feedback ON {$CONFIG['DB_PREFIX']}qt_resprocessing.feedback_linkref_id={$CONFIG['DB_PREFIX']}qt_feedback.feedback_linkref_id WHERE {$CONFIG['DB_PREFIX']}qt_response.ident={$CONFIG['DB_PREFIX']}qt_resprocessing.response_ident AND {$CONFIG['DB_PREFIX']}qt_module_item_links.item_key={$CONFIG['DB_PREFIX']}qt_response.item_key AND {$CONFIG['DB_PREFIX']}qt_resprocessing.response_ident='$ident' AND {$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$this->_module_key'");
			
			while (!$rs->EOF) {
			
				$response_data['response_mattext'] = $rs->fields[0];
				$response_data['score']			= $rs->fields[1];
				$response_data['correct']		  = $rs->fields[2];
				$response_data['feedback']		 = $rs->fields[3];
				$response_data['item_score']	   = $rs->fields[4];												
				$rs->MoveNext();
			
			}  
			
			$rs->Close();
			
		} else {
		
			$rs = $CONN->Execute("SELECT response_ident, {$CONFIG['DB_PREFIX']}qt_resprocessing.score,  {$CONFIG['DB_PREFIX']}qt_module_item_links.score FROM {$CONFIG['DB_PREFIX']}qt_resprocessing, {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE {$CONFIG['DB_PREFIX']}qt_module_item_links.item_key={$CONFIG['DB_PREFIX']}qt_resprocessing.item_key AND ({$CONFIG['DB_PREFIX']}qt_resprocessing.item_key='$item_key' AND correct='1') AND {$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$this->_module_key'");

			sort($ident);
			$correct = array();
			$n=0;
	
			while (!$rs->EOF) {
		
				$correct[$n]				 = $rs->fields[0];
				$response_data['score']	  = $rs->fields[1]+$response_data['score'];
				$response_data['item_score'] = $rs->fields[3];						
				$n++;
				$rs->MoveNext();
	
			}
			sort($correct);
			$diff = array_diff($ident, $correct);

			if (count($diff)>0 || (count($ident)<count($correct))) {
	
				$response_data['correct'] = 0;
				$rs = $CONN->Execute("SELECT response_ident, score, {$CONFIG['DB_PREFIX']}qt_feedback.mattext FROM {$CONFIG['DB_PREFIX']}qt_resprocessing, {$CONFIG['DB_PREFIX']}qt_feedback WHERE {$CONFIG['DB_PREFIX']}qt_feedback.feedback_linkref_id={$CONFIG['DB_PREFIX']}qt_resprocessing.feedback_linkref_id AND ({$CONFIG['DB_PREFIX']}qt_resprocessing.item_key='$item_key' AND correct='0')");
					
				while (!$rs->EOF) {
		
					$response_data['score']	= $rs->fields[1];
					$response_data['feedback'] = $rs->fields[2];		
					$rs->MoveNext();
	
				}
		
			} else {
	
				$response_data['correct'] = 1;
				$rs = $CONN->Execute("SELECT response_ident, score, {$CONFIG['DB_PREFIX']}qt_feedback.mattext FROM {$CONFIG['DB_PREFIX']}qt_resprocessing, {$CONFIG['DB_PREFIX']}qt_feedback WHERE {$CONFIG['DB_PREFIX']}qt_feedback.feedback_linkref_id={$CONFIG['DB_PREFIX']}qt_resprocessing.feedback_linkref_id AND ({$CONFIG['DB_PREFIX']}qt_resprocessing.item_key='$item_key' AND correct='1')");
					
				while (!$rs->EOF) {
		
					$response_data['score']	= $rs->fields[1];
					$response_data['feedback'] = $rs->fields[2];		
					$rs->MoveNext();
	
				}
		
			}
		
		}

		return $response_data;
						
	} //end getResponseData2
	
	/**
	* Get text of correct reponse
	*
	* @param  int $item_key  key of item to get correct answer for
	* @param  string $type  Single or Multiple
	* @return string $correct_reponse string of correct reponse
	* 
	*/
	
	function getCorrectResponse($item_key, $type) {
	
		global $CONN, $CONFIG;

		if ($type=='Single') {
		
		   $rs =  $CONN->Execute("SELECT mattext FROM {$CONFIG['DB_PREFIX']}qt_response, {$CONFIG['DB_PREFIX']}qt_resprocessing WHERE {$CONFIG['DB_PREFIX']}qt_response.ident={$CONFIG['DB_PREFIX']}qt_resprocessing.response_ident AND  {$CONFIG['DB_PREFIX']}qt_response.item_key='$item_key' AND correct='1'");
	
			while(!$rs->EOF) {
			
				$correct_response = $rs->fields[0];
				$rs->MoveNext();
				
			}
			
		} else if ($type='Multiple') {
		
		   $rs =  $CONN->Execute("SELECT mattext FROM {$CONFIG['DB_PREFIX']}qt_response, {$CONFIG['DB_PREFIX']}qt_resprocessing WHERE {$CONFIG['DB_PREFIX']}qt_response.ident={$CONFIG['DB_PREFIX']}qt_resprocessing.response_ident AND  {$CONFIG['DB_PREFIX']}qt_response.item_key='$item_key' AND correct='1'");
		   
		   	while(!$rs->EOF) {
			
				$correct_response .= $rs->fields[0].'<br>';
				$rs->MoveNext();
				
			}			
		
		}

		return $correct_response;
						
	} //end getCorrectResponse
	
	/**
	* Get text of given reponse
	*
	* @param  string $ident  ident of response to get correct answer for
	* @return string $reponse_mattext string of given reponse
	* 
	*/
	
	function getresponse_text($ident) {
	
		global $CONN, $CONFIG;
		
		$rs =  $CONN->Execute("SELECT mattext FROM {$CONFIG['DB_PREFIX']}qt_response WHERE ident='$ident'");
		echo $CONN->ErrorMsg();
	
		while(!$rs->EOF) {
			
			$response_mattext = $rs->fields[0];
			$rs->MoveNext();
				
		}
		
		return $response_mattext ;
		
	} //end getresponse_text()
	
	/**
	* Register the start of an attempt at a quiz
	*
	* @return int $attempt_key 
	* 
	*/
	
	function startAttempt() {
	
		global $CONN, $CONFIG;
		
		$time_started = $CONN->DBDate(date('Y-m-d H:i:s'));
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_attempts(module_key, user_key, time_started) VALUES ('$this->_module_key','$this->_user_key', $time_started)");
		$attempt_key = $CONN->Insert_ID();
		return $attempt_key;
		
	} //end startAttempt()
	
	/**
	* Save attempt data for current attempt
	*
	* @param  string $total_score  total score for current attempt
	* @return true 
	* 
	*/
	
	function saveAttemptData($total_score, $attempt_data, $item_responses) {
	
		global $CONN, $CONFIG;
		
		$time_finished = $CONN->DBDate(date('Y-m-d H:i:s'));
		$attempt_key = $attempt_data['attempt_key'];
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_attempts SET time_finished=$time_finished, score='$total_score' WHERE attempt_key='$attempt_key'");

		//now add each of the responses to the attempt data table
		
		foreach ($attempt_data['item_keys'] as $item_key) {
		
			$response_ident = '';
			
			if (isset($attempt_data['response_text_'.$item_key])) {
				$response_text = $attempt_data['response_text_'.$item_key];
				$response_ident = ''; 
				//set correct field to 2 which means not marked yet.
				$correct = 2;
				$manual_score = 1;
			} else {
				
				$correct = $item_responses[$item_key]['correct'];
			}
			if(count($attempt_data['responses_'.$item_key])>1) {
			
				foreach ($attempt_data['responses_'.$item_key] as $response) {
				
					$response_ident .= $response.',';
				}			
			
			} else {
			
				$response_ident = $attempt_data['responses_'.$item_key][0];
			
			}
			
			
			
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_attempts_data(attempt_key, item_key, response_ident, correct, response_text) VALUES ('$attempt_key','$item_key', '$response_ident','$correct','$response_text')");
		
		
		}
		
		
		//see if there is a gradebook link for this module, if so add results to gradebook
		if (!isset($manual_score)) {
			$this->updategradeBook($total_score, $time_finished, $this->_user_key);
		}
				
		return true;
		
	} //end saveAttemptData()
	
	/**
	* Get the number of times a user has attempted quiz
	*
	* @param  string $user_key  key of user to count attempts for
	* @param  string $module_key  key of module to count attempts for	
	* @return int $attempt_count number of attempts for given user
	* 
	*/
	
	function getAttemptCount($user_key, $module_key) {
	
		global $CONN, $CONFIG;
		
		$rs =  $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE user_key='$user_key' AND module_key='$module_key'");
		
		$attempt_count = $rs->RecordCount();
		$rs->Close();
		return $attempt_count ;
		
	} //end getAttemptCount()
	
	/**
	* Get the possible total score for a given quiz
	*
	* @param  int $module_key  key of module to calculate total for	
	* @return int $possible_total 
	* 
	*/
	
	function getPossibleTotal($module_key) {
	
		global $CONN, $CONFIG;
		
		$possible_total = 0;
		$item_key = '';
		$rs =  $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}qt_module_item_links.item_key, {$CONFIG['DB_PREFIX']}qt_module_item_links.score, {$CONFIG['DB_PREFIX']}qt_resprocessing.score, {$CONFIG['DB_PREFIX']}qt_item.rcardinality FROM {$CONFIG['DB_PREFIX']}qt_module_item_links, {$CONFIG['DB_PREFIX']}qt_resprocessing, {$CONFIG['DB_PREFIX']}qt_item WHERE {$CONFIG['DB_PREFIX']}qt_module_item_links.item_key={$CONFIG['DB_PREFIX']}qt_resprocessing.item_key AND {$CONFIG['DB_PREFIX']}qt_module_item_links.item_key={$CONFIG['DB_PREFIX']}qt_item.item_key AND {$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$module_key' AND  {$CONFIG['DB_PREFIX']}qt_resprocessing.correct='1' ORDER BY {$CONFIG['DB_PREFIX']}qt_module_item_links.item_key");

		while(!$rs->EOF) {
		
			$item_key2	   = $rs->fields[0];
			$item_score	  = $rs->fields[1];
			$response_score  = $rs->fields[2];
			$rcardinality	= $rs->fields[3];			
			
			if ($rcardinality=='Multiple' && $item_score>0) {
			
				if ($item_key!=$item_key2) {
				
					$item_key = $item_key2;
					$possible_total = $possible_total+$item_score;
				
				}
				
			} else if ($rcardinality=='Multiple' && $item_score==0) {
			
			
				$possible_total = $possible_total+$response_score;
			
			
			} else {

				if ($item_score>0) {
			
					$possible_total = $possible_total+$item_score;
				
				} else {
			
					$possible_total = $possible_total+$response_score;
				
				}
			
			}
			
			$rs->MoveNext();

		}
					
		$rs->Close();
		return $possible_total ;
		
	} //end getPossibleTotal()
	
	/**
	* Get results for a given user
	*
	* @param  string $user_key  key of user to count attempts for
	* @param  string $module_key  key of module to count attempts for	
	* @return string $results 
	* 
	*/
	
	function getUserResults($user_key, $module_key) {
	
		global $CONN, $CONFIG, $objDates;
		
		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}
		
		if (!is_object($objDates)) {

			$objDates = new InteractDate();
	
		}		
		
		$rs =  $CONN->Execute("SELECT score, time_started, time_finished, attempt_key FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE user_key='$user_key' AND module_key='$module_key' ORDER BY attempt_key");
	
		if ($rs->EOF){
		
			return false;
			
		} else {
		
			while(!$rs->EOF) {
			
				$unix_start = $CONN->UnixTimestamp($rs->fields[1]);
				$start_date = $objDates->formatDate($unix_start,'short', true);
				$unix_finish = $CONN->UnixTimestamp($rs->fields[2]);
				if ($unix_finish==0) {
				
					$time_taken = $this->_quiz_strings['not_completed'];
					
				} else {
				
					$time_taken = round(($unix_finish-$unix_start)/60).' '.$this->_quiz_strings['minutes'];
				}
				if ($this->_is_admin==true) {
				
					$check_box = '<input name="attempt_keys[]" type="checkbox" id="attempt_key" value="'.$rs->fields[3].'">';
					
				} else {
				
					$check_box = '';
				
				}
				
				$result .= $check_box.$rs->fields[0].' - '.$time_taken.'<span class="small"> ('.$start_date.')</span><br />';
				$rs->MoveNext();
				
			}
			$rs->Close();
			return $result ;
			
		}
		
	} //end getUserResults()
	
	/**
	* A method of class Quiz to get a list of users for current space or group
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
		
			$username = array();
			$user_number = array();
			
			while (!$rs->EOF) {
			
			   $username[$rs->fields[1]] = $rs->fields[0];
			   $user_number[$rs->fields[1]] = $rs->fields[2];
			   $rs->MoveNext(); 
			
			}
			
			$rs->Close();
			asort($username);
			$user_data['by_name']   = $username;
			$user_data['by_number'] = $user_number;			
			return $user_data;

		}
	
	} //end getUserList
	
	/**
	* A method of class Quiz to get sql for retrieving users for current space or group
	*
	* @return string $user_sql sql string to retrieve userlist
	*/
	
	function getUserSql() {
	
		global $CONN, $CONFIG;
		
		$concat = $CONN->Concat("{$CONFIG['DB_PREFIX']}users.last_name",'\', \'',"{$CONFIG['DB_PREFIX']}users.first_name");
		
		if ($this->_group_key=='0') {
					
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}space_user_links.user_key, {$CONFIG['DB_PREFIX']}users.user_id_number FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND space_key='$this->_space_key' AND ({$CONFIG['DB_PREFIX']}space_user_links.access_level_key!='3')";
			
		} else { 
			
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}group_user_links.user_key, {$CONFIG['DB_PREFIX']}users.user_id_number  FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$this->_group_key'";
			
		} 
		
		return $user_sql;
	
	} //end getUserSql
	
	/**
	* Delete all data for a given attempt
	*
	* @param  int $attempt_key  key of attempt to delete
	* @return true 
	* 
	*/
	
	function deleteAttempt($attempt_key) {
	
		global $CONN, $CONFIG;
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE  attempt_key='$attempt_key'");
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE  attempt_key='$attempt_key'");
		
		return true ;
		
	} //end deleteAttempt()
	
	/**
	* Count number of wrong or right ansers for a given item
	*
	* @param  int $item_key  key of item to count results for
	* @param  int $module_key  key of module to count results in
	* @param  string $type  correct or wrong		
	* @return string $count count for responses
	* 
	*/
	
	function countResponses($item_key, $module_key, $type) {
	
		global $CONN, $CONFIG;
		
		if ($type=='correct') {
		
			$rs =  $CONN->Execute("SELECT COUNT(item_key)  FROM {$CONFIG['DB_PREFIX']}qt_attempts_data ,{$CONFIG['DB_PREFIX']}qt_attempts WHERE {$CONFIG['DB_PREFIX']}qt_attempts_data.attempt_key={$CONFIG['DB_PREFIX']}qt_attempts.attempt_key AND {$CONFIG['DB_PREFIX']}qt_attempts_data.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}qt_attempts.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}qt_attempts_data.correct='1'");
	
			while(!$rs->EOF) {
			
				$count = $rs->fields[0];
				$rs->MoveNext();
				
			}
			
		} else 	if ($type=='wrong') {
		
			$rs =  $CONN->Execute("SELECT COUNT(item_key)  FROM {$CONFIG['DB_PREFIX']}qt_attempts_data ,{$CONFIG['DB_PREFIX']}qt_attempts WHERE {$CONFIG['DB_PREFIX']}qt_attempts_data.attempt_key={$CONFIG['DB_PREFIX']}qt_attempts.attempt_key AND {$CONFIG['DB_PREFIX']}qt_attempts_data.item_key='$item_key' AND {$CONFIG['DB_PREFIX']}qt_attempts.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}qt_attempts_data.correct='0'");

			while(!$rs->EOF) {
			
				$count = $rs->fields[0];
				$rs->MoveNext();
				
			}
			
		}
		
		return $count ;
		
	} //end countResponses()
	
	/**
	* Delete a category
	*
	* @param  int $category_key  key of category to delete
	* @param  string $delete_option action to take with attached items
	* @param  int $move_to_category if moving attached items category to move them to		
	* @return true 
	* 
	*/
	
	function deleteCategory($category_key, $delete_option, $move_to_key) {
	
		global $CONN, $CONFIG;
		
		if (empty($category_key)) {
			return false;
		}
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_categories WHERE category_key='$category_key'");
		 
		if ($delete_option=='delete') {
		
			$rs = $CONN->Execute("SELECT item_key FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE category_key='$category_key'");
			
			$item_data=array();
			while (!$rs->EOF) {
			
				$item_data['item_key'] = $rs->fields[0];
				$this->deleteMultiChoice($item_data);
				$rs->MoveNext();
			
			}
			$rs->Close();
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_category_item_links WHERE category_key='$category_key'");
			
		
		} else {
		
			 $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_category_item_links SET category_key='$move_to_key' WHERE category_key='$category_key'");
		
		}		
		return true ;
		
	} //end deleteCategories()	
	
	
	/**
	* Delete a category
	*
	* @param  int $total score of quiz
	* @param  date $time_finished time quiz finished
	* @return true 
	* 
	*/
	function updategradebook($total_score,$time_finished, $user_key) {
		
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT item_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE content_module_key='$this->_module_key'");

		if (!$rs->EOF) {
		
			while (!$rs->EOF) {
			
				$gradebook_item_key = $rs->fields[0];
				$rs->MoveNext();
				
			}
			$rs->Close();
			
			//see if there is already a gradebook entry for this user
			
			$rs = $CONN->Execute("SELECT grade_key FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE item_key='$gradebook_item_key' AND user_key='$user_key'");

	
			if (!$rs->EOF) {
			
				while (!$rs->EOF) {
				
					$grade_key = $rs->fields[0];
					$rs->MoveNext();
					
				} 
				$rs->Close();
				
				//now see how to handle score, eg. average. higest, last
				
				switch($this->_quiz_data['grading_key']) {
				
					case 1: 
					
						if ($total_score>$grade_key) {
						
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET grade_key='$total_score', date_modified=$time_finished WHERE item_key='$gradebook_item_key' AND user_key='$user_key'");
							
						}					
					
					break;
					
					case 2:
					
					   $rs = $CONN->Execute("SELECT AVG(score) FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE user_key='$user_key' AND module_key='$this->_module_key'");
					   
					   while (!$rs->EOF) {
					   
						   $average = round($rs->fields[0],1);
						   $rs->MoveNext();
						   
					   }
					   $rs->Close();
					   $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET grade_key='$average', date_modified=$time_finished WHERE item_key='$gradebook_item_key' AND user_key='$user_key'");					
					
					break;
					
					case 4:

						$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET grade_key='$total_score', date_modified=$time_finished WHERE item_key='$gradebook_item_key' AND user_key='$user_key'");					
					
					break;
				
				
				}
			
			
			} else {
			
				//no existing entry so add a new one
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}gradebook_item_user_links(item_key, user_key, date_added, grade_key) VALUES ('$gradebook_item_key','$user_key', $time_finished, '$total_score')");
				
				echo $CONN->ErrorMsg();
				
			}
			
		}
		return true;
	}//end updategradeBook
					
} //end InteractQuiz class	
?>
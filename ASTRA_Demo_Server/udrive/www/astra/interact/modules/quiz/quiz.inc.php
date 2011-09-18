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
* Quiz module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* quiz
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: quiz.inc.php,v 1.10 2007/01/29 01:34:38 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new quiz module
*
* @param  int $module_key  key of new quiz module
* @return true if details added successfully
*/


function add_quiz($module_key) {
 	
	global $CONN, $CONFIG;
		
	if ($_POST['open_date_month']) { 
		
		$open_date = $_POST['open_date_year'].'-'.$_POST['open_date_month'].'-'.$_POST['open_date_day'].' '.$_POST['open_date_hour'].':'.$_POST['open_date_minute'];
		$open_date = $CONN->DBDate(date($open_date));
			
	} else {
	
		
		$open_date = "''";
		
	}
		
	if ($_POST['close_date_month']) { 
		
		$close_date = $_POST['close_date_year'].'-'.$_POST['close_date_month'].'-'.$_POST['close_date_day'].' '.$_POST['close_date_hour'].':'.$_POST['close_date_minute'];
		$close_date = $CONN->DBDate(date($close_date));
			
	} else {
	
		$close_date = "''";
		
	}		
		
	$type_key		  = $_POST['type_key'];
	$attempts		  = $_POST['attempts'];
	$shuffle_questions = $_POST['shuffle_questions'];
	$shuffle_answers   = $_POST['shuffle_answers'];
	$grading_key	   = $_POST['grading_key'];
	$build			 = $_POST['build'];
	$show_feedback	 = $_POST['show_feedback'];
	$feedback_attempts	 = $_POST['feedback_attempts'];
	$answer_attempts	 = $_POST['answer_attempts'];
	$show_correct	  = $_POST['show_correct'];
	$minutes_allowed   = $_POST['minutes_allowed'];		
		
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}quiz_settings(module_key, type_key, open_date, close_date, attempts, shuffle_questions, shuffle_answers, grading_key, build_on_previous, show_feedback, show_correct, minutes_allowed, answer_attempts, feedback_attempts) VALUES ('$module_key', '$type_key', $open_date, $close_date, '$attempts', '$shuffle_questions','$shuffle_answers','$grading_key','$build', '$show_feedback', '$show_correct','$minutes_allowed','$answer_attempts','$feedback_attempts' )";
		
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your quiz: '.$CONN->ErrorMsg().' <br />';
		echo $message;
		return $message;
		
	} else {	  
	
		return true;  
		
	}										

}

/**
* Function called by Module class to get exisiting quiz data 
*
* @param  int $module_key  key of quiz module
* @return true if data retrieved
*/

function get_quiz_data($module_key) {

	 
	 global $CONN, $module_data, $CONFIG;
	 
	 $sql = "SELECT type_key, open_date, close_date, attempts, shuffle_questions, shuffle_answers, grading_key, build_on_previous, show_correct, show_feedback, minutes_allowed, answer_attempts, feedback_attempts FROM {$CONFIG['DB_PREFIX']}quiz_settings WHERE module_key='$module_key'";
	 
	 $rs = $CONN->Execute($sql);
	 
	 while (!$rs->EOF) {
	 
		 $module_data['type_key']		  = $rs->fields[0];
		 $module_data['open_date_unix']	= $CONN->UnixTimestamp($rs->fields[1]);
		 $module_data['close_date_unix']   = $CONN->UnixTimestamp($rs->fields[2]);		 
		 $module_data['attempts']		  = $rs->fields[3];
		 $module_data['shuffle_questions'] = $rs->fields[4];
		 $module_data['shuffle_answers']   = $rs->fields[5];
		 $module_data['grading_key']	   = $rs->fields[6];
		 $module_data['build']			 = $rs->fields[7];
		 $module_data['show_correct']	  = $rs->fields[8];
		 $module_data['show_feedback']	 = $rs->fields[9];
		 $module_data['minutes_allowed']   = $rs->fields[10];	
		 $module_data['answer_attempts']   = $rs->fields[11];
		 $module_data['feedback_attempts']   = $rs->fields[12];	 		 		 
	 
		 $rs->MoveNext();
		 
	 }

	 return true;

}

/**
* Function called by Module class to modify exisiting quiz data 
*
* @param  int $module_key  key of quiz module
* @param  int $link_key  link key of quiz module being modified
* @return true if successful
*/

function modify_quiz($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	
	if ($_POST['open_date_month']) { 
		
		$open_date = $_POST['open_date_year'].'-'.$_POST['open_date_month'].'-'.$_POST['open_date_day'].' '.$_POST['open_date_hour'].':'.$_POST['open_date_minute'];
		$open_date = $CONN->DBDate(date($open_date));
			
	}else {
	
		$open_date = "''";
		
	}
		
	if ($_POST['close_date_month']) { 
		
		$close_date = $_POST['close_date_year'].'-'.$_POST['close_date_month'].'-'.$_POST['close_date_day'].' '.$_POST['close_date_hour'].':'.$_POST['close_date_minute'];
		$close_date = $CONN->DBDate(date($close_date));
			
	}else {
			
		$close_date = "''";
		
	}		
		
	$type_key		  = $_POST['type_key'];
	$attempts		  = $_POST['attempts'];
	$shuffle_questions = $_POST['shuffle_questions'];
	$shuffle_answers   = $_POST['shuffle_answers'];
	$grading_key	   = $_POST['grading_key'];
	$build			 = $_POST['build'];
	$show_correct	  = $_POST['show_correct'];
	$show_feedback	 = $_POST['show_feedback'];
	$minutes_allowed   = $_POST['minutes_allowed'];
	$feedback_attempts	 = $_POST['feedback_attempts'];
	$answer_attempts	 = $_POST['answer_attempts'];		
		
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}quiz_settings SET type_key='$type_key', open_date=$open_date, close_date=$close_date, attempts='$attempts', shuffle_questions='$shuffle_questions', shuffle_answers='$shuffle_answers', grading_key='$grading_key', build_on_previous='$build', show_correct='$show_correct', show_feedback='$show_feedback', minutes_allowed='$minutes_allowed',answer_attempts='$answer_attempts', feedback_attempts='$feedback_attempts' WHERE module_key='$module_key'";
		
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your quiz: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
		return true;  
		
	}								


} //end modify_quiz


/**
* Function called by Module class to delete exisiting quiz data 
*
* @param  int $module_key  key of quiz module
* @param  int $space_key  space key of quiz module
* @param  int $link_key  link key of quiz module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_quiz($module_key,$space_key,$link_key,$delete_action) 
{

	global $CONN, $CONFIG;
	
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}quiz_settings WHERE module_key='$module_key'");
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE module_key='$module_key'");
	$rs = $CONN->Execute("SELECT attempt_key FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE module_key='$module_key'");
	
	while (!$rs->EOF) {
	
		$attempt_key = $rs->fields[0];
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE attempt_key='$attempt_key'");
		$rs->MoveNext();	
	}
	 
	return true;

} //end delete_quiz

/**
* Function called by Module class to flag a quiz for deletion 
*
* @param  int $module_key  key of quiz module
* @param  int $space_key  space key of quiz module
* @param  int $link_key  link key of dropbox quiz being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_quiz_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_quiz_for_deletion   

/**
* Function called by Module class to copy a quiz 
*
* @param  int $existing_module_key  key of quiz being copied
* @param  int $existing_link_key  link key of quiz module being copied
* @param  int $new_module_key  key of quiz being created
* @param  int $new_link_key  link key of quiz module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of quiz module
* @param  int $new_group_key  quiz key of new folder
* @return true if successful
*/
function copy_quiz($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	 global $CONN, $CONFIG;
	 
	 $sql = "SELECT type_key, open_date, close_date, attempts, shuffle_questions, shuffle_answers, grading_key, build_on_previous, minutes_allowed FROM {$CONFIG['DB_PREFIX']}quiz_settings WHERE module_key='$existing_module_key'";
	 
	 $rs = $CONN->Execute($sql);
	 
	 while (!$rs->EOF) {
	 
		 $type_key		  = $CONN->qstr($rs->fields[0]);
		 $open_date		 = $CONN->qstr($rs->fields[1]);
		 $close_date		= $CONN->qstr($rs->fields[2]);		 
		 $attempts		  = $CONN->qstr($rs->fields[3]);
		 $shuffle_questions = $CONN->qstr($rs->fields[4]);
		 $shuffle_answers   = $CONN->qstr($rs->fields[5]);
		 $grading_key	   = $CONN->qstr($rs->fields[6]);
		 $build			 = $CONN->qstr($rs->fields[7]);
		 $minutes_allowed   = $CONN->qstr($rs->fields[8]);		 
	 
		 $rs->MoveNext();
		 
	 }
	 	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}quiz_settings(module_key, type_key, open_date, close_date, attempts, shuffle_questions, shuffle_answers, grading_key, build_on_previous, minutes_allowed) VALUES ('$new_module_key', $type_key, $open_date, $close_date, $attempts, $shuffle_questions,$shuffle_answers,$grading_key,$build, $minutes_allowed)";
		
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your quiz: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
		$sql = "SELECT item_key, sort_order, score FROM {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE module_key='$existing_module_key'";
	 
		$rs = $CONN->Execute($sql);
	 
		 while (!$rs->EOF) {
	 
			 $item_key		  = $rs->fields[0];
			 $sort_order		 = $rs->fields[1];
			 $score			 = $rs->fields[2];		 
	 		 $CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}qt_module_item_links(module_key, item_key, sort_order, score) VALUES ('$new_module_key', '$item_key', '$sort_order', '$score')");
	 
			 $rs->MoveNext();
		 
		 }
	 
		return true;  
		
	}				
	 
	
} //end copy_quiz


/**
* Function called by Module class to add new quiz link
*
* @param  int $module_key  key of quiz module
* @return true if successful
*/

function add_quiz_link($module_key,$existing_link_key,$new_link_key) {

	return true;

}	

/**
* Function called by deleteUser to run any functions related to deleting
* a user from the system
*
* @param int $user_key key of user being deleted
* @param int $deleted_user key of deleteduser user account
* @return true if successful
*/

function user_delete_quiz($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT attempt_key FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE user_key='$user_key'");
	
	while (!$rs->EOF) {
		
		$attempt_key = $rs->fields[0];
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE attempt_key='$attempt_key'");
		$rs->MoveNext();
		
	}
	
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE user_key='$user_key'");
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_categories SET user_key='$deleted_user' WHERE user_key='$user_key'");
  	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_item SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");			
	return true;

}

?>

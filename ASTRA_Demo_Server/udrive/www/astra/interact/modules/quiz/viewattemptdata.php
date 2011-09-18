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
* View attempt data
*
* Displays a full attempt data for a given quiz 
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: viewattemptdata.php,v 1.10 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/quiz_strings.inc.php');
require_once('lib.inc.php');


//set the required variables


$module_key	= $_GET['module_key'];
$group_key	= $_GET['group_key'];
$view_type	= $_GET['view_type'];

if(!$view_type || $view_type=='by_user') {

	$view_type = 'by_user';
	$new_view_type = 'by_item';
	$view_text = 'View by Question';

} else {

	$new_view_type = 'by_user';
	$view_text = 'View by User';

}

$userlevel_key = $_SESSION['userlevel_key'];

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
if ($is_admin==false) {

	$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	

}

$quiz = new InteractQuiz($space_key, $module_key, $group_key, $is_admin, $quiz_strings);
$quiz_data = $quiz->getQuizData($module_key);
if ($_SERVER['REQUEST_METHOD']=='POST') {
	
	$user_keys = array();
	foreach($_POST['marked_attempts'] as $value) {
		if (!empty($_POST[$value])) {
			$attempt_data=explode('_',$value);
			$attempt_key = $attempt_data[0];
			$item_key = $attempt_data[1];
			$user_key = $attempt_data[2];
			array_push($user_keys,$user_key.'_'.$attempt_key);
			$score = $_POST[$value.'_score'];
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_attempts_data SET correct=1 WHERE attempt_key='$attempt_key' AND item_key='$item_key'");
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}qt_attempts SET score=score+$score WHERE attempt_key='$attempt_key'");
					
		}
	}
	//now make any required additions to gradebook
	$time_finished = $CONN->DBDate(date('Y-m-d H:i:s'));
	foreach($user_keys as $value) {
		$attempt_data=explode('_',$value);
		$user_key = $attempt_data[0];
		$attempt_key = $attempt_data[1];
		//get total score
		$total_score = $CONN->GetOne("SELECT score FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE attempt_key='$attempt_key' AND user_key='$user_key'");
		
		$quiz->updategradebook($total_score,$time_finished,$user_key);		
	}
	header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz.php?space_key=$space_key&module_key=$module_key");
}
echo '<html><head><title>Quiz Attempt Data</title><link rel="stylesheet" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key=1" type="text/css" media="screen, projection"></head><body style="padding:10px">';
echo '<h1>'.$quiz_strings['view_attempt_data'].'</h1>';
echo '<p align="center"><a href="quiz.php?space_key='.$space_key.'&module_key='.$module_key.'">'.$general_strings['back'].'</a></p>';
echo '<p align="center"><a href="viewattemptdata.php?space_key='.$space_key.'&module_key='.$module_key.'&view_type='.$new_view_type.'">'.$view_text.'</a></p>';

if ($view_type=='by_user') {

	echo '<form action="viewattemptdata.php?space_key='.$space_key.'&module_key='.$module_key.'" method="POST"><div align="center"><table border="0" cellpadding="0" class="borderedTable"><tr><th>'.$general_strings['name'].'</th><th>'.$quiz_strings['question'].'</th><th>'.$quiz_strings['response'].'</th><th>'.$quiz_strings['correct'].'</th></tr>';
	$attempt_key='';
	$name = '';
	$rs = $CONN->Execute("SELECT DISTINCT {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name , {$CONFIG['DB_PREFIX']}qt_attempts_data.response_ident,{$CONFIG['DB_PREFIX']}qt_attempts_data.correct, {$CONFIG['DB_PREFIX']}qt_attempts_data.attempt_key, {$CONFIG['DB_PREFIX']}qt_attempts_data.item_key, {$CONFIG['DB_PREFIX']}qt_attempts_data.response_text, {$CONFIG['DB_PREFIX']}qt_attempts.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}qt_attempts, {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}qt_attempts.user_key AND {$CONFIG['DB_PREFIX']}qt_attempts.attempt_key={$CONFIG['DB_PREFIX']}qt_attempts_data.attempt_key AND  {$CONFIG['DB_PREFIX']}qt_attempts.module_key='$module_key' ORDER BY {$CONFIG['DB_PREFIX']}qt_attempts_data.attempt_key, {$CONFIG['DB_PREFIX']}qt_attempts_data.item_key");

	while (!$rs->EOF) {

	
		if ($attempt_key!=$rs->fields[4]) {
		
			$attempt_key=$rs->fields[4];
			echo '<tr><td colspan="4" height="10">&nbsp;</td></tr>';
		
		} 
	
		$full_name = $rs->fields[0].' '.$rs->fields[1];
		
		$item_data = $quiz->getItemData($rs->fields[5]);


		if ($rs->fields[2] == '' && $rs->fields[6]=='') {
	
			$responses = 'no response';
		 
		} else {
	
		
			if (!empty($rs->fields[6])) {
				$responses = $rs->fields[6];
				
			} else if ($item_data['rcardinality'] == 'Multiple'){
	
				$responses = '';
				$response_ident = explode(',',$rs->fields[2]);
			
				foreach($response_ident as $ident) {
			
					$responses .= $quiz->getresponse_text($ident).'<br />';
	
				}
					
			} else {
	
				$ident = $rs->fields[2];
				$responses = $quiz->getresponse_text($ident);
	
			}
		 
		}
		
		switch ($rs->fields[3]) {
			case 1:
				$correct = 'yes';
				break;
			case 2:
				$attempt_id = $rs->fields[4].'_'.$rs->fields[5].'_'.$rs->fields[7];
				$score = $CONN->GetOne("SELECT score FROM {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE item_key='{$rs->fields[5]}' AND module_key='$module_key'");
				$correct = '<div class="small">'.$quiz_strings['not_marked'].'<br /><input type="hidden" value="'.$attempt_id.'" name="marked_attempts[]" style="vertical-align:middle;"><input type="checkbox" value="1" name="'.$attempt_id.'" style="vertical-align:middle;">'.$quiz_strings['correct'].'<br /><input type="text" value="'.$score.'" name="'.$attempt_id.'_score" size="2">'.$quiz_strings['score'].'</div>';
				break;
			case 0:
				$correct = 'no';
				break;
		}

		echo '<tr><td valign="top" width="10%">'.$full_name.'</td><td valign="top" width="30%">'.$item_data['question'].'</td><td valign="top">'.$responses.'</td><td valign="top" width="10%">'.$correct.'</td></tr>';
	
		
		$rs->MoveNext();
	
	}
	
	echo '</table><input type="submit" name="submit" value="Submit Marked Responses"></form></div>';

} else {

	echo '<div align="center"><table border="0" cellpadding="0" class="borderedTable"><tr><th>'.$quiz_strings['question'].'</th><th>'.$quiz_strings['response'].'</th></tr>';
	$attempt_key='';
	$name = '';
	$rs = $CONN->Execute("SELECT DISTINCT {$CONFIG['DB_PREFIX']}qt_item.item_key, mattext  FROM {$CONFIG['DB_PREFIX']}qt_item,{$CONFIG['DB_PREFIX']}qt_module_item_links WHERE {$CONFIG['DB_PREFIX']}qt_item.item_key={$CONFIG['DB_PREFIX']}qt_module_item_links.item_key AND	{$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$module_key' ORDER BY {$CONFIG['DB_PREFIX']}qt_module_item_links.sort_order");
echo $CONN->ErrorMsg();
	while (!$rs->EOF) {
	
		$item_key = $rs->fields[0];
		$question = $rs->fields[1];
		$correct_count = $quiz->countResponses($item_key, $module_key, 'correct');
		$wrong_count   = $quiz->countResponses($item_key, $module_key, 'wrong');
		
		echo '<tr><td valign="top">'.$question.'</td><td valign="top">'.$quiz_strings['correct'].': '.$correct_count.'<br />'.$quiz_strings['not_correct'].': '.$wrong_count.'</td></tr>';
	
		$rs->MoveNext();
	}
	
	echo '</table></div>';

}

$CONN->Close();	   
exit;	
?>

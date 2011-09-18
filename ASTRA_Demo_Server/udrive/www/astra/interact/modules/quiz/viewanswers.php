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
* View answers
*
* Displays quiz answers once quiz is closed 
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: viewanswers.php,v 1.10 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/quiz_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
		
} 

$rs = $CONN->SelectLimit("SELECT attempt_key FROM {$CONFIG['DB_PREFIX']}qt_attempts WHERE module_key='$module_key' AND user_key='{$_SESSION['current_user_key']}' ORDER BY time_finished DESC",1);

while (!$rs->EOF) {

	$attempt_key = $rs->fields[0];
	$rs->MoveNext();

}
	
$rs = $CONN->Execute("SELECT item_key, response_ident FROM {$CONFIG['DB_PREFIX']}qt_attempts_data WHERE attempt_key='$attempt_key'");

$attempt_data = array();
$n=0;

while (!$rs->EOF) {

	
	$item_keys[$n] = $rs->fields[0];
	$attempt_data['responses_'.$rs->fields[0]][0] = $rs->fields[1];
	$n++;
	$rs->MoveNext();

}	
$attempt_data['item_keys'] = $item_keys;

$userlevel_key = $_SESSION['userlevel_key'];
$space_key	 = get_space_key();
$link_key	 = get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

$quiz = new InteractQuiz($space_key, $module_key, $group_key, $is_admin, $quiz_strings);
$quiz_data = $quiz->getQuizData($module_key);

//make sure quiz is closed

if ($quiz_data['close_date_unix']>time() && $quiz_data['close_date_unix']!=0) {

	$message = urlencode($general_strings['access_denied']);
	header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz.php?space_key=$space_key&module_key=$module_key&message=$message");
	exit;

}

foreach ($attempt_data['item_keys'] as $item_key) {
  
	$item_data = $quiz->getItemData($item_key);
	  
	if ($item_data['rcardinality']=='Single') {
	  
		$response_data = $quiz->getResponseData2($item_key, $attempt_data['responses_'.$item_key][0], 'Single');
		  
		$attempt_data[$item_key] = $attempt_data['responses_'.$item_key][0];
		$item_responses[$item_key]['response_mattext']  = $response_data['response_mattext'];
		$item_responses[$item_key]['correct']  = $response_data['correct'];
		$item_responses[$item_key]['score']	= $response_data['score'];
		$item_responses[$item_key]['feedback'] = $response_data['feedback'];
		  
		if ($item_responses[$item_key]['correct']==1 && $response_data['item_score']>0) {
		  
			$total_score = $response_data['item_score']+$total_score;
			  
		} else {
		  
			$total_score = $response_data['score']+$total_score;
		  
		}
		
		if ($response_data['correct']!=1) {
		  
			$item_responses[$item_key]['correct_answer'] = $quiz->getCorrectResponse($item_key, 'Single');
		  
		}					
		
		//echo $response_data['correct'].' '.$response_data['score'].$response_data['feedback'].'<br />';
		  
	} else if ($item_data['rcardinality']=='Multiple') {
	  
		if (!is_array($attempt_data['responses_'.$item_key])) {
		  
			$attempt_data['responses_'.$item_key]=array();
		  
		} else {
		
			$multi_attempt_data = explode(',',$attempt_data['responses_'.$item_key][0]);
			
			$n=0;
			
			foreach($multi_attempt_data as $ident) {
			
				if ($ident!='') {
				
					$attempt_data['responses_'.$item_key][$n] = $ident;
					$n++;
					
				}
	
			}
		
		}

		$response_data = $quiz->getResponseData2($item_key, $attempt_data['responses_'.$item_key], 'Multiple');
					
		foreach ($attempt_data['responses_'.$item_key] as $ident) {
		  
			$item_responses[$item_key]['response_mattext'] .= $quiz->getresponse_text($ident).'<br />';
						
		}
		
		$item_responses[$item_key]['correct']  = $response_data['correct'];
		$item_responses[$item_key]['score']	= $response_data['score'];
		$item_responses[$item_key]['feedback'] = $response_data['feedback'];
		  
		if ($item_responses[$item_key]['correct']==1 && $response_data['item_score']>0) {
		  
			$total_score = $response_data['item_score']+$total_score;
			  
		} else {
		  
			$total_score = $response_data['score']+$total_score;
		  
		}
		
		if ($response_data['correct']!=1) {
		  
			$item_responses[$item_key]['correct_answer'] = $quiz->getCorrectResponse($item_key, 'Multiple');
		  
		}		  
				
	}
	
}	
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'quiz'	   => 'quiz/runquiz.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('ATTEMPT_KEY',$attempt_key);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('QUIZ_HEADING',$quiz_strings['intro']);

$t->parse('CONTENTS', 'header', true); 
get_navigation();

//now get all the questions and display them
$t->set_block('quiz', 'ResponseBlock', 'RBlock');
$t->set_block('quiz', 'FeedbackBlock', 'FBBlock');
$t->set_block('quiz', 'ItemBlock', 'IBlock');

$sort_order = "{$CONFIG['DB_PREFIX']}qt_module_item_links.sort_order";
	

$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}qt_item.item_key, mattext, rcardinality FROM {$CONFIG['DB_PREFIX']}qt_item, {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE {$CONFIG['DB_PREFIX']}qt_item.item_key={$CONFIG['DB_PREFIX']}qt_module_item_links.item_key AND {$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$module_key' ORDER BY $sort_order");

$t->set_block('quiz', 'SubmitBlock', 'SBlock');
$t->set_var('SBlock','');
$t->set_var('FINISHED_STRING',$quiz_strings['finished']);


if ($rs->EOF) {

	$t->set_block('quiz', 'SubmitBlock', 'SBlock');
	$t->set_var('SBlock',$quiz_strings['no_questions']);
	$t->set_var('FINISHED_STRING',$quiz_strings['finished']);   

} else {
	
	$n = 1;
	
	while (!$rs->EOF) {

		$t->set_var('RBlock','');
		$t->set_var('FBBlock','');	
	
		$item_key = $rs->fields[0];
		$question_text  = $rs->fields[1];
		$rcardinality  = $rs->fields[2];
	
		$t->set_var('ITEM_MATTEXT',$question_text);
		$t->set_var('ITEM_NUMBER',$n);
		$t->set_var('ITEM_KEY',$item_key);	
		$n++;	
	
		$t->set_var('QUIZ_HEADING',$quiz_strings['completed_message']);
		$t->set_var('YOUR_ANSWER',$item_responses[$item_key]['response_mattext']);
		$t->set_var('YOUR_ANSWER_STRING',$quiz_strings['your_answer']);
		
		
		if ($item_responses[$item_key]['correct']==1) {
		
			$t->set_var('CORRECT_STRING',$quiz_strings['correct']);
			$t->set_var('CORRECT_ANSWER_STRING','');
			$t->set_var('CORRECT_ANSWER','');			
			
			
		} else {
		
			$t->set_var('CORRECT_STRING','');
		
			if ($quiz_data['show_correct']==2 ) {
			
				$t->set_var('CORRECT_ANSWER_STRING',$quiz_strings['correct_answer']);
				$t->set_var('CORRECT_ANSWER',$item_responses[$item_key]['correct_answer']);
			
			} else {

				$t->set_var('CORRECT_ANSWER_STRING','');
				$t->set_var('CORRECT_ANSWER','');
				
			}
									
		}
		
		if (isset($item_responses[$item_key]['feedback']) && $item_responses[$item_key]['feedback']!='' && $quiz_data['show_feedback']==2) {
		
			$t->set_var('FEEDBACK',$item_responses[$item_key]['feedback']);
			$t->set_var('FEEDBACK_CLASS','quizFeedback');
		
		} else {
		
			$t->set_var('FEEDBACK','');
			$t->set_var('FEEDBACK_CLASS','');
		
		}
		
		$t->parse('FBBlock', 'FeedbackBlock', true);
					
		
		$t->parse('IBlock', 'ItemBlock', true);
		$rs->MoveNext();
	   
	}
	
}
	
$t->parse('CONTENTS', 'quiz', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

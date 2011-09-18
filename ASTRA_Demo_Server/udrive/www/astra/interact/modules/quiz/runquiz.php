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
* Run quiz
*
* Displays quiz questions and processes results 
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: runquiz.php,v 1.24 2007/07/30 01:57:04 glendavies Exp $
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
	$action		= $_GET['action'];
		
} else {

	$module_key	   = $_POST['module_key'];
	$submit		   = $_POST['submit'];
	foreach ($_POST as $key => $value) {
	
		$attempt_data[$key] = $value;
		
	}

}

$userlevel_key = $_SESSION['userlevel_key'];
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. 

//if just starting the quiz check to see if logged in and set cookie to 
//prevent session timeouts affecting quiz submission

if (!isset($submit)) {
 
	$access_levels = authenticate();
	$quiz_user_hash=md5($_SESSION['current_user_key'].$CONFIG['SECRET_HASH']);
	setcookie ('quiz_user_key', $_SESSION['current_user_key'],time()+14400);
	setcookie ('quiz_user_hash', $quiz_user_hash,time()+14400);		
	
} else {

	if (!isset($_SESSION['current_user_key'])) {
	
		if (!isset($_COOKIE['quiz_user_key']) || md5($_COOKIE['quiz_user_key'].$CONFIG['SECRET_HASH'])!=$_COOKIE['quiz_user_hash']) {
		
			$access_levels = authenticate();
		
		} else {
		
			//reset the users session variables from quiz cookie
			
			if (!class_exists('InteractUser')) {
			
				require_once('../../includes/lib/user.inc.php');
			
			}
			if (!is_object($objUser)) {
			
				$objUser = new InteractUser();
			
			}	
			
			$user_data = $objUser->getUserData($_COOKIE['quiz_user_key']);
			$_SESSION['current_user_key']	   	= $_COOKIE['quiz_user_key'];
			$_SESSION['current_user_firstname'] = $user_data['first_name'];
			$_SESSION['current_user_lastname']  = $user_data['last_name'];	
			$_SESSION['current_user_email']	 	= $user_data['email'];
			$_SESSION['userlevel_key']		  	= $user_data['level_key'];
			$_SESSION['last_use']			   	= $user_data['last_use'];
			$_SESSION['use_count']			  	= $user_data['use_count'];
			$_SESSION['username']			  	= $user_data['username'];
			$_SESSION['current_username']	  	= $user_data['username'];
			$_SESSION['language']			   	= $user_data['language_key'];
			$_SESSION['auto_editor']			= $user_data['auto_editor'];
			$_SESSION['read_posts_flag']		= $user_data['flag_posts'];	 	
							
		}
		
	}
	
	$access_levels = authenticate();

} 


$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
$quiz = new InteractQuiz($space_key, $module_key, $group_key, $is_admin, $quiz_strings);
$quiz_data = $quiz->getQuizData($module_key);

//first make sure the user has not reached their maximum attempts
$attempts = $quiz->getAttemptCount($_SESSION['current_user_key'], $module_key);

if ($quiz_data['attempts']!=0 && ( ($_SERVER['REQUEST_METHOD']=='GET' && $attempts>=$quiz_data['attempts']) || ($_SERVER['REQUEST_METHOD']=='GET' && $attempts>=$quiz_data['attempts']+1)) ) {

	$message = urlencode($quiz_strings['maximum_attempts']);
	header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz.php?space_key=$space_key&module_key=$module_key&message=$message");
	exit;

}
//make sure quiz is not closed

if ($quiz_data['open_date_unix']>time() || ($quiz_data['close_date_unix']<time() && $quiz_data['close_date_unix']!=0)) {

	$message = urlencode($quiz_strings['quiz_closed']);
	header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz.php?space_key=$space_key&module_key=$module_key&message=$message");
	exit;

}

if ($submit) {

	foreach ($attempt_data['item_keys'] as $item_key) {

		$item_data = $quiz->getItemData($item_key);
	  	
		if ($item_data['response_type']=='str') {
	  		$attempt_data[$item_key] = $attempt_data['response_text_'.$item_key];
	  		$item_responses[$item_key]['response_mattext']  = $attempt_data['response_text_'.$item_key];
	  		$manual_score = 1;	  		
	  	} else {
			
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
		  //echo $response_data['correct'].' '.$response_data['score'].$response_data['feedback'].'<br>';
		  
		  
	  	} else if ($item_data['rcardinality']=='Multiple') {
	  
		  	if (!is_array($attempt_data['responses_'.$item_key])) {
		  
			  	$attempt_data['responses_'.$item_key]=array();
		  
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
			  
		  	} else if ($item_responses[$item_key]['correct']==1){
		  
			  	$total_score = $response_data['score']+$total_score;
		  
		  	}
		  	if ($response_data['correct']!=1) {
		  
			  	$item_responses[$item_key]['correct_answer'] = $quiz->getCorrectResponse($item_key, 'Multiple');
		  
		  	}		  
		}
  	}
}
  
	//save attempt
	$quiz->saveAttemptData($total_score, $attempt_data, $item_responses);

  
	//see if feedback to be displayed, if not return to quiz homepage
 
	$possible_total = $quiz->getPossibleTotal($module_key);
  
  	if (($quiz_data['show_correct']==1 || 
  		($quiz_data['show_correct']==3 && $attempts>=$quiz_data['answer_attempts']))) {
  			
  			$show_answers = true;
  			
  	} else if ($quiz_data['show_feedback']==1 || 
  		($quiz_data['show_feedback']==3 && $attempts>=$quiz_data['feedback_attempts'])) {
			
  			$show_feedback = true;  
	} else {
		$manual_score=isset($manual_score)?$manual_score:'';
		header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz.php?space_key=$space_key&module_key=$module_key&total_score=$total_score&possible_total=$possible_total&action=completed&manual_score=$manual_score");
		exit;
	}
 		
} else {

	$attempt_key = $quiz->startAttempt();

} 


//end if ($submit)


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

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');

}

$objHtml = new InteractHtml();

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('ATTEMPT_KEY',$attempt_key);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('QUIZ_HEADING',$quiz_strings['intro']);

$t->parse('CONTENTS', 'header', true); 
get_navigation();

//see if time required
if ($quiz_data['minutes_allowed']>0) {

	$t->set_var('TIME_LEFT_STRING',$quiz_strings['time_left']);
	$t->set_var('TIME_UP_STRING',$quiz_strings['time_up']);
	$t->set_var('QUIZ_NAME',$page_details['module_name']);
	$t->set_var('10_MINUTES_LEFT_STRING',$quiz_strings['10_left']);
	
	$seconds = $quiz_data['minutes_allowed']*60;
	$t->set_var('SECONDS_LEFT',$seconds);
	$t->set_var('RUN_TIMER','document.onLoad = startclock();');
				

}
//now get all the questions and display them
$t->set_block('quiz', 'ResponseBlock', 'RBlock');
$t->set_block('quiz', 'FeedbackBlock', 'FBBlock');
$t->set_block('quiz', 'ItemBlock', 'IBlock');

if ($quiz_data['shuffle_questions']==1) {

	$sort_order = 'RAND()';
	
} else {

	$sort_order = "{$CONFIG['DB_PREFIX']}qt_module_item_links.sort_order";
	
}

$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}qt_item.item_key, mattext, rcardinality, response_type FROM {$CONFIG['DB_PREFIX']}qt_item, {$CONFIG['DB_PREFIX']}qt_module_item_links WHERE {$CONFIG['DB_PREFIX']}qt_item.item_key={$CONFIG['DB_PREFIX']}qt_module_item_links.item_key AND {$CONFIG['DB_PREFIX']}qt_module_item_links.module_key='$module_key' ORDER BY $sort_order");

if (isset($submit)) {

	$t->set_block('quiz', 'SubmitBlock', 'SBlock');
	$t->set_var('SBlock','');
	$t->set_var('FINISHED_STRING',$quiz_strings['finished']);
}

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
		$question_text  = $objHtml->parseText($rs->fields[1]);
		$rcardinality  = $rs->fields[2];
		$response_type  = $rs->fields[3];
		$t->set_var('ITEM_MATTEXT',$question_text);
		$t->set_var('ITEM_NUMBER',$n);
		$t->set_var('ITEM_KEY',$item_key);	
		$n++;	
	
		if (!$submit) {
			$t->set_var('QUIZ_HEADING',$quiz_strings['intro']);
			if ($response_type=='str') {
				$t->set_var('RBlock', '<tr><td valign="top" colspan="2"><div class="quizResponse"><textarea name="response_text_'.$item_key.'" cols="60" rows="5" wrap="VIRTUAL" id="response_text_'.$item_key.'">{QUESTION}</textarea></div></td></tr>');
				
			} else {
			
				if ($quiz_data['shuffle_answers']==1) {
 
					$sort_order = 'RAND()';
	
				} else {

					$sort_order = "Ident";
	
				}
	
				$rs2 = $CONN->Execute("SELECT mattext, ident FROM {$CONFIG['DB_PREFIX']}qt_response WHERE item_key='$item_key' ORDER BY $sort_order");


				while (!$rs2->EOF) {

					$response_text = $objHtml->parseText($rs2->fields[0]);
					$response_value = $rs2->fields[1];

					if ($rcardinality=='Single') {
		
						$t->set_var('BUTTON_TYPE','radio');

					} else {
		
						$t->set_var('BUTTON_TYPE','checkbox');
		
					}

					$t->set_var('RESPONSE_MATTEXT',$response_text);
					$t->set_var('RESPONSE_IDENT',$response_value);
					$t->parse('RBlock', 'ResponseBlock', true);
					$rs2->MoveNext();
					
				}
				
	
			}

			
					
		} else {
	
			$t->set_var('SCORE',(!empty($manual_score))?$quiz_strings['manual_score']:sprintf($quiz_strings['your_score'],$total_score,$possible_total));
			$t->set_var('QUIZ_HEADING',$quiz_strings['completed_message']);
			$t->set_var('YOUR_ANSWER',$objHtml->parseText($item_responses[$item_key]['response_mattext']));
			$t->set_var('YOUR_ANSWER_STRING',$quiz_strings['your_answer']);
		
		
			if ($item_responses[$item_key]['correct']==1) {
		
				$t->set_var('CORRECT_STRING',$quiz_strings['correct']);
				$t->set_var('CORRECT_ANSWER_STRING','');
				$t->set_var('CORRECT_ANSWER','');			
			
			
			} else {
		
				$t->set_var('CORRECT_STRING','');
			
				if ($quiz_data['show_correct']==1 && $response_type!='str') {
			
					$t->set_var('CORRECT_ANSWER_STRING',$quiz_strings['correct_answer']);
					$t->set_var('CORRECT_ANSWER',$objHtml->parseText($item_responses[$item_key]['correct_answer']));
			
				} else {

					$t->set_var('CORRECT_ANSWER_STRING','');
					$t->set_var('CORRECT_ANSWER','');
				
				}
									
			}
		
			if (isset($item_responses[$item_key]['feedback']) && $item_responses[$item_key]['feedback']!='' && $quiz_data['show_feedback']==1) {
		
				$t->set_var('FEEDBACK',$objHtml->parseText($item_responses[$item_key]['feedback']));
				$t->set_var('FEEDBACK_CLASS','quizFeedback');
			
			} else {
		
				$t->set_var('FEEDBACK','');
				$t->set_var('FEEDBACK_CLASS','');
		
			}
		
			$t->parse('FBBlock', 'FeedbackBlock', true);
					
		}		
	
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

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
* gradebook homepage
*
* Displays a gradebook start page. 
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: quiz.php,v 1.16 2007/07/30 01:57:04 glendavies Exp $
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
	$group_key	= $_GET['group_key'];
	$action	 = isset($_GET['action'])? $_GET['action']:'';
	$manual_score	 = isset($_GET['manual_score'])? $_GET['manual_score']:'';
	
} else {

	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];
	
}

$userlevel_key = $_SESSION['userlevel_key'];

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
$quiz = new InteractQuiz($space_key, $module_key, $group_key, $is_admin, $quiz_strings);
$quiz_data = $quiz->getQuizData($module_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'quiz'	   => 'quiz/quiz.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

if (isset($action) && $action=='completed') {

	$total_score	= $_GET['total_score'];
	$possible_total = $_GET['possible_total'];
	$message		= $quiz_strings['completed_no_feedback'];
	$t->set_var('SCORE',(!empty($manual_score))?$quiz_strings['manual_score']:sprintf($quiz_strings['your_score'],$total_score,$possible_total));	
	

}
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//see if time limite set for this quiz
if ($quiz_data['minutes_allowed']>0) {

	$t->set_var('TIME_ALLOWED_STRING',$quiz_strings['time_allowed']);
	$t->set_var('TIME',$quiz_data['minutes_allowed'].' '.$quiz_strings['minutes']);
	$t->set_var('TIMER_STRING',$quiz_strings['timer']);

} else {

	$t->set_block('quiz', 'TimeLimitBlock', 'TLBlock');
	$t->set_var('TLBlock','');

}
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('ADD_QUESTION_STRING',$quiz_strings['add_question']);

//see if dates available set

if (!class_exists('InteractDate')) {
		
	require_once('../../includes/lib/date.inc.php');
			
}
	
$dates = new InteractDate();

if ($quiz_data['open_date_unix']>0 |$quiz_data['close_date_unix']>0) {


	if ($quiz_data['open_date_unix']>0) {
	
		$from = sprintf($quiz_strings['open_date_2'],$dates->formatDate($quiz_data['open_date_unix'],'long', true));
		
	} 
	if ($quiz_data['close_date_unix']>0) {
	
		$to = sprintf($quiz_strings['close_date_2'],$dates->formatDate($quiz_data['close_date_unix'],'long', true));
		
	} 
	$t->set_var('DATES_AVAILABLE_STRING',$quiz_strings['available_dates'].' '.$from.' '.$to);

} 

//if not available at present remove start block

if ($quiz_data['open_date_unix']>time() || ($quiz_data['close_date_unix']<time() && $quiz_data['close_date_unix']!=0)) {

	$t->set_block('quiz', 'StartBlock', 'SBlock');
	$t->set_var('SBlock','');
	
	if ($quiz_data['show_correct']==2 || $quiz_data['show_feedback']==2) {
	
		$t->set_var('VIEW_ANSWERS_STRING',$quiz_strings['view_answers']);	
	
	} else {
	
		$t->set_block('quiz', 'ViewAnswersBlock', 'VABlock');
		$t->set_var('VABlock','');
	
	}

} else {

	$t->set_block('quiz', 'ViewAnswersBlock', 'VABlock');
	$t->set_var('VABlock','');
	$t->set_var('START_QUIZ_STRING',$quiz_strings['start_quiz']);

}

//see if user has reached maximum attempts

$attempts = $quiz->getAttemptCount($_SESSION['current_user_key'], $module_key);
$t->set_var('ATTEMPTS_ALLOWED_STRING',$quiz_strings['attempts']);

if ($quiz_data['attempts']==0) {

	$quiz_data['attempts'] = $quiz_strings['unlimited_attempts'];

}

$t->set_var('ATTEMPTS_ALLOWED',$quiz_data['attempts']);

if ($quiz_data['attempts']!=0 && $attempts>=$quiz_data['attempts']) {

	$t->set_block('quiz', 'StartBlock', 'SBlock');
	$t->set_var('SBlock',$quiz_strings['maximum_attempts']);

}
$t->set_block('quiz', 'SubmitButtonBlock', 'SBBlock');
//if user is admin show results for all users
if ($is_admin) {

	$user_data = $quiz->getUserList();
	$t->set_block('quiz', 'ResultsRowBlock', 'RRBlock');
	$t->set_var('TIME_TAKEN_STRING',$quiz_strings['time_taken']);
	$t->set_var('SCORE_STRING',$quiz_strings['score']);
	$t->set_var('VIEW_ATTEMPT_DATA_STRING',$quiz_strings['view_attempt_data']);
	$t->set_var('DELETE_ATTEMPTS_STRING',$quiz_strings['delete_attempts']);
	$t->set_var('CHECK_STRING',$general_strings['check']);
		
	
	if (is_array($user_data)) {
	
		$t->set_var('RESULTS_STRING',$quiz_strings['results']);
		foreach ($user_data['by_name'] as $user_key => $username) {
	
			$user_results = $quiz->getUserResults($user_key, $module_key);
			$t->set_var('USER_NAME',$username);
			$t->set_var('USER_RESULTS',isset($user_results)&&$user_results!=''?$user_results: '&nbsp;');
			$t->parse('RRBlock', 'ResultsRowBlock', true);
		
		}
		
	} else {
	
		$t->set_var('RRBlock','');
	
	}
	$t->parse('SBBlock', 'SubmitButtonBlock', true);

} else {

	//remove the add question block
	$t->set_block('quiz', 'AddQuestionBlock', 'AQBlock');
	$t->set_var('AQBlock','');
	
	//or show results just for current user
	
	$user_results = $quiz->getUserResults($_SESSION['current_user_key'], $module_key);
	$t->set_var('SBBlock','');	
	$t->set_var('TIME_TAKEN_STRING',$quiz_strings['time_taken']);
	$t->set_var('SCORE_STRING',$quiz_strings['score']);
	if ($user_results!=false) {

		$t->set_var('USER_NAME',$_SESSION['current_user_firstname'].' '.$_SESSION['current_user_lastname']);
		$t->set_var('USER_RESULTS',$user_results);
		$t->set_var('RESULTS_STRING',$quiz_strings['results']);
	
	} else {

		$t->set_block('quiz', 'ResultsBlock', 'RBlock');
		$t->set_var('RBlock','');

	}
	
}

$t->parse('CONTENTS', 'header', true); 
get_navigation();


//display admin links if user is an admin
if ($is_admin==true) {

 
	
} else {

	$t->set_block('quiz', 'AddItemBlock', 'AIBlock');
	$t->set_var('AIBlock','');
	
}

//now get a list of an existing items for this gradebook

$t->parse('CONTENTS', 'quiz', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

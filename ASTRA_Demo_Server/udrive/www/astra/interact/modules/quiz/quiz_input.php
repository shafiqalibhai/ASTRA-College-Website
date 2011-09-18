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
* Quiz module
*
* Inputs/modifies/deletes a quiz module 
*
* @package Quiz
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: quiz_input.php,v 1.21 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/quiz_strings.inc.php');

//check we have the required variables

if ($_POST['space_key']) {
	
	$space_key		= $_POST['space_key'];
	$module_key	   = $_POST['module_key']; 
	$link_key		 = $_POST['link_key']; 

	
} else {

	$space_key  = $_GET['space_key'];
	$module_key = $_GET['module_key'];	
	$link_key   = $_GET['link_key'];
		
}

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels   = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access	= $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object

$modules = new InteractModules();
$modules->set_module_type('quiz');

//find out what action we need to take

if (isset($_POST[submit])) {


	switch($_POST[submit]) {

		//if we are adding a new quiz form input needs to be checked 

		case $general_strings['add']:
		
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$message = $modules->add_module('quiz');


			//if the add was successful return the browser to space home or parent quiz
				if ($message=='true') {
					 
					$modules->return_to_parent('quiz','added');
					exit;
				
				}  

			//if the add wasn't succesful return to form with error message

			} else {
				
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
			break;

		case $general_strings['modify']:
	  
			if ($can_edit_module==true) {
				
				$errors = check_form_input();
				
			}
	
			if(count($errors) == 0) {

				 $message = $modules->modify_module('quiz',$can_edit_module);

				//return browser to space home or parent quiz

				   if ($message=='true') {

					  $modules->return_to_parent('quiz','modified');
					exit;

				}  

			} else {
			
				$message = $message = $general_strings['problem_below'];
			 
			}
			
			break;
			
		case $general_strings['delete']:
		
			$space_key	 = $_POST['space_key'];
			$module_key	= $_POST['module_key'];
			$parent_key	= $_POST['parent_key'];
			$group_key	 = $_POST['group_key'];
			$link_key	  = $_POST['link_key'];
									
			header ("Location: {$CONFIG['FULL_URL']}/modules/general/moduledelete.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key");
			exit;			
			
		default:
			$message = $general_strings['no_action'];
			break;
			
	} //end switch($_POST[submit])			

} //end isset($_POST[submit])

if ($_GET['action']=='modify') {

	$module_data  = $modules->get_module_data('quiz');
 
} //end if ($_GET[action]=="modify")		  



//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

$name_error = sprint_error($errors['name']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'quiz/quiz_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details


if (!isset($_GET['action']) && !isset($_POST['action'])) {
	$action = 'add';
	$title = $quiz_strings['add_quiz'];
	$button = $general_strings['add'];
	$t->set_var('FEEDBACK_ATTEMPTS_DISPLAY','none');
	$t->set_var('ANSWER_ATTEMPTS_DISPLAY','none');
	
}
if ($_GET['action']=='modify' || $_POST['submit']==$general_strings['modify']) {
	$action = 'modify2';
	$button = $general_strings['modify'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';
	$t->set_var('FEEDBACK_ATTEMPTS_DISPLAY',($module_data['show_correct']==3)?'block':'none');
	$t->set_var('ANSWER_ATTEMPTS_DISPLAY',($module_data['show_feedback']==3)?'block':'none');
}
$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'quiz');
$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');

}

$html = new InteractHtml();

$suffle_questions_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'shuffle_questions',$module_data['shuffle_questions']);

$suffle_answers_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'shuffle_answers',$module_data['shuffle_answers']);

$build_menu = $html->arrayToMenu(array('1' => $general_strings['yes'], '0' => $general_strings['no']),'build',$module_data['build']);

$show_correct_menu = $html->arrayToMenu(array('0' => $general_strings['no'], '1' => $quiz_strings['on_completion'], '2' => $quiz_strings['when_closed'],'3' => $quiz_strings['after_attempts']),'show_correct',$module_data['show_correct'],false,'',false,'onChange="if(this.value==3){dojo.html.toggleShowing(\'answerAttempts\');}else{dojo.html.hide(\'answerAttempts\');}"');

$show_feedback_menu = $html->arrayToMenu(array('0' => $general_strings['no'], '1' => $quiz_strings['on_completion'], '2' => $quiz_strings['when_closed'],'3' => $quiz_strings['after_attempts']), 'show_feedback',$module_data['show_feedback'],false,'',false,'onChange="if(this.value==3){dojo.html.toggleShowing(\'feedbackAttempts\');}else{dojo.html.hide(\'feedbackAttempts\');}"');

$type_menu = $html->arrayToMenu(array('1' => $general_strings['open'], '0' => $general_strings['closed']),'type_key',$module_data['type_key']);

$grading_menu = $html->arrayToMenu(array('1' => $quiz_strings['highest'], '2' => $quiz_strings['average'], '3' => $quiz_strings['first_attempt'], '4' => $quiz_strings['last_attempt']),'grading_key',$module_data['grading_key']);

//create instance of date object for date functions
if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

$open_date_menu  = $dates->createDateSelect('open_date',$module_data['open_date_unix'], true);
$close_date_menu = $dates->createDateSelect('close_date',$module_data['close_date_unix'], true);

$t->set_var('TYPE_STRING',$general_strings['type']);
$t->set_var('TYPE_MENU',$type_menu);
$t->set_var('ATTEMPTS_STRING',$quiz_strings['attempts']);
$t->set_var('ATTEMPTS',$module_data['attempts']);
$t->set_var('UNLIMITED_STRING',$quiz_strings['unlimited']);
$t->set_var('SHUFFLE_QUESTIONS_STRING',$quiz_strings['shuffle_questions']);
$t->set_var('SHUFFLE_ANSWERS_STRING',$quiz_strings['shuffle_answers']);
$t->set_var('SHOW_FEEDBACK_STRING',$quiz_strings['show_feedback']);
$t->set_var('SHOW_CORRECT_STRING',$quiz_strings['show_correct']);
$t->set_var('OPEN_DATE_STRING',$quiz_strings['open_date']);
$t->set_var('CLOSE_DATE_STRING',$quiz_strings['close_date']);
$t->set_var('OPEN_DATE_MENU',$open_date_menu);
$t->set_var('CLOSE_DATE_MENU',$close_date_menu);
$t->set_var('CLOSE_DATE_MENU',$close_date_menu);
$t->set_var('SHUFFLE_QUESTIONS_MENU',$suffle_questions_menu);
$t->set_var('SHUFFLE_ANSWERS_MENU',$suffle_answers_menu);
$t->set_var('SHOW_CORRECT_MENU',$show_correct_menu);
$t->set_var('SHOW_FEEDBACK_MENU',$show_feedback_menu);
$t->set_var('GRADING_MENU',$grading_menu);
$t->set_var('MINUTES_ALLOWED',$module_data['minutes_allowed']);
$t->set_var('FEEDBACK_ATTEMPTS',isset($module_data['feedback_attempts'])?$module_data['feedback_attempts']:'');
$t->set_var('ANSWER_ATTEMPTS',isset($module_data['answer_attempts'])?$module_data['answer_attempts']:'');
$t->set_var('GRADING_STRING',$quiz_strings['grading']);
$t->set_var('BUILD_MENU',$build_menu);
$t->set_var('BUILD_STRING',$quiz_strings['build']);
$t->set_var('NUMBER_OF_ATTEMPTS_STRING',$quiz_strings['number_of_attempts']);
$t->set_var('NO_TIME_LIMIT_STRING',$quiz_strings['no_time_limit']);
$t->set_var('MINUTES_ALLOWED_STRING',$quiz_strings['minutes_allowed']);


  

$t->parse('GENERAL_SETTINGS', 'general', true);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;


/**
* Check form input   
* 
*  
* @return $errors
*/
function check_form_input() 
{

// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($_POST)){

		$_POST[$key] = trim($value);

	}

//check to see if we have all the information we need

	
} //end check_form_input


?>
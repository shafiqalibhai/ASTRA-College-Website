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
* @version $Id: editattempts.php,v 1.8 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/quiz_strings.inc.php');
require_once('lib.inc.php');


//set the required variables


$module_key	= $_POST['module_key'];
$space_key	= $_POST['space_key'];
$attempt_keys = $_POST['attempt_keys'];

if (!is_array($attempt_keys)) {

	$attempt_keys = array();
	
}
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

foreach ($attempt_keys as $attempt_key) {

	$quiz->deleteAttempt($attempt_key);
	
}

$message = urlencode($quiz_strings['attempts_deleted']);
header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz.php?space_key=$space_key&module_key=$module_key&message=$message");

$CONN->Close();	   
exit;	
?>

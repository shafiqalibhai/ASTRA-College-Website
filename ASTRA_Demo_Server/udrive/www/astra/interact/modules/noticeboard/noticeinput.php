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
* Add notice
*
* Displays the noticeboard notice input screen 
*
* @package Noticeboard
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: noticeinput.php,v 1.20 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/noticeboard_strings.inc.php');

//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key			= $_GET['module_key'];
	$group_key			= $_GET['group_key'];
	$action 			= $_GET['action'];
	$notice_key 		= $_GET['notice_key'];			
	
} else {

	$module_key		   = $_POST['module_key'];
	$group_key		   = $_POST['group_key'];
	$action 		   = $_POST['action'];	
	$notice_key		= $_POST['notice_key'];
	$body			  = $_POST['body'];
	$heading		   = $_POST['heading'];
	$remove_date_year  = $_POST['remove_date_year'];
	$remove_date_month = $_POST['remove_date_month'];
	$remove_date_day   = $_POST['remove_date_day'];
	$submit			= $_POST['submit'];					
		
}
$space_key 		  = get_space_key();
$link_key 		  = get_link_key($module_key,$space_key);
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables
check_variables(true,true,true);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);

//find out what action we need to take

if (isset($action)) {

	switch($action) {

		//if we are adding a new item  form input needs to be checked 
		case add:
		$errors = check_form_input();

		//if there are no errors then add the data
		if(count($errors) == 0) {

			$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));
			$remove_date = $remove_date_year.'-'.$remove_date_month.'-'.$remove_date_day;
			$remove_date = $CONN->DBDate($remove_date);
			$message = add_notice();

			//if the add was successful return the browser to space home 
			if ($message=='true') {
				
				$message = urlencode($noticeboard_strings['notice_added']);
				Header ("Location: {$CONFIG['FULL_URL']}/modules/noticeboard/noticeboard.php?space_key=$space_key&module_key=$module_key&message=$message");
				exit;
				
			//if the add wasn't succesful return to form with error message

			} else {
			
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
		
		} else {
		
			$message = $general_strings['problem_below'];
			$action = 'add';
			$input_heading = $noticeboard_strings['add_notice'];
			$button = $general_strings['add'];		
			
		}
		
		break;

		 //if we are modifying get existing data to display in form

		case modify:
		
			$sql = "SELECT heading,body,remove_date FROM {$CONFIG['DB_PREFIX']}notices WHERE notice_key='$notice_key'";
			
			$rs = $CONN->Execute($sql);
		
			while (!$rs->EOF) {

				$heading = $rs->fields[0];
				$body = $rs->fields[1];
				$unixtime = $CONN->UnixTimeStamp($rs->fields[2]);
				$rs->MoveNext();
			
			}
		
			$rs->Close();
		
		break;
			
		//if modify form has been submitted see if we are modifying or deleting
		
		case modify2:
		
			switch($submit) {

				//if deleting, then delete news
			
				case $general_strings['delete']:
			
					$message=delete_notice();

					//return browser to space home 
					if ($message=='true') {
					
						$message = urlencode($noticeboard_strings['notice_deleted']);
						header ("Location: {$CONFIG['FULL_URL']}/modules/noticeboard/noticeboard.php?space_key=$space_key&module_key=$module_key&message=$message");
						exit;
					
					
					}
					
				break;

				//if modifying then modify news

				case $general_strings['modify']:
			
					$errors = check_form_input();
			
					if(count($errors) == 0) {
				
						$remove_date = $remove_date_year.'-'.$remove_date_month.'-'.$remove_date_day;
						$remove_date = $CONN->DBDate($remove_date);
						$message = modify_notice();

						//return browser to space home or parent folder
				
						if ($message=='true') {
							
							$message = urlencode($noticeboard_strings['notice_modifed']);
							header("Location: {$CONFIG['FULL_URL']}/modules/noticeboard/noticeboard.php?space_key=$space_key&module_key=$module_key&message=$message");
							exit;
				
						} else {
					
							$message = $general_strings['problem_below'];
				
						}
			
				break;
			
			} else {
		
				$message = $general_strings['problem_below'];
				$action  = 'modify';
				$input_heading = $noticeboard_strings['edit_notice'];
				$button  = $general_strings['modify'];			
			
			} 
			
		} //end switch $submit			  

	} //end switch $action

} //end if (isset($action))

if (!isset($action)) {
   
	$action  = 'add';
	$input_heading = $noticeboard_strings['add_notice'];	
	$button  = $general_strings['add'];

}

if ($action=='modify' || $action=='modify2') {
	
	$action = 'modify2';
	$button = $general_strings['modify'];
	$warning= $general_strings['check'];
	$delete_button = "<input type=\"submit\" name=\"submit\" value=\"".$general_strings['delete']."\" onClick=\"return confirmDelete('$warning')\">";

}

//get noticeboard settings

$rs = $CONN->Execute("SELECT type_key, days_to_keep FROM {$CONFIG['DB_PREFIX']}noticeboard_settings WHERE module_key='$module_key'");

while (!$rs->EOF) {

	$type_key	 = $rs->fields[0];
	$days_to_keep = $rs->fields[1];	
	$rs->MoveNext();
	
}
//if change status has been selected we need to convert it to unix time

if ($remove_date_month!='') {

	$unixtime = mktime(0, 0,0 ,$remove_date_month,$remove_date_day,$remove_date_year );

} else {

	if ($days_to_keep>0) {
	
		$unixtime = mktime (0,0,0,date('m')  ,date('d')+$days_to_keep,date('Y'));
		
	} else {
	
		$unixtime = '';
		
	}
	
}
//generate date selection menus
if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();
$date_menu = $dates->createDateSelect('remove_date',$unixtime, false);

//format any errors from form submission

$heading_error = sprint_error($errors['heading']);
$body_error = sprint_error($errors['body']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']); 
 
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'noticeboard/noticeinput.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'body');
$t->parse('CONTENTS', 'header', true); 
get_navigation();

if ($days_to_keep>0) {

	$t->set_var('DAYS_TO_KEEP', sprintf($noticeboard_strings['days_to_keep3'],$days_to_keep)); 

}



$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('OPTIONAL_SETTINGS_STRING',$general_strings['optional_settings']);
$t->set_var('DELETE_AFTER_STRING',$noticeboard_strings['delete_after']);
$t->set_var('HEADING_STRING',$noticeboard_strings['heading']);
$t->set_var('BODY_STRING',$noticeboard_strings['body']);
$t->set_var('HEADING',$heading);

$t->set_var('HEADING_ERROR',$heading_error);
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('INPUT_HEADING',$input_heading);
$t->set_var('BODY',$body);
$t->set_var('DATE_SELECT',$date_menu);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('DELETE_BUTTON',$delete_button);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('NOTICE_KEY',$notice_key);


$t->parse('CONTENTS', 'form', true);

$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Add notice to a noticeboard 
* 
* @return true
*/
function add_notice()
{
	global $CONN,$heading,$body,$note,$date_added,$module_key, $remove_date,$current_user_key, $CONFIG;


	
	$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}notices VALUES ('','$module_key','$heading','$body',$date_added,$remove_date,'$current_user_key')";
   
	if ($CONN->Execute($sql) === false) {
	   
		$message =  'There was an error adding your notice: '.$CONN->ErrorMsg().' <br />';
		return $message;
	
	} else {
		
		return true;  
	
	}

} //end add_notice

/**
* Modify notice 
* 
*  
* @return true
*/

function modify_notice()
{
	global $CONN,$heading,$body,$note,$date_added,$space_key, $remove_date,$notice_key,$current_user_key, $CONFIG;
	

	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}notices SET heading='$heading',body='$body',remove_date=$remove_date,user_key='$current_user_key' WHERE notice_key='$notice_key'";
	
	if ($CONN->Execute($sql) === false) {
	
	   $message =  'There was an error modifying your notice: '.$CONN->ErrorMsg().' <br />';
	   return $message;

	} else {

		return true;  
	  
	}

} //end modify notice

/**
* Delete notice 
* 
*  
* @return true
*/
function delete_notice()
{
	global $CONN, $notice_key, $CONFIG;
	
	$sql="DELETE FROM {$CONFIG['DB_PREFIX']}notices WHERE notice_key='$notice_key'";
	$CONN->Execute($sql);
	$rows_affected = $CONN->Affected_Rows();
   
	if ($rows_affected < '1') {   
	
	   $message = "There was an error deleting the notice - ".$CONN->ErrorMsg();
	   email_error($message);
	   return $message;
	   
	} else {
	
		return true;
	
	}

} //end delete_notice

function check_form_input() 
{

	global $HTTP_POST_VARS, $heading, $body, $noticeboard_strings;
	// Initialize the errors array

	$errors = array();

	// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}

	//check to see if we have all the information we need
	if(!$heading) {
	
		$errors['heading'] = $noticeboard_strings['no_heading'];
	}


	if(!$body) {

		$errors['body'] = $noticeboard_strings['no_heading'];

	}

	return $errors;

} //end check_form_input

?>
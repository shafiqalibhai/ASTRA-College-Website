<?php
/**
* News input
*
* Displays the News input page to add/modify a news item 
*
* @package News
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/news_strings.inc.php');


if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$space_key  = $_GET['space_key'];
	$news_key   = $_GET['news_key'];	
	$action		= $_GET['action'];
		
} else {
	
	$space_key  = $_POST['space_key'];
	$action		= $_POST['action'];
	$heading	= $_POST['heading'];
	$body	   = $_POST['body'];			
	$news_key   = $_POST['news_key'];
	$options   = $_POST['options'];
	$submit	 = $_POST['submit'];
	$remove_date_day   = $_POST['remove_date_day'];
	$remove_date_month = $_POST['remove_date_month'];
	$remove_date_year  = $_POST['remove_date_year'];			
			
}

$current_user_key = $_SESSION['current_user_key'];
$userlevel_key	= $_SESSION['userlevel_key'];

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

if ($userlevel_key!='1' && $accesslevel_key!='1' && $accesslevel_key!='3') {

	$message = urlencode($news_strings['not_allowed']);
	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	exit;
	
}

//find out what action we need to take

if (isset($action)) {

	switch($action) {

		//if we are adding a new item  form input needs to be checked 
		case add:
			
			$errors = check_form_input();

			//if there are no errors then add the data
			if(count($errors) == 0) {

				$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));
				$remove_date = $CONN->DBDate($remove_date_year.'-'.$remove_date_month.'-'.$remove_date_day);
				$message = add_news();

			//if the add was successful return the browser to space home 
			
			if ($message=='true') {
				
				$message = urlencode($news_strings['add_success']);
				Header ("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
				exit;
				
			//if the add wasn't succesful return to form with error message

			} else {
			
				$button  = $general_strings['add'];
				$message = $general_strings['problem_below'];
			
			}
			
		} else {
		
			$message = $general_strings['problem_below'];
			$action = 'add';
			$button = $general_strings['add'];			
			
		}
		
		break;

		//if we are modifying get existing data to display in form

		case modify:
		
			$rs = $CONN->Execute("SELECT heading,body,remove_date,options FROM {$CONFIG['DB_PREFIX']}news WHERE news_key='$news_key'");
			$heading = $rs->fields[0];
			$body = $rs->fields[1];
			$unixtime = $CONN->UnixTimestamp($rs->fields[2]);
			$options = $rs->fields[3];
			$rs->Close();
		
		break;
			
		//if modify form has been submitted see if we are modifying or deleting
		case modify2:
		
			switch($submit) {

			//if deleting, then delete news
			case $general_strings['delete']:
			
				$message=delete_news();

				//return browser to space home 
			
				if ($message=='true') {
				
					$message = urlencode($news_strings['delete_success']);
					
					header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
					exit;
						 
				}
				
			break;

			//if modifying then modify news

			case $general_strings['modify']:
			
				$errors = check_form_input();
			
				if(count($errors) == 0) {
				
					$remove_date = $CONN->DBDate($remove_date_year.'-'.$remove_date_month.'-'.$remove_date_day);
					$message = modify_news();

					//return browser to space home or parent folder
				
					if ($message=='true') {
					
						$message = urlencode($news_strings['modify_success']);
						header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
						exit;
				
					} else {
					
						$message = $general_strings['problem_below'];
				
					}
				
				
				} else {
		
					$message = $general_strings['problem_below'];
					$action = 'modify';
					$button = $general_strings['modify'];			
			
			   } 
			   
		   break;
			
	   } //end switch $submit			  

   } //end switch $action

} //end if (isset($action))

if (!isset($action)) {
	
	$action = 'add';
	$button = $general_strings['add'];
	$options = 1;
	
}

if ($action=='modify' || $action=='modify2') {

	$action  = 'modify2';
	$button  = $general_strings['modify'];
	$warning = $general_strings['check'];
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$warning.'\')">';
	
}

//if change status has been selected we need to convert it to unix time

if ($remove_date_month!='') {

	$unixtime = mktime(0, 0,0 ,$remove_date_month,$remove_date_day,$remove_date_year );

}

// generate status menus

//generate date selection menus
if (!class_exists('InteractDate')) {

	require_once('../includes/lib/date.inc.php');
	
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
	'form'			=> 'news/news_input.ihtml',
	'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$module_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('HEADING_ERROR',$heading_error);
$t->set_var('BODY_ERROR',$body_error);
$t->set_var('HEADING',$heading);
$t->set_var('BODY',$body);
$t->set_var('DATE_SELECT',$date_menu);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('DELETE_BUTTON',$delete_button);
$t->set_var('NEWS_INPUT_HEADING',$news_strings['news_heading']);
$t->set_var('HEADING_STRING',$news_strings['heading']);
$t->set_var('BODY_STRING',$news_strings['body']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('REMOVE_STRING',$general_strings['remove_date']);
$t->set_var('OPTIONAL_STRING',$general_strings['optional_settings']);
$t->set_var('SHOW_PHOTO',$general_strings['show_photo']);
$t->set_var('SHOW_PHOTO_CHECKED',($options==1)?'checked':'');
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('NEWS_KEY',$news_key);


//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'body');

$t->parse('CONTENTS', 'header', true); 
//generate the navigation menu

get_navigation();
$t->parse('CONTENTS', 'form', true);

$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Add news to a space 
* 
* @return true
*/
function add_news()
{
	global $CONN,$heading,$body,$note,$date_added,$space_key, $remove_date,$current_user_key, $CONFIG, $options;

	$heading = $heading; 
	$body = $body; 
	$sql =  "INSERT into {$CONFIG['DB_PREFIX']}news VALUES ('','$space_key','$heading','$body',$date_added,$remove_date,'$current_user_key','$options')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your news: '.$CONN->ErrorMsg().' <br />';
		return $message;
   
	} else {
   
		return true;  
   
	}

} //end add_news

/**
* Modify news 
* 
*  
* @return true
*/

function modify_news()
{
	
	global $CONN,$heading,$body,$note,$date_added,$space_key, $remove_date,$news_key,$current_user_key,$options, $CONFIG;
	
	$heading = $heading; 
	$body = $body;
	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}news SET heading='$heading',body='$body',remove_date=$remove_date,user_key='$current_user_key', options='$options' WHERE news_key='$news_key'";
	if ($CONN->Execute($sql) === false) {
	
	   $message =  'There was an error modifying your news: '.$CONN->ErrorMsg().' <br />';
	   return $message;

	} else {

		return true;  
	  
	}

} //end modify news

/**
* Delete news 
* 
*  
* @return true
*/
function delete_news()
{
	
	global $CONN,$news_key, $CONFIG;
	
	$sql="delete from {$CONFIG['DB_PREFIX']}news where news_key='$news_key'";
	$CONN->Execute($sql);
	
	$rows_affected = $CONN->Affected_Rows();
	if ($rows_affected <1) {   
	   $message = "There was an error deleting the news item - ".$CONN->ErrorMsg();
	   email_error($message);
	   return $message;
	   
	} else {
	
	
		return true;
	}
	
} //end delete_news

function check_form_input() 
{

	global  $heading, $body, $news_strings;
   // Initialize the errors array

	$errors = array();

	
	//check to see if we have all the information we need
	if(!$heading) {
		
		$errors['heading'] = $news_strings['no_heading'];

	}


	if(!$body) {

		$errors['body'] = $news_strings['no_body'];

	}

	return $errors;
	
} //end check_form_input

?>
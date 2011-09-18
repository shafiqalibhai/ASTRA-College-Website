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
* Event input
*
* Input or modify a  calendar event
*
* @package Calendar
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: eventinput.php,v 1.41 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/calendar_strings.inc.php');

//set variables
if ($_SERVER['REQUEST_METHOD']=='GET') {
	$module_key	= $_GET['module_key'];
	$group_key	= isset($_GET['group_key'])?$_GET['group_key']:'';
	$event_key	= isset($_GET['event_key'])?$_GET['event_key']:'';
	$action		= isset($_GET['action'])?$_GET['action']:'';
	if (isset($_GET['date'])) {
		$unixtime1=$_GET['date'];
		if (date('H',$unixtime1)==0) {$unixtime1+=((min(date('H')+1,23))*60*60);}
	}
} else {

	$module_key	= $_POST['module_key'];
	$group_key	= isset($_POST['group_key'])?$_POST['group_key']:'';
	$event_key	= isset($_POST['event_key'])?$_POST['event_key']:'';
	$action		= isset($_POST['action'])?$_POST['action']:'';
	$submit		= isset($_POST['submitbut'])?$_POST['submitbut']:'';		
	
}

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);
$event_type=0;
check_variables(true,false,true);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$userlevel_key = isset($_SESSION['userlevel_key'])?$_SESSION['userlevel_key']:'';
$rs = $CONN->Execute("SELECT type,parent_calendar_key FROM {$CONFIG['DB_PREFIX']}calendars WHERE module_key=$module_key");
$type = ($rs->fields['Type']);
$inheritfrom=$rs->fields['parent_calendar_key'];
$rs->Close();
$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

//autenticate user
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

if ($accesslevel_key=='') {$type='';}

$group_accesslevel = isset($access_levels['group_accesslevel'][$group_key])?$access_levels['group_accesslevel'][$group_key]:'';

$is_admin= check_module_edit_rights($module_key);

if (!($is_admin || ($type=="open"))) {
	header("Location: {$CONFIG['FULL_URL']}/modules/calendar/calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&message=".urlencode($general_strings['not_allowed']));
	exit;
}


//create instance of date object for date functions
if (!is_object($objDates)) {
	if (!class_exists('InteractDate')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	}
	$objDates = new InteractDate();
}

$date_flags=0;
$finDisplay='';
$description='';
$newnameC='';

//find out what action we need to take
if (isset($action)) {
	if ($action=='add' || $action=='modify2') {
	
		$event_date_year   = $_POST['event_date_year'];
		$event_date_month  = $_POST['event_date_month'];
		$event_date_day	= $_POST['event_date_day'];
		$remove_date_year  = $_POST['remove_date_year'];
		$remove_date_month = $_POST['remove_date_month'];
		$remove_date_day   = $_POST['remove_date_day'];
		$name			  = $_POST['name'];
		$description	   = $_POST['description'];
		$current_user_key  = $_SESSION['current_user_key'];					
		$event_type=$_POST['event_type'];
		if ($event_type=='new'&&$_POST['newnameC']) {$newnameC=$_POST['newnameC'];}
		if ($_POST['all_day']) {
			$date_flags|=1;
			$event_date_hour=$event_date_minute=0;
		} else {
			$event_date_hour   = $_POST['event_date_hour'];
			$event_date_minute = $_POST['event_date_minute'];
		}

		$event_date = $event_date_year.'-'.$event_date_month.'-'.$event_date_day.' '.$event_date_hour.':'.$event_date_minute;
		$event_date=$objDates->checkDateValid($event_date);
		if (!isset($_POST['finish_unspec'])) {
			$event_date_finish_year   = $_POST['event_date_finish_year'];
			$event_date_finish_month  = $_POST['event_date_finish_month'];
			$event_date_finish_day	= $_POST['event_date_finish_day'];
			if ($_POST['all_day']) {
				$event_date_finish_hour=$event_date_finish_minute=0;
			} else {
				$event_date_finish_hour   = $_POST['event_date_finish_hour'];
				$event_date_finish_minute = $_POST['event_date_finish_minute'];
			}
	
			$event_date_finish = $event_date_finish_year.'-'.$event_date_finish_month.'-'.$event_date_finish_day.' '.$event_date_finish_hour.':'.$event_date_finish_minute;
			$event_date_finish=$objDates->checkDateValid($event_date_finish);

		} else {
			$finDisplay='none';
			$event_date_finish_month  = '';
			$event_date_finish = '';
		}
		
		if (isset($remove_date_year)) {			 
			$remove_date = $remove_date_year.'-'.$remove_date_month.'-'.$remove_date_day;
		}

	}
	
	switch($_POST['type_command']) {
		case 'recolour':
			if(can_edit_type($event_type) && (!$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}event_types SET colour='{$_POST['newnameC']}' WHERE event_type_key = $event_type"))) {
				$message=$general_strings['error'].': '.$CONN->ErrorMsg();
			}
			break;
		case 'delete':
			if(can_edit_type($event_type) && (!$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}event_types WHERE event_type_key = $event_type"))) {
				$message=$general_strings['error'].': '.$CONN->ErrorMsg();
			} else {$event_type=0;}
			break;
		case 'rename':
			if(can_edit_type($event_type) && (!$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}event_types SET name='".$_POST['newname']."' WHERE event_type_key = $event_type"))) {
				$message=$general_strings['error'].': '.$CONN->ErrorMsg();
			}
			break;

		default:
		switch($action) {
			//if we are adding a new event form input needs to be checked 
			case add:
			$errors = check_form_input();
			//if there are no errors then add the data
			if(count($errors) == 0) {
				$date_added=date('Y-m-d H:i:s');
				if (!$status_key) {
					$status_key='1';
				}
				$message = add_event();
				//if the add was successful return the browser to space home or parent folder
				if ($message=='true') {
					$message = urlencode($calendar_strings['add_success']);
					Header ("Location: {$CONFIG['FULL_URL']}/modules/calendar/calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&message=$message&show_month=$event_date_month&show_year=$event_date_year");
						exit;
				} 
	
			//if the add wasn't succesful return to form with error message
			} else {
				$button = $general_strings['add'];
				$message = $general_strings['problem_below'];
			}
			break;
	
			//if we are modifying get existing data to display in form
			case modify:
				$sql = "SELECT name,description, event_date, remove_date, event_date_finish, date_flags, link, event_type FROM {$CONFIG['DB_PREFIX']}calendar_events WHERE event_key='$event_key'";
				$rs = $CONN->Execute($sql);
			
				while (!$rs->EOF) {
					$name		= $rs->fields[0];
					$description = $rs->fields[1];
					$unixtime1   = $CONN->UnixTimestamp($rs->fields[2]);
					$unixtime2   = $CONN->UnixTimestamp($rs->fields[3]);
					$unixtime3   = $CONN->UnixTimestamp($rs->fields[4]);
					$date_flags  = $rs->fields[5];
					$event_type  = $rs->fields[7];
					$rs->MoveNext();
				}
				$rs->Close();
			break;
				
			//if modify form has been submitted see if we are modifying or deleting
			case modify2:
				switch($submit) {
					//if deleting, then delete event
					case $general_strings['delete']:
						$message = delete_event($module_key);
						//return browser to calendar
						header("Location: {$CONFIG['FULL_URL']}/modules/calendar/calendar.php?space_key=$space_key&module_key=$module_key&message=$message&show_month=$event_date_month&show_year=$event_date_year");
						exit;
						break;
	
						//if modifying then modify event
						case $general_strings['modify']:
						$errors = check_form_input();
						if(count($errors) == 0) {
							$message = modify_event();
							//return browser to space home or parent folder
							if ($message=='true') {
								$message = urlencode($calendar_strings['modify_success']);
								header("Location:  {$CONFIG['FULL_URL']}/modules/calendar/calendar.php?space_key=$space_key&module_key=$module_key&message=$message&show_month=$event_date_month&show_year=$event_date_year");
								exit;
					
							} 
				
						} else {
							$message = $general_strings['problem_below'];
						}
					break;
				} //end switch $submit			  
			} //end switch $action
		}//end switch type_command
} //end if (isset($action))



// generate status menus
$status_sql = "select name, Modulestatus_key from {$CONFIG['DB_PREFIX']}modulestatus order by name";

//format any errors from form submission
$event_date_error = sprint_error($errors['event_date']);
$name_error = sprint_error($errors['name']);

//get the required template files
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'form'	   => 'calendars/eventinput.ihtml',
	'footer'	 => 'footer.ihtml'
));

if (!class_exists('InteractHtml')) {
	require_once('../../includes/lib/html.inc.php');
}
$html = new InteractHtml();
$html->setTextEditor($t, 0, 'description');

$t->set_var('SCRIPT_INCLUDES','<script type="text/javascript" language="javascript">'.file_get_contents('eventinput.js').'</script>',true);

$t->set_var('SCRIPT_BODY','onload="cinit();"',true);


$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$rs = $CONN->Execute("SELECT event_type_key,parent,name,colour FROM {$CONFIG['DB_PREFIX']}event_types WHERE parent IN ($inheritfrom,$module_key) ORDER BY (parent=$module_key), name");

	$t->set_var('GENERAL_TYPE','<input type="radio" value="0" name="event_type" ');
	if ($event_type==0) {
		$t->set_var('GENERAL_TYPE','checked ',true);
	}
	$t->set_var('GENERAL_TYPE','onclick="highlightPick();"><span style="color:#000">'.$calendar_strings['general'].'</span>',true);

// Create list of Event types to choose from, plus edit/add/colour controls
// This is a messy - I apologise!  -=Bruce.

$rowcount=0;$showstar=false;
while (!$rs->EOF) {
//echo $rs->fields['name'].'-'.$rs->fields['parent'].'=='.$module_key.'...'.$showstar;
	if($rs->fields['parent']==$module_key) {
		if($showstar==true) {
			starrow();$showstar=false;
		}
	} else {$showstar=true;}
	$rowcount++;
	$t->set_var('EVENT_TYPES','<tr><td id="PICK_TYPE_'.$rowcount.'"><span style="white-space:nowrap;color:#'.$rs->fields['colour'].'"><input type="radio" value="'.$rs->fields['event_type_key'].'" name="event_type" ',true);
	if ($event_type==($rs->fields['event_type_key'])) {
		$t->set_var('EVENT_TYPES','checked ',true);
	}
	$t->set_var('EVENT_TYPES','onclick="highlightPick();">'.$rs->fields['name'].($showstar?'<span style="font-size:x-small">*</span>':'').'</span></td><td style="white-space:nowrap" align="right">',true);

	if (!$showstar && $is_admin) {
		$t->set_var('EVENT_TYPES','<span id="PICK_TYPE_EDIT_'.$rowcount.'" style="display:none">&nbsp;<span style="cursor:pointer" onclick="if(confirm(\'Warning!  All events of this type will revert to \\\''.$calendar_strings['general'].'\\\'.\')){document.getElementById(\'type_command\').value=\'delete\';document.getElementById(\'event_input_form\').submit();}"><img title="Delete" align="bottom" border="0" width=13 height=13 src="'.$CONFIG['PATH'].'/includes/editor/images/Trash.gif"/></span> <input type="button" style="padding:0;cursor:pointer;margin:0;background-color:#A5C3F8;font-size:xx-small;vertical-align:top" value="Rename"/  onclick="if(nnnn=prompt(\'Warning!  Changing this Event Type will affect all events of this type.\',\''.str_replace('"','&quot;',str_replace("'","\'",$rs->fields['name'])).'\')){document.getElementById(\'newname\').style.color=\'#FFF\';document.getElementById(\'newname\').value=nnnn;				document.getElementById(\'type_command\').value=\'rename\';document.getElementById(\'event_input_form\').submit();}"> <img style="cursor:pointer" onclick="if(confirm(\'Warning!  Changing this Event Type will affect all events of this type.\')){this.nextSibling.style.visibility=\'visible\';showColourPick();}" align="absbottom" border="0" width=18 height=18 src="'.$CONFIG['PATH'].'/includes/editor/images/ed_color_bg.png"/><img id="PICK_TYPE_SUB_'.$rowcount.'" border="0" width=10 height=13 src="'.$CONFIG['PATH'].'/images/menu_sub.gif" style="visibility:hidden"/></span>',true);
	}

	$t->set_var('EVENT_TYPES','</td></tr>',true);
	$rs->MoveNext();
}
$rs->Close();
if($showstar) {starrow();}

$rowcount++;
if ($is_admin) {
$t->set_var('EVENT_TYPE_NEW','<input type="radio" value="new" name="event_type" ');
	if ($event_type==='new') {
		$t->set_var('EVENT_TYPE_NEW','checked ',true);
	}
	$t->set_var('EVENT_TYPE_NEW','id="new_event_type" onclick="highlightPick();document.getElementById(\'newname\').focus(); document.getElementById(\'newname\').select()"><input type="text" name="newname" id="newname" maxlength="28" style="color:#{NEWNAMEC}" value="'.($event_type=='new' && $_POST['newname']? $_POST['newname']:$general_strings['new']).'" size="20" onfocus=""  onclick="document.getElementById(\'new_event_type\').checked=true; this.select();highlightPick();" /> ',true);
	$t->set_var('EVENT_TYPE_NEW','<img id="PICK_TYPE_SUB_'.$rowcount.'" border="0" width=10 height=13 src="'.$CONFIG['PATH'].'/images/menu_sub.gif" style="visibility:hidden"/>',true);
} else {
	$t->set_var('EVENT_TYPE_NEW','');
}

$t->set_var('PICK_TYPE_COUNT',$rowcount);


$rs = $CONN->Execute("SELECT colour FROM {$CONFIG['DB_PREFIX']}colours");
$t->set_var('COLOUR_PICK','<tr>');
$nn=0;

$t->set_var('NEWNAMEC',$newnameC);
while (!$rs->EOF) {
	if (!($nn++%11)) {$t->set_var('COLOUR_PICK','</tr><tr>',true);}
	$t->set_var('COLOUR_PICK','<td height="20px" width="16px" style="background-color:#'.$rs->fields['colour'].($rs->fields['colour']===$newnameC?';border-color:#000000;':'').'" id="cp'.$rs->fields['colour'].'" onMouseOver="cView(this)" onMouseOut="cView(this,1)" onClick="cSet(\''.$rs->fields['colour'].'\')">&nbsp;&nbsp;</td>',true);
	$rs->MoveNext();
}
$rs->Close();

$t->set_var('COLOUR_PICK','</tr>',true);
$t->set_var('PICK_A_COLOUR',$calendar_strings['pick_a_colour']);

$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
//generate the header,title, breadcrumb details
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//if change status has been selected we need to convert it to unix time
if ($event_date_month!='') {
	$unixtime1 = mktime($event_date_hour, $event_date_minute,0 ,$event_date_month,$event_date_day,$event_date_year );
}
if ((!$finDisplay) && ($event_date_finish_month!='')) {
	$unixtime3 = mktime($event_date_finish_hour, $event_date_finish_minute,0 ,$event_date_finish_month,$event_date_finish_day,$event_date_finish_year );
}
if ($remove_date_month!='') {
	$unixtime2 = mktime(0, 0, 0,$remove_date_month,$remove_date_day,$remove_date_year );
}

if ($action=='modify' || $action=='modify2') {
	$action = 'modify2';
	$button = $general_strings['modify'];
	$warning=$general_strings['delete_warning'];
	$delete_button = '<input type="submit" name="submitbut" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$warning.'\')">';

	if (!$unixtime3) {
		$finDisplay='none';
	}
} else {
	$action = 'add';
	$title =  $calendar_strings['add_event'];
	$button = $general_strings['add'];
}


if (!$unixtime1) {
	$date=getdate(time());
	$unixtime1=mktime(min($date['hours']+1,23),0,0,$date['mon'],$date['mday'],$date['year']);
}

if (!$unixtime3) {
	$date=getdate($unixtime1);
	$unixtime3=mktime(min($date['hours']+1,23),0,0,$date['mon'],$date['mday'],$date['year']);
}

$t->set_var('FIN_CHECK',$finDisplay? 'checked':'');

$dateFinishSpan='<span id="event_date_finish_span" style="display:'.$finDisplay.'">'.$objDates->createDateSelect('event_date_finish',$unixtime3, true).'<br /></span>';

if ($date_flags&1) {
	$t->set_var('ALL_CHECK','checked');
	$dateFinishSpan.='<script type="text/javascript">document.getElementById("event_date_finish_time").style.display="none";document.getElementById("event_date_time").style.display="none";</script>';
}

$t->set_var('MESSAGE',$message);
$t->set_var('EVENT_DATE_ERROR',$event_date_error);
$t->set_var('TYPE_ERROR',sprint_error($errors['type']));
$t->set_var('NAME_ERROR',$name_error);
$t->set_var('STATUS_ERROR',$status_error);
$t->set_var('NAME',$name);
$t->set_var('DESCRIPTION',$description);
$t->set_var('REMOVE_DATE',$objDates->createDateSelect('remove_date',$unixtime2, false));
$t->set_var('EVENT_DATE',$objDates->createDateSelect('event_date',$unixtime1, true));
$t->set_var('EVENT_DATE_FINISH',$dateFinishSpan);
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->set_var('DELETE_BUTTON',$delete_button);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('EVENT_KEY',$event_key);
$t->set_var('EVENT_STRING',$calendar_strings['event']);
$t->set_var('TIME_OPTIONAL_STRING',$calendar_strings['time_optional']);
$t->set_var('EVENT_TYPE_STRING',$calendar_strings['event_type']);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('OPTIONAL_STRING',$general_strings['optional']);
$t->set_var('REMOVE_STRING',$general_strings['remove_date']);
$t->set_var('INSERT_LINK',$general_strings['insert_link']);
$t->set_var('PREVIEW',$general_strings['preview']);
$t->set_var('DATE_STRING',$general_strings['date']);
$t->set_var('EMAIL_LINK',$general_strings['email_link']);
$t->set_var('PARENT_CALENDAR_STRING',$calendar_strings['parent_calendar']);
$t->set_var('ALLDAY_STRING',$calendar_strings['allday']);
$t->set_var('FINISH_TIME_STRING',$calendar_strings['finish_time']);
$t->set_var('UNSPECIFIED_STRING',$calendar_strings['unspecified']);
$t->set_var('BODY',$description);
$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);

//output no-cache headers
print_headers();

//output page
$t->p('CONTENTS');

//close database connection
$CONN->Close();
exit;

/**
* Function to add an event to a calendar
*
* @package Calendar
* @return true if successful
*/

function add_event(){
	global $CONN,$name,$description,$event_date,$event_date_finish,$date_flags,$remove_date,$date_added,$space_key,$module_key,$current_user_key, $CONFIG,$event_type ;

	
	$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}calendar_events(module_key,user_key,name,description,event_date,remove_date,date_added,event_date_finish,date_flags,event_type) VALUES ('$module_key','$current_user_key','$name','$description','$event_date','$remove_date','$date_added','$event_date_finish','$date_flags','$event_type')";

	if ($CONN->Execute($sql) === false) {
		$message =  'There was an error adding your event: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {
		return true;  
	}

} //end add_event

/**
* Function to modify an event in a calendar
*
* @package Calendar
* @return true if successful
*/

function modify_event(){

	global $CONN,$name,$description,$event_date,$event_date_finish,$date_flags,$remove_date,$event_key, $CONFIG,$event_type;
	
	$sql = "UPDATE {$CONFIG['DB_PREFIX']}calendar_events SET name='$name',description='$description',event_date='$event_date',remove_date='$remove_date',event_date_finish='$event_date_finish',date_flags='$date_flags',event_type='$event_type' where event_key='$event_key'";

	if ($CONN->Execute($sql) === false) {
		$message =  'There was an error modifying your event: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {
		return true;  
	}

} //end modify event

/**
* Function to delete an event from a calendar
*
* @package Calendar
* @return true if successful
*/
function delete_event(){

	global $CONN,$event_key, $CONFIG;
	
	$sql="DELETE FROM {$CONFIG['DB_PREFIX']}calendar_events where event_key='$event_key'";
	
	if ($CONN->Execute($sql) === false) { 
		$message = 'There was an error deleting that event - '.$CONN->ErrorMsg();
		urlencode($message);
		email_error($message);
		return $message;
	} else {
		$message = 'Your+event+has+been+deleted';
		return $message;
	}
} // end delete_event


/**
* Function to check event form input
*
* @package Calendar
* @return array $errors any array of any errors found
*/

function check_form_input() {
	global $HTTP_POST_VARS, $name, $event_date_month, $calendar_strings, $event_date, $newnameC, $event_date_finish,$event_type,$general_strings, $CONN, $module_key, $CONFIG, $userlevel_key,$is_admin;
	
	// Initialize the errors array
	$errors = array();
	// Trim all submitted data
	while(list($key, $value) = each($HTTP_POST_VARS)){
		$HTTP_POST_VARS[$key] = trim($value);
	}
	//check to see if we have all the information we need
	if(!$event_date_month) {
		$errors['event_date'] = $calendar_strings['no_date'];
	}

	if(($event_date_finish)&&($event_date_finish<$event_date)) {
		$errors['event_date'] = $calendar_strings['no_date'];
	}

	if(!$name) {
		$errors['name'] = $calendar_strings['no_event_name'];
	}
	
	if ($event_type=='new') {
		if ($is_admin) {
			if (!$_POST['newname']||$_POST['newname']==$general_strings['new']) {
				$errors['type'] = $calendar_strings['no_new_type_name'];
			} else {
				if ($_POST['newnameC']=='') {
					$errors['type'] = 'You must choose a colour for your new Event Type';
				} else {
					$newtname_esc=$CONN->qstr($_POST['newname']);
					if ($CONN->Execute('INSERT INTO `'.$CONFIG['DB_PREFIX'].'event_types` (`parent`,`name`,`colour`) VALUES ("'.$module_key.'",'.$newtname_esc.',"'.$_POST['newnameC'].'")') === false) {
						$errors['type'] =  $general_strings['error'].': '.$CONN->ErrorMsg();
					} else {
						$rs = $CONN->Execute("SELECT event_type_key FROM {$CONFIG['DB_PREFIX']}event_types WHERE name=".$newtname_esc." && colour=\"".$_POST['newnameC']."\"");
						if (!$rs->EOF) {
							$event_type=$rs->fields['event_type_key'];$newnameC='';
						} else {$event_type=0;}
					}
				}
			}
		} else {
			$errors['type'] = urlencode($general_strings['not_allowed']);	
		}
	}
	return $errors;
} //end check_form_input

function can_edit_type($typekey) {
	global $userlevel_key,$is_admin,$module_key, $CONN, $CONFIG;
	if ($typekey!=0) {
		if ($userlevel_key==1) {
			return true;
		} else {
			$rs = $CONN->Execute("SELECT parent FROM {$CONFIG['DB_PREFIX']}event_types WHERE event_type_key=$typekey");
			if (($rs->fields[0]==$module_key) && $is_admin) {
				return true;
			} else {
			echo 'false! p:'.$rs->fields[0].' isadmin:'.$is_admin. 'typekey:'.$typekey;
				return false;
			}
			$rs->Close();
		}
	} else {
		return false;
	}
}

function starrow() {
	global $rowcount,$t,$CONN,$CONFIG,$inheritfrom,$calendar_strings;
	$rowcount++;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs2 = $CONN->Execute("SELECT name from {$CONFIG['DB_PREFIX']}modules where module_key='$inheritfrom'");
	$t->set_var('EVENT_TYPES','<tr><td colspan="2" align="center" style="padding-bottom:6px"><span style="font-size:x-small;">*'.sprintf($calendar_strings['inhertitedfrom'],$rs2->fields['name']).'</span></td></tr>',true);
	$rs2->Close();
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
}
?>
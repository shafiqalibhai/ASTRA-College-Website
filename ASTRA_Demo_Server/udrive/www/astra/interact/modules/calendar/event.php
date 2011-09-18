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
* Event display
*
* Displays a calendar event
*
* @package Calendar
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: event.php,v 1.24 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');




//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/calendar_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= isset($_GET['group_key'])?$_GET['group_key']:'';
$event_key	= isset($_GET['event_key'])?$_GET['event_key']:'';

check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);


$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
//get event details
$sql="SELECT user_key,module_key,event_date,name,description,date_flags,event_type,event_date_finish FROM {$CONFIG['DB_PREFIX']}calendar_events WHERE event_key='$event_key'";
$rs = $CONN->Execute($sql);
if (!$rs->EOF) {
	
	//get the required template files
	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array(
		'header'	 => 'header.ihtml',
		'navigation' => 'navigation.ihtml',
		'event'	  => 'calendars/event.ihtml',
		'footer'	 => 'footer.ihtml'
	));
	
	//create instance of date object for date functions
	if (!is_object($objDates)) {
		if (!class_exists('InteractDate')) {
			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
		}
		$objDates = new InteractDate();
	}
	
	
	//get page details, space and module name, etc.
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	$page_details = get_page_details($space_key,$link_key);
	set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
	
	$t->parse('CONTENTS', 'header', true); 
	$t->set_var('RETURN',sprintf($general_strings['return'],$page_details['module_name']));
	
	//create left hand navigation
	get_navigation();
	
	if (!is_object($objDates)) {
		if (!class_exists('InteractDate')) {
			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
		}
		$objDates = new InteractDate();
	}
	
	
	$name = $rs->fields['name'];
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	if ($rs->fields['module_key']!=$module_key) {
		$name.="*";
		
		$rs2 = $CONN->Execute("SELECT name from {$CONFIG['DB_PREFIX']}modules where module_key='{$rs->fields['module_key']}'");
		$t->set_var('STARNOTE','<span style="font-size:x-small">*'.sprintf($calendar_strings['inhertitedfrom'],$rs2->fields['name']).'</span>',true);
		$rs2->Close();
	}
	
	
	$description=ereg_replace( 10, '<br />', $rs->fields['description']);
	$t->set_var('SPACE_KEY',$space_key);
	$t->set_var('MODULE_KEY',$module_key);
	$t->set_var('NAME',$name);
	$t->set_var('DESCRIPTION',$description);
	
	if ($rs->fields['event_type']) {
		$rs2=$CONN->Execute("SELECT name,colour FROM {$CONFIG['DB_PREFIX']}event_types WHERE event_type_key={$rs->fields['event_type']}");
		$colour=$rs2->fields['colour'];
		$t->set_var('TYPE_NAME',$rs2->fields['name']);
		$rs2->Close();
	} else {
		$colour="000";$name='';
	}

	$unixtime1=$CONN->UnixTimeStamp($rs->fields['event_date']);
	$unixtime3=$CONN->UnixTimeStamp($rs->fields['event_date_finish']);
	$event_date = $objDates->formatDate($unixtime1,'long');
	
	if ($rs->fields['date_flags']&1) {
		if ($unixtime3) {
			if (date('Ymd',$unixtime3)>date('Ymd',$unixtime1)) {
				$event_date .= 	' &rarr; '.$objDates->formatDate($unixtime3,'long');
			}
		}	
		$t->set_var('ALL_DAY','AllDay');
		$t->set_var('ESTYLE',"color:#FFF;background-color:#$colour;");
	} else {
		if (date('Hi', $unixtime1)>0) {
			$event_date .= ', '.date((date('i', $unixtime1)>0? 'g:ia':'ga'), $unixtime1);
			if ($unixtime3 && $unixtime3>$unixtime1) {
				$event_date .= ' &rarr; ';
				if (date('Ymd',$unixtime3)>date('Ymd',$unixtime1)) {
					$event_date .= $objDates->formatDate($unixtime3,'long').', ';
				}
				$event_date .= date((date('i', $unixtime3)>0? 'g:ia':'ga'), $unixtime3);
			}	
		}
		$t->set_var('ALL_DAY','');
		$t->set_var('ESTYLE',"color:#$colour;border-bottom:1px solid;");
	}
	$t->set_var('EVENT_DATE',$event_date);

	//if user is an admin or lecturer for the space 
	//to which this event belongs (or author of event) - add edit pencil
	if ($module_key==$rs->fields['module_key'] && ($is_admin==true || $rs->fields['user_key']==$_SESSION['current_user_key'])) {

		$t->set_var('EVENT_EDIT',get_admin_tool("{$CONFIG['PATH']}/modules/calendar/eventinput.php?space_key=$space_key&module_key=$module_key&event_key=$event_key&action=modify"));
	} else {
		$t->set_var('EVENT_EDIT','');
	}
	
	$t->parse('CONTENTS', 'event', true);
}
$rs->Close();

$t->parse('CONTENTS', 'footer', true);
//out put no-cache headers
print_headers();

//output page
$t->p('CONTENTS');

//close database connection	   
$CONN->Close();
exit;

function is_parent($testkey) {
	global $module_key,$inherited;
	if ($testkey==$module_key) {
		return '';
	} else {
		$inherited=true;
		return '*';
	}
}

?>
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
* Calendar module
*
* Displays the calendar module start page 
*
* @package Calendar
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: calendar.php,v 1.46 2007/05/14 00:23:51 websterb4 Exp $
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
$show_year 	= isset($_GET['show_year'])?$_GET['show_year']:'';
$show_month	= isset($_GET['show_month'])?$_GET['show_month']:'';
$message	 = isset($_GET['message'])?$_GET['message']:'';
$userlevel_key = isset($_SESSION['userlevel_key'])?$_SESSION['userlevel_key']:'';

//check we have the variables we need
check_variables(true,true,true);

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];
$group_accesslevel = isset($access_levels['group_accesslevel'][$group_key])?$access_levels['group_accesslevel'][$group_key]:'';

$is_admin=(check_module_edit_rights($module_key));

//update statistics 
statistics('read');
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
// get parent calendar keys
$sql = "SELECT type,parent_calendar_key from {$CONFIG['DB_PREFIX']}calendars where module_key='$module_key'";
$rs = $CONN->Execute($sql);
	if (!$rs->EOF) {
		$type = $rs->fields['Type'];
		$inheritfrom=$rs->fields['parent_calendar_key'];
	} else {
		$message="no Cal";
	}
$rs->Close();

if ($accesslevel_key=='') {$type='';}


$n=0;
$parent_array=array();$parents='';
get_cal_parents($module_key);
while (list ($key, $val) = each ($parent_array)) {
	$parents .= " OR module_key='$val'"; 
}


//get the required templates for this page
require_once($CONFIG['BASE_PATH'].'/includes/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);
  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'calendar'		 => 'calendars/calendar.ihtml',
	'footer'		  => 'footer.ihtml'
));

$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
// get details of this page, space name, module name, etc.
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
//set page variables
$t->parse('CONTENTS', 'header', true); 

// if user is admin or lecturer provide Add option
if ($is_admin || $type=='open') {
	$t->set_var('CURRENT_MODULE_ADMIN_LINKS',get_admin_tool("eventinput.php?space_key=$space_key&module_key=$module_key",false,$calendar_strings['add_event'],'plus'));
}

//create the left hand navigation 
get_navigation();
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;


// create array so we can name months 
$month_name = array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
$type_array = array();


$current_user_key=$_SESSION['current_user_key'];

// build calendar
$startDayOfWeek=1;
$showNDays=7;

if (isset($_GET['view'])) {
	if ($_GET['view']=='5') {
		$showNDays=5;$startDayOfWeek=1;
	}
}


$date=getdate($currentTime=time());

if(!$show_year) {$show_year=$date['year'];}
if(!$show_month) {$show_month=$date['mon'];}

$next_year = $show_year+1;
$previous_year = $show_year-1;

// create links to months of year
if($show_month==1){$t->set_var('MONTHS1',"<a href=\"calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&show_month=12&show_year=".($show_year-1)."\">".$calendar_strings[$month_name[12].'_abb']."&nbsp;".substr($previous_year,2)."</a>&nbsp;",true);}

for($current_month = 1; $current_month <= 12; $current_month++){
	$name=$calendar_strings[$month_name[$current_month].'_abb'];
	if($show_month!=$current_month){
		$t->set_var('MONTHS'.($show_month>$current_month?'1':'2'),"&nbsp;<a href=\"calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&show_month=$current_month&show_year=$show_year\">$name</a>&nbsp;",true);
	}
}

if($show_month==12){$t->set_var('MONTHS2',"&nbsp;<a href=\"calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&show_month=1&show_year=".($show_year+1)."\">".$calendar_strings[$month_name[1].'_abb']."&nbsp;".substr($next_year,2)."</a>",true);}


$day1 = mktime(0,0,0,$show_month,1,$show_year); 

$wStartDay=(date('w',$day1)+7-$startDayOfWeek)%7;
$startDate=strtotime('-'.$wStartDay.' days',$day1);
$startDayOfYear=date('z',$startDate);
$startYear=date('Y',$startDate);

$maxDays=date('t',$day1)+$wStartDay+6;$maxDays-=($maxDays % 7 +1);
$endDate=strtotime('+'.$maxDays.' days',$startDate);

for ($dCount=0;$dCount<=$maxDays;$dCount+=7) {
	$maxrow[$dCount]=0;
	$maxEventEst[$dCount]=3.3;
}

$inherited=false;
$hiddenEvents=false;
$calendar='<tr>';
for ($dinc=0;$dinc<$showNDays;$dinc++) {
	$calendar.='<th width="14%" height=8 align="center">{DAY'.(($startDayOfWeek+$dinc)%7).'_ABB}</th>';
}
$calendar.='</tr>
';

$Ymd_start=date('Y-m-d',$startDate);
$Ymd_end=date('Y-m-d',$endDate);
$sql = "SELECT event_key,name,event_date,description,event_date_finish,date_flags,user_key,event_type,module_key FROM {$CONFIG['DB_PREFIX']}calendar_events WHERE ((event_date BETWEEN '$Ymd_start.' AND '$Ymd_end') OR ((date_flags&1) AND ((event_date_finish BETWEEN '$Ymd_start.' AND '$Ymd_end') OR (event_date_finish>'$Ymd_end' AND event_date<'$Ymd_start.') ))) AND (module_key='$module_key' $parents) ORDER BY date_flags&1 DESC,event_date,event_date_finish DESC,date_added";
$rs = $CONN->Execute($sql);

$lastWeek=0;
//all-day events...
while ((!$rs->EOF)&&($rs->fields['date_flags']&1)) {

	$eventstart_date=$CONN->UnixTimeStamp($rs->fields['event_date']);
	$eventStartYear=date('Y',$eventstart_date);
	$eventStart=date('z',$eventstart_date)-$startDayOfYear;
	for ($i=$startYear;$i<$eventStartYear;$i++) {$eventStart+=(365+date('L',mktime(0,0,0,1,1,$i)));}
	
	$eventEndDate=$CONN->UnixTimeStamp($rs->fields['event_date_finish']);
	$eventEndYear=date('Y',$eventEndDate);
	$eventEnd=date('z',$eventEndDate)-$startDayOfYear;
	for ($i=$startYear;$i<$eventEndYear;$i++) {$eventEnd+=(365+date('L',mktime(0,0,0,1,1,$i)));}

	$hideit=false;

	if ($eventStart<0) {
		$eventStart=0;$cuspDay=0;
	}
	$dCount=$eventStart-$eventStart%7;

	if ($eventStart%7>=$showNDays) {
		if($eventEnd-$eventEnd%7==$dCount) {
			$hideit=true;$hiddenEvents=true;
		} else {
			$eventStart=$eventStart+(7-($eventStart%7));$cuspDay=0;
		}
	} else {$eventStart=$eventStart;$cuspDay=1;}

	if (!$hideit && $eventEnd>=$eventStart) {
		if ($dCount!=$lastWeek) {  //try to shuffle up events to save space
			shuffleWeek($lastWeek+7);
			$lastWeek=$dCount;
		}
		
		$endCusp=2;
	
		$daysToEndOfWeek=((7-1)-($eventStart%7));
		$viewDaysToEndOfWeek=($showNDays-1)-($eventStart%7);
		$nDays=$eventEnd-$eventStart;
		
		$row=1;
		do {
			$OK=true;$i=0;
			while ($i<=$vdays) {
				if (isset($all_day_cal[$eventStart+$i][$row])){$OK=false;$row++;$i=$vdays;}
				$i++;
			}
		} while (!$OK);

		do {
			$maxrow[$dCount]=max($row,$maxrow[$dCount]);
			$days=min($nDays,$daysToEndOfWeek);$vdays=min($nDays,$viewDaysToEndOfWeek);
			$nDays-=($vdays+1);
			if($nDays<0) $cuspDay|=$endCusp;

			$all_day_cal[$eventStart][$row]=array($vdays,$cuspDay,$rs->fields);
			for ($i=1;$i<=$vdays;$i++) {
				$all_day_cal[$eventStart+$i][$row]=0;
			}
			$nDays-=(7-$showNDays);
			$eventStart+=($days+1);
			$cuspDay=0;
			$daysToEndOfWeek=(7-1);$viewDaysToEndOfWeek=($showNDays-1);
			$dCount+=7;
		} while ($nDays>=0); 
	}
	$rs->MoveNext();
}
if ($lastWeek<($maxDays-$maxDays%7)) {shuffleWeek($lastWeek+7);}

//timed events
for ($eventStart=0;$eventStart<=$maxDays;$eventStart++) {
	$time_events[$eventStart]=null;
	$time_events[$eventStart]='';
	$dayEventEst[$eventStart]=0;
	$calDate=strtotime('+'.($eventStart).' days',$startDate);
	while ((!$rs->EOF)&&(substr($rs->fields['event_date'],5,5)==date('m-d',$calDate))) {

		if($eventStart%7>=$showNDays) {
			$hiddenEvents=true;
		} else {
			$event_key = $rs->fields['event_key'];
			$name = $rs->fields['name'];
			
			$description = $rs->fields['description'];
			$tstamp=$CONN->UnixTimeStamp($rs->fields['event_date']);
			if (date('Hi', $tstamp)>0) {
				$name = date((date('i', $tstamp)>0? 'g:ia':'ga'), $tstamp).' '.$name;
			}
			if(strlen($name)>42) {$name=substr($name,0,42).'&hellip;';}
			$dayEventEst[$eventStart]+=round(strlen($name)/(90/$showNDays))+.5;
			$bgcol=get_type_colour($rs->fields['event_type']);
			$time_events[$eventStart] .= "<a class=\"calendarEvent\" style=\"color:#$bgcol\" href=\"event.php?space_key=$space_key&module_key=$module_key&event_key=$event_key&group_key=$group_key\" title=\"".truncDes($rs->fields['description'])."\" >&bull;$name".is_parent($rs->fields['module_key'])."</a>";
		}
		$rs->MoveNext();
	}
}


//render!
for ($dCount=0;$dCount<=$maxDays;$dCount+=7) {
//	$maxEventEst[$dCount]=0;
	for ($row=0;$row<=$maxrow[$dCount]+1;$row++) {
		if ($row==$maxrow[$dCount]+1) {
			$calendar.= '<tr>';
		} else {
			$calendar.= '<tr style="height:1em">';
		}
		for ($dinc=0;$dinc<$showNDays;$dinc++) {

			$calDate=strtotime('+'.($dCount+$dinc).' days',$startDate);
			$calDateNumber=date('j',$calDate);
			if (!isset($all_day_cal[$dCount+$dinc][$row])) {

				$try_row=$row+1;
				while (($try_row<=$maxrow[$dCount]+1)&&(!isset($all_day_cal[$dCount+$dinc][$try_row]))) {
					$all_day_cal[$dCount+$dinc][$try_row]=0;
					$try_row++;
				}
				$rowsN=$try_row-$row-1;


				$more=(($row+$rowsN)<$maxrow[$dCount]);
				$style='';
				$calendar.= "\n<td valign=\"top\" class=\"calendar".dateClass($calDate).'"';
				
				if ($rowsN) {
					$calendar.= ' rowspan="'.($rowsN+1).'"';
				}

				if (!$row) {
					if ($more) {
						$style.= 'border-bottom:none;';
					}
					$calendar.= ' style="'.$style.'"><span class="calendarDate'.dateClass($calDate).'">'.$calDateNumber.'</span>';
					if ($is_admin || $type=='open') {$calendar.= get_admin_tool('eventinput.php?space_key='."$space_key&module_key=$module_key&date=$calDate",false,null,'smallplus',null,'style="margin:.3em;"');}
					$calendar.= '<br />';
				} else {
					if ($more) {
						$style.= 'border-top:none;border-bottom:none;';
					} else {
						$style.= 'border-top:none;';					
					}
					$calendar.= ' style="'.$style.'">';
				}
				
				
				if (!$more) {
					if ($time_events[$dCount+$dinc]) {
						$calendar.= $time_events[$dCount+$dinc];
					} else {
						$calendar.= '<span style="font-size:x-small">';
						if ($row==0) {
							$calendar.= '<br/><br/><br/>';
						} else {
							if ($maxEventEst[$dCount]>=$row+1) {
								for ($i=$row;$i<$maxEventEst[$dCount];$i++) {
									$calendar.= '<br/>';
								}
							} else {
								$calendar.='&nbsp;';
							}
						}
						$calendar.='</span>';
					}
				
					
					$maxEventEst[$dCount]=max($maxEventEst[$dCount],$dayEventEst[$dCount+$dinc]+(max($row-1,0)));
				} else {if ($row) $calendar.= '<span style="font-size:x-small">&nbsp;</span>';}
				$calendar.= "</td>\n";
			} else {
				$all_day_array=$all_day_cal[$dCount+$dinc][$row];
				if ($all_day_array) {
					$calendar.='<td class="calendar'.dateClass($calDate).'" ';
 					if ($all_day_array[0]) {$calendar.='colspan="'.($all_day_array[0]+1).'" ';}
$name=$all_day_array[2]['name'];
if(strlen($name)>3+15*($all_day_array[0]+1)) {$name=substr($name,0,3+15*($all_day_array[0]+1)).'&hellip;';}

 					$calendar.= 'style="border:none">';

 					$calendar.= '<table width="100%" height="100%" cellspacing=0 cellpadding=0 class="calendar'.dateClass($calDate).'" style="border:none"><tr>';
					
					if ($all_day_array[1]&1) {
						$calendar.='<td class="calendar'.dateClass($calDate).'" style="border-top:none;border-right:none;border-bottom:none;width:6px;font-size:xx-small">&nbsp;</td>';
					}
					$description=$all_day_array[2]['description'];
					$calendar.= '<td>'.('<a href="event.php?space_key='.$space_key.'&module_key='.$module_key.'&event_key='.$all_day_array[2]['event_key'].'&group_key='.$group_key.'" title="'.truncDes($description).'" ');
					$bgcol=get_type_colour($all_day_array[2]['event_type']);
					$calendar.= 'class="calendarAllDayEvent" style="background-color:#'.$bgcol.'; ';

					switch ($all_day_array[1]) {
						case 0:	$calendar.='-moz-border-radius: 0;';break;
						case 1: $calendar.='-moz-border-radius-topright: 0em;-moz-border-radius-bottomright: 0;';break;
						case 2: $calendar.='-moz-border-radius-topleft: 0em;-moz-border-radius-bottomleft: 0;';break;
					}
					$calendar.='">';
					$calendar.=((!($all_day_array[1]&1))?'&hellip; ':'').$name.(is_parent($all_day_array[2]['module_key'])).((!($all_day_array[1]&2))?' &hellip;':'').'</a>'.'</td>';

					if ($all_day_array[1]&2) {
						$calendar.='<td class="calendar'.dateClass(strtotime('+'.$all_day_array[0].' days',$calDate)).'" style="border-top:none;border-bottom:none;border-left:none;width:6px;font-size:xx-small">&nbsp;</td>';
					}
					$calendar.='</tr></table></td>';
				}
			}
		}
		$calendar.= "</tr>\n";
	}
}

if ($inherited) {
	$rs = $CONN->Execute("SELECT name from {$CONFIG['DB_PREFIX']}modules where module_key='$inherited'");
	$t->set_var('CAL_KEY','<span style="font-size:x-small">*'.$calendar_strings['inhertitedfrom_a_parent'].'</span><br/>');
	$rs->Close();
}
if ($hiddenEvents) {
	$t->set_var('CAL_KEY','<span style="font-size:x-small">'.$calendar_strings['hidden_events'].'</span>',true);
}

$n=0;
if ($ctt=count($type_array)) {
	$t->set_var('CAL_KEY','<table cellspacing=1 cellpadding=0 align="center" style="margin-top:5px;"><tr><th rowspan="'.$ctt.'" style="padding-right:6px;color:black;text-align:right;">'.$calendar_strings['key'].':</th>',true);
	foreach ($type_array as $tt => $value) {
		$t->set_var('CAL_KEY','<td width="90"><span class="calendarAllDayEvent" style="background-color:#'.$type_array[$tt]['colour'].'">'.$type_array[$tt]['name'].'</span></td></tr>'.(++$n<$ctt?'<tr>':'</table>'),true);
	}
}

$rs->Close();
$CONN->Close(); 

//set remaining page variables
$next_year_link = "<a href=\"{$CONFIG['PATH']}/modules/calendar/calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&show_year=$next_year&show_month=$show_month\" class=\"small\">".$calendar_strings['next_year']."</a>";
$previous_year_link = "<a href=\"{$CONFIG['PATH']}/modules/calendar/calendar.php?space_key=$space_key&module_key=$module_key&group_key=$group_key&show_year=$previous_year&show_month=$show_month\" class=\"small\">".$calendar_strings['previous_year']."</a>";


$t->set_var('CALENDAR',$calendar);
$t->set_var('DAY0_ABB',$calendar_strings['sunday_abb']);
$t->set_var('DAY1_ABB',$calendar_strings['monday_abb']);
$t->set_var('DAY2_ABB',$calendar_strings['tuesday_abb']);
$t->set_var('DAY3_ABB',$calendar_strings['wednesday_abb']);
$t->set_var('DAY4_ABB',$calendar_strings['thursday_abb']);
$t->set_var('DAY5_ABB',$calendar_strings['friday_abb']);
$t->set_var('DAY6_ABB',$calendar_strings['saturday_abb']);
$t->set_var('MONTH_NAME',$calendar_strings[$month_name[$show_month]]);
$t->set_var('MONTH_NUM',$month_num);
$t->set_var('NEXT_YEAR',$next_year_link);
$t->set_var('PREVIOUS_YEAR',$previous_year_link);
$t->set_var('YEAR',$show_year);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_KEY',$module_key);
$t->parse('CONTENTS', 'calendar', true);
$t->parse('CONTENTS', 'footer', true);

//print no-cache headers
print_headers();

//output page
$t->p('CONTENTS');
exit;
/**
* Function to get parent calendars
*
* @package Calendar
* @param  int $parent_cal_key key of parent calendar
*/

function get_cal_parents($cal_key) 
{
	global $CONN,$n,$parent_array, $CONFIG;

	$sql = "SELECT parent_calendar_key from {$CONFIG['DB_PREFIX']}calendars,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links where {$CONFIG['DB_PREFIX']}calendars.module_key='$cal_key' AND ({$CONFIG['DB_PREFIX']}modules.module_key=parent_calendar_key AND {$CONFIG['DB_PREFIX']}modules.status_key!='4') AND ({$CONFIG['DB_PREFIX']}module_space_links.module_key=parent_calendar_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1')";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {
		$parent_cal_key = $rs->fields['parent_calendar_key'];
		$rs->Close();
		if ($parent_cal_key==0 || in_array($parent_cal_key,$parent_array)) {
			return;
		} else {
			$parent_array[$n++]= $parent_cal_key;
			get_cal_parents($parent_cal_key);
		}
		$rs->MoveNext();
	}
} //end get_cal_parents

function get_type_colour($event_type_key) {
	global $CONN, $CONFIG, $type_array;
	$retcol='000';
	if ($event_type_key) {
		if (isset($type_array[$event_type_key])) {
			$retcol=$type_array[$event_type_key]['colour'];
		} else {
			$rs=$CONN->Execute("SELECT name,colour,parent FROM {$CONFIG['DB_PREFIX']}event_types WHERE event_type_key=$event_type_key");
			if (!$rs->EOF) {
				$type_array[$event_type_key]=$rs->fields; 
				$retcol=$rs->fields['colour'];
			}
			$rs->Close();
		}
	}
	return $retcol;
}

function is_parent($testkey) {
	global $module_key,$inherited;
	if ($testkey==$module_key) {
		return '';
	} else {
		$inherited=true;
		return '*';
	}
}
function dateClass($calDate) {
global $show_month,$currentTime;
	if (date('n',$calDate)!=$show_month) {
		return 'OutsideDay"';
	} else {
		if (date("j:n:Y",$calDate)==date("j:n:Y",$currentTime)) {
			return 'Today';
		} else {
			return 'Day';
		}
	}
}

function truncDes($des) {
	if ($des) {
		if (strlen($des)>100) {$des= substr($des,0,100).'&hellip;';}
		return str_replace("\n"," ",htmlspecialchars(strip_tags($des), ENT_QUOTES));
	}
	return '';
}

function shuffleWeek($shuffleStart) {
global $maxDays,$maxrow,$all_day_cal,$showNDays;
	for ($week=$shuffleStart;$week<=$maxDays;$week+=7) {
		for ($row=$maxrow[$week]-1;$row>0;$row--) {
			if (!isset($all_day_cal[$week][$row])) {
				for ($rown=$row;$rown<=$maxrow[$week];$rown++) {
					for ($i=0;$i<$showNDays;$i++) {
						if (isset($all_day_cal[$week+$i][$rown+1])) {
							$all_day_cal[$week+$i][$rown]=$all_day_cal[$week+$i][$rown+1];
						} else {
							unset($all_day_cal[$week+$i][$rown]);
						}
					}
				}
				$maxrow[$week]--;
			}
		}
	}
}

?>
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
* Statistics
*
* Displays access statistics for a space as html tables. Will display 
* by module and by user 
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: statistics.php,v 1.19 2007/07/18 00:40:41 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');


if ($_SERVER['REQUEST_METHOD']=='GET') {

	$space_key = $_GET['space_key'];
	
} else {

	if (!empty($_POST['submit']) && $_POST['submit']==$group_strings['spreadsheet_link']) {
		
	}
	$space_key = $_POST['space_key'];
	$group_key = $_POST['group_key'];
	$user_key  = $_POST['user_key'];
	$start_date = !empty($_POST['start_date_month'])?$_POST['start_date_year'].'-'.$_POST['start_date_month'].'-'.$_POST['start_date_day']:'';
	$end_date = !empty($_POST['end_date_month'])?$_POST['end_date_year'].'-'.$_POST['end_date_month'].'-'.$_POST['end_date_day']:''; 					
	if (!empty($_POST['submit']) && $_POST['submit']==$group_strings['spreadsheet_link']) {
		header("Location: {$CONFIG['FULL_URL']}/spaceadmin/excelstats.php?space_key=$space_key&group_key=$group_key&start_date=$start_date&end_date=$end_date");
		exit;
	}
	$action	= $_POST['action'];
	$module_scope	 = $_POST['module_scope'];		
	$user_scope	 = $_POST['user_scope'];
}
//check we have the required variables
check_variables(true,false);




//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 
$page_details=get_page_details($space_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'page'			=> 'spaceadmin/statistics.ihtml',
	'stats'		   => 'spaceadmin/statstable.ihtml',
	'footer'		  => 'footer.ihtml'
));
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractDate')) {

	require_once('../includes/lib/date.inc.php');
	
}
echo $scope;
$dates = new InteractDate();

$start_date_limit = !empty($start_date)?'AND '.$CONFIG['DB_PREFIX'].'statistics.date_accessed>'.$CONN->DBDate($start_date):'';
$end_date_limit = !empty($end_date)?'AND '.$CONFIG['DB_PREFIX'].'statistics.date_accessed<'.$CONN->DBDate($end_date):'';

if (!empty($_POST['by_module'])) {
	
	if ($module_scope=='summary') {
		
		$sql = "SELECT COUNT({$CONFIG['DB_PREFIX']}modules.module_key),{$CONFIG['DB_PREFIX']}statistics.module_key,{$CONFIG['DB_PREFIX']}modules.name,use_type,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}statistics.module_key  and {$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' $start_date_limit $end_date_limit GROUP BY {$CONFIG['DB_PREFIX']}statistics.module_key,{$CONFIG['DB_PREFIX']}statistics.use_type ORDER BY {$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}statistics.module_key,use_type";
		$rs=$CONN->Execute($sql);
		echo $CONN->ErrorMsg();
		while (!$rs->EOF) {
		
			$count = $rs->fields[0];
			$module_key = $rs->fields[1];
			$module_name = $rs->fields[2];
			$use_type = $rs->fields[3];
			$module_type = $rs->fields[4];			

			$sql_last="SELECT date_accessed FROM {$CONFIG['DB_PREFIX']}statistics WHERE module_key='$module_key' AND use_type='$use_type' $start_date_limit $end_date_limit ORDER BY date_accessed DESC LIMIT 1";
			$rs2=$CONN->Execute($sql_last);
			
			while (!$rs2->EOF) {
				
				 $date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs2->fields[0]),'short', true);
				 $rs2->MoveNext();
				 
			}
			
			$t->set_var('COLUMN_ONE',$module_name.' ('.$module_type.')');
			$t->set_var('COLUMN_TWO',$use_type);
			$t->set_var('COLUMN_THREE',$count);
			$t->set_var('COLUMN_FOUR',$date_accessed);
			$t->set_var('HEADING_ONE','name');
			$t->set_var('HEADING_TWO','Type of Access');
			$t->set_var('HEADING_THREE','Use Count');
			$t->set_var('HEADING_FOUR','Last Access');
			$t->parse('STATS', 'stats', true);
			$rs->MoveNext();

		}
		
		$message = $group_strings['summary_stats'];
		
	} else {
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}statistics.date_accessed,use_type, {$CONFIG['DB_PREFIX']}modules.type_code  FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}modules where {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key  AND {$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' $start_date_limit $end_date_limit ORDER BY date_accessed DESC";
		$rs=$CONN->Execute($sql);
		
		if ($rs->EOF) {
		
			$message = $group_strings['no_stats'];
		
		} else {
		
			while (!$rs->EOF) {
			
				$module_name = $rs->fields[0];
				$date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[1]),'short', true);
				$use_type =  $rs->fields[2];
				$module_type = $rs->fields[3];
				if ($use_type=='post') {
			 
					$use_type = "<strong>$use_type</strong>";
				 
				}				
				$t->set_var('COLUMN_ONE',$module_name.' ('.$module_type.')');
				$t->set_var('COLUMN_TWO',$date_accessed);
				$t->set_var('COLUMN_THREE',$use_type);
				$t->set_var('COLUMN_FOUR','&nbsp;');
				$t->set_var('HEADING_ONE',$group_strings['name']);
				$t->set_var('HEADING_TWO',$group_strings['date_accessed']);
				$t->set_var('HEADING_THREE',$group_strings['type']);
				$t->set_var('HEADING_FOUR','&nbsp;');
				$t->parse('STATS', 'stats', true);
				$rs->MoveNext();
				
			 }
			 
			 $message = $group_strings['full_stats'];
			 
		}
		
	}

} else {

	if ($user_scope=='full') {
	
		$concat = $CONN->Concat('last_name','\', \'', 'first_name');
		$sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}statistics.date_accessed,use_type, {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}statistics.user_key={$CONFIG['DB_PREFIX']}users.user_key  AND ({$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' and {$CONFIG['DB_PREFIX']}statistics.user_key='$user_key') $start_date_limit $end_date_limit ORDER BY date_accessed DESC";
		
		$rs=$CONN->Execute($sql);
		
		if ($rs->EOF) {
		
 			$message = $group_strings['no_stats'];
			
		} else {
		
			while (!$rs->EOF) {
			
				$username = $rs->fields[0];
				$module_name = $rs->fields[1];
				$date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'short', true);
				$use_type =  $rs->fields[3];
				$module_type = $rs->fields[4];
				
				if ($use_type=='post') {
			 
					 $use_type = "<strong>$use_type</strong>";
				 
				}				
				$t->set_var('COLUMN_ONE',$module_name.' ('.$module_type.')');
				$t->set_var('COLUMN_TWO',$date_accessed);
				$t->set_var('COLUMN_THREE',$use_type);
				$t->set_var('COLUMN_FOUR','&nbsp;');
				$t->set_var('HEADING_ONE',$group_strings['name']);
				$t->set_var('HEADING_TWO',$group_strings['date_accessed']);
				$t->set_var('HEADING_THREE',$group_strings['type']);
				$t->set_var('HEADING_FOUR','&nbsp;');
				$t->parse('STATS', 'stats', true);
				$rs->MoveNext();
			 
			}
			
			$message = sprintf($group_strings['user_stats'],$username);
			
		}
		
		
	} else {

		$concat = $CONN->Concat('last_name','\', \'', 'first_name');
		$sql = "SELECT COUNT(*),$concat, {$CONFIG['DB_PREFIX']}modules.name,use_type,{$CONFIG['DB_PREFIX']}modules.type_code  FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}statistics.user_key={$CONFIG['DB_PREFIX']}users.user_key  AND ({$CONFIG['DB_PREFIX']}statistics.space_key='$space_key') AND {$CONFIG['DB_PREFIX']}statistics.user_key='$user_key' $start_date_limit $end_date_limit GROUP BY {$CONFIG['DB_PREFIX']}statistics.module_key,{$CONFIG['DB_PREFIX']}statistics.use_type ORDER BY {$CONFIG['DB_PREFIX']}statistics.module_key,date_accessed DESC";
			
		$rs=$CONN->Execute($sql);
		if ($rs->EOF) {
		
			$message = $group_strings['no_stats'] ;
			
		} else {
		
			while (!$rs->EOF) {
				
				$count = $rs->fields[0];
				$username = $rs->fields[1];
				$module_name = $rs->fields[2];
				$use_type =  $rs->fields[3];
				$module_type = $rs->fields[4];
				if ($use_type=='post') {
			 
					 $use_type = "<strong>$use_type</strong>";
				 
				}
								
				$t->set_var('COLUMN_ONE',$module_name.' ('.$module_type.')');
				$t->set_var('COLUMN_TWO',$use_type);
				$t->set_var('COLUMN_THREE',$count);
				$t->set_var('COLUMN_FOUR','&nbsp;');
				$t->set_var('HEADING_ONE',$group_strings['name']);
				$t->set_var('HEADING_TWO',$group_strings['type']);
				$t->set_var('HEADING_THREE',$group_strings['use_count']);
				$t->set_var('HEADING_FOUR','&nbsp;');
				$t->parse('STATS', 'stats', true);
				$rs->MoveNext();
				
			}
			
			$message = $message = sprintf($group_strings['user_summary_stats'],$username);
		
		}
	
	}
		
}


$concat = $CONN->Concat('last_name','\', \'', 'first_name');
$user_sql = "SELECT DISTINCT $concat, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND (space_key='$space_key') ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
$user_menu = make_menu($user_sql,'user_key',$user_key,'1',false);

$t->set_var('USER_MENU',$user_menu);

$t->parse('CONTENTS', 'header', true);
get_navigation();

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('START_DATE_MENU',$dates->createDateSelect('start_date',$CONN->UnixTimestamp($start_date),false,date('Y',time())-5));
$t->set_var('END_DATE_MENU',$dates->createDateSelect('end_date', $CONN->UnixTimestamp($end_date),false,date('Y',time())-5));
$t->set_var('STATISTICS_HEADING',$group_strings['statistics_heading']);
$t->set_var('SPREADSHEET_LINK',$group_strings['spreadsheet_link']);
$t->set_var('BY_COMPONENT_STRING',$group_strings['by_component']);
$t->set_var('BY_USER_STRING',$group_strings['by_user']);
$t->set_var('VIEW_STRING',$general_strings['view']);
$t->set_var('SUMMARY_STRING',$general_strings['summary']);
$t->set_var('FULL_DETAILS_STRING',$general_strings['full_details']);
$t->set_strings('page',  $group_strings, '', '');
$t->parse('CONTENTS', 'page', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
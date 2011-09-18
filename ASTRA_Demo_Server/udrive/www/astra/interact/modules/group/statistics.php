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
* View statistics
*
* View statistics information for a group
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: statistics.php,v 1.18 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

//set variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];

} else {

	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];
	$user_key	= $_POST['user_key'];	
	$scope	  = $_POST['scope'];
	$action	 = $_POST['action'];	

}
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables

check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

$can_edit_module = check_module_edit_rights($module_key);

if ($can_edit_module!=true) {

	header("Location: {$CONFIG['FULL_URL']}/index.php?message=Sorry+you+can+not+access+that+page");
	exit;
	
}

$page_details=get_page_details($space_key,$link_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'page'			=> 'spaceadmin/statistics.ihtml',
	'stats'		   => 'spaceadmin/statstable.ihtml',
	'footer'		  => 'footer.ihtml'
));

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');

}

$dates = new InteractDate();

if ($_POST['action']=='by_module') {
	
	if ($_POST['scope']=='summary') {
		
		$sql = "SELECT COUNT({$CONFIG['DB_PREFIX']}modules.module_key),{$CONFIG['DB_PREFIX']}statistics.module_key,{$CONFIG['DB_PREFIX']}modules.name,use_type FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}statistics.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.group_key='$group_key' AND {$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' GROUP BY {$CONFIG['DB_PREFIX']}statistics.module_key,{$CONFIG['DB_PREFIX']}statistics.use_type ORDER BY {$CONFIG['DB_PREFIX']}statistics.module_key,use_type";
		
		$rs=$CONN->Execute($sql);
		
		while (!$rs->EOF) {
		
			$count = $rs->fields[0];
			$module_key = $rs->fields[1];
			$module_name = $rs->fields[2];
			$use_type = $rs->fields[3];
			
			if ($use_type=='post') {
			 
				 $use_type = "<strong>$use_type</strong>";
				 
			}			

			$sql_last="SELECT date_accessed FROM {$CONFIG['DB_PREFIX']}statistics WHERE module_key='$module_key' AND use_type='$use_type' ORDER BY date_accessed DESC LIMIT 1";
			$rs2=$CONN->Execute($sql_last);
			
			while (!$rs2->EOF) {
			
				 $date_accessed = date('d M Y H:i',$CONN->UnixTimestamp($rs2->fields[0]));
				 $rs2->MoveNext();
			
			}
			
			$t->set_var('COLUMN_ONE',$module_name);
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
	
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}statistics.date_accessed,use_type FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.group_key='$group_key' AND {$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' ORDER BY date_accessed DESC";
		
		$rs=$CONN->Execute($sql);
		
		if ($rs->EOF) {
				
				$message = $group_strings['no_stats'];
		
		} else {
		
			while (!$rs->EOF) {
			
				$module_name = $rs->fields[0];
				$date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[1]),'short', true);
				$use_type =  $rs->fields[2];
				
				if ($use_type=='post') {
			 
					 $use_type = "<strong>$use_type</strong>";
				 
				}
								
				$t->set_var('COLUMN_ONE',$module_name);
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

} 

if ($_POST['action']=='by_user') {

	if ($_POST['scope']=='full') {
	
		$concat = $CONN->Concat('last_name','\', \'', 'first_name');
		$sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}statistics.date_accessed,use_type FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}statistics.user_key={$CONFIG['DB_PREFIX']}users.user_key AND ({$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}statistics.user_key='$user_key') ORDER BY date_accessed DESC";
		
		$rs=$CONN->Execute($sql);
		
		if ($rs->EOF) {
			
			$message = $group_strings['no_stats'];
		
		} else {
			
			while (!$rs->EOF) {
			
				$username = $rs->fields[0];
				$module_name = $rs->fields[1];
				$date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'short', true);
				$use_type =  $rs->fields[3];
				
				if ($use_type=='post') {
			 
					$use_type = "<strong>$use_type</strong>";
				 
				}
								
				$t->set_var('COLUMN_ONE',$module_name);
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
		$sql = "SELECT COUNT(*),$concat, {$CONFIG['DB_PREFIX']}modules.name,use_type FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}statistics.user_key={$CONFIG['DB_PREFIX']}users.user_key  AND ({$CONFIG['DB_PREFIX']}statistics.space_key='$space_key') AND {$CONFIG['DB_PREFIX']}statistics.user_key='$user_key' GROUP BY {$CONFIG['DB_PREFIX']}statistics.module_key,{$CONFIG['DB_PREFIX']}statistics.use_type ORDER BY {$CONFIG['DB_PREFIX']}statistics.module_key,date_accessed DESC";
			
		$rs=$CONN->Execute($sql);
		
		if ($rs->EOF) {
		
			$message = $group_strings['no_stats'] ;
			
		} else {
		
			while (!$rs->EOF) {
			
				$count = $rs->fields[0];
				$username = $rs->fields[1];
				$module_name = $rs->fields[2];
				$use_type =  $rs->fields[3];
				
				if ($use_type=='post') {
			 
					 $use_type = "<strong>$use_type</strong>";
				 
				}
								
				$t->set_var('COLUMN_ONE',$module_name);
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

//format any errors from form submission

$concat = $CONN->Concat('last_name','\', \'', 'first_name');
$user_sql = "SELECT DISTINCT $concat,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND access_level_key='2' AND (group_key='$group_key') ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";

$user_menu = make_menu($user_sql,'user_key',$user_key,'1',false);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('USER_MENU',$user_menu);
$t->set_var('STATISTICS_HEADING',$group_strings['statistics_heading']);
$t->set_var('SPREADSHEET_LINK',$group_strings['spreadsheet_link']);
$t->set_var('BY_COMPONENT_STRING',$group_strings['by_component']);
$t->set_var('BY_USER_STRING',$group_strings['by_user']);
$t->set_var('VIEW_STRING',$general_strings['view']);
$t->set_var('SUMMARY_STRING',$general_strings['summary']);
$t->set_var('FULL_DETAILS_STRING',$general_strings['full_details']);
$t->parse('CONTENTS', 'header', true);

get_navigation();

$t->set_var('SPACE_KEY',$space_key);

$t->parse('CONTENTS', 'page', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
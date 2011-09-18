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
* Noticeboard module
*
* Displays the noticeboard module start page 
*
* @package Noticeboard
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: noticeboard.php,v 1.17 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/noticeboard_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];
$sort_order = isset($_GET['sort_order'])?$_GET['sort_order']:'date_added';
check_variables(true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

//update stats
statistics('read');

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'noticeboard'	 => 'noticeboard/noticeboard.ihtml',
	'footer'		  => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

$t->parse('CONTENTS', 'header', true);
get_navigation();

$t->set_block('noticeboard', 'AddNoticeBlock', 'ANBlock');
$t->set_block('noticeboard', 'NoticeBlock', 'NOBlock');


//get type setting

$rs = $CONN->Execute("SELECT type_key FROM {$CONFIG['DB_PREFIX']}noticeboard_settings WHERE module_key='$module_key'");

while (!$rs->EOF) {

	$type_key = $rs->fields[0];
	$rs->MoveNext();
	
}

if ($type_key==1 || $is_admin==true) {

	$t->Parse('ANBlock', 'AddNoticeBlock', true);
	
} else {

	$t->set_var('ANBlock','');
	
}
$t->set_var('ADD_NOTICE_STRING',$noticeboard_strings['add_notice']);
$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);

//now get any notices

$sql = "SELECT {$CONFIG['DB_PREFIX']}notices.notice_key, {$CONFIG['DB_PREFIX']}notices.heading, {$CONFIG['DB_PREFIX']}notices.body, {$CONFIG['DB_PREFIX']}notices.date_added, {$CONFIG['DB_PREFIX']}notices.user_key, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name FROM {$CONFIG['DB_PREFIX']}notices, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}notices.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}notices.module_key='$module_key' ORDER BY {$CONFIG['DB_PREFIX']}notices.$sort_order DESC";

$rs = $CONN->Execute($sql);
if ($rs->EOF) {

	$t->set_var('NOBlock',$noticeboard_strings['no_notices']);
	
} else {

	$notice_list = '';
	$n=0;
	while (!$rs->EOF) {
	

		$notice_key = $rs->fields[0];
		$heading	= $rs->fields[1];
		$body	   = $rs->fields[2];
		$date_added = $rs->fields[3];
		$user_key   = $rs->fields[4];
		$first_name = $rs->fields[5];								
		$last_name  = $rs->fields[6];
		
		if ($rs->RecordCount()>2) {
			
			if ($n>0) {
				$notice_list .= '<a href="#'.$notice_key.'" class="small">'.$heading.' ('.$dates->formatDate($CONN->UnixTimeStamp($date_added),'short').')</a> |';
			}
			$n++;
		} 
		$t->set_var('NOTICE_KEY',$notice_key);
		$t->set_var('HEADING',$heading);
		$t->set_var('BODY',$body);
		$t->set_var('DATE_ADDED',$dates->formatDate($CONN->UnixTimeStamp($date_added),'short'));
		$t->set_var('USER_NAME',$first_name.' '.$last_name);
		
		if ($is_admin==true || $user_key==$current_user_key) {
		 
			
			$t->set_var('EDIT_LINK',"<a href=\"noticeinput.php?space_key=$space_key&module_key=$module_key&notice_key=$notice_key&action=modify\">".$noticeboard_strings['edit_link'].'</a>&nbsp;');
			
		} else {
		
			$t->set_var('EDIT_LINK','');
			
		}
				
		$t->parse('NOBlock', 'NoticeBlock', true);
		
		$rs->MoveNext();
		
	}
	
	
}
$t->set_var('NOTICE_LIST',$notice_list);
$t->set_var('SORT_ORDER_STRING',$general_strings['sort_order']);
$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);
$t->set_var('DATE_ADDED_STRING',$general_strings['date_added']);
$t->set_var('HEADING_STRING',$general_strings['heading']);
$rs->Close();

$t->parse('CONTENTS', 'noticeboard', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
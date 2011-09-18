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
* File url display
*
* Displays the url of a file in a sharing module 
*
* @package Sharing
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: fileurl.php,v 1.21 2007/07/30 01:57:05 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/sharing_strings.inc.php');

//set variables
$space_key 		 = get_space_key();
$module_key		 = $_GET['module_key'];
$link_key 		 = get_link_key($module_key,$space_key);
$group_key		 = $_GET['group_key'];
$shareditem_key  = $_GET['shareditem_key'];

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

//get required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'showurl'		=>  'sharing/fileurl.ihtml',
	'footer'		  => 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('MODULE_NAME',$page_details[module_name]);
$t->set_var('SHAREDITEM_KEY',$shareditem_key);


$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('FILE_URL_HEADING_STRING',$sharing_strings['file_url_heading']);
$t->set_var('URL_FOR_STRING',$sharing_strings['url_for']);
$t->set_var('HOME_STRING',$general_strings['home']);
$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);
$t->parse('CONTENTS', 'header', true);
get_navigation();

$sql = "SELECT name,description, first_name, last_name, url,{$CONFIG['DB_PREFIX']}sharing_settings.file_path,filename,{$CONFIG['DB_PREFIX']}shared_items.date_added, {$CONFIG['DB_PREFIX']}shared_items.file_path FROM {$CONFIG['DB_PREFIX']}shared_items,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}sharing_settings WHERE {$CONFIG['DB_PREFIX']}shared_items.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}shared_items.module_key={$CONFIG['DB_PREFIX']}sharing_settings.module_key AND shared_item_key='$shareditem_key'";

$rs = $CONN->Execute($sql);

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

while (!$rs->EOF) {
	
	$name = $rs->fields[0];
	$description = $rs->fields[1];
	$username = $rs->fields[2].' '.$rs->fields[3];
	$url = $rs->fields[4];
	$file_path = $rs->fields[5];
	$file_name = $rs->fields[6];
	$date_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[7]),'short', true);	
	$shared_item_path = $rs->fields[8];	

	$t->set_var('NAME',$name);
	$t->set_var('DESCRIPTION',$description);
	$t->set_var('USER_NAME',$username);
	$t->set_var('DATE_ADDED',$date_added);
	
	if ($url!='') {
	   
	   $t->set_var('URL',$url);
  
	} else {
	
		$url=$CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/sharing/'.$file_path.'/'.$shared_item_path.'/'.$file_name;
		
		$t->set_var('URL',$url);
  
	}
	
	$rs->MoveNext();

}

$rs->Close();

$t->parse('CONTENTS', 'showurl', true);	
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;
 
?>
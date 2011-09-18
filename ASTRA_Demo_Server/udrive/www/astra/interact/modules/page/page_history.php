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
* Page homepage
*
* Displays a page start page. 
*
* @package Note
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: page_history.php,v 1.5 2007/02/18 23:34:12 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/page_strings.inc.php');
require_once('lib.inc.php');
$objPage = new InteractPage();
if (!isset($objDate)) {
	if (!class_exists('InteractDate')) {
		require_once('../../includes/lib/date.inc.php');
	}
	$objDate = new InteractDate();				
}
//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];
$current_user_key = $_SESSION['current_user_key'];

check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);
$is_space_member = $CONN->GetOne("SELECT UseKey FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$current_user_key'"); 


$page_data = $objPage->getPageData($module_key);
if (($page_data['page_edit_rights']==0 && !$is_admin && $_SESSION['userlevel_key']!=1) || ($page_data['page_edit_rights']==2 && empty($current_user_key)) || ($page_data['page_edit_rights']==1 && !$is_space_member)) {
	header("Location: {$CONFIG['FULL_URL']}/modules/page/page.php?space_key=$space_key&module_key=$module_key&message=".urlencode($general_strings['no_access_rights']));
}
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'page'			=> 'pages/pagehistory.ihtml',
	'footer'		  => 'footer.ihtml'
));

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->set_var('MODULE_KEY',$module_key);
$t->set_block('page', 'PageHistoryBlock', 'PageHisBlock');
$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}pages.page_key, {$CONFIG['DB_PREFIX']}pages.date_added, first_name, last_name FROM {$CONFIG['DB_PREFIX']}pages, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}pages.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND module_key='$module_key' ORDER BY {$CONFIG['DB_PREFIX']}pages.date_added DESC");
while (!$rs->EOF) {
	
	$t->set_var('DATE_ADDED',$objDate->formatDate($CONN->UnixTimestamp($rs->fields[1]),'short', true));
	$t->set_var('ADDED_BY',$rs->fields[2].' '.$rs->fields[3]);
	$t->set_var('PAGE_KEY',$rs->fields[0]);
	$t->Parse('PageHisBlock', 'PageHistoryBlock', true);
	$rs->MoveNext();	
}
$t->set_strings('page',  $page_strings);
$t->parse('CONTENTS', 'page', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
	   
$CONN->Close();
exit;


?>
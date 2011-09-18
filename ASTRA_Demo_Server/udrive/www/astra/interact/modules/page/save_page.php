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
* @version $Id: save_page.php,v 1.15 2007/07/22 23:37:40 glendavies Exp $
* 
*/

$no_safehtml=array('page_body');

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('lib.inc.php');

$objPage = new InteractPage();

//set variables
$space_key 	= $_POST['space_key'];
$module_key	= $_POST['module_key'];
if (empty($space_key) || empty($module_key)) {
	echo 0;
	exit;
}
$page_data = $objPage->getPageData($module_key);
if($page_data['page_edit_rights']!=3) {
	$access_levels = authenticate();
	$accesslevel_key = $access_levels['accesslevel_key'];
	$group_access = $access_levels['groups'];
	$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
}
$is_admin = check_module_edit_rights($module_key);
$current_user_key = $_SESSION['current_user_key'];
$is_space_member = $CONN->GetOne("SELECT UserKey FROM SpaceUserLinks WHERE SpaceKey='$space_key' AND UserKey='$current_user_key'"); 
if (($page_data['page_edit_rights']==0 && !$is_admin && $_SESSION['userlevel_key']!=1) || ($page_data['page_edit_rights']!=3 && empty($current_user_key)) || ($page_data['page_edit_rights']==1 && !$is_space_member)) {
	echo 0;
	exit;
}
//see if there are any changes before we worry about saving it
$old_body = $CONN->GetOne("SELECT body FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key' ORDER BY page_key DESC");
if ($old_body==$_POST['page_body']) {
	echo 1;
	exit;
}

if (!$is_admin) {
	$body= $_POST['page_body_safe'];
} else {
	$body = $_POST['page_body'];
}

$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));

if (empty($current_user_key) && $page_data['page_edit_rights']!=3) {
	echo 0;
	exit;
}
//if limited versions required get current page count
if ($page_data['versions']>0) {
	$version_count = $CONN->GetOne("SELECT COUNT(page_key) FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key'");
	
	if ($version_count>=$page_data['versions']) {
		$page_to_delete = $CONN->GetOne("SELECT page_key FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key' ORDER BY page_key");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}pages WHERE page_key='$page_to_delete'");
	}
}
if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}pages(module_key,body,added_by_key, date_added) values ('$module_key','$body', '$current_user_key', $date_added)")===false) {
	echo 0;
	exit;
} else {
	$objHtml = singleton::getInstance('html');
	echo $objHtml->parseText(interact_stripslashes($_POST['page_body']));
	exit;
}
?>
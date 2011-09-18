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
* @version $Id: get_body.php,v 1.5 2007/07/22 23:37:40 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('lib.inc.php');

$objPage = new InteractPage();

//set variables
$space_key 	= get_space_key();
$module_key	= $_POST['module_key'];
if (empty($space_key) || empty($module_key)) {
	echo 0;
	exit;
}
$current_user_key = $_SESSION['current_user_key'];
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);
$is_space_member = $CONN->GetOne("SELECT user_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$current_user_key'"); 
$page_data = $objPage->getPageData($module_key);
if (($page_data['page_edit_rights']==0 && !$is_admin && $_SESSION['userlevel_key']!=1) || ($page_data['page_edit_rights']!=3 && empty($current_user_key)) || ($page_data['page_edit_rights']==1 && !$is_space_member)) {
	echo 0;
	exit;
}
//get body of page
$body = $CONN->GetOne("SELECT body FROM {$CONFIG['DB_PREFIX']}pages WHERE module_key='$module_key' ORDER BY page_key DESC");
if (!$body) {
	echo 0;
	exit;
} else {
	echo $body;
	exit;
}
?>
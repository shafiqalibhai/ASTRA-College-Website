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
* Subscribe
*
* Subscirbes a user to a given module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: subscribe.php,v 1.2 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


$module_key = $_GET['module_key'];
$type_key = $_GET['type_key'];
$referer = $_GET['referer'];
$action = $_GET['action'];
$space_key 	= get_space_key();
authenticate();
check_variables(true,false,true);
$user_key = isset($_SESSION['current_user_key'])?$_SESSION['current_user_key']:'';
switch($action) {
	case 'subscribe':
		if ($user_key!='' && isset($module_key) && $module_key!='') { 
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}module_subscription_links(module_key, user_key, type_key) VALUES ('$module_key','$user_key','1')");
		}
	break;
	case 'unsubscribe':
		if ($user_key!='' && isset($module_key) && $module_key!='') { 
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}module_subscription_links WHERE module_key='$module_key' AND user_key='$user_key'");
		}
	break;
}	
header("Location: {$CONFIG['FULL_URL']}/$referer?space_key=$space_key&module_key=$module_key");
exit;
?>
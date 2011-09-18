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
* Set status
*
* Sets the current online status of a user to either visible or not visible 
*
* @package Messaging
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: setstatus.php,v 1.2 2007/01/04 22:08:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');

if (isset($_GET['status'])) {
	
	$_SESSION['online_status'] = ($_GET['status']==0)?0:1;
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}online_users SET status_key='{$_SESSION['online_status']}' WHERE user_key='{$_SESSION['current_user_key']}'");	
}

header("Location: ".$_SERVER['HTTP_REFERER']);
exit;

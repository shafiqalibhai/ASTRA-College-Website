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
* Message admin
*
* Changes status of messages 
*
* @package Messaging
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: message_admin.php,v 1.6 2007/01/04 22:08:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
require_once($CONFIG['BASE_PATH'].'/messaging/lib.inc.php');
$messagingObj = new InteractMessaging();

$action = isset($_GET['action'])?$_GET['action']:'delete';
$message = isset($_GET['message'])?$_GET['message']:'';

switch($action) {
	
	case 'delete':
		$user_key = isset($_SESSION['current_user_key'])?$_SESSION['current_user_key']:'';
		$message_key = isset($_GET['message_key'])?$_GET['message_key']:'';		$referer = isset($_GET['referer'])?$_GET['referer']:'';
		$messagingObj->deleteMessage($user_key,$message_key);
		//header("Location: ".$referer);
	break;
	case 'delete_all':
		$user_key = isset($_SESSION['current_user_key'])?$_SESSION['current_user_key']:'';
		$messagingObj->deleteMessage($user_key);
		//header("Location: ".$referer);
	break;
	
	
}
		$rs = $CONN->Execute("SELECT message_key,added_by_key, message, first_name, last_name, time FROM {$CONFIG['DB_PREFIX']}user_messages, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}user_messages.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND  added_for_key={$_SESSION['current_user_key']} AND ({$CONFIG['DB_PREFIX']}user_messages.status_key=1 OR {$CONFIG['DB_PREFIX']}user_messages.status_key=2)");

		$message_count  = $rs->RecordCount();
		if ($message_count==0) {
			$on_load = 'noMessages()';	
		} else {
			$on_load = "updateCount('$message_count')";	
		}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>User Messages</title>
	<meta http-equiv="Content-Type" content="text/html; charset={CHARACTER_SET}" />
	<script>
	function noMessages() {
		window.opener.document.getElementById("messageAlert").style["display"]=\'none\';
		window.opener.messageCount=0;
	}
	function updateCount(countText){
		window.opener.document.getElementById(\'messageCount\').innerHTML = countText;
		window.opener.messageCount = countText;
	}
	</script>
	<link rel="stylesheet" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key=1" type="text/css" media="screen, projection"></head><body style="padding:10px" onLoad="'.$on_load.'">';
		echo '<div align="center" class="message">'.$message.'</div>';
		echo '<div align="center"><a href="javascript:self.close();">'.$general_strings['close_window'].'</a> - <a href="message_admin.php?action=delete_all">'.$general_strings['delete_all'].'</a></div>';
		
		

		
		if (!$rs->EOF) {
			require_once($CONFIG['BASE_PATH'].'/messaging/lib.inc.php');
			$messagingObj = new InteractMessaging();
			echo $messagingObj->formatMessages($rs);
			$messagingObj->changestatus($_SESSION['current_user_key'],2);
			
			echo '<div align="center"><a href="javascript:self.close();">'.$general_strings['close_window'].'</a></div>';
		} else {
			echo 'You have no messages';

		}
echo '</body></html>';
exit;
?>
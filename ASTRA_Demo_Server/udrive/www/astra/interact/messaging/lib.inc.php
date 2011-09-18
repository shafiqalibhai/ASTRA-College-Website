<?php

// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education				  |
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
* Messaging functions
*
* Contains any functions related to the Interact messaging
*
* @package Messaging
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.5 2007/01/25 03:11:26 glendavies Exp $
* 
*/

/**
* A class that contains methods related to Interact messaging
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying user messages. 
* 
* @package Messaging
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteractMessaging {

	/**
	* constructor for InteractMessaging class 
	* 
	* sets any required variables, etc. 
	*
	* @param array $rs a result set of messages for a given user 
	* @return $messages string of formatted messages
	*/

	function InteractMessaging() {
	
		global $CONFIG, $messaging_strings;
		
		if (!isset($messaging_strings)) {
			require_once($CONFIG['LANGUAGE_CPATH'].'/messaging_strings.inc.php');
		}
		$this->_messaging_strings = $messaging_strings;
		
	}//end InteractMessaging
	
	/**
	* Loops through a result set of messages and formats them  
	* 
	* @param array $rs a result set of messages for a given user 
	* @return $messages string of formatted messages
	*/

	function formatMessages($rs)
	{
		global $CONFIG;
				
		if (!class_exists('InteractUser')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');
	
		}

		$objUser = new InteractUser();
		$messages = $this->_messaging_strings['new_messages'].':<br />';
		$request_uri = urlencode($_SERVER['REQUEST_URI']);
		while(!$rs->EOF) {
			//$messages .= '<div style="float:left;margin-right:5px">'.$objUser->getUserphotoTag($_SESSION['current_user_key'], '25', $space_key).'</div>';
			$messages .= '<div class="userMessages">'.$objUser->getUserphotoTag($rs->fields[1], '25', $_SESSION['current_space_key']).' '.$rs->fields[2].'<br><span class="small">'.$rs->fields[3].' '.$rs->fields[4].' '.date('d M g:ia',$rs->fields[5]).'</span>';
			$messages .= '<div style="text-align:right" class="small"><a href="'.$CONFIG['PATH'].'/messaging/message_admin.php?action=delete&message_key='.$rs->fields[0].'&referer='.$request_uri.'">Delete</a> - <a href="'.$CONFIG['PATH'].'/messaging/message_input.php?action=reply&message_key='.$rs->fields[0].'&referer='.$request_uri.'&user_key='.$rs->fields[1].'&space_key='.$_SESSION['current_space_key'].'">Reply</a></div>
<br style="clear:both"/></div><br />';
			$rs->MoveNext();	
		}
		return $messages;
	
	} //end formatMessages

	/**
	* change the status of messages for a given user  
	* 
	* @param int $user_key key of user to change status for
	* @param int $status_key status to change to
	* @param int $message_key optional key of message if only changing single
	* @return $messages string of formatted messages
	*/

	function changestatus($user_key,$status_key, $message_key='')
	{
		global $CONFIG, $CONN;
		
		$message_sql = ($message_key!='')?"AND message_key='$message_key'":'';	
		
		if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}user_messages SET status_key='$status_key' WHERE added_for_key='$user_key' $message_sql")===false){
			return 'There was a problem updating the status of user messages for user '.$user_key;
		} else {
			return true;
		}
	
	} //end changestatus	

	/**
	* delete a message  
	* 
	* @param int $user_key key of user to delete message or
	* @param int $message_key key of message to delete
	* @return $messages string of formatted messages
	*/

	function deleteMessage($user_key,$message_key='')
	{
		global $CONFIG, $CONN;

		$message_limit = !empty($message_key)? "AND message_key='$message_key'" : '';
		if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}user_messages  WHERE added_for_key='$user_key' $message_limit")===false){
			return 'There was a problem deleting a user messages for user '.$user_key;
		} else {
			return true;
		}
	
	} //end deleteMessage

	/**
	* add a message  
	* 
	* @param int $user_key key of user to delete message or
	* @param string $user_message text of message
	* @param int $current_user_key key of person adding message
	* @param int $time time message added	
	* @return true if successful
	*/

	function addMessage($user_key,$user_message, $current_user_key, $time)
	{
		global $CONFIG, $CONN;
		
		$user_message = $user_message;
		if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}user_messages(added_for_key, added_by_key, message, time, status_key) VALUES ('$user_key', '$current_user_key', '$user_message','$time',1)")===false){
			return 'There was a problem adding a user messages for user '.$user_key;
		} else {
			return true;
		}
	
	} //end addMessage
	
} //end InteractMessages
?>
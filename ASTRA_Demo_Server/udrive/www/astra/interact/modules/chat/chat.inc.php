<?php
// +----------------------------------------------------------------------+
// | chat.inc.php                                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education                 |
// +----------------------------------------------------------------------+
// | This file is part of Interact.                                       |
// |                                                                      | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)                             |
// |                                                                      | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     |
// | General Public License for more details.                             |
// |                                                                      | 
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, you can view it at                  |
// | http://www.opensource.org/licenses/gpl-license.php                   |
// |                                                                      |
// |                                                                      |
// | Includes functions for adding/modifying/deleting a chat room         |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz>                     |
// | Last Modified 29/01/03                                               |
// +----------------------------------------------------------------------+


/*
 *      Add required info to chats table
 *      
 */

function add_chat($module_key) {

    global $CONN, $CONFIG;
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}chat (module_key) values ('$module_key')";
	
	if ($CONN->Execute($sql) === false) {
		$message =  'There was an error adding your chat: '.$CONN->ErrorMsg().' <br />';
		return $message;
	} else {	  
		return true;  
	}
}

/*
 *      Get required data from chats table
 *      
 */

function get_chat_data($module_key) {
    return true;
}

/*
 *      modify data in chats table
 */

function modify_chat($module_key,$link_key) {
	return true;  
} //end modify_chat



/**
* delete a chat   
* 
*  
* @return true
*/
function delete_chat($module_key,$space_key,$link_key,$delete_action) 
{
    global $CONN,$CONFIG;
   	$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}chat WHERE module_key='$module_key'";
	$CONN->Execute($sql);
	$rows_affected = $CONN->Affected_Rows();
	if ($rows_affected < '1') {	     
    	$message = "There was an problem deleting a $module_code link during a module link deletion module_key=$module_key".$CONN->ErrorMsg();
		email_error($message);
		return $message;
	} else { 
	    return true;
    }
} //end delete_chat     

/**
* flag a chat for deletion   
* 
*  
* @return true
*/
function flag_chat_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
    return true;

} //end flag_chat_for_deletion

/**
* Copy a weblink   
* 
*  
* @return true
*/
function copy_chat($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

    global $CONN,$CONFIG;
	return add_chat($new_module_key);
   
} //end copy_chat

/*
 *      There is nothing else required other than a default module_add_link
 *      when adding chat module links, so just return true
 */

function add_chat_link($module_key) {

    return true;

}

/**
* Function called by auto.php to run any automated functions
*
* @return true if successful
*/

function autofunctions_chat($last_cron) {

	global $CONN, $CONFIG;
	
	$expire_date=$CONN->DBDate(date('Y-m-d H:i:s',strtotime("-2 days")));
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}chat_users WHERE last_poll<$expire_date");
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}chat_events WHERE time<$expire_date");

	return true;

} //end autofunctions_forum

?>

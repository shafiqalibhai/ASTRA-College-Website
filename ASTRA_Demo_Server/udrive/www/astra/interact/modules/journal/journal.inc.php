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
* Journal Module functions
*
* Contains the functions for adding/modifying/deleting a journal
* module
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: journal.inc.php,v 1.34 2007/07/18 22:05:34 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new journal module
*
* @param  int $module_key  key of new journal module
* @return true if details added successfully
*/
function add_journal($module_key) {

	global $CONN, $CONFIG;

	$selected_user_keys  = isset($_POST['selected_user_keys'])?$_POST['selected_user_keys']:'';
	$entries_to_show  = isset($_POST['entries_to_show'])?$_POST['entries_to_show']:'';	

	$options=0;	foreach ($_POST['options'] as $val) {$options|=$val;}
	
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}journal_settings(module_key,options, entries_to_show) VALUES ('$module_key','$options', '$entries_to_show')";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error adding your journal: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  

		//now add any selected users to the journal_user_links table
		if (is_array($selected_user_keys)) {
			$count = count($selected_user_keys);
			$n=1;
			$values = '';
			foreach($selected_user_keys as $value) {
				$values .= '('.$module_key.','.$value.')';
				if ($n<$count) {
					$values .= ',';
				} 
				$n++; 
			}	
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}journal_user_links(module_key,user_key) VALUES $values");
		}
		return true;  
	}
	

}

/**
* Function called by Module class to get exisiting journal data 
*
* @param  int $module_key  key of journal module
* @return true if data retrieved
*/
function get_journal_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT options, entries_to_show FROM {$CONFIG['DB_PREFIX']}journal_settings WHERE module_key='$module_key'";   
	
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$module_data['options'] = $rs->fields[0];
		
$mod_option_names=array('journal_access_level_key','members_key','show_comments','default_display','edit_rights','enable_rss','unauth_comments');
		for($i=0;$i<7;$i++) {
			$module_data[$mod_option_names[$i]] = $module_data['options']&(1<<$i);
		}

		$module_data['entries_to_show'] = $rs->fields[1];
		$rs->MoveNext();
	
	}
	//now get array of users if journal for selected users only
	if ($module_data['members_key']==2) {
		$module_data['selected_user_keys'] = $CONN->GetCol("SELECT user_key FROM {$CONFIG['DB_PREFIX']}journal_user_links WHERE module_key='$module_key'");
	}
	return true;

}

/**
* Function called by Module class to get exisiting journal data 
*
* @param  int $module_key  key of journal module
* @return true if data retrieved
*/

function modify_journal($module_key,$link_key) {

	global $CONN, $CONFIG;
	$selected_user_keys = isset($_POST['selected_user_keys'])?$_POST['selected_user_keys']:'';
	$entries_to_show  = isset($_POST['entries_to_show'])?$_POST['entries_to_show']:'';				$edit_rights  = isset($_POST['edit_rights'])?$_POST['edit_rights']:'0';

	$options=0;	foreach ($_POST['options'] as $val) {$options|=$val;}

	$sql = "UPDATE {$CONFIG['DB_PREFIX']}journal_settings SET options='$options', entries_to_show='$entries_to_show' WHERE module_key='$module_key'"; 
	   
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error modifying your journal: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
		//now add any selected users to the journal_user_links table
		if (is_array($selected_user_keys)) {
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}journal_user_links WHERE module_key='$module_key'");
			$count = count($selected_user_keys);
			$n=1;
			$values = '';
			foreach($selected_user_keys as $value) {
				$values .= '('.$module_key.','.$value.')';
				if ($n<$count) {
					$values .= ',';
				} 
				$n++; 
			}
			"INSERT INTO {$CONFIG['DB_PREFIX']}journal_user_links(module_key,user_key) VALUES $values";	
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}journal_user_links(module_key,user_key) VALUES $values");
		}	
			return true;  
	}

} //end modify_journal



/**
* Function called by Module class to delete exisiting journal data 
*
* @param  int $module_key  key of journal module
* @param  int $space_key  space key of journal module
* @param  int $link_key  link key of journal module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_journal($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN,$CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		if (!class_exists('InteractPosts')) {
			require_once $CONFIG['BASE_PATH'].'/includes/lib/posts.inc.php';
		}
		$objPosts = new InteractPosts();
		$objPosts->setVars($module_key, $space_key); 
		$post_data=array('module_key' => $module_key);
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key' AND parent_key='0'");
	   	if (!$rs->EOF) {
			
			while (!$rs->EOF) {
	   			$post_data['post_key'] = $rs->fields[0];
				$objPosts->deletePost($post_data);
				$rs->MoveNext();
			}		   
	   	}
	   	$rs->Close();
	   
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}journal_settings WHERE module_key='$module_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}journal_links WHERE module_key='$module_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}journal_user_links WHERE module_key='$module_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}statistics WHERE module_key='$module_key'");
		return true;
	}


} //end delete_journal	 

/**
* Function called by Module class to flag a jorunal for deletion 
*
* @param  int $module_key  key of jorunal module
* @param  int $space_key  space key of journal module
* @param  int $link_key  link key of journal being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_journal_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_jorunal_for_deletion   

/**
* Function called by Module class to copy a journal 
*
* @param  int $existing_module_key  key of journal being copied
* @param  int $existing_link_key  link key of journal module being copied
* @param  int $new_module_key  key of journal being created
* @param  int $new_link_key  link key of journal module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of journal module
* @param  int $new_group_key  heading key of new journal
* @return true if successful
*/
function copy_journal($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data, $space_key,$new_group_key) 
{

	global $CONN, $CONFIG;
	$options 			= $CONN->qstr($module_data['options']);
	$entries_to_show	= $CONN->qstr($module_data['entries_to_show']);
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}journal_settings(module_key,options,entries_to_show) VALUES ('$new_module_key',$options,$entries_to_show)";
	
	if ($CONN->Execute($sql) === false) {
	
		$message =  'There was an error copying journal: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else {	  
	
			return true;  
	}
	
} //end copy_journal


/**
* Function called by Module class to add new journal link
*
* @param  int $module_key  key of journal module
* @return true if successful
*/

function add_journal_link($module_key) {

	return true;

}

/*
 *	  There is nothing else required other than a default module_add_link
 *	  when adding journal module links, so just return true
 */

function get_jorunal_settings($module_key) {

	global $CONN, $CONFIG;
	
	$journal_settings = array();
	$sql = "SELECT Journaltype_key FROM {$CONFIG['DB_PREFIX']}JournalSettings WHERE module_key='$module_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$journal_settings['journal_type_key'] = $rs->fields[0];
	   	$rs->MoveNext();
		
	}
	
	$rs->Close();
		
	return $journal_settings;

}
/**
* Function called by deleteUser to run any functions related to deleting
* a user from the system
*
* @param int $user_key key of user being deleted
* @param int $deleted_user key of deleteduser user account
* @return true if successful
*/

function user_delete_journal($user_key, $deleted_user) {

	global $CONN, $CONFIG;
	
	//$rs = $CONN->Execute("SELECT JournalEntryKey FROM {$CONFIG['DB_PREFIX']}JournalEntries WHERE user_key='$user_key'");
	
	//while (!$rs->EOF) {
	
		//$entry_key = $rs->fields[0];
		//$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}JournalEntrycomments WHERE JournalEntryKey='$entry_key'");
		//$rs->MoveNext();
		
	//}
		
	//$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}JournalEntries WHERE user_key='$user_key'");
	//$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}JournalEntrycomments SET user_key='$deleted_user' WHERE user_key='$user_key'");
				
	return true;

}

/**
* Function called to see if there are any new or updated items in journal
* a user from the system
*
* @param int $user_key key of user to check for
* @return true if successful
*/

function updated_items_journal($user_key, $last_use, $groups_sql, &$updated_items, $module_strings, $space_key='') {

	global $CONN, $CONFIG, $general_strings;

	if (!empty($space_key)) {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM 
		{$CONFIG['DB_PREFIX']}posts,
		{$CONFIG['DB_PREFIX']}modules,
		{$CONFIG['DB_PREFIX']}spaces,
		{$CONFIG['DB_PREFIX']}module_space_links,
		{$CONFIG['DB_PREFIX']}journal_settings,
		{$CONFIG['DB_PREFIX']}space_user_links 
		WHERE 
		{$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}modules.module_key
		AND
		{$CONFIG['DB_PREFIX']}journal_settings.module_key={$CONFIG['DB_PREFIX']}modules.module_key
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key		
		AND 
		{$CONFIG['DB_PREFIX']}modules.type_code='journal'
		AND
		{$CONFIG['DB_PREFIX']}space_user_links.user_key='$user_key'
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key
		AND
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.status_key='1'
		AND 
		({$CONFIG['DB_PREFIX']}journal_settings.options&1 OR {$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1'  OR {$CONFIG['DB_PREFIX']}posts.user_key='$user_key')		
		AND
		({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
		OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql)
		AND
		({$CONFIG['DB_PREFIX']}posts.date_added >='$last_use') AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key')";
		
	} else {
	
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM 
		{$CONFIG['DB_PREFIX']}posts,
		{$CONFIG['DB_PREFIX']}modules,
		{$CONFIG['DB_PREFIX']}spaces,
		{$CONFIG['DB_PREFIX']}space_user_links,
		{$CONFIG['DB_PREFIX']}module_space_links,
		{$CONFIG['DB_PREFIX']}journal_settings 
		WHERE 
		{$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
		AND 
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key
		AND 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key
		AND
		{$CONFIG['DB_PREFIX']}journal_settings.module_key={$CONFIG['DB_PREFIX']}modules.module_key
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key
		AND
		(
		{$CONFIG['DB_PREFIX']}space_user_links.user_key='$user_key' 
		AND 
		({$CONFIG['DB_PREFIX']}journal_settings.options&1 OR {$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1' OR {$CONFIG['DB_PREFIX']}posts.user_key='$user_key')
		) 
		AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' 
		AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql) 
		AND ({$CONFIG['DB_PREFIX']}posts.date_added >='$last_use')";

	}

	$rs = $CONN->Execute($sql);
	
	if (!$rs->EOF) {

		$updated_items.='<strong>'.$module_strings['journal'].'</strong><br />';
	
		while (!$rs->EOF) {
	
			$module_key = $rs->fields[0];
			$name = $rs->fields[1];
			$group_key = $rs->fields[2];
			$space_key2 = $rs->fields[3];
			$space_name = $rs->fields[4];		
			
			if (!empty($space_key)) {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/journal/journal.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$name</a><br />";
				
			} else {
			
				$updated_items .= "<a href=\"{$CONFIG['PATH']}/modules/journal/journal.php?space_key=$space_key2&module_key=$module_key&group_key=$group_key\">$space_name - $name</a><br />";
							
			}
			$rs->MoveNext();
	
		}
	
		$rs->Close();
		
	}
	
	return true;
	
}

?>
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
* InteractGroup Class
*
* Contains the Group class for all methods and datamembers related
* to  groups
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.8 2007/01/04 22:09:04 glendavies Exp $
* 
*/

/**
* A class that contains methods for working with groups
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for working with groups 
* 
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractGroup {

	/**
	* Function to get exisiting group data 
	*
	* @param  int $module_key  key of quiz module
	* @return array $group_data data for selected group
	*/

	function getGroupData($module_key) {

	 
		global $CONN, $CONFIG;
	 
		$sql = "SELECT sort_order_key, access_key, access_code, visibility_key, maximum_users, minimum_users, start_date, finish_date, group_management FROM {$CONFIG['DB_PREFIX']}group_settings WHERE module_key='$module_key'";	
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$group_data['sort_order_key'] = $rs->fields[0];
			$group_data['access_key'] = $rs->fields[1];		
			$group_data['access_code'] = $rs->fields[2];
			$group_data['visibility_key'] = $rs->fields[3];	
			$group_data['maximum_users'] = $rs->fields[4];	
			$group_data['minimum_users'] = $rs->fields[5];
			$group_data['start_date_unix'] = $CONN->UnixTimestamp($rs->fields[6]);
			$group_data['finish_date_unix'] = $CONN->UnixTimestamp($rs->fields[7]);
			$group_data['group_management'] = $rs->fields[8];							
			$rs->MoveNext();
		}
		$rs->Close();
		return isset($group_data)? $group_data:false;

	} //end getGroupData()


	/**
	* Function to check if a user is a member of a group 
	*
	* @param  int $group_key  key of group
	* @param  int $user_key  key of user
	* @return array $membership_data data for user in group - access_level, date added. return false 
	* if not member
	*/

	function checkMembership($group_key, $user_key) {

	 
		global $CONN, $CONFIG;
	 
		$sql = "SELECT access_level_key, date_added FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key='$group_key' AND user_key='$user_key'";	
		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {

				$membership_data['access_level_key'] = $rs->fields[0];
				$membership_data['date_added'] = $CONN->UnixTimeStamp($rs->fields[1]);		
 			
		
				$rs->MoveNext();
	
			}
			
			return $membership_data;
			
		}
		
		$rs->Close();
		
	} //end checkMembership()
	
	/**
	* Function to count the number of members in a group 
	*
	* @param  int $group_key  key of group
	* @return int $total_members total number of members in a group
	*/

	function countMembers($group_key, $include_leaders=true) {

	 
		global $CONN, $CONFIG;
	 
		if ($include_leaders==true) {
		
			$sql = "SELECT COUNT(user_key) FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key='$group_key'";
			
		} else {
		
			$sql = "SELECT COUNT(user_key) FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key='$group_key' AND access_level_key='2'";		
		
		}  
		  
		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
		
			return false;
			
		} else {
		
			while (!$rs->EOF) {

				$total_members = $rs->fields[0];
				$rs->MoveNext();
	
			}
			
			return $total_members;
			
		}
		
		$rs->Close();
		
	} //end countMembers()
	
	/**
	* Function to count the number of new members in a group 
	*
	* @param  int $group_key  key of group
	* @return int $new_members total number of members in a group
	*/

	function countNewMembers($group_key, $user_key) {

	 
		global $CONN, $CONFIG;
	 
		//first of all find out the last time the user accessed this group
		
		
		$rs = $CONN->SelectLimit("SELECT date_accessed FROM {$CONFIG['DB_PREFIX']}statistics WHERE module_key='$group_key' AND user_key='$user_key' ORDER BY date_accessed DESC", 1);
		
		if ($rs->EOF || $CONN->ErrorMsg()) {
		
			//user has not accessed group before so don't show new members
			
			return 0;
			
		} else {
		
			if (!isset($_SESSION['group_'.$group_key.'_last_access'])) {
			
				$_SESSION['group_'.$group_key.'_last_access'] = $CONN->DBDate(date('Y-m-d H:i:s',$CONN->UnixTimestamp($rs->fields[0])));;
				
			} else {
			
				$date_accessed = $_SESSION['group_'.$group_key.'_last_access'];
			
			}
			
			$rs->Close();
				
		} 
		
		$new_members = $CONN->GetOne("SELECT COUNT(user_key) FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE date_added>$date_accessed AND group_key='$group_key'");

		return $new_members;

		
	} //end countMembers()

} //end InteractGroup class	
?>
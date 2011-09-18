<?php
// +------------------------------------------------------------------------+
// | This file is part of Interact.											|
// |																	  	| 
// | This program is free software; you can redistribute it and/or modify	|
// | it under the terms of the GNU General Public License as published by	|
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
* Modules functions
*
* This file contains functions, etc. related to adding/modifying modules
*
* @package Module
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: modules.inc.php,v 1.63 2007/07/25 21:30:15 glendavies Exp $
* 
*/
	
class InteractModules
{

	/**
	* array of language strings for forum module
	* @access private
	* @var array 
	*/
	var $_module_strings = '';
	
	/**
	* Constructor for InteractModulesClass
	*/

	function InteractModules() {
	
		//include the module strings file 
		
		global $CONFIG;
		
		require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');
		$this->_module_strings = $module_strings;
		
	
	} // end InteractModules constructor
	
	/**
	* Include function file for specified module code
	* 
	* @param [module_code] code of module to be included
	* 
	*/

	function set_module_type($module_code) {
	
		//include the file which contains all functions related to 
		//specified module code
		
		global $CONFIG;
		
		require_once($CONFIG['BASE_PATH']."/modules/$module_code/$module_code.inc.php");
	
	} // end set_module_type


	/**
	* Add a new module
	* 
	* @param [module_code] code of module to be added
	* 
	*/
	
	function add_module($module_code)
	{
		global $CONN, $CONFIG;
		
		$date_added			= $CONN->DBDate(date('Y-m-d H:i:s'));
		$name			  = $_POST['name'];
		$description	   = $_POST['description'];		 
		$space_key			 = $_POST['space_key'];
		$parent_key			= $_POST['parent_key'];
		$block_key			= $_POST['block_key'];
		$status_key			= $_POST['status_key'];
		$icon_key			  = $_POST['icon_key'];
		$group_key			 = $_POST['group_key'];
		$change_status_key	 = $_POST['change_status_key'];
		$sort_order			= $_POST['sort_order'];
		$target				= $_POST['target'];
		$module_edit_rights	= $_POST['module_edit_rights'];
		$link_edit_rights	  = $_POST['link_edit_rights'];		
		$access_level_key	  = isset($_POST['access_level_key']) ? $_POST['access_level_key'] : '0';
		$current_user_key	  = $_SESSION['current_user_key'];

		if ($_POST['change_status_date_day']!='' && $_POST['change_status_date_month']!='') {
		
			$change_status_date	= $_POST['change_status_date_year'].'-'.$_POST['change_status_date_month'].'-'.$_POST['change_status_date_day'];
		
		} else {
		
			   $change_status_date	= '';
		
		}		
				
		//if child and no status set then inherit status of parent module

		if ($parent_key!='' && !$status_key) {

			$sql = "SELECT status_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {

				$status_key = $rs->fields[0];
				$rs->MoveNext();

			}
			
			$rs->Close();

		}

		//if status hasn't been selected make it visible by default
		
		if (!$status_key) {

			$status_key='1';

		}
		
		if (!isset($icon_key) || $icon_key=='') {

			$icon_key='1';

		}

		//if edit rights haven't been selected make 'owner only' default
		
		if (!$module_edit_rights) {
		
			$module_edit_rights = 6;
			
		}
		
		if (!$link_edit_rights) {
		
			$link_edit_rights = 1;
			
		}

		


		//insert relevant data into Modules table

		$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}modules(module_key,type_code,name,description,added_by_key,owner_key,date_added,modified_by_key,date_modified,edit_rights_key) values ('','$module_code','$name','$description', '$current_user_key','$current_user_key',$date_added,'','','$module_edit_rights')";

		if ($CONN->Execute($sql) === false) {
	 
			$message =  'There was an error adding your '.$module_code.$CONN->ErrorMsg().' <br />';
			return $message;

		} else {

			//insert relevant data into ModuleSpacelinks table
		
			$module_key = $CONN->Insert_ID(); 
			
			$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}module_space_links(module_key, space_key, parent_key, block_key, group_key, status_key, icon_key, added_by_key, owner_key,date_added, modified_by_key, date_modified, edit_rights_key, change_status_date, change_status_to_key, sort_order, target, access_level_key) values ('$module_key','$space_key','$parent_key', '$block_key', '$group_key', '$status_key','$icon_key','$current_user_key','$current_user_key',$date_added,'','','$link_edit_rights','$change_status_date','$change_status_key','$sort_order','$target', '$access_level_key')";

			if ($CONN->Execute($sql) === false) {
	     
				$message =  "There was an error adding your $module_code: ".$CONN->ErrorMsg().' <br />';
				$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
				$CONN->Execute($sql);
				return $message;

			} else {
			
				$link_key = $CONN->Insert_ID();
				$module_link_data['change_status_date'] = $change_status_date;
				$module_link_data['change_status_key']  = $change_status_key;
				$module_link_data['sort_order']		 = $sort_order;
				$module_link_data['target']			 = $target;
				$module_link_data['link_edit_rights']   = $link_edit_rights;				
				
				//if this module has been added to a module then add links to it in any
				//linked copies of the module
							
				$this->add_sybling_links($link_key,$parent_key,$module_key,$module_link_data);

				//call the add function for this type of module
				
				$add_function = 'add_'.$module_code;
				$message = $add_function($module_key);
				if ($message===true) {
				
					return true;
					
				} else {
				
					$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}modules where module_key='$module_key'");
					$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}module_space_links where link_key='$link_key'");					
					return $message;
				
				}  

			}
		}		

	} //end add_module

	/**
	* Modify a module
	* 
	* @param [module_code] code of module to be added
	* 
	*/

	function modify_module($module_code,$can_edit_module=false)
	{
		
		global $CONN, $CONFIG;
		
		$date_modified		= $CONN->DBDate(date('Y-m-d H:i:s'));
		$name			 = $_POST['name'];
		$description	  = $_POST['description'];		 
		$space_key			= $_POST['space_key'];
		$parent_key		   = $_POST['parent_key'];
		$status_key		   = $_POST['status_key'];
		$icon_key			 = $_POST['icon_key'];		
		$group_key			= $_POST['group_key'];
		$change_status_key	= $_POST['change_status_key'];
		$sort_order		   = $_POST['sort_order'];
		$module_key		   = $_POST['module_key'];
		$link_key			 = $_POST['link_key'];		
		$target			   = $_POST['target'];
		$module_edit_rights   = $_POST['module_edit_rights'];
		$link_edit_rights	 = $_POST['link_edit_rights'];						
		$access_level_key	  = isset($_POST['access_level_key']) ? $_POST['access_level_key'] : '0';
		$current_user_key	 = $_SESSION['current_user_key'];
		if ($_POST['change_status_date_day']!='' && $_POST['change_status_date_month']!='') {
		
			$change_status_date	= $_POST['change_status_date_year'].'-'.$_POST['change_status_date_month'].'-'.$_POST['change_status_date_day'];
		
		} else {
		
			   $change_status_date	= '';
		
		}		
		
		//if no status key set make visible by default
		
		if (!$status_key) {

			$status_key='1';

		}

		if (!isset($icon_key) || $icon_key=='') {

			$icon_key='1';

		}


		//update allowed to edit module update everything Modules table
		if ($can_edit_module==true) {
		
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}modules SET name='$name',description='$description',date_modified=$date_modified,modified_by_key='$current_user_key',edit_rights_key='$module_edit_rights' WHERE module_key='$module_key'";

			if ($CONN->Execute($sql) === false) {

				$message =  "There was an error modifying your $module_code: ".$CONN->ErrorMsg().' <br />';
				return $message;

			} else {
		
				//update ModuleSpacelinks table

				$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='$status_key', icon_key='$icon_key', modified_by_key='$current_user_key',date_modified=$date_modified,change_status_date='$change_status_date',change_status_to_key='$change_status_key',sort_order='$sort_order',target='$target',edit_rights_key='$link_edit_rights', access_level_key='$access_level_key' WHERE module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'";
				
				if ($CONN->Execute($sql) === false) {

					$message =  "There was an error modifying your $module_code: ".$CONN->ErrorMsg().' <br />';
					return $message;

				} else {
			
					$modify_function = 'modify_'.$module_code;
					
					$message = $modify_function($module_key,$link_key);
					return $message;  
			
				}
			
			}

		} else {
		
			//if user only has link edit rights just update ModuleSpacelinksTable
			
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='$status_key',modified_by_key='$current_user_key',date_modified=$date_modified,change_status_date='$change_status_date',change_status_to_key='$change_status_key',sort_order='$sort_order',target='$target',edit_rights_key='$link_edit_rights', access_level_key='$access_level_key' WHERE module_key='$module_key' AND space_key='$space_key' AND link_key='$link_key'";

			if ($CONN->Execute($sql) === false) {

				$message =  "There was an error modifying your $module_code: ".$CONN->ErrorMsg().' <br />';
				return $message;

			} else {
			
				return true;  
			
			}
			
		} //end if($can_edit_module)
		
	} //end modify_module
	
	/**
	* Get module data to fill modify form
	* 
	* @param [module_code] code of module to be added
	* 
	*/

	function get_module_data($module_code,$module_key='',$link_key='')
	{

		global $CONN,$module_data, $CONFIG;
		
		$module_data	 = array();		

		if ($module_key=='') {

			$module_key	  = $_GET['module_key'];
			$link_key		= $_GET['link_key'];
			
		}		
				
		//get required data from Modules and ModuleSpacelinks tables

		$sql = "SELECT name,description,{$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.status_key,change_status_date,change_status_to_key,sort_order,target,{$CONFIG['DB_PREFIX']}modules.edit_rights_key,{$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key,{$CONFIG['DB_PREFIX']}modules.added_by_key, {$CONFIG['DB_PREFIX']}module_space_links.icon_key, {$CONFIG['DB_PREFIX']}module_space_links.block_key, {$CONFIG['DB_PREFIX']}module_space_links.access_level_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$link_key'";
		
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$module_data['name']		= $rs->fields[0];
			$module_data['description'] = $rs->fields[1];

			if ($rs->fields[3]!=0) {

				$module_data['parent_key']=$rs->fields[2];

			}

			$module_data['status_key']		  = $rs->fields[3];
			$module_data['change_status_date']  = $CONN->UnixTimestamp($rs->fields[4]);
			$module_data['change_status_key']   = $rs->fields[5];
			$module_data['sort_order']		  = $rs->fields[6];
			$module_data['target']			  = $rs->fields[7];
			$module_data['module_edit_rights']  = $rs->fields[8];
			$module_data['link_edit_rights']	= $rs->fields[9];   
			$module_data['module_added_by_key'] = $rs->fields[10];
			$module_data['icon_key']			= $rs->fields[11];
			$module_data['block_key']			= $rs->fields[12];									
			$module_data['access_level_key']	= $rs->fields[13];
			$rs->MoveNext();
			
			}
  
		$rs->Close();
		
		//call get_data function for specified module type
		
		$get_data_function = 'get_'.$module_code.'_data';
		$get_data_function($module_key);
		return $module_data;		
	
	} //end get_module_data
	
	/**
	* delete a module link or full module
	* 
	* @param [module_key]	 module key of module to be deleted
	* @param [space_key]	  space key of modulespacelink to be deleted
	* @param [link_key]	   link key of modulespacelink to be deleted
	* @param [delete_action]  type of delete to be performed
	* @param [module_code]	code of module to be deleted			
	*/

	function delete_module($module_key,$space_key='',$link_key='',$delete_action,$module_code)
	{
	
		global $CONN, $CONFIG;

		$this->set_module_type($module_code);
						
		switch ($delete_action) {
		
			case link_only:
		
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND space_key='$space_key' AND link_key='$link_key'";
			
			$CONN->Execute($sql);
			$rows_affected = $CONN->Affected_Rows();

			if ($rows_affected < '1') {	

				$message = "There was an problem deleting a $module_code link during a module link deletion module_key=$module_key".$CONN->ErrorMsg();
				email_error($message);
				return $message;
		
			} else { 
			
				//delete any link edit rights
				
				$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE link_key='$link_key'";
				$CONN->Execute($sql);
		
				//call delete function for module type
				
				$delete_function = 'delete_'.$module_code;
				$delete_function($module_key,$space_key,$link_key,$delete_action);
					
				return true;
										
			}
			
			break;
	
			case all:
				
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
			$CONN->Execute($sql);
			$rows_affected = $CONN->Affected_Rows();

			if ($rows_affected < '1') {	

				$message = "There was an problem deleting a $module_code during a full module deletion module_key=$module_key".$CONN->ErrorMsg();
				email_error($message);
				return $message;
		
			} else { 

				//delete any module edit rights links
				$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE module_key='$module_key'";
				$CONN->Execute($sql);
		
				//call delete function for this module type
	
				$delete_function = 'delete_'.$module_code;
				$delete_function($module_key,$space_key,$link_key,$delete_action);
				
				//now delete any user notes attached to this module
		
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE url like '%module_key=$module_key%'");
				
				return true;
			
			}
						
			break;
			
		} //end switch($delete_action)

		
	} //end delete_module

	/**
	* flag a module or link to be deleted
	* 
	* @param [module_key]	 module key of module to be deleted
	* @param [space_key]	  space key of modulespacelink to be deleted
	* @param [link_key]	   link key of modulespacelink to be deleted
	* @param [delete_action]  type of delete to be performed
	* @param [module_code]	code of module to be deleted
	* @param [sybling]		true if sybling of link being flagged			
	* @param [child]		  true if link is child of original link being deleted
	*						 to stop syblings of children being deleted 
	*/
	
	function flag_module_for_deletion($module_key,$space_key,$link_key,$delete_action,$module_code,$sybling=false,$child=false)
	{
	
		global $CONN, $CONFIG;

		$current_user_key = $_SESSION['current_user_key'];
		$date_modified	= $CONN->DBDate(date('Y-m-d H:i:s'));

		$this->set_module_type($module_code);
						
		switch ($delete_action) {
		
			case link_only:
		
			//get the parent key so we can flag any syblings for deletion
			if ($child==false) {
			
				$sql = "SELECT parent_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$link_key'";
				$rs = $CONN->Execute($sql);

				while (!$rs->EOF) {

					$parent_key = $rs->fields[0];
					$rs->MoveNext();

				}
		
				$rs->Close();
				
			}
			
			//change ModuleSpacelink to 'delete' status
			
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='4',modified_by_key='$current_user_key',date_modified=$date_modified WHERE module_key='$module_key' AND space_key='$space_key' AND link_key='$link_key'";

			$CONN->Execute($sql);
			$rows_affected = $CONN->Affected_Rows();

			if ($rows_affected < '1') {	
				
				email_error($message);
				return $message;
		
			} else { 
		
				$delete_function = "flag_".$module_code.'_for_deletion';
				$delete_function($module_key,$space_key,$link_key,$delete_action);

				if ($parent_key!=0 && $parent_key!='' && $sybling==false && $child==false) {
					
					$this->flag_sybling_links($link_key,$parent_key,$module_key,$space_key,$module_code);
				
				}
				
				//find out if this is the last link, if so flag module
				
				if ($sybling==false) {
				
					$sql = "SELECT link_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND status_key!='4'";
					$rs = $CONN->Execute($sql);		
									
					if ($rs->EOF) {
						
						$this->flag_module_for_deletion($module_key,$space_key,$link_key,'last',$module_code);
						return true;
					
					} else {
				
						return true;
					
					}
					
					$rs->Close();
					
				} else {
				
					return true;
					
				}
						
			}
			break;
	
			case all:
				
			//flag module for deletion

			$sql = "UPDATE {$CONFIG['DB_PREFIX']}modules SET status_key='4',modified_by_key='$current_user_key',date_modified=$date_modified WHERE module_key='$module_key'";
			$CONN->Execute($sql);
			$rows_affected = $CONN->Affected_Rows();

			if ($rows_affected < '1') {	

				$message = "There was an problem flagging a module for deletion $module_code  module_key=$module_key".$CONN->ErrorMsg();
				email_error($message);
				return $message;
		
			} else {
			
				//flag modulespacelink for deletion
			 
				$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='4',modified_by_key='$current_user_key',date_modified=$date_modified WHERE module_key='$module_key'";
				$CONN->Execute($sql);
				$rows_affected = $CONN->Affected_Rows();

				if ($rows_affected < '1') {	

					$message = "There was an problem flagging  $module_code links for deletion during  module_key=$module_key".$CONN->ErrorMsg();
					email_error($message);
					return $message;
		
				} else { 
				
					//call relevant function for this module type 
				
					$delete_function = 'flag_'.$module_code.'_for_deletion';
					$delete_function($module_key,$space_key,$link_key,$delete_action);
					return true;
			
				}
				
			}
			break;
			
			//if last link to a module is deleted then this method is called with delete
			//action = last in order to delete the module data also, to avoid orphaned 
			//modules with no modulespacelinks
			case last:
			
			//flag module for deletion
			
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}modules Set status_key='4',modified_by_key='$current_user_key',date_modified=$date_modified WHERE module_key='$module_key'";
			
			$CONN->Execute($sql);
			$rows_affected = $CONN->Affected_Rows();
			if ($rows_affected < '1') {	

				$message = "There was an problem flagging a $module_code for deletion during a last link deletion module_key=$module_key".$CONN->ErrorMsg();
				email_error($message);
				return $message;
		
			} else { 
		
				  //call relevane module delete function for this module type
		
				$delete_function = 'flag_'.$module_code.'_for_deletion';
				$delete_function($module_key,$space_key,$link_key,$delete_action);
				return true;
			
			}			
			
			break;			
		
		} //end switch($delete_action)
		
	}		

	/**
	* add a link to an existing module
	* 
	* @param [module_key]		module key of module to be deleted
	* @param [existing_link_key] link key of an existing link to module
	* @param [module_data]	   an array of module settings data
	*/
	
	function add_module_link($module_key,$existing_link_key,$module_data)
	{
		global $CONN, $CONFIG;

		$date_added			= $CONN->DBDate(date('Y-m-d H:i:s'));
		$space_key			 = $module_data['space_key'];
		$parent_key			= $module_data['parent_key'];
		$status_key			= $module_data['status_key'];
		$group_key			 = $module_data['group_key'];
		$block_key			 = $module_data['block_key'];
		$change_status_date	= $module_data['change_status_date'];
		$change_status_key	 = $module_data['change_status_key'];
		$sort_order			= $module_data['sort_order'];
		$target				= $module_data['target'];
		$module_code		   = $module_data['code']; 
		$link_edit_rights	  = $module_data['link_edit_rights'];			  
		$current_user_key	  = $_SESSION['current_user_key'];

		$this->set_module_type($module_code);
		
		//if child inherit status of parent module

		if ($parent_key!='' && $parent_key!='0') {

			$sql = "SELECT status_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {

				$status_key = $rs->fields[0];
				$rs->MoveNext();

			}
			
			$rs->Close();

		}

		//insert data into ModuleSpacelinks table
		$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}module_space_links(module_key, space_key, parent_key, group_key, status_key, added_by_key, owner_key, date_added,modified_by_key, date_modified,  edit_rights_key, change_status_date, change_status_to_key, sort_order, target, block_key) values ('$module_key','$space_key','$parent_key','$group_key', '$status_key','$current_user_key','$current_user_key', $date_added,'','','$link_edit_rights','$change_status_date','$change_status_key','$sort_order','$target','$block_key')";

		if ($CONN->Execute($sql) === false) {
		
			$message =  "There was an error adding your $module_code link: ".$CONN->ErrorMsg().' <br />';
			 return $message;

		   } else {
			
				//if insert was successful add sybling links in any
				//linked copies of parent modules
						
				$new_link_key = $CONN->Insert_ID();
				
				$module_link_data["change_status_date"] = $change_status_date;
				$module_link_data["change_status_key"] = $change_status_key;
				$module_link_data["sort_order"] = $sort_order;
				$module_link_data["target"] = $target;			
				$this->add_sybling_links($link_key,$parent_key,$module_key,$module_link_data);
				//call relevant add link function for this module type
				
				$add_link_function = 'add_'.$module_code.'_link';
				$message = $add_link_function($module_key,$existing_link_key,$new_link_key,$module_data);
				return $message;  

		}
				

	} //end add_module_link

	/**
	* copy an existing module
	* 
	* @param [module_key]		module key of module to be copied
	* @param [existing_link_key] link key of an existing link to module
	*/
	
	function copy_module($existing_module_key,$existing_link_key,$space_key,$new_parent_key='',$new_group_key='0')
	{
		global $CONN, $CONFIG;
		
		//get module type_key and code
		
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules WHERE  {$CONFIG['DB_PREFIX']}modules.module_key='$existing_module_key'";

		$rs = $CONN->Execute($sql);
		
		while (!$rs->EOF) {

			$module_code = $rs->fields[0];			
			$rs->MoveNext();

		}

		$rs->Close();

		$this->set_module_type($module_code);
		
		$module_data = $this->get_module_data($module_code,$existing_module_key,$existing_link_key);
		
		escape_strings($module_data);
		
		$date_added			= $CONN->DBDate(date('Y-m-d H:i:s'));
		$name			  = $module_data['name'];
		$description	   = $module_data['description'];		 
		$module_added_by_key   = $module_data['module_added_by_key'];
		$status_key			= $module_data['status_key'];
		$group_key			 = $module_data['group_key'];
		$change_status_key	 = $module_data['change_status_key'];
		$change_status_date	= $module_data['change_status_date'];		
		$sort_order			= $module_data['sort_order'];
		$target				= $module_data['target'];
		$module_edit_rights	= $module_data['module_edit_rights'];
		$icon_key	= $module_data['icon_key'];
		$link_edit_rights	  = $module_data['link_edit_rights'];		
		$current_user_key	  = $_SESSION['current_user_key'];
		

		if ($new_parent_key=='') {
		
			$parent_key = $_POST['parent_key'];
			
		} else {
		
			$parent_key = $new_parent_key;
			
		}		  


		//if child inherit status and group of parent module

		if ($parent_key!='' && $parent_key!='0') {

			$sql = "SELECT status_key,group_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {

				$status_key = $rs->fields[0];
				$new_group_key = $rs->fields[1];				
				$rs->MoveNext();

			}
			
			$rs->Close();

		}

		//insert relevant data into Modules table

		$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}modules(module_key,type_code,name,description,added_by_key,owner_key,date_added,modified_by_key,date_modified,edit_rights_key) values ('','$module_code','$name','$description', '$module_added_by_key','$current_user_key',$date_added,'','','$module_edit_rights')";

		if ($CONN->Execute($sql) === false) {
	   
			$message =  'There was an error adding your '.$module_code.'<br />'.$CONN->ErrorMsg();
			return $message;

		} else {

			//insert relevant data into ModuleSpacelinks table
		
			$module_key = $CONN->Insert_ID(); 
			$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}module_space_links(module_key, space_key, parent_key, group_key, status_key, added_by_key, owner_key,date_added, modified_by_key, date_modified, edit_rights_key, change_status_date, change_status_to_key, sort_order, target,icon_key) values ('$module_key','$space_key','$parent_key','$new_group_key', '$status_key','$current_user_key','$current_user_key',$date_added,'','','$link_edit_rights','$change_status_date','$change_status_key','$sort_order','$target','$icon_key')";

			if ($CONN->Execute($sql) === false) {
	   
				$message =  "There was an error adding your $module_code: ".$CONN->ErrorMsg().' <br />';
				$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
				$CONN->Execute($sql);
				return $message;

			} else {
			
				$link_key = $CONN->Insert_ID();
				$module_link_data['change_status_date'] = $change_status_date;
				$module_link_data['change_status_key']  = $change_status_key;
				$module_link_data['sort_order']		 = $sort_order;
				$module_link_data['target']			 = $target;
				
				//copy any module edit rights data
				$rs_edit_rights = $CONN->Execute("SELECT user_key, group_key, edit_level FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE module_key='$existing_module_key'");
				
				while (!$rs_edit_rights->EOF) {
				
					$rights_user_key   = $rs_edit_rights->fields[0];
					$rights_group_key  = $rs_edit_rights->fields[1];
					$rights_edit_level = $rs_edit_rights->fields[2];
					
					$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}module_edit_right_links(user_key, group_key,  edit_level, module_key) VALUES ('$rights_user_key', '$rights_group_key','$rights_edit_level', '$module_key')");
					
					$rs_edit_rights->MoveNext();
					
				}					
				
				$rs_edit_rights->Close();
				
				// now copy any link edit rights data
				$rs_edit_rights = $CONN->Execute("SELECT user_key, group_key, edit_level FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE link_key='$existing_link_key'");
				
				while (!$rs_edit_rights->EOF) {
				
					$rights_user_key   = $rs_edit_rights->fields[0];
					$rights_group_key  = $rs_edit_rights->fields[1];
					$rights_edit_level = $rs_edit_rights->fields[2];
					
					$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}module_edit_right_links(user_key, group_key,  edit_level, link_key) VALUES ('$rights_user_key', '$rights_group_key','$rights_edit_level', '$link_key')");
					
					$rs_edit_rights->MoveNext();
					
				}					
				
				$rs_edit_rights->Close();
									
				//if this module has been added to a module then add links to it in any
				//linked copies of the module
							
				$this->add_sybling_links($link_key,$parent_key,$module_key,$module_link_data);

				//call the add function for this type of module
				
				$copy_function = 'copy_'.$module_code;
				$message = $copy_function($existing_module_key,$existing_link_key,$module_key,$link_key, $module_data, $space_key,$new_group_key);
				return $message;  

			}
		}		

	} //end copy_module
	
	
	/**
	* return browser to parent module after module add/modify/delete
	* 
	* @param [module_code]	   code for module just edited
	* @param [action]			action that was performed on module
	*/

	function return_to_parent($module_code,$action)
	{
		global $CONN, $CONFIG, $space_key;

		$parent_key = $_POST['parent_key'];
		//$space_key  = $_POST['space_key'];
		$group_key  = $_POST['group_key'];
		$link_key   = $_POST['link_key'];
		$module_key = $_POST['module_key'];								
				  
 		if ($module_key!='' && $module_key!=$parent_key && $action!='deleted' && $action!='added' && $module_code=='page') {
		
			header("Location: {$CONFIG['FULL_URL']}/modules/$module_code/$module_code.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key&message=Your+$module_code+has+been+$action");
			exit;
		
		}
		//if we have a parent_key other than 0 return browser to parent module

		if ($parent_key && $parent_key!=0) {
		
			//see if parent is a folder or a group

			$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.module_key, type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$parent_key'";
			
			$rs = $CONN->Execute($sql);
			
			while (!$rs->EOF) {
			
				$module_key = $rs->fields[0];
				$module_type_code = $rs->fields[1];
				$rs->MoveNext();

			}
			
			$rs->Close();

			if ($module_type_code=='folder') {

				Header ("Location: {$CONFIG['FULL_URL']}/modules/folder/folder.php?space_key=$space_key&module_key=$module_key&link_key=$parent_key&parent_key=$parent_key&group_key=$group_key&message=Your+$module_code+has+been+$action");

			} else {

				Header ("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&module_key=$module_key&link_key=$parent_key&parent_key=$parent_key&group_key=$group_key&message=Your+$module_code+has+been+$action");

			}
					return true;
		
		//if no parent key return to space home

		} else {

			Header ("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=Your+$module_code+has+been+$action");
					return true;
					
		}
		
	}//end return_to_parent

	/**
	* Add sybling links. If parent module as syblings then insert links
	* to new module in all sybling links
	* 
	* @param [link_key]		 link key of new link
	* @param [parent_key]	   parent of new link
	* @param [module_key]	   modulekey of new link
	* @param [module_link_data] array of data for new link
	*/

	function add_sybling_links($link_key,$parent_key,$module_key,$module_link_data) 
	{
		global $CONN, $CONFIG;
		
		$change_status_date	= $module_link_data['change_status_date'];
		$change_status_key	 = $module_link_data['change_status_key'];
		$sort_order			= $module_link_data['sort_order'];
		$target				= $module_link_data['target'];
		$link_edit_rights	  = $module_link_data['link_edit_rights']; 				
		$current_user_key	  = $_SESSION['current_user_key'];
		$date_added			= $CONN->DBDate(date('Y-m-d H:i:s'));
	
		//get modulekey of parent 
		
		$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			   $module_key2 = $rs->fields[0];
			   $rs->MoveNext();

		}

		$rs->Close();
		
		$sql="SELECT link_key,space_key,group_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key2' AND link_key!='$parent_key' AND status_key!='4'";

		$rs=$CONN->Execute($sql);
		
		while (!$rs->EOF) {
				
			$link_key2 = $rs->fields[0];
			$space_key = $rs->fields[1];
			$group_key = $rs->fields[2];
				
			//check to make sure sybling link doesn't already exist
			
			$sql2 = "SELECT link_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND space_key='$space_key' AND parent_key='$link_key2' AND status_key!='4'";
			$rs2=$CONN->Execute($sql2);

			//if no sybling link then add one

			if ($rs2->EOF) {						
			
				$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}module_space_links(module_key, space_key, parent_key, group_key, status_key, added_by_key, owner_key,date_added, modified_by_key, date_modified, edit_rights_key, change_status_date, change_status_to_key, sort_order, target) values ('$module_key','$space_key','$link_key2','$group_key', '$status_key','$current_user_key','$current_user_key',$date_added,'','','$link_edit_rights','$change_status_date','$change_status_key','$sort_order','$target')";

				$CONN->Execute($sql);
			
			}
			$rs2->Close();

			$rs->MoveNext();
		}
		
		$rs->Close();
		
	} //end add_sybling_links	


	/**
	* Flag sybling links. If parent module as syblings then flag sybling links 
	* for deletion
	* @param [link_key]		 link key of link being deleted
	* @param [parent_key]	   parent of link being deleted
	* @param [module_key]	   modulekey of link being deleted
	* @param [space_key]		spacekey of link being deleted
	* @param [module_code]	  code for type of module being deleted
	*/
	
	function flag_sybling_links($link_key,$parent_key,$module_key,$space_key,$module_code) 
	{
		global $CONN, $CONFIG;
		
		//first we need to get the parent module_key
		$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$parent_module_key = $rs->fields[0];
			$rs->MoveNext();

		}		
		
		$rs->Close();
		
		//now get all the links to the parent module
		
		$sql = "SELECT link_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$parent_module_key'";
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$parent_link_key = $rs->fields[0];

			//get all the syblings of link being deleted that are not already
			//flagged for deletion
			
			$sql2 = "SELECT link_key,space_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE parent_key='$parent_link_key' AND module_key='$module_key' AND link_key!='$link_key' AND status_key!='4'";
		
			$rs2 = $CONN->Execute($sql2);

			while (!$rs2->EOF) {

				$link_key2 = $rs2->fields[0];
				$space_key2 = $rs2->fields[1];		
				$this->flag_module_for_deletion($module_key,$space_key2,$link_key2,'link_only',$module_code,true,true);		
				$rs2->MoveNext();

			}
			
		$rs2->Close();
		$rs->MoveNext();
		
	}
	
	$rs->Close();

		
	} //end flag_sybling_links
	

	/**
	* Create module input menus. Create generic input menus
	* needed for inputing modules
	* @param [module_data]	  array of module input data
	*/
	
	function create_module_input_menus($module_data)
	{
	
		global $CONFIG, $objDates;
		
		$menus_array = array();
		if ($_GET['action']=='modify') {
		
			$status_key		 = $module_data['status_key'];
			$icon_key		   = $module_data['icon_key']; 
			$change_status_date = $module_data['change_status_date']; 
			$change_status_key  = $module_data['change_status_key']; 
					
		
		} else {
				
			$change_status_date_month	 = $_POST['change_status_date_month'];
			$change_status_date_day	   = $_POST['change_status_date_day'];
			$change_status_date_year	  = $_POST['change_status_date_year'];
			$status_key				   = $_POST['status_key'];
			$icon_key					 = isset($_POST['icon_key'])? $_POST['icon_key']: '1' ;
			$change_status_key			= $_POST['change_status_key'];
			$change_status_date		   = $_POST['change_status_date'];
			
		}

		if ($change_status_date_month!='') {
	
			$change_status_date = mktime(0, 0,0 ,$change_status_date_month,$change_status_date_day,$change_status_date_year );
	
		}

 
		//generate icons menu
		$icon_sql = "SELECT name, icon_key from {$CONFIG['DB_PREFIX']}icons ORDER BY name";
		$menus_array['icon_menu'] = make_menu($icon_sql,'icon_key',$icon_key,'1', false, false);

		if (!is_object($objDates)) {

			if (!class_exists('InteractDate')) {

				require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
			}

			$objDates = new InteractDate();
		}
		
		$menus_array['date_menu'] = $objDates->createDateSelect('change_status_date',$change_status_date, false);
				
		return $menus_array;
		
	} //end create_module_input_menus
		
	/**
	* set common input variables for module input screens
	* needed for inputing modules
	* @param [page_details]	  array of data about current page
	* @param [menus_array]	   array of menus for inputting modules
	* @param [module_data]	   array of data about current module		
	*/

	function set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, $module_code)
	{
		global $CONN,$t,$delete_button,$button,$action,$name_error, $general_strings, $CONFIG, $module_strings, $objHtml, $objSkins, $space_key;
		

		
	if(xmlhconn_ok()) {$t->set_var('MESSAGE_INIT','<script type="text/javascript">messagetrans="'.$general_strings['messages'].'";message_refresh='.(isset($_SESSION['current_user_firstname'])?30000:590000).';setMessagePollTime();</script>');}

		
		$t->set_var('SERVER_NAME',$CONFIG['SERVER_NAME']);
		
		//find out which skin to use
		if (!is_object($objSkins)) {
			if (!class_exists('InteractSkins')) {
				require_once($CONFIG['BASE_PATH'].'/skins/lib.inc.php');
			}
			$objSkins = new InteractSkins();
		}
		$skin_key = $objSkins->getskin_key($space_key);
		$t->set_var('SKIN_KEY',$skin_key[0]);
		$t->set_var('SKIN_VERSION',$skin_key[1]);
		
		if ($_GET[action]=='modify' ) {
		
			$name			   = $module_data['name']; 
			$description		= $module_data['description'];
			$parent_key		 = $module_data['parent_key'];
			$status_key		 = $module_data['status_key']; 
			$icon_key		   = $module_data['icon_key']; 
			$change_status_date = $module_data['change_status_date']; 
			$change_status_key  = $module_data['change_status_key']; 
			$sort_order		 = $module_data['sort_order']; 
			$target			 = $module_data['target'];
			$module_edit_rights = $module_data['module_edit_rights'];
			$link_edit_rights   = $module_data['link_edit_rights'];			
			$space_key		  = $_GET['space_key']; 
			$group_key		  = $_GET['group_key'];
			$link_key		   = $_GET['link_key'];
			$block_key		   = $module_data['block_key'];
			$module_key		 = $_GET['module_key'];
			$parent_key		 = $_GET['parent_key']; 
			$module_key_details = $general_strings['module_text']." number = $module_key";
			$edit_rights_link   = "<a href=\"{$CONFIG['PATH']}/modules/general/editrights.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&action=modify\">Assign Edit Rights</a>";									
		
			
		} else if (!isset($_GET['action']) && !isset($_POST['action'])){
			
			$space_key   = $_GET['space_key']; 
			$group_key   = $_GET['group_key'];
			$link_key	= $_GET['link_key'];
			$block_key 	= $_GET['block_key'];
			$module_key  = $_GET['module_key'];
			$parent_key  = $_GET['parent_key'];  
			$module_edit_rights = 6;
			$link_edit_rights   = 1;	
		
		} else {
		
			$name		 = $_POST['name']; 
			$description  = $_POST['description']; 
			$parent_key   = $_POST['parent_key'];
			$space_key	= $_POST['space_key'];
			$group_key	= $_POST['group_key'];
			$module_key   = $_POST['module_key'];
			$link_key	 = $_POST['link_key'];
			$block_key	= $_POST['block_key'];
			$sort_order   = $_POST['sort_order'];
			$move_to_key  = $_POST['move_to_key']; 
			$module_edit_rights = $_POST['module_edit_rights'];
			$link_edit_rights   = $_POST['link_edit_rights'];				   
		
		}														
		
		if (($parent_key=='' || $parent_key==0) && ($block_key=='' || $block_key==0 )) {

			$module_parent = $this->_module_strings['input_module_parent'];

		} else if (empty($parent_key) && $block_key>0 ) {
		
			$module_parent = $this->_module_strings['block_'.$_GET['block_key']];
		} else {

			$sql = "SELECT name FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND link_key='$parent_key'";
			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {

				$module_parent = $rs->fields[0];
				$rs->MoveNext();
	
			}
			
			$rs->Close();

		}
		
		if ($_GET['action']=='modify' || $_GET['action']=='modify2' || $_POST['action']=='modify' || $_POST['action']=='modify2') {
		
			$input_heading = sprintf($this->_module_strings['modify_module_heading'], $this->_module_strings[$module_code], $module_parent);
		
		} else {
		
			$input_heading = sprintf($this->_module_strings['add_module_heading'], $this->_module_strings[$module_code], $module_parent);		
		
		}
	  
		if (!class_exists('InteractHtml')) {
		
			require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
		
		}
		
		if (!is_object($objHtml)) {
		
			
			$objHtml = new InteractHtml();
			
		}

		$new_window_menu = $objHtml->arrayToMenu(array('new_window' => $general_strings['yes'], '' => $general_strings['no']),'target',$target);
		
		$status_menu = $objHtml->arrayToMenu(array('1' => ucfirst($general_strings['visible']), '2' => ucfirst($general_strings['hidden']), '5' => ucfirst($general_strings['hidden-restricted'])),'status_key',$status_key, false, 2);
		
		$change_status_menu = $objHtml->arrayToMenu(array('1' => ucfirst($general_strings['visible']), '2' => ucfirst($general_strings['hidden']), '5' => ucfirst($general_strings['hidden-restricted'])),'change_status_key',$change_status_key, false, 2);
		
		$module_edit_rights_menu = $objHtml->arrayToMenu(array('1' => $this->_module_strings['edit_rights_1'], '2' => $this->_module_strings['edit_rights_2'], '3' => $this->_module_strings['edit_rights_3'], '5' => $this->_module_strings['edit_rights_5'], '6' => $this->_module_strings['edit_rights_6']),'module_edit_rights',$module_edit_rights);		
		
		$link_edit_rights_menu = $objHtml->arrayToMenu(array('1' => $this->_module_strings['link_edit_rights_1'], '2' => $this->_module_strings['link_edit_rights_2'], '3' => $this->_module_strings['link_edit_rights_3'], '4' => $this->_module_strings['link_edit_rights_4']),'link_edit_rights',$link_edit_rights);		
				

		$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
		$t->set_var('MHBlock','');

		$t->set_var('CONTEXT_HELP_LINK',ucfirst($module_code).'_Admin');
		$t->set_var('LOGIN_LINK',$page_details['login_link']);
		$t->set_var('OTHER_HEADER_LINKS',$page_details['other_header_links']);
		$t->set_var('HOME_LINK',$page_details['home_link']);
		$t->set_var('PAGE_TITLE',$page_details['full_space_name'].' - '.$page_details['module_name']);
		$t->set_var('SPACE_TITLE',$page_details['full_space_name']);
		$t->set_var('DOCS_URL',$CONFIG['DOCS_URL']);
		$t->set_var('PATH',$CONFIG['PATH']);
		$t->set_var('SPACE_BREADCRUMBS',$page_details['space_breadcrumbs']);
		$t->set_var('MODULE_BREADCRUMBS',$page_details['module_breadcrumbs']);
		$t->set_var('INPUT_HEADING',$input_heading);
		$t->set_var('ICON_STRING',$general_strings['icon']);		
		$t->set_var('NAME_ERROR',$name_error);
		$t->set_var('NAME',$name);
		$t->set_var('DESCRIPTION',$description);
		$t->set_var('NAME_STRING',$general_strings['name']);
		$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
		$t->set_var('CANCEL_STRING',$general_strings['cancel']);				
		$t->set_var('STATUS_MENU',$menus_array['status_menu']);
		$t->set_var('ICON_MENU',$menus_array['icon_menu']);
		$t->set_var('DATE_SELECT',$menus_array['date_menu']);
		$t->set_var('CHANGE_STATUS_MENU',$change_status_menu);
		$t->set_var('MODULE_EDIT_RIGHTS_MENU',$module_edit_rights_menu);
		$t->set_var('LINK_EDIT_RIGHTS_MENU',$link_edit_rights_menu);
		$t->set_var('ACCESS_LEVEL_'.$module_data['access_level_key'].'_CHECKED','checked');
		$t->set_var('ACTION',$action);
		$t->set_var('BUTTON',$button);
		$t->set_var('DELETE_BUTTON',$delete_button);
		$t->set_var('SPACE_KEY',$space_key);
		$t->set_var('PARENT_KEY',$parent_key);
		$t->set_var('LINK_KEY',$link_key);
		$t->set_var('BLOCK_KEY',$block_key);
		$t->set_var('GROUP_KEY',$group_key);
		$t->set_var('SORT_ORDER',$sort_order);
		$t->set_var('MODULE_KEY',$module_key);
		$t->set_var('MODULE_KEY_DETAILS',$module_key_details);		
		$t->set_var('EDIT_RIGHTS_LINK',$edit_rights_link);
		$t->set_var('MODULE_TEXT',ucfirst($general_strings['module_text']));		
		$t->set_var('STATUS_KEY',$status_key);
		$t->set_var('SORT_ORDER_STRING',$general_strings['sort_order']);
		$t->set_var('CANCEL_STRING',$general_strings['cancel']);
		$t->set_var('STATUS_STRING',$general_strings['status']);
		$t->set_var('OPTIONAL_SETTINGS_STRING',$general_strings['optional_settings']);
		$t->set_var('NEW_WINDOW_MENU',$new_window_menu);
		$t->set_var('STATUS_MENU',$status_menu);
		$t->set_var('NEW_WINDOW_STRING',$this->_module_strings['new_window']);
		$t->set_var('CHANGE_STATUS_DATE_STRING',$this->_module_strings['change_status_date']);
		$t->set_var('CHANGE_STATUS_TO_STRING',$this->_module_strings['change_status_to']);
		$t->set_var('LINK_EDIT_RIGHTS_STRING',$this->_module_strings['link_edit_rights']);
		$t->set_var('MOVE_STRING',$this->_module_strings['move']);
		$t->set_var('EXPLAIN_MOVE_STRING',$this->_module_strings['explain_move']);
		$t->set_var('MODULE_EDIT_RIGHTS_STRING',sprintf($this->_module_strings['module_edit_rights'], ucfirst($general_strings['module_text'])));
		$t->set_var('OPEN_STRING',$this->_module_strings['open_logged_in']);
		$t->set_var('OPEN_PUBLIC_STRING',$this->_module_strings['open_to_public']);
		$t->set_var('RESTRICTED_STRING',$this->_module_strings['restrict_to_members']);
		$t->set_var('ACCESS_STRING',$this->_module_strings['access']);
		$t->set_var('SAME_AS_PARENT_STRING',$this->_module_strings['same_as_parent']);
		$t->set_var('MOVE_SPACE_STRING',sprintf($this->_module_strings['move_space'], $general_strings['space_text'], $general_strings['module_text']));
		$t->set_var('CHARACTER_SET',$general_strings['character_set']);
		
		$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
		$t->set_var('MESSAGE_COUNT','0');
		$t->set_var('SCRIPT_INCLUDES','',true);
		$t->set_var('MESSAGE',isset($_GET['message'])?strip_tags($_GET['message']):'');
		if (!empty($page_details['space_alt_home'])) {
			$t->set_var('HEADER_HOME_LINK',$page_details['space_alt_home']);
		} else {
			$t->set_var('HEADER_HOME_LINK',$CONFIG['PATH'].'/spaces/space.php?space_key='.$CONFIG['DEFAULT_SPACE_KEY']);
		}
		$this->set_move_option($_GET['action'],$move_to_key,$space_key);			
		
		//if user only has link admin rights don't show module field input boxes

		if ($_GET['action']=='modify' || $_POST['submit']=='Modify') {

			if ($can_edit_module==false) {

				$t->set_block('form', 'ModuleInputBlock', 'MIBlock');
				$module_details = '<p align="left"><strong>name:</strong> '.$module_data['name'].'<br /><strong>description:</strong> '.$module_data['description'].'</p>';
				$t->set_var('MIBlock', $module_details);
		
			   $t->set_block('general', 'ModuleEditRightsBlock', 'MEBlock');
			   $t->set_var('MEBlock', '');
	
			}
	
		}
			
		
	}//end set_common_input_vars
	
	/**
	* set move option - create move menu for moving modules
	* 
	* @param [action]			action currently being performed on module
	* @param [move_to_key]	   link key of new parent
	* @param [space_key]		 space key of current space		
	*/
	
	function set_move_option($action,$move_to_key,$space_key) 
	{

		global $t, $CONFIG, $CONN;
		$link_key = $_GET['link_key'];
		$module_key = $_GET['module_key'];		
		
	
		//if module is being modified then create move menu option

		if ($action=='modify' || $action=='modify2') {
		
			//first see if this is a space we are moving, if so we need to show modules
			//from parent space, not from within itself
			$is_space = $CONN->GetOne("SELECT module_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
			if (!is_space) {
				$parent_space = $space_key;	
			} else {
				$parent_space = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key'");				
			}
			$move_to_sql = "SELECT {$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}module_space_links.link_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND (type_code='folder' OR type_code='group') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$parent_space' AND {$CONFIG['DB_PREFIX']}modules.module_key!='$module_key' ORDER BY name";

			$move_to_menu = make_menu($move_to_sql,"move_to_key",$move_to_key,"5");
			$t->set_var('MOVE_TO_MENU',$move_to_menu);
		
		} else {
	
			$t->set_block('general', 'MoveBlock', 'MBlock');
			$t->set_var('MBlock', '');
		
		}
		
	} //end set_move_option
	
	/**
	* restore a link flagged for deletion
	* 
	* @param [link_key]		  linkkey of link to be restored
	*/
	
	function restore_link($link_key) 
	{

		global $CONN, $CONFIG;
		
		//get modulekey, modulestatus,groupkey and parentkey 
		//of link to be restored
		
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.status_key, {$CONFIG['DB_PREFIX']}module_space_links.module_key, {$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$link_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {
	
			$module_status_key = $rs->fields[0];
			$module_key = $rs->fields[1];
			$parent_key = $rs->fields[2];
			$group_key = $rs->fields[3];
			$space_key = $rs->fields[4];
			$rs->MoveNext();
			
		}
		
		$rs->Close();

		//make sure an active version of module doesn't already exist
		
		$sql = "SELECT link_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND group_key='$group_key' and parent_key='$parent_key' AND space_key='$space_key' AND status_key!='4'";

		$rs = $CONN->Execute($sql);

		if (!$rs->EOF) {

			$message = "A restored version of that {$general_strings['module_text']} already exists in the place you are trying to restore it to";
			return $message;
			
		}
		
		$rs->Close();
		//make sure parent is not flagged for deletion, if it is change status

		if ($parent_key!='0') {

			$sql = "SELECT status_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";
			   $rs = $CONN->Execute($sql);

			while (!$rs->EOF) {
	
				$parent_status_key = $rs->fields[0];
				$rs->MoveNext();
			
			}

			$rs->Close();

			if ($parent_status_key=='4') {

				$this->restore_link($parent_key);

			}

		}

		//change status of link to be restored

		$sql = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='1' WHERE link_key='$link_key'";
		$CONN->Execute($sql);

		if ($module_status_key=='4') {
		
			$sql = "UPDATE {$CONFIG['DB_PREFIX']}modules SET status_key='1' WHERE module_key='$module_key'";
			$CONN->Execute($sql);
			
		}
		
		//if parent has syblings retore links in syblings also


		$sql = "SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$parent_module_key = $rs->fields[0];
			$rs->MoveNext();

		}

		$rs->Close();

		$sql = "SELECT link_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$parent_module_key' AND link_key!='$parent_key'";
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$sybling_link_key = $rs->fields[0];
			$sql2 = "UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='1' WHERE parent_key='$sybling_link_key' AND module_key='$module_key' AND link_key!='$link_key'";
			$CONN->Execute($sql2);
			$rs->MoveNext();

		}

		$rs->Close();

		//restore any children of this link
		
		$sql = "SELECT link_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE parent_key='$link_key'";
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$link_key2 = $rs->fields[0];
			$this->restore_link($link_key2);
			$rs->MoveNext();

		}

		$rs->Close();
		$message = "Your {$general_strings['module_text']} has been restored";	
		return $message;
			
	} //end restore_link
	
	/**
	* create a select list of modules a user has added
	* 
	* @param int $user_key key of user to create dropdown for
	* @param string $select_name name of select list
	*/
	
	function getModuleSelect($user_key, $select_name) 
	{
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT name, module_key FROM {$CONFIG['DB_PREFIX']}modules WHERE (owner_key='$user_key' OR added_by_key='$user_key') AND type_code!='space' ORDER BY name");
		if ($rs->EOF) {
			return false;
		} else {
			$module_select = '<select name="'.$select_name.'"><option value="" selected></option>';
			while (!$rs->EOF) {
				$module_select .= '<option value="'.$rs->fields[1].'">'.substr($rs->fields[0],0,30).' ('.$rs->fields[1].')</option>';
				$rs->MoveNext();
			}
			$rs->Close();
			$module_select .= '</select>';
			return $module_select;
		}
	}

} //end Modules class

?>
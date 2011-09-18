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
* InteractSpaceAdmin Class
*
* Contains the Space class for all methods and datamembers related
* to adding, modifying and deleting spaces
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2004 
* @version $Id: lib.inc.php,v 1.32 2007/07/22 23:37:41 glendavies Exp $
* 
*/

/**
* A class that contains methods for adding/modifying deleting spaces
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying space data 
* 
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractSpaceAdmin {

	/**
	* user key of current user
	* @access private
	* @var int 
	*/
	var $_user_key = '';
	
	/**
	* admin status of current user
	* @access private
	* @var true/false 
	*/
	var $_is_admin = '';
	
	/**
	* array of language strings for spaces
	* @access private
	* @var array 
	*/
	var $_space_strings = '';
	
	/**
	* Constructor for InteractSpace Class. Sets required variables
	*/
	
	function InteractSpaceAdmin() {
	
		global $CONFIG, $space_strings;
		
		if (!isset($space_strings)) {
		
			require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');
		
		}
		$this->_space_strings = $space_strings;				
		$this->_user_key	 = $_SESSION['current_user_key'];						
		
	} //end InteractSpaceAdmin
	
	/**
	* Check input form data before inputting/modifying a space
	*
	* @param  array $space_data  array of posted form data
	* @return array $errors array of any errors found
	*/
	function checkInputFormData(&$space_data) 
	{

		global $CONN, $CONFIG;
	
		// Initialize the errors array

		$errors = array();

		//check to see if we have all the information we need

 
		if (!isset($space_data['name']) || $space_data['name']=='') {

			$errors['name'] = $this->_space_strings['no_name'];

		}

		if (!isset($space_data['description']) || $space_data['description']=='') {

			$errors['description'] = $this->_space_strings['no_description'];

		}
		
		//check that code entered is unique
		if (isset($space_data['code']) && $space_data['code']!='') {

			$code = $space_data['code'];
			$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$code'"); 
			
			if (!$rs->EOF) {
			
				while(!$rs->EOF) {
				
					$space_key = $rs->fields[0];
					$rs->MoveNext();
					
				}
				
				if ($space_key!=$space_data['space_key']) {
								
					$errors['code'] = $this->_space_strings['code_in_use'];
				
				}
			
			}

		}

  
		if ($space_data['copy']=='true') {
	
			//check that space to copy from exists
			$copy_space_code = $space_data['copy_space_code'];
			$sql = "SELECT space_key, module_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$copy_space_code'";
			
			$rs = $CONN->Execute($sql);
		
			if ($rs->EOF) {
		
				$errors['copy_space'] = sprintf($this->_space_strings['no_copy_space'], $general_strings['space_text']);
			
			} else {
			
				while (!$rs->EOF) {
				
					$space_data['copy_space_key'] = $rs->fields[0];
					$space_data['copy_module_key'] = $rs->fields[1];
					$rs->MoveNext();
					
				}
				
				$rs->Close();			
				
				//now check that space is not being copied within itself
				
				if ($this->checkIsBelow($space_data['copy_module_key'], $space_data['space_key'])==true) {
					$errors['copy_space'] = sprintf($this->_space_strings['no_copy_within'],$general_strings['space_text'], $general_strings['space_text']);
				}
			
			}
		
		} 
		 
		return $errors;
	
	} //end checkInputFormData
	
	/**
	* Method of class InteractSpaceAdmin to add a new space
	*
	* @param  array $space_data  array of posted form data
	* @return true if successful
	*/
	function addSpace($space_data)
	{
	
		global $CONN, $CONFIG;
	
		$short_name	   = $space_data['short_name'];
		$name			 = $space_data['name'];
		$description	  = $space_data['description'];
		$access_code	  = $space_data['access_code'];
		$welcome_message	  = $space_data['welcome_message'];
		$combine_names	= $space_data['combine_names'];
		$access_level_key = isset($space_data['access_level_key'])?$space_data['access_level_key']:'1';
		$visibility_key   = $space_data['visibility_key'];	
		$copy			 = $space_data['copy'];
		$copy_space_code   = $space_data['copy_space_code'];
		$sort_order	   = $space_data['sort_order'];	
		$parent_key	   = $space_data['space_key'];
		$show_members	 = $space_data['show_members'];
		$spacemap	 = $space_data['spacemap'];
		$alt_home	 = $space_data['alt_home'];
		$code			 = $space_data['code'];						
		$current_user_key = $space_data['current_user_key'];
		$owned_by_key = isset($space_data['owned_by_key']) ? $space_data['owned_by_key'] : '';
		$module_key = $space_data['module_key'];
		$skin_key = isset($space_data['skin_key'])?$space_data['skin_key']:$CONFIG['DEFAULT_SPACE_KEY'];
		$new_user_alert	 = $space_data['new_user_alert'];
		$type_key	 = $space_data['type_key'];
		$short_date_key   = $CONFIG['SHORT_DATE_FORMAT'];
		$long_date_key	= $CONFIG['LONG_DATE_FORMAT'];
		$date_added=$CONN->DBDate(date('Y-m-d'));
	
		if (!isset($code) || $code=='') {
			
			$code = $this->generateSpaceCode();
						
		}
		
		$code = $code;
	
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}spaces(module_key, skin_key, code, short_name,name,combine_names,description,access_level_key,visibility_key,type_key,access_code, short_date_format_key, long_date_format_key, sort_order, show_members, new_user_alert, owned_by_key, space_map, alt_home, welcome_message) VALUES ('$module_key', '$skin_key', '$code', '$short_name','$name','$combine_names', '$description','$access_level_key','$visibility_key','$type_key','$access_code', '$short_date_key', '$long_date_key', '$sort_order', '$show_members','$new_user_alert', '$owned_by_key', '$spacemap', '$alt_home', '$welcome_message')";
	
		if ($CONN->Execute($sql) === false) {
	  
			$message =  'There was an error adding your Space: '.$CONN->ErrorMsg().' <br />';
			return $message;
	
		} else {
   
			$space_key = $CONN->Insert_ID();
		
			//if copy site specified copy modules from old site to new
			if (($copy=='true' && $copy_space_code!='') && (isset($copy_space_code) && $copy_space_code!='')) {
			
				$this->copySpace($space_key, $copy_space_code);
			
			}
			
			//if we have a site admin key add this to space_user_links table
			
			if (isset($space_data['space_admin_key']) && $space_data['space_admin_key']!='') {
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key,user_key,access_level_key) VALUES ('$space_key', '{$space_data['space_admin_key']}', '1')");
			
			
			}

			return true;

		}

	} //end addSpace
	
	/**
	* Method of class InteractSpaceAdmin to modify a space
	*
	* @param  array $space_data  array of posted form data
	* @return true if successful
	*/
	function modifySpace($space_data)
	{
	
		global $CONN, $CONFIG;
	
		$short_name	   = $space_data['short_name'];
		$name			 = $space_data['name'];
		$description	  = $space_data['description'];
		$welcome_message	  = $space_data['welcome_message'];
		$access_code	  = $space_data['access_code'];
		$code			 = $space_data['code'];		
		$space_key		= $space_data['space_key'];
		$combine_names	= $space_data['combine_names'];
	$access_level_key = isset($space_data['access_level_key'])?$space_data['access_level_key']:'1';
		$visibility_key   = $space_data['visibility_key'];	
		$copy			 = $space_data['copy'];
		$copy_space_code   = $space_data['copy_space_code'];
		$sort_order	   = $space_data['sort_order'];	
		$parent_key	   = $space_data['space_key'];
		$show_members	 = $space_data['show_members'];	
		$spacemap	 = $space_data['spacemap'];	
		$alt_home	 = $space_data['alt_home'];		
		$current_user_key = $space_data['current_user_key'];
		$module_key = $space_data['module_key'];
		$skin_key = $space_data['skin_key'];
		$new_user_alert	 = $space_data['new_user_alert'];
		$short_date_key   = $CONFIG['SHORT_DATE_FORMAT'];
		$long_date_key	= $CONFIG['LONG_DATE_FORMAT'];
		$date_added=$CONN->DBDate(date('Y-m-d'));
	
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}spaces SET skin_key='$skin_key', short_name='$short_name', name='$name', combine_names='$combine_names', description='$description', access_level_key='$access_level_key', visibility_key='$visibility_key', access_code='$access_code', short_date_format_key='$short_date_key', long_date_format_key='$long_date_key', sort_order='$sort_order', show_members='$show_members', code='$code', new_user_alert='$new_user_alert', space_map='$spacemap', alt_home='$alt_home', welcome_message='$welcome_message' WHERE module_key='$module_key'";

		if ($CONN->Execute($sql) === false) {
	  
			$message =  'There was an error modifying your Space: '.$CONN->ErrorMsg().' <br />';
			return $message;
	
		} else {
   
			//set the parent site details
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}SpaceChildlinks WHERE space_key='$space_key'");
			
			if (count($space_data['parent_keys'])==0) {
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}SpaceChildlinks(space_key,parent_key) VALUES ('$space_key', '0')");
								
			} else {
			
				foreach($space_data['parent_keys'] as $parent_key) {
				
					if ($parent_key!=$space_key) {
					
						$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}SpaceChildlinks(space_key,parent_key) VALUES ('$space_key', '$parent_key')");
						
					}
				
				}
				
			}  
			
			//if copy site specified copy modules from old site to new
			if ($copy=='true' && $copy_space_code!='') {
		
				$this->copySpace($space_key, $copy_space_code);
			
			}

			return true;

		}

	} //end modifySpace	


	/**
	* Method of class InteractSpaceAdmin to copy modules from another space
	*
	* @param  $space_key key of space to copy modules to
	* @param  $copy_space_code code of space to copy modules from 
	* @return true if successful
	*/
	function copySpace($space_key, $copy_space_code)
	{

		global $CONN, $CONFIG, $modules;
		
		//get key of space to copy
		$copy_space_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$copy_space_code'");
		
		//get header text of space being copied
		$header = $CONN->qstr($CONN->GetOne("SELECT Header FROM interact_Spaces WHERE space_key='$copy_space_key'"));
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}spaces SET Header=$header WHERE space_key='$space_key'");
		
		if (!class_exists(InteractModules)) {

			require_once($CONFIG['BASE_PATH'].'/includes/modules.inc.php');
				
		}
		$modules = new InteractModules();	
		$sql = "Select link_key,{$CONFIG['DB_PREFIX']}module_space_links.module_key from {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND (space_key='$copy_space_key' AND parent_key='0') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'";

		$rs = $CONN->Execute($sql);
		
		while (!$rs->EOF) {
	
			$existing_link_key	= $rs->fields[0];
			$existing_module_key  = $rs->fields[1];
			$modules->copy_module($existing_module_key,$existing_link_key,$space_key,'0');
			$rs->MoveNext();
	
		}
		
		return true;
	
	} //end copySpace()
	
	/**
	* Method of class InteractSpaceAdmin to get data for given space
	*
	* @param  $space_key key of space to get data for
 	* @return array $space_data array of space data
	*/
	function getSpaceData($module_key)
	{

		global $CONN, $CONFIG;
		
		$sql = "SELECT short_name, name, description,access_level_key, visibility_key, access_code, short_date_format_key, long_date_format_key, sort_order, show_members, combine_names, code, skin_key, new_user_alert, space_map, alt_home, welcome_message, space_key FROM {$CONFIG['DB_PREFIX']}spaces where module_key='$module_key'";
		$rs = $CONN->Execute($sql);
		 
		while (!$rs->EOF) {
				 
			$space_data['short_name']	   = $rs->fields[0];
			$space_data['name']			 = $rs->fields[1];
			$space_data['description']	  = $rs->fields[2];
			$space_data['access_level_key'] = $rs->fields[3];
			$space_data['visibility_key']   = $rs->fields[4];				 
			$space_data['access_code']	  = $rs->fields[5];
			$space_data['short_date_key']   = $rs->fields[6];
			$space_data['long_date_key']	= $rs->fields[7];
			$space_data['sort_order']	   = $rs->fields[8];
			$space_data['show_members']	 = $rs->fields[9];
			$space_data['combine_names']	= $rs->fields[10];
			$space_data['code']			 = $rs->fields[11];
			$space_data['skin_key']			 = $rs->fields[12];
			$space_data['new_user_alert']			 = $rs->fields[13];
			$space_data['spacemap']			 = $rs->fields[14];
			$space_data['alt_home']			 = $rs->fields[15];
			$space_data['welcome_message']			 = $rs->fields[16];
			$space_data['space_key']			 = $rs->fields[16];
			$rs->MoveNext();
		 
		}

		//now get any parent space info
		$sql = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}module_space_links where module_key='$module_key'";
		$rs = $CONN->Execute($sql);
		 
		$n=0;
		$space_data['parent_keys'] = array();
		while (!$rs->EOF) {
		
			$space_data['parent_keys'][$n] = $rs->fields[0];
			$n++;
			$rs->MoveNext();			
		
		}
		return $space_data;
	
	} //end getSpaceData()
	
	/**
	* Method of class InteractSpaceAdmin to delete a given space
	*
	* @param  $space_key key of space to get data for
 	* @return array $space_data array of space data
	*/
	function deleteSpace($module_key)
	{
		global $CONN,$modules, $CONFIG;

		if (!class_exists(InteractModules)) {

			require_once($CONFIG['BASE_PATH'].'/includes/modules.inc.php');
			
	
		}
		$modules = new InteractModules();
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'";
		$CONN->Execute($sql);

		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key'";
		$CONN->Execute($sql);

		$sql="DELETE FROM {$CONFIG['DB_PREFIX']}statistics WHERE space_key='$space_key'";
		$CONN->Execute($sql);

		$sql="DELETE FROM {$CONFIG['DB_PREFIX']}default_space_user_links WHERE space_key='$space_key'";
		$CONN->Execute($sql);

		//now delete any user notes attached to this space
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}UserNotes WHERE url like '%space_key=$space_key%'");
	
		//now delete all the modules links to this space
	
		$sql = "Select link_key,{$CONFIG['DB_PREFIX']}module_space_links.module_key,{$CONFIG['DB_PREFIX']}modules.type_code from {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND (space_key='$space_key' AND parent_key='0')";

		$rs = $CONN->Execute($sql);
		
		while (!$rs->EOF) {
	
			$link_key	= $rs->fields[0];
			$module_key  = $rs->fields[1];
			$module_code = $rs->fields[2];
			$modules->flag_module_for_deletion($module_key,$space_key,$link_key,'link_only',$module_code);
			$rs->MoveNext();
	
		}
		
		if ($delete_subs==1) {
		
			$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}SpaceChildlinks WHERE parent_key='$space_key'");
			
			while (!$rs->EOF) {
			
				$this->deleteSpace($rs->fields[0], $delete_subs);
				$rs->MoveNext();
				
			}
		
		} else {
		
			//get the parent key and move all subsites up one level
		  
			$rs = $CONN->Execute("SELECT parent_key FROM {$CONFIG['DB_PREFIX']}SpaceChildlinks WHERE space_key=$space_key");
		  
		  	while (!$rs->EOF) {
			
				$parent_key = $rs->fields[0];
				$rs->MoveNext();
				
			}
			
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}SpaceChildlinks SET parent_key='$parent_key' WHERE parent_key=$space_key");
		  		
		}
		
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}SpaceChildlinks WHERE space_key='$space_key'";
		$CONN->Execute($sql);


		return true;

	} //end deleteSpace()
	
	/**
	* Method of class InteractSpaceAdmin to generate a menu of parent spaces
	*
	* @param  int $parent_key key of parent to retrieve children for
 	* @param  string $parent_menu html select menu of parent spaces
	* @param  array $parent_keys array of any current parents for given space
	* @param  string $indent indent string to show nesting		
	*/
	function getSpaceParentMenu($parent_key=0, $parent_menu, $parent_keys, $current_space_key='', $indent='',$multiple=true, $sub_space=false)
	{

		global $CONN, $CONFIG;
		
	
		if ($multiple==false) {
			$multiple='';
			$name = 'space_key';
		} else {
			$multiple='multiple';
			$name = 'space_keys[]';
		}
		if ($indent=='') {
		
			$parent_menu = '<select name="'.$name.'" size="15" '.$multiple.'>';
			//get name of home space
			$home_name = $CONN->GetOne("SELECT name FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key={$CONFIG['DEFAULT_SPACE_KEY']}");

			$parent_menu .= '<option value="'.$CONFIG['DEFAULT_SPACE_KEY'].'" selected>'.$home_name.'</option>';
			$indent='&raquo;';
		}
		

		if ($current_space_key!='') {
			$current_space_sql = "AND {$CONFIG['DB_PREFIX']}spaces.space_key!='$current_space_key'";		}
		if($parent_key==0) {
			$parent_key = $CONFIG['DEFAULT_SPACE_KEY'];
		}
		
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}spaces.space_key, {$CONFIG['DB_PREFIX']}spaces.short_name, {$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}spaces WHERE  {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}spaces.module_key AND {$CONFIG['DB_PREFIX']}modules.type_code='space' AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$parent_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1'";
		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {
				 
			$space_data['space_key']		= $rs->fields[0];
			$space_data['short_name']	   = $rs->fields[1];
			$space_data['name']			 = $rs->fields[2];
			
			if (in_array($space_data['space_key'], $parent_keys)) {
			
				$parent_menu .= '<option value="'.$space_data['space_key'].'" selected>'.$indent.' '.$space_data['name'].'</option>';
				
			} else {
			
				$parent_menu .= '<option value="'.$space_data['space_key'].'">'.$indent.' '.$space_data['name'].'</option>';			
			
			}
			$this->getSpaceParentMenu($space_data['space_key'], &$parent_menu, $parent_keys, $current_space_key, $indent.'&raquo;', $multiple, true);
			$rs->MoveNext();
		 
		}
		
		if (!$sub_space) {
		
			$parent_menu .= '</select>';
			
		}
		
		return $parent_menu;
	
	} //end getSpaceParentMenu()	
	
	/**
	* Method of class InteractSpaceAdmin to get data for given space
	*
	* @return string $space_code random space code
	*/
	function generateSpaceCode()
	{

		global $CONN, $CONFIG;
		
		for($len=8,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1)?mt_rand(65,90):mt_rand(97,122))));
		
		$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces where code='$r'");
		
		if (!$rs->EOF) {
		
			$this->generateSpaceCode();
		
		} else {
		
			return $r;
			
		}
		

	} //end generateSpaceCode()	
	
	/**
	* Method of class InteractSpaceAdmin to check space not being copied with itself
	*
	* @param  int $space_to_copy_key key of space being copied
 	* @param  string $space_key key of current parent space
	* @param  
	* @return  true if space is being copied within itself		
	*/
	function checkIsBelow($copy_module_key, $space_key) 
	{
	global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.module_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND  {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key'");
		
		while(!$rs->EOF) {
			$module_key = $rs->fields[0];
			$space_key2 = $rs->fields[1];
			$rs->MoveNext();
		}

		if ($module_key==$copy_module_key) {
			
			return true;
		}
		if ($space_key2==0) {
			return false;
		} else {
			$this->checkIsBelow($copy_module_key, $space_key2);
		}
	}
					
} //end InteractSpaceAdmin class	
?>
<?php
/**
* InteractSkin Class
*
* Contains the Skin class for all methods and datamembers related
* to adding, modifying Skins
*
* @package Skins
*/

/**
* A class that contains methods for adding/modifying skins
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying skins 
* 
* @package Skins
*/

class InteractSkins {
	/**
	* method to get exisiting skin data 
	*
	* @param  int $skin_key  key of skin to get data for
	* @return array $skin_data data for selected skin
	*/
	function getSkinData($skin_key) 
	{
		global $CONN, $CONFIG;

 		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}skins WHERE skin_key='$skin_key'");
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		return $rs->fields;

	} //end getSkinData()

	/**
	* method to get an array of available skins 
	*
	* @param  string $type 'view' for list to view, 'edit' for list to edit
	* @return array $skin_array array of $skin_key => $skin_name data
	*/
	function getSkinArray($type='view') 
	{
		global $CONN, $CONFIG;
	 	
		$user_key = $_SESSION['current_user_key'];
		switch ($type) {
			case 'view': 
				$skin_key = $this->getskin_key($_SESSION['current_space_key']);
				$sql = "SELECT skin_key, name FROM {$CONFIG['DB_PREFIX']}skins WHERE scope_key='1' OR user_key='$user_key' OR skin_key='$skin_key'";
				
			break;
			
			case 'edit':
				if($_SESSION['userlevel_key']==1) {
				$sql = "SELECT skin_key, name FROM {$CONFIG['DB_PREFIX']}skins WHERE (scope_key='1' OR user_key='$user_key') AND skin_key!='1'";
				} else {
					$sql = "SELECT skin_key, name FROM {$CONFIG['DB_PREFIX']}skins WHERE user_key='$user_key' AND skin_key!='1'";
				}
			break;			 
		}
		
		$rs = $CONN->Execute($sql);
		if ($rs->EOF) {
			return false;
		} else {
			$skin_array = array();
			while (!$rs->EOF) {
				$skin_array[$rs->fields[0]] = $rs->fields[1];
				$rs->MoveNext();		
			}
			$rs->Close();
		}
		return $skin_array;

	} //end getSkinArray()
	
	/**
	* Method to add a skin 
	*
	* @param  array $skin_data array of data to add for skin
	* @return true true if successful, error message if not
	*/
	function addSkin($skin_data) 
	{
		global $CONN, $CONFIG;
	 	
		$current_user_key = $_SESSION['current_user_key'];	
		$skin_data['name'] = $this->checknameUnique($skin_data['name']);
		
		foreach ($skin_data as $key => $value) {
			$$key = $value;
		}
		$version = time();
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}skins(name, user_key, template,scope_key,body_background, body_font, body_border_colour, outer_box_background,  outer_box_border_colour,  header_logo, header_logo_width, header_logo_height,header_height,header_background, header_border_colour, server_name_colour ,inner_box_background , inner_box_border_colour, nav_background, nav_border_colour , content_background, content_border_colour,text_colour,colour1,colour2,raw_css, version) VALUES ('$name', '$current_user_key','$template','$scope_key','$body_background', '$body_font', '$body_border', '$outer_box_background', '$outer_box_border', '$header_logo', '$header_logo_width', '$header_logo_height', '$header_height', '$header_background', '$header_border', '$server_name_colour', '$inner_box_background', '$inner_box_border', '$nav_background', '$nav_border', '$content_background', '$content_border', '$text_colour','$colour1','$colour2','$raw_css', '$version')";
		
		if ($CONN->Execute($sql)===false) {
			return $CONN->ErrorMsg();
		} else {
			return true;
		}

	} //end addSkin()	
	
	/**
	* Method to modify a skin 
	*
	* @param  array $skin_data array of data to modify for skin
	* @return true true if successful, error message if not
	*/
	function modifySkin($skin_data) 
	{
		global $CONN, $CONFIG;

		$current_user_key = $_SESSION['current_user_key'];	
		$skin_data['name'] = $this->checknameUnique($skin_data['name'],$skin_data['skin_key']);
		if(!isset($skin_data['scope_key'])) {
			$skin_data['scope_key']=0;
		}
		foreach ($skin_data as $key => $value) {
			$$key = $value;
		}
		$version = time();
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}skins SET name='$name', user_key='$current_user_key', template='$template',scope_key='$scope_key',body_background='$body_background', body_font='$body_font', body_border_colour='$body_border', outer_box_background='$outer_box_background',  outer_box_border_colour='$outer_box_border', header_logo='$header_logo', header_logo_width='$header_logo_width', header_logo_height='$header_logo_height',header_height='$header_height', header_background='$header_background', header_border_colour='$header_border', server_name_colour ='$server_name_colour', inner_box_background ='$inner_box_background', inner_box_border_colour='$inner_box_border', nav_background='$nav_background', nav_border_colour ='$nav_border', content_background='$content_background', content_border_colour='$content_border', raw_css='$raw_css', text_colour='$text_colour', colour1='$colour1',colour2='$colour2',version='$version' WHERE skin_key='$skin_key'";
		
		if ($CONN->Execute($sql)===false) {
			return $CONN->ErrorMsg();
		} else {
			return true;
		}

	} //end modifySkin()	
	
	/**
	* Method to delete a skin 
	*
	* @param  array $skin_key key of skin to delete
	* @return true true if successful, error message if not
	*/
	function deleteSkin($skin_key) 
	{
		global $CONN, $CONFIG;

		//first update any spaces that use this skin
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}spaces SET skin_key='1' WHERE skin_key='$skin_key'");
		//now update any users
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}users SET skin_key='1' WHERE skin_key='$skin_key'");
		
		if ($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}skins WHERE skin_key='$skin_key'")===false) {
			return $CONN->ErrorMsg();
		} else {
			return true;
		}

	} //end deleteSkin()		
	
	/**
	* method to check a users rights to edit a skin 
	*
	* @param  string $skin_key key of skin to check rights for
	* @return true true if allowed to edit, false if not
	*/
	function checkEditRights($skin_key) 
	{
		global $CONN, $CONFIG;
	 	
		if ($_SESSION['userlevel_key']==1) {
			return true;
		}
		$rs = $CONN->Execute("SELECT skin_key FROM {$CONFIG['DB_PREFIX']}skins WHERE skin_key='$skin_key' AND user_key='{$_SESSION['current_user_key']}'");
		if ($rs->RecordCount()==0) {
			return false;
		} else {
			return true;
		}
	} //end checkEditRights()
	
	/**
	* return the skin key that should be used for current page 
	* @param  int $space_key key of current space
	*
	* @return int $skin_key key of skin to use for current page
	*/
	function getskin_key($space_key, $type_key=0) 
	{
		global $CONN, $CONFIG;

		if ($CONFIG['USER_SET_SKIN']==1 && !empty($space_key)) {
			if (!empty($_SESSION['skin_key'])){
				$skin_version = $CONN->GetOne("SELECT version FROM {$CONFIG['DB_PREFIX']}skins WHERE skin_key='{$_SESSION['skin_key']}");
				return array($_SESSION['skin_key'],$skin_version);
			} 
		} else 	if (($CONFIG['ADMIN_SET_SKIN']==1 || $type_key==1) && !empty($space_key)) {
			$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.skin_key,{$CONFIG['DB_PREFIX']}skins.version  FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}skins WHERE {$CONFIG['DB_PREFIX']}spaces.skin_key={$CONFIG['DB_PREFIX']}skins.skin_key AND {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key'");
			return array($rs->fields[0], $rs->fields[1]);
			$rs->Close();	
			
		} else {
			$skin_version = $CONN->GetOne("SELECT version FROM {$CONFIG['DB_PREFIX']}skins WHERE skin_key='{$CONFIG['DEFAULT_SKIN_KEY']}'");
			return array($CONFIG['DEFAULT_SKIN_KEY'], $skin_version);
		}
	
	} //end getskin_key()	
	
	/**
	* Check to see if an entered skin name is unique, if not make unique
	* 
	* @param string $name skin name to check
	* @param string $skin_key if modifying key of skin being modified
	* @return $name
	*/
	function checknameUnique ($name, $skin_key='')
	{

		global $CONN, $CONFIG;

		if($skin_key=='') { 
			$rs = $CONN->Execute("SELECT skin_key from {$CONFIG['DB_PREFIX']}skins WHERE name ='$name'");
			
		} else {
			$rs = $CONN->Execute("SELECT skin_key from {$CONFIG['DB_PREFIX']}skins WHERE name ='$name' AND skin_key!='$skin_key'");
			
		}
		
		if ($rs->EOF) {
		
			return $name;
			
		} else {
		
			$n = 1;
			$exists = true;
			while ($exists==true) {
			
				$new_name = $name.$n;
				$rs = $CONN->Execute("SELECT skin_key FROM {$CONFIG['DB_PREFIX']}skins WHERE name='$new_name'");
				
				if ($rs->EOF) {
					$exists = false;
					return $new_name;
				} else {
				
					$n++;
				
				}
				
			}
				
		}
			
	} //end checknameUnique

	
	
} //end InteractSkin class	
?>
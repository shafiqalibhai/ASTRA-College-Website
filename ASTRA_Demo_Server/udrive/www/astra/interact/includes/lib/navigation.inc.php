<?php
/**
* Navigation functions
*
* Contains any navigation related functions
*
* @package Common
*/

/**
* A class that contains methods related to generation of navigation items 
* 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for generation of navigation objects 
* 
*/
class InteractNavigation {
	
	
	var $_space_breadcrumbs = '';
	var $_module_breadcrumbs = '';
	var $_current_space_key = '';
	
	/**
	* Create a breadcrumb trail of spaces from current space back to home  
	* 
	* @param int $space_key key of current space
	* @return string $this->_space_breadcrumbs html breadcrumb trail
	*/
	function getSpaceBreadcrumbs($space_key){
		
		global $CONFIG, $general_strings;
		
		//see if default space - if so no top breadcrumbs needed
		if ($space_key==$CONFIG['DEFAULT_SPACE_KEY']) {
			if ($_SERVER['PHP_SELF']==$CONFIG['PATH'].'/spaces/space.php') {
					$this->_space_breadcrumbs = '<span class="currentSpaceBreadcrumb">'.$general_strings['home'].'</span>';
			} else { 
					$this->_space_breadcrumbs = '<a href="'.$CONFIG['PATH'].'/spaces/space.php?space_key='.$CONFIG['DEFAULT_SPACE_KEY'].'" class="currentSpaceBreadcrumb">'.$general_strings['home'].'</a>';
			}
			return $this->_space_breadcrumbs;
		}
		$this->_current_space_key = $space_key;
		$this->generateSpaceTrail($space_key);
		return $this->_space_breadcrumbs;
	}
	
	/**
	* Recursive function for breadcrumb trail of spaces from current space back to home  
	* 
	* @param int $space_key key of current space
	* @param int $link_key key of current module if any
	* @return binary return true when top reached
	*/
	function generateSpaceTrail($space_key)
	{
		global $CONN, $CONFIG, $general_strings;
		

		$sql="SELECT 
	 			{$CONFIG['DB_PREFIX']}spaces.space_key, 
				{$CONFIG['DB_PREFIX']}spaces.name, 
				{$CONFIG['DB_PREFIX']}spaces.short_name,
				{$CONFIG['DB_PREFIX']}spaces.alt_home,
				{$CONFIG['DB_PREFIX']}module_space_links.space_key 
			FROM
				{$CONFIG['DB_PREFIX']}spaces,
				{$CONFIG['DB_PREFIX']}module_space_links 
			WHERE
				{$CONFIG['DB_PREFIX']}module_space_links.module_key
				={$CONFIG['DB_PREFIX']}spaces.module_key 
			AND 
				({$CONFIG['DB_PREFIX']}spaces.space_key='$space_key')";	

		$rs = $CONN->Execute($sql);
		
	
		while (!$rs->EOF) {
			$parent_key=$rs->fields[4];
			//check that parent is not deleted or hidden
			if ($parent_key!=$CONFIG['DEFAULT_SPACE_KEY'] && !$CONN->GetOne("SELECT 
	 			{$CONFIG['DB_PREFIX']}spaces.space_key 
			FROM
				{$CONFIG['DB_PREFIX']}spaces,
				{$CONFIG['DB_PREFIX']}module_space_links 
			WHERE
				{$CONFIG['DB_PREFIX']}spaces.module_key=
				{$CONFIG['DB_PREFIX']}module_space_links.module_key
			AND 
				({$CONFIG['DB_PREFIX']}spaces.space_key='$parent_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1')")) {
				$rs->MoveNext();	
			} else {
				$space_key=$rs->fields[0];
				$long_name=$rs->fields[1];
				$short_name=$rs->fields[2];
				$alt_home=$rs->fields[3];
				
				break;
			}
		}
		$rs->Close();
		
		$name = (!empty($short_name))? $short_name: $long_name;
		
		if (!empty($alt_home)) {
			$this->_space_breadcrumbs ="<a href=\"$alt_home\">".$general_strings['home']."</a> &raquo; ".$this->_space_breadcrumbs;
			return true;
		} else if ($parent_key==0) {
			
			$this->_space_breadcrumbs ='<a href="'.$CONFIG['PATH'].'/spaces/space.php?space_key='.$CONFIG['DEFAULT_SPACE_KEY'].'">'.$general_strings['home'].'</a>'.$this->_space_breadcrumbs;
			return true;
		} else {
			if ($space_key==$this->_current_space_key) {
			
				if ($_SERVER['PHP_SELF']!=$CONFIG['PATH'].'/spaces/space.php'){
						$this->_space_breadcrumbs="  &raquo; <a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\" class=\"currentSpaceBreadcrumb\">$name</a>  ".$this->_space_breadcrumbs;
					
				}
			
			} else {
		
				$this->_space_breadcrumbs="  &raquo; <a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\">$name</a>  ".$this->_space_breadcrumbs;
		
			}
			$this->generateSpaceTrail($parent_key);
		}
	}
	
	/**
	* Create a breadcrumb trail of modules from current back to home space  
	* 
	* @param int $link_key key of current module
	* @return string $this->_module_breadcrumbs html breadcrumb trail
	*/
	function getModuleBreadCrumbs($link_key, $page_details) {
		
		global $CONN, $CONFIG;
				
		$parent_key = $CONN->GetOne("SELECT parent_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$link_key'");

		if ($parent_key==0) {
			if ($_SERVER['PHP_SELF']==$CONFIG['PATH'].'/modules/'.$page_details['module_code'].'/'.$page_details['module_code'].'.php'){
				return false;
			} else {
				if ($page_details['module_code']=='space') {
					return false;
				} else {
					return ' &raquo; <a href="'.$CONFIG['PATH'].'/modules/'.$page_details['module_code'].'/'.$page_details['module_code'].'.php?space_key='.$page_details['space_key'].'&module_key='.$page_details['module_key'].'">'.$page_details['module_name'].'</a> ';
				}
			}
			
		} else {
		
			$this->generateModuleTrail($parent_key);	
		}
		if ($_SERVER['PHP_SELF']!=$CONFIG['PATH'].'/modules/'.$page_details['module_code'].'/'.$page_details['module_code'].'.php'){
			return $this->_module_breadcrumbs.' &raquo; <a href="'.$CONFIG['PATH'].'/modules/'.$page_details['module_code'].'/'.$page_details['module_code'].'.php?space_key='.$page_details['space_key'].'&module_key='.$page_details['module_key'].'">'.$page_details['module_name'].'</a> ';	
		} else {
			return $this->_module_breadcrumbs;
		}
		
	}
	
	/**
	* recursive function to generate breadcrumb trail of modules from current back to home space  
	* 
	* @param int $parent_key key of next parent in trai
	* @return binary return true when top reached
	*/
	function generateModuleTrail($parent_key) {
		
		global $CONN, $CONFIG, $general_strings;

		$rs = $CONN->Execute("SELECT DISTINCT {$CONFIG['DB_PREFIX']}module_space_links.link_key, {$CONFIG['DB_PREFIX']}module_space_links.parent_key, {$CONFIG['DB_PREFIX']}module_space_links.group_key, {$CONFIG['DB_PREFIX']}modules.type_code, {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}modules.module_key from {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links where {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$parent_key'");
		
		if ($rs->EOF) {
	
			return false;
	
		}
		
		$link_key=$rs->fields[0];
		$new_parent_key=$rs->fields[1];
		$group_key=$rs->fields[2];
		$module_type_code=$rs->fields[3];
		$name=$rs->fields[4];
		$module_key=$rs->fields[5];
		$rs->Close();

		if ($new_parent_key==0) {

			if ($module_type_code=='group') {
				$this->_module_breadcrumbs = " &raquo; <a href=\"{$CONFIG['PATH']}/modules/group/group.php?space_key=$space_key&amp;module_key=$module_key&amp;link_key=$link_key&amp;group_key=$group_key\">$name</a> ".$this->_module_breadcrumbs;
			} else {
				$this->_module_breadcrumbs = " &raquo; <a href=\"{$CONFIG['PATH']}/modules/folder/folder.php?space_key=$space_key&amp;module_key=$module_key&amp;link_key=$link_key&amp;group_key=$group_key\">$name</a> ".$this->_module_breadcrumbs;
			}
			

		} else {

			if ($module_type_code=='group') {
				$this->_module_breadcrumbs = " &raquo;  <a href=\"{$CONFIG['PATH']}/modules/group/group.php?space_key=$space_key&amp;module_key=$module_key&amp;link_key=$link_key&amp;group_key=$group_key\">$name</a>  ".$this->_module_breadcrumbs;   
			
			} else {
				$this->_module_breadcrumbs = " &raquo; <a href=\"{$CONFIG['PATH']}/modules/folder/folder.php?space_key=$space_key&amp;module_key=$module_key&amp;link_key=$link_key&amp;group_key=$group_key\">$name</a>  ".$this->_module_breadcrumbs;   
			
			}

		 	$this->generateModuleTrail($new_parent_key);

		}

	}
	
}
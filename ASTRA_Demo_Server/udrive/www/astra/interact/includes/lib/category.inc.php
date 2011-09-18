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
* Category functions
*
* Contains any functions related to retrieving category tree information
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: category.inc.php,v 1.4 2007/01/04 22:08:59 glendavies Exp $
* 
*/

/**
* A class that contains methods related to category tree functions 
* 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for category tree related functions 
* 
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteractCategory {

	/**
	* Create an array of categories with correct nesting of sub categories  
	* 
	* @param string $sql sql to retrieve categories
	* @return array $category_array array of categories category_key => category name
	*/

	function getCategoryArray($sql)
	{

		global $CONN, $CONFIG;
		
		$category_array = array();
		$indent = '';
		$parent_key = 0;
		$category_sql  = $sql." AND parent_key='$parent_key' ORDER BY name";
		
		$rs=$CONN->Execute($category_sql);

		while (!$rs->EOF) {

			$category_name				 = $rs->fields[0];
			$category_key				  = $rs->fields[1];
			$category_parent			   = $rs->fields[2];
			$category_array[$category_key] = $category_name;

			$this->getCategoryChildren($sql, $category_key,' - ', $category_array);		
			$rs->MoveNext();		

		}
  
		$rs->Close();		
		return $category_array;
 
	} //end getCategoryArray
	
	/**
	* Create child categories   
	* 
	* @param string $sql sql to retrieve categories
	* @return array $category_array array of categories category_key => category name
	*/

	function getCategoryChildren($sql, $parent_key, $indent='', &$category_array)
	{

		global $CONN, $CONFIG;
		
		$category_sql  = $sql." AND parent_key='$parent_key' ORDER BY name";
		
		$rs=$CONN->Execute($category_sql);

		while (!$rs->EOF) {

			$category_name				  = $rs->fields[0];
			$category_key2				  = $rs->fields[1];
			$category_parent				= $rs->fields[2];
			$category_array[$category_key2] = $indent.$category_name;

			$this->getCategoryChildren($sql, $category_key2,$indent.' - ', $category_array);	  
			$rs->MoveNext();		

		}
  
		$rs->Close();
 
	
	} //end getCategoryArray	


} //end InteractCategory
?>
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
* Page Class
*
* Contains the Page class for all methods and datamembers related
* to adding, modifying and viewing Pages
*
* @package Pages
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.5 2007/01/29 01:34:38 glendavies Exp $
* 
*/

/**
* A class that contains methods for retieving and displaying and updating 
* pages
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying Pages 
* 
* @package Pages
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractPage {

	/**
	* A method of class Page to return settings for current page
	* @param int module key key of module to retrieve data for
	* @return array an array of settings for current journal
	*/
	
	function getPageData($module_key) {
	
		global $CONN,$CONFIG;
		$rs = $CONN->SelectLimit("SELECT body,versions, edit_rights FROM {$CONFIG['DB_PREFIX']}pages,{$CONFIG['DB_PREFIX']}page_settings WHERE {$CONFIG['DB_PREFIX']}pages.module_key={$CONFIG['DB_PREFIX']}page_settings.module_key AND {$CONFIG['DB_PREFIX']}pages.module_key='$module_key' ORDER By page_key DESC",1);
		echo $CONN->ErrorMsg();
		$body = preg_replace("/(<[^>]*)(form[ >])/si", "$1embedded-$2", $rs->fields[0]);
		$body = preg_replace("/(<[^>]*)(textarea[ >])/si", "$1embedded-$2", $body);		
		$page_data['body'] = $body;
		$page_data['versions'] = $rs->fields[1];
		$page_data['page_edit_rights'] = $rs->fields[2];
		$rs->Close();
		return $page_data;
	
	}
	/**
	* A method of class Page to return settings for current page
	* @param int module key key of module to retrieve current page for
	* @return int page_key key of current page
	*/	
	function getCurrentPage($module_key) {
	
		global $CONN,$CONFIG;
		return $CONN->GetOne("SELECT page_key FROM {$CONFIG['DB_PREFIX']}pages WHERE {$CONFIG['DB_PREFIX']}pages.module_key='$module_key' ORDER By page_key DESC");
		
	
	}
					
} //end Page class	
?>
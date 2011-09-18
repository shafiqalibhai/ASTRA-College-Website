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
* Weblink homepage
*
* Refers a browser to Weblink module url. 
*
* @package Weblink
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: weblink.php,v 1.8 2007/07/30 01:57:05 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../../local/config.inc.php');


//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$accesslevel_key = authenticate();

//update statistics 
statistics('read');

$sql = "SELECT url FROM {$CONFIG['DB_PREFIX']}weblinks,{$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}weblinks.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$link_key'";

$rs = $CONN->Execute($sql);
while (!$rs->EOF) {

	$link_url = $rs->fields[0];
	$rs->MoveNext();

}

header ("Location: $link_url");
exit;
?>
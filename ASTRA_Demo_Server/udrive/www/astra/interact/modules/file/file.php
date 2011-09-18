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
* File module
*
* Displays the file module start page 
*
* @package File
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: file.php,v 1.17 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/file_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];

//check we have the required variables
check_variables(true,false,true);

//update statistics 
statistics('read');

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$sql = "select file_path, filename, embedded from {$CONFIG['DB_PREFIX']}files where module_key='$module_key'";

$rs = $CONN->Execute($sql);

while (!$rs->EOF) {

	$file_path = $rs->fields[0];
	$name	  = $rs->fields[1];
	$embedded  = $rs->fields[2];	
	$rs->MoveNext();

}
$rs->Close();

header('Location: '.$CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/file/'.$file_path.'/'.$name);
exit;
?>
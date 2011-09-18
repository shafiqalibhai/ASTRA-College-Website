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
* Popup
*
* Displays certain components in a small popup window
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: popup.php,v 1.9 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


$module_key = $_GET['module_key'];
$type_key = $_GET['type_key'];
$space_key 	= get_space_key();

//check we have the required variables
check_variables(false,false,true);


switch($type_key) {

	case 12:
		
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}pages.body FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}pages WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}pages.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key='$module_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$module_name = $rs->fields[0];
			$page_body = $rs->fields[1];
			$rs->MoveNext();
	
		}
	
	break;
	
	case 5:
		
		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}notes.note FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}notes WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}notes.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key='$module_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$module_name = $rs->fields[0];
			$page_body = $rs->fields[1];
			$rs->MoveNext();
	
		}
		break;		
				

}

$rs->Close();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $module_name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="/Interactstyle.css" type="text/css" />

</head>

<body bgcolor="#FFFFFF" text="#000000" >
<div align="center"><a href="javascript:close()"><?php echo $general_strings['close_window']; ?></a></div>
<strong><?php echo $module_name; ?></strong>
<p><?php echo $page_body; ?></p>
<?php
if (strlen($page_body)>1000) {
?>
<div align="center"><a href="javascript:close()"><?php echo $general_strings['close_window']; ?></a></div>
<?php
}
?>
</body>
</html>
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
* Excel stats
*
* View group statistics as an excel spreadsheet
*
* @package Group
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: excelstats.php,v 1.14 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


$space_key  = $_GET['space_key'];
$group_key  = $_GET['group_key'];
$module_key = $_GET['module_key'];

//check we have the required variables

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$can_edit_module = check_module_edit_rights($module_key);

if ($can_edit_module!=true) {

	header("Location: {$CONFIG['FULL_URL']}/index.php?message=Sorry+you+can+not+access+that+page");
	
}

header("Content-Type: application/vnd.ms-excel")
?>

<table border="1">
<tr>
<td>name</td><td>Component</td><td>Date Accesses</td><td>Use Type</td><td>Location</td>
</tr>
<?php

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

$sql = "SELECT first_name, last_name, {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}statistics.date_accessed,use_type, location FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}statistics.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND ({$CONFIG['DB_PREFIX']}statistics.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key') ORDER BY date_accessed DESC";
	$rs=$CONN->Execute($sql);
	echo $CONN->ErrorMsg();
	if ($rs->EOF) {
		?>
		<tr><td colspan="5">There are no statistics yet for this space</td></tr>
		</table>
		<?php

	} else {
		while (!$rs->EOF) {
			$username = $rs->fields[0].' '.$rs->fields[1];
			$module_name = $rs->fields[2];
			$date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[3]),'short', true);			
			$use_type =  $rs->fields[4];
			$location = $rs->fields[5];
			if ($use_type=='post') {
			 
				 $use_type = "<strong>$use_type</strong>";
				 
			}
			
			echo "<tr>		  <td>$username</td><td>$module_name</td><td>$date_accessed</td><td>$use_type</td><td>$location</td>
			</tr>";
			
			$rs->MoveNext();
			 
			}
			echo"</table>";
			
   }
		
		
  
?>
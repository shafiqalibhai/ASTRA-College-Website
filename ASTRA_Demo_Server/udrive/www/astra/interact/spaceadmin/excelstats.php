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
* Display statistics as a spreadsheet
*
* Displays access statistics for a space as an excel spreadsheet
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: excelstats.php,v 1.17 2007/07/18 00:40:41 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../local/config.inc.php');


$space_key  = $_GET['space_key'];
$module_key = $_GET['module_key'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
//check we have the required variables
check_variables(true,false);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels["accesslevel_key"];
authenticate_admins($level="space_only");

if (!class_exists('InteractDate')) {

	require_once('../includes/lib/date.inc.php');

}
$dates = new InteractDate();

header("Content-Type: application/vnd.ms-excel");
header("Content-disposition: inline; filename=stats.xls");
$start_date_limit = !empty($start_date)?'AND '.$CONFIG['DB_PREFIX'].'statistics.date_accessed>'.$CONN->DBDate($start_date):'';
$end_date_limit = !empty($end_date)?'AND '.$CONFIG['DB_PREFIX'].'statistics.date_accessed<'.$CONN->DBDate($end_date):'';

echo "name	Component	Date Accesses	Use Type	Location\n";



$sql = "SELECT first_name, last_name, {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}statistics.date_accessed,use_type, Location,{$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}statistics,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}statistics.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}statistics.user_key={$CONFIG['DB_PREFIX']}users.user_key  AND ({$CONFIG['DB_PREFIX']}statistics.space_key='$space_key') $start_date_limit $end_date_limit ORDER BY date_accessed DESC";
	$rs=$CONN->Execute($sql);
	if ($rs->EOF) {
		?>
		There are no statistics yet for this space
		
		<?php

	} else {
		while (!$rs->EOF) {
			$username = $rs->fields[0].' '.$rs->fields[1];
			$module_name = $rs->fields[2];
			$date_accessed = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[3]),'short', true);
			$use_type =  $rs->fields[4];
			$location = $rs->fields[5];
			$module_type = $rs->fields[6];
			 
			 
			echo "$username	$module_name ($module_type.)	$date_accessed	$use_type	$location\n";
			
			$rs->MoveNext();
			 
			}

			
   }
		
		
  
?>
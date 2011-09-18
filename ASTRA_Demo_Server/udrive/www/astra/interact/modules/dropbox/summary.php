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
* View dropbox summary
*
* Displays a summary of dropbox info as a spreadsheet 
*
* @package Dropbox
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: summary.php,v 1.13 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/dropbox_strings.inc.php');

//set variables
$module_key	= $_GET['module_key'];
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);


check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$page_details = get_page_details($space_key,$link_key);
$is_admin = check_module_edit_rights($module_key);

if ($is_admin!=true) {

	$message = urlencode($general_strings['access_denied']);
	header("Location:{$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	exit;
	
}

header('Content-Type: application/vnd.ms-excel');
header("Content-disposition: inline; filename=summary.xls");
?>
<table width="100%" border="1" cellspacing="1" cellpadding="0">
	<tr>
		<td colspan="5">Dropbox Summary Information for <?php echo $page_details['module_name'].' in '.$page_details['space_short_name'].' - '.$page_details['space_name']?></td>
	</tr>
	<tr>
		<td><strong>name</strong></td>
 		<td><strong>description</strong></td>
		<td><strong>Date Added</strong></td>
		<td><strong>status</strong></td>
		<td><strong>comments</strong></td>
		<td><strong>filename</strong></td>
	</tr>
<?php

 $sql = "SELECT description,{$CONFIG['DB_PREFIX']}dropbox_file_status.name,CONCAT(first_name,' ',last_name),{$CONFIG['DB_PREFIX']}dropbox_files.date_added,comments, {$CONFIG['DB_PREFIX']}dropbox_files.filename FROM {$CONFIG['DB_PREFIX']}dropbox_files,{$CONFIG['DB_PREFIX']}dropbox_settings,{$CONFIG['DB_PREFIX']}dropbox_file_status,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}dropbox_settings.module_key={$CONFIG['DB_PREFIX']}dropbox_files.module_key AND  {$CONFIG['DB_PREFIX']}dropbox_files.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}dropbox_files.status={$CONFIG['DB_PREFIX']}dropbox_file_status.status_key AND ({$CONFIG['DB_PREFIX']}dropbox_files.module_key='$module_key') ORDER BY last_name, {$CONFIG['DB_PREFIX']}dropbox_files.date_added desc";

$rs = $CONN->Execute($sql);

if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}

while (!$rs->EOF) {
	
	$description = $rs->fields[0];
	$status = $rs->fields[1];
	$username = $rs->fields[2];
	$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[3]),'short', true);	
	$comments = $rs->fields[4];
	$file_name = $rs->fields[5];
	
	echo "<tr><td>$username</td><td>$description</td><td>$date_added</td><td>$status</td><td>$comments</td><td>$file_name</td></tr>";
	
	$rs->MoveNext();
	
}

  
?>


</table>

<?php
$CONN->Close();
exit;

?>
?>

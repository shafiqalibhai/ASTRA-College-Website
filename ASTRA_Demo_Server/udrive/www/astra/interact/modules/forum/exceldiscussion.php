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
* Forum as spreadsheet
*
* Displays all forum postings in a spreadsheet
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: exceldiscussion.php,v 1.17 2007/07/30 01:56:59 glendavies Exp $
* 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$access_levels = authenticate();

//check we have the required variables
check_variables(true,false,true);

$is_admin = check_module_edit_rights($module_key);

if ($is_admin==false) {

	$message = urlencode($forum_string['not_admin']);
	header("Location: {$CONFIG['FULL_URL']}/spaces/index.php?space_key=$space_key&message=$message");
	exit;
	
}



header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="exceldiscussion.xls";'); 
echo '<table border="1"><tr>';
echo "<td>{$general_strings['first_name']}</td><td>{$general_strings['first_name']}</td><td>{$forum_strings['thread_no']}</td><td>{$forum_strings['post_no']}</td><td>{$forum_strings['parent_no']}</td><td>{$general_strings['subject']}</td><td>{$forum_strings['message']}</td><td>{$general_strings['date_added']}</td>
</tr>";


$sql = "SELECT {$CONFIG['DB_PREFIX']}users.first_name,{$CONFIG['DB_PREFIX']}users.last_name,{$CONFIG['DB_PREFIX']}posts.thread_key,{$CONFIG['DB_PREFIX']}posts.post_key,{$CONFIG['DB_PREFIX']}posts.parent_key,{$CONFIG['DB_PREFIX']}posts.subject,{$CONFIG['DB_PREFIX']}posts.body,{$CONFIG['DB_PREFIX']}posts.date_added from {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users where {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}posts.added_by_key and (module_key='$module_key') order by thread_key,parent_key";
$rs=$CONN->Execute($sql);
echo $CONN->ErrorMsg();


if ($rs->EOF) {

		
	echo '<tr><td colspan="5">'.$forum_strings['no_posts'].'</td></tr></table>';
		
		
} else {

	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}

		$objDates = new InteractDate();

	}
	
	while (!$rs->EOF) {
	
		$first_name = $rs->fields[0];
		$last_name = $rs->fields[1];
		$thread_key = $rs->fields[2];
		$post_key = $rs->fields[3];
		$parent_key = $rs->fields[4];
		$subject = $rs->fields[5];
		$body = $rs->fields[6];
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[7]),'short', true);

			
		echo "<tr><td>$first_name</td><td>$last_name</td><td>$thread_key</td><td>$post_key</td><td>$parent_key</td><td>$subject</td><td>$body</td><td>$date_added</td>
			</tr>";
			
		$rs->MoveNext();
			 
	} 
	
	echo '</table>';
			
}
  
?>
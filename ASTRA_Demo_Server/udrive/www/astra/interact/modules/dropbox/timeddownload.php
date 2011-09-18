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
* Timeddownload
*
* Downloads a file from timed download box 
*
* @package Dropbox
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: timeddownload.php,v 1.6 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');



	
$module_key	= isset($_GET['module_key']) ? $_GET['module_key'] : '';
$space_key	= isset($_GET['space_key']) ? $_GET['space_key'] : '';
$link_key	= isset($_GET['link_key']) ? $_GET['link_key'] : '';
$current_user_key	= isset($_SESSION['current_user_key']) ? $_SESSION['current_user_key'] : '';

$sql = "SELECT file_path, download_file FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'";	
	
$rs = $CONN->Execute($sql);
	
while (!$rs->EOF) {

	$file_path	 = $rs->fields[0];
	$download_file = $rs->fields[1];
	$rs->MoveNext();
	
}

$full_file__path=$CONFIG['MODULE_FILE_SAVE_PATH']."/dropbox/".$file_path.'/'.$download_file;
$string = get_random_string(5);
$new_file_name = $string.$download_file;

//Check if already downloaded, if not add entry to download table

$sql = "SELECT user_key FROM {$CONFIG['DB_PREFIX']}dropbox_download_links WHERE user_key='$current_user_key' AND module_key='$module_key'";

$rs = $CONN->Execute($sql);

if ($rs->EOF) {

	$time_downloaded = $CONN->DBDate(date('Y-m-d H:i:s'));
	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}dropbox_download_links(module_key, user_key, TimeDownLoaded, filename) VALUES ('$module_key','$current_user_key',$time_downloaded,'$new_file_name')");


}


header('Content-Type: application/octetstream');
header('Accept-Ranges: bytes');
header('Content-Length: '.filesize($full_file__path));
header('Content-Disposition: attachment; filename="'.$new_file_name.'";'); 		
header('Connection: close');

$fp = fopen($full_file__path,'r');
fpassthru($fp);
fclose($fp);

header("Location: {$CONFIG['FULL_URL']}");

function get_random_string($length = 6)
{

  // start with a blank password
  $random_string = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOP"; 
	
  // set up a counter
  $i = 0; 
	
  // add random characters to $password until $length is reached
  while ($i < $length) { 

	// pick a random character from the possible ones
	$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		
	// we don't want this character if it's already in the password
	if (!strstr($random_string, $char)) { 
	  $random_string .= $char;
	  $i++;
	}

  }

  // done!
  return $random_string;

}


?>

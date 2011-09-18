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
* Assign membership
*
* Assigns membership for given username to selected sites
*
* @package Admin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: spaceadminset.php,v 1.17 2007/01/08 20:32:57 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');



$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"	  => "header.ihtml",
	"navigation"  => "admin/adminnavigation.ihtml",
	"form"		=> "admin/spaceadminset.ihtml",
	"footer"	  => "footer.ihtml"));

if ($_SERVER['REQUEST_METHOD']=='POST') {

			  
	$users_name	= $_POST['users_name'];
	$space_keys	= $_POST['space_keys'];
	$level_key	= $_POST['level_key'];	
	$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));		
	
	$sql = "SELECT user_key, first_name, last_name FROM {$CONFIG['DB_PREFIX']}users WHERE username='$users_name'";
	$rs = $CONN->Execute($sql);
	if ($rs->EOF) {
		$message="There is no user with that User name!";
	} else {
		while (!$rs->EOF) {
			$user_key = $rs->fields[0];
			$full_name = $rs->fields[1].' '.$rs->fields[2];
			$rs->MoveNext();
		}
		$rs->Close();
		if (isset($space_keys) && is_array($space_keys)) {
			foreach ($space_keys as $space_key) {
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$user_key'");
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key,user_key,access_level_key, date_added) VALUES ('$space_key','$user_key','$level_key', $date_added)");

			}
			$message = "Done!";
		} else {
			$message = "You did not select any {$general_strings['space_plural']}";
		} 
	}
}
 

set_common_admin_vars('Assign Membership', $message);
require_once('../../spaceadmin/lib.inc.php');
$objSpaceAdmin = new InteractSpaceAdmin();
$space_menu = $objSpaceAdmin->getSpaceParentMenu('0');
$t->set_var("SPACE_MENU",$space_menu);

$t->parse("CONTENTS", "header", true); 
admin_navigation();
$t->parse("CONTENTS", "form", true);
$t->parse("CONTENTS", "footer", true);
$t->p("CONTENTS");

exit;


?>
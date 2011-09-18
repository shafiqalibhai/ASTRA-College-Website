<?php
// +----------------------------------------------------------------------+
// | memberlookup.php  1.0												|
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education				  |
// +----------------------------------------------------------------------+
// | This file is part of Interact.									   |
// |																	  | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)							 |
// |																	  | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 |
// | General Public License for more details.							 |
// |																	  | 
// | You should have received a copy of the GNU General Public License	|
// | along with this program; if not, you can view it at				  |
// | http://www.opensource.org/licenses/gpl-license.php				   |
// |																	  |
// |																	  |
// | Show site membership details of a selected user					  |
// |																	  |
// |																	  |
// |																	  |
// |																	  |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz					  |
// | Last Modified 11/12/01											   |
// +----------------------------------------------------------------------+
require_once('../../local/config.inc.php');
require_once('../../includes/category.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

   
 if ($_SERVER['REQUEST_METHOD']=='POST') {

	$submit	 = $_POST['submit'];
	$user_key   = $_POST['user_key'];	
	$space_keys = $_POST['space_keys'];
	$users_name = $_POST['users_name'];		
	
	switch ($submit) {
	
		case "Remove Membership":
		
			if ($user_key && $space_keys) {
				
				$num_selected = count($space_keys);
			
				if ($num_selected) {
				
					for ($c=0; $c < $num_selected; $c++) {
				
						$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_keys[$c]' AND user_key='$user_key'";
					
						$CONN->Execute("$sql");
					}
			
				}
	
			}
		
		break;
		
		case "Ordinary Member":
		
			if ($user_key && $space_keys) {
			
				$num_selected = count($space_keys);
			
				if ($num_selected) {
					
					for ($c=0; $c < $num_selected; $c++) {
				
						$sql = "UPDATE {$CONFIG['DB_PREFIX']}space_user_links SET access_level_key='2' WHERE   space_key='$space_keys[$c]' AND user_key='$user_key'";
					
						$CONN->Execute("$sql");
					
					}
			
				}
	
			}
		
		break;	

		case "Lecturer/Admin":
		
			if ($user_key && $space_keys) {
			
				$num_selected = count($space_keys);
				
				if ($num_selected) {
				
					for ($c=0; $c < $num_selected; $c++) {
				
						$sql = "UPDATE {$CONFIG['DB_PREFIX']}space_user_links SET access_level_key='1' WHERE   space_key='$space_keys[$c]' AND user_key='$user_key'";
					
						$CONN->Execute("$sql");
					}
			
				}
	
			}
		
		break;
		
		case "Invisible Admin":
		
			if ($user_key && $space_keys) {
			
				$num_selected = count($space_keys);
			
				if ($num_selected) {
				
					for ($c=0; $c < $num_selected; $c++) {
				
						$sql = "UPDATE {$CONFIG['DB_PREFIX']}space_user_links SET access_level_key='3' WHERE   space_key='$space_keys[$c]' AND user_key='$user_key'";
					
						$CONN->Execute("$sql");
				
					}
			
				}
	
			}
		
		break;
		
		case "Invisible Guest":
		
			if ($user_key && $space_keys) {
				
				$num_selected = count($space_keys);
			
				if ($num_selected) {
				
					for ($c=0; $c < $num_selected; $c++) {
				
						$sql = "UPDATE {$CONFIG['DB_PREFIX']}space_user_links SET access_level_key='4' WHERE   space_key='$space_keys[$c]' AND user_key='$user_key'";
					
						$CONN->Execute("$sql");
					}
				
				}
	
			}
		
			break;						
	
		}	


		$sql = "select {$CONFIG['DB_PREFIX']}users.user_key,first_name,last_name,{$CONFIG['DB_PREFIX']}space_user_links.access_level_key,{$CONFIG['DB_PREFIX']}spaces.name,{$CONFIG['DB_PREFIX']}spaces.space_key,{$CONFIG['DB_PREFIX']}spaces.code from {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND (username='$users_name')";
		
		$rs = $CONN->Execute($sql);
 	
		if ($rs->EOF) {
		
			$message = "Either that user doesn't exist, or they are not a member of any ".$general_strings['space_text'].'.';
	
		} else { 
		
			while (!$rs->EOF) {
			
				$user_key=$rs->fields[0];
				$name=$rs->fields[1].' '.$rs->fields[2];
				$access_level_key=$rs->fields[3];
			
				switch($access_level_key) {
				
				case 1:
					$access_level = "Lecturer";
					break;
				case 2:
					$access_level = "Member";
					break;
				case 3:
					$access_level = "Invisible Admin";
					break;
				case 4:
					$access_level = "Invisible Guest";
					break;				
			}						
			$space_name=$rs->fields[4]; 
			$space_key=$rs->fields[5]; 
			$space_short_name=$rs->fields[6]; 
			$results .= "<tr><td><input name=\"space_keys[]\" type=\"checkbox\" value=\"$space_key\"></td><td>$space_short_name - $space_name</td><td>$access_level</td></tr>";
   			$rs->MoveNext();
		}
	}
		
}



$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header" => "header.ihtml",
	  "navigation" => "admin/adminnavigation.ihtml",
	"form" => "admin/memberlookup.ihtml",
	"footer" => "footer.ihtml"));

set_common_admin_vars("User Lookup", $message);
$t->set_var("RESULTS","$results");
$t->set_var("NAME","$name");
$t->set_var("USER_KEY","$user_key");
$t->set_var("USER_NAME","$users_name");
$t->parse("CONTENTS", "header", true); 
admin_navigation();
if (!$submit || !$results) {
	$t->set_block('form', 'ResultBlock', 'RBlock');
	$t->set_var('RBlock', '');
}
//now get usergroups

$rs = $CONN->Execute("SELECT group_name FROM {$CONFIG['DB_PREFIX']}user_groups, {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE  {$CONFIG['DB_PREFIX']}user_groups.user_group_key={$CONFIG['DB_PREFIX']}user_usergroup_links.user_group_key AND {$CONFIG['DB_PREFIX']}user_usergroup_links.user_key='$user_key'");

while (!$rs->EOF) {

	$groups .= $rs->fields[0].'<br />';
	$rs->MoveNext();

}
$t->set_var("USER_GROUPS","$groups");
$t->parse("CONTENTS", "form", true);
$t->parse("CONTENTS", "footer", true);
$t->p("CONTENTS");

exit;


?>
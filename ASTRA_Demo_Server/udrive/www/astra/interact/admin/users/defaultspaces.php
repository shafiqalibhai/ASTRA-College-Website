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
* Default Spaces
*
* Set default spaces for specific user groups
*
* @package UserAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: defaultspaces.php,v 1.21 2007/04/24 04:03:24 glendavies Exp $
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

if (isset($_POST['action']) || isset($_GET['action'])) {

	if ($_POST['action']) {
	
		$action		 = $_POST['action'];
		$user_group_key = $_POST['user_group_key'];		
		
	} else {
	
		$action		 = $_GET['action'];	
		$user_group_key = $_GET['user_group_key'];		

	}
	
	switch($action) {

		
		case display:
		
			$sql = "SELECT group_name FROM {$CONFIG['DB_PREFIX']}user_groups where user_group_key='$user_group_key'";
			$rs=$CONN->Execute($sql);
			
			while (!$rs->EOF) {
				
				$group_name = $rs->fields[0];
				$rs->MoveNext();
			
			}
			
			$t = new Template($CONFIG['TEMPLATES_PATH']);  
			$t->set_file(array(
				'header'	  => 'header.ihtml',
				'navigation'  => 'admin/adminnavigation.ihtml',
				'form'		=> 'admin/defaultspaces2.ihtml',
				'list'		=> 'admin/defaultspaces3.ihtml',
 				'footer'	  => 'footer.ihtml'));
				
			set_common_admin_vars('Set default '.$general_strings['space_plural'].' for a user group', $message);
			$t->set_var('USER_GROUP',$group_name);
			
			$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}spaces.space_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}default_space_user_links WHERE {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}default_space_user_links.space_key AND user_group_key='$user_group_key'";
			
			$rs=$CONN->Execute($sql);
			
			while (!$rs->EOF) {
				
				$space_key = $rs->fields[0];
				$space_name = $rs->fields[1];
				$t->set_var('SPACE_KEY',$space_key);
				$t->set_var('SPACE_NAME',$space_name);
				$t->set_var('USER_GROUP_KEY',$user_group_key);
				$t->parse('DEFAULT_SPACES', 'list', true);
				$rs->MoveNext();
			
			}
			
			
			$t->set_var('SPACE_KEY',$space_key);
			$t->set_var('USER_GROUP_KEY',$user_group_key);
			$t->parse('CONTENTS', 'header', true); 
			admin_navigation();
			$t->parse('CONTENTS', 'form', true);
			$t->parse('CONTENTS', 'footer', true);
			$t->p('CONTENTS');
			exit;
			break;
   
		case add:

			$user_group_key = $_POST['user_group_key'];
			$permanent	  = $_POST['permanent'];
			$space_code	 = $_POST['space_code'];
									
			$sql = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$space_code'";
			$rs=$CONN->Execute($sql);
			
			if ($rs->EOF) {
			
				header("Location: {$CONFIG['FULL_URL']}/admin/users/defaultspaces.php?action=display&user_group_key=$user_group_key&message=There+is+no+{$general_strings['space_text']}+with+that+{$general_strings['space_text']}+code");
			
			} else {
			
				while (!$rs->EOF) {
			
					$space_key=$rs->fields[0];
					$rs->MoveNext();
			
				}
			
				$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}default_space_user_links VALUES ('$user_group_key','$space_key','$permanent')";
				$CONN->Execute($sql);
				
				//now add any existing users to this space
				
				$sql = "SELECT {$CONFIG['DB_PREFIX']}user_usergroup_links.user_key FROM {$CONFIG['DB_PREFIX']}user_usergroup_links, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}user_usergroup_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}users.account_status='1' AND {$CONFIG['DB_PREFIX']}user_usergroup_links.user_group_key='$user_group_key'";
				
				$rs = $CONN->Execute($sql);
				
				while(!$rs->EOF) {
				
					$user_key = $rs->fields[0];
					$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key) VALUES ('$space_key','$user_key','2')");
					$rs->MoveNext();
					
				}
				
				header("Location: {$CONFIG['FULL_URL']}/admin/users/defaultspaces.php?action=display&user_group_key=$user_group_key&message=The+{$general_strings['space_text']}+has+been+added");
				exit;
			
			}
			
		break;
		
		case delete:

			$user_group_key = $_POST['user_group_key'];
			$permanent	  = $_POST['permanet'];
			$space_key	  = $_POST['space_key'];
			
			$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}default_space_user_links WHERE space_key='$space_key' and user_group_key='$user_group_key'";
			$CONN->Execute($sql);
			
			if (isset($_POST['remove_membership_'.$space_key ]) && $_POST['remove_membership_'.$space_key ]==1) {
			
				$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_group_key='$user_group_key'");
			
				while (!$rs->EOF) {
				
					$user_key = $rs->fields[0];
					
					 $CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE user_key='$user_key' AND space_key='$space_key'");
					$rs->MoveNext();
				
				}
			
			
			}
			header("Location: {$CONFIG['FULL_URL']}/admin/users/defaultspaces.php?action=display&user_group_key=$user_group_key&message=The+{$general_strings['space_text']}+has+been+deleted");
			exit;
			break;
	}

}

$group_sql = "SELECT group_name, user_group_key FROM {$CONFIG['DB_PREFIX']}user_groups ORDER BY group_name";
$group_menu = make_menu($group_sql,'user_group_key',$user_group_key,'4');

$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'	  => 'header.ihtml',
	'navigation'  => 'admin/adminnavigation.ihtml',
	'form'		=> 'admin/defaultspaces1.ihtml',
 	'footer'	  => 'footer.ihtml'));
	

set_common_admin_vars('Set default '.$general_strings['space_plural'].' for a user group', $message);

$t->set_var('USER_GROUP_MENU',$group_menu);
$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

exit;

?>
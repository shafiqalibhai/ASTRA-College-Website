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
* Group Access Code Page
*
* If a group is restricted and user is not a member, then this page
* requests an access code. If access code correct then user_key is
* entered in the GroupUserlinks table and user is forwarded to the group
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: access.php,v 1.9 2007/07/30 01:57:01 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/group_strings.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$space_key  = $_GET['space_key'];
	$group_key  = $_GET['group_key'];
	$module_key = $group_key;
	
} else {
	
	$group_key   = $_POST['group_key'];
	$space_key   = $_POST['space_key'];
	$action		 = $_POST['action'];
	$access_code = $_POST['access_code'];
	
}
$current_user_key = $_SESSION['current_user_key'];
$link_key 	= get_link_key($group_key,$space_key);
//check we have the required variables

check_variables(true,false);

$group_access=array();
$group_accesslevel=array();
$n=1;
$sql = "select group_key,access_level_key from {$CONFIG['DB_PREFIX']}group_user_links where user_key='{$_SESSION['current_user_key']}'";
$rs = $CONN->Execute($sql);

while (!$rs->EOF) {
	
	$group_key2 = $rs->fields[0];
	$group_accesslevel_key = $rs->fields[1];
	$group_access[$n]=$group_key2;
	$group_accesslevel[$group_key2] = $group_accesslevel_key;
	$n++;
	$rs->MoveNext();
	
}

if (!class_exists(InteractGroup)) {
			
	require_once('lib.inc.php');
				
}
			
if (!is_object($groupObject)) {
			
	$groupObject = new InteractGroup();
			
}
$group_data = $groupObject->getGroupData($group_key);

if ($action=='add') {

	$true_access_code = $group_data['access_code'];
	
	if (strtoupper($true_access_code)!=strtoupper($access_code)) {
	
		$message = $group_strings['access_code_fail'];
		
	} else {
	
		$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}group_user_links VALUES ('$group_key','$current_user_key','2',$date_added)";
		$CONN->Execute($sql);
		$CONN->Close();
		header("Location: {$CONFIG['FULL_URL']}/modules/group/group.php?space_key=$space_key&module_key=$group_key");
		exit;
	
	} 
	   
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'accessform' => 'groups/access.ihtml',
	'footer'	 => 'footer.ihtml'
	
));

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$sql = "SELECT first_name, last_name, email, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key' AND {$CONFIG['DB_PREFIX']}group_user_links.access_level_key='1'";

$rs = $CONN->Execute($sql);

if ($rs->EOF) {

	$site_admins = sprintf($group_strings['no_admins'], $general_strings['space_text'], $general_strings['space_text']);
	
} else {

	while (!$rs->EOF) {

		$name		 = $rs->fields[0].' '.$rs->fields[1];
		$url_name	 = urlencode($rs->fields[0].' '.$rs->fields[1]);
		$email		= $rs->fields[2];
		$user_key	 = $rs->fields[3];		
		$site_admins .= "<li>$name <a href=\"../../spaces/emailuser.php?space_key=$space_key&email_user_key=$user_key&email_username=$url_name\">{$general_strings['email']}</a></li>";
		$rs->MoveNext();
	
	}
	
}
$rs->Close();

if ($group_data['access_key']==1) {

	$t->set_var('INSTRUCTION_STRING2',$group_strings['access_instruction3']);
	$t->set_block('accessform', 'access_codeBlock', 'ACBlock');
	$t->set_var('ACBlock','');	

} else {

	$t->set_var('INSTRUCTION_STRING2',$group_strings['access_instruction2']);
	$t->set_var('INSTRUCTION_STRING',$group_strings['access_instruction']);

}
$t->set_var('PAGE_TITLE',$group_strings['group_restricted']);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('SITE_ADMINS',$site_admins);
$t->set_var('ACCESS_CODE_STRING',$group_strings['access_code']);
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('HEADING_STRING',$group_strings['group_restricted']);
$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->parse('CONTENTS', 'accessform', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
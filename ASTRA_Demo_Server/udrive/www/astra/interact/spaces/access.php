<?php
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
// | access.php - allows users to enter access code to gain access to	 |
// | restricted spaces													|
// +----------------------------------------------------------------------+
/**
* Space Access Code Page
*
* If space is restricted and user is not a member, then this page
* requests an access code. If access code correct then user_key is
* entered in the space_user_links table and user is forwarded to the space
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: access.php,v 1.25 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$space_key  = $_GET['space_key'];

	
} else {
	
	$space_key   = $_POST['space_key'];
	$action		 = $_POST['action'];
	$access_code = $_POST['access_code'];
	
}
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables

check_variables(true,false);

if ($action=='add') {

	$sql = "SELECT access_code,new_user_alert FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'";
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$true_access_code=$rs->fields[0];
		$new_user_alert=$rs->fields[1];
		$rs->MoveNext();
	}
	
	$rs->Close();
	
	if (strtoupper($true_access_code)!=strtoupper($access_code)) {
	
		$message = $space_strings['access_code_fail'];
		
	} else {
	
		$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
		$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key,user_key, access_level_key, date_added) VALUES ('$space_key','$current_user_key','2', $date_added)";
		$CONN->Execute($sql);
		echo $CONN->ErrorMsg();
		//if new user alert true get course lecturers and notify them that a new student has accessed site
/*
		if ($new_user_alert=='true') {
			
			$sql = "SELECT first_name, last_name, details FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$current_user_key'";
			$rs = $CONN->Execute($sql);
			
			while (!$rs->EOF) {
				
				$name = $rs->fields[0].' '.$rs->fields[1];
				$details = ereg_replace( 10, "\n", $rs->fields[2]);
				$rs->MoveNext();
				
			}
			
			$rs->Close();
	   
			$member_keys = array();
			$n = 0;
			
			$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}spaces.name FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links,{$CONFIG['DB_PREFIX']}spaces WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND ({$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1' AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key')";

			$rs = $CONN->Execute($sql);
			
			while (!$rs->EOF) {
			
				$member_keys[$n] = $rs->fields[0];
				$space_name = $rs->fields[1];
				$n++;
				$rs->MoveNext();
				
			}
			
			$mailbody = sprintf($space_strings['new_user_alert'], $name, $space_name, $general_strings['space_text']);
			$mailbody .= "\n\n";
			$mailbody .= sprintf($space_strings['details'], $name);
				
			if ($details!='') {
					
				$mailbody = $mailbody."\n$details";
				
			} else {
			   
				$mailbody = $mailbody."\n".$space_strings['no_details'];
				
			}
			
			require_once('../includes/email.inc.php');
			$subject = sprintf($space_strings['new_member'], $space_name);
			
			email_users($subject, $mailbody, $member_keys, '', '', '');
			
			 
		}
		*/
		$rs->Close();
		$CONN->Close();
		header("Location: {$CONFIG['FULL_URL']}/spaces/welcome.php?space_key=$space_key");
		exit;
	
	} 
	   
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'accessform' => 'spaces/access.ihtml',
	'footer'	 => 'footer.ihtml'
	
));

$sql = "SELECT name, description, access_code FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'";
$rs = $CONN->Execute($sql);
$space_name=$rs->fields[0];
$description=$rs->fields[1];
$access_code=$rs->fields[2];
$rs->Close();

$home_access = $CONN->GetOne("SELECT {$CONFIG['DB_PREFIX']}spaces.access_level_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key={$CONFIG['DEFAULT_SPACE_KEY']}");

$email_page=($home_access==3 || $home_access==1);

$sql = "SELECT email,first_name,last_name,{$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1'";

$rs = $CONN->Execute($sql);
if ($rs->EOF) {
	$site_admins = sprintf($space_strings['no_admins'], $general_strings['space_text'], $general_strings['space_text']);
	
} else {

	while (!$rs->EOF) {
		$site_admins .= '<li>';
		$aname="{$rs->fields[1]} {$rs->fields[2]}";
		$site_admins .= ($email_page || $CONFIG['SHOW_EMAILS']==1)?
			('<a href="'.($email_page?
				"{$CONFIG['PATH']}/spaces/emailuser.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}&email_user_key={$rs->fields[3]}":
				
				"mailto:{$rs->fields[0]}").
			"\">$aname &mdash; {$general_strings['email']}</a>"):
			$aname;

		$rs->MoveNext();
	}
}

$rs->Close();
$page_details = get_page_details('','');
set_common_template_vars('','',$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('PAGE_TITLE','No Access at Present');
$t->set_var('SPACE_KEY',$space_key);

$t->set_var('SITE_ADMINS',$site_admins);
$t->set_var('DESCRIPTION',$description);


if (empty($access_code) || empty($_SESSION['current_user_key'])) {
	$t->set_block('accessform', 'AccessCodeBlock', 'AccssCdeBlock');
} else {
	$t->set_var('INSTRUCTION_STRING', $space_strings['access_instruction']);
	$t->set_var('ACCESS_CODE_STRING',$space_strings['access_code']);
	$t->set_var('SUBMIT_STRING',$general_strings['submit']);
	$t->set_var('CANCEL_STRING',$general_strings['cancel']);
	$t->set_var('INSTRUCTION_STRING2',sprintf($space_strings['access_instruction2'], $general_strings['space_text'],$general_strings['space_text']));
}
$t->set_var('SPACE_ADMINS_STRING',$general_strings['space_admin_text']);
$t->set_var('HEADING_STRING',sprintf($space_strings['access_heading'], $space_name));
$t->parse('CONTENTS', 'header', true); 

$t->parse('CONTENTS', 'accessform', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
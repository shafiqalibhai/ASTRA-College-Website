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
* link input
*
* Displays a link input page. 
*
* @package Journal
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: linkinput.php,v 1.10 2007/07/18 05:17:44 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php');



//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key		= $_GET['module_key'];
	$action			= $_GET['action'];
	$journal_link_key 		= $_GET['journal_link_key'];
	$journal_user_key 	= $_GET['journal_user_key'];
	$type_key   		= $_GET['type_key'];	
		
		
} else {

	$journal_user_key 	= $_POST['journal_user_key'];
	$module_key			= $_POST['module_key'];
	$journal_link_key   = $_POST['journal_link_key'];
	$type_key   		= $_POST['type_key'];	
	$link_name  		= strip_tags($_POST['link_name']);
	$link_url  			= strip_tags($_POST['link_url']);
	$action				= $_POST['action'];
	$journal_user_key 	= $_POST['journal_user_key'];

}

$userlevel_key = $_SESSION['userlevel_key'];
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
//check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
$current_user_key = $_SESSION['current_user_key'];

if ($is_admin!=true && $current_user_key!=$journal_user_key) {

	$message = urlencode($general_strings['no_edit_rights']);
	header("Location: {$CONFIG['FULL_URL']}/modules/journal/journal.php?space_key=$space_key&module_key=$module_key&message=$message");
	exit;

}
require_once('lib.inc.php');
$journal = new InteractJournal($space_key, $module_key, $group_key, $is_admin, $journal_strings);
$journal->setJournalSettings();
$journal->setJournaluser_key($journal_user_key);
if (isset($action) && $action!='') {

	switch ($action) {
	
		case 'add':
		
			$errors = $journal->checkFormlink($link_name, $link_url);
			
			if (count($errors)==0) {
			
				$journal_link_key = $journal->addlink($link_name, $link_url, $type_key);
				
				if (isset($journal_link_key)) {
				
					$message 		= 'Your link has been added';
					$journal_link_key 	= '';
					$link_name	= '';
					$link_url		= '';					

				} 
				
			}
		
		break;
		
		case 'modify':

			$link_data 	= $journal->getlinkData($journal_link_key);
			$link_name 	= $link_data['name'];
			$link_url 	= $link_data['url'];
			
					
		break;
		
		case 'modify2':

			switch($_POST['submit']) {
			
				case $general_strings['modify']:
			
					$errors = $journal->checkFormlink($link_name, $link_url);
			
					if (count($errors)==0) {
			
						$message = $journal->modifylink($journal_link_key, $link_name, $link_url);
				
						if ($message===true) {
				
							$message		= 'Your link has been modified';
							$action			= '';
							$link_name	= '';
							$link_url	= '';
							$journal_link_key 	= '';
					
						} 
				
					}
					
				break;
				
				case $general_strings['delete']:
				
					$message = $journal->deletelink($journal_link_key);
				
					if ($message===true) {
				
						$message		= 'Your link has been deleted';
						$action			= '';
						$link_name	= '';
						$link_url 	= '';
						$journal_link_key 	= '';						
						
					}
					
				
				break;
				
			} //end switch($_POST['submit'])
		
		break;		
		
	} //end switch(action)
		
} //end if ($action)

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'body'	   => 'journal/linkinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!$action | $action=='add') {

	$button		= $general_strings['add'];
	$input_heading = $journal_strings['add_link'];
	$action		= 'add';
	$t->set_block('body', 'ModifyLinkBlock2', 'ML2Block');
	$t->set_var('ML2Block');			
	
} else {

	$button		= $general_strings['modify'];
	$input_heading =  $journal_strings['modify_link'];
	$t->set_var('WARNING',$general_strings['delete_warning']);
	$t->set_block('body', 'AddLinkBlock', 'ALBlock');
	$t->set_var('ALBlock');	
	$t->set_block('body', 'ModifyLinkBlock', 'MLBlock');
	$t->set_var('MLBlock');	

}

if($CONN->GetOne("SELECT link_key FROM {$CONFIG['DB_PREFIX']}journal_links WHERE journal_user_key='$journal_user_key' AND module_key='$module_key'")) {
	$link_sql  = "SELECT name, link_key FROM {$CONFIG['DB_PREFIX']}journal_links WHERE journal_user_key='$journal_user_key' AND module_key='$module_key'";
	$links_menu  = make_menu($link_sql,'journal_link_key',$journal_link_key, 5,false,  false);
} else {
	$links_menu = '';
	$t->set_block('body', 'ModifyLinkBlock', 'MLBlock');
	$t->set_var('MLBlock');	
}

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('JOURNAL_USER_KEY',$journal_user_key);
$t->set_var('LINKS_MENU',$links_menu);
$t->set_var('JOURNAL_LINK_KEY',isset($journal_link_key)? $journal_link_key : '');
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('INPUT_HEADING',$input_heading);
$t->set_var('LINK_NAME',isset($link_name) ? $link_name : '');
$t->set_var('LINK_URL',isset($link_url) ? $link_url : '');
$t->set_var('ACTION',$action);
$t->set_var('BUTTON',$button);
$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->set_strings('body',  $journal_strings, '', $errors);
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>
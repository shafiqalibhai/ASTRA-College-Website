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
* Journal print view page
*
* Displays a journals entries in a format suitable for printing
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: printview.php,v 1.21 2007/07/30 01:57:02 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key			= $_GET['module_key'];
	$group_key			= $_GET['group_key'];
	$journal_user_key   = $_GET['journal_user_key'];
	$view			   = $_GET['view'];
	
} else {

	$module_key			= $_POST['module_key'];
	$group_key			= $_POST['group_key'];
	$journal_user_key   = $_POST['journal_user_key'];
	$view			   = $_POST['view'];		
	
}

$userlevel_key	  = $_SESSION['userlevel_key'];


$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

$journal = new InteractJournal($space_key, $module_key, $group_key, $is_admin, $journal_strings);
$journal->setJournalSettings();

//if user is not and admin, and journal is closed only show current users 
//entries

if($journal->checkShowAll()==false || $journal_user_key=='') {

	$journal_user_key = $_SESSION['current_user_key']; 

}
   
//create an InteractUser class so we can retrieve user details
if (!class_exists('InteractUser')) {

	require_once('../../includes/lib/user.inc.php');
	
}

$user = new InteractUser();
$user_data = $user->getUserData($journal_user_key);


$journal->setJournaluser_key($journal_user_key);
$journal->countJournalEntries();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "printheader.ihtml",
	"journal"		 => "journal/printview.ihtml",
	"footer"		  => "footer2.ihtml"
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

//set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('DATE',$dates->formatDate(time(),'short', true));
$t->set_var('JOURNAL_USER_KEY',$journal_user_key);

$t->set_var('HEADING_STRING',sprintf($journal_strings['view_journal_heading'], $page_details['module_name'], $user_data['first_name'].' '.$user_data['last_name']));

$t->set_var('NAME',$user_data['first_name'].' '.$user_data['last_name']);
$t->parse('CONTENTS', 'header', true); 


if ($journal->countJournalEntries()==0) {

	if (!$user_data) {
	
		$message = $journal_strings['no_entries_individual'];
		
	} else {
	
		$message = sprintf($journal_strings['no_entries'],$user_data['first_name'].' '.$user_data['last_name']); 
	
   }
	
} else {

	$journal->displayJournalEntries($sort_order, $view, $view_comments);
		
}
$t->set_block('journal', 'NoPrintBlock', 'NPBlock');
$t->set_var('NPBlock','');

$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->parse('CONTENTS', 'journal', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p("CONTENTS");
$CONN->Close();	   
exit;	
?>

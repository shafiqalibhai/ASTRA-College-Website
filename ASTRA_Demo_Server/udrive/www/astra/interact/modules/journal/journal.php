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
* Journal homepage
*
* Displays a journal start page. If user is an admin, or if journal is open 
* thena list of users is displayed, otehrwise the user journal entries are
* displayed
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: journal.php,v 1.25 2007/07/30 01:57:02 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once '../../local/config.inc.php';
require_once $CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php';
require_once 'lib.inc.php';


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];
	
} else {

	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];
	
}
if (isset($_POST['show_new']) && $_POST['show_new']!=0) {
	$show_new = $_POST['show_new'];
	$last_use_seconds = $_POST['show_new']*86400;
	$last_use = date('Y-m-d H:i:s',time()-$last_use_seconds);

	//if they have not logged in before set their last login to today

} else if ($_SESSION['last_use']>0) {

	$last_use = $_SESSION['last_use'];
	
} else {

	$last_use = date('Y-m-d H:i:s');
	
}

$userlevel_key = $_SESSION['userlevel_key'];
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);
//temporary fix to get correct groupkey if not passed in from url.
$group_key = $CONN->GetOne("SELECT group_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND link_key='$link_key'");
//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
statistics('read');
$objJournal = new InteractJournal($space_key, $module_key, $group_key, $is_admin, $journal_strings);
$objJournal->setJournalSettings();
$journal_settings = $objJournal->getJournalSettings();
//if user is not and admin, and journal is closed refer to users own journal entries

if($objJournal->checkShowAll()==false || ($journal_settings['default_display']=='show_all' && $objJournal->checkShowAll()==true)) {

	header("Location: {$CONFIG['FULL_URL']}/modules/journal/journalview.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key");
	exit;

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "header.ihtml",
	"navigation"	  => "navigation.ihtml",
	"journal"		 => "journal/journal.ihtml",
	"footer"		  => "footer.ihtml"
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('INSTRUCTIONS_STRING',$journal_strings['start_instructions']);
$t->set_var('ENTRIES_STRING',$journal_strings['entries']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('ADD_MULTIPLE_STRING',$journal_strings['add_multiple']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->set_block('journal', 'MultiAddBlock', 'MABlock');
$t->set_block('journal', 'UserListHeading', 'ULHBlock');
$t->set_block('journal', 'UserListBlock', 'ULBlock'); 


if ($is_admin==true) {

	$t->Parse('MABlock', 'MultiAddBlock', true);

} else {

	$t->set_var('MABlock', '');
	
}

$user_limit = $objJournal->getUserLimit();

if ($user_limit==false) {

	$t->set_block('journal', 'UserTableHeaderBlock', 'ULTHBlock');
	$t->set_block('journal', 'UserTableFooterBlock', 'ULTFBlock');
	$t->set_var('ULHBlock', '');
	$t->set_var('ULTHBlock', '');
	$t->set_var('ULTFBlock', '');
	$t->set_var('MABlock', '');		
	$message = $journal_strings['no_users'];
	
} else {

	$now = $CONN->DBTimeStamp(time());

	$CONN->SetFetchMode('ADODB_FETCH_ASSOC');
	//first get the list of users
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}users.user_key as user_key, first_name as first_name, last_name as last_name FROM {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}users.user_key IN $user_limit ORDER BY last_name, first_name");
	if ($rs->RecordCount()==1) {
		header("Location: {$CONFIG['FULL_URL']}/modules/journal/journalview.php?space_key=$space_key&module_key=$module_key&journal_user_key={$rs->fields[0]}");
		exit;
	}
	$rs_count = $CONN->GetAssoc("SELECT user_key, COUNT(*) as total FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key=0 AND user_key IN $user_limit AND {$CONFIG['DB_PREFIX']}posts.date_published<=$now  AND {$CONFIG['DB_PREFIX']}posts.status_key='1' AND {$CONFIG['DB_PREFIX']}posts.module_key='$module_key' GROUP BY user_key ORDER BY user_key");
		$rs_comments_count = $CONN->GetAssoc("SELECT user_key, COUNT(*) as total FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key!=0 AND user_key IN $user_limit AND {$CONFIG['DB_PREFIX']}posts.date_published<=$now  AND {$CONFIG['DB_PREFIX']}posts.status_key='1' AND {$CONFIG['DB_PREFIX']}posts.module_key='$module_key' GROUP BY user_key ORDER BY user_key");
		$comment_sql = "SELECT user_key, COUNT(*) as total FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key!=0 AND user_key IN $user_limit AND {$CONFIG['DB_PREFIX']}posts.date_published<=$now  AND {$CONFIG['DB_PREFIX']}posts.status_key='1' AND {$CONFIG['DB_PREFIX']}posts.module_key='$module_key' GROUP BY user_key ORDER BY user_key";
	//now get the total number of new posts
	$rs_new = $CONN->GetAssoc("SELECT user_key, COUNT(*) as total FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key=0 AND user_key IN $user_limit AND ({$CONFIG['DB_PREFIX']}posts.date_published>=".$CONN->DBDate($last_use)." AND {$CONFIG['DB_PREFIX']}posts.date_published<=$now ) AND {$CONFIG['DB_PREFIX']}posts.status_key='1' AND {$CONFIG['DB_PREFIX']}posts.module_key='$module_key' GROUP BY user_key ORDER BY user_key");
		$rs_new_comments = $CONN->GetAssoc("SELECT user_key, COUNT(*) as total FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key!=0 AND user_key IN $user_limit AND ({$CONFIG['DB_PREFIX']}posts.date_published>=".$CONN->DBDate($last_use)." AND {$CONFIG['DB_PREFIX']}posts.date_published<=$now ) AND {$CONFIG['DB_PREFIX']}posts.status_key='1' AND {$CONFIG['DB_PREFIX']}posts.module_key='$module_key' GROUP BY user_key ORDER BY user_key");

	$CONN->SetFetchMode('ADODB_FETCH_NUM');
	
	while(!$rs->EOF) {

		if (isset($rs_new[$rs->fields['user_key']])) {
			$t->set_var('NEW_ENTRIES','('.$rs_new[$rs->fields['user_key']].' '.$journal_strings['new_entries'].')');		
		
		} else {
			$t->set_var('NEW_ENTRIES','');
		}
		if (isset($rs_new_comments[$rs->fields['user_key']])) {
			$t->set_var('NEW_COMMENTS','('.$rs_new_comments[$rs->fields['user_key']].' '.$journal_strings['new_entries'].')');		
		
		} else {
			$t->set_var('NEW_COMMENTS','');
		}
		
		
		$t->set_var('ENTRY_COUNT', (isset($rs_count[$rs->fields['user_key']])) ? $rs_count[$rs->fields['user_key']] : '0');
		$t->set_var('COMMENT_COUNT', (isset($rs_comments_count[$rs->fields['user_key']])) ? $rs_comments_count[$rs->fields['user_key']] : '0');		 
		$t->set_var('NAME',$rs->fields['first_name'].' '.$rs->fields['last_name']);
		$t->set_var('JOURNAL_USER_KEY',$rs->fields['user_key']);
		$t->Parse('ULBlock', 'UserListBlock', true);
		$rs->MoveNext();
	}
	$t->Parse('ULHBlock', 'UserListHeading', true);
 
}

//create dropdown to select days of new items to show
$show_new_menu = $objHtml->arrayToMenu(array('0' => $general_strings['since_last_login'], '1' => $general_strings['today'], '3' => sprintf($general_strings['for_last_days'],3), '7' => sprintf($general_strings['for_last_days'],7), '30' => sprintf($general_strings['for_last_days'],30)),'show_new',$show_new,'','',true,'onchange="this.form.submit();"');
$t->set_var('SHOW_NEW_MENU',$show_new_menu);
$t->set_strings('journal',  $journal_strings, '', $errors);
$t->parse('CONTENTS', 'journal', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p("CONTENTS");
$CONN->Close();	   
exit;	
?>

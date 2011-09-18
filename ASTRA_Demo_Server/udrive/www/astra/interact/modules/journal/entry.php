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
* Journal entry page
*
* Displays a single journal entry with comments
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: entry.php,v 1.38 2007/07/08 10:23:19 websterb4 Exp $
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
	$journal_user_key	= $_GET['journal_user_key'];
	$post_key			= isset($_GET['post_key']) ? $_GET['post_key'] : '';	
	$tag_key			= isset($_GET['tag_key']) ? $_GET['tag_key'] : '';
		
} else {

	$module_key			= $_POST['module_key'];
	$group_key			= $_POST['group_key'];
	$journal_user_key	= $_POST['journal_user_key'];	
	$view				= $_POST['view'];
	$sort_order			= $_POST['sort_order'];
	$post_key			= isset($_POST['post_key']) ? $_POST['post_key'] : '';
	$tag_key			= isset($_POST['tag_key']) ? $_POST['tag_key'] : '';	
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

$objJournal = new InteractJournal($space_key, $module_key, $group_key, $is_admin, $journal_strings);
$objJournal->setJournalSettings();
$journal_settings = $objJournal->getJournalSettings();
//if user is not an admin, and journal is closed only show current users 
//entries

if($objJournal->checkShowAll()==false && !empty($_SESSION['current_user_key']) && $journal_user_key!=$_SESSION['current_user_key']) {
	$journal_user_key = $_SESSION['current_user_key']; 
}
$objJournal->setJournaluser_key($journal_user_key);
//create an InteractUser class so we can retrieve user details
$objHtml = singleton::getInstance('html');
$objDate = singleton::getInstance('date');				
$objUser = singleton::getInstance('user');
$objTags = singleton::getInstance('tags');
$objPosts = singleton::getInstance('posts');
$objPosts->setVars($module_key, $space_key);
$user_data = $objUser->getUserData($journal_user_key);
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "header.ihtml",
	"navigation"	  => "navigation.ihtml",
	"journal"		 => "journal/entry.ihtml",
	"comments"		 => "modules/comments.ihtml",
	"footer"		  => "footer.ihtml"
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($is_admin!=true && $journal_user_key!=$_SESSION['current_user_key']) {
	$t->set_block('journal', 'AddBlock', 'AdEntryBlock');
}
$t->set_var('SPACE_KEY', $space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',	$link_key);
$t->set_var('JOURNAL_USER_KEY', $journal_user_key);
$t->set_var('USER_KEY',	isset($journal_user_key)?$journal_user_key:'');
$t->set_var('HEADING_STRING', $user_data['first_name'].' '.$user_data['last_name']);
$t->set_var('POSTED_BY', $general_strings['posted_by']);
$t->set_var('REPLY', $general_strings['reply']);
if ($journal_settings['default_display']=='show_all') {
	$journal_name = $journal_strings['all_members_entries'];
} else {
	$rs = $CONN->Execute($objJournal->getUserSql());
	$user_count = $rs->RecordCount();
	$journal_name = ($user_count>1)?$user_data['first_name'].' '.$user_data['last_name']:$general_strings['all_entries'];
}

$t->set_block('comments', 'DeleteBlock', 'DelBlock');
if ((empty($_SESSION['current_user_key'])&&$journal_settings['allow_comments']!='from_anyone') || $journal_settings['allow_comments']=='no') {
	$t->set_block('comments', 'CommentInputBlock', 'CommInputBlock');
	$t->set_block('comments', 'CommentlinkBlock', 'CommlinkBlock');
} else {
	if(isset($_GET['ac'])) {
		$t->set_block('comments', 'CommentlinkBlock', 'CommlinkBlock');	
		$objHtml->setTextEditor($t, false, 'body');
	} else {
		$t->set_block('comments', 'CommentInputBlock', 'CommInputBlock');
		$t->set_var('CommInputBlock', '<a href="entry.php?space_key='.$space_key.'&module_key='.$module_key.'&post_key='.$post_key.(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'&ac#add_comment">'.$general_strings['add_comment'].'</a>');
	}
}


$t->set_var('HEADING_STRING', '<a href="journalview.php?space_key='.$space_key.'&module_key='.$module_key.(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'">'.$journal_name.'</a> &raquo; '.$general_strings['selected_entry']);

$t->set_block('journal', 'post_keysBlock', 'PKeysBlock');
$t->set_block('journal', 'DayBlock', 'DBlock');
$t->set_block('journal', 'EntryBlock', 'EBlock');
$t->set_var('REFERER', isset($_GET['referer'])? $_GET['referer'] : $CONFIG['PATH'].urlencode('/modules/journal/entry.php?space_key='.$space_key.'&module_key='.$module_key.'&post_key='.$post_key.(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'')));
if (!empty($_SESSION['current_user_key']) && ($journal_user_key==$_SESSION['current_user_key'] || $is_admin==true)) {
	$edit_comments=true;	
}
$limits['module_key']=$module_key;
$limits['post_key']=$post_key;
$posts_array = $objPosts->getPostData($limits,true);
$entry_count = count($posts_array);
$t->set_block('comments', 'CommentBlock', 'CommBlock');
$comment_array = $CONN->GetArray("SELECT added_by_key,post_key, thread_key, parent_key, added_by_key, modified_by_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, date_published, unauth_name,unauth_url, {$CONFIG['DB_PREFIX']}users.first_name as first_name,{$CONFIG['DB_PREFIX']}users.last_name as last_name FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND parent_key!='0' AND thread_key='$post_key'  ORDER BY date_published ASC");



$t->set_var('KABLOOEY',md5($_COOKIE['PHPSESSID']));

if(empty($_SESSION['current_user_key'])) {
	if(!empty($_COOKIE['PHPSESSID'])) {
		require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');
	
		$t->set_var('LOGIN_INFO',"<a href=\"{$CONFIG['PATH']}/login.php?request_uri=".urlencode($_SERVER['REQUEST_URI']).'">'.$general_strings['login']."</a>, <a href=\"{$CONFIG['PATH']}/users/userinput.php?request_uri=".urlencode($_SERVER['REQUEST_URI']).'">'.$user_strings['add_account'].'</a>, <strong><em>'.$general_strings['or'].'</em></strong>&nbsp; '.$general_strings['fill_in_fields'].':<br />');
	} else {
		$t->set_block('comments','CommentInputBlock','CIBlock');$t->set_var('CIBlock','<div class="error">'.$general_strings['need_cookies_to_post'].'</div>');	
	}
} else {
	$t->set_block('comments','CommentNotLoggedInBlock','CNLBlock');$t->set_var('CNLBlock',' ');
}

for ($i=0;$i<$entry_count;$i++) {
	
	if ($posts_array[$i]['post_key']==$post_key) {
		$date_published = $CONN->UnixTimestamp($posts_array[$i]['date_published']);
		if (($date_published<time() && $posts_array[$i]['status_key']==1) ||$_SESSION['current_user_key']==$posts_array[$i]['added_by_key']) {
			$t->set_var('DBlock', '');
			$t->set_var('PKeysBlock', '');
			$t->set_var('SHOW_COMMENTS', '');		
			$t->set_var('CommBlock', '');
		

			$user_key = $posts_array[$i]['added_by_key'];
			$t->set_var('ADDED_BY_KEY',$user_key);

			$day = date('l j M, Y',$date_published);
			$hour = date('g:ia',$date_published);
			
			if(($postyear=date('Y',$date_published))!=date('Y')) $t->set_var('POSTED_YEAR',$postyear);

			$t->set_var('POSTED_MONTH',date('M',$date_published));
			$t->set_var('POSTED_DAYOFMONTH',date('j',$date_published));
			$t->set_var('POSTED_TIME', $objDate->formatDate($date_published,'short',true));
			$has_comments = $objPosts->formatThread($comment_array,$posts_array[$i]['post_key'],0,$edit_comments);
			if(isset($has_comments[$posts_array[$i]['post_key']])){	
				$t->set_var('COMMENTS_HEADING', $general_strings['comments']);
			} else {
				$t->set_var('COMMENTS_HEADING', '');
			}
			$t->parse('SHOW_COMMENTS', 'comments', true);
			if ($posts_array[$i]['extended_body']!='') {
				$t->set_var('EXTENDED_BODY', $posts_array[$i]['extended_body']);
			} else {
				$t->set_var('EXTENDED_BODY', '');
			}
			if ($posts_array[$i]['added_by_key']!=$journal_user_key && $journal_settings['default_display']!='show_all') {
				$t->set_var('JOURNAL_BODY_CLASS', 'postBodyOther');		
			} else {
				
				$t->set_var('JOURNAL_BODY_CLASS', 'postBody');
			}
			$t->set_var('POST_KEY', $posts_array[$i]['post_key']);
			$t->set_var('PARENT_KEY', $posts_array[$i]['post_key']);
			$t->set_var('THREAD_KEY', $posts_array[$i]['post_key']);
			$t->set_var('POST_SUBJECT', $posts_array[$i]['subject'].
				 ' <a href="'.$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH'].'post/'.$space_key.'/'.$post_key.'" title="'.$general_strings['permalink'].'"><img src="'.$CONFIG['PATH'].'/images/link.gif" width="16" height="16" valign="bottom" border="0"></a>'
			
			);
			$t->set_var('POST_BODY', $objHtml->parseText($posts_array[$i]['body']));
			$t->set_var('USER_FIRST_NAME', $posts_array[$i]['first_name']);
			$t->set_var('POSTED_HOUR', $hour);
			$t->set_var('DRAFT', ($posts_array[$i]['status_key']==2) ? $general_strings['draft'] : '');		
			$t->set_var('STATUS', ($date_published>time()) ? $general_strings['not_published'] : '');		
			if ($is_admin==true || $user_key==$_SESSION['current_user_key']) {
		
				$edit_link = get_admin_tool('entryinput.php?space_key='.$space_key.'&module_key='.$module_key.'&group_key='.$group_key.'&post_key='.$posts_array[$i]['post_key'].(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'&action=Modify',true,$journal_strings['edit_entry']);
				$t->set_var('EDIT_LINK',$edit_link);
			
			} else {
	
				$t->set_var('EDIT_LINK','');
			}
			if ($day!=$day1) {
	
				$t->set_var('DATE', $day);
				$t->parse('DBlock', 'DayBlock', true);
		
				$day1 = $day;
		
			} else {
	
				$t->set_var('DBlock', '');
	
			}
			//get tags for this entry
			$tag_array = $objTags->getTags($module_key, '', $post_key);
			$entry_tags = '';
			$count = count($tag_array);
			for ($i=0;$i<$count;$i++) {

			if ($i<$count-1) {
				$delimiter = '<wbr>'; //  Optional line break. Was ', ';
			} else {
				$delimiter = '';					
			}
			$entry_tags .= '<a href="journalview.php?space_key='.$space_key.'&module_key='.$module_key.'&tag_key='.$tag_array[$i]['tag_key'].(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'">'.$tag_array[$i]['text'].'</a>'.$delimiter;
			
			}
			
			$t->set_var('TAG_LIST',$entry_tags);
			$t->set_var('POST_TAGS',(empty($entry_tags)?'':$general_strings['tags'].':'));
			if ($is_admin==true || $journal_user_key==$_SESSION['current_user_key']) {
				$t->parse('PKeysBlock', 'post_keysBlock', true);
			}
			$t->parse('EBlock', 'EntryBlock', true);
	
		}
	}	
}


$objJournal->getSideBar($module_key, $post_key, $posts_array, $journal_user_key);

$t->set_var('SUBMIT_BUTTON',$general_strings['add']);	
//now set values for any undefined variables in templates
$t->set_strings('journal',  $journal_strings, '', $errors);

$t->set_strings('comments',  $journal_strings, '', $errors);

$t->parse('CONTENTS', 'header', true); 
get_navigation(true,true);

$t->parse('CONTENTS', 'journal', true);

$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p("CONTENTS");
$CONN->Close();	   
exit;	
?>


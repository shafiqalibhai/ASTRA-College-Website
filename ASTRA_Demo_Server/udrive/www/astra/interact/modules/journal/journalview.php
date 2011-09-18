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
* Journal view page
*
* Displays a journal entries page. If user is an admin, or if journal is open 
* then display requested users journal, or if not display current users journal
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: journalview.php,v 1.68 2007/07/26 22:10:53 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/calendar_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key			= $_GET['module_key'];
	$group_key			= $_GET['group_key'];
	$journal_user_key	= $_GET['journal_user_key'];
	$show_comments		= isset($_GET['show_comments'])? $_GET['show_comments'] : '';
	$sort_order			= isset($_GET['sort_order'])? $_GET['sort_order'] : '';
	$tag_key			= isset($_GET['tag_key']) ? $_GET['tag_key'] : '';
	$date_limit			= isset($_GET['date_limit']) ? $_GET['date_limit'] : '';	
} else {

	$show_comments		= isset($_POST['show_comments'])? $_POST['show_comments'] : '';
	$module_key			= $_POST['module_key'];
	$group_key			= $_POST['group_key'];
	$journal_user_key	= $_POST['journal_user_key'];	
	$view				= $_POST['view'];
	$sort_order			= $_POST['sort_order'];
	$tag_key			= isset($_POST['tag_key']) ? $_POST['tag_key'] : '';

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

if($objJournal->checkShowAll()==false && $journal_user_key!==$_SESSION['current_user_key']) {

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

if (isset($_POST['post_keys']) && !empty($_POST['delete'])) {
	foreach($_POST['post_keys'] as $value) {
		if ($objJournal->checkEntryEditRights($value)) {
			$objPosts->deletePost(array('post_key'=>$value,'module_key'=>$module_key));
		}	
	}
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"		  => "header.ihtml",
	"navigation"	  => "navigation.ihtml",
	"journal"		 => "journal/journalview.ihtml",
	"comments"		 => "modules/comments.ihtml",
	"footer"		  => "footer.ihtml"
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('ACTION_SELECTED_MENU', '<input type="submit" value="'.$general_strings['display'].'" name="display">');

if ($_SESSION['current_user_key'] && (($is_admin==true || $journal_user_key==$_SESSION['current_user_key'])  || 
	($journal_settings['default_display']=='show_all' && 
		(($journal_settings['members']=='selected' && in_array($_SESSION['current_user_key'],$journal_settings['selected_user_keys'])) || $journal_settings['members']=='all') 
		)
	)){
	
$t->set_var('ACTION_SELECTED_MENU', ' &nbsp; <input class="admin_tool" type="submit" value="'.$general_strings['delete'].'" name="delete">',true);
	$t->set_var('ADD_ENTRY_CLASS',get_admin_tool_class());
} else {
	$t->set_block('journal', 'AddBlock', 'AdEntryBlock');
}

$t->set_var('SPACE_KEY', $space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',	$link_key);
$t->set_var('JOURNAL_USER_KEY', $journal_user_key);
$t->set_var('USER_KEY',	isset($journal_user_key)?$journal_user_key:'');
$t->set_var('POSTED_BY', $general_strings['posted_by']);
$t->set_var('REPLY', $general_strings['reply']);
$t->set_var('REFERER', urlencode($_SERVER['REQUEST_URI']));


$t->set_var('SHOW_COMMENTS', $show_comments);
if ((isset($show_comments) && $show_comments==1)|| ((!isset($show_comments) || $show_comments=='') && $journal_settings['show_comments']==1)) {
	$t->set_var('SHOW_COMMENTS_TOGGLE', $general_strings['hide_comments']);
	$t->set_var('SHOW_COMMENTS_VALUE', '0');
	$show_comments=1;
}  else {
	$t->set_var('SHOW_COMMENTS_TOGGLE', $general_strings['show_comments']);
	$t->set_var('SHOW_COMMENTS_VALUE', '1');
}

if ($show_comments==1) {

	$t->set_block('journal', 'CommentlinkBlock', 'CommlinkBlock');
	$t->set_var('CommLinkBlock','');
	$show_comments = true;
	$parent_key = '';

} else {
	$t->set_var('COMMENTS_LINK', $general_strings['comments']);
	$parent_key = '0';
}
$limits = array();
if (isset($date_limit) && $date_limit!='') {
	$start_date = $CONN->DBDate($date_limit.'-01');
	$end_date = $CONN->DBDate($date_limit.'-31');
	$limits['date_limit'] = 'AND date_published>='.$start_date.' AND date_published<='.$end_date;
	$journal_settings['entries_to_show'] = '500';
}
//add rss link to header if available
if ($journal_settings['enable_rss']==1) {
	$base_rss=$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH'].'rss/'.$space_key.'/'.$module_key.($journal_user_key?'/'.$journal_user_key:'');
	$tag_limit = !empty($tag_key)?'/t'.$tag_key:'';
	$t->set_var('META_TAGS', '<link href="'.$base_rss.$tag_limit.'" rel="alternate" type="application/rss+xml" title="'.$t->get_var('PAGE_TITLE').' RSS" />',true);
}
 

$t->set_block('journal', 'post_keysBlock', 'PKeysBlock');
//$t->set_block('journal', 'DayBlock', 'DBlock');
$t->set_block('journal', 'EntryBlock', 'EBlock');

$limits['module_key']     = $module_key;
$limits['parent_key']     = $parent_key;
$limits['tag_key']        = $tag_key;

// We need to have paging when > entries_to_show... in the meantime, ignore entries_to_show when filtering by tag.
if(empty($limits['tag_key'])) $limits['row_limit'] = $journal_settings['entries_to_show'];

$limits['date']           = $date_limit_sql;
$limits['selected_posts'] = (isset($_POST['post_keys']) && !empty($_POST['display'])) ? $_POST['post_keys'] : '';

if (!isset($sort_order) || $sort_order=='desc' || $sort_order=='') {
	$sort_order = 'desc';
	$t->set_var('SORT_ORDER', 'asc');
} else {
	$t->set_var('SORT_ORDER', 'desc');
}


//set any limit strings for headings
if (!empty($tag_key)) {
	$heading_limits = $objTags->getTagText($tag_key);
	if ($journal_settings['enable_rss']==1) {
		$base_rss=$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH'].'rss/'.$space_key.'/'.$module_key.($journal_user_key?'/'.$journal_user_key:'');
		$rss_limit = ' <span class="rssLimit"><a href="'.$base_rss.'/t'.$tag_key.'" class="small" title="'.$general_strings['rss_limit'].'"><img src="'.$CONFIG['PATH'].'/images/feedreader.gif" width="14" height="14" border="0" align="bottom" alt="'.$general_strings['rss_limit'].'"> RSS</a></span>';
	}
} else if (!empty($date_limit)){
	$date_parts = explode('-',$date_limit);
	$heading_limits =  $objDate->convertMonthNumtoTxt($date_parts[1]).' '.$date_parts[0];
} else if (isset($_POST['post_keys'])) {
	$heading_limits = ' &nbsp; ';
} else {
	$heading_limits = '';
}
$t->set_var('PAGE_TITLE', ' : '.$heading_limits, true);
$t->parse('CONTENTS', 'header', true);
if ($journal_settings['default_display']=='show_all') {
	
	$heading = !empty($heading_limits)?'<a href="journalview.php?space_key='.$space_key.'&module_key='.$module_key.(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'">'.$journal_strings['all_members_entries'].'</a> &raquo; '.$general_strings['selected_entries'].' : '.$heading_limits:$journal_strings['all_members_entries'];
	
} else {
	$limits['user_key'] = $journal_user_key;
	$rs = $CONN->Execute($objJournal->getUserSql());
	$user_count = $rs->RecordCount();
		$journal_name = ($user_count>1)?($user_data['first_name'].' '.$user_data['last_name']):(!empty($heading_limits)?$general_strings['all_entries']:'');
	$heading = !empty($heading_limits)?'<a href="journalview.php?space_key='.$space_key.'&module_key='.$module_key.(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'">'.$journal_name.'</a> &raquo; '.$general_strings['selected_entries'].' : '.$heading_limits:$journal_name.$heading_limits;
	
}
$t->set_var('HEADING_STRING', $heading);
$t->set_var('RSS_LIMIT', !empty($rss_limit)?$rss_limit:'');
$posts = $objPosts->getPostData($limits,false,$sort_order);
if ($is_admin) {
	$t->set_var('MODULE_BREADCRUMBS', ' '.get_admin_tool('journal_input.php?space_key='.$space_key.'&amp;module_key='.$module_key.'&amp;link_key='.$link_key.'&amp;action=modify',true,'Edit component '.$module_key),true);
}
$t->set_block('comments', 'CommentBlock', 'CommBlock');
if ((!empty($_SESSION['current_user_key']) && $journal_user_key==$_SESSION['current_user_key']) || $is_admin==true) {
	$edit_comments=true;	
}
get_navigation(true,true);
$entry_count = $posts->RecordCount();

if ($entry_count==0) {
	$t->set_var('EBlock', '<p>'.$journal_strings['no_entries_individual'].'</p>');
	$t->set_var('SHOW_COMMENTS_TOGGLE', '');
	$t->set_var('REVERSE_SORT_ORDER', '');
	$t->set_block('journal', 'ActionBlock', 'ActBlock');
} else {
	
	if ($show_comments==true){
		$parent_limit = '(';
		while(!$posts->EOF) {
			if ($posts->CurrentRow()==0) {
				$parent_limit .= $posts->fields['post_key'];
			} else {
				$parent_limit .= ','.$posts->fields['post_key'];
			}
 			$posts->MoveNext();
		}
		$parent_limit .= ')';
		$posts->MoveFirst();
		
		$comment_array = $CONN->GetArray("SELECT added_by_key,post_key, thread_key, parent_key, modified_by_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, date_published, unauth_name,unauth_url, {$CONFIG['DB_PREFIX']}users.first_name as first_name,{$CONFIG['DB_PREFIX']}users.last_name as last_name FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND parent_key!='0' AND thread_key in $parent_limit ORDER BY date_published ASC");
	} 
	while (!$posts->EOF) {
		
		if ($posts->fields['parent_key']==0) {
		
			$date_published = $CONN->UnixTimestamp($posts->fields['date_published']);
			if (($date_published<time() && $posts->fields['status_key']==1) ||$_SESSION['current_user_key']==$posts->fields['added_by_key']) {
				$t->set_var('DBlock', '');
				$t->set_var('PKeysBlock', '');
				$t->set_var('SHOW_COMMENTS', '');		
				$t->set_var('CommBlock', '');
				
				$user_key = $posts->fields['added_by_key'];
				$day = date('l j M, Y',$date_published);
				$hour = date('g:ia',$date_published);
				
				if(($postyear=date('Y',$date_published))!=date('Y')) $t->set_var('POSTED_YEAR',$postyear);
				
				$t->set_var('POSTED_MONTH',date('M',$date_published));
				$t->set_var('POSTED_DAYOFMONTH',date('j',$date_published));
				$t->set_var('POSTED_TIME', $objDate->formatDate($date_published,'short',true));
				
				$referer = $CONFIG['PATH'].urlencode('/modules/journal/journalview.php?space_key='.$space_key.'&module_key='.$module_key.(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'&show_comments=1#anchor_'.$posts->fields['post_key']);
				if ($show_comments===true) {
					$t->set_block('comments', 'CommentInputBlock', 'CommInputBlock');
					$t->set_var('CommInputBlock', '<a href="entry.php?space_key='.$space_key.'&module_key='.$module_key.'&post_key='.$posts->fields['post_key'].(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'&referer='.$referer.'&ac#add_comment">'.$general_strings['add_comment'].'</a>');
					
					$t->set_var('COMMENTS_HEADING', $general_strings['comments']);
					$has_comments = $objPosts->formatThread($comment_array,$posts->fields['post_key'],0,$edit_comments);
					if(isset($has_comments[$posts->fields['post_key']])){	
						$t->set_var('COMMENTS_HEADING', $general_strings['comments']);
					} else {
						$t->set_var('COMMENTS_HEADING', '');
					}
					$t->set_var('COMMENT_COUNT', '');
					$t->parse('SHOW_COMMENTS', 'comments', true);
				} else {
					$t->set_var('COMMENT_COUNT', $objPosts->countPosts('','',$posts->fields['post_key']));
				}
				if ($posts->fields['extended_body']!='') {
					$t->set_var('EXTENDED_BODY', '<a href="entry.php?space_key='.$space_key.'&module_key='.$module_key.'&post_key='.$posts->fields['post_key'].(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'">&hellip;'.$general_strings['more'].'</a>');
						
				} else {
					$t->set_var('EXTENDED_BODY', '');
				}
				if ($posts->fields['added_by_key']!=$journal_user_key && $journal_settings['default_display']!='show_all') {
					$t->set_var('JOURNAL_BODY_CLASS', 'postBodyOther');		
				} else {
					$t->set_var('JOURNAL_BODY_CLASS', 'postBody');
				}
				$t->set_var('ENTRY_ANCHOR', 'anchor_'.$posts->fields['post_key']);
				$t->set_var('POST_KEY', $posts->fields['post_key']);
				$t->set_var('ADDED_BY_KEY', $posts->fields['added_by_key']);
				$t->set_var('PARENT_KEY', $posts->fields['post_key']);
				$t->set_var('THREAD_KEY', $posts->fields['post_key']);
				$t->set_var('POST_SUBJECT', '<a href="'.$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH'].'post/'.$space_key.'/'.$posts->fields['post_key'].'" class="postTitleLink">'.$posts->fields['subject'].'</a>');
				
				// entry.php?space_key='.$space_key.'&module_key='.$module_key.'&post_key='.$posts->fields['post_key'].(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'" class="postTitleLink">'.$posts->fields['subject'].'</a>');
				$t->set_var('POST_BODY', $objHtml->parseText($posts->fields['body']));
				$t->set_var('USER_FIRST_NAME', $posts->fields['first_name']);
				$t->set_var('POSTED_HOUR', $hour);
				$t->set_var('DRAFT', ($posts->fields['status_key']==2) ? $general_strings['draft'] : '');		
				$t->set_var('STATUS', ($date_published>time()) ? $general_strings['not_published'] : '');		
				if (!empty($_SESSION['current_user_key']) && ($is_admin==true || $user_key==$_SESSION['current_user_key'] || ($journal_user_key==$_SESSION['current_user_key'] && $journal_settings['edit_rights']=='all'))) {
					
					$t->set_var('EDIT_LINK',get_admin_tool('entryinput.php?space_key='.$space_key.'&module_key='.$module_key.'&group_key='.$group_key.'&post_key='.$posts->fields['post_key'].(!empty($journal_user_key)?'&journal_user_key='.$journal_user_key:'').'&action=Modify',true,$journal_strings['edit_entry']));
				} else {
		
					$t->set_var('EDIT_LINK','');
				}
//				if ($day!=$day1) {
		
					$t->set_var('DATE', $day);
//					$t->parse('DBlock', 'DayBlock', true);
			
//					$day1 = $day;
			
//				} else {
//					$t->set_var('DATE', $day);
//					$t->parse('DBlock', 'DayBlock', true);
		
//					$t->set_var('DBlock', '');
		
//				}
				//get tags for this entry
				$tag_array = $objTags->getTags($module_key, '', $posts->fields['post_key']);
				$entry_tags = '';
				$count = count($tag_array);
				for ($i=0;$i<$count;$i++) {
	
				if ($i<$count-1) {
					$delimiter = '<wbr>'; //  Optional line break. Was ', ';
				} else {
					$delimiter = '';					
				}
				//$t->set_var('TAG_KEY',$tag_array[$i]['tag_key']);
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
		$posts->MoveNext();
	}

	
}

$objJournal->getSideBar($module_key, $post_key, $posts_array, $journal_user_key, $sort_order);
//create dropdown to select days of new items to show
$show_new_menu = $objHtml->arrayToMenu(array('0' => $general_strings['since_last_login'], '1' => $general_strings['today'], '3' => sprintf($general_strings['for_last_days'],3), '7' => sprintf($general_strings['for_last_days'],7), '30' => sprintf($general_strings['for_last_days'],30)),'show_new',$show_new,'','',true,'onchange="this.form.submit();"');
$t->set_var('SHOW_NEW_MENU',$show_new_menu);	
$t->set_var('DATE_LIMIT',$date_limit);
//now set values for any undefined variables in templates
$t->set_strings('journal',  $journal_strings, '', $errors);
$t->set_strings('comments',  $journal_strings, '', $errors);
$t->parse('CONTENTS', 'journal', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p("CONTENTS");
$CONN->Close();	   
exit;	
?>

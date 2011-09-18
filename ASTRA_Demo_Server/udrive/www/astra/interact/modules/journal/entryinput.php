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
* Input entry
*
* Add or modify an entry to a journal
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: entryinput.php,v 1.46 2007/07/31 00:53:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


if (!isset($_SESSION['current_user_key'])) {
	$request_uri = urlencode($_SERVER['REQUEST_URI']);
	require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
	$message = urlencode($forum_strings['login_to_post']);
	header("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=$request_uri&message=$message");
	exit;
} 
//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php');
	


if ($_SERVER['REQUEST_METHOD']=='GET') {
	foreach($_GET as $key => $value) {
		$post_data[$key] = $value;
 	}
 	$module_key = $_GET['module_key'];
	$group_key = isset($_GET['group_key'])?$_GET['group_key']:'';		
} else if($_SERVER['REQUEST_METHOD']=='POST'){
	foreach($_POST as $key => $value) {
		$post_data[$key] = $value;
 	}		
	$post_data['user_key']=$_POST['journal_user_key'];
	$group_key = isset($_POST['group_key'])?$_POST['group_key']:'';	
	$module_key = $_POST['module_key'];
}
$space_key 	= get_space_key();


$link_key 	= get_link_key($module_key,$space_key);
//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
require_once 'lib.inc.php';
$objJournal = new InteractJournal($space_key, $module_key, $group_key, $is_admin, $journal_strings);
$objJournal->setJournalSettings();

if (!isset($objPosts) || !is_object($objPosts)) {
	if (!class_exists('InteractPosts')) {
		require_once '../../includes/lib/posts.inc.php';
	}
	$objPosts = new InteractPosts();
}
$objPosts->setVars($module_key, $space_key);
if(!isset($objTags) || !is_object($objTags)) {
	if (!class_exists('InteractTags')){
		require_once $CONFIG['BASE_PATH'].'/includes/lib/tags.inc.php';
	}
	$objTags = new InteractTags();
}
// See if the form has been submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$errors = check_form(array('body','subject'),$post_data,$journal_strings);
	if (count($errors)==0) {
	
		switch($post_data['action']) {
			
			case 'Add':
				$post_data['added_by_key']=$_SESSION['current_user_key'];	
				
				if ($post_data['entry_type']=='multiple') {
					if (!isset($post_data['multiple_add']) || $post_data['multiple_add']=='all'){
						$user_keys = $objJournal->getuser_keys();	
					} else {
						$user_keys = $post_data['user_keys'];
					}
					$post_data['multi_entry_key'] = '';
					$n=0;
					foreach($user_keys as $value) {
						$post_data['user_key'] = $value;
						$post_key = $objPosts->addPost(array('module_key', 'subject','body','added_by_key', 'user_key','date_added','date_published','extended_body','status_key','multi_entry_key'),$post_data);
						if ($n==0) {
							$post_data['multi_entry_key'] = $post_key;
							$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}posts SET multi_entry_key='$post_key' WHERE post_key='$post_key'");
						}							
						$n++;
					}
				} else {
					$post_key = $objPosts->addPost(array('module_key', 'subject','body','added_by_key', 'user_key','date_added','date_published','extended_body','status_key'),$post_data);	
				}
				if ($post_key>0) {
					if (isset($post_data['journal_user_key']) && $post_data['journal_user_key']!='') {
						header('Location: '.$CONFIG['FULL_URL'].'/modules/journal/journalview.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&journal_user_key='.$post_data['journal_user_key']);
					} else {
						header('Location: '.$CONFIG['FULL_URL'].'/modules/journal/journal.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key']);
					}
				}
			break;
			
			case 'Modify':
				if (!$objJournal->checkEntryEditRights($post_data['post_key'])) {
					$message = urlencode($general_strings['no_edit_rights']);
					header('Location: '.$CONFIG['FULL_URL'].'/modules/journal/journal.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&message='.$message);
					exit;
				}
				switch($post_data['submit']) {
					case $general_strings['modify']:
						$post_data['modified_by_key']=$_SESSION['current_user_key'];	
						$post_data['added_by_key']=$_SESSION['current_user_key'];
						$message = $objPosts->modifyPost(array('module_key', 'subject','body','modified_by_key', 'date_modified','date_published','extended_body','status_key'),$post_data);
						if ($message===true){
							header('Location: '.$CONFIG['FULL_URL'].'/modules/journal/journalview.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&journal_user_key='.$post_data['journal_user_key']);
						}
					break;
					case $general_strings['delete']:
					
						$message = $objPosts->deletePost($post_data);
						if ($message===true){
							header('Location: '.$CONFIG['FULL_URL'].'/modules/journal/journalview.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&journal_user_key='.$post_data['journal_user_key']);
						}						
					break;	
				}
			break;
		
		}		
	}
}

if (isset($post_data['action']) && $post_data['action']=='Modify'){
	if (!$objJournal->checkEntryEditRights($post_data['post_key'])) {
		
		$message = urlencode($general_strings['no_edit_rights']);
		header('Location: '.$CONFIG['FULL_URL'].'/modules/journal/journal.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&message='.$message);
		exit;
	}
	$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='{$post_data['post_key']}' AND user_key='{$_SESSION['current_user_key']}' AND monitor_post='1'");
	if (!$rs->EOF) {
		$monitor_post = 'checked';
	}
	$post_values = $objPosts->getPostData(array('module_key' => $post_data['module_key'], 'post_key' => $post_data['post_key']));
	foreach ($post_values->fields as $key => $value) {
		if ($key=='date_published') {
			$post_data[$key] = $CONN->UnixTimestamp($value);
		} else {
			$post_data[$key] = $value;
		}
	}
	//get any existing tags
	$existing_tags = $objTags->getTags('','',$post_data['post_key']);
	if (is_array($existing_tags)) {
		$count = count($existing_tags);
		$post_data['tags'] = '';
		for ($i=0;$i<$count;$i++) {
			if ($i==$count-1) {
				$post_data['tags'] .= $existing_tags[$i]['text'];
			} else {
				$post_data['tags'] .= $existing_tags[$i]['text'].', ';					
			}
		}
	}
}

if (!isset($objHtml) || !is_object($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once('../../includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}
if (!isset($objDates) || !is_object($objDates)) {
	if (!class_exists('InteractDate')) {
		require_once('../../includes/lib/date.inc.php');
	}
	$objDates = new InteractDate();
}
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'form'		 => 'journal/entryinput.ihtml',
	'footer'	 => 'footer.ihtml'));
	
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//generate the editor components

$objHtml->setTextEditor($t, $_SESSION['auto_editor'], 'body');
$objHtml->setTextEditor($t, false, 'extended_body');
//$t->set_var('SCRIPT_BODY','onload="initEditor(\'body\')"');
$t->set_var('MONITOR_POST_CHECKED',isset($monitor_post) ? $monitor_post : '');
//generate date to publish menu
if (!isset($post_data['date_published'])) {
	$post_data['date_published'] = time();
}

$t->set_var('PUBLISH_DATE_MENU',$objDates->createDateSelect('date_published', $post_data['date_published'], true));
$t->set_var('STATUS_MENU',$objHtml->arrayToMenu(array('1' => $general_strings['published'], '2' => $general_strings['draft']),'status_key',$post_data['status_key']));
//now set values for any undefined variables in templates

if ($is_admin==true && ((isset($post_data['entry_type']) && $post_data['entry_type']=='multiple') || (isset($post_data['multi_entry_key']) && $post_data['multi_entry_key']>0))) {
	
	$user_sql = $objJournal->getUserSql();
	if (isset($post_data['multi_entry_key']) && $post_data['multi_entry_key']>0) {
		$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}posts WHERE multi_entry_key='".$post_data['multi_entry_key']."'");
		$user_keys = array();
		while(!$rs->EOF) {
			array_push($user_keys, $rs->fields[0]);
			$rs->MoveNext();
		} 
	} 
	$members_menu = make_menu($user_sql,'user_keys[]',$user_keys,'6','true');
	$t->set_var('MEMBERS_MENU', $members_menu);	
	$t->set_var('MULTI_ENTRY_KEY',isset($post_data['multi_entry_key']) ? $post_data['multi_entry_key']: '');	
} else {
	$t->set_block('form', 'MultiInputBlock', 'MultiInBlock');
	$t->set_var('MultiInBlock','');
}

$tag_array = $objTags->getTags('',$_SESSION['current_user_key']);
$count = count($tag_array);
if ($count>15) {
	$count = 15;
}
$tag_list = '';
for ($i=0;$i<$count;$i++) {

	$escaped_tag_text = addslashes($tag_array[$i]['text']);
	$tag_list .= '<a href="javascript:selectTag(\'tag_list\',\''.$escaped_tag_text.'\')" class="tagSelect">'.$tag_array[$i]['text'].'</a> ';
	
}


$t->set_var('TAG_LIST',$tag_list);

$t->parse('CONTENTS', 'header', true); 
get_navigation();
$t->set_strings('form',  $journal_strings, $post_data, $errors);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
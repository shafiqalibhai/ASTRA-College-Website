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
* Page to view a knowledge base entry in standalone window with no navigation
*
* Displays a single knowledgebase entry and any attached comments 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: singleentry.php,v 1.10 2007/07/30 01:57:03 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/kb_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

$module_key		= $_GET['module_key'];
$space_key		= $_GET['space_key'];
$category_key	= $_GET['category_key'];
$entry_key		= $_GET['entry_key'];
$message		= isset($_GET['message'])? $_GET['message']: '';	
$link_key 		= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);
$current_user_key = $_SESSION['current_user_key'];

$objKb = new InteractKB($space_key, $module_key, $group_key, $is_admin, $quiz_strings);
$kb_data = $objKb->getKbData($module_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(


	'header'	   	=> 'popupheader.ihtml',
	'body'	   		=> 'kb/entry.ihtml',
	'thread'	   	=> 'forums/fullthread.ihtml',
	'fullposts'		=> 'forums/showfullpost.ihtml'


));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('ENTRY_KEY',$entry_key);
$t->set_var('REFERER','/kb/entry.php');
$t->set_var('COMMENTS_STRING',$general_strings['comments']);
$t->set_var('ADD_COMMENT_STRING',$general_strings['add_comment']);
$t->set_var('EDIT_STRING',$general_strings['edit']);
$t->set_var('ADDED_BY_STRING',$general_strings['added_by']);


$t->parse('CONTENTS', 'header', true); 
//get_navigation();


//now get the fields for this entry
$t->set_block('body', 'FieldBlock', 'FLDBlock');
$entry_data = $objKb->getEntryData($entry_key);

//get user details

if (!class_exists('InteractUser')) {

	require_once('../../includes/lib/user.inc.php');
	
} 

if (!is_object($objUser)) {

	$objUser = new InteractUser();

} 

if (!class_exists(InteractDates)) {

	require_once('../../includes/lib/date.inc.php');
	
} 

if (!is_object($objDates)) {

	$objDates = new InteractDate();

} 

$add_user = $objUser->getUserData($entry_data['added_by_key']);
$t->set_var('ADDED_BY',$add_user['first_name'].' '.$add_user['last_name']);
$t->set_var('DATE_ADDED',', '.$objDates->formatDate($entry_data['date_added'],'long'));

if ($entry_data['modified_by_key']!=0) {

	$modified_user = $objUser->getUserData($entry_data['modified_by_key']);
	$t->set_var('MODIFIED_BY', $modified_user['first_name'].' '.$modified_user['last_name']);
	$t->set_var('MODIFIED_BY_STRING','<br />'.$general_strings['modified_by']);
	$t->set_var('DATE_MODIFIED',', '.$objDates->formatDate($entry_data['date_modified'],'long'));

}


$t->set_block('body', 'EditlinkBlock', 'EDLBlock');
$t->set_var('EDLBlock','');



$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_fields.name, {$CONFIG['DB_PREFIX']}kb_entry_data.data, {$CONFIG['DB_PREFIX']}kb_fields.type_key FROM {$CONFIG['DB_PREFIX']}kb_fields, {$CONFIG['DB_PREFIX']}kb_entry_data WHERE {$CONFIG['DB_PREFIX']}kb_fields.field_key={$CONFIG['DB_PREFIX']}kb_entry_data.field_key AND {$CONFIG['DB_PREFIX']}kb_entry_data.entry_key='$entry_key' ORDER BY display_order");

if ($rs->EOF) {

	$t->set_var('FLDBlock','');
	
} else {

	while (!$rs->EOF) {

		switch ($rs->fields[2]) {
		
			case 1:
				
				if ($rs->fields[1]!='') {
				
					$t->set_var('LABEL',$rs->fields[0]);
					$t->set_var('DATA',$rs->fields[1]);		
				
				} else {
				
					$t->set_var('LABEL','');
					$t->set_var('DATA','');	
					
				}	
				
		
			break;
			
			case 2:
			
				if ($rs->fields[1]!='') {
				
					$t->set_var('LABEL',$rs->fields[0]);
					
					if (strpos($rs->fields[1], 'http://')===false) {
				
						$url = 'http://'.$rs->fields[1];
				
					} else {
				
						$url = $rs->fields[1];
				
					}
				
					$t->set_var('DATA','<a href="'.$url.'">'.$url.'</a>');	
					
				} else {
				
					$t->set_var('LABEL','');
					$t->set_var('DATA','');
				
				}
				
			break;
			
			case 3:
			
				$t->set_var('LABEL','');
				$file_path = $CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/kb/'.$kb_data['file_path'].'/'.$rs->fields[1];
				$full_file_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$kb_data['file_path'].'/'.$rs->fields[1];
				if (is_file($full_file_path)) {
				
					$t->set_var('DATA','<a href="'.$file_path.'">'.$rs->fields[0].'</a>');
					
				} else {
				
					$t->set_var('DATA','');
				
				}				
			
			break;
			
		}		
		
		$t->parse('FLDBlock', 'FieldBlock', true);
		$rs->MoveNext();
	
	}

}
$kb_trail = '';

if (isset($category_key) && $category_key!='') {

	$objKb->getTrail($category_key, false, $kb_trail);
	
}
$t->set_var('KB_TRAIL',$kb_trail);
$t->set_block('body', 'AddCommentBlock', 'AddCommBlock');
$t->set_var('AddCommBlock','');
$t->parse('CONTENTS', 'body', true);
//now get any comments

$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE entry_key='$entry_key'");

if (!$rs->EOF) {

	$t->set_block('thread', 'ForumlinksBlock', 'FLSBlock');
	$t->set_var('FLSBlock','');
	
	require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');;
	
	if (!class_exists(InteractForum)) {
	
	
		require_once('../forum/lib.inc.php');
		
	} 
	
	if (!is_object($objForum)) {
	
		$objForum = new InteractForum($space_key,$module_key,$group_key,$is_admin,$forum_strings);		
	
	}
	
	//get array of read posts
	$post_statuses = $objForum->getPoststatusArray($module_key,$current_user_key);
	$objForum->setThreadDisplayStrings($t, $entry_key);
	$objForum->getFullThread('0','', $t, $post_statuses, $entry_key);
	$t->parse('CONTENTS', 'thread', true); 

}

$t->set_var('KB_TRAIL',$kb_trail);

//$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

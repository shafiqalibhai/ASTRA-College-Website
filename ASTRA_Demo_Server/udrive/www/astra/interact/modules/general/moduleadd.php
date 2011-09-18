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
* Add module
*
* Displays page for adding a new module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: moduleadd.php,v 1.28 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');

$space_key 	= get_space_key();

if ($_SERVER['REQUEST_METHOD']=='GET') {
	
	$module_key	= $_GET['module_key'];
	$parent_key	= $_GET['parent_key'];
	$group_key	= $_GET['group_key'];
	$link_key	= $_GET['link_key'];
	$block_key	= isset($_GET['block_key'])? $_GET['block_key'] : 0;			

} else {
	
	$module_type = $_POST['module_type'];
	$module_key	 = $_POST['module_key'];
	$parent_key	 = $_POST['parent_key'];
	$group_key	 = $_POST['group_key'];
	$link_key	 = $_POST['link_key'];
	$block_key	 = isset($_POST['block_key'])? $_POST['block_key'] : 0;	
 	
}

//check that we have the required variables
check_variables(true,false);



//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$userlevel_key = $_SESSION['userlevel_key'];

//get parent module key

$rs = $CONN->Execute("SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$parent_key'");

while (!$rs->EOF) {

	$parent_module_key = $rs->fields[0];
	$rs->MoveNext();
	
} 
$rs->Close();

//see if user has admin rights to do this.

$is_admin = check_module_edit_rights($parent_module_key);

if ($userlevel_key!='1' && $accesslevel_key!='1' && $accesslevel_key!='3' && $group_accesslevel!='1' && $is_admin!='1') {

	print $module_strings['no_admin_rights'];
	exit;

}

if($_SERVER['REQUEST_METHOD']=='POST') {
	
	switch($module_type) {
		
		case folder:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/folder/folder_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
		   exit;
		
		break;

		case weblink:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/weblink/weblink_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
			
		break;

		case forum:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/forum/forum_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		break;

		case note:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/note/note_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		break;
		
		case file:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/file/file_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		break;

		case group:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/group/group_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		case calendar:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/calendar/calendar_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;

		case dropbox:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/dropbox/dropbox_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		case sharing:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		case heading:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/heading/heading_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		break;
		
		case page:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/page/page_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		break;
		
		case chat:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/chat/chat_input.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&block_key=$block_key");
			exit;
		
		break;		
		
		case copy:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/general/modulecopy.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&link_key=$link_key&block_key=$block_key");
			exit;
		
		break;
		
		case link:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/general/modulelink.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
		
		break;
		
		case journal:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/journal/journal_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
			
		break;

		case noticeboard:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/noticeboard/noticeboard_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
			
		break;	

case scorm:
			header("Location: {$CONFIG['FULL_URL']}/modules/scorm/scorm_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;

break;
		case gradebook:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/gradebook/gradebook_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
			
		break;				

		case quiz:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/quiz/quiz_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
			
		break;	
		
		case kb:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/kb/kb_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
			
		break;				
		case space:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/space/space_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key");
			exit;
			
		break;						
		case portfolio:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/space/space_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key&type_key=1");
			exit;
			
		break;								
		case feedreader:
		
			header("Location: {$CONFIG['FULL_URL']}/modules/feedreader/feedreader_input.php?space_key=$space_key&parent_key=$parent_key&group_key=$group_key&module_key=$module_key&link_key=$link_key&block_key=$block_key&type_key=1");
			exit;
			
		break;							
		default:
		  
			$message = urlencode($module_strings['nothing_selected']);

			header("Location: {$CONFIG['FULL_URL']}/modules/general/moduleadd.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key&message=$message");
			exit;
			
		break;
	
	}
 
} else {
	
	$button = $general_strings['submit'];
 
}	
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'modules/moduleadd.ihtml',
	'footer'		  => 'footer.ihtml'
));


$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($page_details['type_key']==1) {
	$t->set_block('form', 'FullOptionsBlock', 'FullOptsBlock');
	$t->set_var('FullOptsBlock','');
} else {
	$t->set_block('form', 'LimitedOptionsBlock', 'LimitedOptsBlock');
	$t->set_var('LimitedOptsBlock','');
}
if (!$module_key && $block_key==0) {

	$module_parent = $module_strings['left_menu'];

} else if (!$module_key && $block_key!=0){

	$module_parent = $module_strings['block_'.$block_key];

} else {

	$module_parent = $page_details['module_name'];

}
if ($_SESSION['userlevel_key']!=1 && $CONFIG['ADMINS_ADD_SPACES']!=1) {
    $t->set_block('form', 'NewSpaceBlock', 'NEWSBlock');
	$t->set_var('NEWSBlock','');
}
if ($CONFIG['ENABLE_PORTFOLIOS']!=1) {
    $t->set_block('form', 'PortfolioBlock', 'PrtfolioBlock');
	$t->set_var('PrtfolioBlock','');
}
$t->set_var('MODULE_PARENT',$module_parent);
$t->set_var('ADD_MODULE_STRING',sprintf($module_strings['add_module_heading'],$general_strings['module_text'],$module_parent));
$t->set_var('INSTRUCTIONS_STRING',$module_strings['add_instructions']);
$t->set_var('INTERACTION_STRING',$module_strings['interaction']);
$t->set_var('CONTENT_STRING',$module_strings['content']);
$t->set_var('FORUM_STRING',$module_strings['forum']);
$t->set_var('GROUP_STRING',$module_strings['group']);
$t->set_var('DROPBOX_STRING',$module_strings['dropbox']);
$t->set_var('SHARING_STRING',$module_strings['sharing']);
$t->set_var('CHAT_STRING',$module_strings['chat']);
$t->set_var('JOURNAL_STRING',$module_strings['journal']);
$t->set_var('FOLDER_STRING',$module_strings['folder']);
$t->set_var('FILE_STRING',$module_strings['file']);
$t->set_var('WEBLINK_STRING',$module_strings['weblink']);
$t->set_var('GRADEBOOK_STRING',$module_strings['gradebook']);
$t->set_var('QUIZ_STRING',$module_strings['quiz']);
$t->set_var('NOTE_STRING',$module_strings['note']);
$t->set_var('PAGE_STRING',$module_strings['page']);
$t->set_var('KB_STRING',$module_strings['kb']);
$t->set_var('CALENDAR_STRING',$module_strings['calendar']);
$t->set_var('NOTICEBOARD_STRING',$module_strings['noticeboard']);
$t->set_var('HEADING_STRING',$module_strings['heading']);
$t->set_var('NEW_SPACE_STRING',$module_strings['new_space']);
$t->set_var('PORTFOLIO_SPACE_STRING',$module_strings['portfolio_space']);
$t->set_var('SCORM_STRING',$module_strings['scorm']);
$t->set_var('LINK_TO_STRING',sprintf($module_strings['link_to_module'],$general_strings['module_text']));
$t->set_var('COPY_STRING',sprintf($module_strings['copy_module'],$general_strings['module_text']));
$t->set_var('USE_EXISTING_STRING',sprintf($module_strings['use_existing'], $general_strings['module_text']));
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('BUTTON',$button);

$t->set_var('ACTION',$action);
$t->set_var('MODULE_TEXT',$general_strings['module_text']);



$t->parse('CONTENTS', 'header', true); 

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('PARENT_KEY',$parent_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('BLOCK_KEY',$block_key);
get_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
exit;
?>
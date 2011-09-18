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
* Mark files
*
* Displays form for marking a file in dropbox 
*
* @package Dropbox
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: mark.php,v 1.30 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/dropbox_strings.inc.php');

//set variables
$space_key 	= get_space_key();
if ($_GET['module_key']) {

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];	
	$file_key   = $_GET['file_key'];

} else {

	$module_key	 = $_POST['module_key'];
	$group_key	 = $_POST['group_key'];
	$file_key	 = $_POST['file_key'];
	$file_name   = $_POST['file_name'];
	$item_key    =  $_POST['item_key'];
	$user_key    =  $_POST['user_key'];
	$status_key  =  $_POST['status_key'];					

}

$link_key = get_link_key($module_key,$space_key);

check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$page_details = get_page_details($space_key,$link_key);
 	
if (isset($_POST['action'])) {

	switch($_POST['action']) {

		//if we are adding a new file  form input needs to be checked 
		
		case modify:
	 
		$comments	 = $_POST['comments'];
		$status_key   = $_POST['status_key'];
		$file		 = $_FILES['file']['tmp_name'];
		$new_file_name	 = $_FILES['file']['name'];
		$item_key	 = $_POST['item_key'];
		$user_key	 = $_POST['user_key'];
		$grade_key	= $_POST['grade_key'];				
										

		//if there is a gradebook link to this dropbox and gradebook grade selected
		//then update gradebook
		
		if ($item_key!='' && $grade_key!='' && $user_key!='') {

			require_once('../gradebook/lib.inc.php');
			$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

			$gradebook->modifygrade($item_key, $user_key, $grade_key, $comments);
			
		}

		if ($file!='' && $file!='none') {
		
			$description = ereg_replace('<b> - ('.$dropbox_strings['annotated_tag'].')</b>', "", $_POST['description']);
			$description = substr($description,0,30).'<b> - ('.$dropbox_strings['annotated_tag'].')</b>';
			$file_path = get_dropbox_file_path($module_key);
			$save_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$file_path;	
			unlink($save_path.'/'.$file_name);
			$file_name = ereg_replace("\.[a-zA-Z]*", "", $file_name);
			$file_name = ereg_replace("_annotated", "", $file_name);
			
			$file_name = substr($file_name,0,30);
			$file_name = $file_name.'_annotated';
			
			if (preg_match("/\./",$new_file_name)) {
				$new_ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $new_file_name);
				$new_ext = '.'.$new_ext;
			} else {
				$new_ext='';
			}
			$file_name = $file_name.$new_ext;

			move_uploaded_file($file,$save_path.'/'.$file_name);

				
		} else {
		
			$description = $_POST['description'];
			
		}
		
		
		$date_modified   = $CONN->DBDate(date('Y-m-d H:i:s'));
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}dropbox_files SET comments='$comments',status='$status_key',description='$description', date_status_changed=$date_modified, gradebook_item_key='$item_key', filename='$file_name' where file_key=$file_key";
		

		if ($CONN->Execute($sql) === false) {
		
			$message =  'There was an error modifying your file: '.$CONN->ErrorMsg().' <br />';
		
		} else { 
		
			header("Location: {$CONFIG['FULL_URL']}/modules/dropbox/dropbox.php?space_key=$space_key&module_key=$module_key");
			
		}
		
	}
	
} 

$description_error = sprint_error($errors['description']);
$file_error = sprint_error($errors['file']);

$sql = "SELECT file_key,description,{$CONFIG['DB_PREFIX']}dropbox_file_status.name, first_name, last_name ,{$CONFIG['DB_PREFIX']}dropbox_files.date_added,comments,{$CONFIG['DB_PREFIX']}dropbox_files.status,{$CONFIG['DB_PREFIX']}dropbox_files.filename, {$CONFIG['DB_PREFIX']}dropbox_files.user_key, {$CONFIG['DB_PREFIX']}dropbox_files.gradebook_item_key FROM {$CONFIG['DB_PREFIX']}dropbox_files,{$CONFIG['DB_PREFIX']}dropbox_file_status,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}dropbox_files.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}dropbox_files.status={$CONFIG['DB_PREFIX']}dropbox_file_status.status_key AND (file_key='$file_key')";

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'mark'			=> 'dropboxes/mark.ihtml',
	'footer'		  => 'footer.ihtml'
));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
//generate the editor components

if (!class_exists('InteractHtml')) {

	require_once('../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();
$html->setTextEditor($t, $_SESSION['auto_editor'], 'comments');
$t->parse('CONTENTS', 'header', true);

$rs = $CONN->Execute($sql);
if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}

while (!$rs->EOF) {

	$file_key		   = $rs->fields[0];
	$description		= $rs->fields[1];
	$status			 = $rs->fields[2];
	$username		  = $rs->fields[3].' '.$rs->fields[4];
	$date_added		 = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[5]),'short', true);
	$comments		   = $rs->fields[6];
	$status_key		 = $rs->fields[7];
	$file_name		  = $rs->fields[8];
	$user_key		   = $rs->fields[9];
	
	if (!$item_key && $rs->fields[10]!=0) {
	
		$item_key = $rs->fields[10];
		
	}
	$file_path = get_dropbox_file_path($module_key);

	$t->set_var('FILE_KEY',$file_key);
	$t->set_var('DESCRIPTION2',$description);
	$t->set_var('ORIGINAL_DESCRIPTION',$description);
	$t->set_var('COMMENTS',$comments);
	$t->set_var('USER_NAME',$username);
	$t->set_var('DATE_ADDED',$date_added);
	$t->set_var('FILE_NAME',$file_name);
	$t->set_var('FILE_PATH',$file_path);



   	if( $CONN->GetOne("SELECT type_key FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'") =='3') {

$domname=ereg_replace("https?://([^/]+)","\\1",$CONFIG['SERVER_URL']);
	$filedownloadpath=$CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'].'recordings/'.$domname.'/modules/dropbox/';

	$file_url=$filedownloadpath.'/'.$file_path.'/'.$file_name.'.flv';

$t->set_var('FILE_STUFF','{DESCRIPTION2}<br />
  	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="66" height="33" id="av" align="middle">
						<param name="allowScriptAccess" value="sameDomain" />
						<param name="movie" value="{PATH}/includes/players/AVPLAYER.swf" />
						<param name="FlashVars" value="sndname={URL}" />
						<param name="quality" value="high" />
						<embed src="{PATH}/includes/players/AVPLAYER.swf" flashvars="sndname={URL}" quality="high" width="66" height="33" name="av" align="middle" allowscriptaccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
					</object>');

$t->set_block('mark','AmendBlock');
$t->clear_var('AmendBlock');

} else {

	$file_url = $CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/dropbox/'.$file_path.'/'.$file_name;

$t->set_var('FILE_STUFF','<a href="{URL}">
                    {DESCRIPTION2}</a>');

                    
}

	$t->set_var('URL',$file_url);
	$t->parse('FILE','FILE_STUFF');

  
	$rs->MoveNext();
}
$rs->Close();

//see of there are any gradebook links to this dropbox

$t->set_block('mark', 'gradebookBlock', 'GBlock'); 
require_once('../gradebook/lib.inc.php');
$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

if ($item_key!='' && $item_key!=0) {

	$rs = $CONN->Execute("SELECT scale_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE item_key='$item_key'");

	while (!$rs->EOF) {
	
		$scale_key = $rs->fields[0];
		$rs->MoveNext();
		
	}
	 
	$grade_data = $gradebook->getgradeData($item_key, $user_key);
	$grade_menu = $gradebook->makegradeMenu($item_key, $grade_data['grade_key'], $scale_key);
	
	$t->set_var('GRADE_MENU',$grade_menu);
	$gradebook_item_sql = "SELECT name,item_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE content_module_key='$module_key'";
	$item_menu = make_menu($gradebook_item_sql,'item_key',$item_key,'1',false);
	$t->set_var('ITEM_MENU',$item_menu);
	$t->set_var('ACTION','modify');	
	$t->set_var('GRADE_BOOK_STRING',$dropbox_strings['gradebook']);
	$t->Parse('GBlock', 'gradebookBlock', true);	

} else {
		
	$rs = $CONN->Execute("SELECT item_key, scale_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE content_module_key='$module_key'");

	if ($rs->EOF) {

		$t->set_var('GBlock','');
		$t->set_var('ACTION','modify');
	
	} else {

		if ($rs->RecordCount()>1) {
	
			$gradebook_item_sql = "SELECT name,item_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE content_module_key='$module_key'";
			$item_menu = make_menu($gradebook_item_sql,'item_key',$item_key,'1',false);
			$t->set_var('ITEM_MENU',$item_menu);
			$t->set_var('GRADE_MENU','');
			$t->set_var('ACTION','get_grades');			
			$t->set_var('GRADE_BOOK_STRING',$dropbox_strings['select_gradebook_item']);
			$t->set_var('GET_GRADES_BUTTON','<input type="submit" name="Submit" value="'.$dropbox_strings['get_grades'].'">');						
	
		} else {
	
			while (!$rs->EOF) {
	
				$item_key  = $rs->fields[0];
				$scale_key = $rs->fields[1];		
				$rs->MoveNext();
		
			} 
	
			$rs->Close();

			$grade_data = $gradebook->getgradeData($item_key, $user_key);

			$grade_menu = $gradebook->makegradeMenu($item_key, $grade_data['grade_key'], $scale_key);
			
			$t->set_var('GRADE_MENU',$grade_menu);
			$t->set_var('ACTION','modify');
			$t->set_var('GRADE_BOOK_STRING',$dropbox_strings['gradebook']);
		
		}
	
		$t->Parse('GBlock', 'gradebookBlock', true);

	}
	
}	

$t->set_var('STATUS_STRING',$general_strings['status']);
$t->set_var('COMMENTS_STRING',$general_strings['comments']);
$t->set_var('OPTIONAL_STRING',$general_strings['optional']); 
$t->set_var('SUBMIT_STRING',$general_strings['submit']); 
$t->set_var('AMMENDED_STRING',$dropbox_strings['ammended']);
$t->set_var('BACK_LINK_STRING',$general_strings['back_to'].' '. $page_details['module_name']); 

get_navigation();


$status_sql = "select name, status_key from {$CONFIG['DB_PREFIX']}dropbox_file_status order by name";
$status_menu = make_menu($status_sql,'status_key',$status_key,'4');
$t->set_var('STATUS_MENU',$status_menu);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('ITEM_KEY',$item_key);
$t->set_var('USER_KEY',$user_key);

$t->parse('CONTENTS', 'mark', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Return a dropbox file path
* 
* @return string $file_path path to drop box directory
*/
function get_dropbox_file_path($module_key) 
{
	global $CONN, $CONFIG;
	
	$sql = "SELECT file_path FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'";
   	$rs = $CONN->Execute($sql);

   	while (!$rs->EOF) {

	   	$file_path = $rs->fields[0];
	   	$rs->MoveNext();

   	}
	
	$rs->Close();

	return $file_path;
	
} //end get_dropbox_file_path
?>
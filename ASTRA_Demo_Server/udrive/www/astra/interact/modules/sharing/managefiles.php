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
* Manage files
*
* Allow uploading of files associated with a file on sharing module 
*
* @package Sharing
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: managefiles.php,v 1.19 2007/07/30 01:57:05 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/sharing_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/file_strings.inc.php');

//set variables
$space_key 	= get_space_key();
if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key 	= $_GET['module_key'];
	$shareditem_key	= $_GET['shareditem_key'];
	$action			= $_GET['action'];
	$sub_dir		= $_GET['sub_dir'];			
	
} else {

	$module_key		= $_POST['module_key'];
	$shareditem_key	= $_POST['shareditem_key'];	
	$action			= $_POST['action'];
	$new_sub_dir	= $_POST['new_sub_dir'];
	$sub_dir	 = $_POST['sub_dir'];	
}

$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables
check_variables(true,false,true);

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

$sql = "SELECT {$CONFIG['DB_PREFIX']}shared_items.name,{$CONFIG['DB_PREFIX']}shared_items.file_path,{$CONFIG['DB_PREFIX']}sharing_settings.file_path, {$CONFIG['DB_PREFIX']}shared_items.date_added, {$CONFIG['DB_PREFIX']}shared_items.user_key FROM {$CONFIG['DB_PREFIX']}shared_items, {$CONFIG['DB_PREFIX']}sharing_settings WHERE {$CONFIG['DB_PREFIX']}sharing_settings.module_key={$CONFIG['DB_PREFIX']}shared_items.module_key AND {$CONFIG['DB_PREFIX']}shared_items.shared_item_key='$shareditem_key'";

$rs = $CONN->Execute($sql);
while (!$rs->EOF) {
	   
	$shared_item_name  = $rs->fields[0];
	$sharing_item_path = $rs->fields[1];
	$module_path	   = $rs->fields[2];
	$date_added		= $CONN->UnixTimeStamp($rs->fields[3]);	
	$user_key		  = $rs->fields[4];		
	$rs->MoveNext();
	
}

$date_now = mktime();
$editable_date = $date_now-1800;


//check if user is admin or owner and if still has edit rights

$can_edit_module = check_module_edit_rights($module_key);

if ($can_edit_module==true || ($user_key==$current_user_key && $date_added>$editable_date)) {

	$can_edit==true;
	
} else {

		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=You+are+not+allowed+to+edit+that+{$general_strings['module_text']}");

}

$full_dir_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$sharing_item_path;
$rs->Close();
if ($sub_dir) {

	$full_dir_path = $full_dir_path.'/'.$sub_dir;
	$parent_sub_dir = $sub_dir.'/';

}

if ($action) {

	switch($action) {
	
		case 'delete':
		
		
			if (unlink($full_dir_path.'/'.$_GET['file_name'])) {
				
				$message = $file_strings['file_deleted'];
		
			} else {
			
				$message = $file_strings['file_delete_error'];
		
			}
			
		break;
		
		case 'upload':
		
			if ($_FILES['new_file']['name']=='.htaccess') {
   
			   $message = $file_strings['htaccess'];
	   
			} else {
		   
				if (copy($_FILES['new_file']['tmp_name'],$full_dir_path.'/'.$_FILES['new_file']['name'])) {
			
					$message = $file_strings['upload_success'];
				
				}else {
			
					$message = $file_strings['upload_failure'];
				
				}
				
			}
			
		break;
		
		case 'add_sub_dir':
		
			$new_sub_dir=ereg_replace("[^a-z0-9A-Z._]","",$new_sub_dir);
			
			$new_subdirectory_path = $full_dir_path.'/'.$new_sub_dir;
			
			if (!Is_Dir($new_subdirectory_path)) {

				if (mkdir($new_subdirectory_path,0777)) {
				
					$message = $file_strings['subfolder_success'];
					
				} else {
				
					$message = $file_strings['subfolder_failure'];
					
				}
		
			} else {
			
				$message = $file_strings['subfolder_exists'];
				
			}
			
		break;	
	
	} //end switch($action)
	
}	//end if($action)

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header" => "header.ihtml",
	"navigation" => "navigation.ihtml",
	"file" => "sharing/managefiles.ihtml",
	"footer" => "footer.ihtml"
));
// get details of this page, space name, module name, etc.
$page_details = get_page_details($space_key,$link_key);
$t->set_block('file', 'FileListBlock', 'FLBlock');
$t->set_block('file', 'SubDirectoryBlock', 'SDBlock');

$dir = dir($full_dir_path); 

//first get any subdirectories
while($entry=$dir->read()) {
 
	if($entry == "." || $entry == "..") { 
		
		continue; 
	
	} 

	if (is_dir("$full_dir_path/$entry")){
	
		$fp = opendir("$full_dir_path/$entry"); 
	
		if(!$fp) { 
		
			print "Bad entry: $entry<br />"; 
			continue; 
		} 
	
		//$name = fgets($fp,4096); 
		//fclose($fp); 

		if ($sub_dir==$entry) {
			
			$t->set_var('SUB_DIR',$parent_sub_dir.$entry);
				
			$t->set_var('SELECTED','selected');				  
			$t->Parse('SDBlock', 'SubDirectoryBlock', true); 

		
		} else {
			
			$t->set_var('SUB_DIR',$parent_sub_dir.$entry);
		
			$t->set_var('SELECTED','');				  
			$t->Parse('SDBlock', 'SubDirectoryBlock', true); 
		
		}
	
	}

}


//now get files

$dir = dir($full_dir_path); 

while($entry=$dir->read()) { 
	if($entry == "." || $entry == "..") { 
		continue; 
	} 

  	if (is_file("$full_dir_path/$entry")) {
	   
		$fp = @fopen("$full_dir_path/$entry","r"); 

		if(!$fp) { 
		
			print "Bad entry: $entry<br />"; 
			continue; 
	
		} 
	
		$name = fgets($fp,4096); 
		fclose($fp); 

		$t->set_var('FILE_NAME',$entry);
		$t->set_var('SUB_DIR',$sub_dir);	  
		$t->Parse('FLBlock', 'FileListBlock', true); 

	}

} 
if ($sub_dir) {

	$sub_dir_text = 'in subfolder \''.$sub_dir.'\'';
	$back_to_top = "<a href=\"managefiles.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key\">Back to top folder</a>";
	
}
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('SHARED_ITEM_NAME',$shared_item_name);

$t->set_var('SUB_FOLDER_TEXT',$sub_dir_text);
$t->set_var('SUB_DIR',$sub_dir);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SHAREDITEM_KEY',$shareditem_key);
$t->set_var('PARENT_SUB_DIR',$parent_sub_dir);
$t->set_var('BACK_TO_TOP',$back_to_top);

$t->set_var('FILES',$files);
$t->set_var('BACK_TO_TOP',$back_to_top);

$t->set_var('FILES',$files);
$t->set_var('MODULE_TEXT',$sharing_strings['shared_item']);
$t->set_var('UPLOAD_FILE_STRING',$file_strings['upload_file']);
$t->set_var('ADD_SUBFOLDER_STRING',$file_strings['add_subfolder']);
$t->set_var('ADD_SUBFOLDER_STRING',$file_strings['add_subfolder']);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('UPLOAD_STRING',$general_strings['upload']);
$t->set_var('ADD_STRING',$general_strings['add']);
$t->set_var('VIEW_STRING',$general_strings['view']);
$t->set_var('DELETE_STRING',$general_strings['delete']);
$t->set_var('SUBFOLDERS_STRING',$file_strings['subfolders']);
$t->set_var('CURRENT_FILES_STRING',$file_strings['current_files']);
$t->set_var('MANAGE_FILES_HEADING',$sharing_strings['manage_files']);
$t->parse("CONTENTS", "header", true); 
//create the left hand navigation 

get_navigation();
$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
$t->set_var('FILE_PATH',$CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$sharing_item_path);
$t->set_var('SPACE_FILE_PATH','');
$t->set_var('NAME',$name);
$t->parse('CONTENTS', 'file', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();

//output page
$t->p('CONTENTS');
exit;

?>
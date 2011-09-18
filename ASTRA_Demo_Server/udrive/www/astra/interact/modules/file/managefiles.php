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
* Manage associated files
*
* Shows files associated with a file component 
*
* @package File
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: managefiles.php,v 1.19 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/file_strings.inc.php');

//set variables
$space_key 	= get_space_key();
if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$action		= $_GET['action'];
	$sub_dir	= $_GET['sub_dir'];
	
} else {

	$module_key	 = $_POST['module_key'];
	$action		 = $_POST['action'];
	$new_sub_dir = $_POST['new_sub_dir'];	
	$sub_dir	 = $_POST['sub_dir'];	
	

}
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];

//check we have the required variables
check_variables(true,false,true);

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

$can_edit_module = check_module_edit_rights($module_key);
if ($can_edit_module==false) {

   		$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
		header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
	
}

$sql = "SELECT file_path, filename FROM {$CONFIG['DB_PREFIX']}files WHERE module_key='$module_key'";

$rs = $CONN->Execute($sql);
while (!$rs->EOF) {

	$file_path = $rs->fields[0];
	$name = $rs->fields[1];
	$rs->MoveNext();
}

$full_dir_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$file_path;

$rs->Close();

if ($sub_dir) {

	$full_dir_path = $full_dir_path.'/'.$sub_dir;
	$parent_sub_dir = $sub_dir.'/';

}

if ($action) {

	switch($action) {
	
		case 'delete':
		
		
			if (unlink($full_dir_path.'/'.$_GET['file_name'])) {
				
				$message = $file_strings['delete_success'];
		
			} else {
			
				$message = $file_strings['delete_failure'];
		
			}
			
		break;
		
		case 'upload':
		
			if ($_FILES['new_file']['name']=='.htaccess') {
   
			   $message = $file_strings['htaccess'];
	   
			} else {
			
				//remove any 'dangerous' file extensions 
				$file_name = $_FILES['new_file']['name'];
				if (preg_match("/\./",$file_name)) {
   
					$ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name);
					$ext = ".".$ext;
					$file_name=str_replace($ext,'',$file_name);
   
				} else { 
   
					$ext='';
   
				}
				
				$ext=str_replace("cgi","html",$ext);
				$ext=str_replace("pl","html",$ext);
				$ext=str_replace("phtml","html",$ext);
				$ext=str_replace("shtml","html",$ext);
				$ext=str_replace("iphp","html",$ext);   
				if ($CONFIG['ALLOW_PHP']==0) {
		
					$ext=str_replace("php","html",$ext);
			
				}
				echo " $ext";
				$file_name = $file_name.$ext;
				
				if (copy($_FILES['new_file']['tmp_name'],$full_dir_path.'/'.$file_name)) {
			
					$message = $file_strings['upload_success'];
				
				} else {
			
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
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'file'	   => 'files/managefiles.ihtml',
	'footer'	 => 'footer.ihtml'
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
	$back_to_top = "<a href=\"managefiles.php?space_key=$space_key&module_key=$module_key\">".$file_strings['back_to_top']."</a>";
	
}
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('MODULE_TEXT',$general_strings['module_text']);
$t->set_var('SUB_FOLDER_TEXT',$sub_dir_text);
$t->set_var('SUB_DIR',$sub_dir);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('PARENT_SUB_DIR',$parent_sub_dir);
$t->set_var('BACK_TO_TOP',$back_to_top);

$t->set_var('FILES',$files);
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
$t->set_var('MANAGE_FILES_STRING',$file_strings['manage_files']);

$t->parse('CONTENTS', 'header', true); 
//create the left hand navigation 

get_navigation();

$t->parse('CONTENTS', 'file', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();

//output page
$t->p('CONTENTS');

exit;

?>

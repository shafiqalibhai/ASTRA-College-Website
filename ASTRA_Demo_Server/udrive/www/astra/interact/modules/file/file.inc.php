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
* File module add/modify/delete functions file
*
* Contains the main functions for adding/modifying/deleting a 
* file module
*
* @package File
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: file.inc.php,v 1.21 2007/01/29 01:34:36 glendavies Exp $
* 
*/

/**
* Function called by Module class when adding a new file module
*
* @param  int $module_key  key of new file module
* @return true if details added successfully
*/

function add_file($module_key) {

	global $CONN, $CONFIG;
	$file_name		= $_FILES['user_file']['name'];
	$file_extension   = $_POST['file_extension'];
	$embedded		 = $_POST['embedded'];	
	$user_file		= $_FILES['user_file']['tmp_name'];
	$name		 = $_POST['name'];  
	$unzip			= $_POST['unzip'];
	$zip_start_file   = $_POST['zip_start_file'];  
 
	if ($file_extension=="other") {
   
		if (preg_match("/\./",$file_name)) {
   
			$ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name);
			$ext = ".".$ext;
   
		} else { 
   
			$ext='';
   
		}
   
	} else {
   
		$ext='.'.$file_extension;
   
	}

	if ($unzip=='true') {
		
		$newfile_name = $zip_start_file;
			
	} else {
			
		$newfile_name = $name;
		
		$ext=str_replace("cgi","html",$ext);
		$ext=str_replace("pl","html",$ext);
		$ext=str_replace("phtml","html",$ext);
		$ext=str_replace("shtml","html",$ext);
		$ext=str_replace("iphp","html",$ext);
		if ($CONFIG['ALLOW_PHP']==0) {
		
			$ext=str_replace("php","html",$ext);
			
		}
		$newfile_name=ereg_replace("[^a-z0-9A-Z._]","",$newfile_name);
		$newfile_name = substr($newfile_name,0,40);
		$newfile_name = $newfile_name.$ext;
			
	}
	   
	//create a diretcory to store the file in
	  
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		if (!mkdir($subdirectory_path,0777)) {
		 
			$message = 'There was an error adding your file';
			return $message;
						
		}
		
	} 
		
	$file_path = $subdirectory.'/'.$module_key;
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$file_path;

	if (!Is_Dir($full_file_path)) {

		if (!mkdir($full_file_path,0777)) {
			
			$message = 'There was an error adding yoru file';
			return $message;
				
		}
		
	}   
   
	if ($unzip=='true') {
			
		exec("unzip -qq -o -d \"$full_file_path\" \"$user_file\" -x \*.iphp .htaccess \*.php");
							
	} else {
		
		if (!move_uploaded_file($user_file,$full_file_path.'/'.$newfile_name)) {
				
			$message = 'There was an error adding your file';
			return $message;
					
		} 
		
	}
				
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}files(module_key,file_path,filename, embedded) VALUES ('$module_key','$file_path','$newfile_name','$embedded')";

	if ($CONN->Execute($sql) === false) {
		
		$message =  'There was an error adding your file: '.$CONN->ErrorMsg().' <br />';
		
		return $message;
				
	} else {
			
		//if ($embedded==1) {
		
			//copy($CONFIG['BASE_PATH'].'/modules/file/embedfile.iphp',$full_file_path.'/embedfile.iphp');
		
		//}
		return true;
				
	}

}

/**
* Function called by Module class to get exisiting file data 
*
* @param  int $module_key  key of file module
* @return true if data retrieved
*/

function get_file_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT filename,file_path, embedded FROM {$CONFIG['DB_PREFIX']}files WHERE module_key='$module_key'";	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {

		$module_data['file_name'] = $rs->fields[0];
		$module_data['file_path'] = $rs->fields[1];
		$module_data['embedded']  = $rs->fields[2];				
		$rs->MoveNext();
	
	}
	
	return true;

}

/**
* Function called by Module class to modify exisiting file data 
*
* @param  int $module_key  key of file module
* @param  int $link_key  link key of file module being modified
* @return true if successful
*/

function modify_file($module_key,$link_key) {

	global $CONN, $CONFIG;
	
	$file_name		= $_FILES['user_file']['name'];
	$file_extension   = $_POST['file_extension'];
	$embedded		 = $_POST['embedded'];	
	$user_file		= $_FILES['user_file']['tmp_name'];
	$name		 = $_POST['name'];  
	$unzip			= $_POST['unzip'];
	$zip_start_file   = $_POST['zip_start_file'];  	  
		
	//get the exisitng file info so we can delete it if need be
	$sql = "select file_path, filename from {$CONFIG['DB_PREFIX']}files where module_key='$module_key'";

	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

		$file_path = $rs->fields[0];
		$existing_name = $rs->fields[1];
		$rs->MoveNext();
	}

	$rs->Close();
	
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$file_path;
	
	if ($embedded==1) {
		
		copy($CONFIG['BASE_PATH'].'/modules/file/embedfile.iphp',$full_file_path.'/embedfile.iphp');
		
	}
				
	if (!$file_name) {   
	
	   $sql = "UPDATE {$CONFIG['DB_PREFIX']}files SET embedded='$embedded' WHERE module_key='$module_key'";

	   $CONN->Execute($sql);
	   return true;  

	} else {   
	

		
		$old_file = $CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$file_path.'/'.$existing_name;

		if (file_exists($old_file)) {

			unlink($old_file);

		}
		
		//set new file name and copy file to module directory
		if ($file_extension=="other") {
   
		   if (preg_match("/\./",$file_name)) {
   
			   $ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name);
			   $ext = ".".$ext;
   
		   } else { 
   
			   $ext='';
   
		   }
   
	   } else {
   
		   $ext='.'.$file_extension;
   
	   }

		if ($unzip=='true') {
		
			$newfile_name = $zip_start_file;
			
		} else {
		
			$newfile_name = $name_esc;
		
			if ($CONFIG['ALLOW_PHP']==0) {
		
				$ext=str_replace("php","html",$ext);
			
			}
		
			$ext=str_replace("cgi","html",$ext);
			$ext=str_replace("pl","html",$ext);
			$ext=str_replace("phtml","html",$ext);
			$ext=str_replace("shtml","html",$ext);
			$ext=str_replace("iphp","html",$ext);
			$newfile_name=ereg_replace("[^a-z0-9A-Z._]","",$newfile_name);
			$newfile_name = substr($newfile_name,0,40);
			$newfile_name = $newfile_name.$ext;
			
		}
		
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}files SET filename='$newfile_name', embedded='$embedded' WHERE module_key='$module_key'";

		if ($CONN->Execute($sql) === false) {

			$message =  'There was an error modifying your file: '.$CONN->ErrorMsg().' <br />';
			return $message;
			
		} else { 
	 

			if ($unzip=='true') {
			
				exec("unzip -qq -o -d \"$full_file_path\" \"$user_file\" -x \*.iphp .htaccess \*.php");
							
			} else {
			
				copy($user_file,$full_file_path.'/'.$newfile_name);
			
			}
			
			return true;  

		}
		
	}

} //end modify_file



/**
* Function called by Module class to delete exisiting file data 
*
* @param  int $module_key  key of file module
* @param  int $space_key  space key of file module
* @param  int $link_key  link key of file module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_file($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN,$CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "select file_path from {$CONFIG['DB_PREFIX']}files where module_key='$module_key'";

		$rs = $CONN->Execute($sql);

		while (!$rs->EOF) {

			$file_path = $rs->fields[0];
			$rs->MoveNext();

		}
		
		if ($CONFIG['MODULE_FILE_SAVE_PATH'] && $CONFIG['MODULE_FILE_SAVE_PATH']!='' && $file_path && $file_path!='') {
		
			$directory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$file_path;
		
			if (Is_Dir($directory_path)) {
		
				delete_directory($directory_path);		
		
			}
			
		}		
		
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}files WHERE module_key='$module_key'";
		
		$CONN->Execute($sql);		
		$rows_affected = $CONN->Affected_Rows();

		if ($rows_affected < '1') {	

			$message = "There was an problem deleting a $module_code during a module  deletion module_key=$module_key".$CONN->ErrorMsg();
			email_error($message);
			return $message;
		
		} else { 
	
			return true;
						
		}
	
	} else {	
	
		return true;
		
	}

} //end delete_file	 

/**
* Function called by Module class to flag a file for deletion 
*
* @param  int $module_key  key of file module
* @param  int $space_key  space key of file module
* @param  int $link_key  link key of file module being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_file_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_file_for_deletion   

/**
* Function called by Module class to copy a file 
*
* @param  int $existing_module_key  key of file being copied
* @param  int $existing_link_key  link key of file module being copied
* @param  int $new_module_key  key of file being created
* @param  int $new_link_key  link key of file module being created
* @param  int $module_data  array of existing module data
* @param  int $space_key  space key of file module
* @param  int $new_group_key  group key of new file
* @return true if successful
*/
function copy_file($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{

	global $CONN, $CONFIG;

	//create a diretcory to store the file in
		
	$file_name = $CONN->qstr($module_data['file_name']);
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		mkdir($subdirectory_path,0777);
		
	} 
		
	$file_path = $subdirectory.'/'.$new_module_key;
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$file_path;

	if (!Is_Dir($full_file_path)) {

		mkdir($full_file_path,0777);
		
	}   
   
	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}files(module_key,file_path,filename) VALUES ('$new_module_key','$file_path',$file_name)";

	if ($CONN->Execute($sql) === false) {
		
		$message =  'There was an error copying your file: '.$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else { 
	 
			$old_file_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/file/'.$module_data['file_path'];
			
			if (copy_directory($old_file_path,$full_file_path)) {

				return true;
				
			} else {  

				$message =  'There was an error copying your file';
				return $message;
				
			}

	}

} //end copy_file

/**
* Function called by Module class to add new file link
*
* @param  int $module_key  key of file module
* @return true if successful
*/
function add_file_link($module_key) {

	return true;

}


?>

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

* File input

*

* Displays form for inputting a file in a sharing module 

*

* @package Sharing

* @author Glen Davies <glen.davies@cce.ac.nz>

* @copyright Christchurch College of Education 2001 

* @version $Id: fileinput.php,v 1.20 2007/07/30 01:57:05 glendavies Exp $

* 

*/



/**

* Include main system config file 

*/

require_once('../../local/config.inc.php');





//get language strings



require_once($CONFIG['LANGUAGE_CPATH'].'/sharing_strings.inc.php');



//set variables

if ($_SERVER['REQUEST_METHOD']=='POST') {

	

	$module_key	 = $_POST['module_key'];

	$shareditem_key = $_POST['shareditem_key'];

	$action		 = $_POST['action'];		

	

} else {

 

	$module_key	 = $_GET['module_key']; 

	$shareditem_key = $_GET['shareditem_key']; 	   

	$action		 = $_GET['action'];	

	

}

$current_user_key = $_SESSION['current_user_key'];

$space_key 	= get_space_key();

$link_key 	= get_link_key($module_key,$space_key);



//check we have the required variables

check_variables(true,true,true);



//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate();

$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];







//find out what action we need to take





if (isset($action)) {



	switch($action) {



		//if we are adding a new folder  form input needs to be checked 

		case add:

		

			$errors = check_form_input();



		//if there are no errors then add the data

		

			if(count($errors) == 0) {



				$message = add_file();



				//if the add was successful return the browser to space home or parent folder

			

				if ($message===true) {

			

					$message = urlencode($sharing_strings['file_added']);

					Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");

					exit;

				

				} else { 

				

					$button = $general_strings['add'];

					

				}



		//if the add wasn't succesful return to form with error message



			} else {

		

				$button = $general_strings['add'];

					

			}

			

		break;

		

		case modify:

		

			$sql = "SELECT {$CONFIG['DB_PREFIX']}shared_items.name,{$CONFIG['DB_PREFIX']}shared_items.description from {$CONFIG['DB_PREFIX']}shared_items WHERE shared_item_key='$shareditem_key'";

			

			$rs = $CONN->Execute($sql);

			

			while (!$rs->EOF) {



				$name = $rs->fields[0];

				$description = $rs->fields[1];

				$file_path = $rs->fields[2];

				$existing_file_name = $rs->fields[3];

				$rs->MoveNext();

			

			}

		

		$rs->Close();

		

		break;

 

		case modify2:

		

		switch($_POST['submit']) {



			//if deleting, then delete link

			

			case $general_strings['delete']:

			

				$message=delete_file();



				 //return browser to sharing space 

				if ($message=='true') {

					

					$message = urlencode($sharing_strings['file_deleted']);

					Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");

					exit;

				}

			 break;



			//if modifying then modify folder



			case $general_strings['modify']:

			

				$errors = check_form_input();

		

				if(count($errors) == 0) {

				

					$message = modify_file();



					//return browser to sharing space



					if ($message=='true') {

 

						 $message = urlencode($sharing_strings['file_modified']);

						 Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");

						 exit;

						 

					} 

						   

				}

			

			

			break;

			

			} //end switch $submit

	  



		} //end switch $link_action





} //end if (isset($link_action))





if (!isset($action)) {

	

	$action = 'add';

	$button = $general_strings['add'];



}

if ($action=='modify'|$action=='modify2') {

	

	$action = 'modify2';

	$button = $general_strings['modify'];

	$warning=$general_strings['check'];

	$delete_button = "<input type=\"submit\" name=\"submit\" value=\"".$general_strings['delete']."\" onClick=\"return confirmDelete('$warning')\">";



}





//format any errors from form submission



$name_error = sprint_error($errors['name']);

$file_error = sprint_error($errors['file']);

$file_type_error = sprint_error($errors['file_type']);



//get the required template files



require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(

	'header'		  => 'header.ihtml',

	'navigation'	  => 'navigation.ihtml',

	'form'			=> 'sharing/fileinput.ihtml',

	'footer'		  => "footer.ihtml"));



//generate the header,title, breadcrumb details



$page_details = get_page_details($space_key,$link_key);



if (!isset($action) | $action=='add') {

 

	$t->set_block('form', 'ManageFilesBlock', 'MFlock');

	$t->set_var('MFlock', '');

	

} else {



//for backwards compatibiltiy make sure we have a sharing item subdirectory

//before showing 'manage associated files' option



	$sql = "SELECT {$CONFIG['DB_PREFIX']}shared_items.file_path FROM {$CONFIG['DB_PREFIX']}shared_items WHERE shared_item_key='$shareditem_key'";

	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {

	

		$shared_item_path = $rs->fields[0];

		$rs->MoveNext();	

	}

	

	if (!$shared_item_path | $shared_item_path='') {

	

		$t->set_block('form', 'ManageFilesBlock', 'MFlock');

		$t->set_var('MFlock', '');

		

	}



}

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);





$t->set_var('NAME_ERROR',$name_error);

$t->set_var('USER_FILE_ERROR',$file_error);

$t->set_var('FILE_TYPE_ERROR',$file_type_error);

$t->set_var('NAME',$name);

$t->set_var('DESCRIPTION',$description);

$t->set_var('ACTION',$action);

$t->set_var('EXISTING_FILE_NAME',$existing_file_name);

$t->set_var('BUTTON',$button);

$t->set_var('DELETE_BUTTON',$delete_button);

$t->set_var('SPACE_KEY',$space_key);

$t->set_var('MODULE_KEY',$module_key);

$t->set_var('SHAREDITEM_KEY',$shareditem_key);

$t->set_var('SCRIPT_BODY','onUnload="closeWin()"');

$t->set_var('MAX_FILE_UPLOAD_SIZE',$CONFIG['MAX_FILE_UPLOAD_SIZE']);

$t->set_var('FILE_STRING',$general_strings['file']);

$t->set_var('FILE_TYPE_STRING',$general_strings['file_type']);

$t->set_var('CANCEL_STRING',$general_strings['cancel']);

$t->set_var('NAME_STRING',$general_strings['name']);

$t->set_var('DESCRIPTION_STRING',$general_strings['description']);

$t->set_var('OPTIONAL_STRING',$general_strings['optional']);

$t->set_var('ZIP_STRING',$sharing_strings['zip_options']);

$t->set_var('UNZIP_STRING',$sharing_strings['unzip']);

$t->set_var('START_FILE_STRING',$sharing_strings['start_file']);

$t->set_var('ASSOCIATED_STRING',$sharing_strings['associated']);

$t->set_var('ASSOCIATED_EXAMPLE',$sharing_strings['associated_example']);

$t->set_var('ADD_FILE_HEADING',sprintf($sharing_strings['add_file_heading'],$page_details['module_name']));

 

$t->parse('CONTENTS', 'header', true); 



//generate the navigation menu



get_navigation();

$t->parse('CONTENTS', 'form', true);





$t->parse('CONTENTS', 'footer', true);

print_headers();



//output page



$t->p('CONTENTS');

$CONN->Close();

exit;







/**

* Add a file to a sharing space

* 

* @return true

*/

function add_file()

{



	global $CONN,$sharing_strings, $CONFIG;



	$file_name 				= $_FILES['user_file']['name'];

	$file_extension 		= $_POST['file_extension'];

	$user_file				= $_FILES['user_file']['tmp_name'];

	$name	 				= $_POST['name'];

	$name 				=$_POST['name'];

	$description 		= $_POST['description'];

	$module_key				= $_POST['module_key'];

	$unzip				  = $_POST['unzip'];

	$zip_start_file		 = $_POST['zip_start_file'];  		

	$current_user_firstname = $_SESSION['current_user_firstname'];

	$current_user_lastname 	= $_SESSION['current_user_lastname'];

	$current_user_key 		= $_SESSION['current_user_key'];	

	$date_added				= $CONN->DBDate(date('Y-m-d H:i:s'));				

	$file_path 				= get_sharing_file_path($module_key);

	

	if ($unzip=='true') {

		

		$newfile_name = $zip_start_file;

		

	} else {

	

		if (preg_match("/\./",$file_name)) {

		

			   $ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name);

			   $file_name = ereg_replace("\.$ext", '', $file_name);			   

			   $ext = '.'.$ext;

			   

		} else { 

		

			$ext='';

		

		}

		

		if ($file_extension!='other') {		



	 		$ext='.'.$file_extension;	



		}



	//replace any dangerous extensions



	

		$ext=str_replace('.php','.html',$ext);

		$ext=str_replace('.cgi','.html',$ext);

		$ext=str_replace('.pl','.html',$ext);

		$ext=str_replace('.phtml','.html',$ext);

		$ext=str_replace('.shtml','.html',$ext);

		$newfile_name=ereg_replace('[^a-z0-9A-Z._]','',$file_name);

		$newfile_name = substr($newfile_name,0,45);

		$newfile_name=$newfile_name.$ext;

		

	}

	

	$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}shared_items(module_key, user_key, date_added, name, description, filename) VALUES ('$module_key','$current_user_key',$date_added,'$name','$description','$newfile_name')";



	if ($CONN->Execute($sql) === false) {

	

		$message =  'There was an error adding your file: '.$CONN->ErrorMsg().' <br />';

		return $message;

		

	} else { 

	

		$shared_item_key = $CONN->Insert_ID();

		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}shared_items SET file_path='$shared_item_key' WHERE shared_item_key='$shared_item_key'");		

		$save_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$file_path.'/'.$shared_item_key;			

		

		if (!Is_Dir($save_path)) {



  

			if (!mkdir($save_path,0777)) {

			

				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}shared_items WHERE shared_item_key='$shared_item_key'");

				return $sharing_strings['upload_error'];

				

			}

		

		}

		   

		if ($unzip=='true') {

			

			exec("unzip -qq -o -d \"$save_path\" \"$user_file\" -x .htaccess");

			return true;

							

		} else {

		

			if (!copy($user_file,$save_path.'/'.$newfile_name)) {

			

				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}shared_items WHERE shared_item_key='$shared_item_key'");

				return $sharing_strings['upload_error'];

			

			} else {

			

				statistics('post');

				return true;  

			

			}

			

		}

		

		//update statistics 

		



	}

   

} //end add_file



/**

* Modify A file 

* 

*  

* @return true

*/



function modify_file()

{

   global $CONN,$CONFIG;



	$file_name 				= $_FILES['user_file']['name'];

	$file_extension 		= $_POST['file_extension'];

	$user_file				= $_FILES['user_file']['tmp_name'];

	$name	 				= $_POST['name'];

	$existing_file_name		= $_POST['existing_file_name'];

	$unzip				  = $_POST['unzip'];

	$zip_start_file		 = $_POST['zip_start_file'];  	

	$name 				= $_POST['name'];

	$description 		= $_POST['description'];

	$module_key				= $_POST['module_key'];

	$shareditem_key			= $_POST['shareditem_key'];		

	$current_user_firstname = $_SESSION['current_user_firstname'];

	$current_user_lastname 	= $_SESSION['current_user_lastname'];

	$current_user_key 		= $_SESSION['current_user_key'];	

	$file_path 				= get_sharing_file_path($module_key);

	

	if (!$file_name) {



		$sql = "UPDATE {$CONFIG['DB_PREFIX']}shared_items SET name='$name',description='$description' WHERE shared_item_key='$shareditem_key'";



	} else {



		if ($unzip=='true') {

		

			$newfile_name = $zip_start_file;

		

		} else {

	

			if (preg_match("/\./",$file_name)) {

		

				$ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name);

				$file_name = ereg_replace("\.$ext", '', $file_name);			   

				$ext = '.'.$ext;

			   

			} else { 

		

				$ext='';

		

			}

		

			if ($file_extension!='other') {		



	 			$ext='.'.$file_extension;	



			}



			//replace any dangerous extensions



			$ext=str_replace('.php','.html',$ext);

			$ext=str_replace('.cgi','.html',$ext);

			$ext=str_replace('.pl','.html',$ext);

			$ext=str_replace('.phtml','.html',$ext);

			$ext=str_replace('.shtml','.html',$ext);

			$newfile_name=ereg_replace('[^a-z0-9A-Z._]','',$file_name);

			$newfile_name = substr($newfile_name,0,45);

			$newfile_name=$newfile_name.$ext;

		

		}



		$sql = "UPDATE {$CONFIG['DB_PREFIX']}shared_items set name='$name',description='$description',filename='$newfile_name' WHERE shared_item_key='$shareditem_key'";



	}



	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}shared_items.filename,{$CONFIG['DB_PREFIX']}shared_items.file_path,{$CONFIG['DB_PREFIX']}sharing_settings.file_path FROM {$CONFIG['DB_PREFIX']}shared_items, {$CONFIG['DB_PREFIX']}sharing_settings WHERE {$CONFIG['DB_PREFIX']}sharing_settings.module_key={$CONFIG['DB_PREFIX']}shared_items.module_key AND {$CONFIG['DB_PREFIX']}shared_items.shared_item_key='$shareditem_key'");

			

	while(!$rs->EOF) {

			

		$existing_file_name = $rs->fields[0];

		$sharing_item_path  = $rs->fields[1];

		$module_path  = $rs->fields[2];				

		$rs->MoveNext();

				

	}

	

	if ($CONN->Execute($sql) === false) {



		$message =  'There was an error modifying your file: '.$CONN->ErrorMsg().' <br />';

		return  $message;



	} else { 



		if (!$file_name) {



			return true;



		} else {



			if ($sharing_item_path==0 | $sharing_item_path=='') {			



				$old_file = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$existing_file_name;

				$save_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path; 				

				

			} else {

			

				$old_file = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$sharing_item_path.'/'.$existing_file_name;

				$save_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$sharing_item_path;				

				

			}



			if (file_exists($old_file)) {

			

				unlink($old_file);

			

			}

			

			if ($unzip=='true') {

			

				exec("unzip -qq -o -d \"$save_path\" \"$user_file\" -x .htaccess");

							

			} else {

		

				copy($user_file,$save_path."/".$newfile_name);

			

			}

			return true;

	   

		}

				

	}  

		



} //end modify file



/**

* delete file 

* 

*  

* @return true

*/

function delete_file()

{

	global $CONN, $CONFIG;

	

	$existing_file_name		= $_POST['existing_file_name'];

	$module_key				= $_POST['module_key'];

	$shareditem_key			= $_POST['shareditem_key'];

	$file_path 				= get_sharing_file_path($module_key);	

		

	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}shared_items.filename,{$CONFIG['DB_PREFIX']}shared_items.file_path,{$CONFIG['DB_PREFIX']}sharing_settings.file_path FROM {$CONFIG['DB_PREFIX']}shared_items, {$CONFIG['DB_PREFIX']}sharing_settings WHERE {$CONFIG['DB_PREFIX']}sharing_settings.module_key={$CONFIG['DB_PREFIX']}shared_items.module_key AND {$CONFIG['DB_PREFIX']}shared_items.shared_item_key='$shareditem_key'");

			

	while(!$rs->EOF) {

			

		$existing_file_name = $rs->fields[0];

		$sharing_item_path  = $rs->fields[1];

		$module_path  = $rs->fields[2];				

		$rs->MoveNext();

				

	}

	

	$sql="DELETE FROM {$CONFIG['DB_PREFIX']}shared_items where shared_item_key='$shareditem_key'";

	$CONN->Execute($sql);

	$rows_affected = $CONN->Affected_Rows();



	if ($rows_affected < 1) {   



	   $message = "There was an error deleting a file from a shared space - ".$CONN->ErrorMsg();

	   email_error($message);

	   return $message;



	} else {







		if ($sharing_item_path==0 || $sharing_item_path=='') {			



			$full_file_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$existing_file_name;



			if (file_exists($full_file_path)) {



				if (unlink($full_file_path)!=true) {



					$message = "There was an error deleting a file from a sharing space$full_file_path";

					email_error($message);



				}



			}

			

		} else if ($module_path && $sharing_item_path) {

		

		   $full_file_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/sharing/'.$module_path.'/'.$sharing_item_path;

		   

		   if (Is_Dir($full_file_path)) {

		

			   delete_directory($full_file_path);		

		

		   }

		   

	   }			   		



		$sql="delete from {$CONFIG['DB_PREFIX']}shared_item_comments where shared_item_key='$shareditem_key'";



		if ($CONN->Execute($sql) === false) {   



			$message = "There was a problem deleting comments from a shared item - ".$CONN->ErrorMsg();

			email_error($message);

			return $message;



		} else {



			return true;



		}



	}



} //end delete_file



/**

* Check Form Input 

* 

*  

* @return $errors

*/

function check_form_input() 

{

	global $general_strings;

	$name 			= $_POST['name'];

	$action			= $_POST['action'];

	$user_file_name	= $_FILES['user_file']['name'];

	$file_extension = $_POST['file_extension'];

	// Initialize the errors array



	$errors = array();



	



	//check to see if we have all the information we need

	if(!$name) {



		$errors['name'] = $general_strings['no_name'];



	}

	

	if ($action=='add' | ($action=='modify2' && $user_file_name!='')) {

		

		$check_file_ok = check_file_upload('user_file');

   

		if ($check_file_ok!='true') {

   

		   $errors['file'] = $check_file_ok;

	   

		}

  

  		if(!$file_extension) {



			$errors['file_type'] = $general_strings['no_file_type'];

	   

   		}

		

	}



return $errors;

} //end check_form_input



function get_sharing_file_path($module_key) 

{

	global $CONN, $CONFIG;

	

	$sql = "SELECT file_path FROM {$CONFIG['DB_PREFIX']}sharing_settings WHERE module_key='$module_key'";

   	$rs = $CONN->Execute($sql);



   	while (!$rs->EOF) {



	   	$file_path = $rs->fields[0];

	   	$rs->MoveNext();



   	}

	

	$rs->Close();



	return $file_path;

	

} //end get_sharing_file_path

?>
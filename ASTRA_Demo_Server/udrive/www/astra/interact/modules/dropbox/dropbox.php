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
* Dropbox module
*
* Displays the dropbox module start page 
*
* @package Dropbox
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: dropbox.php,v 1.39 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/dropbox_strings.inc.php');

//set variables



if ($_GET['module_key']) {
	
	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];
	$action		= $_GET['action'];

} else {
	
	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];
	$action		= $_POST['action'];
	$submit		= $_POST['submit'];		
	
}
$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);
$current_user_key = $_SESSION['current_user_key'];

//check we have the required variables

check_variables(true,true,true);


//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$page_details = get_page_details($space_key,$link_key);
if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}

//update statistics 

if (!$action && !$message) {

	statistics('read');
	
}

$domname=ereg_replace("https?://([^/]+)","\\1",$CONFIG['SERVER_URL']);
	

//find out if this is a timed dropbox
$sql_type = "SELECT type_key, time_allowed FROM {$CONFIG['DB_PREFIX']}dropbox_settings WHERE module_key='$module_key'";	
	
$rs = $CONN->Execute($sql_type);
	
while (!$rs->EOF) {

	$dropbox_type_key = $rs->fields[0];
	$time_allowed = $rs->fields[1];
	$rs->MoveNext();
	
}


if (isset($action)){

	switch($action) {


		//if we are adding a new file  form input needs to be checked 
		
		case add:
		
			$errors = check_form_input();

		//if there are no errors then add the data
		
			if(count($errors) == 0) {
			
			
				$message = add_file();

				//if the add was successful return the browser to space home or parent folder
			
				if ($message===true) {
				
					statistics('post');
					
					$message = urlencode($dropbox_strings['upload_success']);
					header("Location: {$CONFIG['FULL_URL']}/modules/dropbox/dropbox.php?space_key=$space_key&module_key=$module_key&message=$message");
					exit;
			
				} else {
			
					echo $message;
				
				} 

			} else {
			
				$message = $dropbox_strings['upload_fail'];
			}
		
		break;
   
		case action_marked:
		
			switch ($submit) {
			
				case $dropbox_strings['delete_button']:
				
					$message = delete_file();
					$action='add';
		
					if ($message===true) {
			
						$message = $dropbox_strings['delete_success'];
		
					}
					
				break;

				case $dropbox_strings['zip_button']:
				
					$message = download_files();
					$action='add';
					
				break;
				
			}
							
		
		break;
	
	}

} else {

  $action = 'add';

}

$description_error = sprint_error($errors['description']);
$file_error = sprint_error($errors['file']);
$file_type_error = sprint_error($errors['file_type']);

//see if user has admin rights. If yes trhen show all dropbox files

$is_admin = check_module_edit_rights($module_key);

if ($is_admin===true) { 

	$sql = "SELECT file_key,description,{$CONFIG['DB_PREFIX']}dropbox_file_status.name, first_name,last_name,{$CONFIG['DB_PREFIX']}dropbox_files.date_added,comments, {$CONFIG['DB_PREFIX']}dropbox_settings.file_path, filename,date_status_changed FROM {$CONFIG['DB_PREFIX']}dropbox_files,{$CONFIG['DB_PREFIX']}dropbox_settings,{$CONFIG['DB_PREFIX']}dropbox_file_status,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}dropbox_settings.module_key={$CONFIG['DB_PREFIX']}dropbox_files.module_key AND  {$CONFIG['DB_PREFIX']}dropbox_files.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}dropbox_files.status={$CONFIG['DB_PREFIX']}dropbox_file_status.status_key AND ({$CONFIG['DB_PREFIX']}dropbox_files.module_key='$module_key') ORDER BY last_name, first_name, {$CONFIG['DB_PREFIX']}dropbox_files.date_added DESC";
	
	 $summary_link = "<a href=\"summary.php?space_key=$space_key&module_key=$module_key\">".$dropbox_strings['summary_link'].'</a>';
 

} else {

	$sql = "SELECT file_key,description,{$CONFIG['DB_PREFIX']}dropbox_file_status.name,first_name,last_name,{$CONFIG['DB_PREFIX']}dropbox_files.date_added,comments, {$CONFIG['DB_PREFIX']}dropbox_settings.file_path, filename, date_status_changed FROM {$CONFIG['DB_PREFIX']}dropbox_files,{$CONFIG['DB_PREFIX']}dropbox_file_status,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}dropbox_settings WHERE {$CONFIG['DB_PREFIX']}dropbox_settings.module_key={$CONFIG['DB_PREFIX']}dropbox_files.module_key AND  {$CONFIG['DB_PREFIX']}dropbox_files.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}dropbox_files.status={$CONFIG['DB_PREFIX']}dropbox_file_status.status_key AND ({$CONFIG['DB_PREFIX']}dropbox_files.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}dropbox_files.user_key='$current_user_key') ORDER BY  {$CONFIG['DB_PREFIX']}dropbox_files.date_added DESC";
 
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);
  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'dropbox'		 => 'dropboxes/dropbox.ihtml',
	'uploadform'	  => 'dropboxes/upload_form.ihtml',
	'dropboxitems'	=> 'dropboxes/dropbox_items'.($dropbox_type_key=='3'?'_audio':'').'.ihtml',
	'dropboxusers'	=> 'dropboxes/dropbox_users.ihtml',
	'footer'		  => 'footer.ihtml'
));

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_block('uploadform', 'TimedBlock', 'TBlock');








if ($dropbox_type_key=='2') {

	
	$t->set_var('TIMED_DROPBOX_STRING',sprintf($dropbox_strings['explain_timed'],$time_allowed) );
	$t->set_var('DOWNLOAD_FILE_STRING',$dropbox_strings['download_file']);
	
	$t->Parse('TBlock', 'TimedBlock', true);
	$seconds = $time_allowed*60;
	$t->set_var('SECONDS_LEFT',$seconds);
	$t->set_var('TIME_LEFT_STRING',$dropbox_strings['time_left']);
	$t->set_var('TIME_UP_STRING',$dropbox_strings['time_up']);
	$t->set_var('DROPBOX_NAME',$page_details['module_name']);
	$t->set_var('10_MINUTES_LEFT_STRING',$dropbox_strings['10_left']);	

} else {

	$t->set_var('TBlock','');

}

if ($dropbox_type_key=='3') {

	if(isset($CONFIG['FLASH_COM'])) {

	$t->set_var('SCRIPT_INCLUDES','
<script type="text/javascript" language="javascript">function setURL(f_url) {
document.getElementById("f_url").value=f_url;}
</script>',true);
	

	$filepath=$domname.'/modules/dropbox/'.get_dropbox_file_path($module_key).'/';
	
	$filerecordpath='rtmp://'.$domname.'/recordings/'.$filepath;
	$filedownloadpath=$CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'].'recordings/'.$filepath;
	$AVPLAYER=$CONFIG['PATH'].'/includes/players/AVPLAYER.swf';
	
 	if ($_POST['link_url']) {
 		$rstatus='stopped';
 		$filename=basename($_POST['link_url'],'.flv');
		$t->set_var('URL',$_POST['link_url']);

 	} else {
		$rstatus='new';
		$filename=uniqid();
	}
	
	$rec_obj='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="214" height="137" id="recorder" align="middle">
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="movie" value="'.$CONFIG['PATH'].'/includes/players/recorder.swf?filerecordpath='.$filerecordpath.'&filedownloadpath='.$filedownloadpath.'&filename='.$filename.'&AVPLAYER='.$AVPLAYER.'&rstatus='.$rstatus.'" />
		<param name="quality" value="high" />
		<param name="bgcolor" value="#cccccc" />
		<embed src="'.$CONFIG['PATH'].'/includes/players/recorder.swf?filerecordpath='.$filerecordpath.'&filedownloadpath='.$filedownloadpath.'&filename='.$filename.'&AVPLAYER='.$AVPLAYER.'&rstatus='.$rstatus.'" quality="high" bgcolor="#cccccc" width="214" height="137" name="recorder" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
	$t->set_var('RECORDER_OBJECT',$rec_obj);
	} else {
		$t->set_block('uploadform','TYPE3');
		$t->set_var('TYPE3','Error:  No Flashcom server config for audio recording stream!');
	}

	$t->set_block('uploadform','TYPE1');
	$t->set_var('TYPE1','');

} else {
	$t->set_var('SUBMIT_ONCLICK','onclick="		openWin(\'{PATH}/includes/upload/progressbar.htm\');"');
	$t->set_block('uploadform','TYPE3');
	$t->set_var('TYPE3','');
}


$t->set_var('SCRIPT_BODY','onUnload="closeWin()"');
$t->parse('CONTENTS', 'header', true); 
$t->set_var('FILE_DESCRIPTION',$_POST['description']);
$t->set_var('DESCRIPTION_STRING',$dropbox_strings['brief_description']);
$t->set_var('EXAMPLE_STRING',$dropbox_strings['description_example']);
$t->set_var('FILE_STRING',$general_strings['file']);
$t->set_var('FILE_TYPE_STRING',$general_strings['file_type']);
$t->set_var('FILE_TYPE_TEXT',$dropbox_strings['file_type_text']);
$t->set_var('DECLARATION_TEXT',$dropbox_strings['declaration_text']);
$t->set_var('NEW_FILE_STRING',($dropbox_type_key=='3'?'Record Audio':$dropbox_strings['new_file']));
$t->set_var('SUBMIT_STRING',$general_strings['submit']);
$t->set_var("SUMMARY_LINK","$summary_link");
$t->set_var('FILES_HEADING',sprintf($dropbox_strings['files_heading'],$page_details['module_name']));
$t->set_var('MAX_FILE_UPLOAD_SIZE',$CONFIG['MAX_FILE_UPLOAD_SIZE']);
$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);

get_navigation();

$rs = $CONN->Execute($sql);

if (!$rs->EOF) {

	$delete_button = "<input type=\"submit\" name=\"submit\" value=\"".$dropbox_strings['delete_button']."\" onClick=\"return confirmDelete('".$general_strings['delete_warning']."')\">";
	$zip_button = "<input type=\"submit\" name=\"submit\" value=\"".$dropbox_strings['zip_button']."\" onClick=\"return confirmDelete('".$general_strings['delete_warning']."')\">";	
	$t->set_var('DELETE_BUTTON',$delete_button);
	$t->set_var('ZIP_BUTTON',$zip_button);
	$t->set_var('UPLOADED_BY_STRING',$dropbox_strings['uploaded_by']);
	$t->set_var('COMMENTS_STRING',$general_strings['comments']);
	$t->set_var('STATUS_STRING',$general_strings['status']);
	
	
	
}

$n=1;

$filedownloadpath=$CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'].'recordings/'.$domname.'/modules/dropbox/';

while (!$rs->EOF) {

	$file_key = $rs->fields[0];
	$description2 = $rs->fields[1];
	$status = $rs->fields[2];
	$username = $rs->fields[3].' '.$rs->fields[4];
	$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[5]),'short', true);	
	$new_date = date('Y-m-d H:i', $CONN->UnixTimestamp($rs->fields[5]));
	$comments = $rs->fields[6];
	
	
if ($dropbox_type_key=='3') {
	$file_url=$filedownloadpath.$rs->fields[7].'/'.$rs->fields[8].'.flv';
} else {
	$file_url = $CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/dropbox/'.$rs->fields[7].'/'.$rs->fields[8];
}
	$date_status_changed = date('Y-m-d H:i', $CONN->UnixTimestamp($rs->fields[9]));

	if ($username!=$new_name) {
	
		$new_name=$username;
		$t->set_var('USER_NAME',$username);
		$t->set_var('CLASS','');
		$t->parse('DROPBOX_ITEMS', 'dropboxusers', true);

	}

	if ($last_use < $new_date && $date_status_changed < $last_use) {

		$t->set_var('NEW',$general_strings['new']);

	} else {

		$t->set_var('NEW','');

	}

	$t->set_var('FILE_KEY',$file_key);
	$t->set_var('URL',$file_url);
	$t->set_var('DESCRIPTION2',$description2);
	$t->set_var('STATUS',$status);
	$t->set_var('USER_NAME',$username);
	$t->set_var('DATE_ADDED',$date_added);
	
	if ($status=='Submitted' || $status=='Resubmit required') {
	
		$t->set_var('STATUS_CLASS','Red');
	
	} else {
	
		$t->set_var('STATUS_CLASS','Green');
	
	}
	
	if ($is_admin===true) {
	
		$mark_link=" - <a href=\"{$CONFIG['PATH']}/modules/dropbox/mark.php?space_key=$space_key&module_key=$module_key&file_key=$file_key\">".$dropbox_strings['mark_file']."</a>";
		
		if ($dropbox_type_key==2) {
		
			//calculate the tim etaken to complete
			$rs_time = $CONN->Execute("SELECT TimeDownLoaded, time_uploaded FROM {$CONFIG['DB_PREFIX']}dropbox_download_links WHERE module_key='$module_key' AND user_key='$current_user_key'");
			while(!$rs_time->EOF) {
			
				$time_downloaded = $rs_time->fields[0];
				$time_uploaded   = $rs_time->fields[1];
				$rs_time->MoveNext();
				
			}
			
			$str_start = strtotime($time_downloaded); // The start date becomes a timestamp 
			$str_end = strtotime($time_uploaded); // The end date becomes a timestamp 
			$time_taken = round(($str_end-$str_start)/60,0);
			$t->set_var('TIME_TAKEN',$time_taken.' '.$dropbox_strings['minutes']);
			$t->set_var('TIME_TAKEN_STRING',$dropbox_strings['time_taken']);			
		
		}
	
	} else {
	
		$mark_link = '';
	
	}
		
	$t->set_var('MARK_LINK',$mark_link);

	$evenodd = $n % 2; 
	
	if($evenodd) { 
	
		$class = 'sandybackground';
		 
	} else {
	 
		$class = 'goldbackground';
	
	}

	if ($comments!='') {
	
		$t->set_var('COMMENTS',$comments);
	
	} else {
	   
	   $t->set_var("COMMENTS",$dropbox_strings['no_comments']);  
	}
	
	$t->set_var('CLASS',$class);
	$t->parse('DROPBOX_ITEMS', 'dropboxitems', true);
	$n++;
	$rs->MoveNext();

}

$rs->Close();

$t->set_var('DESCRIPTION_ERROR',$description_error);
$t->set_var('FILE_ERROR',$file_error);
$t->set_var('FILE_TYPE_ERROR',$file_type_error);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('ACTION',$action);

if ($page_details[module_status_key]=='3') {

	
	$t->set_var('UPLOAD_FORM','<p>'.$dropbox_strings['closed'].'</p>');

} else {

	$t->parse('UPLOAD_FORM', 'uploadform', true);

}

$t->parse('CONTENTS', 'dropbox', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();

exit;

/**
* Adds a file to the dropbox
*
* @return true if successful
*/
function add_file()
{
	global $CONN,$space_key, $module_key, $page_details, $dropbox_strings, $CONFIG, $objDates, $dropbox_type_key;

	$description		= $_POST['description'];
	$file_path			  = get_dropbox_file_path($module_key);
	
	$current_user_key	   = $_SESSION['current_user_key'];
	$current_user_firstname = $_SESSION['current_user_firstname'];
	$current_user_lastname  = $_SESSION['current_user_lastname'];
	$date_added			 = $CONN->DBDate(date('Y-m-d H:i:s'));
			
	if (!$_POST['status_key']) {
			   
		$status_key='1';
				
	}		
	if ($dropbox_type_key==3) { 

		$upload_filename=$description;
		$newfile_name=basename($_POST['link_url'],'.flv');

	} else {
		$file_name			  = $_FILES['user_file']['name'];
		$file_extension		 = $_POST['file_extension'];
		$user_file			  = $_FILES['user_file']['tmp_name'];
		if ($dropbox_type_key==2) { 
		
			$newfile_name = $file_name;
			$time_uploaded = $CONN->DBDate(date('Y-m-d H:i:s'));
			//update time uploaded details
			$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}dropbox_download_links SET time_uploaded=$time_uploaded WHERE module_key='$module_key' AND user_key='$current_user_key'");
			
		} else {
		
			if ($file_extension=='other') {
		
				if (preg_match("/\./",$file_name)) {
				   
					$ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name);
					$ext = '.'.$ext;
			   
				} else { 
				   
					$ext='';
			   
				}
		   
		   } else {
	   
			   $ext='.'.$file_extension;
	   
		   }
	   
		   //add random number to file for security
	   
		   srand ((float) microtime() * 1000000);
		   $randval = rand();
		   $newfile_name = $current_user_firstname."_".$current_user_lastname."_".$description."_".$randval;
		   //$newfile_name=strtolower($newfile_name);
		   $newfile_name=str_replace(".php",".html",$newfile_name);
		   $newfile_name=str_replace(".cgi",".html",$newfile_name);
		   $newfile_name=str_replace(".pl",".html",$newfile_name);
		   $newfile_name=str_replace(".phtml",".html",$newfile_name);
		   $newfile_name=str_replace(".shtml",".html",$newfile_name);
		   $newfile_name=ereg_replace("[^a-z0-9A-Z._]","",$newfile_name);
		   $newfile_name = substr($newfile_name,0,45);
		   $newfile_name = $newfile_name.$ext;
			
	   }
	   
	   	$save_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$file_path;			

		if (!move_uploaded_file($user_file,$save_path.'/'.$newfile_name)){
		
			$message =  $dropbox_strings['upload_fail'];
			echo $message;
			return $message;
		}
		$upload_filename=$_FILES['user_file']['name'];
	}
   
   $sql = "insert into {$CONFIG['DB_PREFIX']}dropbox_files(module_key,user_key,date_added,status,description, filename) values ('$module_key','$current_user_key',$date_added,'$status_key','$description','$newfile_name')";


	
	if ($CONN->Execute($sql) === false) {
		
		unlink($save_path.'/'.$newfile_name);
		$message =  $dropbox_strings['upload_fail'].$CONN->ErrorMsg().' <br />';
		return $message;
		
	} else { 

		require_once('../../includes/email.inc.php');

		$date_added = $objDates->formatDate(time(),'short');
		$mail_subject = $page_details['space_short_name'].' - '.$dropbox_strings['email_subject'];
		$mail_body = $dropbox_strings['email_body'].$page_details['module_name'].' in '.$page_details['space_short_name'].' - '.$page_details['space_name']."\n\nDate uploaded  = $date_added\n\nfilename = ".$upload_filename;
		$from_email = $CONFIG['ERROR_EMAIL'];
		$to_email = $_SESSION['current_user_email'];
		email_users($mail_subject, $mail_body, $_SESSION['current_user_key'], '', '', '');
		return true;
			
	}
   
} //end add_file

/**
* Delete a file from dropbox
* 
* @return true
*/
function delete_file()
{
	global $CONN,$module_key, $CONFIG, $dropbox_type_key, $domname;
	
	$files_selected = $_POST['files_selected'];
	
	if (!$files_selected) {
	
		$message = $dropbox_strings['no_files_selected'];
		return $message;
		
	} else {
	
	   $file_path = get_dropbox_file_path($module_key);
	   $num_selected = count($files_selected);
	   
	   for ($c=0; $c < $num_selected; $c++) {
	   
		   $sql = "SELECT filename FROM {$CONFIG['DB_PREFIX']}dropbox_files WHERE file_key='$files_selected[$c]'";
		   $rs = $CONN->Execute($sql);
		   
		   while (!$rs->EOF) {
		   
			   $name = $rs->fields[0];
			   $rs->MoveNext();
			   
		   }
		   
if($dropbox_type_key=='3') {
$full_file_path=$CONFIG['FLASH_COM'].'recordings/streams/'.$domname.'/modules/dropbox/'.$file_path.'/'.$name;
		   if (file_exists($full_file_path.'.flv')) {
		   
			   unlink($full_file_path.'.flv');
			   unlink($full_file_path.'.idx');
			   
		   }
} else {
		   $full_file_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$file_path.'/'.$name;
		   if (file_exists($full_file_path)) {
		   
			   unlink($full_file_path);
			   
		   }

		}   
		   
		   $sql = "DELETE FROM {$CONFIG['DB_PREFIX']}dropbox_files WHERE file_key='$files_selected[$c]'";
		   $CONN->Execute($sql);
		   
		}
		
	} //end else

	return true;
} //end delete_file


/**
* Delete a file from dropbox
* 
* @return true
*/
function download_files()
{
	global $CONN,$module_key, $CONFIG, $domname, $dropbox_type_key;
	
	$files_selected = $_POST['files_selected'];
	
	if (!$files_selected) {
	
		$message = $dropbox_strings['no_files_selected'];
		return $message;
		
	} else {
	
	   $file_path = get_dropbox_file_path($module_key);
	   $num_selected = count($files_selected);
	   
	   for ($c=0; $c < $num_selected; $c++) {
	   
		   $sql = "SELECT filename FROM {$CONFIG['DB_PREFIX']}dropbox_files WHERE file_key='$files_selected[$c]'";
		   $rs = $CONN->Execute($sql);
		   
		   while (!$rs->EOF) {
		   
			   $name = $rs->fields[0];
			   $rs->MoveNext();
			   
		   }

if($dropbox_type_key=='3') {
$full_file_path=$CONFIG['FLASH_COM'].'recordings/streams/'.$domname.'/modules/dropbox/'.$file_path.'/'.$name.'.flv';
} else {	 
		   $full_file_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/dropbox/'.$file_path.'/'.$name;
		   }
		 
		   if (file_exists($full_file_path)) {
		   
			   $file_list .= '"'.$full_file_path.'" ';
			   
		   }
		   
		   		   
		}
		
		$random_string = get_random_string();
		$time = time();
		$zip_file_name = $random_string.$time.'.zip';
		$zip_path	   = $CONFIG['TEMP_DIR'].'/'.$zip_file_name;
		
		exec("zip -j \"$zip_path\" $file_list");
			   
		// register delete function here, otherwise your file will not be deleted in
		// case of user abort
		register_shutdown_function('delete_zip_file');

		header('Content-Type: application/zip');
		header('Accept-Ranges: bytes');
		header('Content-Length: '.filesize($zip_path));
		header('Content-Disposition: attachment; filename="dropboxfiles.zip";'); 		
		header('Connection: close');

		$fp = fopen($zip_path,'r');
		fpassthru($fp);
		fclose($fp);
		unlink($zip_path);
		exit;
			
	} //end else

	return true;
	
} //end download_files

/**
* Check dropbox form input
* 
* @return true
*/
function check_form_input() 
{
global $HTTP_POST_VARS, $general_strings, $dropbox_type_key, $module_key, $current_user_key, $CONN, $CONFIG;
// Initialize the errors array

	$errors = array();

// Trim all submitted data

	while(list($key, $value) = each($HTTP_POST_VARS)){

		$HTTP_POST_VARS[$key] = trim($value);

	}


	if(!$_POST['description']) {

		$errors['description'] = $general_strings['no_description'];

	}

	if ($dropbox_type_key==3) {
		if(empty($_POST['link_url'])) {
			$errors['file'] = "You didn't record audio.";
		}
	} else {

		$check_file_ok = check_file_upload('user_file');
	   
		if ($check_file_ok!='true') {
	   
		   $errors['file'] = $check_file_ok;
		   
		}
		   
	   
		if(!$_POST['file_extension']) {
	
			$errors['file_type'] = $general_strings['no_file_type'];
		   
		}
	}
 
 
 
	if ($dropbox_type_key==2) {
	
		$rs = $CONN->Execute("SELECT filename FROM {$CONFIG['DB_PREFIX']}dropbox_download_links WHERE module_key='$module_key' AND user_key='$current_user_key'");
		echo $CONN->ErrorMsg();
		while (!$rs->EOF) {
		
			$downloaded_file_name = $rs->fields[0];
			$rs->MoveNext();
			
		}
		
		if ($_FILES['user_file']['name']!=$downloaded_file_name) {
		
			$errors['file'] = 'Your file has the wrong filename. You need to upload a file with name '.$downloaded_file_name;
			
		}
		
	}
			
	return $errors;

} //end check_form_input

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
function get_random_string($length = 6)
{

  // start with a blank password
  $random_string = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOP"; 
	
  // set up a counter
  $i = 0; 
	
  // add random characters to $password until $length is reached
  while ($i < $length) { 

	// pick a random character from the possible ones
	$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		
	// we don't want this character if it's already in the password
	if (!strstr($random_string, $char)) { 
	  $random_string .= $char;
	  $i++;
	}

  }

  // done!
  return $random_string;

}

function delete_zip_file(){
		
	global $zip_path;
	if (is_file($zip_path)) {

		unlink($zip_path);
			   
	}
	
}
?>
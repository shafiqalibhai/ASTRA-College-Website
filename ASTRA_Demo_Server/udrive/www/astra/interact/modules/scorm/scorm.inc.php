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
* @package Scorm
* @author Bruce Webster <bruce.webster@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: 
* 
*/

/**
* Function called by Module class when adding a new scorm module
*
* @param  int $module_key  key of new scorm module
* @return true if details added successfully
*/



/**
* Function called by Module class to get exisiting file data 
*
* @param  int $module_key  key of file module
* @return true if data retrieved
*/

function get_scorm_data($module_key) {

	global $CONN,$module_data, $CONFIG;
	$sql = "SELECT file_path, browsemode, width, height FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key='$module_key'";	
	$rs = $CONN->Execute($sql);
	
	if (!$rs->EOF) {

		$module_data['file_path'] = $rs->fields[0];	
		$module_data['browsemode'] = $rs->fields[1];
		$module_data['width'] = $rs->fields[2];	
		$module_data['height'] = $rs->fields[3];
	}
	
	return true;

}


/**
* Function called by Module class to delete exisiting scorm data 
*
* @param  int $module_key  key of file module
* @param  int $space_key  space key of file module
* @param  int $link_key  link key of file module being modified
* @param  int $delete_action 
* @return true if successful
*/
function delete_scorm($module_key,$space_key,$link_key,$delete_action) 
{
	global $CONN,$CONFIG;
	
	if ($delete_action=='all' || $delete_action=='last') {

		$sql = "select file_path from {$CONFIG['DB_PREFIX']}scorm where module_key='$module_key'";

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
		$err=false;

		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE module_key='$module_key'");		

		if($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key='$module_key'")=== false) {$err=true;} else {
			if($CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key='$module_key'")=== false) {$err=true;} 
		}
		
		if($err) {
			$message = "There was an problem deleting a $module_code during a module deletion module_key=$module_key".$CONN->ErrorMsg();
			email_error($message);
			return $message;
		
		} else { 
	
			return true;
						
		}
	
	} else {	
	
		return true;
		
	}

} //end delete_scorm

/**
* Function called by Module class to flag a file for deletion 
*
* @param  int $module_key  key of file module
* @param  int $space_key  space key of file module
* @param  int $link_key  link key of file module being modified
* @param  int $delete_action 
* @return true if successful
*/
function flag_scorm_for_deletion($module_key,$space_key,$link_key,$delete_action) 
{
   
	return true;

} //end flag_scorm_for_deletion   


function add_scorm($module_key,$copy_module=0) {
	global $CONN, $CONFIG, $module_data;
	
	require_once($CONFIG['BASE_PATH'].'/modules/scorm/xml2array.class.php');
	require_once($CONFIG['BASE_PATH'].'/modules/scorm/lib.inc.php');
	require_once($CONFIG['INCLUDES_PATH'].'/lib/db.inc.php');

	//create a diretcory to store the file in
	  
	mt_srand ((float) microtime() * 1000000);
	$subdirectory = mt_rand(1,100);
	$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/scorm/'.$subdirectory;

	if (!Is_Dir($subdirectory_path)) {

		if (!mkdir($subdirectory_path,0777)) {
		 
			$message = 'There was an error creating directory for package.';
			return $message;
						
		}
		
	} 
		
	$file_path = $subdirectory.'/'.$module_key;
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/scorm/'.$file_path;

	if (!Is_Dir($full_file_path)) {

		if (!mkdir($full_file_path,0777)) {
			
			$message = 'There was an error creating directory for package.';
			return $message;
				
		}
		
	}   

	if ($copy_module) {
		get_scorm_data($copy_module);
			
		if (empty($module_data['file_path'])) {
			return "Could not find path for package";
		} else {
			if (!copy_directory($CONFIG['MODULE_FILE_SAVE_PATH'].'/scorm/'.$module_data['file_path'],$full_file_path)) {
				return 'Could not copy package files.';
			}
		}
	} else {
		$module_data['width']=$_POST['width'];
		$module_data['height']=$_POST['height'];
		$module_data['browsemode']=($_POST['browsemode']?1:0);
		$file_name		= $_FILES['user_file']['name'];
		$user_file		= $_FILES['user_file']['tmp_name'];
		$name		 = $_POST['name'];  
   
		$zipout=exec("unzip -qq -o -d \"$full_file_path\" \"$user_file\" -x \*.iphp .htaccess \*.php");
	}

	
	$launch=0;
	if (is_file($manifestfile="$full_file_path/imsmanifest.xml")) {

        $xmlstring = file_get_contents($manifestfile);
        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmlstring);
            
        $scoes = new stdClass();
        $scoes = scorm_get_manifest($manifests,$scoes);
        if (empty($scoes->version)) {$scoes->version = 'SCORM';}

        if (count($scoes->elements) > 0) {
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        $item->module_key = $module_key;
                        $item->manifest = $manifest;
                        $item->organization = $organization;

						$objDb=new InteractDb;

						$sql = $objDb->getInsertSql('scorm_scoes',array ('module_key','manifest','organization','parent','identifier','launch','parameters','scormtype','title','prerequisites','maxtimeallowed','timelimitaction','datafromlms','masteryscore','next','previous'),$item);

						if ($CONN->Execute($sql) === false) {
		
							$message =  'There was an error adding your file: '.$CONN->ErrorMsg().' <br />';
		
							return $message;
						}
		
                  	    if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                    	    $launch = $CONN->Insert_ID();
                  	    	
                        }
                    }
                }
            }
         } else {
			delete_directory($full_file_path);
			$message =  'Manifest does not contain any SCOs!!<br />';
			return $message;
         
         }
    } else {
		delete_directory($full_file_path);
		$message =  'Package does not contain the required imsmanifest!  Maybe you could upload it as a "File" component instead<br />'.'or maybe the zip command failed...<br />'."unzip -qq -o -d \"$full_file_path\" \"$user_file\" -x \*.iphp .htaccess \*.php<br />Output this:<br />".$zipout;
		return $message;

	}

	$sql = "INSERT INTO {$CONFIG['DB_PREFIX']}scorm(module_key,file_path,version,launch,browsemode,auto,width,height) VALUES ('$module_key','$file_path','$scoes->version','$launch',{$module_data['browsemode']},1,{$module_data['width']},{$module_data['height']})";

	if ($CONN->Execute($sql) === false) {
		
		$message =  'There was an error adding your '.$module_strings['scorm'].': '.$CONN->ErrorMsg().' <br />';
		
		return $message;
				
	} else {
			
		//if ($embedded==1) {
		
			//copy($CONFIG['BASE_PATH'].'/modules/file/embedfile.iphp',$full_file_path.'/embedfile.iphp');
		
		//}
		return true;
	}
}


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
function copy_scorm($existing_module_key, $existing_link_key, $new_module_key, $new_link_key, $module_data,$space_key,$new_group_key) 
{
	return add_scorm($new_module_key,$existing_module_key);
} //end copy_scorm



/**
* Function called by Module class to add new file link
*
* @param  int $module_key  key of file module
* @return true if successful
*/
function add_scorm_link($module_key) {

	return true;

}


?>
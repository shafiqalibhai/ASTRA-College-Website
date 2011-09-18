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
* Icon input
*
* Displays a page for adding/modifying custom icons
*
* @package Admin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: iconinput.php,v 1.11 2007/01/25 03:11:24 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$action = $_GET['action'];
	$message = isset($_GET['message'])? $_GET['message'] : '';
	
} else {

	$submit   = $_POST['submit'];
	$action   = $_POST['action'];
	$icon_key = $_POST['icon_key'];	
	
}

$userlevel_key = $_SESSION['userlevel_key'];
$current_user_key = $_SESSION['current_user_key'];

if (isset($submit)) {

	switch ($submit) {

		case Add:
	
			$errors = check_form_input('add');
		
			if (count($errors)>0) {
		
				$message = 'There was a problem. Please see below for details';
		
			} else {
		
				if (add_icon()===true) {
			
					$message = 'Your icon has been added';
			
				} else {
			
					$message = 'There was a problem adding your icon';
			
				}
			
		
			} 
	
		break;
		
		case Modify:
		
		   switch($action) {
		   
			   case modify1:
			   
				   $rs = $CONN->Execute("SELECT name, description, small_icon, large_icon FROM {$CONFIG['DB_PREFIX']}icons WHERE icon_key='$icon_key'");
				   
				   while (!$rs->EOF) {
		
					   $name = $rs->fields[0];
					   $description = $rs->fields[1];
					   $small_icon = '<img src="'.$CONFIG['MODULE_FILE_VIEW_PATH'].'icons/'.$rs->fields[2].'">';		
					   $large_icon = '<img src="'.$CONFIG['MODULE_FILE_VIEW_PATH'].'icons/'.$rs->fields[3].'">';					   
					  $rs->MoveNext();			
		
				   }
				   $rs->Close();			   
			   
			   break;
			   
			   case modify2:

				   $errors = check_form_input('modify');
				   
				   if (count($errors)>0) {
		
					   $message = 'There was a problem. Please see below for details';
		
				   } else {
		
					   if (modify_icon()===true) {
			
						   $message = urlencode('The icon has been modified');
						   header("Location: {$CONFIG['FULL_URL']}/admin/iconinput.php?action=modify&message=$message");
			
					   } else {
			
						   $message = 'There was a problem modifying your icon';
			
					   }
			
		
				   } 			   
			   
			   
			   break;
			   
		   
		   
		   }
		
		break;
		
		case Delete:
		
			if (delete_icon()===true) {
			
				$message = urlencode('The icon has been deleted');
				header("Location: {$CONFIG['FULL_URL']}/admin/iconinput.php?action=modify&message=$message");
				exit;
			
			} else {
			
				$message = 'There was a problem modifying your icon';
			
			}		
		
		
		break;	
	
	}
	
}

if (!isset($action) || $action=='' || $action=='modify1') {

	$template = 'iconinput.ihtml';

} else {

	$template = 'iconlist.ihtml';

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'body'	   => 'admin/'.$template,
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
set_common_admin_vars('Input System Icons', $message);

if (isset($submit)) {
	
	$t->set_var('NAME_ERROR',isset($errors['name'])? sprint_error($errors['name']): '');
	$t->set_var('SMALL_ICON_ERROR',isset($errors['small_icon'])? sprint_error($errors['small_icon']): '');

}

if (!isset($action) || $action=='') {

	$t->set_var('BUTTON','Add');
	$t->set_block('body', 'DeleteBlock', 'DBlock');
	$t->set_var('DBlock','');
	
} else if ($action=='modify1' || $action=='modify2'){

	$t->set_var('BUTTON','Modify');
	$t->set_var('ICON_KEY',$icon_key);
	$t->set_var('NAME',$name);
	$t->set_var('DESCRIPTION',$description);
	$t->set_var('SMALL_ICON',$small_icon);
	$t->set_var('LARGE_ICON',$large_icon);
	$t->set_var('ACTION','modify2');				
	

} else {

	//retrieve list of current icons
	
	$rs = $CONN->Execute("SELECT icon_key, name, small_icon, large_icon FROM {$CONFIG['DB_PREFIX']}icons ORDER By name");
	
	if ($rs->EOF) {

		$t->set_block('body', 'ListTableBlock', 'LTBlock');
		$t->set_var('LTBlock','There are currently no system icons');	
	
	} else {
	
		$t->set_block('body', 'IconBlock', 'IBlock');
		
		while (!$rs->EOF) {
		
			$t->set_var('ICON_KEY',$rs->fields[0]);
			$t->set_var('NAME',$rs->fields[1]);
			$t->set_var('SMALL_ICON',$CONFIG['MODULE_FILE_VIEW_PATH'].'icons/'.$rs->fields[2]);
			$t->set_var('LARGE_ICON',$CONFIG['MODULE_FILE_VIEW_PATH'].'icons/'.$rs->fields[3]);
			
			$t->parse('IBlock', 'IconBlock', true);
			$rs->MoveNext();			
		
		}
	
	
	}

}



$t->parse('CONTENTS', 'header', true); 
admin_navigation();

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

function check_form_input($action) {

	$errors = array();
	
	if (!isset($_POST['name']) || $_POST['name']=='') {
	
		$errors['name'] = 'You did not enter a name';
			
	}
	
	 if ($action=='add') {
	 
		 if (!isset($_FILES['small_icon']['name']) || $_FILES['small_icon']['name']=='') {
	
			$errors['small_icon'] = 'You did not enter a small icon';
	
		}
		
	}
	
	return $errors;

} //end check_form_input

function add_icon() {

	global $CONFIG, $CONN;
	
	$name		= $_POST['name'];
	$description = $_POST['description'];
	$small_icon	  = $_FILES['small_icon']['tmp_name'];
	$large_icon	  = $_FILES['large_icon']['tmp_name'];	 
	$small_icon_name = $_FILES['small_icon']['name'];
	$large_icon_name = $_FILES['large_icon']['name'];
	
	$small_icon_name =ereg_replace("[^a-z0-9A-Z._]","",substr($small_icon_name,-30));
	$large_icon_name =ereg_replace("[^a-z0-9A-Z._]","",substr($large_icon_name,-30));
	
	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}icons(name, description, small_icon, large_icon)	VALUES ('$name','$description', '$small_icon_name', '$large_icon_name')");
	echo $CONN->ErrorMsg();
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/icons';
	copy($small_icon,$full_file_path.'/'.$small_icon_name);
	copy($large_icon,$full_file_path.'/'.$large_icon_name);
	
	return true;
	
}	

function modify_icon() {

	global $CONFIG, $CONN;
	
	$name		= $_POST['name'];
	$description = $_POST['description'];
	$icon_key		= $_POST['icon_key'];	
	
	//get existing file names
	$rs = $CONN->Execute("SELECT small_icon, large_icon FROM {$CONFIG['DB_PREFIX']}icons WHERE icon_key='$icon_key'");
	echo $CONN->ErrorMsg();
	while (!$rs->EOF) {
	
		$old_small_icon = $CONFIG['MODULE_FILE_SAVE_PATH'].'/icons/'.$rs->fields[0];		
		$old_large_icon = $CONFIG['MODULE_FILE_SAVE_PATH'].'/icons/'.$rs->fields[1];					   
		$rs->MoveNext();			
		
	}
	
	$rs->Close();	
	$full_file_path=$CONFIG['MODULE_FILE_SAVE_PATH'].'/icons';

	if (isset($_FILES['small_icon']['name']) && $_FILES['small_icon']['name']!='') {
	
		$small_icon	  = $_FILES['small_icon']['tmp_name'];
		$small_icon_name = $_FILES['small_icon']['name'];
		$small_icon_name = ereg_replace("[^a-z0-9A-Z._]","",substr($small_icon_name,-30));
		
		if (is_file($old_small_icon)) {
		
			unlink($old_small_icon);
		
		}
		
		copy($small_icon,$full_file_path.'/'.$small_icon_name);
		$small_icon_sql = ", small_icon='$small_icon_name' ";
		
	}		
	
	if (isset($_FILES['large_icon']['name']) && $_FILES['large_icon']['name']!='') {
	
		$large_icon	  = $_FILES['large_icon']['tmp_name'];	 
		$large_icon_name = $_FILES['large_icon']['name'];
		$large_icon_name = ereg_replace("[^a-z0-9A-Z._]","",substr($large_icon_name,-30));
		
		if (is_file($old_large_icon)) {
		
			unlink($old_large_icon);
		
		}
		
		copy($large_icon,$full_file_path.'/'.$large_icon_name);
		$large_icon_sql = ", large_icon='$large_icon_name' ";				
		
	}
	
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}icons SET name='$name',  description='$description' $small_icon_sql $large_icon_sql WHERE icon_key='$icon_key'");
	echo $CONN->ErrorMsg();
	return true;
	
}	

function delete_icon() {

	global $CONFIG, $CONN;
	
	$icon_key = $_POST['icon_key'];	
	
	//get existing file names
	$rs = $CONN->Execute("SELECT small_icon, large_icon FROM {$CONFIG['DB_PREFIX']}icons WHERE icon_key='$icon_key'");

	while (!$rs->EOF) {
	
		$small_icon = $CONFIG['MODULE_FILE_SAVE_PATH'].'/icons/'.$rs->fields[0];		
		$large_icon = $CONFIG['MODULE_FILE_SAVE_PATH'].'/icons/'.$rs->fields[1];					   
		$rs->MoveNext();			
		
	}
	
	$rs->Close();	

		
	if (is_file($small_icon)) {
		
		unlink($small_icon);
		
	}
		
	if (is_file($large_icon)) {
		
		unlink($large_icon);
		
	}

	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}icons WHERE icon_key='$icon_key'");
	echo $CONN->ErrorMsg();
	return true;
	
}	
?>

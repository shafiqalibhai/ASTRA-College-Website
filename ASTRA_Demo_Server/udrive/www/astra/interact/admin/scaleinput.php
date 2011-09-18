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
* Scale input
*
* Displays a page for adding/modifying custom scales
*
* @package gradebook
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: scaleinput.php,v 1.16 2007/07/18 05:17:44 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/gradebook_strings.inc.php');
require_once('../modules/gradebook/lib.inc.php');


//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$action	   = $_GET['action'];
	$grade_action = $_GET['grade_action'];
	$scale_key	= $_GET['scale_key'];		 
	
} else {

	$module_key		= $_POST['module_key'];
	$scale_key		   = $_POST['scale_key'];
	$submit			   = $_POST['submit'];
	$action			   = $_POST['action'];
	$grade_key		   = $_POST['grade_key'];
	$grade_name		   = $_POST['grade_name'];
	$scale_name		   = $_POST['scale_name'];
	$grade_action	  = $_POST['grade_action'];	
	$scale_description = $_POST['scale_description'];				

	
}

$userlevel_key = $_SESSION['userlevel_key'];
$current_user_key = $_SESSION['current_user_key'];
$gradebook = new Interactgradebook($space_key, $module_key, $group_key, $is_admin, $gradebook_strings);

switch ($action) {

	case add_scale:
	
		$message = $gradebook->addScale($scale_name, $scale_description, $space_key, 'system');
		
		if ($message===true) {
		
			$message = $gradebook_strings['add_scale_success'];
			
		}
		
	break;
	
	case select_scale:
	
		$scale_data		= $gradebook->getScaleData($scale_key);
		$scale_name		= $scale_data['name'];
		$scale_description = $scale_data['description'];
		
	break;
	
	case modify_scale:
	
		switch ($submit) {
		
			case $general_strings['modify']:
			
				$message = 	$gradebook->modifyScale($scale_key, $scale_name, $scale_description);
		
				if ($message===true) {
		
					$message = $gradebook_strings['modify_scale_success'];
			
				}
				
			break;
			
			case $general_strings['delete']:
			
				$message = 	$gradebook->deleteScale($scale_key);
		
				if ($message===true) {
		
					$message = $gradebook_strings['delete_scale_success'];
			
				}
				
			break;
			
		}			
					
		
	break;	
	
}

switch ($grade_action) {

	case add_grade:
	
		$message = $gradebook->addScalegrade($grade_name, $scale_key);
		
		if ($message===true) {
		
			$message = $gradebook_strings['add_grade_success'];
			
		}
		
	break;
	
	case select_grade:
	
		$grade_data		= $gradebook->getScalegradeData($grade_key);
		$grade_name		= $grade_data['grade'];

		
	break;
	
	case modify_grade:
	
		switch ($submit) {
		
			case $general_strings['modify']:
			
				$message = 	$gradebook->modifyScalegrade($grade_key, $grade_name);
		
				if ($message===true) {
		
					$message = $gradebook_strings['modify_grade_success'];
			
				}
				
			break;
			
			case $general_strings['delete']:
			
				$message = 	$gradebook->deleteScalegrade($grade_key);
		
				if ($message===true) {
		
					$message = $gradebook_strings['delete_grade_success'];
			
				}
				
			break;
			
		}			
					
		
	break;	
	
}


//if user is not and admin, and forum is closed refer to users own journal entries


require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'body'	   => 'gradebook/scaleinput.ihtml',
	'footer'	 => 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
set_common_admin_vars('gradebook scales', $message);
$scale_sql = "SELECT name, scale_key FROM {$CONFIG['DB_PREFIX']}gradebook_scales WHERE (added_by_key='0') ORDER BY name";
$scale_menu = make_menu($scale_sql,'scale_key',$scale_key,'1',false);



if (!$action || $action=='add_scale' || $action=='modify_scale') {

	$t->set_var('INPUT_SCALE_STRING', $gradebook_strings['add_scale']);
	$t->set_var('INPUT_SCALE_ACTION', 'add_scale');	 
	$t->set_var('SCALE_BUTTON',$general_strings['add']);
	$t->set_block('body', 'GradeBlock', 'GBlock');
	$t->set_var('GBlock','');
	$t->set_var('SCALE_NAME','');
	$t->set_var('SCALE_DESCRIPTION','');
	
} else if (!$action || $action=='select_scale' ) {

	$t->set_var('INPUT_SCALE_STRING', $gradebook_strings['modify_scale']);
	$t->set_var('INPUT_SCALE_ACTION', 'modify_scale');	 
	$t->set_var('SCALE_BUTTON',$general_strings['modify']);
	$delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')" />';
	$t->set_var('SCALE_NAME',$scale_name);
	$t->set_var('SCALE_DESCRIPTION',$scale_description);
	$t->set_var('ADD_SCALE_LINK','<a href="scaleinput.php">'.$gradebook_strings['add_scale'].'</a>');	
	$grade_sql = "SELECT grade, grade_key FROM {$CONFIG['DB_PREFIX']}gradebook_scale_grades WHERE scale_key='$scale_key' ORDER BY grade";
	$grade_menu = make_menu($grade_sql,'grade_key',$grade_key,'1',false);
	
	if (!$grade_action || $grade_action=='add_grade' || $grade_action=='modify_grade' ) {
		
		$t->set_var('INPUT_GRADE_STRING',$gradebook_strings['add_grade']);
		$t->set_var('GRADE_BUTTON',$general_strings['add']);
		$t->set_var('GRADE_ACTION','add_grade');
		$grade_name='';		
		
	} else if ($grade_action=='select_grade') {		
	
		$t->set_var('INPUT_GRADE_STRING',$gradebook_strings['modify_grade']);
		$t->set_var('GRADE_BUTTON',$general_strings['modify']);
		$t->set_var('GRADE_ACTION','modify_grade');
		$t->set_var('ADD_GRADE_LINK',"<a href=\"scaleinput.php?scale_key=$scale_key&action=select_scale&grade_action=add_grade\">".$gradebook_strings['add_grade'].'</a>');		
		$grade_delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" onClick="return confirmDelete(\''.$general_strings['check'].'\')" />';
				
	}

}

$t->set_var('SPACE_KEY',$space_key);
$t->set_var("SPACE_TEXT",ucfirst($general_strings['space_text']));
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('LINK_KEY',$link_key);
$t->set_var('SCALE_KEY',$scale_key);
$t->set_var('GRADE_KEY',$grade_key);
$t->set_var('SCALE_MENU',$scale_menu);
$t->set_var('GRADE_MENU',$grade_menu);
$t->set_var('HEADING',$gradebook_strings['custom_scales']);
$t->set_var('EXISTING_SCALES_STRING',$gradebook_strings['existing_scales']);
$t->set_var('EXISTING_GRADES_STRING',sprintf($gradebook_strings['existing_grades'],$scale_name));
$t->set_var('SELECT_SCALE_STRING',$gradebook_strings['select_scale']);
$t->set_var('SELECT_GRADE_STRING',$gradebook_strings['select_grade']);
$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('DELETE_BUTTON',$delete_button);
$t->set_var('GRADE_DELETE_BUTTON',$grade_delete_button);
$t->set_var('GRADE_NAME',$grade_name);


$t->set_var('NAME_STRING',$general_strings['name']);
$t->parse('CONTENTS', 'header', true); 
admin_navigation();

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

<?php
// +----------------------------------------------------------------------+
// | This file is part of Interact.                                       |
// |                                                                      | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)                             |
// |                                                                      | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     |
// | General Public License for more details.                             |
// |                                                                      | 
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, you can view it at                  |
// | http://www.opensource.org/licenses/gpl-license.php                   |
// +----------------------------------------------------------------------+

/**
* Calendar input file
*
* Inputs/modifies a new calendar module
*
* @package Calendar
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: calendar_input.php,v 1.32 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/calendar_strings.inc.php');

//set main variables 

if ($_POST['space_key']) {
    $space_key  = $_POST['space_key'];
    $module_key = $_POST['module_key']; 
    $link_key   = $_POST['link_key']; 	   
} else {
    $space_key  = $_GET['space_key'];
    $module_key = $_GET['module_key'];    
    $link_key   = $_GET['link_key'];    
}

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$can_edit_module = check_module_edit_rights($module_key);

//create new modules object
$modules = new InteractModules();
$modules->set_module_type('calendar');

//find out what action we need to take
if (isset($_POST[submit])) {
 
    switch($_POST[submit]) {

        //if we are adding a new calendar form input needs to be checked 
        case $general_strings['add']:
            $errors = check_form_input();
            //if there are no errors then add the data
            if(count($errors) == 0) {
                $message = $modules->add_module('calendar');
            //if the add was successful return the browser to space home or parent calendar
                if ($message=='true') {
                    $modules->return_to_parent('calendar','added');
                    exit;
                }  
            //if the add wasn't succesful return to form with error message
            } else {
                $parent_calendar_key = $_POST['parent_calendar_key'];                
                $button = $general_strings['add'];
                $message = $general_strings['problem_below'];
            }
            break;

        case $general_strings['modify']:
            if ($can_edit_module==true) {
				$errors = check_form_input();
			}
            if(count($errors) == 0) {
                $message = $modules->modify_module('calendar',$can_edit_module);
                //return browser to space home or parent calendar
				if ($message=='true') {
                       $modules->return_to_parent('calendar','modified');
                       exit;
                }  
            } else {
                $parent_calendar_key = $_POST['parent_calendar_key'];
                $message = $general_strings['problem_below'];
            }
		break;
            
        case $general_strings['delete']:
            $space_key     = $_POST['space_key'];
            $module_key    = $_POST['module_key'];
            $parent_key    = $_POST['parent_key'];
            $group_key     = $_POST['group_key'];
            $link_key      = $_POST['link_key'];
            header ("Location: {$CONFIG['FULL_URL']}/modules/general/moduledelete.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key");
            exit;            
           
        default:
            $message = $general_strings['no_action'];
            break;
    } //end switch($_POST[submit])            
} //end isset($_POST[submit])

if ($_GET[action]=='modify') {
    $module_data = $modules->get_module_data('calendar');
    $parent_calendar_key = $module_data['parent_calendar_key'];
    $type = $module_data['type'];	    
} //end if ($_GET[action]=="modify")          

if (!isset($_GET[action]) && !isset($_POST[action])) {
    $action = 'add';
    $title = $calendar_strings['add_calendar'];
    $button = $general_strings['add'];
}

if ($_GET[action]=='modify'||$_POST[submit]=='Modify') {
    $action = 'modify2';
    $button = $general_strings['modify'];
    $delete_button = '<input type="submit" name="submit" value="'.$general_strings['delete'].'" />';
}

//generate any input menus
$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission
$name_error = sprint_error($errors['name']);
$parent_error = sprint_error($errors['parent']);

//get the required template files
//get the required templates for this page
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'calendars/calendar_input.ihtml',
	'general'		 => 'modules/generalsettings.ihtml',
	'footer'		  => 'footer.ihtml'));


//generate the header,title, breadcrumb details
$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module);

if (!class_exists('InteractHtml')) {
	require_once('../../includes/lib/html.inc.php');
}
$html = new InteractHtml();
$html->setTextEditor($t, 0, 'description');


if (!$type || $type=='closed') {
    $t->set_var('CLOSED_SELECTED','selected');
} else {
    $t->set_var('OPEN_SELECTED','selected');
}
$t->set_var('PARENT_ERROR',$parent_error);
$t->set_var('NAME_STRING',$general_strings['name']);
$t->set_var('DESCRIPTION_STRING',$general_strings['description']);
$t->set_var('CANCEL_STRING',$general_strings['cancel']);
$t->set_var('OPEN_STRING',$general_strings['open']);
$t->set_var('CLOSED_STRING',$general_strings['closed']);
$t->set_var('OPTIONAL_STRING',$general_strings['optional']);
$t->set_var('TYPE_STRING',$general_strings['type']);
$t->set_var('PARENT_CALENDAR_STRING',$calendar_strings['parent_calendar']);
$t->set_var('PARENT_CALENDAR_KEY',!isset($parent_calendar_key)|$parent_calendar_key==0?'':$parent_calendar_key);
$t->set_var('INPUT_HEADING',$calendar_strings['add_calendar']);
$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu
get_navigation();
$t->parse('GENERAL_SETTINGS', 'general', true);
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page
$t->p('CONTENTS');
$CONN->Close();
exit;

/**
* Function to check form input when adding/modifying a calendar
*
* @package Calendar
* @return  array $errors an array of any errors in form input
*/
function check_form_input() 
{
    global $general_strings, $CONN, $CONFIG,$module_key;
	// Initialize the errors array
    $errors = array();
    // Trim all submitted data
    while(list($key, $value) = each($_POST)){
        $_POST[$key] = trim($value);
    }
    //check to see if we have all the information we need
	if(!$_POST['name']) {
        $errors['name'] = $general_strings['no_name'];
    }
	
	if(isset($_POST['parent_calendar_key']) && $_POST['parent_calendar_key']!='' && $_POST['parent_calendar_key']!=0) {
        $rs = $CONN->Execute("SELECT type_code FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='{$_POST['parent_calendar_key']}'");
		while (!$rs->EOF) {
			$module_type_code = $rs->fields[0];
			$rs->MoveNext();
		}
		$rs->Close();

		if ($module_type_code!='calendar') {
			$errors['parent'] = 'There is no calendar with that component number';
		}

		if ($_POST['parent_calendar_key']==$module_key) {
			$errors['parent'] = 'You can\'t make a calendar a parent of itself';
		}
    }
    return $errors;
} //end check_form_input
?>
<?php
// +----------------------------------------------------------------------+
// | chatinput.php  1.0                                                   |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education                 |
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
// |                                                                      |
// |                                                                      |
// | Input/Modify a chat room in an Interact Space                        |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz>                     |
// | Last Modified 29/01/03                                               |
// +----------------------------------------------------------------------+


require_once('../../local/config.inc.php');
require_once('../../includes/modules.inc.php');


//check we have the required variables

if ($_POST['space_key']) {
    
    $space_key  = $_POST['space_key'];
    $module_key = $_POST['module_key']; 
    $link_key   = $_POST['link_key']; 	   
    
} else {

    $space_key  = $_GET['space_key'];
    $module_key = $_GET['module_key'];    
    $link_key   = $_GET['link_key'];    
}

check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate(true);
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

//check and see that this user is allowed to edit this module or link
$can_edit_module = check_module_edit_rights($module_key);
	


//create new modules object

$modules = new InteractModules();
$modules->set_module_type('chat');

//find out what action we need to take

if (isset($_POST[submit])) {


    switch($_POST[submit]) {

        //if we are adding a new page form input needs to be checked 

        case Add:
        
            $errors = check_form_input();

            //if there are no errors then add the data
            if(count($errors) == 0) {

                $message = $modules->add_module('chat');


            //if the add was successful return the browser to space home or parent page
                if ($message=='true') {
                     
                    $modules->return_to_parent('chat','added');
                    exit;
                
                }  

            //if the add wasn't succesful return to form with error message

            } else {

                $body = $_POST['body'];                
                $button = 'Add';
                $message = 'There was a problem, see below for details';
            
            }
            
            break;

        case Modify:
      
            if ($can_edit_module==true) {
                
				$errors = check_form_input();
				
			}
    
            if(count($errors) == 0) {

                 $message = $modules->modify_module('chat', $can_edit_module);

                //return browser to space home or parent page

                   if ($message=='true') {

                      $modules->return_to_parent('chat','modified');
                    exit;

                }  

            } else {
                        
                  $body = $_POST['body'];
                $message = 'There was a problem, see below for details';
             
            }
            
            break;
            
        case Delete:
            $space_key     = $_POST['space_key'];
            $module_key    = $_POST['module_key'];
            $parent_key    = $_POST['parent_key'];
            $group_key     = $_POST['group_key'];
            $link_key      = $_POST['link_key'];
                                    
            header ("Location: {$CONFIG['FULL_URL']}/modules/general/moduledelete.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&parent_key=$parent_key&group_key=$group_key");
            exit;            
            
        default:
            $message = 'A valid action was not selected. Please try again';
            break;
            
    } //end switch($_POST[submit])            

} //end isset($_POST[submit])

if ($_GET[action]=='modify') {

    $module_data = $modules->get_module_data('chat');
    $body     = $module_data['body'];    
 
} //end if ($_GET[action]=="modify")          

if (!isset($_GET['action']) && !isset($_POST['action'])) {

    $action = 'add';
    $title = 'Add a chat room';
    $button = 'Add';
}

if ($_GET['action']=='modify' || $_POST['submit']=='Modify') {

    $action = 'modify2';
    $button = 'Modify';
    $delete_button = '<input type="submit" name="submit" value="Delete" />';

}

//generate any input menus

$menus_array = $modules->create_module_input_menus($module_data);

//format any errors from form submission

$name_error = sprint_error($errors['name']);

//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
    'header'          => 'header.ihtml',
    'navigation'      => 'navigation.ihtml',
    'form'            => 'chat/chat_input.ihtml',
    'general'         => 'modules/generalsettings.ihtml',
    'footer'          => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);
$modules->set_common_input_vars($page_details,$menus_array,$module_data,$can_edit_module, 'chat');

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
* Check form input   
* 
*  
* @return $errors
*/
function check_form_input() 
{

// Initialize the errors array

    $errors = array();

// Trim all submitted data

    while(list($key, $value) = each($_POST)){

        $_POST[$key] = trim($value);

    }

//check to see if we have all the information we need
    if(!$_POST['name']) {

        $errors['name'] = 'You didn\'t enter a name.';

    }
    

    return $errors;
    
} //end check_form_input


?>
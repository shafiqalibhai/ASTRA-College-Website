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

* link input

*

* Input a link into a sharing module

*

* @package Sharing

* @author Glen Davies <glen.davies@cce.ac.nz>

* @copyright Christchurch College of Education 2001 

* @version $Id: linkinput.php,v 1.16 2007/07/30 01:57:05 glendavies Exp $

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

	

	$module_key	   = $_POST['module_key'];

	$shareditem_key   = $_POST['shareditem_key'];

	$action		   = $_POST['action'];

	$link_name		= $_POST['link_name'];

	$link_url		 = $_POST['link_url'];

	$link_description = $_POST['link_description'];		

				

	

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

				

				$message = add_link();



				//if the add was successful return the browser to space home or parent folder

				if ($message=='true') {

					

					$message = urlencode($sharing_strings['link_added']);

					Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");

					exit;

				} 



			//if the add wasn't succesful return to form with error message



			} else {

			

				$button = $general_strings['add'];

				

				

			}

			break;

		

		case modify:

		

			$sql = "SELECT name,description,url FROM {$CONFIG['DB_PREFIX']}shared_items WHERE shared_item_key='$shareditem_key'";

			$rs = $CONN->Execute($sql);

			while (!$rs->EOF) {



				$link_name = $rs->fields[0];

				$link_description = $rs->fields[1];

				$link_url = $rs->fields[2];

				$rs->MoveNext();

				

			}

			

			$rs->Close();

		

		break;

		

		case modify2:

		

			switch($_POST['submit']) {



				//if deleting, then delete link

				case $general_strings['delete']:

				

					$message=delete_link();



				//return browser to sharing space 

				

				if ($message=='true') {

				

					$message = urlencode($sharing_strings['link_deleted']);

					Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");

					exit;

				

				}

				

			 break;



			//if modifying then modify folder



			case $general_strings['modify']:

			

				$errors = check_form_input();

				

				if(count($errors) == 0) {

					

					$message = modify_link();



					//return browser to space home or parent folder



					if ($message=='true') {

 

						$message = urlencode($sharing_strings['link_modified']);

						Header ("Location: {$CONFIG['FULL_URL']}/modules/sharing/sharing.php?space_key=$space_key&module_key=$module_key&message=$message");

						 exit;

				

					} 

			

				} else {

				

					$message = $general_strings['problem_below'];

			 

				}

			

				break;

			

			} //end switch $submit  

	  



		} //end switch $action





} //end if (isset($action))







if (!isset($action)) {

	

	$action = 'add';

	$button = $general_strings['add'];

	$link_url = "http://";



}





if ($action=='modify'|$action=='modify2') {

	

	$action = 'modify2';

	$button = $general_strings['modify'];

	 $warning=$general_strings['check'];

	$delete_button = "<input type=\"submit\" name=\"submit\" value=\"".$general_strings['delete']."\" onClick=\"return confirmDelete('$warning')\">";

	

}





//format any errors from form submission



$link_name_error = sprint_error($errors['link_name']);

$link_url_error = sprint_error($errors['link_url']);



//get the required template files

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(

	'header'		   => 'header.ihtml',

	'navigation'	   => 'navigation.ihtml',

	'form'			 => 'sharing/linkinput.ihtml',

	'footer'		   => 'footer.ihtml'));







$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);





$t->set_var('NAME_ERROR',$link_name_error);

$t->set_var('URL_ERROR',$link_url_error);

$t->set_var('LINK_NAME',$link_name);

$t->set_var('LINK_DESCRIPTION',$link_description);

$t->set_var('LINK_URL',$link_url);

$t->set_var('ACTION',$action);

$t->set_var('BUTTON',$button);

$t->set_var('DELETE_BUTTON',$delete_button);

$t->set_var('SPACE_KEY',$space_key);

$t->set_var('MODULE_KEY',$module_key);

$t->set_var('SHAREDITEM_KEY',$shareditem_key);

$t->set_var('CANCEL_STRING',$general_strings['cancel']);

$t->set_var('NAME_STRING',$general_strings['name']);

$t->set_var('DESCRIPTION_STRING',$general_strings['description']);

$t->set_var('OPTIONAL_STRING',$general_strings['optional']);

$t->set_var('ADD_LINK_HEADING',sprintf($sharing_strings['add_link_heading'],$page_details['module_name']));

$t->parse("CONTENTS", "header", true); 

//generate the navigation menu



get_navigation();

$t->parse("CONTENTS", "form", true);





$t->parse("CONTENTS", "footer", true);

print_headers();



//output page



$t->p("CONTENTS");

$CONN->Close();

exit;



/**

* Add a link to a space or folder

* 

* @return true

*/

function add_link()

{

	global $CONN,$link_name,$link_description,$link_url,$date_added,$space_key,$module_key, $current_user_key, $CONFIG;



	$date_added	= $CONN->DBDate(date('Y-m-d H:i:s'));

	$name = $link_name; 

	$description = $link_description; 

	$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}shared_items VALUES ('','$module_key','$current_user_key',$date_added,'$name','$description','$link_url','','')";



	if ($CONN->Execute("$sql") === false) {

	  

		$message =  'There was an error adding your link: '.$CONN->ErrorMsg().' <br />';

		return $message;

	} else {

		//update statistics 

		statistics("post");

		return true;  

	}

} //end add_link



/**

* Modify A link 

* 

*  

* @return true

*/



function modify_link()

{

	global $CONN,$link_name,$link_description,$link_url,$shareditem_key, $CONFIG;

	$name = $link_name; 

	$description = $link_description;

	$url = $link_url;

	$sql = "UPDATE {$CONFIG['DB_PREFIX']}shared_items SET name='$name',description='$description',url='$url' WHERE shared_item_key='$shareditem_key'";

	if ($CONN->Execute("$sql") === false) {

	   $message =  'There was an error modifying your link: '.$CONN->ErrorMsg().' <br />';

	   return $message;



	} else {

		return true;  

	}



} //end modify link





function delete_link()

{

	global $CONN,$shareditem_key, $CONFIG;

	$sql="delete from {$CONFIG['DB_PREFIX']}shared_items where shared_item_key='$shareditem_key'";

	$CONN->Execute("$sql");

	$rows_affected = $CONN->Affected_Rows();

	if ($rows_affected < "1") {   

	   $message = "There was an error deleting a link from a shared space - ".$CONN->ErrorMsg();

	   email_error($message);

	   return $message;

	} else {

		$sql="delete from {$CONFIG['DB_PREFIX']}shared_item_comments where shared_item_key='$shareditem_key'";

		if ($CONN->Execute("$sql") === false) {   

			$message = "There was a problem deleting comments from a shared item - ".$CONN->ErrorMsg();

			email_error($message);

			return $message;

		} else {

			return true;

		}

	}

} //end delete_link









function check_form_input() 

{

global $HTTP_POST_VARS, $link_name, $link_description,$link_url;

// Initialize the errors array



	$errors = array();



// Trim all submitted data



	while(list($key, $value) = each($HTTP_POST_VARS)){



		$HTTP_POST_VARS[$key] = trim($value);



	}



//check to see if we have all the information we need

	if(!$link_name) {



		$errors["link_name"] = "You didn't enter a name.";



	}





	if(!$link_url || $link_url=='http://') {



		$errors["link_url"] = "You didn't enter a link.";



	}



return $errors;

} //end check_form_input





?>
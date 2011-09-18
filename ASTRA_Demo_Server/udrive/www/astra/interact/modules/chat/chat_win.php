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
* Chat homepage
*
*
* @package Chat
* @author Bruce Webster <b@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: chat_win.php,v 1.2 2007/05/30 13:34:48 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once '../../local/config.inc.php';
//require_once $CONFIG['LANGUAGE_CPATH'].'/chat_strings.inc.php';

$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= isset($_GET['group_key'])?$_GET['group_key']:'';
$message	 = isset($_GET['message'])?$_GET['message']:'';


//check we have the variables we need
check_variables(true,true,true);

$access_levels = authenticate();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"chat"		  => "chat/chat_win.ihtml",
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('MODULE_NAME',$page_details['module_name']);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('USER_KEY',$_SESSION['current_user_key']);
//$t->set_var('HANDLE',$_SESSION['current_user_firstname'].' '.$_SESSION['current_user_lastname']);

$t->parse('CONTENTS', 'chat', true);
print_headers();

if(empty($_SESSION['current_user_key'])) {
	echo "You need to be logged in to chat.";
} else {
	$t->p("CONTENTS");
}
?>

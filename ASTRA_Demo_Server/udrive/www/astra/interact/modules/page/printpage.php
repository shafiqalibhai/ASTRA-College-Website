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
* Display page for printing
*
* Displays a page component for printing 
*
* @package Page
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: printpage.php,v 1.12 2007/07/30 01:57:04 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];

//check we have the required variables
check_variables(true,false,true);


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];


require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'fullpage'		  => 'pages/printpage.ihtml'
));

$page_details = get_page_details($space_key,$link_key);

if (!class_exists('InteractDate')) {

	require_once('../../includes/lib/date.inc.php');
	
}

$dates = new InteractDate();
$date = $dates->formatDate(time(),'short', true);

$t->set_var('PAGE_TITLE',$page_details[full_space_name].' - '.$page_details[module_name]);
$t->set_var('SPACE_TITLE',$page_details[full_space_name]);
$t->set_var('BACK_URL',$_SERVER['HTTP_REFERER']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('FROM_STRING',$general_strings['from']);
$t->set_var('DATE',$date);

$sql = "SELECT name, body FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}pages WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}pages.module_key and {$CONFIG['DB_PREFIX']}modules.module_key='$module_key'";

$rs = $CONN->Execute($sql);

while (!$rs->EOF) {

	$title = $rs->fields[0];
	$body = stripslashes($rs->fields[1]);
	$t->set_var('TITLE',$title);
	$t->set_var('BODY',$body);
	$rs->MoveNext();
}

$rs->Close();
$t->parse('CONTENTS', 'fullpage', true); 
print_headers();
$t->p('CONTENTS');
	   
$CONN->Close();
exit;

?>
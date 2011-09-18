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
* Show all module urls
*
* Displays a list of all module urls for site 
*
* @package SpaceAdmin
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: showallurls.php,v 1.10 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key = $_GET['space_key'];
	

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
authenticate_admins($level='space_only');
$group_access = $access_levels['groups'];	 


require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'form'			=> 'spaceadmin/showallurls.ihtml',
	'footer'		  => 'footer.ihtml'
));

//format any errors from form submission

$access_code_error = sprint_error($errors['access_code']);
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->parse('CONTENTS', 'header', true);
get_navigation();
$t->set_var('HEADING_STRING',$space_strings['show_urls_heading']);
//now get all the module urls for this space.
$sql = "select {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4') ORDER BY {$CONFIG['DB_PREFIX']}modules.name";

$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
while (!$rs->EOF) {

	$module_key = $rs->fields[0];
	$name	   = $rs->fields[1];
	$code	   = $rs->fields[2];
	$all_urls  .= '<tr><td>'.$name.'</td><td>'.$CONFIG['PATH'].'/modules/'.$code.'/'.$code.'.php?module_key='.$module_key.'</td></tr>';
	$rs->MoveNext();
	
}
$t->set_var('ALL_URLS',$all_urls);
$t->set_var('SPACE_KEY',$space_key);

$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

?>
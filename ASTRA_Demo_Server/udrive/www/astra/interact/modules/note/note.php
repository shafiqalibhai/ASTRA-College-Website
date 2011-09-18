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
* Note homepage
*
* Displays a note start page. 
*
* @package Note
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: note.php,v 1.17 2007/07/30 01:57:03 glendavies Exp $
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

check_variables(true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);

//update statistics 
statistics('read');

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'note'	   => 'notes/note.ihtml',
	'footer'	 => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true); 

get_navigation();

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}
$html = new InteractHtml();

$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,note from {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}notes where {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}notes.module_key and ({$CONFIG['DB_PREFIX']}modules.module_key='$module_key')";

$rs = $CONN->Execute($sql);

while (!$rs->EOF) {

	$module_key2 = $rs->fields[0];
	$note = $html->parseText($rs->fields[2]);

	$t->set_var('SPACE_KEY',$space_key);
	$t->set_var('MODULE_KEY',$module_key2);
	$t->set_var('NOTE',$note);
	
	if ($is_admin==true) {
	
		$admin_image=get_admin_tool("{$CONFIG['PATH']}$admin_url?space_key=$space_key&module_key=$module_key2&action=modify");
	
	}
	
	$t->set_var('ADMIN_IMAGE',$admin_image);
	$t->parse('CONTENTS', 'note', true);
	$rs->MoveNext();

}
$rs->Close();

$t->parse('CONTENTS', 'footer', true);

print_headers();

$t->p('CONTENTS');

exit;

?>
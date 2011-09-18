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
* Module url
*
* Displays the url of a module
*
* @package Modules
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: moduleurl.php,v 1.25 2007/07/30 01:57:00 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');

$module_key = $_GET['module_key'];
$space_key 	= get_space_key();
$link_key = get_link_key($module_key,$space_key);
//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels   = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access	= $access_levels['groups'];

	
$rs = $CONN->GetRow("SELECT {$CONFIG['DB_PREFIX']}modules.type_code, {$CONFIG['DB_PREFIX']}modules.name FROM {$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}modules.module_key='$module_key'");
$module_code = $rs[0];
$module_name = $rs[1];

//get the required template files
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
'header'		  => 'header.ihtml',
'navigation'	  => 'navigation.ihtml',
'form'			=> 'modules/moduleurl.ihtml',
'footer'		  => 'footer.ihtml'));

//generate the header,title, breadcrumb details

$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//if this is a not a page or a note don't show advannced linking options

if ($module_code!='page' && $module_code!='note') {

	$t->set_block('form', 'AdvancedlinkingBlock', 'ALBlock');
	$t->set_var('ALBlock', '');

}


$t->set_var('DIRECT_TYPE',$CONFIG['DIRECT_PATH']);

if ($module_code=='space') {

	$this_space_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
	$int_module_url = '/spaces/space.php?space_key='.$this_space_key;
	$ext_module_url = '/spaces/space.php?space_key='.$this_space_key;
	$email_url		= 'space/'.$this_space_key;

} else {

	$int_module_url = '/modules/'.$module_code.'/'.$module_code.'.php?module_key='.$module_key;
	$ext_module_url = '/modules/'.$module_code.'/'.$module_code.'.php?space_key='.$space_key.'&module_key='.$module_key;
	$email_url = $space_key.'/'.$module_key;


}
$t->set_var('PATH',$CONFIG['PATH']);
$t->set_var('TOP_BREADCRUMBS',$page_details['top_breadcrumbs']);
$t->set_var('BREADCRUMBS',$page_details['breadcrumbs']);
$t->set_var('INT_MODULE_URL',$int_module_url);
$t->set_var('EXT_MODULE_URL',$ext_module_url);
$t->set_var('EMAIL_URL',$email_url);
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('SPACE_KEY',$space_key); 
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('LINK_KEY',$link_key);	
$t->set_var('GROUP_KEY',$group_key);
$t->set_var('MODULE_TEXT',$general_strings['module_text']);
$t->set_var('MODULE_CODE',$module_code);
$t->set_var('MODULE_NAME',$module_name);
$t->set_var('SPACE_TEXT',$general_strings['space_text']);
$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
$t->set_var('BACK_URL',$_SERVER['HTTP_REFERER']);
$t->set_var('BACK_STRING',$general_strings['back']);
$t->set_var('URL_HEADING',sprintf($module_strings['url_heading'], $general_strings['module_text'], $module_name));
$t->set_var('URL_STRING1',sprintf($module_strings['url1'], $module_name, $general_strings['space_text'] ));
$t->set_var('URL_STRING2',sprintf($module_strings['url2'], $general_strings['space_text'], $module_name, $general_strings['space_text'] ));
$t->set_var('OTHER_OPTIONS_STRING',$module_strings['other_options']);
$t->set_var('EMAIL_LINK_STRING',$module_strings['email_link_heading']);
$t->set_var('EMAIL_LINK_TEXT_STRING',sprintf($module_strings['email_link_text'], $general_strings['module_text']));
$t->set_var('POPUP_STRING',sprintf($module_strings['popup_text'], $general_strings['module_text']));



$t->parse('CONTENTS', 'header', true); 

//generate the navigation menu

get_navigation();

$t->parse('CONTENTS', 'form', true);

$t->parse('CONTENTS', 'footer', true);
print_headers();

//output page

$t->p('CONTENTS');
$CONN->Close();
exit;

?>
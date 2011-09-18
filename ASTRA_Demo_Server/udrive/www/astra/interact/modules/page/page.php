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
* Page homepage
*
* Displays a page start page. 
*
* @package Note
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: page.php,v 1.40 2007/06/07 08:30:10 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/page_strings.inc.php');
require_once('lib.inc.php');
$objPage = new InteractPage();
//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];
$current_user_key = $_SESSION['current_user_key'];

check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);
$is_space_member = $CONN->GetOne("SELECT user_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$current_user_key'"); 

//update statistics 
statistics('read');
$page_data = $objPage->getPageData($module_key);
$current_page_key = $objPage->getCurrentPage($module_key);
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'page'			=> 'pages/page.ihtml',
	'footer'		  => 'footer.ihtml'
));

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('DESCRIPTION','');
$browser=browser_get_agent();

if (
	(($browser['agent']=='IE' && $browser['version']>=5.5) || ($browser['gecko_version']>'20030210')) 
	&& 
	(
	$_SESSION['userlevel_key']==1
	||
	($page_data['page_edit_rights']==0 && $is_admin) 
	|| 
	($page_data['page_edit_rights']==2 && !empty($current_user_key)) 
	|| 
	($page_data['page_edit_rights']==1 && $is_space_member)
	||
	($page_data['page_edit_rights']==3))
	){
	$t->set_var('SCRIPT_EDITOR','<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/dojo/dojo_ed.js?v2"></script>',true);
} else {
	$t->set_block('page', 'PageEditBlock', 'PageEdBlock');
	$t->set_block('page', 'PageSaveBlock', 'PageSvBlock');
	$t->set_block('page', 'PageHistoryBlock', 'PageHisBlock');

}
$t->parse('CONTENTS', 'header', true); 
get_navigation();

if (!empty($_GET['version'])) {
	$version_limit  = "AND page_key='".$_GET['version']."'";
	
	if ($current_page_key>$_GET['version']) {
		
		$t->set_var('MESSAGE',sprintf($page_strings['old_version_warning'],'<a href="page.php?space_key='.$space_key.'&module_key='.$module_key.'">'.$page_strings['current_version'].'</a><br/>'));
	}
} else {
	$version_limit='';
}
$rs = $CONN->SelectLimit("SELECT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}modules.name,body FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}pages WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}pages.module_key AND ({$CONFIG['DB_PREFIX']}modules.module_key='$module_key') $version_limit ORDER BY page_key DESC",1);

if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');

}

$objHtml = new InteractHtml();
$module_key2 = $rs->fields[0];
$body = $rs->fields[2];

//replace any garbage MS characters 
$body = ereg_replace(0x92, "'", $body);
$body = ereg_replace(0x91, "'", $body);	
$body = ereg_replace(0x93, "&#147;", $body);
$body = ereg_replace(0x94, "&#148;", $body);
$body = ereg_replace(' ', " ", $body);
$body = ereg_replace('%3f', '?', $body);
$body = $objHtml->parseText($body);
	
if ($CONFIG['ALLOW_PHP']==true) {

	$body = eval_html($body);
		
}
	
$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key2);
$t->set_var('BODY',$body);
	

if ($is_admin==true) {
	
	$admin_image=get_admin_tool("{$CONFIG['PATH']}/modules/page/page_input.php?space_key=$space_key&module_key=$module_key2&link_key=$link_key&parent_key={$page_details['parent_key']}&action=modify");
	
}
	
$t->set_var('ADMIN_IMAGE',$admin_image);
$t->set_strings('page',  $page_strings);
$t->set_var('ADMIN_TOOL_CLASS',get_admin_tool_class());

$t->parse('CONTENTS', 'page', true);


$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
	   
$CONN->Close();
exit;

function eval_buffer($string) { 
   ob_start(); 
   eval("$string[2];"); 
   $ret = ob_get_contents(); 
   ob_end_clean(); 
   return $ret; 
} 

function eval_html($string) { 
   return preg_replace_callback("/(<\?php|<\?)(.*?)\?>/si", 
"eval_buffer",$string); 
} 

?>
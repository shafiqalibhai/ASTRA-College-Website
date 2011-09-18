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
* @version $Id: page_diff.php,v 1.7 2007/01/07 22:25:26 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/page_strings.inc.php');
if (!isset($objDate)) {
	if (!class_exists('InteractDate')) {
		require_once('../../includes/lib/date.inc.php');
	}
	$objDate = new InteractDate();				
}
require_once('lib.inc.php');
$objPage = new InteractPage();
//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$message	= $_GET['message'];
$diff1 =  $_GET['diff1'];
$diff2 =  $_GET['diff2'];
check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$is_admin = check_module_edit_rights($module_key);
$page_data = $objPage->getPageData($module_key);
if (($page_data['page_edit_rights']==0 && !$is_admin && $_SESSION['userlevel_key']!=1) || ($page_data['page_edit_rights']==2 && empty($current_user_key)) || ($page_data['page_edit_rights']==1 && !$is_space_member)) {
	header("Location: {$CONFIG['FULL_URL']}/modules/page/page.php?space_key=$space_key&module_key=$module_key&message=".urlencode($general_strings['no_access_rights']));
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'page'			=> 'pages/pagediff.ihtml',
	'footer'		  => 'footer.ihtml'
));

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true); 
include_once '../../includes/pear/Text/Diff.php';
include_once '../../includes/pear/Text/Diff/Renderer.php';
include_once '../../includes/pear/Text/Diff/Renderer/inline.php';
get_navigation();

$rs = $CONN->SelectLimit("SELECT {$CONFIG['DB_PREFIX']}pages.body,{$CONFIG['DB_PREFIX']}pages.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name  FROM {$CONFIG['DB_PREFIX']}pages,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}pages.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND ({$CONFIG['DB_PREFIX']}pages.module_key='$module_key' AND (page_key='$diff1' OR page_key='$diff2')) ORDER BY page_key DESC",2);

if (!class_exists('InteractHtml')) {
	require_once('../../includes/lib/html.inc.php');
}
$objHtml = new InteractHtml();
$n=1;
while(!$rs->EOF) {
	if ($n==1) {
		$body2 = $rs->fields[0];
		$body2 = $objHtml->parseText($body2);
		$version_details = $objDate->formatDate($CONN->UnixTimestamp($rs->fields[1]),'short', true).' - '.$rs->fields[2].' '.$rs->fields[3];
	} else {
		$body1 = $rs->fields[0];
		$body1 = $objHtml->parseText($body1);
		$version_details = $objDate->formatDate($CONN->UnixTimestamp($rs->fields[1]),'short', true).' - '.$rs->fields[2].' '.$rs->fields[3].' : '.$version_details;
	}
	$n++;
	$rs->MoveNext();
}
$body1 = explode("\n",$body1);
$body2 = explode("\n",$body2);	
$diff = &new Text_Diff($body1, $body2);

/* Output the diff in unified format. */
$renderer = &new Text_Diff_Renderer_inline();
$body =  $renderer->render($diff);

$t->set_var('SPACE_KEY',$space_key);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('VERSION_DETAILS',$version_details);
$t->set_var('BODY',$body);

$t->set_strings('page',  $page_strings);	
$t->parse('CONTENTS', 'page', true);

$rs->Close();
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
	   
$CONN->Close();
exit;

?>
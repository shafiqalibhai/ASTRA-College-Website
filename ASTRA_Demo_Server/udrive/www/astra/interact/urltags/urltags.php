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
* View tagged urls
*
* View url tags added by and for a user
*
* @package urlTags
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: urltags.php,v 1.7 2007/01/07 22:25:33 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

$current_user_key = $_SESSION['current_user_key'];
$category_key = isset($_POST['category_key'])? $_POST['category_key']: '';
if(!isset($_GET['space_key'])) {
	$space_key = $CONFIG['DEFAULT_SPACE_KEY'];
} else {
	$space_key = $_GET['space_key'];
}

require_once($CONFIG['LANGUAGE_CPATH'].'/tag_strings.inc.php');
//check to see if user is logged in. If not refer to Login page.
authenticate_home();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header' => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'body'   => 'urltags/urltags.ihtml',
	'footer' => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$module_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('PAGE_TITLE',$tag_strings['view_tags']);
$t->set_var('TOP_BREADCRUMBS','');

$t->set_var('TAGS_HEADING',$tag_strings['view_tags']);
$t->set_var('ADDED_BY_YOU_STRING',$tag_strings['added_by_you']);
$t->set_var('ADDED_FOR_YOU_STRING',$tag_strings['added_for_you']);
$t->set_var('YOUR_LINKS_STRING',sprintf($general_strings['your_links'],$general_strings['space_plural']));
$t->set_var('SEARCH_STRING',$general_strings['search']);
$t->parse('CONTENTS', 'header', true);
get_navigation();  



//get tags added by user

$sql = "SELECT heading, url, {$CONFIG['DB_PREFIX']}tagged_urls.url_key, {$CONFIG['DB_PREFIX']}tagged_urls.space_key FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE added_by_key='$current_user_key' ORDER BY heading DESC";

$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
if ($rs->EOF) {

	$t->set_var('BY_YOU_TAGS',$tag_strings['no_tags']);
	
} else {

	while (!$rs->EOF) {
	
		//$note = substr(strip_tags($rs->fields[0]),0,50);
		//$note .= '...';
		$heading = $rs->fields[0];
		$url  = $rs->fields[1];
		$url_key  = $rs->fields[2];
		$space_key  = $rs->fields[3];
		if (strrpos($url,'http://')) {
			$by_you_tags .= "<p><a href=\"{$CONFIG['PATH']}$url#tag\">$heading</a>".get_admin_tool($CONFIG['PATH']."/urltags/urltaginput.php?space_key=1&url_key=$url_key&referer=tag_page&action=modify")."</p>";
		} else {
			$by_you_tags .= "<p><a href=\"$url\">$heading</a>".get_admin_tool($CONFIG['PATH']."/urltags/urltaginput.php?space_key=1&url_key=$url_key&referer=tag_page&action=modify")."</p>";
		}
		$rs->MoveNext();
		
	}

	$rs->Close();	
	$t->set_var('BY_YOU_TAGS',$by_you_tags);

}

//get notes added for user

// find out what groups and spaces user is a member of

if (!class_exists('InteractUser')) {

	require_once('../includes/lib/user.inc.php');
	
}

$user = new InteractUser();
$groups_data  = $user->getGroupsData($_SESSION['current_user_key']);
$groups_sql   = $groups_data['groups_sql'];

$spaces_data  = $user->getSpacesData($_SESSION['current_user_key']);
$spaces_sql   = $spaces_data['spaces_sql'];

$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}tagged_urls.heading, {$CONFIG['DB_PREFIX']}tagged_urls.url, {$CONFIG['DB_PREFIX']}tagged_urls.added_by_key, {$CONFIG['DB_PREFIX']}tagged_urls.date_added
FROM 
{$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}tagged_urls LEFT JOIN {$CONFIG['DB_PREFIX']}group_user_links ON {$CONFIG['DB_PREFIX']}tagged_urls.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key
WHERE
(
({$CONFIG['DB_PREFIX']}tagged_urls.added_for_key='$current_user_key')
OR 
({$CONFIG['DB_PREFIX']}tagged_urls.added_for_key='-1' AND {$CONFIG['DB_PREFIX']}tagged_urls.space_key in $spaces_sql AND {$CONFIG['DB_PREFIX']}tagged_urls.group_key='0') 
OR
({$CONFIG['DB_PREFIX']}tagged_urls.added_for_key='-1' AND {$CONFIG['DB_PREFIX']}tagged_urls.group_key in $groups_sql) 
OR
({$CONFIG['DB_PREFIX']}tagged_urls.added_for_key='0' AND {$CONFIG['DB_PREFIX']}tagged_urls.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key='1')
OR
({$CONFIG['DB_PREFIX']}tagged_urls.added_for_key='0' AND  {$CONFIG['DB_PREFIX']}group_user_links.user_key='$current_user_key' AND {$CONFIG['DB_PREFIX']}group_user_links.access_level_key='1')
)
AND {$CONFIG['DB_PREFIX']}tagged_urls.added_by_key!='$current_user_key'
ORDER BY {$CONFIG['DB_PREFIX']}tagged_urls.date_added Desc";

$rs = $CONN->Execute($sql);

if ($rs->EOF) {

	$t->set_var('FOR_YOU_TAGS',$tag_strings['no_tags']);
	
} else {

				
	//create date object for date functions
	if (!class_exists('InteractDate')) {
	
		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
		
	}
	
	$dates = new InteractDate();
	
	while (!$rs->EOF) {
	
		$heading = $rs->fields[0];
		$url  = $rs->fields[1];
		$user_data = $user->getUserData($rs->fields[2]);
		$date_added = $dates->formatDate($CONN->UnixTimestamp($rs->fields[3]));
		$for_you_tags.= '<p><a href="'.$CONFIG['PATH'].$url.'#tag">'.$heading.'</a><br /><span class="small">'.$user_data['first_name'].' '.$user_data['last_name'].' '.$date_added.'</span></p>';
		$rs->MoveNext();
		
	}

	$rs->Close();	
	$t->set_var('FOR_YOU_TAGS',$for_you_tags);

}

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
exit;
$CONN->Close();
?>
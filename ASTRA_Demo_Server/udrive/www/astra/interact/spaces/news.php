<?php
/**
* News display
*
* Displays news items, either from single space or from all users spaces
*
* @package Spaces
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');


//check to see if user is logged in. If not refer to Login page.
$space_key  = isset($_GET['space_key'])? $_GET['space_key'] : '';
$offset	 = isset($_GET['offset'])? $_GET['offset'] : 2;
$current_user_key=$_SESSION['current_user_key'];

$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];   



//if they have not logged in before set their last login to today
if ($_SESSION['last_use']>0) {

	$last_use = $_SESSION['last_use'];
	
} else {

	$last_use = date('Y-m-d H:i:s');
	
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'body'	   => 'spaces/news.ihtml',
	'footer'	 => 'footer.ihtml'));
// get page details for titles and breadcrumb navigation
$page_details=get_page_details($space_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('NEWS_HEADING',$general_strings['news']);
if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
	
	if ($CONFIG['DISPLAY_LATEST']==0 || isset($_POST['last_use'])) {

		$date_limit = "AND {$CONFIG['DB_PREFIX']}news.date_added>'$last_use'";

	}
	
	$sql = "SELECT 
		{$CONFIG['DB_PREFIX']}news.heading,
		{$CONFIG['DB_PREFIX']}news.body,
		{$CONFIG['DB_PREFIX']}news.date_added, 
		first_name, last_name, 
		{$CONFIG['DB_PREFIX']}news.user_key, 
		{$CONFIG['DB_PREFIX']}news.news_key, 
		{$CONFIG['DB_PREFIX']}spaces.name,
		{$CONFIG['DB_PREFIX']}news.space_key, 
		{$CONFIG['DB_PREFIX']}space_user_links.user_key
		FROM 
		{$CONFIG['DB_PREFIX']}news, 
		{$CONFIG['DB_PREFIX']}users, 
		{$CONFIG['DB_PREFIX']}spaces LEFT JOIN
		{$CONFIG['DB_PREFIX']}space_user_links ON
		{$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key 
		WHERE 
		{$CONFIG['DB_PREFIX']}news.user_key={$CONFIG['DB_PREFIX']}users.user_key 
		AND 
		{$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}news.space_key
		AND 
		({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key' 
		OR 
		({$CONFIG['DB_PREFIX']}space_user_links.user_key IS NULL AND 		{$CONFIG['DB_PREFIX']}news.space_key='$space_key'))
		ORDER BY {$CONFIG['DB_PREFIX']}news.date_added DESC";
	
} else {

	$sql = "SELECT {$CONFIG['DB_PREFIX']}news.heading,{$CONFIG['DB_PREFIX']}news.body,{$CONFIG['DB_PREFIX']}news.date_added, first_name, last_name,  {$CONFIG['DB_PREFIX']}news.user_key, {$CONFIG['DB_PREFIX']}news.news_key FROM {$CONFIG['DB_PREFIX']}news, {$CONFIG['DB_PREFIX']}users  WHERE {$CONFIG['DB_PREFIX']}news.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}news.space_key='$space_key'  ORDER BY {$CONFIG['DB_PREFIX']}news.date_added DESC";

}

		
$t->set_block('body', 'NewsBlock', 'NWBlock');
$rs = $CONN->Execute($sql);
$total_news = $rs->RecordCount();
$rs = $CONN->SelectLimit($sql,10, $offset);

if (!class_exists('InteractDate')) {

	require_once('../includes/lib/date.inc.php');
	
}

$objDates = new InteractDate();


if (!class_exists('InteractUser')) {

	require_once('../includes/lib/user.inc.php');
	
}

$objUser = new InteractUser();

while (!$rs->EOF) {

$heading = $rs->fields[0];
		
		$body=$objHtml->parseText($rs->fields[1]);
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'long');	
		$name = $rs->fields[3].' '.$rs->fields[4];
		$user_key = $rs->fields[5];
		$news_key = $rs->fields[6];
		$space_name = $rs->fields[7];
		$news_space_key = $rs->fields[8];
		$photo_tag = $objUser->getUserphotoTag($user_key, '40');
	
		if ($photo_tag==false) {
	
			$photo_tag='&nbsp;';
	
		}
		if ($_SESSION['userlevel_key']=='1' || $accesslevel_key=='1' || $accesslevel_key=='3') {
            
	    	$admin_image=get_admin_tool("{$CONFIG['PATH']}/news/newsinput.php?space_key=$space_key&news_key=$news_key&action=modify");
        	$t->set_var('ADMIN_IMAGE',$admin_image);
        
		}
		$t->set_var('PHOTO_TAG',$photo_tag);
		$t->set_var('SPACE_HEADING',$space_name);
		$t->set_var('HEADING',$heading);
		$t->set_var('ADDED_BY',$name);
		$t->set_var('DATE_ADDED',$date_added);
		$t->set_var('BODY',$body);
		$t->set_var('SPACE_HEADING',$space_name);
		$t->set_var('NEWS_SPACE_KEY',$news_space_key);	
		$t->parse('NWBlock', 'NewsBlock', true);
		$rs->MoveNext();
		}

$rs->Close();

if ($offset>=10) {

	$previous_offset = $offset-10;
	$t->set_var('PREVIOUS_LINK'," < <a href=\"news.php?space_key=$space_key&offset=$previous_offset\">{$general_strings['previous']}</a>");

}

$next_offset = $offset+10;
if ($next_offset<$total_news) {

	$next_offset = $offset+10;
	$t->set_var('NEXT_LINK',"<a href=\"news.php?space_key=$space_key&offset=$next_offset\">{$general_strings['next']}</a> > ");

}


if (!isset($space_key) || $space_key=='') {
 
	$t->set_var('BREADCRUMBS','');
	$t->set_var('PAGE_TITLE',$general_strings['news']);
	$t->set_var('SPACE_TITLE','');
	$t->set_var('MAKE_MEMBER','');
	$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
	$t->set_var('MHBlock','');	
	
}
$t->parse('CONTENTS', 'header', true);

get_navigation();

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

$CONN->Close();
exit;




?>

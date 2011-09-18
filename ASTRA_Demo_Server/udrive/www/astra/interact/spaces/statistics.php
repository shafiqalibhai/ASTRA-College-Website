<?php
/*
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$begintime = $time;
*/
/**
* Space homepage
*
* Displays a space home page with header, news items, and if 
* the space contains a calendar, a list of important dates for the month
*
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

if (!empty($_POST['show_new'])) {
	$show_new = $_POST['show_new'];
	$last_use = date('Y-m-d H:i:s',time()-($_POST['show_new']*86400));
} else if ($_SESSION['last_use']>0) {
	$last_use = $_SESSION['last_use'];
} else {
//if they have not logged in before set their last login to today
	$last_use = date('Y-m-d H:i:s');
}
$db_last_use = $CONN->DBDate($last_use);
$current_user_key = $_SESSION['current_user_key'];

//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');
$space_key 	= $_GET['space_key'];
$message = isset($_GET['message'])?$_GET['message']:'';

//check we have the required variables
check_variables(true,false);
	
if (!$space_key) {
	$space_key = $_SESSION['current_space_key'];
} else if ($space_key!=$_SESSION['current_space_key']){
	$_SESSION['current_space_key'] = $space_key;
}



//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = isset($access_levels['accesslevel_key'])?$access_levels['accesslevel_key']:'';
$group_access = $access_levels['groups']; 
if (!isset($objSpace)) {
	if (!class_exists('InteractSpace')) {
		require_once('lib.inc.php');
	}
	$objSpace = new InteractSpace();				
}
if ((!empty($_SESSION['current_user_key']) && $_SESSION['userlevel_key']!=1 && !$CONN->GetOne("SELECT user_key FROM {$CONFIG['DB_PREFIX']}statistics WHERE user_key='$current_user_key' AND space_key='$space_key'") && !$CONN->ErrorMsg()) || !empty($_GET['first_login'])){
	$objSpace->newUserAlert($space_key,$current_user_key);
	statistics('read');
	header("Location: {$CONFIG['FULL_URL']}/spaces/welcome.php?space_key=$space_key");
	exit;		
}
statistics('read');   


$objHtml = singleton::getInstance('html');
$objDate = singleton::getInstance('date');				
$objUser = singleton::getInstance('user');

	
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		=> 'headerFp.ihtml',
	'body'			=> 'spaces/statistics.ihtml',
));


$disclosures = !empty($_COOKIE['disclosures'])? explode('&',$_COOKIE['disclosures']):'';

foreach(array('whoOnline','whatsNew', 'eventsTable','updatedItems','latestPostings','latestItems') as $value) {
	if (in_array($value.'=0',$disclosures)){
		$t->set_var(strtoupper($value).'_CONTENT_DEFAULTS','style="display:none"');
		$t->set_var(strtoupper($value).'_DEFAULT_STYLE','Closed');
	} else {
		$t->set_var(strtoupper($value).'_DEFAULT_STYLE','Open');		
	}
}
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

//get space home hit count
$hit_count = $CONN->GetOne("SELECT Count(space_key) FROM {$CONFIG['DB_PREFIX']}statistics WHERE space_key='$space_key' AND module_key='0'");
$t->set_var('PAGE_HITS',"Page views - $hit_count");
if ($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
	$t->set_var('FULL_SPACE_NAME',$page_details['full_space_name']);
}
if (($_SESSION['userlevel_key']==1 || $accesslevel_key=='1' || $accesslevel_key=='3')) {
			
		$admin_string = sprintf($general_strings['space_admin'], ucfirst($general_strings['space_text']));
		$t->set_var('SPACE_ADMIN_TOOL',' '.get_admin_tool($CONFIG['PATH'].'/spaceadmin/admin.php?space_key='.$space_key,true,$admin_string,'spanner'));
			
}	
$t->set_var('META_TAGS','<meta name="description"
content="'.$page_details['space_description'].'">');


//count number of users online
if ($space_key==$CONFIG['DEFAULT_SPACE_KEY']) {
	$online_user_count = $CONN->GetOne("SELECT count(DISTINCT user_key) FROM {$CONFIG['DB_PREFIX']}online_users WHERE user_key!='0' AND  status_key=1");
	$guest_count = '<br />Guests: '.$CONN->GetOne("SELECT count(user_key) FROM {$CONFIG['DB_PREFIX']}online_users WHERE user_key='0' OR  status_key=0");
	
} else {
	$online_user_count = $CONN->GetOne("SELECT count(DISTINCT {$CONFIG['DB_PREFIX']}online_users.user_key) FROM {$CONFIG['DB_PREFIX']}online_users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}online_users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key<3 AND {$CONFIG['DB_PREFIX']}online_users.user_key!='0' AND {$CONFIG['DB_PREFIX']}online_users.status_key=1 ORDER BY time DESC");
}
if (empty($online_user_count) || empty($_SESSION['current_user_key'])) {
	$member_count = $online_user_count;	
} else {
	$member_count = ''.$online_user_count.'';
}
$t->set_var('USERS_ONLINE',$space_strings['members_online'].': '.$member_count.$guest_count);

//if portfolio space then remove new items block
if ($page_details['type_key']==1) {
	$t->set_block('body', 'UpdatesBlock', 'UpdtsBlock');
	$t->set_var('UpdtsBlock','');	
} else {
	//create dropdown to select days of new items to show
	$t->set_var('SHOW_NEW_MENU',$objHtml->arrayToMenu(array('0' => $general_strings['since_last_login'], '1' => $general_strings['today'], '3' => sprintf($general_strings['for_last_days'],3), '7' => sprintf($general_strings['for_last_days'],7), '30' => sprintf($general_strings['for_last_days'],30)),'show_new',$show_new,'','',true,'class="formTxtInput small" onchange="this.form.submit();"'));
	$t->set_var('SHOW_NEW_ITEMS',$general_strings['show_new']);
}

//see if we have any new members, if so show new members links
$new_members = ($space_key!=$CONFIG['DEFAULT_SPACE_KEY'])?$CONN->GetOne("SELECT COUNT(user_key) FROM {CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND date_added>$db_last_use"):'';
if(!empty($new_members)) {
	$t->set_var('NEW_SPACE_MEMBERS_STRING','<a href="newmembers.php?space_key='.$space_key.'">('.sprintf($general_strings['new_postings_count'], $new_members).')</a>');	
} else {
	$t->set_block('navigation', 'NewMembersBlock', 'NMBlock');
	$t->set_var('NMBlock','');	
}



if ($_SESSION['userlevel_key']=='1' || $accesslevel_key=='1' || $accesslevel_key=='3') {
	if(empty($page_details['header'])) {
		$space_header = '<span'.get_admin_tool_class().'>'.$space_strings['space_welcome_prompt'].'</span>';
	} else {
		$space_header = $objHtml->parseText($page_details['header']);
	}
	$t->set_var('EDIT_HEADER',get_admin_tool('../spaceadmin/spaceheader.php?space_key='.$space_key,true));
} else {
	$space_header = (!empty($page_details['header']))?$objHtml->parseText($page_details['header']):'';
	$t->set_var('EDIT_HEADER','');
}
	
$t->set_var('HEADER',$space_header);
$t->parse('CONTENTS', 'header', true);
get_navigation(true,true);
if ($_SESSION['userlevel_key']==1 || $accesslevel_key==1 || $accesslevel_key==3) {
	$add_block_one = sprintf($space_strings['add_block_one'], $general_strings['module_text']);
	$t->set_var('ADD_BLOCK_ONE_LINK',get_admin_tool('../modules/general/moduleadd.php?space_key='.$space_key.'&block_key=1',true,$add_block_one,'plus'));
	$t->set_var('ADD_BLOCK_TWO_LINK',get_admin_tool('../modules/general/moduleadd.php?space_key='.$space_key.'&block_key=2',true,$add_block_one,'plus'));

}

// find out what groups and spaces user is a member of
$groups_data  = $objUser->getGroupsData($_SESSION['current_user_key'], $space_key);
$groups_sql   = $groups_data['groups_sql'];
$group_access = $groups_data['groups_array'];
$spaces_data  = $objUser->getSpacesData($_SESSION['current_user_key']);
$spaces_sql   = $spaces_data['spaces_sql'];

if ($_SESSION['userlevel_key']=='1' || $accesslevel_key=='1' || $accesslevel_key=='3' && $page_details['type_key']!=1) {
	$add_news_link = get_admin_tool('../news/newsinput.php?space_key='.$space_key,true,$space_strings['add_news'],'plus');
	$t->set_var('ADD_NEWS_LINK',$add_news_link);
}

//get any news items for this space

$t->set_block('body', 'NewsBlock', 'NWBlock');

if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
	 
	if ($CONFIG['DISPLAY_LATEST']==0 || isset($_POST['last_use'])) {

		$date_limit = "AND {$CONFIG['DB_PREFIX']}news.date_added>$db_last_use";

	}
	
	$sql = "SELECT DISTINCT
		{$CONFIG['DB_PREFIX']}news.heading,
		{$CONFIG['DB_PREFIX']}news.body,
		{$CONFIG['DB_PREFIX']}news.date_added, 
		first_name, last_name, 
		{$CONFIG['DB_PREFIX']}news.user_key, 
		{$CONFIG['DB_PREFIX']}news.news_key, 
		{$CONFIG['DB_PREFIX']}spaces.name,
		{$CONFIG['DB_PREFIX']}news.space_key,
		{$CONFIG['DB_PREFIX']}news.options
		FROM 
		{$CONFIG['DB_PREFIX']}news, 
		{$CONFIG['DB_PREFIX']}users, 
		{$CONFIG['DB_PREFIX']}spaces 
		WHERE 
		{$CONFIG['DB_PREFIX']}news.user_key={$CONFIG['DB_PREFIX']}users.user_key 
		AND 
		{$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}news.space_key
		AND 
		(({$CONFIG['DB_PREFIX']}news.space_key IN $spaces_sql $date_limit) 
		OR 
		 ({$CONFIG['DB_PREFIX']}news.space_key='{$CONFIG['DEFAULT_SPACE_KEY']}'))
		ORDER BY {$CONFIG['DB_PREFIX']}news.date_added DESC";

} else {

	if (isset($_POST['last_use'])) {
		$date_limit = "AND {$CONFIG['DB_PREFIX']}news.date_added>$db_last_use";
	}
	$sql = "SELECT {$CONFIG['DB_PREFIX']}news.heading,{$CONFIG['DB_PREFIX']}news.body,{$CONFIG['DB_PREFIX']}news.date_added, first_name, last_name,  {$CONFIG['DB_PREFIX']}news.user_key, {$CONFIG['DB_PREFIX']}news.news_key, null,null,{$CONFIG['DB_PREFIX']}news.options FROM {$CONFIG['DB_PREFIX']}news, {$CONFIG['DB_PREFIX']}users  WHERE {$CONFIG['DB_PREFIX']}news.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}news.space_key='$space_key'  $date_limit ORDER BY {$CONFIG['DB_PREFIX']}news.date_added DESC";

}
$rs = $CONN->SelectLimit($sql,4);

if (($rs->EOF && $_SESSION['userlevel_key']!='1' && $accesslevel_key!='1' && $accesslevel_key!='3') || $page_details['type_key']==1) {
	$t->set_block('body', 'NewsHeadingBlock', 'NWHBlock');
	$t->set_var('NWHBlock','');
} else {
	$t->set_var('NEWS_STRING',$general_strings['news']);
	$n=0;
	while (!$rs->EOF && $n<2) {
		$heading = $rs->fields[0];
		$body=$objHtml->parseText($rs->fields[1]);
		$date_added = $objDate->formatDate($CONN->UnixTimeStamp($rs->fields[2]),'long');	
		$name = $rs->fields[3].' '.$rs->fields[4];
		$user_key = $rs->fields[5];
		$news_key = $rs->fields[6];
		$space_name = $rs->fields[7];
		$news_space_key = $rs->fields[8];
		$options = $rs->fields[9];
		if ($options==1) {
			$photo_tag = $objUser->getUserphotoTag($user_key, '40');
			if ($photo_tag==false) {
				$photo_tag='&nbsp;';
			}
		} else {
			$photo_tag='&nbsp;';
		}
		if ($_SESSION['userlevel_key']=='1' || $accesslevel_key=='1' || $accesslevel_key=='3') {
            
	    	$admin_image=get_admin_tool("{$CONFIG['PATH']}/news/newsinput.php?space_key=$space_key&news_key=$news_key&action=modify");
        	$t->set_var('ADMIN_IMAGE',$admin_image);
        
		}
		$t->set_var('PHOTO_TAG',$photo_tag);
		$t->set_var('SPACE_HEADING',($news_space_key!=$CONFIG['DEFAULT_SPACE_KEY'])?$space_name:'');
		$t->set_var('HEADING',$heading);
		$t->set_var('ADDED_BY',$name);
		$t->set_var('DATE_ADDED',$date_added);
		$t->set_var('BODY',$body);
		$t->set_var('NEWS_SPACE_KEY',$news_space_key);	
		$t->parse('NWBlock', 'NewsBlock', true);
		$n++;				
		$rs->MoveNext();
	}
	
}
if ($rs->RecordCount()<=2) {
	$t->set_block('body', 'MoreNewsBlock', 'MNBlock');
	$t->set_var('MNBlock','');
} else {
	$t->set_var('MORE_NEWS_STRING',$space_strings['more_news']);
}
$rs->Close();

//find out if there are any module in this space, if not don't display current item boxes and give prompt on ow to add content


if (!$CONN->GetOne("SELECT module_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'")) {
	$t->set_block('body', 'UpdatesBlock', 'UDBlock');
	$t->set_var('UDBlock','');
	$t->set_var('SPACE_NAME','');
	if ($_SESSION['userlevel_key']=='1' || $accesslevel_key=='1' || $accesslevel_key=='3') {
		$t->set_var('MESSAGE',sprintf($space_strings['space_content_prompt'],$general_strings['space_text']));
	}
} else {

	//find any calendars so we can grab calendar events
	if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key 
		FROM
			{$CONFIG['DB_PREFIX']}modules,
			{$CONFIG['DB_PREFIX']}module_space_links,
			{$CONFIG['DB_PREFIX']}space_user_links 
		WHERE 
			{$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
			AND
			{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key
			AND
			type_code='calendar' 
			AND 
			{$CONFIG['DB_PREFIX']}module_space_links.status_key='1' 
			AND 
			(({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key' AND 
			({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
			OR
			{$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql)) )";
	} else {

		$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.module_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND type_code='calendar') AND ({$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}modules.status_key!='4') AND
{$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql)";

	}

	$rs = $CONN->Execute($sql);
	$t->set_block('body', 'EventTableBlock', 'EvntTbleBlock');
	
	if (!$rs->EOF) {
		$n=0;
		$parent_array=array();
		$mods='';
		while (!$rs->EOF) {
			if ($mods) {$mods.=',';}
			$mods.="'{$rs->fields[0]}'";
			get_cal_parents($rs->fields[0]);
			while (list ($key, $val) = each ($parent_array)) {
				$mods .= ",'$val'"; 
			}
			$rs->MoveNext();
		}
		$rs->Close();
		$month=date('m');
		$year=date('Y');
		$sql = "SELECT event_key, event_date,name, module_key FROM {$CONFIG['DB_PREFIX']}calendar_events WHERE (module_key in ($mods)) AND event_date >= CURDATE() AND event_date <= DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY) ORDER BY event_date";
		$rs = $CONN->Execute($sql);
		$t->set_var('IMPORTANT_DATES_STRING',$space_strings['important_dates']);
		if ($rs->EOF) {
			$events=$space_strings['no_events'];
		} else {
			while (!$rs->EOF) {
				$event_key = $rs->fields[0];
				$event_date = $objDate->formatDate($CONN->UnixTimeStamp($rs->fields[1]),'short');
				$name = $rs->fields[2];
				$module_key2 = $rs->fields[3];
				$events = $events."<li><a href=\"../modules/calendar/event.php?space_key=$space_key&amp;module_key=$module_key2&amp;event_key=$event_key\" >$event_date</a> - $name</li>";
				$rs->MoveNext();
			}
			$rs->Close();
		}
		$t->set_var('EVENTS',$events);
		$t->parse('EvntTbleBlock', 'EventTableBlock', true);
	}
	
	//now count new postings
	if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
	
		$sql = "SELECT DISTINCT COUNT({$CONFIG['DB_PREFIX']}posts.post_key) FROM  {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}posts.date_added >= $db_last_use AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND {$CONFIG['DB_PREFIX']}modules.type_code!='journal' AND (({$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND ({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key') AND  ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) OR ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key'))";
		
	} else {

		$sql = "SELECT DISTINCT COUNT({$CONFIG['DB_PREFIX']}posts.post_key) FROM  {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}modules WHERE  {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key') AND ({$CONFIG['DB_PREFIX']}posts.date_added >= $db_last_use) AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND {$CONFIG['DB_PREFIX']}modules.type_code!='journal'";

	}
	
	$record_count=$CONN->GetOne($sql);

	
	if ($CONFIG['DISPLAY_LATEST']==0 || isset($_POST['last_use'])) {

		$date_limit = "AND {$CONFIG['DB_PREFIX']}posts.date_added>$db_last_use";

	}
	if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
	
		$sql = "SELECT DISTINCT 
		{$CONFIG['DB_PREFIX']}posts.post_key, 
		subject, 
		thread_key, 
		{$CONFIG['DB_PREFIX']}posts.module_key, 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key,
		{$CONFIG['DB_PREFIX']}modules.type_code,
		{$CONFIG['DB_PREFIX']}posts.entry_key 
		FROM  
		{$CONFIG['DB_PREFIX']}module_space_links,
		{$CONFIG['DB_PREFIX']}space_user_links, 
		{$CONFIG['DB_PREFIX']}posts,
		{$CONFIG['DB_PREFIX']}modules 
		WHERE 
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key 
		AND 
		{$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key 
		AND 
		({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key') 
		AND 
		({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
		OR 
		{$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql)
		AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1'
		AND {$CONFIG['DB_PREFIX']}modules.type_code!='journal' 
		$date_limit
		ORDER BY 
		{$CONFIG['DB_PREFIX']}posts.date_added DESC";
		
	} else {	
	
		//now retrieve latest 5 forum postings

		$sql = "SELECT DISTINCT 
		{$CONFIG['DB_PREFIX']}posts.post_key, 
		subject, 
		thread_key, 
		{$CONFIG['DB_PREFIX']}posts.module_key, 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key,
		{$CONFIG['DB_PREFIX']}modules.type_code,
		{$CONFIG['DB_PREFIX']}posts.entry_key
		FROM  
		{$CONFIG['DB_PREFIX']}module_space_links,
		{$CONFIG['DB_PREFIX']}posts,
		{$CONFIG['DB_PREFIX']}modules
		WHERE 
		{$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key 
		AND 
		({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key') 
		AND 
		({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
		OR 
		{$CONFIG['DB_PREFIX']}module_space_links.group_key IN $groups_sql) 
		AND 
		{$CONFIG['DB_PREFIX']}module_space_links.status_key='1'
		 AND {$CONFIG['DB_PREFIX']}modules.type_code!='journal'
		$date_limit
		ORDER BY 
		{$CONFIG['DB_PREFIX']}posts.date_added DESC";

	}
	$rs = $CONN->SelectLimit($sql,5);
	$n=0;
	if (!$rs->EOF) {
		$t->set_block('body', 'NewPostBlock', 'NPBlock');
		while (!$rs->EOF) {
			$t->set_var('POST_KEY',$rs->fields[0]);
			$t->set_var('SUBJECT',$rs->fields[1]);
			$t->set_var('THREAD_KEY',$rs->fields[2]);
			$t->set_var('MODULE_KEY',$rs->fields[3]);
			$t->set_var('FORUM_SPACE_KEY',$rs->fields[4]);
			$t->set_var('ENTRY_KEY',$rs->fields[6]);
			
			if ($rs->fields[5]=='forum') {
				$t->set_var('POST_URL','forum/thread.php');
			} else {
				$t->set_var('POST_URL', $rs->fields[5].'/entry.php');
			}						
			$t->parse('NPBlock', 'NewPostBlock', true);
			$n++;
			$rs->MoveNext();
	
		}
	
		if ($rs->RecordCount()>1) {
	
			$days = !empty($_POST['show_new'])?$_POST['show_new']:'';
			$t->set_var('MORE_POSTS_LINK','<div align="right"><a href="newposts.php?show_read_posts=1&days='.$days.'">...'.$general_strings['show_all'].' ('.$rs->RecordCount().')</a></div>');
	
		}

	} else {

		$t->set_block('body', 'NewPostsBlock', 'NPSBlock');
		$t->set_var('NPSBlock','');

	}

	if ($CONFIG['DISPLAY_LATEST']==0 || isset($_POST['last_use'])) {

		$date_limit = "AND {$CONFIG['DB_PREFIX']}module_space_links.date_added>$db_last_use";

	}
	if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
	
		$sql = "
		SELECT DISTINCT 
		{$CONFIG['DB_PREFIX']}module_space_links.module_key, 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key, 
		{$CONFIG['DB_PREFIX']}modules.name,
		{$CONFIG['DB_PREFIX']}modules.type_code
		FROM  
		{$CONFIG['DB_PREFIX']}modules, 

		{$CONFIG['DB_PREFIX']}module_space_links,
		{$CONFIG['DB_PREFIX']}space_user_links 
		WHERE 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key 
		AND
		{$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
		AND 
		({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key')
		AND 
		({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
		OR 
		{$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) 	 
		AND
		({$CONFIG['DB_PREFIX']}module_space_links.status_key='1')
		AND
		({$CONFIG['DB_PREFIX']}modules.type_code!='heading')
		$date_limit
		ORDER BY 
		{$CONFIG['DB_PREFIX']}module_space_links.date_added DESC";
	
	} else {
	
		$sql = "
		SELECT DISTINCT 
		{$CONFIG['DB_PREFIX']}module_space_links.module_key, 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key, 
		{$CONFIG['DB_PREFIX']}modules.name,
		{$CONFIG['DB_PREFIX']}modules.type_code
		FROM  
		{$CONFIG['DB_PREFIX']}modules, 
		{$CONFIG['DB_PREFIX']}module_space_links
		WHERE 
		{$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key 
		AND 
		{$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' 
		AND 
		({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' 
		OR 
		{$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) 	 
		AND
		({$CONFIG['DB_PREFIX']}module_space_links.status_key='1')
		AND
		({$CONFIG['DB_PREFIX']}modules.type_code!='heading')
		$date_limit
		ORDER BY 
		{$CONFIG['DB_PREFIX']}module_space_links.date_added DESC";
	}
	
	$rs = $CONN->Execute($sql);
	$t->set_var('NEW_ITEMS_COUNT',$rs->RecordCount());
	$n=0;
	if (!$rs->EOF) {
		$t->set_block('body', 'NewItemBlock', 'NIBlock');
		while ($n<6) {
			$t->set_var('MODULE_KEY', $rs->fields[0]);
			$t->set_var('ITEM_SPACE_KEY', $rs->fields[1]);
			$t->set_var('NAME', $rs->fields[2]);
			$t->set_var('CODE', $rs->fields[3]);			
			$t->parse('NIBlock', 'NewItemBlock', true);	  
			$n++;		
			$rs->MoveNext();
		}
	
		if ($n==5 && $record_count>5) {
	
			$t->set_var('MORE_ITEMS_LINK','<div align="right"><a href="newitems.php?space_key='.$space_key.'">...'.$general_strings['more'].' ('.$record_count.')</a></div>');
	
		}	
	
		$rs->Close();

	} else {

		$t->set_block('body', 'NewItemsBlock', 'NISBlock');
		$t->set_var('NISBlock','');	

	}

	//now run through modules and get any updated items

	require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');
	if ($space_key == $CONFIG['DEFAULT_SPACE_KEY']) {
		$updates_space_key='';
	} else {
		$updates_space_key=$space_key;
	}
	$rs = $CONN->Execute("SELECT code From {$CONFIG['DB_PREFIX']}module_types");
	while (!$rs->EOF) {
		$code		= $rs->fields[0];
		$module_file = $CONFIG['BASE_PATH'].'/modules/'.$code.'/'.$code.'.inc.php';
	
		if (file_exists($module_file)) {
	
			include_once($module_file);
			$updated_items_function = 'updated_items_'.$code;
	   
			if (function_exists($updated_items_function)) {
				
				if (!$updated_items_function($current_user_key, $last_use, $groups_sql, $updated_items, $module_strings, $updates_space_key)) {
					echo "Error: could not run updated items functions for  $code\n";
					
				}
		
			}
		}
	
		$rs->MoveNext();
	
	}
	
	if (empty($updated_items)) {

		$t->set_var('UPDATED_ITEMS_LIST','<span>'.$general_strings['no_item_updates'].'</span>');	 

	} else {

		$t->set_var('UPDATED_ITEMS_LIST',$updated_items);	 
	
	}
	$n=0;
	while (!$rs->EOF) {
		
		$t->set_var('MODULE_KEY', $rs->fields[0]);
		$t->set_var('UPDATED_SPACE_KEY', $rs->fields[1]);
		$t->set_var('NAME', $rs->fields[2]);
		$t->set_var('CODE', $rs->fields[3]);			
		$t->parse('NIBlock', 'NewItemsBlock', true);	  
		$n++;		
		$rs->MoveNext();
		
	}
	if ($n==5 && $record_count>5) {
	
		 $t->set_var('MORE_ITEMS_LINK','<div align="right"><a href="newitems.php">...'.$general_strings['more'].'</a></div>');
	
	}	

	$rs->Close();

}

$block_one_components = $objSpace->getBlockComponents($space_key, 1,$accesslevel_key);
$block_two_components = $objSpace->getBlockComponents($space_key, 2,$accesslevel_key);

if (($block_one_components==false && ($_SESSION['userlevel_key']!=1 && $accesslevel_key!=1 && $accesslevel_key!=3)) || $page_details['type_key']==1) {
	$t->set_block('body', 'BlockOneBlock', 'BOneBlock');
	$t->set_var('BOneBlock','');	
} else {
	$t->set_var('BLOCK_ONE_COMPONENTS',$block_one_components);
}
if (($block_two_components==false && ($_SESSION['userlevel_key']!=1 && $accesslevel_key!=1 && $accesslevel_key!=3)) || $page_details['type_key']==1) {
	$t->set_block('body', 'BlockTwoBlock', 'BTwoBlock');
	$t->set_var('BTwoBlock','');	
} else {
	$t->set_var('BLOCK_TWO_COMPONENTS',$block_two_components);
}
$t->set_var('OTHER_SPACES',$spaces);
$t->set_strings('body',  $space_strings, '', '');
$t->parse('CONTENTS', 'body', true);
$date_modified = get_date_space_modified($space_key);
$t->set_var('DATE_MODIFIED_INFO', sprintf($space_strings['last_modified'],$general_strings['space_text'], $date_modified));
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p("CONTENTS");

$CONN->Close();
/*
$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$endtime = $time;
$totaltime = ($endtime - $begintime);
echo 'PHP parsed this page in ' .$totaltime. ' seconds.';
*/
exit;

function get_cal_parents($cal_key) 
{
	global $CONN,$n,$parent_array, $CONFIG;

	$sql = "SELECT parent_calendar_key from {$CONFIG['DB_PREFIX']}calendars,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links where {$CONFIG['DB_PREFIX']}calendars.module_key='$cal_key' AND ({$CONFIG['DB_PREFIX']}modules.module_key=parent_calendar_key AND {$CONFIG['DB_PREFIX']}modules.status_key!='4') AND ({$CONFIG['DB_PREFIX']}module_space_links.module_key=parent_calendar_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1')";
	$rs = $CONN->Execute($sql);

	while (!$rs->EOF) {
		$parent_cal_key = $rs->fields[0];
		$rs->Close();
		if ($parent_cal_key==0 || in_array($parent_cal_key,$parent_array)) {
			return;
		} else {
			$parent_array[$n++]= $parent_cal_key;
			get_cal_parents($parent_cal_key);
		}
		$rs->MoveNext();
	}
} //end get_cal_parents

?>
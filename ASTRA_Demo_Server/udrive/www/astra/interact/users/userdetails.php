<?php
/**
* User details
*
* Displays a users details
*
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/user_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/module_strings.inc.php');

$space_key = get_space_key();
$user_key  = $_GET['user_key'];
$pop_up = isset($_GET['pop_up'])?$_GET['pop_up']:''; 

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];

$userlevel_key = isset($_SESSION['userlevel_key'])?$_SESSION['userlevel_key']:'';
$current_user_key = isset($_SESSION['current_user_key'])?$_SESSION['current_user_key']:'';
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	  => 'header.ihtml',
	'navigation'  => 'navigation.ihtml',
	'userdetails' => 'users/userdetails.ihtml',
	'footer'	  => 'footer.ihtml'
));
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
$t->set_var('MHBlock','');
if (!isset($space_key) || $space_key=='') {
 
	$t->set_var('BREADCRUMBS','');
	$t->set_var('PAGE_TITLE',$title);
	$t->set_var('SPACE_TITLE','');
	$t->set_var("MAKE_MEMBER","");	
	
} else {

	$breadcrumbs = "<a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\" class=\"spaceHeadinglink\">".$page_details['space_name'].'</a> >';
	$t->set_var('BREADCRUMBS',$breadcrumbs);

}


$t->set_var('PAGE_TITLE',$page_details['full_space_name'].' - Member details');


$t->set_var('SPACE_KEY',$space_key);
$t->set_var('EMAIL_STRING',$general_strings['email']);
if (empty($pop_up)) {
	$t->parse('CONTENTS','header', true); 
} else {
	$t->set_var('USER_DETAILS_STYLE','width:400px;padding:10px;font-size:x-small');
}
//get any members details
$sql = "SELECT first_name, last_name,email,details,file_path FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$user_key'";
$rs = $CONN->Execute($sql);

if (!$rs->EOF) {
	$full_name=$rs->fields[0].' '.$rs->fields[1];
//	$file_path = $rs->fields[4];
	$email_username=urlencode($name);
	$t->set_var('EMAIL_USER_NAME',$email_username);
	$t->set_var('FULL_NAME',$full_name);
	
	if ($CONFIG['SHOW_EMAILS']==1) {
			
		$t->set_var('EMAIL',$rs->fields[2]);
				
	} else {
			
		$t->set_var('EMAIL',$general_strings['email']);
				
	}
	if($user_key==$current_user_key) {$t->set_var('MOD_DETAILS','<br /><a class="small" href="'.$CONFIG['PATH'].'/users/userinput.php?action=modify">'.$general_strings['modify_details'].'</a>');}
	if (empty($pop_up)) {
		$t->set_var('DETAILS',nl2br($rs->fields[3]));
	} else {
		$t->set_var('DETAILS',substr(strip_tags($rs->fields[3]),0,255).' ...');
	}
	$t->set_var('USER_KEY',$user_key);
}   
$rs->Close();
if (!isset($objUser)) {
	if (!class_exists('InteractUser')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');
	}
	$objUser = new InteractUser();
}
if (!isset($objDates)) {
	if (!class_exists('InteractDate')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	}
	$objDates = new InteractDate();
}
$user_data = $objUser->getUserData($user_key);
$t->set_var('PHOTO',$objUser->getUserphotoTag($user_key, '200', $space_key));
$t->set_var('MEMBER_SINCE_STRING', $general_strings['member_since']);

$t->set_var('MEMBER_SINCE_DATE', $objDates->formatDate($CONN->UnixTimeStamp($user_data['date_added']),'long'));
$t->set_var('LAST_LOGIN_STRING', $general_strings['last_login']);

$t->set_var('LAST_LOGIN_DATE', $objDates->formatDate($CONN->UnixTimeStamp($user_data['last_use']),'long'));
$t->set_var('POST_COUNT_STRING', $general_strings['member_post_count']);
$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE user_key=$user_key");

$t->set_var('POST_COUNT', $rs->RecordCount());
//now get a list of user groups this user is a member of
$rs_usergroups = $CONN->Execute("SELECT group_name FROM {$CONFIG['DB_PREFIX']}user_groups, {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE {$CONFIG['DB_PREFIX']}user_groups.user_group_key={$CONFIG['DB_PREFIX']}user_usergroup_links.user_group_key AND {$CONFIG['DB_PREFIX']}user_usergroup_links.user_key='$user_key'");
		 	
if ($rs_usergroups->EOF) {
	$t->set_var('USER_GROUP_STRING','');
	$t->set_var('USER_GROUPS','');
} else {
		  		
	$t->set_var('USER_GROUP_STRING',$general_strings['user_groups'].'<br />');
	$user_groups = '';
	while (!$rs_usergroups->EOF && $n<3) {
 		$user_groups .= $rs_usergroups->fields[0].'<br />';	
		$rs_usergroups->MoveNext();	
	}
		  		
	$t->set_var('USER_GROUPS',$user_groups);
}
if (empty($pop_up)) {
	get_navigation();
}
$t->parse('CONTENTS', 'userdetails', true);

//memberships
$getSpaces= Array();
$gkeys= Array();
$gspacekeys= Array();

$spaceInfo='';

if (isset($_GET['all_sites'])) {
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}space_user_links.space_key from {$CONFIG['DB_PREFIX']}space_user_links WHERE user_key=$user_key");
	$t->set_var('CONTENTS',"<p><strong>".ucfirst($general_strings['space_text'])." ".$general_strings['membership'].":</strong><br />",true);
	while(!$rs->EOF) {
		$getSpace=$rs->fields[0];

		if (ICanAccess($getSpace)) {
	//echo "can access $getSpace";echo "SELECT Modules.name,Modules.description,parent_key FROM {$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}spaces.space_key=$getSpace AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}spaces.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}spaces.module_key";
			$rs2= $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}modules.description,parent_key FROM {$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}spaces.space_key=$getSpace AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}spaces.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}spaces.module_key"); 		
	//echo $CONN->ErrorMsg();if ($rs2->EOF) {echo 'EMPTY';}
			while(!$rs2->EOF) {
				$spaceInfo.='<a href="'.$CONFIG['FULL_URL'].'/spaces/space.php?space_key='.$getSpace.'">'.$rs2->fields[0].'</a>'.($rs2->fields[1]? ' - '.$rs2->fields[1]:'').'<br>';
				$rs2->MoveNext();//echo 'spinfo::'.$spaceInfo;
			}
		} //else {	echo "cant access $getSpace";}
		$rs->MoveNext();
	}
	if ($spaceInfo) {
		$t->set_var('CONTENTS',$spaceInfo,true);
	} else {$t->set_var('CONTENTS','None',true);}

	// get groups in all spaces that user is a member of
	$rs = $CONN->Execute("SELECT group_key from {$CONFIG['DB_PREFIX']}group_user_links WHERE user_key=$user_key");
	while(!$rs->EOF) {
		$rs2 = $CONN->Execute("SELECT space_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key={$rs->fields[0]}");
		while(!$rs2->EOF) {
			if (ICanAccess($rs2->fields[0])) {
				$gspacekeys[$rs2->fields[0]][]=$rs->fields[0];
			}
			$rs2->MoveNext();
		}
		$rs->MoveNext();
	}
	$t->set_var('CONTENTS',"</p>",true);
} else {
// get groups in just this space that user is a member of
//echo 'at get groups';
	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}group_user_links.group_key from {$CONFIG['DB_PREFIX']}group_user_links,{$CONFIG['DB_PREFIX']}module_space_links WHERE user_key='$user_key' AND {$CONFIG['DB_PREFIX']}group_user_links.group_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND space_key='$space_key'");
	while(!$rs->EOF) {//echo "pushing {$rs->fields[0]} into $space_key";
		$gspacekeys[$space_key][]=$rs->fields[0];
//echo'xx'.($gspacekeys[$space_key][0]).'xx';
		$rs->MoveNext();
	}
}


$allgroupInfo='';$spaceRow=0;
foreach ($gspacekeys as $getSpace => $gkeyvals) {
//echo "(space)$getSpace(space): ".$gksyvals.'-'.implode($gkeyvals);
	$groupInfo='';
	foreach ($gkeyvals as $gkeyval) {
//echo "in group $gkeyval, space $getSpace";	
		$rs= $CONN->Execute("SELECT access_key FROM {$CONFIG['DB_PREFIX']}group_settings WHERE module_key=$gkeyval");
		$rcount=$CONN->Execute("SELECT COUNT(*) FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE group_key=$gkeyval AND user_key={$_SESSION['current_user_key']}");
		if ($userlevel_key=='1' || 
				($rs->fields[0]==2 || $rcount->fields[0])) {

			$rs=$CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.name FROM {$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}modules WHERE space_key=$getSpace AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}spaces.module_key");
			$this_space_name=$rs->fields(0);
	
			$rs= $CONN->Execute("SELECT name,description,parent_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND space_key=$getSpace AND {$CONFIG['DB_PREFIX']}modules.module_key=$gkeyval"); 		

			while(!$rs->EOF) {
				$groupInfo.='<a href="'.$CONFIG['FULL_URL'].'/modules/group/group.php?space_key='.$getSpace.'&module_key='.$gkeyval.'&group_key='.$gkeyval.'&link_key='.$rs->fields[2].'">'.$rs->fields[0].'</a>'.($rs->fields[1]? ' - '.$rs->fields[1]:'').'<br>';
				$rs->MoveNext();
			}
		}
	}

	if ($groupInfo) {
		$allgroupInfo.='<tr style="background-color:#'.(($spaceRow++&1)?'E7E5E0':'D5D1C9').'"><td style="padding-left:5px"><a href="'.$CONFIG['FULL_URL'].'/spaces/space.php?space_key='.$getSpace.'">'.$this_space_name.'</a></td><td style="padding-left:15px">'.$groupInfo.'</td></tr>';
	}
}

if ($allgroupInfo) {
	$t->set_var('CONTENTS','<p><strong>'.$module_strings['group'].' '.$general_strings['membership'].(isset($_GET['all_sites'])?'':' in this '.$general_strings['space_text']).':</strong><br />',true);
	$t->set_var('CONTENTS','<table cellspacing=0 cellpadding=0><tr><th style="padding-left:5px">'.ucfirst($general_strings['space_text']).'</th><th style="text-align:left;padding-left:15px">Group</th></tr>'.$allgroupInfo.'</table></p>',true);
}

$t->set_var('CONTENTS','<p>',true);
if (!isset($_GET['all_sites'])) {$t->set_var('CONTENTS',"<a href=\"{$_SERVER['SCRIPT_NAME']}?space_key=$space_key&user_key=$user_key&all_sites=1\">Show all</a> membership details for $full_name<br>",true);} else {
$t->set_var('CONTENTS',"<a href=\"{$_SERVER['SCRIPT_NAME']}?space_key=$space_key&user_key=$user_key\">Show only this ".$general_strings['space_text']."</a> membership details for $full_name<br>",true);
}

$t->set_var('CONTENTS',$user_strings['restricted_membership_note'],true);
$t->set_var('CONTENTS','</p>',true);
if (empty($pop_up)) {
	$t->parse('CONTENTS','footer', true);
}
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

function ICanAccess($spaceK) {
	global $CONN,$CONFIG,$userlevel_key;
	
	$rs=$CONN->Execute("SELECT access_level_key,show_members FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key=$spaceK");
	$rcount=$CONN->Execute("SELECT COUNT(*) FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key=$spaceK AND user_key=$current_user_key");

	return (
		$userlevel_key=='1' || 
		($rs->fields[1] &&
			($rcount->fields[0] ||
				$rs->fields[0]==3 ||
				($rs->fields[0]==1 && $current_user_key))));
}
?>

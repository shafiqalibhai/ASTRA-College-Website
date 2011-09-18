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
* Space members
*
* Displays a list of members of a space, with space administators at
* the top of the list
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: members.php,v 1.57 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key	 = $_GET['space_key'];
$userlevel_key = $_SESSION['userlevel_key'];
$current_user_key = $_SESSION['current_user_key'];
$online_only = isset($_GET['online_only'])?$_GET['online_only']:0;
//check we have the required variables
check_variables(true,false);
$limit=50;


//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];	 

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'membertable'	 => 'spaces/membertable.ihtml',
	'members'		 => 'spaces/members.ihtml',
	'footer'		  => 'footer.ihtml'
));
$page_details=get_page_details($space_key);
$message = isset($_GET['message'])? $_GET['message'] : '';
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$search_string=create_search_string($_GET['search_terms'],array('first_name','last_name','username'),$_GET['rule']);

if($search_string) {$online_only=0;}

$memlink='<a href="members.php?space_key='.$space_key;
//if showing online users then include messaging box
if ($online_only==1) {
	require_once($CONFIG['LANGUAGE_CPATH'].'/messaging_strings.inc.php');
	$t->set_var('MESSAGE_INPUT_HEADING',$messaging_strings['selected_users']);
	$t->set_var('CHARACTERS_STRING',$messaging_strings['characters']);
	$t->set_var('SEND_STRING',$messaging_strings['send']);
	$t->set_var('MAXIMUM_CHAR_STRING',sprintf($messaging_strings['maximum_characters'],250));

	$t->set_var('ONLINE_MEMBERS',$memlink.'">'.$general_strings['all'].'</a>');
	$t->set_var('MEMBERS_STRING',$space_strings['members_online']);
	$t->set_var('SEARCH_STRING',$general_strings['search']);
	$t->set_var('SAVE_MEMBERS','');
	$memlink.='&online_only=1';
} else {
	$t->set_block('membertable','MessageInputBlock','MessInputBlck');
	$t->set_var('MessInputBlck','');
	$t->set_var('MEMBERS_STRING',$space_strings['members'].($search_string?' ('.$general_strings['search'].':'.$_GET['search_terms'].')':''));
	$t->set_var('SEARCH_STRING',$general_strings['search']);
	$t->set_var('ONLINE_MEMBERS',!empty($_SESSION['current_user_key']) ? $memlink.(!empty($search_string)?'">'.$general_strings['all']:'&online_only=1">'.$space_strings['online_only']).'</a>':'');
	if($search_string){$memlink.='&search_terms='.$_GET['search_terms'];}
}


$breadcrumbs = "<a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\" class=\"spaceHeadinglink\">".$page_details['space_name'].'</a> &raquo;';
$t->set_var('BREADCRUMBS',$breadcrumbs);
if (!isset($objSpace)) {
	if (!class_exists('InteractSpace')) {
		require_once($CONFIG['BASE_PATH'].'/spaces/lib.inc.php');
	}
	$objSpace = new InteractSpace();
}
$new_members = $objSpace->countNewMembers($space_key, $_SESSION['current_user_key']);
//see if we have any new members
if ($new_members>0) {
	$new_members = '('.sprintf($general_strings['new_postings_count'], $new_members).')';
	$new_members = "<a href=\"newmembers.php?space_key=$space_key\">$new_members</a>";	
} else {
	$new_members='';
}
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
if (!isset($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}
$t->set_var('NEW_MEMBERS_LINK',$new_members);


$t->parse('CONTENTS', 'header', true); 

//if admin show "Show Invisible Members" link
if (($userlevel_key=='1' || $accesslevel_key=='1' || $accesslevel_key=='3')) {

	if(empty($online_only)) {$t->set_var('SAVE_MEMBERS',"<a href=\"membersave.php?space_key=$space_key\" target=\"_new\">".$space_strings['save_members']."</a>");}
	$t->set_var('ACTION_MEMBERS_STRING',$space_strings['action_members']);	
	$t->set_var('SUBMIT_STRING',$general_strings['submit']);
	$t->set_var('CHECK_ACTION_STRING',$general_strings['delete_warning']);

if($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
	$add_members = "<a href=\"memberedit.php?space_key=$space_key\">".$space_strings['add_members']."</a>";
	$t->set_var('ADD_MEMBERS',$add_members);


	$t->set_var('ACTION_MEMBERS_MENU',$objHtml->arrayToMenu(array('delete' => ucfirst($general_strings['remove']),''=>'-----','promote'=>$space_strings['promote'],'demote'=>$space_strings['demote'], 'make_inv_admin'=>$space_strings['make_inv_admin'],'make_inv_member'=>$space_strings['make_inv_member']),'access_level','',false,'1',false));
	} else {
		$t->set_var('ACTION_MEMBERS_MENU',$objHtml->arrayToMenu(array(''=>'-----','promote'=>$space_strings['promote'],'demote'=>$space_strings['demote'], 'make_inv_admin'=>$space_strings['make_inv_admin']),'access_level','',false,'1',false));
	
	}
	
} else {
	if (empty($_SESSION['current_user_key']) || empty($online_only)) {
		$t->set_block('members','CheckBoxBlock','ChckBxBlock');
		$t->set_var('ChckBxBlock','');
	}
	$t->set_block('membertable','MemberAdminBlock','MemAdminBlck');
	$t->set_var('MemAdminBlck','');
}

$adminstuff= ($userlevel_key=='1' || $accesslevel_key=='1' || $accesslevel_key=='3')? 1:0;


	
	$sql_member = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$current_user_key'";

	$rsmember=$CONN->Execute($sql_member);
		
	if ($rsmember->EOF && (isset($space_key) && $space_key!='') && (isset($current_user_key) && $current_user_key!='') && $online_only!=1){
		$add_me = "<a class=\"addmeButton\" style=\"float:none;display:inline\" href=\"{$CONFIG['PATH']}/spaces/makemember.php?space_key=$space_key&referer=members.php\" title=\"".sprintf($general_strings['add_me_explain'],$general_strings['space_text']).'">'.$general_strings['add_me'].'</a>';
		
		$t->set_var('ADD_ME',$add_me);
			
	} else if (!$rsmember->EOF && (isset($space_key) && $space_key!='') && (isset($current_user_key) && $current_user_key!='')) {
			
		$warning=sprintf($general_strings['remove_membership_warning'],
			$general_strings['space_text']);

		$remove_me = "<a class=\"addmeButton\" 
			style=\"float:none;display:inline\" title=\"$warning\"
						href=\"{$CONFIG['PATH']}/spaces/makemember.php?space_key=$space_key&action=remove&referer=members.php\" 
		onclick=\"return confirmDelete('$warning"
		.(($adminstuff&&$userlevel_key!=1)?
			' '.$general_strings['remove_admin']:'')
		."')\">".$general_strings['leave'].'</a>';
		
		$t->set_var('ADD_ME',$remove_me);
			
	}
	
$t->set_var('ADMIN_TOOL_CLASS',($adminstuff)? get_admin_tool_class() :'');

$invis=false;

if($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
	$space_limit = 'AND '.$CONFIG['DB_PREFIX'].'space_user_links.space_key=\''.$space_key.'\'';
} else {
		$space_limit = (!empty($search_string) || $online_only==1)?'AND account_status=1':' account_status=1';	
}


$sel="SELECT DISTINCT {$CONFIG['DB_PREFIX']}users.user_key, first_name, last_name, email, access_level_key,username, details, {$CONFIG['DB_PREFIX']}users.date_added, {$CONFIG['DB_PREFIX']}users.last_use, {$CONFIG['DB_PREFIX']}users.account_status";
	
	if ($online_only==1) {

		$sql = $sel." FROM {$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}online_users WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND  {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}online_users.user_key $space_limit AND (access_level_key=1".($adminstuff?'||access_level_key=3':'').") AND {$CONFIG['DB_PREFIX']}online_users.status_key=1 ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	} else {
		$sql = $sel." FROM {$CONFIG['DB_PREFIX']}space_user_links LEFT JOIN {$CONFIG['DB_PREFIX']}users ON {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key WHERE {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' $search_string AND (access_level_key=1".($adminstuff?'||access_level_key=3':'').") ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	}
	$rs = $CONN->Execute($sql);

	$adminn=process_members();

	if (!$adminn && empty($search_string)) {$t->set_var('ADMINS', '<br />'.sprintf($space_strings['no_admins'], $general_strings['space_text'], $general_strings['space_text']).'<br />');}
	$t->set_var('SPACE_ADMIN_TEXT',$general_strings['space_admin_text']);

$user_limit=$user_list?"AND ({$CONFIG['DB_PREFIX']}users.user_key NOT IN (".substr($user_list,0,-1).'))':'';

$current_page=isset($_GET['page'])? $_GET['page']:1;
if ($online_only==1) {
	if($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
		$sql = $sel." FROM {$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}online_users WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND  {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}online_users.user_key $space_limit AND (access_level_key=2".($adminstuff?'||access_level_key=4':'').") AND {$CONFIG['DB_PREFIX']}online_users.status_key=1 ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	} else {
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}users.user_key, first_name, last_name, email, null,username, details, {$CONFIG['DB_PREFIX']}users.date_added, {$CONFIG['DB_PREFIX']}users.last_use, {$CONFIG['DB_PREFIX']}users.account_status FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}online_users WHERE  {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}online_users.user_key AND {$CONFIG['DB_PREFIX']}online_users.user_key!='0' $space_limit $user_limit AND {$CONFIG['DB_PREFIX']}online_users.status_key=1 ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
		
		}
} else {
	if($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
	$sql = $sel." FROM {$CONFIG['DB_PREFIX']}space_user_links LEFT JOIN {$CONFIG['DB_PREFIX']}users ON {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key WHERE {$CONFIG['DB_PREFIX']}space_user_links.space_key='$space_key' $search_string AND (access_level_key=2".($adminstuff?'||access_level_key=4':'').") $space_limit ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
	
	} else {
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}users.user_key, first_name, last_name, email, null,username, details, {$CONFIG['DB_PREFIX']}users.date_added, {$CONFIG['DB_PREFIX']}users.last_use, {$CONFIG['DB_PREFIX']}users.account_status FROM {$CONFIG['DB_PREFIX']}users WHERE ".substr($search_string,4)." $space_limit $user_limit ORDER BY {$CONFIG['DB_PREFIX']}users.last_name";
		
	}
}

$rs = $CONN->PageExecute($sql,$limit,$current_page);

if(!$rs) echo $sql.'->'.$CONN->ErrorMsg();
$n=$rs->_maxRecordCount;
$last_page=ceil($n/$limit);

process_members();
$t->set_var('NUMBER_OF_MEMBERS',sprintf($space_strings['number_of_members'], $n+$adminn));


if ($last_page>1) {
	$t->set_var('PAGE_NAV','<div style="display:block;background:#D6D3D0"><table style="padding:2px;border:0"><tr><th style="width:8em; white-space:nowrap; background:none;text-align:left;">Page {CURRENT_PAGE} of {LAST_PAGE}</th><td style="width:5em; text-align:right;white-space:nowrap">{PREV_LINK}</td><td class="small" style="text-align:center;white-space:normal;">{PAGE_LINKS}</td><td style="text-align:left;white-space:nowrap">{NEXT_LINK}</td></tr></table></div>');

	$t->set_var('CURRENT_PAGE',$current_page);

	$t->set_var('LAST_PAGE',$last_page);

	$page_links="";
	for ( $i = 1; $i <= $last_page; $i++ ) {
		if ($i==$current_page) {
			$page_links.=$i.' ';
		} else {
			$page_links.=$memlink.'&page='.($i).'">'.$i.'</a> ';
		}
	}
	$t->set_var('PAGE_LINKS',$page_links);
	
	if ($current_page>1) {$t->set_var('PREV_LINK',$memlink.'&page='.($current_page-1).'">'.$general_strings['previous'].'</a>&nbsp;');}

	if ($current_page<$last_page) {$t->set_var('NEXT_LINK','&nbsp;'.$memlink.'&page='.($current_page+1).'">'.$general_strings['next'].'</a>');}

}


   

get_navigation();

if($invis) {$t->set_var('INVISIBLE_NOTE',sprintf($space_strings['invisible_note'],'<span class="smallred">X</span>').'<br />');}

$t->parse('CONTENTS', 'membertable', true);


$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;

function process_members() {
	global $rs,$t,$adminstuff,$general_strings,$space_strings,$CONFIG,$CONN,$space_key, $objUser, $objDates, $invis, $user_list;
	$user_list='';
	$n=0;
	while ($rs && !$rs->EOF) {
		$user_key = $rs->fields[0];
		$user_list.=$user_key.',';
		if (!isset($rs->fields[1])) {
			$no_account = sprintf($space_strings['no_account'], $user_key);
			$name = '';
		
		} else {
			 $name = $rs->fields[1].' '.$rs->fields[2];
			 $no_account = '';
		}
		$email = $rs->fields[3];
		$member_accesslevel_key = $rs->fields[4];
		
		if($adminstuff || ($member_accesslevel_key<3)) {
			$n++;
			$email_username=urlencode($name);
			
			if (strlen($rs->fields[6])>120) {
				$details = substr(strip_tags($rs->fields[6]),0,120).' <a href="../users/userdetails.php?space_key='.$space_key.'&user_key='.$user_key.'">&hellip;</a>';;
			} else {
				$details = $rs->fields[6];
			}
			
				$t->set_var('USER_KEY',$user_key);
				$t->set_var('SPACE_KEY',$space_key);
				$t->set_var('NAME',$name);
	
				$t->set_var('NO_ACCOUNT',$no_account);
				$t->set_var('DETAILS',$details);
				$t->set_var('EMAIL_USER_NAME',$email_username);
				$t->set_var('USER_PHOTO',$objUser->getUserphotoTag($user_key, '30', $space_key,'top'));
				$t->set_var('MEMBER_SINCE_STRING', $general_strings['member_since']);
	
				$t->set_var('MEMBER_SINCE_DATE', $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[7]),'long'));
				$t->set_var('LAST_LOGIN_STRING', $general_strings['last_login']);
	
				if ($rs->fields[8]>0) {
					$t->set_var('LAST_LOGIN_DATE', $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[8]),'long'));
				} else {
					$t->set_var('LAST_LOGIN_DATE', '');
				}
				if ($CONFIG['SHOW_EMAILS']==1) {
				
					$t->set_var('EMAIL',$email);
					
				} else {
				
					$t->set_var('EMAIL',$general_strings['email']);
					
				}
				$t->set_var('MEMBER_USERNAME',($adminstuff)? 
					((($member_accesslevel_key > 2)?
						' <span class="smallred" style="font-size:small;">X</span>'
						:'')
					.' ('.$rs->fields[5].')')
					:'');
				
				//flag to denote 1 or more invisible members on page
				if($member_accesslevel_key > 2){$invis=true;}
	
				//now get a list of user groups this user is a member of
				$rs_usergroups = $CONN->Execute("SELECT group_name FROM {$CONFIG['DB_PREFIX']}user_groups, {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE {$CONFIG['DB_PREFIX']}user_groups.user_group_key={$CONFIG['DB_PREFIX']}user_usergroup_links.user_group_key AND {$CONFIG['DB_PREFIX']}user_usergroup_links.user_key='$user_key'");
				
				if ($rs_usergroups->EOF) {
					$t->set_var('USER_GROUP_STRING','');
					$t->set_var('USER_GROUPS','');
				} else {
					
					$t->set_var('USER_GROUP_STRING',$general_strings['user_groups'].'<br />');
					$user_groups = '';
					$nn=0;
					while (!$rs_usergroups->EOF && $nn<3) {
						
						if ($nn==2 && $rs_usergroups->RecordCount()>3) {
							$user_groups .= $rs_usergroups->fields[0].' <a href="../users/userdetails.php?space_key='.$space_key.'&user_key='.$user_key.'">&hellip;</a>';
						} else {
							$user_groups .= $rs_usergroups->fields[0].'<br />';	
						}
						$nn++;
						$rs_usergroups->MoveNext();	
					}
					
					$t->set_var('USER_GROUPS',$user_groups);
				}

				if ($member_accesslevel_key == 1 || $member_accesslevel_key == 3) {
					$t->parse('ADMINS', 'members', true);
				 } else {
					  $t->parse('MEMBERS','members', true);	
				 }
		}
		$rs->MoveNext();
   }
   return $n;
}


function  create_search_string($search_terms,$fields,$rule) { 
	if(empty($search_terms)) return '';

	$cond=' AND ';
	if (empty($rule)) {$joiner = 'AND'; 
	} elseif ($rule == 'any') {$joiner = 'OR';  
	} elseif ($rule == 'exact') {return $cond.create_search_cond($search_terms,$fields);
	} else {$joiner='AND';}

	// Split up keywords by the delimiter (" ") 
	$arg = split(' ', $search_terms); 
	for($i=count($arg); $i--;) { 
		$cond.=create_search_cond($arg[$i],$fields);
		if ($i) {$cond.=" $joiner ";}}
	return $cond;
}

function create_search_cond($term,&$fields) {
	$cond='(';
	for($j=count($fields); $j--;) {
		$cond.='('.$fields[$j]." LIKE '%$term%')";
		if ($j) {$cond.=' OR ';}}	
	return $cond.')';
}
?>
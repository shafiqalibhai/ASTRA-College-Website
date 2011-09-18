<?php
// +----------------------------------------------------------------------+
// |chat.php     1.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education	              |
// +----------------------------------------------------------------------+
// | This file is part of Interact.                                       |
// |                                                                      | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)                             |
// |                                                                      | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     |
// | General Public License for more details.                             |
// |                                                                      | 
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, you can view it at                  |
// | http://www.opensource.org/licenses/gpl-license.php                   |
// |                                                                      |
// |                                                                      |
// | Displays a chat room                                                 |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz>                     |
// | Last Modified 29/01/03                                               |
// +----------------------------------------------------------------------+

require_once('../../local/config.inc.php');


//set variables
$space_key 	= get_space_key();
$module_key	= $_GET["module_key"];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET["group_key"];

check_variables(true,true,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels["accesslevel_key"];
$group_access = $access_levels["groups"];

//update statistics 
statistics("read");



require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	"header"          => "header.ihtml",
	"navigation"      => "navigation.ihtml",
	"footer"          => "footer.ihtml"
));

if(empty($_SESSION['current_user_key'])) {
	header("Location:".$CONFIG['PATH']."/login.php?request_uri=".urlencode($_SERVER['REQUEST_URI']));
	exit;
} else {
	$t->set_file("chat","chat/chat.ihtml");
}

$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('SCRIPT_INCLUDES','<SCRIPT TYPE="text/javascript">
<!--
var mywin;
function popupChat(mylink, windowname) {
var  href;
if (typeof(mylink) == "string") href=mylink; else href=mylink.href;

if(!mywin || mywin.closed) mywin = window.open("", windowname, "width=598,height=520,scrollbars=no,resizable=yes,directories=no,location=no");

if(mywin.location.href!=href) mywin.location.href=href; else mywin.focus();

return false;
}
//-->
</SCRIPT>',true);

$t->set_var('LOGIN_LINK',$page_details['login_link']);
$t->set_var('OTHER_HEADER_LINKS',$page_details['other_header_links']);
$t->set_var("HOME_LINK","$page_details[home_link]");
$t->set_var("PAGE_TITLE","$page_details[full_space_name] - $page_details[module_name]");
//$t->set_var("ROOM_NAME", urlencode(str_replace("'","",$page_details['space_name'].' - '.$page_details['module_name'])));
$t->set_var("WINDOW_NAME", 'chat'.$module_key);
$t->set_var("SPACE_TITLE","$page_details[full_space_name]");
$t->set_var("PATH",$CONFIG['PATH']);
$t->set_var("TOP_BREADCRUMBS","$page_details[top_breadcrumbs]");
$t->set_var("BREADCRUMBS","$page_details[breadcrumbs]");
$t->set_var("FULL_NAME","$current_user");
$t->set_var("CHAT_URL","{$CONFIG['FULL_URL']}/modules/chat/chat_win.php?module_key=$module_key&space_key=$space_key");
$t->parse("CONTENTS", "header", true); 
$t->set_var("MODULE_KEY","$module_key");
get_navigation();


$t->parse("CONTENTS", "chat", true);
$t->parse("CONTENTS", "footer", true);
print_headers();
$t->p("CONTENTS");
       
$CONN->Close();
exit;

?>
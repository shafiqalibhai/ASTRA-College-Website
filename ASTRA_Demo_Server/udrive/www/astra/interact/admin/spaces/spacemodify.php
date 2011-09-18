<?php
// +----------------------------------------------------------------------+
// | This file is part of Interact.									   |
// |																	  | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)							 |
// |																	  | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 |
// | General Public License for more details.							 |
// |																	  | 
// | You should have received a copy of the GNU General Public License	|
// | along with this program; if not, you can view it at				  |
// | http://www.opensource.org/licenses/gpl-license.php				   |
// +----------------------------------------------------------------------+


require_once('../../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');

$sort = isset($_GET['sort'])?$_GET['sort']:'';
$personal = isset($_GET['personal'])?$_GET['personal']:'';
//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'body'	   => 'admin/spacemodify.ihtml',
	'spaces'	 => 'admin/spacelist.ihtml',
	'footer'	 => 'footer.ihtml'));
	
set_common_admin_vars("Modify/Delete ".$general_strings['space_text'], $message);

$t->parse('CONTENTS', 'header', true); 

if (!empty($sort)) {
	if (empty($personal)) {
		$personal_limit = "WHERE type_key!=1 AND owned_by_key=0";		
	}
	$rs = $CONN->Execute("SELECT name,space_key FROM {$CONFIG['DB_PREFIX']}spaces $personal_limit ORDER BY name");
	echo $CONN->ErrorMsg();
	$space_menu = $rs->GetMenu2('space_key','',false,false,15); 	
} else {
	require_once('../../spaceadmin/lib.inc.php');
	$objSpaceAdmin = new InteractSpaceAdmin();
	$space_menu = $objSpaceAdmin->getSpaceParentMenu('0','','','','',false,$personal);
	
}
$t->set_var('SORT',$sort);
$t->set_var('PERSONAL',$personal);
$t->set_var('SPACE_MENU',$space_menu);
admin_navigation();
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

exit;

?>
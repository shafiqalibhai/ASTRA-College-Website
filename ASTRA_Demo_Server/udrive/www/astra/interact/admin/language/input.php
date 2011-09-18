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
*
* Show/modify language translations
*
* @package ServerAdmin
* @author Bruce Webster <bruce.webster@cce.ac.nz>
* @copyright Christchurch College of Education 2006
* @version $Id: input.php,v 1.6 2007/01/17 23:24:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

require_once($CONFIG['INCLUDES_PATH']."/lib/languages.inc.php");

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'admin/adminnavigation.ihtml',
	'form'	   => 'admin/language_input.ihtml',
	'footer'	 => 'footer.ihtml'));


$lang=$_GET['lang'];
$file=$_GET['file'];

$admin_strings=array();
get_full_lang($lang,'admin_strings',$admin_strings);
get_full_lang($lang,'general_strings',$general_strings);

$message=' ';


$iArray=array();$sourceArray=array();
$langArray=get_all_inheritance($lang,$file,$iArray,$sourceArray);

if($lang!='default'){$t->set_var('LANG_TO_USE_DEFAULT',$admin_strings['lang_to_use_default'].'<br />');}
$odd=1;
$t->set_var('MODULE_STYLES','<style>
.smallgrey{font-size:x-small;color:#777;padding-bottom:6px;}
td{padding-left:6px;padding-top:1px;}
</style>',true);
foreach(($lang=='default'?$langArray:$iArray) as $key=>$sval) {
	$t->set_var('LANG_LIST','<tr style="background-color:'.($odd&1?' #E0E0E0':'#F5F5F5').'"><td>'.$key.'</td><td>',true);
	$iv=(isset($langArray[$key])?$langArray[$key]:'');
	$ivlen=max(strlen($iv),strlen($sval));
	$t->set_var('LANG_LIST','<textarea name="'.$key.'" rows="'.ceil(($ivlen+1)/70).'" cols="48"'.($iv==''?' tabindex="1"':'').'>'.$iv.'</textarea>',true);
	$t->set_var('LANG_LIST','</td></tr>',true);
	if($lang!='default'){
		$t->set_var('LANG_LIST','<tr style="background-color:'.($odd&1?' #E0E0E0':'#F5F5F5').'"><td align="right" valign="top"><span class="smallred" style="float:right"><strong>&lsaquo;'.$sourceArray[$key].'&rsaquo;</strong></span>'.($key=='character_set'?'<br /><span class="smallgrey" style="font-size:xx-small">'.$admin_strings['charset_note'].'</span>':'').'</td><td valign="top"><span class="smallgrey">'.htmlspecialchars($sval).'</span>'.'</td></tr>',true);
	}
	$odd++;
}


$t->set_var('HEADING','<a href="index.php">'.$admin_strings['languages'].'</a> &raquo; <a href="index.php?lang='.$lang.'">'.lang_desc($lang).' &lsaquo;'.$lang.'&rsaquo;</a> &raquo; '.substr($file,0,-8).($lang=='default'?'':' '.amount_done($lang,$file)));

set_common_admin_vars($admin_strings['languages'].' &raquo; '.lang_desc($lang).' &lsaquo;'.$lang.'&rsaquo; &raquo '.substr($file,0,-8), $message);

$t->set_var('CHARACTER_SET',$general_strings['character_set']);
$t->set_var('LANG',$lang);
$t->set_var('FILE',$file);
$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

exit;

?>
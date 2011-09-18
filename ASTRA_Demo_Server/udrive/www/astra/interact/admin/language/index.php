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
* @version $Id: index.php,v 1.18 2007/01/17 23:24:07 glendavies Exp $
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
	'form'	   => 'admin/language.ihtml',
	'footer'	 => 'footer.ihtml'));

$compile=isset($_GET['compile']);
$lang=(isset($_GET['lang'])?$_GET['lang']:'');
$file=(isset($_GET['file'])?$_GET['file']:'');

$message='';
$admin_strings=array();
get_full_lang($lang,'admin_strings',$admin_strings);

$helplink='<span class="vlink" style="font-size:small" onClick="window.open (\''.$CONFIG['PATH'].'/language/readme.html\', \'Language Help\', \'toolbar=no, scrollbars=yes, width=800, height=520, menubar=no, location=no, resizable=yes\')">help</span>';

if(!empty($_POST['new_lang'])) {
	if(preg_match("/^\w{2,3}(-|\(|$)?/",$_POST['new_lang'])) {
		if(mkdir($CONFIG['LANGUAGE_PATH'].'/'.$_POST['new_lang']) && 
			mkdir($CONFIG['LANGUAGE_PATH'].'/'.$_POST['new_lang'].'/strings')) {
				$message='New language &lsaquo;'.$_POST['new_lang'].'&rsaquo; created. <br />';$compile=true;$lang=$_POST['new_lang'];
		} else {
			$message=sprintf($admin_strings['lang_cannot_make_dir'],$CONFIG['LANGUAGE_PATH'].'/'.$_POST['new_lang'].'/strings');
		}
	} else {$message='Code name "'.$_POST['new_lang'].'" is invalid for a new language - please click '.$helplink.'.';}
}



$lang_list=get_listing(true);
$file_list=get_listing(false);

$continue=true;
if(isset($_POST['__SUBMIT_CHANGES'])) {

	//write strings
	$content='';
	foreach($_POST as $key=>$sval) {
		if(strlen($sval) && substr($key,0,2)!='__') {$content.=$key.'='.clean0_string($sval).chr(10);}}
	$langpath=$CONFIG['LANGUAGE_PATH'].'/'.$lang.'/strings/'.$file.'.txt';
	if (($content==''&&!is_file($langpath)) || ($handle = fopen($langpath,"wb"))) {
		if ($handle) {fwrite($handle,$content);fclose($handle);}

	//compile
		$compile=true;
	} else {
		$continue=false;
		$message=sprintf($admin_strings['cannot_write_strings_file'],$langpath).'<br />'.$admin_strings['lang_write_access'].'<br />'.$admin_strings['lang_reload'];
		$t->set_var('LANG_LIST','<tr><td><textarea name="strings" rows="32" cols="60" readonly>'.$content.'</textarea></td></tr>',true);
	}
}

if($compile) {
	$retv=true;
	$clang=($lang=='default'?dependant_list(''):dependant_list($lang));
	while($retv===true && $cl=current($clang)){

		$langpath=$CONFIG['LANGUAGE_PATH'].'/'.$cl.'/strings/compiled';
		if(is_dir($langpath) || mkdir($langpath)) {
	
			$blist=($file==''?$file_list:array($file));
			while($retv===true && $b=current($blist)){
				$content='<?php   //'.$admin_strings['lang_do_not_modify_comp'].chr(10).chr(10).'$'.$b.'=array(';
				$iArray=array();
				get_full_lang($cl,$b,$iArray);
				foreach($iArray as $key=>$sval) {$content.="'".$key."'=>'".clean1_string($sval).'\',';}
				$content=substr($content,0,-1).');?>';
	
				if($retv===true && is_dir($langpath)){
					if($handle = fopen(($langpath.'/'.$b.'.inc.php'),"wb")) {
						fwrite($handle,$content);
						fclose($handle);
					} else {$retv=false;}
				}
				next($blist);
			}
		} else {
			$message.=sprintf($admin_strings['lang_cannot_make_dir'],$langpath).' <br /> &nbsp;';
		}
		next($clang);
	}
	require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');
	
	//get compile message
	if($retv===true) {
		$message.=sprintf($admin_strings['lang_compiled'],'<span style="color:black">'.
			(($lang==''||$lang=='default')?
				$general_strings['all']:
				(lang_desc($lang).(' &lsaquo;'.implode('&rsaquo;, &lsaquo;',$clang).'&rsaquo;'))
			).($file!=''?' &raquo '.substr($file,0,-8):'').'</span>');
	} else {
		$continue=false;
		$message.=sprintf($admin_strings['cannot_write_compiled_file'],$langpath).$admin_strings['lang_write_access'].'<br />'.$admin_strings['lang_reload'];
		$t->set_var('LANG_LIST','<tr><td><textarea name="strings" rows="32" cols="60" readonly>'.$content.'</textarea></td></tr>',true);
	}
	if ($file==''){$lang='';}
}


if($lang=='') {
	if($continue) {
		while($a=current($lang_list)){
			$t->set_var('LANG_LIST','<tr><td><a href="index.php?lang='.$a.'" title="'.$general_strings['edit'].'">'.(lang_desc($a)?lang_desc($a):$a).'</a></td><td>&lsaquo;'.$a.'&rsaquo;</td><td><a href="index.php?lang='.$a.'&compile=1" class="small">'.$admin_strings['lang_'.(is_dir($CONFIG['LANGUAGE_PATH']."/$a/strings/compiled")?'re':'').'compile'].'</a></td></tr>',true);
			next($lang_list); 
		}
	
		$t->set_var('LANG_LIST','<tr><td></td><td colspan="2"><a class="small" href="index.php?compile=1">'.$admin_strings['lang_recompile_all'].'</a></td></tr>',true);
		$t->set_var('LANG_LIST','<tr><td></td><td colspan="2"><br /><form action="index.php" method="post"><input name="new_lang" type="text" size="6" maxlength="32"><input class="small" name="sub" type="submit" value="Add new code"></form></td></tr>',true);
		$t->set_var('LANG_LIST','<tr style="background-color:#DDD"><td><br /><a href="index.php?lang=default" title="'.$general_strings['edit'].'">'.lang_desc('default').'</a></td><td><br />&lsaquo;default&rsaquo;</td><td></td></tr>',true);
	}
	

	$t->set_var('HEADING',$admin_strings['languages'].' '.$helplink);
	set_common_admin_vars($admin_strings['languages'], $message);
} else {
	if($continue){

		while($a=current($file_list)){
			$bbold=($a=='general_strings');
			$t->set_var('LANG_LIST','<tr><td><a href="input.php?lang='.$lang.'&file='.$a.'" title="'.$general_strings['edit'].'">'.($bbold?'<strong>':'').substr($a,0,-8).($bbold?'</strong>':'').'</a></td>'.($lang=='default'?'':'<td>'.amount_done($lang,$a).'</td>').'<td><a href="index.php?lang='.$lang.'&file='.$a.'&compile=1" class="small">'.$admin_strings['lang_'.($lang=='default'||is_file($CONFIG['LANGUAGE_PATH']."/$lang/strings/compiled/$a.inc.php")?'re':'').'compile'].'</a></td>			
			</tr>',true);
			next($file_list); 
		}
	}
	
	$t->set_var('HEADING','<a href="index.php">'.$admin_strings['languages'].'</a> &raquo; '.lang_desc($lang).' &lsaquo;'.$lang.'&rsaquo;');
	set_common_admin_vars($admin_strings['languages'].' &raquo; '.lang_desc($lang).' &lsaquo;'.$lang.'&rsaquo;', $message);
}

$t->parse('CONTENTS', 'header', true); 
admin_navigation();
$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);
$t->p('CONTENTS');

exit;


function dependant_list($lang) {
global $lang_list;
$dlist=array();
foreach($lang_list as $key=>$val) {
	if ($lang=='') {
		array_push($dlist,$val);
	} else {
		$noff=strpos($val,'(');
		$delim=array('-',')','');

		if($noff!==false) {
			if(substr($val,$noff+1,strlen($lang))==$lang && in_array(substr($val,$noff+strlen($lang)+1,1),$delim)) {
				array_push($dlist,$val);
			}
		}
		if(substr($val,0,strlen($lang))==$lang && in_array(substr($val,strlen($lang),1),$delim)) {
			array_push($dlist,$val);
		}
	}
}
reset($lang_list);
return $dlist;
}


function clean0_string($string) {return preg_replace("/(\r\n?|\r?\n)/","<br />",trim($string));}
function clean1_string($string) {return trim(preg_replace(array('/\\\\n/','/\\\\/',"/'/"),array('
','\\\\\\',"\'"),$string));}

?>
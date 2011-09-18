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
* File module
*
* Displays the file module start page 
*
* @package File
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: loadfile.php,v 1.10 2007/01/07 22:25:19 glendavies Exp $
* 
*/


//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$name  = isset($_GET['file_name'])?  $_GET['file_name'] : '';
//check we have the required variables
check_variables(true,false,true);

//update statistics 
statistics('read');

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$sql = "select file_path, filename, embedded from {$CONFIG['DB_PREFIX']}files where module_key='$module_key'";

$rs = $CONN->Execute($sql);

while (!$rs->EOF) {

	$file_path = $rs->fields[0];
	
	if (!isset($name) || $name=='') {
	
		$name	  = $rs->fields[1];
		
	} 
	
	$embedded  = $rs->fields[2];	
	$rs->MoveNext();

}

$rs->Close();

if ($embedded!=1) {

	if (preg_match("/\./",$name)) {
   
		$ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $name);
				   
	}

	if ($ext=='htm' || $ext=='html' || $ext=='php') {

		header("Location: {$CONFIG['FULL_URL']}".$CONFIG['MODULE_FILE_VIEW_PATH']."/file/$file_path/$name");
		exit;
	
	}


}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'file'	   => 'files/loadfile.ihtml',
	'footer'	 => 'footer.ihtml'
));

// get details of this page, space name, module name, etc.
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if ($embedded!=1) {
	$refresh_tag =  "<meta http-equiv=\"refresh\" content=\"4;URL={$CONFIG['FULL_URL']}{$CONFIG['MODULE_FILE_VIEW_PATH']}/file/$file_path/$name\">";
	$t->set_var('META_TAGS',$refresh_tag);
	$t->set_var('WAIT_STRING',$file_strings['wait']);
	$t->set_var('NO_LOAD_STRING',$file_strings['no_load']);
	$t->set_var('CLICK_HERE_STRING',$general_strings['click_here']);
	
} else { 

	$t->set_block('file', 'RefreshBlock', 'RFBlock');
	$file = $CONFIG['BASE_PATH'].'/local/modules/file/'.$file_path.'/'.$name;

	$htmlfile = file_get_contents($file);
	//$htmlfile = preg_replace ('/\r/', '', $htmlfile);
	//$htmlfile = preg_replace ('/\n/', '', $htmlfile);
	$htmlfile = preg_replace("/href=\"([^:\"(\.css)]*)\"/i", "href=\"embedfile.iphp?space_key=$space_key&module_key=$module_key&file_name=$1\"", $htmlfile);
	$t->set_var('RFBlock',$htmlfile);	

}

$t->parse('CONTENTS', 'header', true); 
//create the left hand navigation 

get_navigation();
$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
$t->set_var('FILE_PATH',$CONFIG['MODULE_FILE_VIEW_PATH'].'/file/'.$file_path.'/'.$name);
$t->set_var('SPACE_FILE_PATH','');
$t->set_var('NAME',$name);
$t->parse('CONTENTS', 'file', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();

//output page
$t->p('CONTENTS');

exit;

?>
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

* Sharing module

*

* Displays the sharing module start page 

*

* @package Sharing

* @author Glen Davies <glen.davies@cce.ac.nz>

* @copyright Christchurch College of Education 2001 

* @version $Id: sharing.php,v 1.27 2007/07/30 01:57:05 glendavies Exp $

* 

*/



/**

* Include main system config file 

*/

require_once('../../local/config.inc.php');





//get language strings



require_once($CONFIG['LANGUAGE_CPATH'].'/sharing_strings.inc.php');



//set variables

$space_key 	= get_space_key();

$module_key	= $_GET['module_key'];

$link_key 	= get_link_key($module_key,$space_key);

$group_key	= $_GET['group_key'];



//check we have the required variables

check_variables(true,true);





//check to see if user is logged in. If not refer to Login page.

$access_levels = authenticate();

$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];

$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

$is_admin = check_module_edit_rights($module_key);

$current_user_key = $_SESSION['current_user_key'];



//update statistics 

if (!$_GET['message']) {



	statistics('read');

	

}



require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');

$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(

	'header'		  => 'header.ihtml',

	'navigation'	  => 'navigation.ihtml',

	'share'		   => 'sharing/sharing.ihtml',

	'sharingitems'	=> 'sharing/sharingitems.ihtml',

	'sharingitemsaudio'	=> 'sharing/sharingitems_audio.ihtml',

	'footer'		  => 'footer.ihtml'

));

$page_details = get_page_details($space_key,$link_key);

set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);





$t->parse('CONTENTS', 'header', true); 



$t->set_var('TITLE','');



get_navigation();



$admin_links="<p align=\"center\"><a href=\"{$CONFIG['PATH']}/modules/sharing/fileinput.php?space_key=$space_key&module_key=$module_key\" class=\"small\">".$sharing_strings['add_file']."</a> - <a href=\"{$CONFIG['PATH']}/modules/sharing/linkinput.php?space_key=$space_key&module_key=$module_key\" class=\"small\">".$sharing_strings['add_link']."</a>";



if(isset($CONFIG['FLASH_COM'])) {

	$admin_links.=" - <a href=\"{$CONFIG['PATH']}/modules/sharing/audioinput.php?space_key=$space_key&module_key=$module_key\" class=\"small\">".'Record Audio'."</a>";

}



$admin_links.='</p>';



$t->set_var('ADMIN_LINKS',$admin_links);

$t->set_var('ADDED_BY_STRING',$sharing_strings['added_by']);

$t->set_var('ADD_COMMENT_STRING',$sharing_strings['add_comment']);

$t->set_var('SPACE_KEY',$space_key);

$t->set_var('MODULE_KEY',$module_key);





$sql = "SELECT shared_item_key,name,description, first_name, last_name, url,{$CONFIG['DB_PREFIX']}sharing_settings.file_path,{$CONFIG['DB_PREFIX']}shared_items.filename,{$CONFIG['DB_PREFIX']}shared_items.date_added,{$CONFIG['DB_PREFIX']}users.user_key,  {$CONFIG['DB_PREFIX']}shared_items.file_path FROM {$CONFIG['DB_PREFIX']}sharing_settings,{$CONFIG['DB_PREFIX']}shared_items,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}shared_items.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}sharing_settings.module_key={$CONFIG['DB_PREFIX']}shared_items.module_key AND {$CONFIG['DB_PREFIX']}shared_items.module_key='$module_key' ORDER BY {$CONFIG['DB_PREFIX']}shared_items.date_added DESC";



$rs = $CONN->Execute($sql);

echo $CONN->ErrorMsg();

if (!class_exists(InteractDate)) {



	require_once('../../includes/lib/date.inc.php');

	

}



$dates = new InteractDate();



while (!$rs->EOF) {

	$shareditem_key = $rs->fields[0];
	$name = $rs->fields[1];
	$description = $rs->fields[2];
	$username = $rs->fields[3].' '.$rs->fields[4];
	$url = $rs->fields[5];
	$file_path = $rs->fields[6];
	$file_name = $rs->fields[7];
	$date_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[8]),'short', true);	
	$unix_date_added = $CONN->UnixTimestamp($rs->fields[8]);
	$user_key = $rs->fields[9];
	$item_file_path = $rs->fields[10];	
	$date_now = mktime();
	$editable_date = $date_now-1800;

	$t->set_var('SHAREDITEM_KEY',$shareditem_key);
	$t->set_var('NAME',$name);
	$t->set_var('DESCRIPTION',$description);
	$t->set_var('USER_NAME',$username);
	$t->set_var('DATE_ADDED',$date_added);
	$admin_image='';

	$isaudio=false;

	if (!$file_name) {



		$show_url = "<a href=\"fileurl.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key\" >show url</a>";

		$t->set_var('SHOW_URL',$show_url);		
		$t->set_var('URL',$url);

		if(strtolower(substr($url,-4)=='.flv')) {
			$isaudio=true;			
		} else {
			$t->set_var('IMAGE','link');
		}

		if ($is_admin==true | ($user_key==$current_user_key && $unix_date_added>$editable_date)) {

			$admin_image=get_admin_tool("{$CONFIG['PATH']}/modules/sharing/".($isaudio?'audio':'link')."input.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key&action=modify");
		}		
		$t->set_var('ADMIN_IMAGE',$admin_image);
		

	} else {

	

		if ($item_file_path==0 | $item_file_path=='') {

			$url=$CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/sharing/'.$file_path.'/'.$file_name;

		} else  {

			$url=$CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/sharing/'.$file_path.'/'.$item_file_path.'/'.$file_name;			

		}

				

		$show_url = "<a href=\"fileurl.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key\" >show url</a>";

		

		$t->set_var('URL',$url);

		$t->set_var('IMAGE','file');

		$t->set_var('SHOW_URL',$show_url);

		

		if ($is_admin==true | ($user_key==$current_user_key && $unix_date_added>$editable_date)) {

			

			$admin_image=get_admin_tool("{$CONFIG['PATH']}/modules/sharing/fileinput.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key&action=modify");



		}

		$t->set_var('ADMIN_IMAGE',$admin_image);

		//$t->parse('ITEMS', 'sharingitems', true);

	}

	

	$rs2 = $CONN->Execute("SELECT comment_key FROM {$CONFIG['DB_PREFIX']}shared_item_comments WHERE shared_item_key='$shareditem_key'");

	

	if (!$rs2->EOF) {

	

		$count = $rs2->RecordCount();

		$comments="<a href=\"comments.php?space_key=$space_key&module_key=$module_key&shareditem_key=$shareditem_key\" class=\"small\">comments($count)</a> -"; 

		$t->set_var('COMMENTS',$comments);

		

	} else {

	

		$t->set_var('COMMENTS','');

	

	}

	
$t->parse('ITEMS', 'sharingitems'.($isaudio?'audio':''), true);
	

	$rs->MoveNext();

}



$rs->Close();

$t->parse('CONTENTS', 'share', true);

$t->parse('CONTENTS', 'footer', true);

print_headers();

$t->p('CONTENTS');

$CONN->Close();	   

exit;



?>
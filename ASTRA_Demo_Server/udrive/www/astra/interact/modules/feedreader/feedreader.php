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
* Weblink homepage
*
* Refers a browser to Weblink module url. 
*
* @package Weblink
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: feedreader.php,v 1.17 2007/07/30 01:56:58 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/

require_once('../../local/config.inc.php');


//set variables
$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= $_GET['group_key'];
$is_admin = check_module_edit_rights($module_key);
//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$accesslevel_key = authenticate();

//update statistics 
statistics('read');
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'	 => 'header.ihtml',
	'navigation' => 'navigation.ihtml',
	'feed'	   => 'feedreader/feedreader.ihtml',
	'footer'	 => 'footer.ihtml'
));
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->parse('CONTENTS', 'header', true); 

get_navigation();
$rs = $CONN->Execute("SELECT url, file_path, item_count FROM {$CONFIG['DB_PREFIX']}feedreader_settings WHERE module_key='$module_key'");
$feed_urls = $rs->fields[0];
$file_path = $rs->fields[1];
$item_count = $rs->fields[2];
require_once('../../includes/magpie/rss_fetch.inc');
define('MAGPIE_CACHE_ON', true);
define('MAGPIE_CACHE_DIR', $CONFIG['BASE_PATH'].'/local/modules/feedreader'.$file_path);
define('MAGPIE_CACHE_AGE', '7200');
define('MAGPIE_CONDITIONAL_GET_ON', false);
$feed = '';
$urls = explode(',',$feed_urls);

$n=0;
foreach($urls as $value) {
	$id =$n.rand(1111,9999);
	$rss = fetch_rss(trim($value));
	$rss->items = array_slice($rss->items, 0, $item_count);
	
	// should have option to show channel description
	$feed.='<div class="feedHeading"><a href="'.$rss->channel['link'].'"><span class="feedHeadingText">'.$rss->channel['title'].'</span></a></div><div class="feedContent" id="rss'.$id.'"><ul>';
	
	foreach ($rss->items as $item) {
		
		$link = !empty($item['link'])?$item['link']:(!empty($item['guid'])?$item['guid']:'');
		$feed .= '<li><a href="'.$link.'" class="small">'.$item['title'].'</a><br />'.substr(strip_tags($item['description']),0,2000).' ... <a href="'.$link.'">more</a></li>';
			
	}

	$feed .= '</ul>';
// 	$feed .= '<div align="right" class="small"><a href="'.$rss->channel['link'].'">'.$rss->channel['link'].'</a></div>';
}
//if ($is_admin==true) {
	
	//$admin_image=get_admin_tool("{$CONFIG['PATH']}$admin_url?space_key=$space_key&module_key=$module_key2&action=modify");
	
//}
$t->set_var('FEED',$feed);
//$t->set_var('ADMIN_IMAGE',$admin_image);
$t->parse('CONTENTS', 'feed', true);



$t->parse('CONTENTS', 'footer', true);

print_headers();

$t->p('CONTENTS');

exit;
?>
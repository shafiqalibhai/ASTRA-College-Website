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
* Journal rss page
*
* Displays a journal entries as an rss feed
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: rss.php,v 1.37 2007/07/24 05:25:25 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/journal_strings.inc.php');
require_once('lib.inc.php');
require_once($CONFIG['INCLUDES_PATH'].'/lib/html.inc.php');
$html = new InteractHtml();

$space_key			= $_GET['space_key'];
$module_key			= $_GET['module_key'];
$journal_user_key   = $_GET['journal_user_key'];
$tag_key   	= isset($_GET['tag_key'])? $_GET['tag_key'] : '';
$objJournal = new InteractJournal($space_key, $module_key, $group_key, $is_admin, $journal_strings);
$objJournal->setJournalSettings();
$journal_settings = $objJournal->getJournalSettings();
if ($journal_settings['enable_rss']==0) {
	echo 'Sorry - rss is not enabled for the requested journal';
	exit;
}

header("Content-Type: text/xml; charset={$general_strings['character_set']}"); 
if (isset($module_key) && $module_key!='') {

	$rs = $CONN->Execute("SELECT name,description FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'");
	
	if (!$rs->EOF) {
		$module_name = html_to_xml($rs->fields[0]);
		$description = html_to_xml($rs->fields[1]);
	}

}
if (isset($journal_user_key) && $journal_user_key!='') {

	$objUser = singleton::getInstance('user');
	$user_data = $objUser->getUserData($journal_user_key);
	$name='('.$user_data['first_name'].' '.$user_data['last_name'].')';
	$name = html_to_xml($name);
}

$objDate = singleton::getInstance('date');				
$objTags = singleton::getInstance('tags');
$objPosts = singleton::getInstance('posts');
$tag_limit = !empty($tag_key)?' : '.$objTags->getTagText($tag_key):'';

$imagePath=$CONFIG['DATA_PATH'].'/modules/journal/'.($module_key % 100).'/'.$module_key;
$viewPath=$CONFIG['FULL_URL'].'/viewfile.php/modules/journal/'.($module_key % 100).'/'.$module_key;
$iTunesImageTag='';
$imgformats=array('jpg','png'); // iTunes-compatible formats
foreach($imgformats as $value) {
	if(file_exists("$imagePath/iTunesImage.$value")) {
		$iTunesImageTag='<itunes:image href="'.$viewPath.'/iTunesImage.'.$value.'"/>
';
		break;
	}
}
array_push($imgformats,'gif'); // non-iTunes format(s)

echo '<?xml version="1.0" encoding="utf-8" ?>
<?xml-stylesheet title="XSL_formatting" type="text/xsl" href="'.$CONFIG['PATH'].'/local/skins/default/rss.xsl"?>
';

if($iTunesImageTag) {
// using extra iTunes tags as well
	echo'<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
';
} else {
	echo '<rss version="2.0">
';
}

$weblink=$CONFIG['FULL_URL'].'/modules/journal/journalview.php?space_key='.$space_key.'&amp;module_key='.$module_key.($journal_user_key?'&amp;journal_user_key='.$journal_user_key:'');

echo'<channel>
<title>'.$CONFIG['SERVER_NAME'].' - '.$module_name.$tag_limit.'</title> 
<link>'.$weblink.'</link> 
<description>'.$description.'</description>
<generator>Interact http://interactole.org</generator>
';

echo $iTunesImageTag.'
';

foreach($imgformats as $value) {
	if(file_exists("$imagePath/rssImage.$value")) {
		echo '<image>
	<url>'.$viewPath.'/rssImage.'.$value.'</url> 
	<link>'.$weblink.'</link> 
	<title>'.$CONFIG['SERVER_NAME'].' - '.$module_name.'</title>
	<description>'.$description.'</description>
</image>
';
		break;
	}
}

//any extra miscellaneous tags
if(file_exists("$imagePath/rssExtraTags.xml")) {
	readfile("$imagePath/rssExtraTags.xml");
}

$limits = array('row_limit'=> $journal_settings['entries_to_show'],'module_key'=>$module_key, 'user_key'=>$journal_user_key, 'tag_key'=>$tag_key, 'date_limit'=> 'AND date_published<='.$CONN->DBDate(date('Y-m-d H:i:s')));	

// no comments for iTunes...
if(substr($_SERVER['HTTP_USER_AGENT'],0,6)=='iTunes') $limits['parent_key']=0;


$rs = $objPosts->getPostData($limits,false,'DESC');
echo $CONN->ErrorMsg();
while (!$rs->EOF) {
	if($rs->fields['status_key']<2) {
		$parent_key = $rs->fields['parent_key'];
		$thread_key = $rs->fields['thread_key'];
		$entry_key = ($parent_key==0) ? $rs->fields['post_key']: $thread_key;
	
		$heading = html_to_xml($rs->fields['subject']);
		$body = html_to_xml($rs->fields['body']);
	
		if(strtolower($general_strings['character_set'])=='iso-8859-1') {
			$heading=utf8_encode($heading);
			$body=utf8_encode($body);	
		}
	
		$first_name = $rs->fields['first_name'];
		$second_name = $rs->fields['last_name'];
		$date_added = $CONN->UnixTimestamp($rs->fields['date_published']);
		$user_key = $rs->fields['added_by_key'];
		$day = date('D, j M Y',$date_added);
		$hour = date('H:i:s',$date_added);
		$comment = ($parent_key!=0) ? '[Comment] ' : '';
		
		echo '<item>
';
		echo '<title>'.$comment.$heading.'</title>
'; 
		echo '<description>'.$body.'</description>
';
		echo '<pubDate>'.$day.' '.$hour.' GMT</pubDate>
';
		echo 		'<link>'.$CONFIG['FULL_URL'].'/modules/journal/entry.php?space_key='.$space_key.'&amp;module_key='.$module_key.($journal_user_key?'&amp;journal_user_key='.$journal_user_key:'').'&amp;post_key='.$entry_key.'</link>
';

		if($po=$html->parseMediaPlaceholder($rs->fields['body'])) {
//foreach($po as $k=>$v) {echo "$k => $v";}

			if($po['DL'] && $po['DL']!='embed') {
				$extn=$po['DL'];
				if(strpos($extn,',')!==false) $extn=substr($extn,0,strpos($extn,','));
				$url=substr($po['medianame'],0,strrpos($po['medianame'],'.')+1).$extn;
				switch($extn) {
					case 'mp3': $mtype="audio/mpeg"; break;
					case 'mp4': $mtype="video/mp4"; break;
					case 'm4v': $mtype="video/x-m4v"; break;
					case 'mov': $mtype="video/quicktime"; break;
					case 'm4a': $mtype="audio/x-m4a"; break;
	
					//iTunes incompatible, but just in case...
					case 'wmv': $mtype="video/x-ms-wmv"; break;
					case 'wma': $mtype="audio/x-ms-wma"; break;
					default: $mtype='';
				}
				if($po['size'] && $extn==substr($po['medianame'],-4)) {$len=$po['size'];} else {
	
					$len='';
					if($testpath=VIEWFILE_PATH_to_path($url)) {
						$len=filesize($testpath);
					}
					if(!$len) $len=filesize($url);
				}
				if(!preg_match('|^https?://|',$url)) {$url=$CONFIG['SERVER_URL'].$url;}
	
				echo '<enclosure url="'.$url.'"'.($len?' length="'.$len.'"':'').($mtype?' type="'.$mtype.'"':'').' />
';
				
				if($po['dur'] && $iTunesImageTag) {
					echo '<itunes:duration>'.$po['dur'].'</itunes:duration>
';
				}
			}
		}
		
		echo '</item>

';
	}
	$rs->MoveNext();
	
}

$CONN->Close();	   
	
?>
</channel>
</rss>
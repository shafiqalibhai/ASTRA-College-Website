<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Preview Audio</title>
<script language="javascript">
function newmess(){};
parent.dojo.event.topic.registerPublisher("/ipreview/mess", this, "newmess");

<?php

$media=str_replace('&amp;','&',urldecode($_GET['media_placeholder']));

$CONFIG['NO_SESSION'] = 1;
require_once('../../../local/config.inc.php');
require_once($CONFIG['INCLUDES_PATH'].'/lib/html.inc.php');
$html = new InteractHtml();
$po=$html->parseMediaPlaceholder($media);
$browser=browser_get_agent();
if($browser["agent"]=='MOZILLA') $po['wmode']='window';  // should be default 'transparent', but this screws up in preview in FF.

if($po['mediatype']=='mp3') {
	include_once('classAudioFile.php');
	$AF = new AudioFile;
	if(!($thefile=VIEWFILE_PATH_to_path($po['medianame']))) $thefile=$po['medianame'];

	$AF->loadFile($thefile);
//	echo 'DURATION:'.round($AF->wave_length).' seconds';
	$po['dur']=!empty($AF->wave_length)?$AF->wave_length:0;
	echo 'newmess('.$po['dur'].','.(!empty($AF->wave_size)?$AF->wave_size:0).',0,0);';

}else {
// if ($po['player']=='CUSTOMSWFU' || ($po['player']=='AVPLAYER' && $po['mediatype']!='mp3')) {
	$w2=400;$h2=250;  //max size of preview

	if($po['mediatype']=='flv') {
		if($_GET['setsize']) {$w2=320;$h2=280;} else {
			$w2=min($po['w'],400); $h2=min($po['h'],280);
		}
		if($_GET['setsize']) $po['flashvars'].='&report=1';

	} else {
		if($po['player']=='AVPLAYER') {
			$h2=280;
			if(!($thefile=VIEWFILE_PATH_to_path($po['medianame']))) $thefile=$po['medianame'];
		} else $thefile=$_GET['fullpath'];
	
		$mess='';
		//$matches[1];
		require_once('swfheader.class.php');
	//	echo $thefile.'...'.'filesize=';
		$factor=0;

		if($po['player']=='AVPLAYER') {
			$tw=80;
			if(preg_match('/showprogress=1/',$po['flashvars'])){$tw+=320;}
			if(preg_match('/showvolume=0/',$po['flashvars'])){$tw-=40;}
			if(preg_match('/showplay=0/',$po['flashvars'])){$tw-=40;}
			if($tw) $h2-=40;
		}
		
		if($filesize=filesize($thefile)) {
			$swf = new swfheader() ;
			$swf->loadswf($thefile) ;
	//  			echo 'valid=',$swf->valid;
	//  			echo '&filewidth=',$swf->width,'&fileheight=',$swf->height;
	//  			echo '&fps='.$swf->fps[1] . '.' . $swf->fps[0].'&version='.$swf->version;
			if($swf->width) {
				if (($factor=min($h2/$swf->height,$w2/$swf->width))<1) {
					$w2=round($swf->width*$factor);
					$h2=round($swf->height*$factor);
				}
			}
		} else {
			//echo'0&valid=0';
		}

		$fps=$swf->fps[1]? 
			$swf->fps[1].($swf->fps[0]?'.'.round($swf->fps[0]/25.6):'') 
			: 1;
		if(!$po['dur'] || $_GET['setsize']) $po['dur']=round($swf->frames/(floatval($fps)));
	
		echo 'newmess('.($filesize?
	$po['dur'].','.$filesize.','.$swf->width.','.$swf->height.','.($_GET['setsize']?1:0).','.$swf->valid.','.(round($factor*100)).','.$fps.','.$swf->version
	:0
	).');';
	}
	$po['w']=$w2; $po['h']=$h2;

}



?>
</script>

<style>
.embedLink {
background-position:right 50%;
background-repeat:no-repeat;
padding-right:13px;
cursor:pointer;
}
</style>

</head>
<body style="margin: 0px;padding: 2px 6px 1px 6px;">
<?php
echo $html->Media_toSWF($po);
?>
<form id="preview_submit" action="insert_media_preview.php"><input type="hidden" name="media_placeholder" id="media_placeholder"/></form>
</body>
</html>

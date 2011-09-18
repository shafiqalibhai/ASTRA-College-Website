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


require_once('../../../local/config.inc.php');


if(!$_SESSION['current_user_key']) {
	echo '<script type="text/javascript">
<!--
parent.doDisableForm("&nbsp;");
-->
</script>
<br /><br /><br /><br /><div align="center">You don\'t appear to be logged in<br />
<a href="javascript:parent.close()">Close this Window</a></div>';
	exit;
}

$sessbit='/'.$_SESSION['file_path'];
$full_dir_path = $CONFIG['USERS_PATH'].$sessbit;

if(!(strlen($sessbit)>0)) {
	echo '<script type="text/javascript">
<!--
parent.doDisableForm("&nbsp;");
-->
</script>
<br /><br /><br /><br /><div align="center">Error: No file_path session variable.<br />Check that your interact server was updated correctly.<br /><br />
<a href="javascript:parent.close()">Close this Window</a></div>';
	exit;
}
	
if(isset($_POST['flashcommand'])) {
	$pathtofile=$_POST['pathtofile'];
	
	// if $pathtofile begins with our SERVER_URL, convert to server path.
	if (strpos($pathtofile,$removebit=$CONFIG['SERVER_URL'])===0) {
		$replacebit=$CONFIG['BASE_PATH'];
		if ($cpath=$CONFIG['PATH']) {
			if (strstr($replacebit,$cpath)===$cpath) {
				$replacebit=substr($replacebit,0,-strlen($cpath));
			} else {$removebit.=$cpath;}
		}
		$removebit.="/";$replacebit.="/";
		$thefile=$replacebit.substr($pathtofile,strlen($removebit));
	} else {
		if (strpos($pathtofile,"http://")!==0) {
			$thefile=$full_dir_path.'/'.$pathtofile;
		}
	}

	switch ($_POST['flashcommand']) {
	case 'tree':
		echo 'xmltree='.make_tree($full_dir_path);
		break;
	case 'fileinfo':
		checkfile($thefile);
		break;
	case 'imageinfo':
		echo 'filesize=';
		if ($filesize=filesize($thefile)) {
			echo $filesize;
			echo'&';
			$image_array = getimagesize($thefile);
			echo 'filewidth=',$image_array[0],'&fileheight=',$image_array[1];
		} else {echo'0&valid=0';}
		break;
	case 'swfinfo':
		require_once('swfheader.class.php');
		echo 'filesize=';
		if ($filesize=filesize($thefile)) {
			echo $filesize;
			echo'&';
			$swf = new swfheader() ;
			$swf->loadswf($thefile) ;
			echo 'valid=',$swf->valid;
			echo '&filewidth=',$swf->width,'&fileheight=',$swf->height;
			echo '&fps='.$swf->fps[1] . '.' . $swf->fps[0].'&version='.$swf->version;
		} else {echo'0&valid=0';}
		break;
	case 'delete':
			if (is_dir($thefile)) {
				if (delete_directory($thefile)) {echo 'rstatus=OK';} else {echo 'rstatus=ERR -'.$thefile;}
			} else {
				if (unlink($thefile)) {echo 'rstatus=OK';} else {echo 'rstatus=ERR -'.$thefile;
			}
		}
		break;
	case 'newfolder':
		if  (mkdir($thefile)) {echo 'rstatus=OK';} else {echo 'rstatus=ERR';}
		break;
	}
} else {


	echo '<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>IntFlashFrame</title>';

	$xmltree = "";
	$pathtofile= "";
	
	if (isset($_FILES['file']) && $_FILES['file']['name']!='') {
	
		$file_name=$pathtofile=$_POST['pathtofile'];
		if (strlen($file_name)>0) {$file_name .= '/';}
		$file_name .= $_FILES['file']['name'];
		$tmp_file		= $_FILES['file']['tmp_name'];
//if .zip extn (use pathinfo)		exec("unzip -qq -o -d \"$full_file_path\" \"$user_file\" -x \*.iphp .htaccess");
			
//		echo "Path + name of uploaded file = $pathtofile<br />";
		
		if (copy($tmp_file,$full_dir_path.'/'.$file_name)) {
			$pathtofile=$file_name;
		} else {
			echo '<script type="text/javascript">
alert("File failed to upload.  Check permissions.");
</script>';}
		
	//	exit;
	}

	if ($_POST['winIE']==1) {
//	echo 'Windows IE';
	echo '<script language="VBScript">
		On Error Resume Next
		Sub Interactives_FSCommand(ByVal command, ByVal args)
			Call parent.Interactives_DoFSCommand(command, args)
		End Sub
		</script>';
	}
	
	echo '<style type="text/css">
	<!--
	body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
		background-color: #DDDDFF;
	}
	-->
	</style></head>
	<body>';
//	echo '-start of iframe-';
	$playersfolder='../../players/';
	$interactive=$_POST['interactive'];
	if ((substr($interactive,-1)=="U")||(substr($interactive,-6)=="STREAM")) {
		$xmltree=make_tree($full_dir_path);}
	echo '<object align="middle" id="interactives" width="700" height="502" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,79,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="sameDomain" name="allowScriptAccess" /><param name="bgcolor" value="#DDDDFF" /><param value="insert_interactives.swf" name="movie" /><param name="FlashVars" value=\'playersfolder='.$playersfolder.'&theuser_view_path='.$CONFIG['SERVER_URL'].$CONFIG['USERS_VIEW_PATH'].$sessbit.'&pathtofile='.$pathtofile.'&winIE='.$_POST['winIE'].'&interactive='.$interactive.'&iscaling='.$_POST['iscaling'].'&'.$_POST['urlstring'].'&xmltree=',$xmltree,'\'><param value="high" name="quality" /><param value="lt" name="salign" /><embed pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" align="center" name="interactives" bgcolor="#DDDDFF" width="700" height="502" salign="lt" quality="high" src="insert_interactives.swf" FlashVars=\'playersfolder='.$playersfolder.'&theuser_view_path='.$CONFIG['SERVER_URL'].$CONFIG['USERS_VIEW_PATH'].$sessbit.'&pathtofile='.$pathtofile.'&winIE='.$_POST['winIE'].'&interactive='.$interactive.'&iscaling='.$_POST['iscaling'].'&'.$_POST['urlstring'].'&xmltree=',$xmltree,'\' /></object>';

//	echo '-end of iframe-';
	echo '</body></html>';
}

function checkfile($thefilep){
	if (file_exists($thefilep) && ($filesize=filesize($thefilep))!==FALSE) {
		echo 'valid=1&filesize='.$filesize; return TRUE;
	} else {echo 'valid=0&filesize=0';return FALSE;}
}

function make_tree($full_path){ //where $full_path is your source dir.

	if (!isset($tree)) {
		$tree='';
	}
	
	$list=array();
	$handle=opendir($full_path);
     
	while($a=readdir($handle)){
		if(!preg_match('/^\./',$a)){
    		array_push($list,$a);
		}
	}
    closedir($handle);	

	natcasesort($list);
	
	reset($list);
	while($a=current($list)){
		if(is_dir($full_path."/".$a)){
                   
			$tree .= ' <node label="'.$a.'" isBranch="true"';

			$recursive=make_tree($full_path."/".$a);
			$tree .= ' >'.$recursive.' </node>';

		} else {
			   
			$tree .= ' <node label="'.$a.'" />';

		}
		next($list); 
     }
     return $tree;
}
?>
<?php
require_once('../../../local/config.inc.php');
if(!$_SESSION['current_user_key']) {
	echo "<div align=\"center\">You need to be logged in to store files.<br />";
//	echo "<a href=\"javascript:parent.onCancel()\">Close this Window</a>";
	echo '</div>
	<script type="text/javascript">
		self.focus();
	</script>';
	exit;
}
$phplibpath=str_replace('\\','/',$CONFIG['LIBRARY_PATH']); // always use forward slashes.
$phpuserpath=str_replace('\\','/',$CONFIG['USERS_PATH']);

$sessbit='/'.$_SESSION['file_path'];

//check (don't want to delete too much!)
if(!(strlen($sessbit)>0)) {
	echo '<b>FATAL ERROR:</b> No file_path session variable.<br /><br />Check that your interact server was updated correctly.<br /><br />
<a href="javascript:parent.close()">Close this Window</a>';
	exit;
}

$forcenew=false;
$firstload=false;
if ((isset($_POST['full_dir_path']))&&($_POST['full_dir_path']!="")){
	$full_dir_path = $_POST['full_dir_path'];
	$web_dir_path =  $_POST['web_dir_path'];
} else {
	$firstload=!empty($_GET['callfirst']);
	$full_dir_path= $phpuserpath.$sessbit;
	$web_dir_path = $CONFIG['USERS_VIEW_PATH'].$sessbit;
}

$ww=isset($_GET['ww'])? $_GET['ww']:200;

$pathtofile= (isset($_POST['pathtofile'])? $_POST['pathtofile'] : "");

if (!is_dir($full_dir_path."/".$pathtofile)) $pathtofile="";

$trytofindurl= ((!empty($_GET['trytofindurl']) && $_GET['trytofindurl']!='http://')?
$_GET['trytofindurl']:'');


$adminuser=0;
$message="";
$space_key 	= get_space_key();
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

if ($accesslevel_key=='1' || $accesslevel_key=='3') $adminuser=1;
if ($_SESSION['userlevel_key']=='1') $adminuser=2;

if ($adminuser) {

$foundit=false;
$library=array();
$handle = opendir($full_path=$phplibpath); 
$nlib=0;
$modded=false;
$newupload=false;

if ($full_dir_path==$full_path) {$foundit=-1; $message="Make, delete or upload <strong>Library folders</strong> here";}

while($a=readdir($handle)) {
	if (($a{0}!='.') && (is_dir("$full_path/$a"))) {
		if (findit("$full_path/$a",$CONFIG['LIBRARY_VIEW_PATH']."/$a") || $full_dir_path==$full_path."/$a") {
			$full_dir_path=$full_path."/$a";
			$web_dir_path=$CONFIG['LIBRARY_VIEW_PATH']."/$a";
			$foundit=$nlib;
		}
		$library[$nlib++]=$a;
	}
}
closedir($handle);
}

if ($foundit===false) {  // path *must* be user folder if we haven't found it yet.  This prevents potential security breaches.
	$full_dir_path= $phpuserpath.$sessbit;
	$web_dir_path = $CONFIG['USERS_VIEW_PATH'].$sessbit;
	findit($full_dir_path,$web_dir_path);
}

if ((strpos($full_dir_path,$phplibpath)===0) && ($adminuser!=2)) $allowmod=false; else $allowmod=true;

//max size in KB for normal-user:admin:superadmin
$maxSize = (isset($_GET['maxSize']))? $_GET['maxSize'] : "150:300:10000";
if (strpos($maxSize,':')===FALSE) $maxUserSize=$maxSize; else {$tmp=explode(":",$maxSize); $maxUserSize=$tmp[$adminuser];}

$flines = (isset($_GET['flines']))? $_GET['flines'] : 18;  //number of lines in file viewer

if (!empty($_GET['allowed_file_types'])) {
	$allowed_file_types = ($_GET['allowed_file_types']);
	$allowed_file_typesAR = explode(",",$allowed_file_types);
}

if (isset($_FILES['file']) && $_FILES['file']['name']!='') {
	$file_name  = cleanname($_FILES['file']['name']);
	$tmp_file	= $_FILES['file']['tmp_name'];
	
	$exten=getExtension($file_name);
//	strtolower(ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name));
	if (!$allowmod) $message = "<span class=\"espan\">Error:</span> You are not allowed to modify this folder!!"; else 
	if (inAllowed($exten)===false) {
		$message = "<span class=\"espan\">Error:</span> You can only upload <b>$allowed_file_types</b> files";
	} else {
		//No more than $maxUserSize KB, but big files should be caught by form.
		if ((($file_size=$_FILES['file']['size']>>10)>$maxUserSize) || (($_FILES['file']['error'])==1)){
			$message = "<span class=\"espan\">Error:</span> Your ".(($_FILES['file']['size']>0)? $file_size."KB ":"")."file is too big to upload"; if ($_FILES['file']['error']==1){$message.="<br />It exceeds php.ini ".ini_get(upload_max_filesize)." limit";}
		} else {
			if ($_FILES['file']['error']==3) $message = "<span class=\"espan\">Error:</span> File was only partially uploaded"; else
			if (($_FILES['file']['error']==4)||($_FILES['file']['size']==0)) {$message = "<span class=\"espan\">Error:</span> No File!?";

			} else
			if ($exten=="zip") {
				$file_name=substr($file_name,0,-4);
				if (newfolder()) {

	 				exec("unzip -qq -o -d \"".$fullfname."\" \"$tmp_file\" -x \*.iphp .htaccess");
					$message="<b>Unzipped</b> into folder!";
					$modded=true;
				}
			} else {
				if (move_uploaded_file ($tmp_file,($fullfname=$full_dir_path.'/'.pathWithEndSlash($pathtofile).$file_name))) {
					chmod($fullfname, 0755);
					$message=dispname($file_name,24).", $file_size"."KB (".ceil($file_size/5)."s@56K)<br />of type ".$_FILES['file']['type']." uploaded";
					$modded=true;
					$newupload=true;
				} else 
				$message="<span class=\"espan\">Error:</span> Upload failed.  Check permissions";
			}
		}
	}	
}

$moving=false;
switch ($_POST['command']) {
case 'delete':
	if (!$allowmod) $message = "<span class=\"espan\">Error:</span> You are not allowed to modify this folder!!"; else {
	$errs=0;
	foreach ($_POST['files'] as $file_name) {
		$fullfname=$full_dir_path.'/'.pathWithEndSlash($pathtofile).substr($file_name,1);
		$modded=true;
		if ($file_name{0}=="D") {
			if (!delete_directory($fullfname)) $errs+=1;
		} else {
			if (!unlink($fullfname)) $errs+=1;
		}
	}
	if ($errs) {$message="<span class=\"espan\">Error:</span> $errs item".($errs>1? "s":"")." could not be deleted";}}
	break;
	
case 'rename':
	if (!$allowmod) $message = "<span class=\"espan\">Error:</span> You are not allowed to modify this item!!"; else {
		$file_name=cleanname($_POST['file_name']);
		foreach ($_POST['files'] as $tmp) {
			$fullfname=$full_dir_path.'/'.pathWithEndSlash($pathtofile).substr($tmp,1);
			$fulldestname=$full_dir_path.'/'.pathWithEndSlash($pathtofile).$file_name;
			$modded=true;
			if (!rename($fullfname,$fulldestname)) $message="<span class=\"espan\">Error:</span> Rename failed";
		}
	}
	break;

case 'newfolder':
	$file_name=cleanname($_POST['file_name']);
	newfolder();
	break;

case 'move':
	$moving=true;
case 'copy':	
	$source_dir=$_POST['source_dir'];

	if (!$allowmod) $message = "<span class=\"espan\">Error:</span> You are not allowed to modify this folder!"; else {
	if ($adminuser || (strpos($source_dir,$phpuserpath.$sessbit)===0)) {

		$destpath=$full_dir_path."/".pathWithEndSlash($pathtofile);
		$errs=0;
		foreach (explode(":",$_POST['source_files']) as $file_name) {
			$file_name=substr($file_name,1);
			$fullfname=pathWithEndSlash($source_dir).$file_name;
			$fulldestname=$destpath.$file_name;
			if ($moving) {
				if (!rename($fullfname,$fulldestname)) $errs++;
			} else {
				if (!copyr($fullfname,$fulldestname)) $errs++;  // deep copy
			}
		}
		if ($errs) {$message="<span class=\"espan\">Error:</span> $errs item".($errs>1? "s":"")." could not be ".($moving? "moved":"copied");} else {$modded=true;$message="Item(s) ".($moving? "moved":"copied")." to here";}

	} else $message = "<span class=\"espan\">Error:</span> You shouldn't have access to those files!!";
	}
}

function dispname($funame,$nn) {
    if (strlen($funame)>$nn) {
    	return substr($funame,0,$nn>>1)."&hellip;".substr($funame,1-($nn>>1));
    } else {
    	return $funame;
    }
}

function findit($fi_path,$web_path) {
global $trytofindurl,$pathtofile,$file_name;
// hunt down actual file on first launch if possible!
	if($trytofindurl) {
		if (($ppos=strpos($trytofindurl,$web_path))!==FALSE) {
			$findstr=substr($trytofindurl,$ppos+1+strlen($web_path));
			if (is_file("$fi_path/$findstr")) {
	
				$pathtofile=substr($findstr,0,$ppos=strrpos($findstr,"/"));
				$file_name=($ppos==FALSE)? $findstr:substr($findstr,$ppos+1);
				return true;
			}
		}
	}
	return false;
}

function pathWithEndSlash($pp) {
	if (strlen($pp)>0) $pp .= '/';
	return $pp;
}

function cleanname($name) {
	return ereg_replace("[^a-z0-9A-Z._]","",str_replace(" ","_",$name));
}

function divideName($name) {
	if(($lastdot=strrpos($name,'.'))===false) return Array($name,'');

	return Array(substr($name,0,$lastdot),substr($name,$lastdot+1));
}

function getExtension($name) {
	$dname=divideName($name);
	return $dname[1];
}

function inAllowed($extn) {
	global $allowed_file_typesAR, $fgroup;
	
	if(!isset($allowed_file_typesAR)) {
		return 0;
	}
	
	if(empty($extn)) return false;
	
	$rv=false;
	if(!isset($fgroup)) $fgroup=Array();
	
	foreach($allowed_file_typesAR as $key=>$val) {
		if(($pos=strpos($val,strtolower($extn)))!==false) {
			if(!isset($fgroup[$key])) $fgroup[$key]=Array();
			$fgroup[$key][$pos]=$extn;
			$rv=($pos==0?$key:true);
		}
	}
	
	return $rv;
}

function outputOne($full,$vname,$fsel) {
	$type=$vname{0};
	echo "<option value=\"$vname\"";
	if($fsel!==false) echo " selected=\"true\"";
	echo '>'.($type=='f'?'&middot;':'&equiv;')." ".dispname($full,28)."</option>";
}
/**
 * Copy a file, or recursively copy a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure*/

function copyr($source,$dest) {
// Simple copy for a file
	if (is_file($source)) {
		return copy($source, $dest);
	}
	
	if (!is_dir($dest)) {
		mkdir($dest);
	}
  
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
  
        // Deep copy directories
        if ($dest !== "$source/$entry") {
            copyr("$source/$entry", "$dest/$entry");
        }
    }
  
    // Clean up
    $dir->close();
    return true;
}

function newfolder() {
	global $allowmod,$message,$file_name,$fullfname,$full_dir_path,$pathtofile,$modded;
	if (!$allowmod) $message = "<span class=\"espan\">Error:</span> You are not allowed to modify this folder!!"; else {
	if ($file_name>"") {
		$fullfname=$full_dir_path.'/'.pathWithEndSlash($pathtofile).$file_name;
		if (file_exists($fullfname)) {
			if (is_dir($fullfname)) {
				$message="<span class=\"espan\">Error:</span> Folder '".$file_name."' already exists";
			} else {
				$message="<span class=\"espan\">Error:</span> A file named '".$file_name."' already exists";
			}
		} else {
			$modded=true;
			if (mkdir($fullfname)) {return true;} else {$message="<span class=\"espan\">Error:</span> Cannot make folder $fullfname";}
		}
	} else {
		$message="<span class=\"espan\">Error:</span> An alpha-numeric folder name is required";
	}}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script language="javascript">
<?php 
echo 'libpath="'.$phplibpath.'"; weblibpath="'.$CONFIG['LIBRARY_VIEW_PATH'].'"; allowmod='.($allowmod? 'true':'false').';modded='.($modded? 'true':'false').";\n"."forcenewj=0;\n";
?>
function filemanager() {
	ww=Math.min(Math.max(screen.availWidth-620,20),500);
	Pwin=window.open("file_manager.php?ww="+ww, "File_Manager", "toolbar=no,scrollbar=no,location=no,directories=no,status=no,menubar=no,width=600,height=436,resizable=no,left="+(ww+16)+",top=32,screenX="+ww+",screenY=32");
	Pwin.focus();
};

function pathWithStartSlash(pp) {return ((pp.length>0)? "/":"")+pp;}

function clickList(nclicks) {
	var ncount=countSelected();
	if (ncount) {
		if (nclicks&2) {
			clearMultiple();
			ncount=1;
		}
		var sel=document.upload['files[]'];

		var fullname=sel.options[sel.selectedIndex].value;
		
		var fname=fullname.substr(1);
		var filetype=fullname.charAt(0);
		
		var fpath=document.upload.pathtofile.value;
		
		if (fpath.length>0) fpath+="/";
		fpath+=fname;
		if (filetype=="D" && nclicks&2) {
			if (document.upload.full_dir_path.value==libpath) {
				selLibPath(fname);
			} else {
				document.upload.pathtofile.value=fpath;
				document.upload.submit();
			}
		} else {
			if (ncount==1 && filetype!="D") {
				newFi(document.upload.web_dir_path.value+"/"+fpath,nclicks,document.upload.full_dir_path.value+'/'+fpath);
			}
		}
	}
}

function newFi(a,b,c){}

function countSelected() {
	var ncount=0;var sel=document.upload['files[]'];
	if (sel.selectedIndex>-1) {
		for (i=sel.selectedIndex; i<sel.length;) if (sel.options[i++].selected) ncount++;
	}
	return ncount;
}

function clearMultiple() {
	var sel=document.upload['files[]'];
	for (i=sel.selectedIndex+1; i<sel.length;) sel.options[i++].selected=false;
}

function init(){
	var sel=document.upload['files[]'];
	if (sel.selectedIndex>-1) {
		if (sel.selectedIndex)
		sel.options[sel.selectedIndex].selected=true;
	}

	if((modded)&&(window.name!="idir")) {	
		openerdir=parent.window.opener.document.upload.full_dir_path.value+pathWithStartSlash(parent.window.opener.document.upload.pathtofile.value);
		sourceform=parent.window.frames["idir"+(3-(window.name.charAt(window.name.length-1)))].document.upload;
		sourcedir=sourceform.full_dir_path.value+pathWithStartSlash(sourceform.pathtofile.value);
	
		thisdir=document.upload.full_dir_path.value+pathWithStartSlash(document.upload.pathtofile.value);
	
	//+pathWithStartSlash(document.upload.pathtofile.value)
	//+pathWithStartSlash(sourceform.pathtofile.value)
	
		if ((openerdir.indexOf(thisdir)===0) <?php if ($moving) echo '||(openerdir.indexOf(sourcedir)===0)'; ?>) parent.window.opener.document.upload.submit();

	}
	parent.dojo.event.topic.registerPublisher("/idir/newfile", this, "newFi");
	if(forcenewj) newFi(<?php 
	echo str_replace('\\','\\\\',"'$web_dir_path/".pathWithEndSlash($pathtofile)."$file_name',forcenewj,'".$full_dir_path.'/'.pathWithEndSlash($pathtofile).$file_name."'") ?> );
}

function clickDelete() {
	var sel=document.upload['files[]'];
	var ncount=countSelected();
	if (ncount) {
		if (ncount>1) {
			var confString="Are you sure you want to delete the "+ncount +" selected items?";
		} else {
			var fullname = sel.options[sel.selectedIndex].value;
			var fname=fullname.substr(1);
			if(!checkFgroup(fname)) return;
			
			var confString="Are you sure you want to delete the "+(fullname.charAt(0)=="D"? "folder":"file")+' "'+fname+'"?';
		}
		if (confirm(confString)) {
			document.upload.command.value="delete";
			document.upload.submit();
		}
	}
}

function rename() {
	var sel=document.upload['files[]'];
	if (sel.selectedIndex>-1) {
		clearMultiple();
		var fullname=sel.options[sel.selectedIndex].value;
		var fname=fullname.substr(1);
		if(checkFgroup(fname)) {
			if (document.upload.file_name.value=prompt("Enter the new name:",fname)) {
				document.upload.command.value="rename";
				document.upload.submit();
			}
		}
	}
}

function getExtension(name) {
	var li=name.lastIndexOf('.');
	return (li>-1? name.substr(li+1) : '');
}

function checkFgroup(name) {
	var extn=getExtension(name);
	if(extn.indexOf(':')>-1) {
		alert('Cannot edit grouped media files - please use File Manager');
		return false;
	}
	return true;
}

function newFolder() {
	if (document.upload.file_name.value=prompt("Enter a name for the new Folder:","NewFolder")) {
		document.upload.command.value="newfolder";
		document.upload.submit();
	}
}

function parentFolder() {
	pathtofile=document.upload.pathtofile.value;
	pathtofile= ((xchop=pathtofile.lastIndexOf("/"))<1)? "" : pathtofile.substring(0,xchop);

	document.upload.pathtofile.value=pathtofile;
	document.upload.submit();
}

function gotoPath(path) {
	document.upload.pathtofile.value=path;
	document.upload.submit();
}

function submitFile() {
	document.getElementById('fileupload').style.display='none';
	document.getElementById('progress').style.display='block';
	document.upload.submit();
}

function selLibPath(newpath) {
	document.upload.pathtofile.value="";

	if (newpath!="*") {
		if (newpath=="") {
			document.upload.full_dir_path.value="";
		} else {
			document.upload.full_dir_path.value=libpath+"/"+newpath;
			document.upload.web_dir_path.value=weblibpath+"/"+newpath;
		}
		document.upload.submit();		
	} 
	<?php
		if ($adminuser==2) {
			echo 'else {
			document.upload.full_dir_path.value=libpath;
			document.upload.web_dir_path.value=weblibpath;
			document.upload.submit();
		}';
		}
	?>
}

</script>


<style>
	HTML {height: 100%;}
	BODY
		{
		FONT-FAMILY: Verdana;FONT-SIZE: 10pt;
		margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
		background-color: #D2D2D8;
		height: 100%;
		}
	TABLE
		{
	    FONT-SIZE: xx-small;
		}
	INPUT
		{
		font:8pt verdana,arial,sans-serif;background-color: #DDDDDF;
		}
	SELECT
		{
		font:9pt verdana,arial,sans-serif; line-height: 9pt;
		}

	BUTTON {border-width: 1px; background-color: #DDDDDF;}

	 A {cursor : pointer;}
	 A:hover {color: blue;}
	 .espan {color: red; font-weight:bold;}
</style>

</head>





<body onload="init();">
<table cellspacing="0" cellpadding="0" width="100%" height="100%"><tr><td width="100%" height="100%" align="center" valign="middle">
  <form name="upload" id="upload" action="<?php echo $_SERVER['PHP_SELF']."?allowed_file_types=$allowed_file_types&maxSize=$maxSize&flines=$flines";?>" method="post" enctype="multipart/form-data">

<?php
if ((count($library)>0) || ($adminuser==2)){
	$flines--;
	echo 'Library: <select name="selLib" id="selLib" style="margin-bottom:2px;" onChange="selLibPath(this.options[this.selectedIndex].value);">';
	echo '<option value=""';
	if ($foundit===false) echo " selected=\"true\"";
	echo ">&bull; My User Folder</option>";
	
	for ($csel=0;$csel<count($library);$csel++) {
		echo "<option value=\"$library[$csel]\"";
		if ($csel===$foundit) echo " selected=\"true\"";
		echo ">&equiv;".dispname($library[$csel],20)."</option>";
	}

	if ($adminuser==2) {  //only super admins can go to main lib folder
		echo "<option value=\"*\"";
		if ($foundit===-1) echo " selected=\"true\"";
		echo ">Edit Library Folders</option>";
	}
	echo '</select><br />';
}

if ($pathtofile>"") {
	$flines-=2;
	echo '<table cellspacing="0" style="margin-bottom:1px;"><tr><td><button type="button" style="width:40px;" onclick="parentFolder();" title="Go up one level (Parent Folder)"><img  src="../images/ParentFolder.gif" width="17" height="18" ></button>&nbsp;</td><td valign="top"><a onclick="gotoPath(\'\')">Home</a> &raquo; ';
	$pathbits="";
	$pathexplode=explode("/", $pathtofile);
	for($csel=0;$csel<count($pathexplode);$csel++) {
		$pathbits.=$pathexplode[$csel];
		if ($csel==count($pathexplode)-1) {
			echo "<strong>".dispname($pathexplode[$csel],30)."</strong>";
		} else {
			echo "<a onclick=\"gotoPath('$pathbits')\">".dispname($pathexplode[$csel],18)."</a> &raquo; ";
		}
		$pathbits.="/";
	}
	echo"</td></tr></table>";
}

if ($message>"") $flines--;

echo '
<select style="width:96%;height:'.$flines*1.3.'em;" name="files[]" id="files[]" size="',$flines,'" multiple onchange="clickList(1);" ondblclick="clickList(2);">';

$full_path=$full_dir_path."/".$pathtofile;
$list=array();
$handle = opendir($full_path);


$somehidden=false;
$fgroup=Array();

while($a=readdir($handle)) {
    if($a{0}!='.') { 
		if (is_file("$full_path/$a")) {
			if(isset($allowed_file_typesAR)) {
				$dname=divideName($a);
				$extn=$dname[1];
				
				foreach($allowed_file_typesAR as $val) {

					if(($epos=strpos($val,strtolower($extn)))===0) {
						array_push($list,"f$a");
					} else {
						if(epos!==false) {
							if($extn!='flv') {
								$superfile=$dname[0].'.'.substr($val,0,strpos($val,':'));
								if(!isset($fgroup[$superfile])) $fgroup[$superfile]=Array();
								array_push($fgroup[$superfile],$extn);
							}
						}
					}
				}
			} else {array_push($list,"f$a");}

		} else {
			array_push($list,"D$a");
		}
	}
}

closedir($handle);	

natcasesort($list);

reset($list);
$lastname='';

while($a=current($list)){
	$fname=substr($a,1);
	if ($a{0}=="D") {
		if($lastname) outputGroup($lastname);
		$lastname='';
		outputOne($fname,$a,($file_name==$fname));
	} else {
		$oname=$fname;
		$vname=$a;
		if(isset($fgroup[$fname])) {
			$k=0;
			foreach($fgroup[$fname] as $val) {
				$dname=divideName($fname);
				if($file_name==($dname[0].".$val")) {$file_name=$oname;}
				$fname.=($k? ',':' (') . $val;
				$vname.=':'.$val;
				$k++;
			}
			$fname.=')';
		}
		outputOne($fname,$vname,($file_name==$oname));
		if($file_name==$oname && ($newupload || $firstload)) {$forcenew=true; $file_name=$vname;}
	}
	next($list); 
}
?>

</select><br />
<?php
  if ($message>"") echo "<div name='message' id='message' style='padding: 1px; margin:2px; border: 1px solid;' align=\"center\">$message</div>";

  echo '<table width="96%" cellspacing="0" cellpadding="0" style="margin-top:1px;"><tr><td width="';
  if ($allowmod) echo '36" align="center">
   	<button type="button" style="width:36px;" onclick="clickDelete();" title="Delete"><img src="../images/Trash.gif" width="16" height="17" valign="bottom">
    </button></td><td width="36" align="center">
    <button type="button" style="width:45px; padding:0;height:21px; FONT-SIZE:xx-small" onclick="rename();" title="Rename"><span style="background-color:#A5C3F8;">Rename</span></button></td><td width="36" align="center">
    <button type="button" style="width:36px;" onclick="newFolder();" title="New Folder"><img src="../images/NewFolder.gif" width="21" height="17" valign="bottom">
    </button>'; else echo '72">';
    
    echo '</td>
<script language="javascript">';
	echo 'if (window.name=="idir") {
    	document.write(\'<td width="36" align="center"><button type="button" style="width:36px;" onclick="filemanager();" title="Open File Manager"><img src="../images/ed_file_manager.gif" width="18" height="17" valign="bottom"></button></td>\');
    }';
    if($forcenew) {echo 'forcenewj='.($newupload?1:5).';';}
    echo '
</script>

    <td valign="top" align="right">
    <INPUT type="button" type="button" style="font:10pt verdana" onclick="clickList(3);" value="Open"></td></tr>
  </table>';

	if ($allowmod) echo '<table id="fileupload" cellpadding="0" style="margin-top:6px; border: 1px solid grey;"><tr><td>Upload&nbsp;a&nbsp;file:<input type="hidden" name="MAX_FILE_SIZE" value="'.($maxUserSize<<10).'" /><input name="file" type="file" title="Use this to upload a file" size="1" onChange="submitFile();"> '.$maxUserSize.'KB max<br /></td></tr></table><img src="'.$CONFIG['PATH'].'/images/progress.gif" style="display:none;padding:0;margin:0" id="progress" align="absbottom" width="96" height="21" border="0">';

	if(isset($allowed_file_types)) {
		echo "only $allowed_file_types files"; 
		if ($somehidden) echo " - <span class=\"espan\">some files hidden</span>";
		}
//	echo '<br /><span style="FONT-SIZE: xx-small;">'.$full_path.'</span>';
?>
	<input type="hidden" name="pathtofile" value="<?php echo $pathtofile;?>">
    <input type="hidden" name="full_dir_path" value="<?php echo $full_dir_path;?>">
    <input type="hidden" name="web_dir_path" value="<?php echo $web_dir_path;?>">
    <input type="hidden" name="command" value="">
    <input type="hidden" name="file_name" value="">
    <input type="hidden" name="source_files" value="">
    <input type="hidden" name="source_dir" value="">
  </form>
</td></tr></table>
</body>
</html>
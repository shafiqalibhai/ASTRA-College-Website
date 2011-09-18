<?php
require_once('../../../local/config.inc.php');

if(!$_SESSION['current_user_key']) {
	echo "<div align=\"center\">
You don't appear to be logged in 
<br />
"; echo "<a href=\"javascript:close()\">Close this Window</a> 
</div>
"; echo '<script type="text/javascript">
		self.focus()
		</script>'; exit; 
}


$ww=isset($_GET['ww'])? $_GET['ww']:200;
$file_dir_iframe_url="file_dir_iframe.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>File Manager</title> 
<style>
	HTML, BODY
		{
		FONT-FAMILY: Verdana;FONT-SIZE: xx-small;
		background: #E0E0E4;
  		color: ButtonText;
  		margin: 0px;
  		padding: 0px;
		}
	TABLE
		{
	    FONT-SIZE: xx-small;
		}
	INPUT
		{
		font:8pt verdana,arial,sans-serif;
		}
	select
		{
		font:8pt verdana,arial,sans-serif
		}	

	body { padding: 5px; }

	select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
	button { width: 70px; }

	form { padding: 0px; margin: 0px; }
</style>


<script type="text/javascript">
self.focus();
var pWin;

function pathWithStartSlash(pp) {return ((pp.length>0)? "/":"")+pp;}

function NewFile(url,nclicks) {
if (nclicks&2) {
	<?php echo "var ww=$ww;"; ?>
	
		if (pWin=window.open(url,"File_Manager_preview","toolbar=no,scrollbar=yes,location=no,directories=no,status=no,menubar=no,width="+ww+",height="+(ww>>2)*3+",resizable=yes,left=0,top=32,screenX=0,screenY=32")) {
			pWin.focus();
			window.focus();
		} else alert("Pop-up preview could not open, probably due to pop-up blocking.\n\n  Try using the Open button rather than double-clicking.");
	}
}

function moveList(source, moveType) {
dest=3-source;
	sourcei=window.frames["idir"+source];
	sourceform=sourcei.document.upload;
	sourcefilelist=sourceform['files[]'];
	desti=window.frames["idir"+dest];
	destform=desti.document.upload;
destform.source_dir.value=sourceform.full_dir_path.value+pathWithStartSlash(sourceform.pathtofile.value);
	destdir=destform.full_dir_path.value+pathWithStartSlash(destform.pathtofile.value);
	if (sourcefilelist.selectedIndex<0) alert('Select something first!');
	else if (destform.source_dir.value==destdir) alert('Cannot '+moveType+' to the same place as original item');
	else if(!(desti.window.allowmod)) alert('You are not allowed to modify '+destform.selLib.options[destform.selLib.selectedIndex].value+pathWithStartSlash(destform.pathtofile.value));
	else if ((moveType=="move") && (!(sourcei.window.allowmod))) alert('You are not allowed to move items out of '+sourceform.selLib.options[sourceform.selLib.selectedIndex].value+pathWithStartSlash(sourceform.pathtofile.value));
	else {
		source_files="";
		for(i=sourcefilelist.options.length-1;i>=0;i--) {
			if (sourcefilelist.options[i].selected) {
				source_files+=sourcefilelist.options[i].value+":";
				if (moveType=="move") sourcefilelist.options[i]=null;
			}
		}
		openerfilelist=null;
		if (mess=sourcei.document.getElementById("message")) mess.style.visibility="hidden";

		destform.source_files.value=source_files.substring(0,source_files.length-1);
		destform.command.value=moveType;
		destform.submit();
	}
}


</script>
</head>

<!-- iFrame dirs should be named "idir1" and "idir2".  "idir" is what it is called in the opener (insert image, et al) -->
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="46%" valign="top">

			<fieldset style="background-color: #D2D2D8; padding: 0px; margin-bottom:4px;">

				<legend>
					File Browser 1 
				</legend>
				<iframe name="idir1" id="idir1" frameborder="0" style="border : 0px; width: 100%; height: 400px;" src="<?php echo $file_dir_iframe_url;?>" scrolling="no">
				</iframe>
			</fieldset>
		</td>
		<td valign="bottom">
<table border="0" cellspacing="0" cellpadding="2"><tr>
<td align="center" BGCOLOR="#5AB05A">
		<button width="30" name="LtoR" title="Move selected items from left side to right side" onclick="moveList(1,'move');"> >>> </button><br /><br />
		Move Files<br /><br />
		<button width="30" name="RtoL" title="Move selected items from right side to left side" onclick="moveList(2,'move');"> <<< </button>
</td></tr><tr><td>
<br /><br /><br />
</td></tr><tr><td align="center" BGCOLOR="#7070FF">
		<button width="30" name="LtoR" title="Copy selected items from left side to right side" onclick="moveList(1,'copy');"> >>> </button><br /><br />
		Copy Files<br /><br />
		<button width="30" name="RtoL" title="Copy selected items from right side to left side" onclick="moveList(2,'copy');"> <<< </button>
</td></tr>		
</table>
<br /><br /><br /><button width="30" name="Finished" onclick="if (pWin) pWin.close();window.close();"> Finished </button>
		</td>
		<td width="46%" valign="top">

			<fieldset style="background-color: #D2D2D8; padding: 0px; margin-bottom:4px;">

				<legend>
					File Browser 2 
				</legend>
				<iframe name="idir2" id="idir2" frameborder="0" style="border : 0px; width: 100%; height: 400px;" src="<?php echo $file_dir_iframe_url;?>" scrolling="no">
				</iframe>
			</fieldset>
		</td>
	</tr>
</table>

</body>
</html>

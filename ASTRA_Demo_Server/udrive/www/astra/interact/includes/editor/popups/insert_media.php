<?php

require_once('../../../local/config.inc.php');

if(!$_SESSION['current_user_key']) {
	echo "<div align=\"center\">You don't appear to be logged in<br />";
	echo "<a href=\"javascript:close()\">Close this Window</a></div>";
	exit;
}

$sql = "SELECT file_path from {$CONFIG['DB_PREFIX']}users where user_key='{$_SESSION['current_user_key']}'";
$rs = $CONN->Execute($sql);

$action    = $_GET['action'];
if ($_SERVER['REQUEST_METHOD']=='GET') {

    $file = $_GET['file'];
	
} else {

    $file_name = $_FILES['file']['name'];
    $file      = $_FILES['file']['tmp_name'];
	
}



while (!$rs->EOF) {
	$filepath = $CONFIG['USERS_PATH']."/".$rs->fields[0];
	$url_path = $CONFIG['USERS_VIEW_PATH']."/".$rs->fields[0];
	$rs->MoveNext();
}
$rs->Close();

	if ($action=="del" && is_file($CONFIG['BASE_PATH'].$file)) {

		if (unlink($CONFIG['BASE_PATH'].$file)) {
			$message = "The image has been deleted";
		} else {
			$message = "The image could not be deleted";
		}	
	} else if ($action=="upload" && $file!="none") {
		//get file extension to see if it is an image
		$allowed_image_types = array('mp3', 'swf');
		$ext = strtolower(ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name));
		if (!in_array($ext,$allowed_image_types)) {
			$message = "You can only upload gif, jpg and png files";
		//check that the file is not bigger than 100KB
		} else if ($file_size>100000000000) {
			$message = "That file is too large to upload";
		
		} else {
			//if image is wider than 300 pixels resize it
   			$image_array = GetImageSize($file); // Get image dimensions
    		$image_width = $image_array[0]; // Image width
    		$image_height = $image_array[1]; // Image height
		    if ($image_width > 400) {
            	$newwidth=400;
            	$factor=400/$image_width;
            	$newheight=$image_height*$factor;
            	exec("mogrify -geometry $newwidth x $newheight $file");
        	}
			$image_name = ereg_replace("[^a-z0-9A-Z._]","",$file_name);
			if (copy($file,$filepath."/".$image_name)) {
				$message = "The image was uploaded";
			} else {
				$message = "The image could not be uploaded for some reason";
			}
		}
	}
	?>
<html>

<head>
  <title>Insert Audio/Video</title>
	<style>
	BODY
		{
		FONT-FAMILY: Verdana;FONT-SIZE: xx-small;
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
		height: 22px; 
		top:2;
		font:8pt verdana,arial,sans-serif
		}	
	.bar 
		{
		BORDER-TOP: #99ccff 1px solid; BACKGROUND: #003399; WIDTH: 100%; BORDER-BOTTOM: #000000 1px solid; HEIGHT: 20px
		}		
	</style>
	<script type="text/javascript" src="popup.js"></script>

<script type="text/javascript">

window.resizeTo(450, 100);
self.focus()

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
  if (param) {
      document.getElementById("f_url").value = param["f_url"];
      document.getElementById("f_alt").value = param["f_alt"];
      document.getElementById("f_border").value = param["f_border"];
      document.getElementById("f_align").value = param["f_align"];
      document.getElementById("f_vert").value = param["f_vert"];
      document.getElementById("f_horiz").value = param["f_horiz"];
	  document.getElementById("f_width").value = param["f_width"];
	  document.getElementById("f_height").value = param["f_height"];	  
	  //window.ipreview.location.replace(param.f_url);

	  showExistingImage(param["f_url"]);
  }
  document.getElementById("f_url").focus;
};

function onOK() {
  document.getElementById("f_url").value = '/includes/editor/images/ed_sound.gif';
  var required = {
  
  };
  for (var i in required) {
    var el = document.getElementById(i);
    if (!el.value) {
      alert(required[i]);
      el.focus();
      return false;
    }
  }
  // pass data back to the calling window
  var fields = ["f_url", "f_alt", "f_align", "f_border",
                "f_horiz", "f_vert", "f_width", "f_height"];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};

// Fired when the width or height input texts change
function sizeChanged(axe) 
{
	// Verifies if the aspect ration has to be mantained
	txtWidth = document.getElementById("f_width");
	txtHeight = document.getElementById("f_height");
	if (imageOriginal && document.getElementById("chkLockRatio").checked)
	{
		if ((axe) == "Width")
		{
			if (txtWidth.value != "") 
			{
				if (! isNaN(txtWidth.value))
					txtHeight.value = Math.round( imageOriginal.height * ( txtWidth.value  / imageOriginal.width ) ) ;
			}
			else
				txtHeight.value = "" ;
		}
		else
			if (txtHeight.value != "")
			{
				if (! isNaN(txtHeight.value))
					txtWidth.value  = Math.round( imageOriginal.width  * ( txtHeight.value / imageOriginal.height ) ) ;
			}
			else
				txtWidth.value = "" ;
	}
	
	//updatePreview() ;
}
// Fired when the Reset Size button is clicked
function resetSize()
{
	if (! imageOriginal) return ;

	txtWidth.value  = imageOriginal.width ;
	txtHeight.value = imageOriginal.height ;
	//updatePreview() ;
}
</script>

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
form p {
  margin-top: 5px;
  margin-bottom: 5px;
}
.fl { width: 9em; float: left; padding: 2px 5px; text-align: right; }
.fr { width: 6em; float: left; padding: 2px 5px; text-align: right; }
fieldset { padding: 0px 10px 5px 5px; }
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
.space { padding: 2px; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}
form { padding: 0px; margin: 0px; }
</style>

</head>

<body onLoad="Init()">
<div class="title">Insert Audio</div>
<?php
echo "<div align=\"center\">$message</div>";
?>

	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign=top>
		<!-- Content -->

		    <table border=0 cellpadding=3 cellspacing=3 align=center>
		        <tr>
		
  		            <td valign=top>

				
				<table border=0 cellpadding=0 cellspacing=0 width=220>
				            <tr>
                                <td>
				<div class="title" style="padding-left: 5px;">
				<font size="2" ><b>File name</b></font>
				</div></td>
                            </tr>
				        </table>
                        <div style="overflow:auto;height:120;width:220;BORDER-LEFT: #316AC5 1px solid;BORDER-RIGHT: LightSteelblue 1px solid;BORDER-BOTTOM: LightSteelblue 1px solid;">
                            <table border=0 cellpadding=3 cellspacing=0 width=220>
                                <?php

	


$dir = dir($filepath); 

while($entry=$dir->read()) { 

    if($entry == "." || $entry == "..") { 
        continue; 
    } 

    $fp = @fopen("$filepath/$entry","r"); 
    if(!$fp) { 
        print "Bad entry: $entry<br />"; 
        continue; 
    } 
    $name = fgets($fp,4096); 
    fclose($fp); 
 	if (is_file("$filepath/$entry")) {

		$ext = strtolower(ereg_replace("^.+\\.([^.]+)$", "\\1", $entry));
		
		if ($ext=='mp3') {
	
?>
                                 
		                        <tr bgcolor=Gainsboro>
    	                            <td valign=top><?php echo $entry?></td>
                                    <td valign=top></td>
                                    <td valign=top></td>
                                    <td valign=top style="cursor:hand;" onClick="selectImage('<?php echo "$url_path/$entry" ?>', '<?php echo "%MP3%$url_path/$entry%MP3%" ?>', '<?php echo "$url_path/$entry" ?>')"><u><font color=blue>select</font></u></td>
                                    <td valign=top style="cursor:hand;" onClick="deleteImage('<?php echo "$url_path/$entry" ?>')">	<u><font color=blue>del</font></u></td>
                                </tr>

                                <?php 
		}
	}
	
} 
 ?>
                                               
    
	
	
	


                            </table>
                        </div>
                        <FORM METHOD="Post" ENCTYPE="multipart/form-data" ACTION="insert_media.php?action=upload" ID="form1" name="form1">
				Upload Audio: <br />
				            <INPUT type="file" id="file" name="file" size=22 style="font:8pt verdana,arial,sans-serif">
                            <br />
				            <input name="sub_dir" ID="sub_dir" type=hidden value="<?php echo $sub_dir?>">
				            <INPUT TYPE="submit" value="Upload" >
				</FORM></td>
                    <td align=center style="BORDER-TOP: #336699 1px solid;BORDER-LEFT: #336699 1px solid;BORDER-RIGHT: #336699 1px solid;BORDER-BOTTOM: #336699 1px solid;" bgcolor=White>
			
				    <iframe name="ipreview" id="ipreview" frameborder="0" style="border : 1px solid gray;" height="130" width="150" src=""></iframe>
				</td>
                </tr>
		
		        <tr>
		            <td align=center colspan=2>&nbsp;
				</td>
                </tr>
		    </table>

		<!-- /Content -->
		
          </td>
	</tr>
	</table>



<script language="JavaScript">
function deleteImage(sURL)
	{
	if (confirm("Delete this image?") == true) 
		{
		window.navigate("insert_image.php?action=del&file="+sURL);
		}
	}
function selectImage(surl, filename, filename2)
	{
	
	window.ipreview.location.replace('/includes/players/previewaudio.php?mp3_file='+filename2);
	imageOriginal = new Image() ;
	imageOriginal.src = surl ;
	image_1 = new Image();
    image_1.src = surl;
	document.getElementById("f_url").value = surl;
	document.getElementById("f_alt").value = filename;
	document.getElementById("f_width").value = '18';
	document.getElementById("f_height").value = '18';

	}
	function showExistingImage(surl)
	{
	imageOriginal = new Image() ;
	imageOriginal.src = surl ;
	window.ipreview.location.replace(surl);
	document.getElementById("f_url").value = surl;

	}
function test() {

alert('hello jo');
}

</script>


<!--- new stuff --->
<form action="" method="get">

<input name="url" type="hidden" id="f_url"  title="Enter the image URL here" size="30" />
<input name="alt" type="hidden" id="f_alt"  title="For browsers that don't support images" size="30" />

	
<input type="hidden" name="align" id="f_align"  title="Positioning of this image" />
<input type="hidden" name="border" id="f_border" title="Leave empty for no border" />
<input type="hidden" name="horiz" id="f_horiz"  title="Horizontal padding" />

<input type="hidden" name="vert" id="f_vert"  title="Vertical padding" />
<input type="hidden"  id="f_width" >
<input type="hidden"  id="f_height" >
<input type="hidden" class="CheckBox" checked id="chkLockRatio" >
  <div align="center">
<input type="submit" name="ok" value="OK" onClick="return onOK();">
<input type="submit" name="cancel" value="Cancel" onClick="return onCancel();">
    </div>
</form>

</body>
</html>

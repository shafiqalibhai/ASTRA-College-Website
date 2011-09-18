<?php
require_once('../../../local/config.inc.php');

$file_dir_iframe_url="file_dir_iframe.php?allowed_file_types=mp3,zip&maxSize=300:5000:10000";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Insert Audio</title> 

<script type="text/javascript">
function onCancel() {
  __dlg_close(null);
  return false;
};

<?php
readfile('popup'.(!empty($_GET['type'])?'_'.$_GET['type']:'').'.js');

// if(!$_SESSION['current_user_key']) {
// 	echo 'self.focus();</script></head><body><div align="center">You don\'t appear to be logged in<br />';
// 	echo '<a href="javascript:onCancel()">Close this Window</a></div></body></html>';
// 	exit;
// }
?>

self.focus();
var pre_image;

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
  
  document.getElementById("f_url").focus();
  
  if (param) {
document.getElementById("f_url").value=param["f_url"].substring(param["f_url"].indexOf("sndname=")+8,param["f_url"].lastIndexOf("XXMP3STREAMXX"));

	showExisting(document.getElementById("f_url").value);
  	document.getElementById("f_alt").value=param["f_alt"];
  }
  
  document.getElementById("idir").src="<?php echo $file_dir_iframe_url;?>&trytofindurl="+document.getElementById("f_url").value;
}

function NewFile(url,nclicks) {
	if (nclicks&1) {
		document.getElementById("f_url").value = url;
		document.getElementById("f_alt").value = "Flash MP3 Sound";
		window.ipreview.location.replace('insert_media_preview.php?media_placeholder='+escape('<img src='+media_placeholder_url()+'"/>'));
	}
}
	
function showExisting(url)
	{
	NewFile(url,3);
}


function onOK() {
  var required = {
    "f_url": "You must enter the URL" };
  for (var i in required) {
    var el = document.getElementById(i);
    if (!el.value) {
      alert(required[i]);
      el.focus();
      return false;
    }
  }
  // pass data back to the calling window
  var param = new Object();
  param["f_url"] = media_placeholder_url();
param["f_alt"] = document.getElementById("f_alt").value;
param["f_width"] = 81;
param["f_height"] = 52;

  __dlg_close(param);
  return false;
};

function media_placeholder_url() {
	return '<?php echo $CONFIG['PATH']?>/includes/editor/images/MP3STREAM_placeholder.gif?XXMP3STREAMXXfixed:81:52:sndname='+document.getElementById("f_url").value+'XXMP3STREAMXX';
}
</script>

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
</head>


<body onload="Init();">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="220" rowspan="2" align=center valign="top">
			<fieldset style="height:76px; background-color: #F6F6F6; padding: 0px;">
				<legend>
					Preview 
				</legend>
				<iframe name="ipreview" id="ipreview" height="78" frameborder="0" style="border : 2px; width: 100%; height: 78px;" src="about:blank" scrolling="no"> </iframe>
			</fieldset><input type="button" style="font-size:xx-small;float:right;padding:0px;margin:0px;" onClick="NewFile(document.getElementById('f_url').value,1)" value="Update">
			<div align="center" id="premess">
				<br />
			</div>
			<form action="" method="get">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top">
										URL:<br />
										<input name="url" type="text" id="f_url" title="Enter the image URL here" size="32"> <br /><br />
<!-- 										<input name="alt" type="hidden" id="f_alt" title="For browsers that don't support images" size="30">  -->

						</td>
					</tr>
				</table>
			</form><br /><br /> <br /><br /><br /><br />
			<fieldset style="width:180px;background-color: #ECECF0;">
				<legend>
					Note: 
				</legend>Your audio file needs to have a sample rate of 8, 11, 22 or 44kHz. <br /> <br />It then needs to be saved in <strong>mp3</strong> format.<br /><br />A <strong>mono 32kb/s</strong> compression bit-rate is recommended for dial-up use.</fieldset>
		</td>
		<td width="276" valign="top">

			<fieldset style="background-color: #D2D2D8; padding: 0px; margin-bottom:4px;">

				<legend>
					Choose File 
				</legend>
				<iframe name="idir" id="idir" frameborder="0" style="border : 0px; width: 100%; height: 400px;" src="about:blank" scrolling="no"> </iframe>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td width="276" align="right" valign="top">
			<button type="button" name="ok" onclick="return onOK();">
				OK 
			</button>
			&nbsp;&nbsp;<button type="button" name="cancel" onclick="return onCancel();">
				Cancel 
			</button>
			&nbsp;&nbsp;&nbsp; 
		</td>
	</tr>
</table>
</body>
</html>

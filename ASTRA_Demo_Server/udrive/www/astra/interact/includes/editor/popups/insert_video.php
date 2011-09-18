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

$file_dir_iframe_url="file_dir_iframe.php?allowed_file_types=swf,zip&maxSize=1000:12000:30000";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Insert Video</title> 
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

<script type="text/javascript" src="popup.js"></script>

<script type="text/javascript">
self.focus();
var pre_image;

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
  
  document.getElementById('f_url').focus();
  
  if (param) {
  	var vtitle_ind=param['f_url'].indexOf('&vtitle')
document.getElementById('f_url').value=param['f_url'].substring(param['f_url'].indexOf('medianame=')+10,vtitle_ind);
	document.getElementById('f_vtitle').value=param['f_url'].substring(vtitle_ind+8,param['f_url'].lastIndexOf('XXVIDSTREAMXX'));
	showExisting();
  	document.getElementById('f_alt').value=param['f_alt'];
  }
  
  document.getElementById('idir').src="<?php echo $file_dir_iframe_url;?>&trytofindurl="+document.getElementById('f_url').value;
}

function NewFile(url,nclicks) {
	if (nclicks&1) {
		document.getElementById("f_url").value = url.replace(/_[0-9]*k.swf/i,'');
		document.getElementById("f_alt").value = "Flash Video";
		window.ipreview.location.replace('insert_media_preview.php?media_placeholder='+escape('<img src='+media_placeholder_url()+'"/>'));
		document.getElementById("f_vtitle").focus();
	}
}
	
function showExisting()
	{
	NewFile(document.getElementById('f_url').value,3);
}


function onOK() {
  var required = {
    "f_url": "You must enter the URL"
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
  var param = new Object();
  param["f_url"] = media_placeholder_url();
param["f_alt"] = document.getElementById("f_alt").value;
param["f_width"] = 185;
param["f_height"] = 159;

  __dlg_close(param);
  return false;
};

function media_placeholder_url() {
	return '<?php echo $CONFIG['PATH']?>/includes/editor/images/VIDSTREAM_placeholder.gif?XXVIDSTREAMXXfixed:370:317:medianame='+document.getElementById("f_url").value+'&vtitle='+document.getElementById("f_vtitle").value+'XXVIDSTREAMXX';
}

function onCancel() {
  __dlg_close(null);
  return false;
};


</script>
</head>


<body onload="Init();">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="394" rowspan="2" align=center valign="top">
			<fieldset style="height:342px; background-color: #F6F6F6; padding: 0px;">
				<legend>
					Preview 
				</legend>
				<iframe name="ipreview" id="ipreview" frameborder="0" style="border : 0px; width: 100%; height: 332px;" src="about:blank" scrolling="no"> </iframe>
			</fieldset>
			<div align="center" id="premess">
				<br />
			</div>
			<form action="" method="get">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" align="right" style="padding-right:20px">
										Video URL:
										<input name="url" type="text" id="f_url" title="Video URL (does not include suffix)" size="42"> <br /><br />
										Title:
										<input name="vtitle" type="text" id="f_vtitle" title="Video Title" size="42" onchange="showExisting();"> <br /><br />
<div align="left" class="small" style="float:left">Note: At present, 3 .swf files are required:<br />filename_40k.swf, filename_105k.swf and filename_400k.swf</div>
										<input name="alt" type="hidden" id="f_alt" size="40"> <br /><br />
						</td>
					</tr>
				</table>
			</form>
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

<?php
require_once('../../../local/config.inc.php');

$file_dir_iframe_url="file_dir_iframe.php?allowed_file_types=jpeg,jpg,gif,png,zip";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Choose Image</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

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

var img_params=["f_url","f_width","f_height"<?php if($_GET['basic']!=true) {echo ',"f_alt","f_border","f_align","f_vert","f_horiz"';} ?>];

function dInit() {
  __dlg_init();
  var param = window.dialogArguments;
  document.getElementById("f_url").focus();
  if (param) {
  	for (var i in img_params) {
	  if (param[img_params[i]]!=undefined) {
      	document.getElementById(img_params[i]).value = param[img_params[i]];
	  }
	}
	showExisting(param["f_url"]);
  }
  
  document.getElementById("idir").src="<?php echo $file_dir_iframe_url;?>&trytofindurl="+document.getElementById("f_url").value;
}

function NewFile(url,nclicks) {
	if (nclicks&1) {
		var pre_image = document.getElementById("pre_image");
		var ipreview=document.getElementById("ipreview");
		ipreview.alt="Loading...";
		ipreview.src=null;
		pre_image.onload=newUpdate;	
		pre_image.src=url;
		document.getElementById("f_url").value=url;
	}
}

function oldUpdate() {
	imgUpdate();
	<?php if($_GET['basic']!=true) echo 'checkSize();';?>
}

function newUpdate() {
	imgUpdate();
	resetSize();
}

function showExisting(url) {
	var pre_image = document.getElementById("pre_image");
	pre_image.onload=oldUpdate;
	pre_image.src=url;
}


function imgUpdate() {
	var ipreview = document.getElementById("ipreview");
	var pre_image = document.getElementById("pre_image");

	ipreview.alt="Loaded";

	var nwidth  = pre_image.width;
	var nheight = pre_image.height;
	var factor;
	
	if (nwidth>0) {
	var premess=nwidth+"x"+nheight+" pixels";
	if (pre_image.fileSize) premess+=" &bull; "+Math.ceil(pre_image.fileSize>>10)+"KB ("+Math.ceil(pre_image.fileSize/5000)+"s@56K)";
	if ((factor=Math.min(228/nheight,304/nwidth))<1) {
       	nwidth=Math.round(nwidth*factor);
	    nheight=Math.round(nheight*factor);
		premess+=" &bull; shown at "+Math.round(factor*100)+"%";
	}

	premess+="<br />";} else premess="";
	
	document.getElementById("premess").innerHTML=premess;
	ipreview.src=pre_image.src;
	ipreview.width=nwidth;ipreview.height=nheight;

	<?php if($_GET['basic']!=true) echo 'document.getElementById("f_alt").focus();'; ?>	
}

function checkSize() {
	var pre_image = document.getElementById("pre_image");
	if (document.getElementById("f_width").value!=pre_image.width) 
		document.getElementById("wtit").style.fontWeight="bold";
		else document.getElementById("wtit").style.fontWeight="";
	if (document.getElementById("f_height").value!=pre_image.height) 
		document.getElementById("htit").style.fontWeight="bold";
		else document.getElementById("htit").style.fontWeight="";
}

// Fired when the Reset Size button is clicked
function resetSize() {
	var pre_image = document.getElementById("pre_image");
	if (pre_image.width>0) {
		document.getElementById("f_width").value=pre_image.width;
		document.getElementById("f_height").value=pre_image.height;
	}
	<?php if($_GET['basic']!=true) echo 'checkSize();'; ?>
}

// Fired when the width or height input texts change
function sizeChanged(axe) {
	// Verifies if the aspect ration has to be mantained
	var pre_image = document.getElementById("pre_image");
	if (document.getElementById("chkLockRatio").checked && (pre_image.width>0)) {

		txtWidth = document.getElementById("f_width");
		txtHeight = document.getElementById("f_height");

		if ((axe) == "Width") {
			if (txtWidth.value != "") {
				if (! isNaN(txtWidth.value))
					txtHeight.value = Math.round( pre_image.height * ( txtWidth.value  / pre_image.width ) ) ;
			}
			else txtHeight.value = "" ;
		} else
			if (txtHeight.value != "") {
				if (! isNaN(txtHeight.value))
					txtWidth.value  = Math.round( pre_image.width  * ( txtHeight.value / pre_image.height ) ) ;
			} else txtWidth.value = "" ;
	}
	checkSize();
}

function onOK() {
  var required = {
    "f_url": "You must enter the URL"
    <?php if($_GET['basic']!=true) echo ',"f_alt": "Please enter alternate text - this helps people who can\'t see images"'; ?>
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
  for (var i in img_params) {
    var id = img_params[i];
    var el = document.getElementById(id);
//    if(el.value.length) 
    param[id] = el.value;
  }
  __dlg_close(param);
  return false;
};

</script>

<style>
	HTML, BODY
		{
		FONT-FAMILY: Verdana;FONT-SIZE: xx-small;
		background: #E0E0E4;
  		color: ButtonText;
  		margin: 0px;
  		padding: 0px;
  		overflow:hidden;
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


<body onload="dInit();">
<div style="position:absolute; visibility: hidden;">
	<img id="pre_image">
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="54%" rowspan="2" align="center" valign=<?php echo ($_GET['basic']==true)? '"middle"':'"top"'; ?> >
			<fieldset style="height:247px; background-color: #F6F6F6; padding: 0px;">
				<legend>
					Preview 
				</legend>
				<table height="228" border="0" style="padding: 0px; margin: 0px">
					<tr>
						<td valign="middle" align="center">
							<img name="ipreview" align="middle" id="ipreview" alt="no image"> 
						</td>
					</tr>
				</table>
			</fieldset><input type="button" style="font-size:xx-small;float:right;padding:0px;margin:0px;" onClick="NewFile(document.getElementById('f_url').value,1)" value="Update">
			<div align="center" id="premess">
				<br />
			</div>
			<form action="" method="get">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:3px;">
					<tr>
						<td valign="top">
							<table border="0" width="100%" style="padding: 0px; margin: 0px">
								<tr>
									<td style="width: 20%; text-align: right">
										Image URL: 
									</td>
									<td valign="top">
										<input name="url" type="text" id="f_url" title="Enter the image URL here" size="42"> 
									</td>
								</tr>
								<tr>
								<?php if($_GET['basic']!=true) { ?>
									<td style="color:red; width: 20%; text-align: right">
										Alt text: 
									</td>
									<td valign="top">
										<input name="alt" type="text" id="f_alt" title="For browsers that don\'t support images" size="42"> 
									</td> 
								<?php } ?>
								</tr>
								
							</table>
							
							<?php if($_GET['basic']==true) {
								echo '<input type="hidden" id="f_width"><input type="hidden" id="f_height"> ';
							} else { ?>

							<table align="center">
								<tr>
									<td>
										<fieldset style="margin-left:25px;padding-top:1px; padding-right:2px; padding-bottom:1px;">
											<legend>
												Alignment 
											</legend>
											<select size="1" name="align" id="f_align" title="Positioning of this image"> 
												<option value="" selected="1">
													Not set 
												</option>
												<option value="left">
													Left 
												</option>
												<option value="right">
													Right 
												</option>
												<option value="texttop">
													Texttop 
												</option>
												<option value="absmiddle">
													Absmiddle 
												</option>
												<option value="baseline">
													Baseline 
												</option>
												<option value="absbottom">
													Absbottom 
												</option>
												<option value="bottom">
													Bottom 
												</option>
												<option value="middle">
													Middle 
												</option>
												<option value="top">
													Top 
												</option>
											</select> 
											<br />
											<table>
												<tr>
													<td nowrap align="right">
														Border thickness: 
													</td>
													<td>
														<input type="text" name="border" id="f_border" size="3" title="Leave empty for no border" value="1"/> 
													</td>
												</tr>
												<tr>
													<td nowrap align="right">
														Horizontal Space: 
													</td>
													<td>
														<input type="text" name="horiz" id="f_horiz" size="3" title="Horizontal padding - gap on left and right of image" /> 
													</td>
												</tr>
												<tr>
													<td nowrap align="right">
														Vertical Space: 
													</td>
													<td>
														<input type="text" name="vert" id="f_vert" size="3" title="Vertical padding - gap on top and bottom of image"/> 
													</td>
												</tr>
											</table>
										</fieldset>
									</td>
									<td valign="top" align="center">
										<fieldset style="margin-left: 12px; padding-bottom:2px;padding-top:0px;padding-right:2px;">
											<legend>
												Size 
											</legend>
											<table>
												<tr>
													<td nowrap align="right" id="wtit">
														Width: 
													</td>
													<td width="10">
														<input type="text" size="4" id="f_width" title="Display Width (does not affect actual image file)" onkeyup="sizeChanged('Width');"> 
													</td>
												</tr>
												<tr>
													<td nowrap align="right" id="htit">
														Height: 
													</td>
													<td>
														<input type="text" size="4" id="f_height" title="Display Height (does not affect actual image file)" onkeyup="sizeChanged('Height');"> 
													</td>
												</tr>
												<tr>
													<td nowrap colspan="2" valign="top">
														<INPUT type="checkbox" class="CheckBox" checked id="chkLockRatio" title="When changing size above, keep aspect ratio of image correct (don't squish it)"> Lock Ratio 
													</td>
												</tr>
											</table>
											<INPUT type="button" value="Reset Size" title="Reset width and height to that of the image file" onclick="resetSize();"> 
										</fieldset>
									</td>
								</tr>
							</table>
							<?php } ?>
							
						</td>
					</tr>
				</table>
			</form>
		</td>
		<td width="46%" valign="top">

			<fieldset style="background-color: #D2D2D8; padding: 0px; margin-bottom:4px;">

				<legend>
					Choose File 
				</legend>
				<iframe name="idir" id="idir" frameborder="0" style="border : 0px; width: 100%; height: 400px;" src="about:blank" scrolling="no"> </iframe>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td width="100%" align="right" valign="top">
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

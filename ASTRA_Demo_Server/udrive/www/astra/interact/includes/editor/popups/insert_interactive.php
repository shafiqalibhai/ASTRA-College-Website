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
?>

<html>

<head>
<title>Insert Interactive</title>

<script type="text/javascript">
var urlstring="";
var interactive="";
var iscaling="";
var fileuploadtype="*";
var pathtofile="";
<?php
echo 'var includesfolder="'.$CONFIG['PATH'].'/includes";';
?>

var winIE=0;var windowsBrowser=0;
// Hook for Internet Explorer.
if (navigator.userAgent.indexOf("Windows") != -1) {
	if (navigator.appname && navigator.appname.indexOf("Microsoft") != -1) {
		winIE=1;  // Windows IE - will use FSCommand ActiveX for communication.
	}
	windowsBrowser=1;  // Just used for popup sizing.
}

function Interactives_DoFSCommand(retOK,evalstuff) {
	eval(evalstuff);
//	alert(retOK+":"+evalstuff);
	if (retOK=="OK") {
		myurl= includesfolder+'/editor/images/'+interactive+'_placeholder.gif?XX'+interactive+'XX'+iscaling+":"+urlstring+'XX'+interactive+'XX';

		var param = new Object();

		param["f_url"] = myurl;
		param["f_alt"] = interactive+" interactive element";

		var isplit=iscaling.split(":");
		param["f_width"]=isplit[1];
	  	param["f_height"]=isplit[2];

		__dlg_close(param);
	} else {updateForm();}
}

function getAbsolutePos(el) {
	var r = { x: el.offsetLeft, y: el.offsetTop };
	if (el.offsetParent) {
		var tmp = getAbsolutePos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
};


function __dlg_onclose() {
	opener.Dialog._return(null);
};

function __dlg_init(bottom) {
	var body = document.body;
	var body_height = 0;
	if (typeof bottom == "undefined") {
		var div = document.createElement("div");
		body.appendChild(div);
		var pos = getAbsolutePos(div);
		body_height = pos.y;
	} else {
		var pos = getAbsolutePos(bottom);
		body_height = pos.y + bottom.offsetHeight;
	}
	window.dialogArguments = opener.Dialog._arguments;
	window.resizeTo(windowsBrowser*12+700, windowsBrowser*25+502+22+20);
	self.focus()
	document.body.onkeypress = __dlg_close_on_esc;
};


// closes the dialog and passes the return info upper.
function __dlg_close(val) {
	opener.Dialog._return(val);
	window.close();
};

function __dlg_close_on_esc(ev) {
	ev || (ev = window.event);
	if (ev.keyCode == 27) {
		window.close();
		return false;
	}
	return true;
};


function onCancel() {
  __dlg_close(null);
  return false;
};


function Init() {
  __dlg_init();
//	preload('insert_interactive_progress.gif');
	interactive="";
	urlstring="";

var param = window.dialogArguments;
  if (param) {
 // alert("init string:"+param["f_url"]);
      urlstring = param["f_url"];
 /*     document.getElementById("f_alt").value = param["f_alt"];
      document.getElementById("f_border").value = param["f_border"];
      document.getElementById("f_align").value = param["f_align"];
      document.getElementById("f_vert").value = param["f_vert"];
      document.getElementById("f_horiz").value = param["f_horiz"];
	  document.getElementById("f_width").value = param["f_width"];
	  document.getElementById("f_height").value = param["f_height"];
*/
	if (urlstring.length>0) {
	  interactive=urlstring.replace(/...+(XX[^X]...+XX)$/,"$1");
	  var iind;
	  var urlall= (urlstring.split(interactive)[1]);
	  iscaling=urlall.substring(0,(iind=urlall.indexOf(":")+1))+urlall.substring(iind,(iind=urlall.indexOf(":",iind)+1)).replace(/%/,"p")+urlall.substring(iind,(iind=urlall.indexOf(":",iind)) ).replace(/%/,"p");
	  urlstring=urlall.substr(iind+1)//urlall[3];
	  interactive=interactive.replace(/^XX(...+)XX$/,"$1");
	}
//	  alert("inter:"+interactive);
//	  alert("urlstring:"+urlstring);
  }
  //document.getElementById("f_url").focus;
};

function doDisableForm(msg) {
document.getElementById("topmsg").innerHTML = msg;
document.getElementById("disableForm").style.visibility="visible";
}
function doEnableForm() {
//document.forms.upload.reset();  WinIE isn't fond of this
document.getElementById("disableForm").style.visibility="hidden";
document.getElementById("topmsg").innerHTML = "&nbsp;";
}

function settitle(ftype) {
//interactive=inter;
//document.upload.interactive.value = interactive;
document.getElementById("topmsg").innerHTML = "&nbsp;";
if (ftype!="") {fileuploadtype=ftype;doEnableForm();}
}

function evalstring(evalstuff) {
eval(evalstuff);
}

function updateForm() {
	document.upload.interactive.value = interactive;
	document.upload.urlstring.value = urlstring;
	document.upload.pathtofile.value=pathtofile;
	document.upload.iscaling.value = iscaling;
}

function changeme() {
updateForm();
//alert("uploadtype:"+fileuploadtype+" fileind:"+document.upload.file.value.lastIndexOf(fileuploadtype));
	if (fileuploadtype=='*' || 		document.upload.file.value.lastIndexOf(fileuploadtype)==document.upload.file.value.length-fileuploadtype.length) { // || document.upload.file.value.substr(document.upload.file.value.length-4,4)==".zip") {
		doDisableForm('Processing... <img src="insert_interactive_progress.gif"  	align="absbottom" width="96" height="21" border="0">&nbsp;&nbsp;&nbsp;');
		document.upload.submit();
	} else {alert("Sorry, but your file must be of type "+fileuploadtype+"\nfor the "+interactive+" interactive.")} //or a .zip archive 
}

//function preload(im) {
//var xx=new Image; xx.src=im;
//}

</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #DDDDFF;
	font-family: Arial, Helvetica, sans-serif;
	color: #000000;
}
-->
</style>
</head>

<body>
<script type="text/javascript">
Init();
</script>
<form name="upload" id="upload" action="insert_interactive_iframe.php" method="post" enctype="multipart/form-data" target="IntFlashFrame"><div id="disableForm" style="position:absolute; width:100%; height:22px; z-index:15; visibility: visible;">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td valign="top" align="right" height="24" width="100%" bgcolor="#DDDDFF" ><div ID="topmsg" valign="top">&nbsp</div>
		</td>
	  </tr>
	</table>
</div><div align="right" valign="top">
<input type="hidden" name="winIE" value=0>
<input type="hidden" name="interactive" value="">
<input type="hidden" name="urlstring" value="">
<!--fc<input type="text" name="flashcommand" value="">-->
<input type="hidden" name="pathtofile" value="">
<input type="hidden" name="iscaling" value="">
Upload a file: <input name="file" type="file" title="Use this to upload a file" size="11" onChange="changeme();"></div>
<iframe name="IntFlashFrame" width="700" height="502"  marginwidth="0" marginheight="0" frameborder="no" scrolling="no">Sorry, you need inline frames to use this page.</iframe>
</form>
<!--
Testing: <input name="jsubmit" value="jsubmit" type="button" onClick="doDisableForm('&nbsp;&nbsp;&nbsp;Testing reload... <img src=&quot;insert_interactive_progress.gif&quot;  align=&quot;absbottom&quot; width=&quot;96&quot; height=&quot;21&quot; border=&quot;0&quot;>'); document.upload.submit();">
<input name="enable" value="Show Form" type="button" onClick="doEnableForm();">
<input name="disable" value="Show Processing" type="button" onClick="doDisableForm('&nbsp;&nbsp;&nbsp;Processing... <img src=&quot;insert_interactive_progress.gif&quot;  align=&quot;absbottom&quot; width=&quot;96&quot; height=&quot;21&quot; border=&quot;0&quot;>&nbsp;');">
<input name="disable" value="Show hidden fields (Moz only)" type="button" onClick='	document.upload.interactive.type = "text";
	document.upload.urlstring.type = "text";
	document.upload.pathtofile.type = "text";
	document.upload.iscaling.type = "text";'>
-->
<script type="text/javascript">
document.upload.winIE.value = winIE;
doDisableForm('<table width="100%" border="0" cellspacing="0" cellpadding="0"><td width="100%" valign="top" align="center">Loading...&nbsp;<img src="insert_interactive_progress.gif"  align="absbottom" width="96" height="21" border="0"></td></table>');
updateForm();
document.upload.submit();
</script>

</body>
</html>

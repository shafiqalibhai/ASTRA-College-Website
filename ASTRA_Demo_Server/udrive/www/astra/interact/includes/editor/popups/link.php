<html>

<head>
  <title>Insert/Modify link</title>

<script type="text/javascript">

<?php readfile('popup'.(!empty($_GET['type'])?'_'.$_GET['type']:'').'.js')?>
  
self.focus();

function showPopupSettings() {
	if(document.getElementById('f_targetpopup').checked) {
		document.getElementById('popupSettings').style['display']='';
		document.getElementById('othername').style['display']='';
		document.getElementById('f_pwidth').focus();
		document.getElementById('f_pwidth').select();
	} else {
		document.getElementById('popupSettings').style['display']='none';
		if(document.getElementById('f_targetother').checked) {
			document.getElementById('othername').style['display']='';
			document.getElementById('f_target_other').focus();
			document.getElementById('f_target_other').select();
		} else {
			document.getElementById('othername').style['display']='none';
		}
	}
}

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
  var fhrefbox=document.getElementById("f_href");
  var fpwidth=document.getElementById("f_pwidth");
  var fpheight=document.getElementById("f_pheight");
  if (param) {
	  if(param["f_href"].substring(0,11)=="javascript:") {
	  	fhrefbox.value = param["f_href"].match(/^[^']+'([^']+).+$/)[1];
		popup_params = param["f_href"].match(/'[^']*'/gim);
		fpwidth.value = popup_params[3].replace(/'/g,"");
		fpheight.value = popup_params[2].replace(/'/g,"");
		document.getElementById("f_targetpopup").checked=true;
		document.getElementById("f_target_other").value=popup_params[1].replace(/'/g,"");
	  } else {
	  	fhrefbox.value = param["f_href"];
	  	if(param["f_target"]=='_blank') {
	  	  	document.getElementById("f_target_blank").checked=true;
	  	  } else {
	  	  	if(param["f_target"]!='') {
	  	  		document.getElementById("f_targetother").checked=true;
	  	  		document.getElementById("f_target_other").value=param["f_target"];
	  	  	}
	  	  }
	  }
      document.getElementById("f_title").value = param["f_title"];

		showPopupSettings();
  } else {
  	fhrefbox.value = "http://";
	document.getElementById("remove").style.display="none";
  }

  fhrefbox.focus();
document.getElementById("idir").src="file_dir_iframe.php?trytofindurl="+document.getElementById("f_href").value;

};


function NewFile(url,nclicks) {
	if (nclicks&1) {
		document.getElementById("f_href").value=url;
	}
}

function onOK() {
    var el = document.getElementById("f_href");

	if (!el.value || el.value=="http://") {
		alert("You must enter the URL where this link points to");
		return false;
    } else {
		return justRet();}
 }


function justRet() {
 // pass data back to the calling window
  var fields = ["f_href", "f_title"];
  var param = new Object();
  
  param['f_target']=(document.getElementById('f_target_blank').checked?'_blank':(!document.getElementById('f_target').checked?document.getElementById('f_target_other').value:''));

  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }

  if (param.f_href && document.getElementById("f_targetpopup").checked==true) {
  	param.f_href = "javascript:open_window('"+param.f_href+"','"+document.getElementById('f_target_other').value+"','"+
  		document.getElementById('f_pheight').value+"','"+
  		document.getElementById('f_pwidth').value+"')";
	param.f_target='';
  }  
  __dlg_close(param);
  return false;
}

function killIt() {
	document.getElementById("f_href").value="";
	return justRet();
}

function onCancel() {
  __dlg_close(null);
  return false;
};


</script>

<style type="text/css">
	HTML, BODY
		{
		FONT-FAMILY: Verdana;
		background: #E0E0E4;
  		color: ButtonText;
  		margin: 0px;
  		padding: 0px;
		}
	TABLE
		{
	    FONT-SIZE: x-small;
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

	button { width: 70px; }

	form { padding: 0px; margin: 0px; }

	.label{white-space:nowrap;width:10%;text-align:right;}

</style>

</head>

<body onLoad="Init();">
<!-- <div class="title">Insert/Modify link</div> -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="58%" valign="top">
<fieldset style="background-color: #D2D2D8; padding: 0px; margin-right:20px;">
<legend>URL of link:</legend>
<table border="0" style="width: 100%;">
  <tr>
    <td class="label"><strong>Enter URL</strong>:</td>
    <td><input type="text" id="f_href" style="width: 99%" /></td>
  </tr>
</table></fieldset>
<br /><br />

			<fieldset style="background-color: #D2D2D8; padding: 0px;margin-right:20px;">
<legend><strong>OR</strong> Select content to link to:</legend>
<table border="0" style="width: 100%;">
  <tr>
    <td class="label">My Posts:</td>
    <td>
<select id="f_post_href" onChange="document.getElementById('f_href').value=this.value;" style="width: 99%;">
     <option value=""></option>
	  <?php
	  require_once('../../../local/config.inc.php');
	  $rs = $CONN->Execute("SELECT subject, post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE  {$CONFIG['DB_PREFIX']}posts.added_by_key='{$_SESSION['current_user_key']}'");
	 
	  while (!$rs->EOF) {
	  	echo '<option value="'.$CONFIG['PATH'].$CONFIG['DIRECT_PATH'].'post/'.$_SESSION['current_space_key'].'/'.$rs->fields[1].'">'.substr($rs->fields[0],0,20).' ('.$rs->fields[1].')</option>';
	  	$rs->MoveNext();
	  }
	?>
    </select>
	</td></tr>
  <tr>
    <td class="label">Components in this space:</td>
    <td>
<select id="f_module_href" onChange="document.getElementById('f_href').value=this.value;" style="width: 100%;">
     <option value=""></option>
	  <?php
	  $rs = $CONN->Execute("SELECT name, {$CONFIG['DB_PREFIX']}modules.module_key,space_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE type_code!='heading' AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='{$_SESSION['current_space_key']}'");
	 
	  while (!$rs->EOF) {
	  	echo '<option value="'.$CONFIG['PATH'].$CONFIG['DIRECT_PATH'].$rs->fields[2].'/'.$rs->fields[1].'">'.substr($rs->fields[0],0,20).' ('.$rs->fields[1].')</option>';
	  	$rs->MoveNext();
	  }
	?>
    </select>
	</td></tr></table></fieldset><br /><br /><br />
	
	<fieldset style="background-color: #D2D2D8; padding: 0px;margin-right:40px;margin-left:20px;">
<legend>details:</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

	  <tr>
    <td class="label">Title (tooltip):</td>
    <td><input type="text" id="f_title" style="width: 99%" /></td>
  </tr>
  <tr>
    <td class="label" valign="top" ><br />target:</td>
    <td align="left"><br />
    <input name="f_target" id="f_target" type="radio" value="" onClick="showPopupSettings()"checked>None (Implicit)<br />
    <input name="f_target" id="f_target_blank" type="radio" value="_blank" onClick="showPopupSettings()">New Window (_blank)<br />
    <input name="f_target" id="f_targetother" type="radio" value="other" onClick="showPopupSettings()">Other Window&hellip;<br />
    <input name="f_target" id="f_targetpopup" type="radio" value="popup" onClick="showPopupSettings()">Popup Window&hellip;<br />
 <div id="othername" style="display:none;margin-left:2.2em;margin-top:2px">Window name:<input type="text" name="f_target_other" id="f_target_other" size="10" value=""/>
</div>    
	<div id="popupSettings" style="display:none;margin-left:2.2em;margin-top:2px">
	width:<input type="text" id="f_pwidth"  size="3" value="400" /> 	height:<input type="text" id="f_pheight" size="3" value="300" />
	
	</div>
    </td>
  </tr>
	</table></fieldset>
	
	<br /><br />
	<table width="100%"><tr><td width="50%">
			<button type="button" size="20" id="remove" onClick="return killIt();">Remove link</button></td><td align="left">
  <button type="button" name="ok" onClick="return onOK();">OK</button>&nbsp;&nbsp;
  <button type="button" name="cancel" onClick="return onCancel();">Cancel</button>
			&nbsp;&nbsp;&nbsp; </td></tr></table>
	
			</td>
		<td width="280px" valign="top">

			<fieldset style="background-color: #D2D2D8; padding: 0px;">

				<legend>
					<strong>OR</strong> Choose File 
				</legend>
				<iframe name="idir" id="idir" frameborder="0" style="border : 0px; width: 100%; height: 400px;" src="about:blank" scrolling="no"> </iframe>
			</fieldset>
		</td>
	</tr>
</table>


</body>
</html>

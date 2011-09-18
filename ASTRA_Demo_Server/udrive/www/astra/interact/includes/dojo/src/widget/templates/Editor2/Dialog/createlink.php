<?php
require_once('../../../../../../../local/config.inc.php');
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:x-small">
	<tr>
		<td width="58%" valign="top">

<fieldset style="background-color: #D2D2D8; padding: 0px; margin-right:20px;">
<legend>URL of link:</legend>
<table border="0" style="width: 100%;">
  <tr>
    <td class="label"><strong>Enter URL</strong>:</td>
    <td><input type="text" dojoAttachPoint="link_href" dojoAttachEvent="onKeyUp:checkok" style="width: 240px" /></td>
  </tr>
</table></fieldset>
<br /><br />

			<fieldset style="background-color: #D2D2D8; padding: 0px;margin-right:20px;">
<legend><strong>OR</strong> Select content to link to:</legend>
<table border="0" style="width: 100%;">

<?php	  
	  if($_SESSION['current_user_key']) {
	echo '  <tr>
    <td align="right">My Posts:</td>
    <td>
<select dojoAttachPoint="link_post_href" dojoAttachEvent="onChange:setURLpost" style="width:150px">
     <option value=""></option>';
     
	  $rs = $CONN->Execute("SELECT subject, post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key='{$_SESSION['current_user_key']}'");
	 
		  while (!$rs->EOF) {
			echo '<option value="'.$CONFIG['PATH'].$CONFIG['DIRECT_PATH'].'post/'.$_SESSION['current_space_key'].'/'.$rs->fields[1].'">'.substr($rs->fields[0],0,20).' ('.$rs->fields[1].')</option>';
			$rs->MoveNext();
		  }
		  echo '
    </select>
	</td></tr>
';
	 }
?>

  <tr>
    <td align="right">Components in this space:</td>
    <td>
<select dojoAttachPoint="link_module_href" dojoAttachEvent="onChange:setURLmodule" >
     <option value=""></option>
	  <?php

	  $rs = $CONN->Execute("SELECT name, {$CONFIG['DB_PREFIX']}modules.module_key,space_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links WHERE type_code!='heading' AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='{$_SESSION['current_space_key']}'");

	 
	  while (!$rs->EOF) {
	  	echo '<option value="'.$CONFIG['PATH'].$CONFIG['DIRECT_PATH'].$rs->fields[2].'/'.$rs->fields[1].'">'.substr($rs->fields[0],0,20).' ('.$rs->fields[1].')</option>';
	  	$rs->MoveNext();
	  }
	?>
    </select>
	</td></tr></table></fieldset><br /><br />
	
	<fieldset style="background-color: #D2D2D8; padding: 0px;margin-right:40px;margin-left:20px;">
<legend>Details (optional):</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

	  <tr>
    <td class="label">Name:</td>
    <td>&nbsp;<input type="text" dojoAttachPoint="link_name" id="link_name" dojoAttachEvent="onKeyUp:checkok" size="25" /> (anchor)</td>
  </tr>
	  <tr>
    <td class="label">Title:</td>
    <td>&nbsp;<input type="text" dojoAttachPoint="link_title" id="link_title" size="25" /> (tooltip)</td>
  </tr>
  <tr>
    <td class="label" valign="top" ><br />Target:</td>
    <td align="left"><br />
    <input name="link_target" dojoAttachPoint="link_target_none" type="radio" value="" dojoAttachEvent='onClick:showPopupSettings' checked>None (Implicit)<br />
    <input name="link_target" dojoAttachPoint="link_target_blank" type="radio" value="_blank" dojoAttachEvent='onClick:showPopupSettings'>New Window (_blank)<br />
    <input name="link_target" dojoAttachPoint="link_target_other" type="radio" value="other" dojoAttachEvent='onClick:showPopupSettings'>Other Window&hellip;<br />
    <input name="link_target" dojoAttachPoint="link_target_popup" type="radio" value="popup" dojoAttachEvent='onClick:showPopupSettings'>Popup Window&hellip;<br />
 <div dojoAttachPoint="link_target_div" style="display:none;margin-left:2.2em;margin-top:2px">Window name:<input type="text" dojoAttachPoint="link_target" size="10" value=""/>
</div>    
	<div dojoAttachPoint="link_popupSettings" id="link_popupSettings" style="display:none;margin-left:2.2em;margin-top:2px">
	width:<input type="text" id="link_pwidth"  size="3" value="400" /> 	height:<input type="text" id="link_pheight" size="3" value="300" />
	
	</div>
    </td>
  </tr>
	</table></fieldset>
	
	<br />
		<table><tr>
		<td style="padding-right:60px">
			<button dojoType="button" dojoAttachPoint="link_remove" dojoAttachEvent="onClick:removeLink">Remove Link</button></td>
		<td style="padding-right:20px">
			<button dojoType='Button' dojoAttachPoint="link_ok" dojoAttachEvent='onClick:ok'>OK</button>
			</td><td>
			<button dojoType='Button' dojoAttachEvent='onClick:cancel'>Cancel</button>
		</td></tr></table>
	
			</td>
		<td width="280px" valign="top">

			<fieldset style="background-color: #D2D2D8; padding: 0px;">

				<legend>
					<strong>OR</strong> Choose File 
				</legend>
				<iframe dojoAttachPoint="idir" name="idir" frameborder="0" style="border : 0px; width: 100%; height: 400px;" src="about:blank" scrolling="no"> </iframe>
			</fieldset>
		</td>
	</tr>
</table>
<?php

require_once('../../../local/config.inc.php');
?>
<html>

<head>
  <title>Insert Image</title>
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

//window.resizeTo(450, 100);
self.focus()

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
 
};

function onOK(url) {
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
function seturl(url){
    var fv = document.getElementById("f_url");
    fv.value=url;
};

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

<body onload="Init()">

  <form id="form">
 
<input type="hidden" id="f_url" value="">
<input type="hidden" id="f_alt" value="smiley icon">
<input type="hidden" id="f_align" value="">
<input type="hidden" id="f_border" value="0">
<input type="hidden" id="f_horiz" value="">		
<input type="hidden" id="f_vert" value="">
<input type="hidden" id="f_width" value="19">
<input type="hidden" id="f_height" value="19">

<div align="center">
    <table  border="0" cellspacing="0" cellpadding="8">
        <tr>
            <td><div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/angel_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/angel_smile.gif');"><img src="<?php echo $CONFIG['PATH']?>/images/smileys/angel_smile.gif" width="19" height="19"></div></td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/angry_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/angry_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/angry_smile.gif" width="19" height="19"></div></td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/broken_heart.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/broken_heart.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/broken_heart.gif" width="19" height="19"></div>
			</td>
            <td>
						<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/cake.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/cake.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/cake.gif" width="19" height="19">
			</div>
			</td>
        </tr>
        <tr>
            <td>
			
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/cry_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/cry_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/cry_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/devil_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/devil_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/devil_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/embaressed_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/embaressed_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/embaressed_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/heart.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/heart.gif');">
			
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/heart.gif" width="19" height="19">
			</div>
			</td>
        </tr>
        <tr>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/confused_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/confused_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/confused_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/lightbulb.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/lightbulb.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/lightbulb.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/omg_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/omg_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/omg_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/regular_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/regular_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/regular_smile.gif" width="19" height="19">
			</div>
			</td>
        </tr>
        <tr>
            <td>
			
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/shades_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/shades_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/shades_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/teeth_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/teeth_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/teeth_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/thumbs_down.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/thumbs_down.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/thumbs_down.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/thumbs_up.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/thumbs_up.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/thumbs_up.gif" width="19" height="19">
			</div></td>
        </tr>
        <tr>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/whatchutalkingabout_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/whatchutalkingabout_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/whatchutalkingabout_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/wink_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/wink_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/wink_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/tounge_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/tounge_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/tounge_smile.gif" width="19" height="19">
			</div>
			</td>
            <td>
			<div onclick="seturl('<?php echo $CONFIG['PATH']?>/images/smileys/sad_smile.gif');onOK('<?php echo $CONFIG['PATH']?>/images/smileys/sad_smile.gif');">
			<img src="<?php echo $CONFIG['PATH']?>/images/smileys/sad_smile.gif" width="19" height="19">
			</div>
			</td>
        </tr>
    </table>
<input type="submit" name="cancel" value="Cancel" onclick="return onCancel();">
    </div>
</form>

</body>
</html>

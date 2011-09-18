<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003 Frederico Caldeira Knabben
 *
 * Licensed under the terms of the GNU Lesser General Public License
 * (http://www.opensource.org/licenses/lgpl-license.php)
 *
 * For further information go to http://www.fredck.com/FCKeditor/ 
 * or contact fckeditor@fredck.com.
 *
 * fck_specialchar.html: Special characters chooser dialog box.
 *
 * Authors:
 *   Frederico Caldeira Knabben (fckeditor@fredck.com)
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<HTML>
	<HEAD>
		<TITLE>Insert Special Character</TITLE>
		<LINK rel="stylesheet" type="text/css" href="../css/fck_dialog.css">
			<style type="text/css">
				.Disactive { border-right: 1px solid; border-top: 1px solid; border-left: 1px solid; width: 1%; cursor: hand; border-bottom: 1px solid; background-color: #dedbd6; text-align: center; }
				.Active { cursor: hand; background-color: #ffffcc; text-align: center; }
				.MainTable { border-right: #e8e8e8 5px solid; border-top: #e8e8e8 5px solid; border-left: #e8e8e8 5px solid; border-bottom: #e8e8e8 5px solid; }
				.Sample { border-right: 1px solid; border-top: 1px solid; font-size: 24px; border-left: 1px solid; border-bottom: 1px solid; background-color: #dedbd6; }
				.Empty { border-right: 1px solid; border-top: 1px solid; border-left: 1px solid; width: 1%; cursor: default; border-bottom: 1px solid; background-color: #dedbd6; }
			</style>
			<script language="javascript">
<!--
window.resizeTo(450, 400);
self.focus()

function insertChar(td)
{
	
}

function over(td)
{
	oSample.innerHTML = td.innerHTML ;
	td.classname = 'Active' ;
}

function out(td)
{
	oSample.innerHTML = "&nbsp;" ;
	td.classname = 'Disactive' ;
}
function Init() {
  __dlg_init();
  //document.getElementById("f_rows").focus();
};

function onOK() {
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
  var fields = ["f_char"];
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
//-->
			</script>
	</HEAD>
<body onload="Init()">
		<form name="form1" id="form1" action="" method="get">
		
<input type="hidden" name="f_char" id="f_char" value="">
		<table cellpadding="0" cellspacing="10" width="100%" height="100%">
			<tr>
				<td rowspan="2" width="100%">
					<table class="MainTable" cellpadding="0" cellspacing="0" align="center" border="1" width="100%" height="100%">
						<script language="javascript">
<!--
var aChars = ["!","&quot;","#","$","%","&","'","(",")","*","+","-",".","/","0","1","2","3","4","5","6","7","8","9",":",";","&lt;","=","&gt;","?","@","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","[","]","^","_","`","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","{","|","}","~","&euro;","�","�","�","�","�","�","\�","�","�","�","&lsquo;","&rsquo;","&rsquo;","&ldquo;","&rdquo;","�","&ndash;","&mdash;","�","�","�","�","�","�","&iexcl;","&cent;","&pound;","&pound;","&curren;","&yen;","&brvbar;","&sect;","&uml;","&copy;","&ordf;","&laquo;","&not;","�","&reg;","&macr;","&deg;","&plusmn;","&sup2;","&sup3;","&acute;","&micro;","&para;","&middot;","&cedil;","&sup1;","&ordm;","&raquo;","&frac14;","&frac12;","&frac34;","&iquest;","&Agrave;","&Aacute;","&Acirc;","&Atilde;","&Auml;","&Aring;","&AElig;","&Ccedil;","&Egrave;","&Eacute;","&Ecirc;","&Euml;","&Igrave;","&Iacute;","&Icirc;","&Iuml;","&ETH;","&Ntilde;","&Ograve;","&Oacute;","&Ocirc;","&Otilde;","&Ouml;","&times;","&Oslash;","&Ugrave;","&Uacute;","&Ucirc;","&Uuml;","&Yacute;","&THORN;","&szlig;","&agrave;","&aacute;","&acirc;","&atilde;","&auml;","&aring;","&aelig;","&ccedil;","&egrave;","&eacute;","&ecirc;","&euml;","&igrave;","&iacute;","&icirc;","&iuml;","&eth;","&ntilde;","&ograve;","&oacute;","&ocirc;","&otilde;","&ouml;","&divide;","&oslash;","&ugrave;","&uacute;","&ucirc;","&uuml;","&uuml;","&yacute;","&thorn;","&yuml;"] ;

var cols = 20 ;

var i = 0 ;
while (i < aChars.length)
{
	document.write("<TR>") ;
	for(var j = 0 ; j < cols ; j++) 
	{
		if (aChars[i])
		{
			document.write("<TD class='Disactive' onclick='insertChar(this)' onmouseover='over(this)' onmouseout='out(this)'>") ;
			document.write(aChars[i]) ;
		}
		else
			document.write("<TD class='Empty'>&nbsp;") ;
		document.write("</TD>") ;
		i++ ;
	}
	document.write("</TR>") ;
}
//-->
						</script>
					</table>
				</td>
				<td valign="top">
					<table class="MainTable">
						<tr>
							<td id="SampleTD" width="40" height="40" align="center" class="Sample">&nbsp;
								
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="right" valign="bottom">
					<input type="submit" name="cancel" value="Cancel" onclick="return onCancel();">
				</td>
			</tr>
		</table>
		</form>
	</BODY>
</HTML>
<script language="javascript">
<!--
oSample = document.getElementById("SampleTD") ;
//-->
</script>

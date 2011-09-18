<?php

require_once('../../../local/config.inc.php');

?>
<!-- note: this version of the color picker is optimized for IE 5.5+ only -->

<html style="width: 400px; height: 400px;">
<head>
<title>Insert link to tagged page</title>

<script type="text/javascript" src="popup.js"></script>

<script type="text/javascript">
//window.resizeTo(400, 400);
self.focus();
function _CloseOnEsc() 
{
    if ( event.keyCode == 27 ) 
    { 
        window.close(); 
        return; 
    }
}

function Init()                                                         // run on page load    
{                                                      
    __dlg_init();    
    document.body.onkeypress = _CloseOnEsc;
    
                                                   
}


function Set( fullReference, referencelink )                // return character
{                  
    
	if (document.form1.reference[1].checked == true) {
	
		var ref = referencelink;
		
	} else {
	
		var ref = fullReference;
	
	}
	//parent_object	   = opener.HTMLArea._object;
    //parent_object.setHTML(editor.insertHTML( character ));
     //editor.insertHTML( character );
	__dlg_close( ref );
}

function onCancel()                   // cancel selection
{
    __dlg_close( null );
    
    return false;
}

</script>
</head>
<body style="background: Buttonface;" onLoad="Init()">

<?php 

$current_user_key=$_SESSION['current_user_key'];
//get tags added by user

	$sql = "SELECT {$CONFIG['DB_PREFIX']}tagged_urls.url_key, note, url, heading FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE added_by_key='$current_user_key' ORDER BY heading DESC";
	
$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();

    while (!$rs->EOF) {
	
		$url_key = $rs->fields[0];
		$heading = $rs->fields[3];
		$full_note = preg_replace ('/\r/', '', $rs->fields[1]);
    	$full_note = preg_replace ('/\n/', '', $full_note);
		$full_note = '<blockquote style=\"font-style: italic;	color: #000099;	margin-left: 10px;margin-right: 10px;\">'.$full_note.'</blockquote>';
		$url  = '<div align=\"right\"><a href=\"'.$CONFIG['FULL_URL'].$rs->fields[2].'\" target=\"new\" class=\"small\">'.$general_strings['reference'].'</a></div>';
		$full_note = addslashes($full_note);
		$full_reference = $full_note.$url;
		echo '<script>';
		
		echo 'var full_reference_'.$url_key.' = \''.$full_reference.'\';';
		echo 'var reference_link_'.$url_key.' = \''.$url.'\';';
		echo '</script>';
		echo  "<p><a href=\"\" onClick=\"Set(full_reference_".$url_key.", reference_link_".$url_key.")\">$heading</a></p>";
		$rs->MoveNext();
		
	}

    $rs->Close();	
  

?>

<table border="0" cellspacing="1" cellpadding="0" width="100%" style="cursor: pointer; background: #ADAD9C; border: 1px inset;">

<form id="form1" name="form1" style="text-align: center;">

<input id="reference" name="reference" type="radio" value="yes" checked>Include note<br />
<input id="reference" name="reference" type="radio" value="no">Reference link only<br />
<div align="center"><br />
<button type="button" name="cancel" onClick="return onCancel();" class="submitInsertTable">Cancel</button><button type="button" name="finished" onClick="return onCancel();" class="submitInsertTable">Finished</button>
</div>
</form>
</body>
</html>

<?php

require_once('../../local/config.inc.php');

if(!$_SESSION['current_user_key']) {
	header("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=/FCKeditor/nonieimages.php");
	//echo "<div align=\"center\">You don't appear to be logged in<br />";
	//echo "<a href=\"javascript:close()\">Close this Window</a></div>";
	exit;
}
$current_user_key=$_SESSION['current_user_key'];

if ($_SERVER['REQUEST_METHOD']=='GET') {

    $action = $_GET['action'];
	$file   = $_GET['file'];
	
} else {

    $action    = $_GET['action'];
    $file_name = $_FILES['file']['name'];
    $file      = $_FILES['file']['tmp_name'];
	
}

$sql = "SELECT file_path from {$CONFIG['DB_PREFIX']}users where user_key='$current_user_key'";
$rs = $CONN->Execute("$sql");
while (!$rs->EOF) {
	$filepath = $CONFIG['USERS_PATH']."/".$rs->fields[0];
	$url_path = $CONFIG['USERS_VIEW_PATH']."/".$rs->fields[0];
	$rs->MoveNext();
}
$rs->Close();

	if ($action=="del" && is_file("{$CONFIG['BASE_PATH']}$file")) {

		if (unlink("{$CONFIG['BASE_PATH']}$file")) {
			$message = "The image has been deleted";
		} else {
			$message = "The image could not be deleted";
		}	
	} else if ($action=="upload" && $file_name!="none") {
		//get file extension to see if it is an image
		
		$allowed_image_types = array("jpg","jpeg","gif","png");
		$ext = strtolower(ereg_replace("^.+\\.([^.]+)$", "\\1", $file_name));
		if (!in_array($ext,$allowed_image_types)) {
			$message = "You can only upload gif, jpg and png files";
		//check that the file is not bigger than 100KB
		} else if ($file_size>100000) {
			$message = "That file is too large to upload";
		
		} else {
			//if image is wider than 300 pixels resize it
   			$image_array = GetImageSize("$file"); // Get image dimensions
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
	<title>Insert/Update Image</title>
	<link rel="stylesheet" href="/Interactstyle.css" type="text/css" />

</head>

<body  link=Blue vlink=MediumSlateBlue alink=MediumSlateBlue leftmargin=5 rightmargin=5 topmargin=5 bottommargin=5 bgcolor=Gainsboro>

<?php
echo "<div align=\"center\">$message</div>";
?>


		<table border=0 cellpadding=3 cellspacing=3 align=center>
		<tr>
		
  		<td valign=top>

				<FORM METHOD="Post" ENCTYPE="multipart/form-data" ACTION="../../FCKeditor/nonieimages.php?action=upload" ID="form1" name="form1">
				Upload Image : <br />
				<INPUT type="file" id="file" name="file" size=22 style="font:8pt verdana,arial,sans-serif"><br />
				<input name="sub_dir" ID="sub_dir" type=hidden value="<?php echo $sub_dir?>">
				<INPUT TYPE="submit" value="Upload" >
				</FORM>	
				
				<table border=0 cellpadding=0 cellspacing=0 width=260>
				<tr><td>
				
				<b>File name</b>
				
				</td></tr>
				</table>
				
				
<table border=1 cellpadding=5 cellspacing=0 width=240>
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
    	$image_array = GetImageSize("$filepath/$entry"); // Get image dimensions
    	$image_width = $image_array[0]; // Image width
    	$image_height = $image_array[1]; // Image height
 		if ($image_width>150) {
 			$factor=150/$image_width; 
			$reduced_height=round($image_height*$factor);
		}
	

?> 
		<tr bgcolor=Gainsboro>
    	<td valign=top><?php echo $entry?></td>
    	
    	            <td valign=top><a href="javascript:viewImage('<?php echo "$url_path/$entry" ?>','<?php echo $image_width?>','<?php echo $image_height?>','<?php echo $reduced_height?>')">view</a></td>
    	<td valign=top ><a href="javascript:selectImage('<?php echo "$url_path/$entry" ?>','<?php echo $image_width?>','<?php echo $image_height?>','<?php echo $reduced_height?>')">select</a></td>
   	<td valign=top  ><a href="javascript:deleteImage('<?php echo "$url_path/$entry" ?>')">del</a></td></tr>

<?php 
	}
} 
 ?>               
    
	
	
	


</table>

				</div>
	
				
		</td>						
		</tr>
		<tr>
		            <td colspan=2>&nbsp; </td>
		</tr>
		<tr>
		<td align=center colspan=2>
				<table cellpadding=0 cellspacing=0 align=center><tr>
				<td><INPUT type="button" value="Cancel" onClick="self.close();" style="height: 22px;font:8pt verdana,arial,sans-serif" ID="Button1" NAME="Button1"></td>
				                <td> <span id="btnImgInsert" style="display:none"> </span> <span id="btnImgUpdate" style="display:none"> 
                                    </span> </td>
				</tr></table>
		</td>
		</tr>
		</table>

		<!-- /Content -->
	
	



<script language="JavaScript">
function deleteImage(sURL)
	{
	if (confirm("Delete this document ?") == true) 
		{
		window.location.href = "nonieimages.php?action=del&file="+ sURL;
		}
	}
function selectImage(sURL,width,height,reducedHeight)
	{

	opener.document.inputform.<?php echo $_GET['form_id']; ?>.value +=  "<img src='" + sURL + "' width='" + width + "' height='" + height + "'>";
	self.close();
	
	}
	function viewImage(sURL,width,height,reducedHeight)
	{
	viewImageWindow = window.open(sURL,"ViewWindow","scrollbars=NO,status=no,width=400,height=300")
	 
	
	
	}

</script>
</body>
</html>
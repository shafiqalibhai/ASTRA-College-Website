<?php
require_once('../local/config.inc.php');

$module = isset($_GET['module']) ? $_GET['module']:'';
$file   = isset($_GET['file']) ? $_GET['file']:'';
$page   = isset($_GET['page']) ? urldecode($_GET['page']):'';
$anchor = isset($_GET['anchor']) ?$_GET['anchor']:'';
if ($page!='') {
	
	if (is_file($CONFIG['BASE_PATH'].'/language/'.$_SESSION['language'].'/help/html/'.$page)) {
		header('Location: '.$CONFIG['FULL_URL'].'/language/'.$_SESSION['language'].'/help/html/'.$page.'#'.$anchor);
		exit;
	} else if (is_file($CONFIG['BASE_PATH'].'/language/default/help/html/'.$page)) {
		header('Location: '.$CONFIG['FULL_URL'].'/language/default/help/html/'.$page.'#'.$anchor);
		exit;
	} else {
		header('Location: '.$CONFIG['FULL_URL'].'/language/default/help/html/');
		exit;
	}
	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Interact Help</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?php echo $CONFIG['PATH'];?>/interactstyle.php" type="text/css" />
<style type="text/css">
<!--
body {
	margin: 10px;
}
-->
</style>
</head>

<body>

    <p align="center" class="small"><a href="javascript:close();">Close
Help Window
</a></p>

<?php

$hpath=$CONFIG['BASE_PATH'].'/language/'.$_SESSION['language'].'/help/'.$module.'/'.$file;
if (is_file($hpath)){
	require_once($hpath);
} else {
 	require_once($CONFIG['BASE_PATH'].'/language/default/help/'.$module.'/'.$file);
}

?>
    <p align="center" class="small"><a href="javascript:close();">Close
Help Window
</a></p>

</body>
</html>

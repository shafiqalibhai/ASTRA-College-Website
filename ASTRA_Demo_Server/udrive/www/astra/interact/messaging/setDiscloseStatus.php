<?php
require_once('../local/config.inc.php');
	if(isset($_GET['module']) && isset($_GET['status'])) {
		$_SESSION['disclose_statuses'][$_GET['module']]=$_GET['status'];
	}
$CONN->Close();
exit;
?>
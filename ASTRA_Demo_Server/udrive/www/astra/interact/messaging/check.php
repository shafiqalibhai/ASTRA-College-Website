<?php
require_once('../local/config.inc.php');

if (isset($_SESSION['current_user_key']) && $_SESSION['current_user_key']!='0') {
	$session_id =  session_id();
	$time = time();
	$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}online_users SET user_key='{$_SESSION['current_user_key']}',session_id='$session_id',time='$time', polling='1' WHERE user_key='{$_SESSION['current_user_key']}' AND session_id='$session_id'");
	$rs = $CONN->Execute("SELECT message_key FROM {$CONFIG['DB_PREFIX']}user_messages WHERE   added_for_key={$_SESSION['current_user_key']}");
	$count = $rs->RecordCount();
	$rs->Close();

	$_SESSION['message_count'] = $count;
	echo $count;
} else {
	echo 0;
}
$CONN->Close();
exit;
?>
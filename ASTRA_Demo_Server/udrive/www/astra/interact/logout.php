<?php
/**
* Logout 
*
* Log a user out of the system 
*/

/**
* Include main config file
*/
require_once('local/config.inc.php');
$session_id =  session_id();
$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}online_users WHERE user_key='{$_SESSION['current_user_key']}' AND session_id='$session_id'");


session_destroy();
setcookie('permanent_user','',time() - 3600,$CONFIG['PATH']);
setcookie('permanent_user_key','',time() - 3600,$CONFIG['PATH']);
header("Location:{$_SERVER['HTTP_REFERER']}");
exit;
?>
<?php
/**
* Remove from spaces
*
* Removes a user as a member of a space 
*
*/

/**
* Include main config file 
*/

require_once('local/config.inc.php');


//check to see if user is logged in. If not refer to Login page.
authenticate_home();	 

$space_key = $_GET['space_key'];
$current_user_key = $_SESSION['current_user_key'];

$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' and user_key='$current_user_key'";
$CONN->Execute($sql);
$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE space_key='$space_key' and user_key='$current_user_key'";
$CONN->Execute($sql);
header("Location: {$CONFIG['FULL_URL']}/index.php?edit=true");
exit;
?>
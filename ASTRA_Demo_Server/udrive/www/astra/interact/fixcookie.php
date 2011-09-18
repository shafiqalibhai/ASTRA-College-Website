<?php
// | Fixes corrupt cookies												|

require_once('local/config.inc.php');
session_start();
session_destroy();
setcookie ("permanent_user","",time() - 3600);
setcookie ("permanent_user_key","",time() - 3600);
Header("Location:{$CONFIG['FULL_URL']}/");

?>
<?php
/**
* Index page
*
* Redirects user to default space homepage
*
*/

/**
* Include main config file 
*/
require_once('local/config.inc.php');
header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}");
exit;
?>
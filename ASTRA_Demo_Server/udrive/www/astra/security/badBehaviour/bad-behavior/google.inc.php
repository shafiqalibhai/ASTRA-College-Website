<?php if (!defined('BB2_CORE')) die('Access Denied');

// Analyze user agents claiming to be Googlebot

function bb2_google($package)
{
	if (match_cidr($package['ip'], "66.249.64.0/19") === FALSE && match_cidr($package['ip'], "64.233.160.0/19") === FALSE) {
		return "f1182195";
	}
	return false;
}

?>

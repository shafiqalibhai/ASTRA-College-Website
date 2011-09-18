<?php
$tests= array('/direct?m=','/direct.php/','/');

$short_urls=0;
foreach($tests as $i => $test_string) {
	$file = @fopen($CONFIG['FULL_URL'].$test_string.'testDirect', 'rb');
	if($file && (fread($file,8)=='TEST OK!')) {$short_urls|=1<<$i;}
}
?>
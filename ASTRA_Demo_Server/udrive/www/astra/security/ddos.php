<?php
$cookie = $_COOKIE['yourcookie']; /* if you have a known cookie on your site,
you can use this, otherwise just ignore this, it will set a different limit
for people with this cookie */

/* I use yourothercookie as the cookie ID for the forum, my forum uses ID
greater than 0 for all members and -1 for guests and members who have logged out,
so making it match greater than zero means members will get better access and 
guests with or without cookies won't */
$othercookie = $_COOKIE['yourothercookie'];
if($cookie && $othercookie > 0) $itime = 20;  // Minimum number of seconds between visits
else $itime = 20; // Minimum number of seconds between visits
$ipenalty = 60; // Seconds before visitor is allowed back
if($cookie && $othercookie > 0)$imaxvisit = 100; // Maximum visits per $iteme segment
else $imaxvisit = 100; // Maximum visits per $iteme segment
$iplogdir = "security/iplog/";
$iplogfile = "iplog.dat";

$ipfile = substr(md5($_SERVER["REMOTE_ADDR"]), -2);
$oldtime = 0;
if (file_exists($iplogdir.$ipfile)) $oldtime = filemtime($iplogdir.$ipfile);

$time = time();
if ($oldtime < $time) $oldtime = $time;
$newtime = $oldtime + $itime;

if ($newtime >= $time + $itime*$imaxvisit)
{
touch($iplogdir.$ipfile, $time + $itime*($imaxvisit-1) + $ipenalty);
$oldref = $_SERVER['HTTP_REFERER'];
// header("location: http://www.google.com");
// header("HTTP/1.0 503 Service Temporarily Unavailable");
 //header("Connection: close");
// header("Content-Type: text/html");
 echo "<html><body>
<font face='Arial'><p><b>
Too many page views (more than ".$imaxvisit." visits within ".$itime." seconds)
 by your IP address.</b>
";
echo "Please wait ".$ipenalty." seconds and try again.</p> 
</font></body></html>";
$fp = fopen($iplogdir.$iplogfile, "a");
	if ($fp)
	{
	$useragent = "<unknown user agent>";
	if (isset($_SERVER["HTTP_USER_AGENT"])) $useragent = $_SERVER["HTTP_USER_AGENT"];
	fputs($fp, $_SERVER["REMOTE_ADDR"]." ".date("d/m/Y H:i:s")." ".$useragent."n");
	fclose($fp);
	$yourdomain = $_SERVER['HTTP_HOST'];
	
	//the @ symbol before @mail means 'supress errors' so you wont see errors on the page if email fails.
      if($_SESSION['reportedflood'] < 1 && ($newtime < $time + $itime + $itime*$imaxvisit))
	@mail('webmaster@'.$yourdomain, $yourdomain.'site flood by '.$cookie.' '
	.$_SERVER['REMOTE_ADDR'],'flood occured and ban for '.$_SERVER['REMOTE_ADDR'].' at '
	.$_SERVER['REQUEST_URI'].' from '.$oldref.' agent '.$_SERVER['HTTP_USER_AGENT'].' '
	.$cookie.' '.$othercookie);
	$_SESSION['reportedflood'] = 1;
	}
	exit();
}
else $_SESSION['reportedflood'] = 0;

touch($iplogdir.$ipfile, $newtime);
?>
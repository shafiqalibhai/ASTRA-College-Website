<?php

## Coded By Subhash
## Hacking-Truths.NeT  


// This Is The Blocked Words Inserted By The Url .. Just Insert The Name Of The Table Where Is The Users/Admin Password
// 4 Blocking Some SQL-Injection In The Specified Tables...
// Other XSS is blocked like alert
// Remote command like : cat config.php is blocked
// Remote file include : is possible blocked but not sure...
// When there are new exploit you can add here the malicious script 4 block it!

// Example : in phpBB = <prefix>_users
// Example : in SMF   = <prefix>_members
// Example : in PHP-Nuke = <prefix>_authors
// Example : Remote File Inclusion : /cmd.txt , /shell.txt, /shell.gif ...
// Example : Remote Command : cat, ls -la , ls, uname -a, wget ...
// Example : Directory Trasversal : /etc/passwd ...

$securityrules = array('chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(',
                   'cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20',
                   'union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=',
                   'esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(',
                   'mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm',
                   'mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(',
                   'chmod(', 'chmod%20', '%20chmod', 'chmod(', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(',
                   'locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall',
                   'passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20',
                   'insert%20into', 'select%20', 'nigga(', '%20nigga', 'nigga%20', 'fopen', 'fwrite', '%20like', 'like%20',
                   '$_request', '$_get', '$request', '$get', '.system', 'http_php', '%20getenv', 'getenv%20',
                   '/etc/password','/etc/shadow', '/etc/groups', '/etc/gshadow',
                   'http_user_agent', 'http_host', '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id',
                   '/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g\+\+', 'bin/python',
                   'bin/tclsh', 'bin/nasm', 'traceroute%20', 'ping%20', '.pl', '/usr/x11r6/bin/xterm', 'lsof%20',
                   '/bin/mail', '.conf', 'motd%20', 'http/1.', '.inc.php', 'config.php', 'cgi-', '.eml',
                   'file\://', 'window.open', '<script>', 'javascript\://','img src', 'img%20src','.jsp','ftp.exe',
                   'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd',
                   'servlet', '/etc/passwd', 'wwwacl', '~root', '~ftp', '.js', '.jsp', '.history',
                   'bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20',
                   'powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con',
                   '<script', '/robot.txt' ,'/perl' ,'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from',
                   'select from', 'drop%20', '.system', 'getenv', 'http_', '_php', 'php_', 'phpinfo()', '<?php', '?>', 'sql=',
                   '_global', 'global_', 'global[', '_server', 'server_', 'server[', '/modules', 'modules/', 'phpadmin',
                   'root_path', '_globals', 'globals_', 'globals[', 'ISO-8859-1', 'http://www.google.de/search', '?hl=',
              '.txt', '.exe', 'ISO-', '</', '>', '<', 'SELECT', 'FROM%20', 'alert', 'document.cookie', '*', 'c99shell.php', 'shell.php', 'cmd.php', 'cmd.txt',
                    'c99.gif', '/r57.txt', 'http*', '$*', '/backdoor.php', '/backdoor.gif', '/backdoor.txt', '/shell.txt',
                    'smf_members', 'sourcedir=', 'dirname=', 'CREATE%20', 'UNION%20', '_members%20', 'passwd',
                    'script', '<img', '<?', 'WHERE', 'FLOOD', 'flood', 'floodding', 'ls -', 'uname', 'phpinfo', 'cat%20',
                    'AVWS', 'avws', 'acunetyx', 'ACUNETYX', 'boot.ini', 'magic%20string', 'STRING', '/membri/',
                    '/membri2/', '/membri2', '/membri', 'r57.php?phpinfo', 'r57.php?phpini', 'r57.php?cpu', 'r57.php?',
                    '|dir', '&dir&', 'printf', 'acunetix_wvs_security_test', '=http', 'converge_pass_hash', 'st=-9999{SQl]',
                    'st=-', 'cat%20', 'include', '_path=');



// Rules for the url ...for blocking malicius script inserted in $securityrules

  $cracker = $_SERVER['QUERY_STRING'];
  $cracker = strtolower($cracker);
  $worm  = str_replace($securityrules, '*', $cracker);



// Beta Log ScripT ... 4 Hacking-truths.NeT :)



if($cracker != $worm)
  {
    //
    // Collecting information about the Attack and the Attacker
    //
    $ctl_pmeld    = 1;
    $ctl_stamp    = time();
    $ctl_remotead = $HTTP_SERVER_VARS['REMOTE_ADDR'];
    $ctl_query    = $HTTP_SERVER_VARS['QUERY_STRING'];
    $ctl_referrer = $HTTP_SERVER_VARS['HTTP_REFERER'];
    $ctl_agent    = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
    $ctl_agent    = str_replace('||', ' ', $ctl_agent); // Remove || from User Agents

    //
    // Now we built the Line for the Logfile
    //
    $ctr_logfile  = $ctl_pmeld . '||' . $ctl_stamp . '||' . $ctl_remotead . '||' . $ctl_query . '||' . $ctl_referrer . '||' . $ctl_agent;

    //
    // How many entrys are into the Logfile and how much entrys (default 100) are allowed?
    // I hardcoded this value because I won't contact the Database during an attack.;)
    //
    $ctr_logsize  = count(file("security/ctracker/logs/logfile_worms.txt"));
    $ctr_maxlogs  = 100;

    //
    // Now logging and Counting. The Counter here is asymmetric so just if CTracker has to delete the Log
    // it writes something to the Counter. This is better because during an DoS Like attack we only have to
    // do one file operation. Into the footer the Counter will also count how many entrys are in the log
    // so the Counter-Value is always correct.
    //
    if ($ctr_logsize > $ctr_maxlogs)
    {
      $clog = @fopen("security/ctracker/logs/logfile_worms.txt", "a") or die("Error opening logfile.");
      @ftruncate($clog, '0'); // Better than delete and recreate it later
      @fwrite($clog, "0||" . $ctl_stamp . "||SYSTEM MESSAGE||AUTOMATIC LOG FILE RESET||-||CRACKERTRACKER\n" . $ctr_logfile . "\n");
      @fclose($clog);

      //
      // Because we deleted the Logfile we will now write our new value for the Counter.
      // Oh yes, redundant code because it would not be very performant if we include the complete function File already here. This File is the front line so we
      // rewirte it here because we ONLY need this part and not more.
      //
      $ct_counter_val = 0;
      $countername    = "security/ctracker/logs/counter.txt";
      $ct_counter_val = @file_get_contents($countername);

      $ct_counter_val = $ct_counter_val + 100;

      $cfp = @fopen ($countername, 'a') or die("Error opening counter file.");
      @ftruncate($cfp, '0');
      @fwrite($cfp, $ct_counter_val);
      @fclose($cfp);
    }
    else
    {
      $clog = @fopen('security/ctracker/logs/logfile_worms.txt', 'a') or die ("Error Opening Logfile.");
      @fwrite($clog, $ctr_logfile . "\n");
      @fclose($clog);
    }

}




// Die Message for alert the attacker ...

if($cracker != $worm)
  {
    die("Access Denied, you have been logged.");
  }

?>

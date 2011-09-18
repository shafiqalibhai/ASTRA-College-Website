<?php


/*
* Configuration File
*
* This file sets the main system variables
*/

/* Fill in the $CONFIG values below with your settings.

 Single Codebase Instructions
 ----------------------------
 ARRAYS may be used to have multiple Interact installations
 running from a single codebase.

 e.g. FULL_URL would be like this:
 $CONFIG['FULL_URL'] = array('http://example.com/interact',
                             'http://example.com/othersite',...);

 Other settings only need to be arrays when they contain database info that is
 unique to each install (with elements matching the order in the FULL_URL array).
*/

// Enter the full url(s) for your installation(s)
$CONFIG['FULL_URL'] = 'http://localhost/astra/interact';

// Enter database(s) details 
$CONFIG['DATABASE']      = 'astra';
$CONFIG['DB_PREFIX']     = 'astra_';
// To run multiple Interact sites with one database, use a unique DB_PREFIX for each site.

$CONFIG['DBUSER']        = 'root';
$CONFIG['DBPASSWORD']    = 'root';

$CONFIG['DATABASE_TYPE'] = 'mysql';
$CONFIG['DBSERVER']      = 'localhost';
// DBSERVER should be 'localhost' unless your database is on a separate server


// BASE_PATH is set automatically to Interact codebase root -- Don't change this line!
$CONFIG['BASE_PATH'] 	= substr(__FILE__,0,-21);


/* Path(s) to your Interact data folder(s) -
   folder(s) need to have read/write access for your webserver user. */
$CONFIG['DATA_PATH'] = $CONFIG['BASE_PATH'].'/local';

/* For maximum security you may want to put DATA_PATH outside of your webserver root,  
   but the .htaccess files will prevent direct access if you are running on 
   Apache server with htaccess overrides allowed.

   Note for upgrading users:
   If moving DATA_PATH from /local, you need to move the existing
   'users', 'modules' and 'library' (if any) folders to the new folder.
   DO NOT move the default 'skins' or other files/folders out of /local   */


// Change timezone from server default (requires php 5.1)
//if(function_exists('date_default_timezone_set'))
//	date_default_timezone_set('Pacific/Auckland');


//If you have multiple installations and want to list them on detection-failure
//$CONFIG['SHOW_URL_ARRAY_ON_ERROR']=true;


//If you have macromedia flashcom server installed, set this to the applications path
//$CONFIG['FLASH_COM']='/opt/macromedia/fcs/applications/';

//If you have a file mirror setup...
/*
$CONFIG['FILE_MIRROR']='http://mirrorsite.com/v.php/datacopy';
$CONFIG['FILE_MIRROR_AREAS']=array('users','library','modules');
$CONFIG['FILE_MIRROR_IPMASKS']=array(  //value, bitmask of caller IPs to redirect
	array('0.0.0.0',0)
);
*/

//normally, journal posts are never searched
//WARNING: Enabling this searches all journals and ignores the 'private' setting.
//$CONFIG['SEARCH_ALL_JOURNALS']=true;


/* You should not need to edit anything else.
   If you are wanting to make more detailed changes to system file locations see
   the init_config() function near the top of the /includes/common.inc.php file */ 
require_once($CONFIG['BASE_PATH'].'/includes/common.inc.php');
?>
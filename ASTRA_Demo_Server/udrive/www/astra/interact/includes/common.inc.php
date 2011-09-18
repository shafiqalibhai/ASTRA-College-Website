<?php
/**
* Common functions and variables
*
* This file contains functions and variables, etc. common to the other scripts
* and functions within the system
*
* @package Common
* 
*/

// Global variables are now set in the init_config() function below...
// VIEW PATHS are set via short_url setting in database... at end of init_config()

$CONFIG['ADODB_PATH']	= $CONFIG['BASE_PATH'].'/includes/adodb';
//Include database abstraction classes 
require_once($CONFIG['ADODB_PATH'].'/adodb.inc.php');
require_once($CONFIG['ADODB_PATH'].'/session/adodb-session.php');

//if cron job not set run autofunctions
//curently this option is disabled
//if ($CONFIG['CRON']!=1) {
	//require_once($CONFIG['BASE_PATH'].'/admin/auto.php');
//}

$CONN=get_data_config();

$CONFIG['DOCS_URL'] = '';
//Fix for IIS which doesn't have REQUEST_URI
if(empty($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI']=$_SERVER['PHP_SELF'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING'].'':'');
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
//error_reporting(E_ALL);

require_once('safe_html/safehtml.php');
$objSafeHTML = new SafeHTML();

if(!isset($no_safehtml)) $no_safehtml=array('body');
//escape any user submitted data
clean_strings($_POST);
clean_strings($_GET);
clean_strings($_COOKIE);

//$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
$CONN->SetFetchMode('ADODB_FETCH_NUM');

if (!empty($new_module)) {
	
	preg_match('/modules\/(.*)\//',$_SERVER['SCRIPT_NAME'], $matches);
	$module_code = $matches[1];
	require_once($CONFIG['LANGUAGE_CPATH'].'/'.$module_code.'_strings.inc.php');
	require_once($CONFIG['BASE_PATH'].'/includes/lib/template.inc.php');
	$t = new InteractTemplate($module_code);
	require_once '../../includes/lib/modulerun.inc.php';
	require_once 'lib.inc.php';
	$objForum = new InteractForum($space_key, $module_key, $group_key, $is_admin, $forum_new_strings);
	//instatiate any required objects
	if (isset($objects) && is_array($objects)) {
		foreach($objects as $value){
			$class_name = 'Interact'.ucfirst($value);
			$obj_name = 'obj'.ucfirst($value);
			if (!class_exists($class_name)) {
				require_once('../../includes/lib/'.$value.'.inc.php');
			}
			$$obj_name = new $class_name();
		}
	}

	//set variables
	$space_key 	= get_space_key();
	if ($_SERVER['REQUEST_METHOD']=='GET'){
		$module_key	= isset($_GET['module_key'])?$_GET['module_key']:'';
	} else {
		$module_key	= isset($_POST['module_key'])?$_POST['module_key']:'';
	}
	//$module_key	= $_GET['module_key'];
	$link_key 	= get_link_key($module_key,$space_key);
	$current_user_key	= $_SESSION['current_user_key'];
	//check we have the required variables
	check_variables(true,true);
	//check to see if user is logged in. If not refer to Login page.
	$access_levels = authenticate();
	$accesslevel_key = $access_levels['accesslevel_key'];
	$group_access = $access_levels['groups'];
	$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
	$is_admin = check_module_edit_rights($module_key);
	//update statistics 
	statistics($action_type);
	$page_details = get_page_details($space_key,$link_key);
	set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

	$t->set_var('MODULE_CODE',$module_code);

  	
}
function output_page($module_code, $module_strings='', $module_data='') {
	
	global $t, $forum_strings, $module_data, $CONN;
	$t->parse('CONTENTS', 'header', true);
	get_navigation();
	$t->set_strings($module_code,  $module_strings, $module_data, '');
	$t->parse('CONTENTS', $module_code, true);
	$t->parse('CONTENTS', 'footer', true);
	$t->p('CONTENTS');
	$CONN->Close();
}

/**
* Init Config
*
* Sets up all global variables and database connection.
*
*/
function init_config() {
	global $CONFIG,$ADODB_SESSION_DRIVER,$ADODB_SESSION_CONNECT,$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB,$ADODB_SESSION_TBL, $general_strings;
	
	preg_match('|(https?://[[:alnum:]\._:-]+)(.*)|',$CONFIG['FULL_URL'],$regs);
	$CONFIG['SERVER_URL'] 	=$regs[1];
	$CONFIG['PATH'] 		=$regs[2];
	
	$CONFIG['SESSIONDB']    = $CONFIG['DATABASE'];
	$CONFIG['CHAT_SERVER']           = $CONFIG['DBSERVER'];
	$CONFIG['CHAT_DATABASE']         = $CONFIG['DATABASE'];
	$CONFIG['INCLUDES_PATH']         = $CONFIG['BASE_PATH'].'/includes';
	$CONFIG['TEMPLATE_CLASS_PATH']   = $CONFIG['BASE_PATH'].'/includes/';
	$CONFIG['TEMPLATES_PATH']        = $CONFIG['BASE_PATH'].'/templates';
	$CONFIG['LIBRARY_PATH']          = $CONFIG['DATA_PATH'].'/library';
	$CONFIG['MODULE_FILE_SAVE_PATH'] = $CONFIG['DATA_PATH'].'/modules';
	$CONFIG['USERS_PATH']            = $CONFIG['DATA_PATH'].'/users';
	$CONFIG['TEMP_DIR']				 = $CONFIG['DATA_PATH'].'/temp';
	$CONFIG['USER_INPUT_OPTIONAL_FIELDS'] = array('ID_NUMBER' => 0, 'LANGUAGE' => 0, 'PREFERED_NAME' => 0,'DETAILS' => 0, 'PHOTO' => 0, 'EDITOR' => 0, 'FLAG_POSTS' => 0, 'SKINS' => 0);
	$CONFIG['USER_MODIFY_OPTIONAL_FIELDS'] = array('ID_NUMBER' => 0, 'LANGUAGE' => 1, 'PREFERED_NAME' => 1,'DETAILS' => 1, 'PHOTO' => 1, 'EDITOR' => 1, 'FLAG_POSTS' => 1, 'AUTO_PASSWORD' => 0, 'SKINS' => 1);
	
//set the following to 1 if you want php script within 'page' components to be
//executed. This has some major security implications particularly if space
//admin is devolved to a wide group of users. If you are not aware of the what these
//security implications are then it is best to leave this set to 0
	$CONFIG['ALLOW_PHP'] = 0;
	
	$ADODB_SESSION_DRIVER	= $CONFIG['DATABASE_TYPE'];
	$ADODB_SESSION_CONNECT	= $CONFIG['DBSERVER'];
	$ADODB_SESSION_USER 	= $CONFIG['DBUSER'];
	$ADODB_SESSION_PWD 		= $CONFIG['DBPASSWORD'];
	$ADODB_SESSION_DB 		= $CONFIG['SESSIONDB'];
	$ADODB_SESSION_TBL 		= $CONFIG['DB_PREFIX'].'Sessions';
//make a database conection
	$new_conn = &ADONewConnection($CONFIG['DATABASE_TYPE']); 
	if (!$new_conn->NConnect($CONFIG['DBSERVER'],$CONFIG['DBUSER'],$CONFIG['DBPASSWORD'],$CONFIG['DATABASE'])) {
		echo 'There was a fatal error connecting to the database';
		echo $new_conn->ErrorMsg();
		if (strpos($_SERVER['SCRIPT_NAME'],'/admin/setup/')!==FALSE) {
			echo '<p class="message">The setup scripts were not able to connect to the database that you have specified in your config file</p>';
			echo '<p><a href="'.substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'/admin/setup/')).'/help/admin/install/install.html">Instructions for installing a new copy of Interact</a></p>';
			echo '<p>Please check that the following lines in your config.inc.php file are correctly set for your database<br/ >$CONFIG[\'DBUSER\']<br />$CONFIG[\'DBPASSWORD\']<br />$CONFIG[\'DATABASE\']<br />$CONFIG[\'DBSERVER\']</p>';
		}
		exit;
	}
	
//get all the Interact server settings
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $new_conn->GetRow("SELECT * FROM {$CONFIG['DB_PREFIX']}server_settings");
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	


	if (is_array($rs)){
		foreach($rs as $key => $value) {
			$CONFIG[strtoupper($key)] = $value;
		}
		if (!isset($CONFIG['NO_SESSION'])) {
			//echo $CONFIG['SERVER_NAME'];
			//session_name($CONFIG['SERVER_NAME']);
			session_start();
			
		}	
//VIEW PATHS are set via short_url setting in database.
		$CONFIG['DIRECT_PATH'] = ($CONFIG['SHORT_URLS']&4)?
			'/':
			'/direct'.(($CONFIG['SHORT_URLS']&1)?'':'.php').(($CONFIG['SHORT_URLS']&2)?'/':'?m=');
		
		$CONFIG['VIEWFILE_PATH'] = (($CONFIG['SHORT_URLS']&2)?
			'/viewfile.php/':(
				($CONFIG['SHORT_URLS']&4)?
					$CONFIG['DIRECT_PATH'].'viewf/':
					'/viewfile.php?path='
			)
		);
	
		$CONFIG['LIBRARY_VIEW_PATH'] = $CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'].'library';
		$CONFIG['MODULE_FILE_VIEW_PATH'] = $CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'];
		$CONFIG['USERS_VIEW_PATH']       = $CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'].'users';
		set_language();
	//get language strings
		include_once($CONFIG['LANGUAGE_CPATH'].'/general_strings.inc.php');
	}

	return $new_conn;
}


/**
* Get Data Config
*
* gets data for requested install if multiple installs with a single codebase
*
*/
function get_data_config() {
	global $CONFIG,$ACONFIG;
	if(is_array($CONFIG['FULL_URL']))  {
		if(isset($ACONFIG['CONFIG_NUM'])) {
			if($ACONFIG['CONFIG_NUM']==0) {$ACONFIG['CONFIG']=$CONFIG;
				$ACONFIG['MAX_CONFIG_NUM']=count($CONFIG['FULL_URL']);
			}
			$CFkey=$ACONFIG['CONFIG_NUM'];
			$CONFIG['FULL_URL']=$CONFIG['FULL_URL'][$CFkey];
		} else {
			$this_url='http'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

			foreach ($CONFIG['FULL_URL'] as $CF=>$val) {
				
				if (strncasecmp($this_url,$val,strlen($val))===0) {
					$CFkey=$CF;
					break;
				}
			}
			if(isset($CFkey)) {
				$CONFIG['FULL_URL']=$val;
			} else {
				if (isset($CONFIG['DEFAULT_SERVER'])) {
					$CONFIG['FULL_URL'] = $CONFIG['FULL_URL'][$CONFIG['DEFAULT_SERVER']];
				} else {
					echo '<h1>Interact server error</h1>Cannot find matching site for '.$this_url;
					if(!empty($CONFIG['SHOW_URL_ARRAY_ON_ERROR'])){
						echo '<ul><h2 style="margin-bottom:2px">Maybe you wanted one of these sites:</h2>';
						foreach ($CONFIG['FULL_URL'] as $val) {
							if(substr($val,-1)!='/') $val.='/';
							echo "<li><a href='$val'>$val</a></li>";
						} 
						echo'</ul>';
					}
					exit;
				}
			}

		}
		
		foreach(array('DATABASE_TYPE','DATABASE','DBUSER','DBPASSWORD','DBSERVER','DB_PREFIX','DATA_PATH') as $key) {if(is_Array($CONFIG[$key])) $CONFIG[$key]=$CONFIG[$key][$CFkey]; }}

	$newCONN=init_config();
	return $newCONN;
}


/**
* Set language 
*
* Sets the default language for current user
*
*/
function set_language() {
	global $CONFIG;

	$lpath=$CONFIG['BASE_PATH'].'/language/';
	if (isset($_SESSION['language'])) {
		$lpath.=$_SESSION['language'];
	} else {
		$lpath.=$CONFIG['DEFAULT_LANGUAGE'];
		$_SESSION['language']=$CONFIG['DEFAULT_LANGUAGE'];
	}
	$CONFIG['LANGUAGE_CPATH']=$lpath.'/strings/compiled';
} //end set_language


/**
* Authenticate 
* Checks users login status if accessing home page
*
*/
function authenticate_home()
{
   global $CONN,$CONFIG, $general_strings;
	
	//if there is no current_user_key cookie and this is a secure server
	//then refer to login page
	
	if (!isset($_SESSION['current_user_key']) && $CONFIG['SECURE_SERVER']==1) {
		
		session_destroy();
		$request_uri = urlencode($_SERVER['REQUEST_URI']);
		$message = urlencode($general_strings['login_needed']);
		Header ("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=$request_uri&message=$message");
		exit;
		
	}   

}

/**
* Check variable 
* check that any required variables are present before proceeding
*
*/
function check_variables($check_space_key=false,$check_link_key=false,$check_module_key=false) 
{
	global $CONFIG,$space_key,$link_key,$module_key;
	if ($check_space_key==true) {
		if ($space_key=="") {
			header("Location: {$CONFIG['FULL_URL']}/index.php?message=You+seem+to+have+accessed+a+page+the+wrong+way+.+Please+try+again");
			exit;
		}
	}
	if ($check_link_key==true) {
		if ($link_key=="") {
			header("Location: {$CONFIG['FULL_URL']}/index.php?message=You+seem+to+have+accessed+a+page+the+wrong+way+.+Please+try+again");
			exit;
		}
	}
	
	   if ($check_module_key==true) {
		if ($module_key=="") {
			header("Location: {$CONFIG['FULL_URL']}/index.php?message=You+seem+to+have+accessed+a+page+the+wrong+way+.+Please+try+again");
			exit;
		}
	}
} //end check_variables

/**
* Authenticate 
* Checks users login status
*
*/
function authenticate($module_admin=false) 
{

	global $CONN,$space_key,$module_key,$group_key, $link_key, $CONFIG, $general_strings;

	$session_id =  session_id();
	$time = time();
	$sql_update = "UPDATE {$CONFIG['DB_PREFIX']}online_users SET user_key='{$_SESSION['current_user_key']}',session_id='$session_id',time='$time' WHERE user_key='{$_SESSION['current_user_key']}' AND session_id='$session_id'";
	$CONN->Execute($sql_update);
	if ($CONN->Affected_Rows()==0) {

		$sql_insert = "INSERT INTO {$CONFIG['DB_PREFIX']}online_users(user_key,session_id,time, status_key) VALUES ('{$_SESSION['current_user_key']}','$session_id','$time','1')";
		$CONN->Execute($sql_insert);

	}
	$no_poll_stale = time()-600;
	$poll_stale = time()-35;
	$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}online_users WHERE (time<'$no_poll_stale' AND polling='0') OR (time<'$poll_stale' AND polling='1')");

	if (!empty($module_key)) {

		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.access_level_key,group_key,{$CONFIG['DB_PREFIX']}module_space_links.status_key, {$CONFIG['DB_PREFIX']}module_space_links.access_level_key  FROM {$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}module_space_links.space_key and ({$CONFIG['DB_PREFIX']}spaces.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4')");

		if ($rs->EOF) {

			$message = urlencode($general_strings['access_denied']);
			header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}&message=$message");
			exit;	
		} else {
			$space_access_level=$rs->fields[0];
			$group_key=$rs->fields[1];
			$status_key = $rs->fields[2];
			$module_access_level = $rs->fields[3];
			$rs->Close();
		}

	} else {
	
		if ($space_key==1) {
			$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.access_level_key, null FROM {$CONFIG['DB_PREFIX']}spaces WHERE {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key'");
		} else {
			$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}spaces.access_level_key, {$CONFIG['DB_PREFIX']}module_space_links.status_key FROM {$CONFIG['DB_PREFIX']}spaces LEFT JOIN {$CONFIG['DB_PREFIX']}module_space_links ON {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key WHERE {$CONFIG['DB_PREFIX']}spaces.space_key='$space_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'");
		}
		
		echo $CONN->ErrorMsg();
		if ($rs->EOF) {
			$message = urlencode($general_strings['access_denied']);
			header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key={$CONFIG['DEFAULT_SPACE_KEY']}&message=$message");
			exit;	
		} else {
			$space_access_level=$rs->fields[0];
			$space_status_key=$rs->fields[1];
		}	
		$rs->Close();
	}



		//check to see of use can view current page

		if ((empty($_SESSION['current_user_key']) && $space_access_level!=3 && $module_access_level!=3)
			||
			(!empty($_COOKIE['permanent_user_key']) && empty($_SESSION['current_user_key']))
			 || 
			 (empty($_SESSION['current_user_key']) && (
			 ($CONFIG['SECURE_SERVER']==1 && $space_access_level!=3 && $module_access_level!=3)
			 )
			 )){
			
			$request_uri = urlencode($_SERVER['REQUEST_URI']);
			$message = urlencode($general_strings['login_needed']);
			header ("Location: {$CONFIG['FULL_URL']}/login.php?request_uri=$request_uri&message=$message");
			exit;
		
		}  
	

//if user is not an top level admin check to see if they have access to this space
//and what level of access

		if ($_SESSION['userlevel_key']!=1) {

			$sql = "select access_level_key from {$CONFIG['DB_PREFIX']}space_user_links where user_key='{$_SESSION['current_user_key']}' and space_key='$space_key'";

			$rs = $CONN->Execute($sql);
			if ($rs->EOF) {
				
				if (($space_access_level!=1 &&  $space_access_level!=3 && $module_access_level!=3 && $module_access_level!=1) || (!isset($_SESSION['current_user_key']) && $module_access_level==1)  || $module_access_level=='2') {
					
					header("Location: {$CONFIG['FULL_URL']}/spaces/access.php?space_key=$space_key&module=$module");
					exit;
				}
				//see if user has access to module
				if($_SESSION['userlevel_key']!=1 && ($status_key==5 || $space_status_key==5)) {
						$message = urlencode($general_strings['access_denied']);
						header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
						exit;		
				}
			} else {
				while (!$rs->EOF) {
					$accesslevel_key=$rs->fields[0];
					$rs->MoveNext();
				}
				//see if they have access to this module
				
				if (isset($module_key) && $module_key!='') {
					
					if($_SESSION['userlevel_key']!=1 && $accesslevel_key!=1 && $accesslevel_key!=3 && $status_key==5 ) {
						$message = urlencode($general_strings['access_denied']);
						header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
						exit;		
					}
				} 
			}
		} //end if $userlevel_key


//find out which groups the user has access to 
				
	$group_access=array();
	$group_accesslevel=array();
	if ($_SESSION['userlevel_key']!="1" || $accesslevel_key!="1" || $accesslevel_key!="3") {
	$n=1;
	$sql = "select group_key,access_level_key from {$CONFIG['DB_PREFIX']}group_user_links where user_key='{$_SESSION['current_user_key']}'";
	$rs = $CONN->Execute($sql);
	while (!$rs->EOF) {
		$group_key2 = $rs->fields[0];
		$group_accesslevel_key = $rs->fields[1];
		$group_access[$n]=$group_key2;
		$group_accesslevel[$group_key2] = $group_accesslevel_key;
		$n++;
		$rs->MoveNext();
	}
}
	
	//find out the access level of the group
	if (!class_exists(InteractGroup)) {
			
		require_once($CONFIG['BASE_PATH'].'/modules/group/lib.inc.php');
				
	}
			
	if (!is_object($groupObject)) {
			
	$groupObject = new InteractGroup();
			
	}
				
	$group_data = $groupObject->getGroupData($group_key);  

if ($accesslevel_key!=1 && $accesslevel_key!=3 && $_SESSION['userlevel_key']!=1  && (isset($group_key) && $group_key!=0) && $group_data['access_key']!=2) {
	
	if (!in_array($group_key,$group_access) || !$_SESSION['current_user_key'] || $_SESSION['current_user_key']=='0') {
  
		header("Location: {$CONFIG['FULL_URL']}/modules/group/access.php?space_key=$space_key&group_key=$group_key");
		exit;
	
	}
}

$access_levels=array();
$access_levels["accesslevel_key"]=$accesslevel_key;
$access_levels["groups"]=$group_access;
$access_levels["group_accesslevel"]=$group_accesslevel;

	if ($module_admin && ($group_accesslevel[$group_key]!=1 && $accesslevel_key!=1 && $accesslevel_key!=3 && $_SESSION['userlevel_key']!=1 && !check_module_edit_rights($module_key))) {
			$message = urlencode($general_strings['no_edit_rights'].' '.$general_strings['module_text']);
			header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key&message=$message");
			exit;
		
	
}

		return $access_levels;


} //end authenticate

/**
* Authenticate Admin 
* Check that user has admin rights
*
*/

function authenticate_admins($level='full')
{
	global $CONFIG,$accesslevel_key;
	
	if ($level=='full') {
		
		if ($_SESSION['userlevel_key']!=1) {
			
			$message = urlencode($general_strings['access_denied']);
			header("Location: {$CONFIG['FULL_URL']}/index.php?message=$message");
			exit;
		
		}
		
	} elseif ($level="space_only") {
	   
	   if ($_SESSION['userlevel_key']!=1 && $accesslevel_key!=1 && $accesslevel_key!=3) {
		   
			$message = urlencode($general_strings['access_denied']);
			header("Location: {$CONFIG['FULL_URL']}/index.php?message=$message");
			exit;
		
		}
	
	}

}


function VIEWFILE_PATH_to_path($url) {
	global $CONFIG;
	if(strstr($url,$CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'])) {
		$testpath=substr($url,strlen($CONFIG['PATH'].$CONFIG['VIEWFILE_PATH']));
		switch(substr($testpath,0,($fslash=strpos($testpath,'/')))) {
		case 'users':
			return $CONFIG['USERS_PATH'].substr($testpath,$fslash);
			break;
		case 'library':
			return $CONFIG['LIBRARY_PATH'].substr($testpath,$fslash);
		break;
		}
	} else return false;
}


/**
* Format error messages 
* 
* @param [string] string of error message
* @return formatted error
*/

function sprint_error($string)
{
	if(!empty($string))
	{
		return("<br /><span class=\"error\">&nbsp;!&nbsp;$string</span>\n");
	}
} //end sprint_error


/**
* check to see if a string is in valid email address format  
* 
* @param [string] email address string 
* @return true or false
*/

function is_email($string)
{
	// Remove whitespace
	$string = trim($string);
	
	$ret = ereg(
				'^([a-zA-Z0-9_&]|\\-|\\.)+'.
				'@'.
				'(([a-zA_Z0-9_&]|\\-)+\\.)+'.
				'[a-zA_Z]{2,4}$',
				$string);
	
	return($ret);
}

/**
* get list of items as an HTML menu  
* @param [sql]sql to execute
* @param [name]name of the menu
* @param [selected] key of preselected items 
* @return html menu
*/
function make_menu($sql,$name,$selected,$size,$multiple=false,$blank=false)
{
	global $CONN;
	$rs = $CONN->Execute($sql);

	$menu = $rs->GetMenu2($name,$selected,$blank,$multiple,$size);
	return $menu;  
}

/**
* gets page details, space, module name, etc.  
* @param [space_key]current space key
* @param [module_key]current module key
* @return $page_details array
*/

function get_page_details($space_key,$link_key="")
{
	global $CONN, $trail, $general_strings, $CONFIG, $accesslevel_key;

	$PHP_SELF			   = $_SERVER['PHP_SELF'];
	$REQUEST_URI			= $_SERVER['REQUEST_URI'];
	$SCRIPT_NAME			= $_SERVER['SCRIPT_NAME'];
	$userlevel_key		  = $_SESSION['userlevel_key'];
	$current_user_firstname = $_SESSION['current_user_firstname'];
	
	$page_details=array();
	
	$objNavigation = singleton::getInstance('navigation');
	$page_details['space_breadcrumbs']=$objNavigation->getSpaceBreadcrumbs($space_key);

	//if there is no module_key then it must be the homepage of a space
	
	$rs = $CONN->Execute("SELECT short_name, name, description,file_path,access_level_key,header, show_members, combine_names, owned_by_key, type_key, skin_key, space_map, alt_home FROM {$CONFIG['DB_PREFIX']}spaces where space_key='$space_key'");

	$page_details['space_description']=$rs->fields[2];
	$page_details['space_name']=$rs->fields[1];
	$page_details['space_short_name']=$rs->fields[0];
	$combine_name=$rs->fields[7];
			
	if ($combine_name==1 && $page_details['space_short_name']!='') {
		$page_details['full_space_name']=$rs->fields[0].' - '.$rs->fields[1];
	} else {
		$page_details['full_space_name']=$rs->fields[1];
	}
			
	$page_details["file_path"]=$rs->fields[3];
	$page_details["access_level_key"]=$rs->fields[4];
	$page_details["header"]=$rs->fields[5];	
	$page_details["show_members"]=$rs->fields[6];
	$page_details['space_owner_key']=$rs->fields[8];
	$page_details['type_key']=$rs->fields[9];
	$page_details['skin_key']=$rs->fields[10];	
	$page_details['spacemap']=$rs->fields[11];
	$page_details['space_alt_home']=$rs->fields[12];
	$page_details['space_key'] = $space_key;	
	//if logged in show  logout, else show login 
	if (isset($current_user_firstname)) {
		$page_details["login_link"] = "<a href=\"{$CONFIG['PATH']}/logout.php\">".$general_strings['logout'].'</a>';
	} else {
		$page_details["login_link"] = '<a href="'.$CONFIG['PATH'].'/login.php?request_uri='.urlencode($REQUEST_URI).'">'.$general_strings['login'].'</a>';

	}
		
		

	if (!empty($link_key)){

		// if we have a link key then we need to also get module details 

		$rs = $CONN->Execute("SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}module_space_links.status_key, {$CONFIG['DB_PREFIX']}modules.type_code, {$CONFIG['DB_PREFIX']}module_space_links.icon_key, {$CONFIG['DB_PREFIX']}modules.module_key  FROM {$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE   {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}module_space_links.space_key  AND  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'");
		
		$page_details['module_name']=$rs->fields[0];
		$page_details['module_description']=$rs->fields[1];
		$page_details['parent_key']=$rs->fields[2];
		$page_details['group_key']=$rs->fields[3];
		$page_details['module_status_key']=$rs->fields[4];
		$page_details['module_code']=$rs->fields[5];
		$page_details['icon_key']=$rs->fields[6];
		$page_details['module_key']=$rs->fields[7];
		$rs->Close();	
		$page_details['module_breadcrumbs']=$objNavigation->getModuleBreadcrumbs($link_key,$page_details);

	} else {
		
		$page_details['module_name']='';
		$page_details['module_description']='';
		$page_details['parent_key']='';
		$page_details['group_key']='';
		$page_details['module_status_key']='';
		$page_details['module_code']='';
		$page_details['icon_key']='';
		$page_details['module_key']='';

	}
	
	return $page_details;
}



/**
* Generate main navigation menu  
* 
*/

function get_navigation($include_active=true,$shift_breadcrumbs=false)
{
	global $space_key, $t, $CONN, $module_key, $group_key, $accesslevel_key,  $page_details, $group_access, $group_accesslevel,$link_key, $general_strings, $CONFIG, $objSpace;
 
	
	$PHP_SELF			   = $_SERVER['PHP_SELF'];
	$REQUEST_URI			= $_SERVER['REQUEST_URI'];
	$userlevel_key		  = $_SESSION['userlevel_key'];
	$current_user_key	   = $_SESSION['current_user_key'];
	$current_user_firstname = $_SESSION['current_user_firstname'];
	$current_user_lastname  = $_SESSION['current_user_lastname'];
	$hidden_divs = '';
	
	if (isset($current_user_key) && $current_user_key!='') {
	
		get_your_links_menu();
		
	}
	
	if (isset($_SESSION['current_user_key']) && $_SESSION['current_user_key']!='0' && $page_details['space_owner_key']==$_SESSION['current_user_key']) {
			$my_links = "<a class=\"mylinks\" href=\"{$CONFIG['PATH']}/spaces/myposts.php?space_key=$space_key\" >".$general_strings['my_posts'].'</a>';
			if ($CONFIG['ALLOW_TAGS']==1) {
				$my_links .= "<a class=\"mylinks\" href=\"{$CONFIG['PATH']}/urltags/urltags.php?space_key=$space_key\" >".$general_strings['url_tags'].'</a>';
			}
			$t->set_var("MY_LINKS",$my_links);
	}
	$rs = $CONN->Execute("SELECT parent_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key'");
	
	while (!$rs->EOF) {
	
		$parent_key = $rs->fields[0];
		$rs->MoveNext();
	
	}
	
	
	if ($parent_key!=0) {
	
		$rs = $CONN->Execute("SELECT type_code, navigation_mode, link_key, {$CONFIG['DB_PREFIX']}module_space_links.module_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}folder_settings WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}folder_settings.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.link_key='$parent_key'");
		
		if (!$rs->EOF) {
		
			while (!$rs->EOF) {
		
				$type_code = $rs->fields[0];
				$navigation_mode = $rs->fields[1];
				$parent_link_key = $rs->fields[2];
				$parent_module_key = $rs->fields[3];
				
				$rs->MoveNext();		
		
			}
	 
			if (isset($navigation_mode) && $navigation_mode==1) {
			
				require_once($CONFIG['BASE_PATH'].'/modules/folder/lib.inc.php');
				$objFolder = new InteractFolder();
				$folder_navigation = $objFolder->getFolderNavigation($parent_module_key, $parent_link_key,$space_key,$group_key, $module_key);
				$t->set_var("FOLDER_NAVIGATION",$folder_navigation['top']);
				$t->set_var("FOLDER_NAVIGATION_BOTTOM",$folder_navigation['bottom']);
				
			} 
			
		}
		
	} else {
	
		$t->set_var("FOLDER_NAVIGATION",'');
		
	}
	
	//if logged in show user name and logout, else show login link
	if (isset($current_user_key)) {
	
		if ($_SESSION['userlevel_key']!=5) {
		
			$login_details = get_login_details();
			
		} else {
		
			$login_details = $general_strings['logged_in_as']."<br /> $current_user_firstname $current_user_lastname";
		
		}

		$t->set_var("LOGIN_DETAILS","$login_details");
	

		//get users photo
		$objUser = singleton::getInstance('user');
		$photo=$objUser->getUserphotoTag($current_user_key, '35', $space_key);
		$t->set_var("CURRENT_USER_PHOTO",$photo?'<div id="personalBoxPhoto">'.$photo.'</div>':'');
		
	} else {
	
		$request_uri=urlencode($REQUEST_URI);
		$login_details = $general_strings['not_logged_in'];
		//$login_details .= "<br /><a href=\"{$CONFIG['PATH']}/login.php\" class=\"navlinks\">".$general_strings['login'].'</a>';		
		$t->set_var("LOGIN_DETAILS","$login_details");
	}
	set_online_status_link($current_user_key);

//if the user has admin rights show edit Add link and Admin Functions link

	if (($userlevel_key=="1" || $accesslevel_key=="1" || $accesslevel_key=="3") && (isset($space_key) && $space_key!='')) {
		
		$add_module = sprintf($general_strings['add_module_to'], $page_details['space_name']);
		$admin_links=' '.get_admin_tool($CONFIG['PATH'].'/modules/general/moduleadd.php?space_key='.$space_key,true,$add_module,'plus');

		$sql = "select {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}modules.type_code,  {$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key,  {$CONFIG['DB_PREFIX']}module_space_links.owner_key, {$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key, {$CONFIG['DB_PREFIX']}module_space_links.sort_order, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links where  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' and {$CONFIG['DB_PREFIX']}module_space_links.parent_key='0' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}module_space_links.block_key='0') order by sort_order,{$CONFIG['DB_PREFIX']}module_space_links.date_added";

		$t->set_var("ADMIN_LINKS","$admin_links");
		$admin_functions = "<a href=\"{$CONFIG['PATH']}/spaceadmin/admin.php?space_key=$space_key\" class=\"navlinks\">Admin</a>";
		$t->set_var("ADMIN_FUNCTIONS","$admin_functions");
		if ($module_key) {
			
			$current_module_string = sprintf($general_strings['current_module_no'], $general_strings['module_text']);
			$current_module_key=$current_module_string.' '.$module_key;
			$t->set_var("CURRENT_MODULE_KEY",'<div id="currentmodule_key"'.get_admin_tool_class().'>'.$current_module_key.'</div>');
		}
	} else {

		$sql = "select {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}modules.type_code,  {$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.parent_key,{$CONFIG['DB_PREFIX']}module_space_links.target,{$CONFIG['DB_PREFIX']}module_space_links.status_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key, {$CONFIG['DB_PREFIX']}module_space_links.edit_rights_key, {$CONFIG['DB_PREFIX']}module_space_links.owner_key, {$CONFIG['DB_PREFIX']}module_space_links.sort_order, {$CONFIG['DB_PREFIX']}module_space_links.icon_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links where  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' and {$CONFIG['DB_PREFIX']}module_space_links.parent_key='0' and ({$CONFIG['DB_PREFIX']}module_space_links.status_key='1' OR {$CONFIG['DB_PREFIX']}module_space_links.status_key='3') AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4' AND {$CONFIG['DB_PREFIX']}module_space_links.block_key='0') ORDER BY sort_order,{$CONFIG['DB_PREFIX']}module_space_links.date_added";
  
	}
	$members_page = $CONFIG['PATH']."/spaces/members.php";

	if ($space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
	
		if ($page_details['space_short_name']!='') {
		
			$space_name = $page_details['space_short_name'];
						
		} else {
		
			$space_name = $page_details['space_name'];
			
		}
	
		if ((isset($module_key) && $module_key!='')||($PHP_SELF == $members_page)) {
			$t->set_var("SPACE_NAME",'<a href="'.$CONFIG['PATH'].'/spaces/space.php?space_key='.$space_key.'">'.$space_name.'</a>');
		} else {
			$t->set_var("SPACE_NAME",$space_name);
		}

		
	} else {
	
		$t->set_var("SPACE_NAME",'');
		
	}


//if space is restricted show link to members and if not show make_member link
	$make_member='';$members_link='';
	if (($page_details['show_members']!=0)||($PHP_SELF == $members_page)) {
//	echo '--'.$make_member.'--'.$page_details['access_level_key'];
//		if ($page_details['access_level_key']==1)  {
			$sql_member = "SELECT space_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='$space_key' AND user_key='$current_user_key'";

			$rsmember=$CONN->Execute($sql_member);
		
			if ($rsmember->EOF && (isset($space_key) && $space_key!='') && (isset($current_user_key) && $current_user_key!='')){
		
				$make_member = "<a class=\"inlineButton addmeButton\" href=\"{$CONFIG['PATH']}/spaces/makemember.php?space_key=$space_key\" title=\"".sprintf($general_strings['add_me_explain'],$general_strings['space_text']).'">'.$general_strings['add_me'].'</a>';
			} else {
				if(isset($current_user_firstname)) {  // only if logged in...
					$warning=sprintf($general_strings['remove_membership_warning'],
						$general_strings['space_text']);
	
					$make_member = "<a class=\"inlineButton addmeButton\" title=\"$warning\"
									href=\"{$CONFIG['PATH']}/spaces/makemember.php?space_key=$space_key&action=remove&referer=members.php\" 
					onclick=\"return confirmDelete('$warning"
					.(($adminstuff&&$userlevel_key!=1)?
						' '.$general_strings['remove_admin']:'')
					."')\">".$general_strings['leave'].'</a>';
				}
			}
//		}
	}
//	$t->set_var('MAKE_MEMBER',$make_member);

	
	if ($PHP_SELF == $members_page) {
   
		$members_link = "<li id=\"activelink\">$make_member<span class=\"memberButton navlinks\">".$general_strings['members'].'</span></li>';
		
	} else if (isset($space_key) && $space_key!='' && $page_details['show_members']!=0){
	
		if (!isset($objSpace)) {
			if (!class_exists('InteractSpace')) {
				require_once($CONFIG['BASE_PATH'].'/spaces/lib.inc.php');
			}
			$objSpace = new InteractSpace();
		}
		$new_members = $objSpace->countNewMembers($space_key, $_SESSION['current_user_key']);
		//see if we have any new members

		if ($new_members>0) {
			$new_members = ' ('.sprintf($general_strings['new_postings_count'], $new_members).')';	
		} else {
			$new_members='';
		}
		$members_link = "<li>$make_member<a class=\"memberButton navlinks\" href=\"{$CONFIG['PATH']}/spaces/members.php?space_key=$space_key\">".$general_strings['members'].$new_members.'</a></li>';

	}

	$t->set_var('MEMBERS_LINK',$members_link);
	if ($members_link=='') {
		$t->set_block('navigation', 'MembersBlock');
		$t->set_var('MembersBlock','');
	}
	if ($page_details['spacemap']==1) {
		if (str_replace($CONFIG['PATH'],'',$_SERVER['SCRIPT_NAME'])!='/spaces/spacemap.php') {
			$t->set_var('SPACEMAP','<li><a class="spaceMapButton navlinks" href="'.$CONFIG['PATH'].'/spaces/spacemap.php?space_key='.$space_key.'">'.$general_strings['spacemap'].'</a></li>');
		} else {
			$t->set_var('SPACEMAP','<li id="activelink"><span class="spaceMapButton navlinks">'.$general_strings['spacemap'].'</span></li>');			
		}
	}	
	
	
	$rs = $CONN->Execute("$sql");

	$header_array=array();$link='<ul class="navlevel">';
	while (!$rs->EOF) {


		$icon_tag = ''; 
		
		$nav_code = $rs->fields[3];

		$module_key2 = $rs->fields[0];
		$group_key2 = $rs->fields[1];
		

if ($nav_code=='group') {		
			if (!class_exists(InteractGroup)) {
			
				require_once($CONFIG['BASE_PATH'].'/modules/group/lib.inc.php');
				
			}
			
			if (!is_object($groupObject)) {
			
				$groupObject = new InteractGroup();
			
			}
				
			$group_data = $groupObject->getGroupData($module_key2);
					
		}
		
		if ($nav_code!='group' || $userlevel_key==1 || $accesslevel_key==1 || $accesslevel_key==3 || in_array($module_key2,$group_access) || $group_data['visibility_key']==1) {

			
			$nav_name = $rs->fields[2];
			if(strlen($nav_name)>35){$nav_name=substr($nav_name,0,54).'&hellip;';}
			$nav_image = $rs->fields[3];
			$nav_description = strip_tags(substr($rs->fields[4],0,255));
			$target = ($rs->fields[6]=='new_window')?'target="'.$module_key2.'"':'';
			$status_key = $rs->fields[7];
			$link_key2 = $rs->fields[8];
			$owner_key = $rs->fields[9];
			$edit_rights_key = $rs->fields[10];
			$sort_order = $rs->fields[11];						
			$nav_admin = $nav_code."_input.php";
			$icon_key = $rs->fields[12];
			
			if ($icon_key>2) {
			
				if (!class_exists('InteractHtml')) {
			
					require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
				
				}
			
				if (!is_object($htmlObject)) {
			
					$htmlObject = new InteractHtml();
			
				}
				
				$icon_tag = $htmlObject->getIconurl($icon_key);	
			
			}
			$extraclass='';
			if (isset($icon_tag) && $icon_tag!=false) {
			
				$nav_image = "$icon_tag";$extraclass=" customicon";
				
			} else if ($icon_key==1){
			
				$nav_image = "{$CONFIG['PATH']}/images/$nav_image.gif";
			
			} else if ($icon_key==2){
			
				$nav_image = '';
			
			}
			// if user has admin rights show edit image
			$can_edit_link = check_link_edit_rights($link_key2,$accesslevel_key,$group_accesslevel,$owner_key);

			$extra_info='';
			if ($can_edit_link==true) {
				$admin_javascript = "onMouseOver=\"showhide(this,'admin_$module_key2','visible')\" onmouseout=\"showhide(this,'admin_$module_key2','hidden')\"";
			} else {
				$admin_javascript = '';
			}

			if ($status_key==2 || $status_key==5) {
				$hidden = '<span class="smallred">X</span>';
			} else {
				$hidden = '';
			}
			if ($nav_code=='heading') {
				$rs2=$CONN->Execute("SELECT initial_state,level FROM {$CONFIG['DB_PREFIX']}headings WHERE module_key=$module_key2");
				$hclosed=(isset($_SESSION['disclose_statuses']['navH'.$module_key2])?
					$_SESSION['disclose_statuses']['navH'.$module_key2]:$rs2->fields[0]);
				$newhlevel=min(3,max(1,$rs2->fields[1]));
$extra_info.="<br />{$general_strings['heading']} $newhlevel ";
				if ($nav_name) {
					$link.=pophlist($header_array,$newhlevel)."<li class=\"navHeadingLI\"><span class=\"navHeading\" ";
					$link.="style=\"background-image:url({$CONFIG['PATH']}/images/disclosure_";
					if($hclosed) {  //allow for later force-open for active link!
						$link.='{NAVSTATIMG_'.$module_key2.'}';
						$t->set_var('NAVSTATIMG_'.$module_key2,'closed');							
					} else {
						$link.='open';
					}
					
					$link.=".gif)\" onClick=\"disclose_it('navH$module_key2',this.style,'backgroundImage')\" $admin_javascript";
					
					$link.="><span>$nav_name</span></span><span class=\"{NAVSTATCLASS_$module_key2}\" id=\"navH$module_key2\"><ul class=\"navlevel\" ";
					if($hclosed) {$t->set_var('NAVSTATCLASS_'.$module_key2,'jsHide');}

					$link.='>';
					$header_array[count($header_array)+1]=array($newhlevel,$module_key2);
				} else {
					$link.=pophlist($header_array,$newhlevel)."<li class=\"navSpacer\"><span $admin_javascript></span></li>";
				}

			} else {
				if ($link_key==$link_key2) {
					
					$link .= ($include_active? "<li id=\"activelink\"><span style=\"background-image: url($nav_image)\" class=\"navlinks$extraclass\" $admin_javascript>$nav_name $hidden</span></li>":"<li class=\"navSpacer\"><span></span></li>");
					
					foreach($header_array as $elem) {
						$t->set_var('NAVSTATIMG_'.$elem[1],'open');
						$t->set_var('NAVSTATCLASS_'.$elem[1],'');
					}
					 
				} else {
								
					if ($nav_code=='space') {
						
						$rs_space_key = $CONN->Execute("SELECT space_key,short_name,combine_names FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key2'");
						
						$space_key2 = $rs_space_key->fields[0];
						$display_name = $rs_space_key->fields[1];
						if($display_name=='') {$display_name=$nav_name;} else {
							if($rs_space_key->fields[2]){$display_name.=' - '.$nav_name;
							} else {
								$nav_description=$nav_name.' - '.$nav_description;
							}
						}
						$rs_space_key->Close();

						$link .= "<li".($hidden?get_admin_tool_class():'')."><a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key2&link_key=$link_key2\"  title=\"$nav_description\" $target  style=\"background-image: url($nav_image)\" class=\"navlinks$extraclass\"  $admin_javascript>$display_name $hidden</a> </li>";
						
					} else {
					
						$link .= "<li".($hidden?get_admin_tool_class():'')."><a href=\"{$CONFIG['PATH']}/modules/$nav_code/$nav_code.php?space_key=$space_key&amp;module_key=$module_key2&amp;link_key=$link_key2&amp;group_key=$group_key2\"  class=\"navlinks$extraclass\" title=\"$nav_description\" $target style=\"background-image: url($nav_image)\" $admin_javascript>$nav_name $hidden</a> </li>";
						
					
					}
		
				}
				

			}
			
			if ($can_edit_link==true) {
			
				$admin_image=get_admin_tool("{$CONFIG['PATH']}/modules/$nav_code/$nav_admin?space_key=$space_key&amp;module_key=$module_key2&amp;link_key=$link_key2&amp;action=modify",false,"{$general_strings['edit']} {$general_strings['module_text']} $module_key2 - sort order $sort_order",'tool','',"onMouseOut=\"killpopup2(event,'admin_$module_key2')\"");
				

				$hidden_divs .= "<div id=\"admin_$module_key2\" class=\"popup\" onMouseOver=\"keeppopup('admin_$module_key2')\" onMouseOut=\"killpopup(event,'admin_$module_key2')\">  $admin_image {$general_strings['module_text']} #$module_key2 - {$general_strings['sort_order']} $sort_order$extra_info</div>";
				$admin_javascript = "onMouseOver=\"showhide(this,'admin_$module_key2','visible')\" 
onmouseout=\"showhide(this,'admin_$module_key2','hidden')\"";
				
			} else { 
				$admin_image='';
			}
			
			
		}
		$rs->MoveNext();
	}

	$t->set_var('HIDDEN_DIVS',$hidden_divs);
	
	$link.=pophlist($header_array,0);
	
	$sql="select name,url from {$CONFIG['DB_PREFIX']}common_nav_links where space_type_key='$page_details[type_key]'";
	$rs = $CONN->Execute("$sql");

	while (!$rs->EOF) {

		$name = $rs->fields[0];
		$url = $rs->fields[1];
		$nav_image = "<td><img src=\"{$CONFIG['PATH']}/images/link.gif\" width=\"16\" height=\"16\" align=\"middle\" alt=\"icon\" /></td>";
		$t->set_var("NAV_IMAGE","$nav_image");
		$link .= "<td><a href=\"$url\" class=\"navlinks\" >$name</a></td>";
		$t->set_var("ADMIN_IMAGE","");
		$rs->MoveNext();
	}
	//see if admin for current module, if so show admin tool
	$can_edit_link = check_link_edit_rights($link_key,$accesslevel_key,$group_accesslevel,$owner_key);				
	
	if ($can_edit_link==true) {
	
		$code = $page_details['module_code'];
		
		$current_module_admin=get_admin_tool("{$CONFIG['PATH']}/modules/".$page_details['module_code']."/".$page_details['module_code']."_input.php?space_key=$space_key&amp;module_key=$module_key&amp;link_key=$link_key&amp;action=modify",true,"{$general_strings['edit']} {$general_strings['module_text']} $module_key - {$general_strings['sort_order']} $sort_order",'tool',"{$general_strings['edit']} {$general_strings['module_text']} $module_key");
		
	
		$t->set_var("CURRENT_MODULE_ADMIN_IMAGE",$current_module_admin);		
	}
	if (($userlevel_key==1 || $accesslevel_key==1 || $accesslevel_key==3) || $can_edit_link || $_SESSION['disclose_statuses']['admin_tool_button']) {
		$t->set_var('ADMIN_TOOL_BUTTON','<img class="jsShow" src="'.$CONFIG['PATH'].'/images/admin_tool_'.($_SESSION['disclose_statuses']['admin_tool_button']?'closed':'open').img_extn().'" width="19" height="18" id="admin_tool_button" onclick="admin_toggle()" title="'.$general_strings['admin_tool_button'].'">');
	}

	$t->set_var("SPACE_KEY","$space_key");
	
	$t->set_var("NAV_LINK_TOTAL",$link."</ul>");
	$t->Parse('NAV_LINK', 'NAV_LINK_TOTAL');
	
	if($shift_breadcrumbs) {
		$t->set_block('navigation', 'BreadcrumbsBox');
		$t->set_var('SHIFTED_BREADCRUMBS');
		$t->parse('SHIFTED_BREADCRUMBS','BreadcrumbsBox');
		$t->set_var('BreadcrumbsBox','');
	}
	
	$t->parse("CONTENTS", "navigation", true);
	

} //end get_navigation


function pophlist(&$header_array,$newhlevel) {
	$header_depth=count($header_array);
	$pops='';
	while($header_depth>0 && $header_array[$header_depth][0]>=$newhlevel) {
		$pops.='</ul></span></li>';
		unset($header_array[$header_depth--]);
	}
	return $pops;
}



function get_login_details() {
	global $general_strings,$CONFIG;
	return $general_strings['logged_in_as']."<br /> <a href=\"".$CONFIG['PATH']."/users/userinput.php?action=modify\"  title=\"".$general_strings['modify_details']."\">{$_SESSION['current_user_firstname']} {$_SESSION['current_user_lastname']}</a>";
}


function set_online_status_link($current_user_key) {
	global $t,$general_strings,$CONFIG;

	if(isset($current_user_key)) {

		if (isset($_SESSION['online_status']) && $_SESSION['online_status']==1) {
			$t->set_var("ONLINE_STATUS_LINK",'<a href="'.$CONFIG['PATH'].'/messaging/setstatus.php?status=0" title="'.$general_strings['hide_me_desc'].'">'.$general_strings['hide_me'].'</a>');
		} else {
			$t->set_var("ONLINE_STATUS_LINK",'<a href="'.$CONFIG['PATH'].'/messaging/setstatus.php?status=1" title="'.$general_strings['show_me_desc'].'">'.$general_strings['show_me'].'</a>');
		}
		$t->set_var("LOGGED_IN_CLASS",'loggedin');
	} else {

		$t->set_block('navigation', 'OnlineStatusBlock');
		$t->set_var('OnlineStatusBlock','');
		$t->set_var("LOGGED_IN_CLASS",'notloggedin');
	}
}

function get_your_links_menu() {
	global $t,$CONN,$space_keys,$general_strings, $CONFIG, $last_use, $spaces_sql, $groups_sql;

   	$PHP_SELF			   = $_SERVER['PHP_SELF'];
	$current_user_key	   = $_SESSION['current_user_key'];
	$current_user_firstname = $_SESSION['current_user_firstname'];
	$current_user_lastname  = $_SESSION['current_user_lastname'];
	$edit				   = $_GET['edit'];
	
	// find spaces that user is a member of
	$space_keys=array();
	$spaces = $spaces.'<form name="yourlinks" method="GET" action="'.$CONFIG['PATH'].'/redirect.php" style="margin: 0px; padding: 0px;"><select onChange="openDir(this.form)" name="URL">';
	
	//if user_notes enabled find out if there have been any notes added for this user
	$spaces .= '<option value="'.$CONFIG['PATH'].'/">'.$general_strings['home'].'</option>';
	$spaces .= '<option value="'.$_SERVER['REQUEST_URI'].'" selected>&mdash; '.$general_strings['your_links'].' &mdash;</option>';
	if ($CONFIG['USER_SPACES']==1) {
	
		//see if user has a MySpace
		$myspace_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE owned_by_key='$current_user_key'");
		if ($myspace_key) {
			$spaces = $spaces."<option value=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$myspace_key\">".$general_strings['myspace']."</option>";	
		}
				
	}
	if ($CONFIG['ALLOW_TAGS']==1) {

		$spaces = $spaces."<option value=\"{$CONFIG['PATH']}/urltags/urltags.php\">".$general_strings['url_tags']."</option>";
			
	}
	if ($CONFIG['GLOBAL_GRADEBOOK']==1) {
	
		$spaces = $spaces."<option value=\"{$CONFIG['PATH']}/modules/gradebook/globalgradebook.php\">".$general_strings['global_gradebook']."</option>";
			
	}

	

	
	//if server admin then give server admin link
	if ($_SESSION['userlevel_key']==1) {
	
		$spaces = $spaces.'<option value="'.$CONFIG['PATH'].'/admin/">Server Admin</option>';
		
	}
	
	$sql = "select {$CONFIG['DB_PREFIX']}spaces.space_key,short_name,{$CONFIG['DB_PREFIX']}spaces.name from {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}modules where {$CONFIG['DB_PREFIX']}spaces.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND  ({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key') AND ({$CONFIG['DB_PREFIX']}spaces.short_name!='$current_user_key') AND {$CONFIG['DB_PREFIX']}modules.status_key='1' AND {$CONFIG['DB_PREFIX']}spaces.owned_by_key!='$current_user_key' ORDER BY short_name";
	
	$rs = $CONN->Execute("$sql");
echo $CONN->ErrorMsg();
	if (!$rs->EOF) {
		
		$spaces .= '<optgroup label="'.ucfirst($general_strings['space_plural']).'">';
				
		while (!$rs->EOF) {
			$space_key = $rs->fields[0];
			$short_name = $rs->fields[1];
			$name = $rs->fields[2];
			
			if ($short_name=='') {
				
				if (strlen($name)>14) {
					$name = substr($name,0,13).'&hellip;';
				}
				$spaces = $spaces."<option value=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\"> $name</option>";
				$space_keys[$space_key]=$name;
			} else {
				$spaces = $spaces."<option value=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\"> $short_name</option>";
				$space_keys[$space_key]=$short_name;
			}
			
			$rs->MoveNext();
		}
		$spaces .= "</optgroup>";
	}
	
	
	//now get groups
		$sql = "select {$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}module_space_links.space_key, {$CONFIG['DB_PREFIX']}module_space_links.module_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}group_user_links where {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key AND  ({$CONFIG['DB_PREFIX']}group_user_links.user_key='$current_user_key')  AND {$CONFIG['DB_PREFIX']}modules.status_key='1' AND {$CONFIG['DB_PREFIX']}modules.type_code='group' ORDER BY name";
	
	$rs = $CONN->Execute("$sql");
echo $CONN->ErrorMsg();
	if (!$rs->EOF) {
		
		$spaces .= '<optgroup label="Groups">';
				
		while (!$rs->EOF) {
			$name = $rs->fields[0];
			$space_key= $rs->fields[1];
			$module_key = $rs->fields[2];
			
			if (strlen($name)>14) {
				$name = substr($name,0,13).'&hellip;';
			}
			$spaces = $spaces."<option value=\"{$CONFIG['PATH']}/modules/group/group.php?space_key=$space_key&module_key=$module_key\"> $name</option>";
			$space_keys[$space_key]=$name;
						
			$rs->MoveNext();
		}
		$spaces .= "</optgroup>";
	}
	
	$spaces .= '</select> <script type="text/javascript">
</script><noscript> <input name="submit" type="image" align="absbottom" src="'.$CONFIG['PATH'].'/images/go.gif" alt="go" title="follow link" height="17" width="23" /></noscript></form>';
	$t->set_var("YOUR_LINKS_MENU","$spaces");
} //get_your_links_menu()


/**
* create admin navigation  
*/

function admin_navigation() 
{
	global $t, $CONFIG, $general_strings;
	
	get_your_links_menu();

	if (!class_exists('InteractUser')) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');
	}
	$user = new InteractUser();
	$photo=$user->getUserphotoTag($_SESSION['current_user_key'], '35', '');
	$t->set_var("CURRENT_USER_PHOTO",$photo?'<div id="personalBoxPhoto">'.$photo.'</div>':'');

	$current_user_firstname = $_SESSION['current_user_firstname'];
	$current_user_lastname = $_SESSION['current_user_lastname'];
	$current_user_key = $_SESSION['current_user_key'];
	if (isset($current_user_firstname)) {
		$t->set_var("LOGIN_DETAILS",get_login_details());
		$t->set_var("LOGIN_LINK","<a href=\"{$CONFIG['PATH']}/logout.php\">".$general_strings['logout'].'</a>');
	}
//	$SPACE_TEXT = ucfirst($SPACE_TEXT);
	$t->set_var("NAVIGATION","$navlinks");
	$t->set_var("SPACE_TEXT",ucfirst($general_strings['space_text']));
	
	set_online_status_link($current_user_key);

//hack top heading into link if not on admin index page
	if(substr($_SERVER['SCRIPT_NAME'],-15)!='admin/index.php') {
		$t->set_var('SERVER_ADMIN','<a href="'.$CONFIG['PATH'].'/admin/">'.$general_strings['server_admin'].'</a>');}

	$t->parse("CONTENTS", "navigation", true);
	
	
//sneaky hack to make menu highlight current page
$SCRNAME=preg_replace('/\\//' ,'\\/', $_SERVER['SCRIPT_NAME'].(!empty($_SERVER['QUERY_STRING'])? '\\?'.$_SERVER['QUERY_STRING'] : ''));
$t->set_var("CONTENTS",(preg_replace("/<li\>[^\<]*\<a href=\"".$SCRNAME."\"([^\>]+)class=(\"[^\>]*\>[^\<]+<\/)a>[^\<]*\<\/li\>/", "<li id=\"activelink\"><span\\1class=\\2span></li>", $t->get_var("CONTENTS"))));
} //end admin_navigation

function img_extn() {
	return preg_match('/MSIE [56]\./',$_SERVER['HTTP_USER_AGENT'])?'.gif':'.png';
}

function get_admin_tool($href,$class_it=true,$title='*',$type='tool',$alt='',$miscImgTags='') {
	global $CONFIG,$general_strings;
	$isize=16;

	switch($type) {
	case 'tool':$isize=11;if($title=="*"){$title=$general_strings['edit'];}
	break;
	case 'plus':$isize=10;if($title=="*"){$title=$general_strings['add'];}
	break;
	case 'smallplus':$isize=8;if($title=="*"){$title=$general_strings['add'];}
	break;
	}
	if($title=='*') {$title=$general_strings['edit'];}
	return "<a href=\"$href\"".($class_it?get_admin_tool_class():'')."><img src=\"{$CONFIG['PATH']}/images/$type".img_extn()."\" width=\"$isize\" height=\"$isize\" border=\"0\" title=\"".($title?$title:$alt).'" '.($alt?'alt="'.$alt.'"':($title?'alt="'.$title.'"':'')).' '.$miscImgTags.'></a>';
}

function get_admin_tool_class() {
	return ' class="admin_tool"'.(($_SESSION['disclose_statuses']['admin_tool_button']==1)?' style="display:none"':'');
}

/**
* Print headers to stop pages being cached  
*/

function print_headers() 
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
} //end print_headers



function statistics($type="read") 
{
	
	global $CONN, $space_key,$module_key,$accesslevel_key, $CONFIG;
	
	$REMOTE_ADDR	  = $_SERVER['REMOTE_ADDR'];
	$userlevel_key	= $_SESSION['userlevel_key'];
	$current_user_key = $_SESSION['current_user_key'];	
	

	$date = date("Y-m-d H:i:s");

	$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}statistics(space_key,module_key,user_key,date_accessed,use_type, location) VALUES ('$space_key','$module_key','$current_user_key','$date','$type','$REMOTE_ADDR')");
}

function browser_get_agent() {
	
	$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT']; 

	$browser=array();
	if (ereg( 'MSIE ([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
		$browser["version"]=$log_version[1];
		$browser["agent"]='IE';
	} elseif (ereg( 'Opera ([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
		$browser["version"]=$log_version[1];
		$browser["agent"]='OPERA';
	} elseif (ereg('Safari/([0-9]+\.[0-9]+)',$HTTP_USER_AGENT,$log_version)) {
		$browser["version"]=$log_version[1];
		$browser["agent"]='SAFARI';
	} elseif (ereg( 'Mozilla/([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
		$browser["version"]=$log_version[1];
		$browser["agent"]='MOZILLA';
	} else {
		$browser["version"]=0;
		$browser["agent"]='OTHER';
	}
	if (ereg( 'Gecko\/([0-9]*)',$HTTP_USER_AGENT,$log_version)) {
		$browser["gecko_version"]=$log_version[1];
	} 
	if (strstr($HTTP_USER_AGENT,'Win')) {
	$browser["platform"]='Win';
	} else if (strstr($HTTP_USER_AGENT,'Mac')) {
	$browser["platform"]='Mac';
	} else if (strstr($HTTP_USER_AGENT,'Linux')) {
	$browser["platform"]='Linux';
	} else if (strstr($HTTP_USER_AGENT,'Unix')) {
	$browser["platform"]='Unix';
	} else {
	$browser["platform"]='Other';
	}
		return $browser;
}



function delete_directory($dir) 
{ 
 
     global $CONFIG;
     
     if (strpos($dir, $CONFIG['DATA_PATH'])!==0) {
         return false;
     }

		
	if(!$opendir = opendir($dir)) {
		
		return false;
		
	}

	while(false !== ($readdir = readdir($opendir))) {

		if($readdir !== '..' && $readdir !== '.') {
	
			$readdir = trim($readdir);
			
			if(is_file($dir.'/'.$readdir)) {
			
				if(!unlink($dir.'/'.$readdir)) {
					
					return false;

				}

		   } elseif(is_dir($dir.'/'.$readdir)) {

			   // Calls itself to clear subdirectories

			   if(!delete_directory($dir.'/'.$readdir)) {
			   
				   return false; 
			   
			   }

		   }

	   }

	}

	closedir($opendir);

	if(!rmdir($dir)) {

		return false;
	
	}

	return true;

}

function copy_directory($source, $dest){
     // Simple copy for a file
     if (is_file($source)) {
        $c = copy($source, $dest);
        chmod($dest, 0777);
        return $c;
      }
 
     // Make destination directory
     if (!is_dir($dest)) {
          $oldumask = umask(0);
          mkdir($dest, 0777);
        umask($oldumask);
      }
 
     // Loop through the folder
     if ($dir = dir($source)) {
     
		 $c=true;
		 while ($c && (false !== $entry = $dir->read())) {
			  // Skip pointers
			  if ($entry == '.' || $entry == '..') {
				   continue;
			   }
	  
			  // Deep copy directories
			  if ($dest !== "$source/$entry") {
				   $c=copy_directory("$source/$entry", "$dest/$entry");
			   }
		  }
	 } else {return false;}
	 
     // Clean up
     $dir->close();
     return $c;
}


function _is_valid($string, $min_length, $max_length, $regex)
{
	// Check if the string is empty
	$str = trim($string);
	if(empty($str))
	{
		return(false);
	}

	// Does the string entirely consist of characters of $type?
	if(!eregi("^$regex$", $string))
	{
		return(false);
	}
	
	// Check for the optional length specifiers
	$strlen = strlen($string);
	if(($min_length != 0 && $strlen < $min_length) || ($max_length != 0 && $strlen > $max_length))
	{
		return(false);
	}

	// Passed all tests
	return(true);

}

/*
 *	  bool is_alpha(string string[, int min_length[, int max_length]])
 *	  Check if a string consists of alphabetic characters only. Optionally
 *	  check if it has a minimum length of min_length characters and/or a
 *	  maximum length of max_length characters.
 */
function is_alpha($string, $min_length = 0, $max_length = 0)
{
	$ret = _is_valid($string, $min_length, $max_length, "[[:alpha:]]+");

	return($ret);
}


/*
 *	  bool is_alphanumeric(string string[, int min_length[, int max_length]])
 *	  Check if a string consists of alphanumeric characters only. Optionally
 *	  check if it has a minimum length of min_length characters and/or a
 *	  maximum length of max_length characters.
 */
function is_alphanumeric($string, $min_length = 0, $max_length = 0)
{
	$ret = _is_valid($string, $min_length, $max_length, "[[:alnum:]]+");

	return($ret);
}

function email_error($error_type)
{
	global $CONFIG,$module_key,$space_key;
	
	$current_user_key = $_SESSION['current_user_key'];
	
	$message = "There was an error - $error_type\n";
	$message .= "\nmodule_key=$module_key\n";
	$message .= "space_key=$space_key\n";
	$message .= "current_user_key=$current_user_key\n";
	$subject = "Interact Error";

	mailfrom($CONFIG['ERROR_EMAIL'],$CONFIG['ERROR_EMAIL'],$CONFIG['ERROR_EMAIL'],$subject,$message,$headers);
}

function mailfrom($fromaddress, $toaddress, $fullto,$subject, $body, $headers) { 
$toaddress=ereg_replace( "&", "\&", $toaddress);
$fullto=ereg_replace( "&", "\&", $fullto);
$headers="Content-Transfer-Encoding: 8bit\n".$headers;
$fp = popen('/usr/sbin/sendmail -f'.$fromaddress.' '.$toaddress,"w"); 
if(!$fp) return false; 

fputs($fp, "To: $fullto\n"); 
fputs($fp, "Subject: $subject\n"); 
fputs($fp, $headers."\n\n"); 
fputs($fp, $body); 
fputs($fp, "\n"); 
pclose($fp); 
}

//returns selected userkeys as an array
function get_userkey_array($sql)
{
	global $CONN;
	$n=0;
	$user_keys = array();
	$rs = $CONN->Execute("$sql");

	while (!$rs->EOF) {
		$user_keys[$n] = $rs->fields[0];
		$n++;
		$rs->MoveNext();
	}
	return $user_keys;
}

function get_readposts_array($module_key='',$user_key)
{
	global $CONN, $CONFIG;
	$n=0;
	$readposts_array = array();
	if ($module_key=="") {
		$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}ReadPosts WHERE user_key='$user_key'";
	} else {
		$sql = "SELECT post_key FROM {$CONFIG['DB_PREFIX']}ReadPosts WHERE module_key='$module_key' AND user_key='$user_key'";
	}

	$rs = $CONN->Execute($sql);
	while (!$rs->EOF) {
		$readposts_array[$n] = $rs->fields[0];
		$n++;
		$rs->MoveNext();
	}
	return $readposts_array;

}



function set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel)
{
	global $t, $CONFIG, $CONN, $general_strings, $objHtml, $objSkins;

	if(xmlhconn_ok()) {$t->set_var('MESSAGE_INIT','<script type="text/javascript">messagetrans="'.$general_strings['messages'].'";message_refresh='.(isset($_SESSION['current_user_firstname'])?30000:600000).';setMessagePollTime();</script>');}

	if (isset($_SESSION['current_user_key']) && $_SESSION['current_user_key']!=0) {
		$rs = $CONN->Execute("SELECT message_key,added_by_key, message, first_name, last_name, time FROM {$CONFIG['DB_PREFIX']}user_messages, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}user_messages.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND  added_for_key={$_SESSION['current_user_key']} AND ({$CONFIG['DB_PREFIX']}user_messages.status_key=1 OR {$CONFIG['DB_PREFIX']}user_messages.status_key=2)");

		if (!$rs->EOF) {
			
			$record_count = $rs->RecordCount();
			$t->set_var('MESSAGE_ALERT_STYLE','style="display:block"');
			if (isset($_SESSION['message_count']) && $_SESSION['message_count']<$record_count) { 
			$t->set_var('MESSAGES','<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="20" height="20" id="bing" align="middle"> <param name="allowScriptAccess" value="sameDomain" /> <param name="movie" value="'.$CONFIG['PATH'].'/messaging/messagebing.swf?snd=1" /> <param name="quality" value="high" /><param name="wmode" value="transparent" />  <embed src="'.$CONFIG['PATH'].'/messaging/messagebing.swf?snd=1" quality="high"  width="20" height="20" name="messagebing" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="transparent"/></object> '."<a href=\"javascript:open_window('".$CONFIG['PATH']."/messaging/message_admin.php?action=read','messsageAdmin')\">".$general_strings['messages'].'</a> (<span id="messageCount">'.$record_count.'</span>)');
			
			} else {
			
				$t->set_var('MESSAGES','<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="20" height="20" id="bing" align="middle"> <param name="allowScriptAccess" value="sameDomain" /> <param name="movie" value="'.$CONFIG['PATH'].'/messaging/messagebing.swf?snd=0" /> <param name="quality" value="high" /><param name="wmode" value="transparent" />  <embed src="'.$CONFIG['PATH'].'/messaging/messagebing.swf?snd=0" quality="high"  width="20" height="20" name="messagebing" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="transparent"/></object> '."<a href=\"javascript:open_window('".$CONFIG['PATH']."/messaging/message_admin.php?action=read','messsageAdmin')\">".$general_strings['messages'].'</a> (<span id="messageCount">'.$record_count.'</span>)');			
			}
			
		} 
		$_SESSION['message_count'] = $record_count;
		$rs->Close();
	}
	
	$t->set_var('SPACE_KEY',$space_key);
	$t->set_var('DOCS_URL',$CONFIG['DOCS_URL']);
	$t->set_var('CONTENT_CLASS',$page_details['module_code']?$page_details['module_code']:'space');
	$t->set_var('CONTEXT_HELP_LINK',ucfirst($page_details['module_code']));
	$t->set_var('FULL_URL',$CONFIG['FULL_URL']);
	$t->set_var('MESSAGE_COUNT',isset($record_count)?$record_count:0);
	$t->set_var('SERVER_NAME',$CONFIG['SERVER_NAME']);
	$t->set_var('PATH',$CONFIG['PATH']);
	$t->set_var('MODULE_FILE_VIEW_PATH',$CONFIG['MODULE_FILE_VIEW_PATH']);
		$t->set_block('footer', 'TagBlock', 'TGSBlock');
	
	if (!is_object($objSkins)) {
		if (!class_exists('InteractSkins')) {
			require_once($CONFIG['BASE_PATH'].'/skins/lib.inc.php');
		}
		$objSkins = new InteractSkins();
	}
	$skin_key = $objSkins->getskin_key($space_key, $page_details['type_key']);
	$t->set_var('SKIN_KEY',$skin_key[0]);
	$t->set_var('SKIN_VERSION',$skin_key[1]);
	if (is_file($CONFIG['BASE_PATH'].'/modules/'.$page_details['module_code'].'/'.$page_details['module_code'].'.css')){
		$t->set_var('META_TAGS','<link rel="stylesheet" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key='.$skin_key[0].'&skin_version='.$skin_key[1].'&module_code='.$page_details['module_code'].'" type="text/css" media="screen, projection, print">');
	}
	if (empty($message)) {
		$message = !empty($_GET['message'])?
			urldecode($_GET['message']):
			(!empty($_POST['message'])?
				urldecode($_POST['message']):'');
				
	}
	$message =strip_tags($message);
	$t->set_var('CHARACTER_SET',$general_strings['character_set']);
	if (!is_array($page_details)) {
		
		$t->set_var('TGSBlock','');
		$t->set_var('MESSAGE',$message);
		$t->set_var('HEADER_HOME_LINK',$CONFIG['FULL_URL']);
		
		
	} else {
		if (!is_object($objHtml)) {
			if (!class_exists('InteractHtml')) {
				require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
			}
			$objHtml = new InteractHtml();
		}

		$t->set_var('LOGIN_LINK',isset($page_details['login_link']) ? $page_details['login_link'] : '');

		$t->set_var('OTHER_HEADER_LINKS',isset($page_details['other_header_links']) ? $page_details['other_header_links'] : '');
		$t->set_var('HOME_LINK',isset($page_details['home_link']) ? $page_details['home_link'] : '');
		$t->set_var('PAGE_TITLE',$page_details['full_space_name'].' - '.$page_details['module_name']);
		$t->set_var('SERVER_NAME',$CONFIG['SERVER_NAME']);
		
		$t->set_var('SEARCH',$general_strings['search']);
		$t->set_var('SUBMIT',$general_strings['submit']);

		$t->set_var('MESSAGE',$message);	
		$t->set_var('SPACE_BREADCRUMBS',$page_details['space_breadcrumbs']);
		$t->set_var('MODULE_BREADCRUMBS',$page_details['module_breadcrumbs']);
		$t->set_var('DESCRIPTION',$objHtml->parseText($page_details['module_description']));	
		$t->set_var('FULL_NAME',$current_user);
		if (!empty($page_details['space_alt_home'])) {
			$t->set_var('HEADER_HOME_LINK',$page_details['space_alt_home']);
		} else {
			$t->set_var('HEADER_HOME_LINK',$CONFIG['PATH'].'/spaces/space.php?space_key='.$CONFIG['DEFAULT_SPACE_KEY']);
		}
		
		if (($_SERVER['PHP_SELF']!=$CONFIG['PATH'].'/modules/'.$page_details['module_code'].'/'.$page_details['module_code'].'.php') && (isset($space_key) && $space_key!='')) {
			$t->set_block('navigation', 'ModuleHeadingBlock', 'MHBlock');
			$t->set_var('MHBlock','');
		
		} else {
	
			if (!class_exists('InteractHtml')) {
				require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
	 		}
	
			$html = new InteractHtml();
			$t->set_var('MODULE_NAME',$page_details['module_name']);
			$icon_tag = $html->getIconTag($page_details['icon_key'], 'large');
	
			if ($icon_tag!=false) {
				$t->set_var('ICON_TAG',$icon_tag);
			} else {
				$t->set_var('ICON_TAG','<img src="'.$CONFIG['PATH'].'/images/'.$page_details['module_code'].'.gif'.'" height="16" width="16" alt="Icon">');
			}
		
		}
	
		//if tags enabled set Add Tag link
		if ($CONFIG['ALLOW_TAGS']==1 && isset($_SESSION['current_user_key'])) {
			if ($space_key!='' && $_SERVER['PHP_SELF']!='/urltags/urltaginput.php') {
				$current_url = urlencode($_SERVER['REQUEST_URI']);
				$add_tag_link = '<a href="'.$CONFIG['PATH'].'/urltags/urltaginput.php?space_key='.$space_key.'&module_key='.$module_key.'&tag_url='.$current_url.'" title="'.$general_strings['tag_page'].'"><img src="'.$CONFIG['PATH'].'/images/addtag.gif" border="0"></a>';
				$t->set_var('ADD_TAG',$add_tag_link);
			} else {
		   		$t->set_var('ADD_TAG','');
	   		}
	
			//get any url tags for this page
			if (!class_exists('InteracturlTags')) {
				require_once($CONFIG['BASE_PATH'].'/urltags/lib.inc.php');
			}
			$objurlTags = new InteracturlTags();
			$urltags = $objurlTags->geturlTags($_SERVER['REQUEST_URI'], $space_key, $_SESSION['current_user_key'], $accesslevel_key, $group_accesslevel, $module_key);
		
		} else {
			$t->set_var('TGSBlock','');
		}
	}
}

function set_common_admin_vars($page_title, $message)
{
	global $t, $CONFIG, $general_strings,$admin_strings;
	
	if(xmlhconn_ok()) {$t->set_var('MESSAGE_INIT','<script type="text/javascript">messagetrans="'.$general_strings['messages'].'";message_refresh='.(isset($_SESSION['current_user_firstname'])?30000:590000).';setMessagePollTime();</script>');}

	if (empty($message)) {
		$message = !empty($_GET['message'])?
			$_GET['message']:
			(!empty($_POST['message'])?
				$_POST['message']:'');
	}
	
	$t->set_block('footer', 'TagBlock', 'TGSBlock');
	$t->set_var('TGSBlock','');
	//find out which skin to use
	if (!is_object($objSkins)) {
		if (!class_exists('InteractSkins')) {
			require_once($CONFIG['BASE_PATH'].'/skins/lib.inc.php');
		}
		$objSkins = new InteractSkins();
	}
	$skin_key = $objSkins->getskin_key(null, $page_details['type_key']);
	$t->set_var('SKIN_KEY',$skin_key[0]);
	$t->set_var('SKIN_VERSION',$skin_key[1]);
	$t->set_var('DOCS_URL',$CONFIG['DOCS_URL']);


	$t->set_var('PAGE_TITLE',$CONFIG['SERVER_NAME'].' - Server Admin - '.$page_title);
	$t->set_var('SPACE_TITLE',$CONFIG['SERVER_NAME'].' - Server Admin - '.$page_title);
	$t->set_var('CHARACTER_SET',$general_strings['character_set']);
	$t->set_var('PATH',$CONFIG['PATH']);
	$t->set_var('MESSAGE',strip_tags($message));
	$t->set_var('MESSAGE_COUNT','0');
	$t->set_var('SERVER_NAME',$CONFIG['SERVER_NAME']);
	$t->set_var('FULL_NAME',$current_user);
	$t->set_block('footer', 'UserNoteBlock', 'UNBlock');	
	$t->set_var('UNBlock','');
	$t->set_var('MODULE_TEXT',ucfirst($general_strings['module_text']));
	$t->set_var('HEADER_HOME_LINK',$CONFIG['PATH'].'/spaces/space.php?space_key='.$CONFIG['DEFAULT_SPACE_KEY']);
	$t->set_strings('navigation', $admin_strings);
	$t->set_var('FULL_URL',$CONFIG['FULL_URL']);


}

function get_module_key($link_key)
{
	global $CONN, $CONFIG;
	$sql = "SELECT module_key from {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$link_key'";

	$rs = $CONN->Execute("$sql");
   
	while (!$rs->EOF) {
		$module_key = $rs->fields[0];
		$rs->MoveNext();
	}
	return $module_key;
}
function get_space_key()
{
	if (!empty($_GET['space_key'])) {
		$space_key	= $_GET['space_key'];
	} else if (!empty($_POST['space_key'])){
		$space_key	= $_POST['space_key'];
	}
	if (empty($space_key)) {
		$space_key = $_SESSION['current_space_key'];
	} else if ($space_key!=$_SESSION['current_space_key']){
		$_SESSION['current_space_key'] = $space_key;
	}
	return $space_key;
}

function get_link_key($module_key,$space_key)
{
	global $CONN, $CONFIG;
	
	$link_key 	= $_GET["link_key"];

	if (!$link_key) {

	$sql = "SELECT link_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key' and space_key='$space_key'";
   	$rs = $CONN->Execute("$sql");

   	while (!$rs->EOF) {

	   	$link_key = $rs->fields[0];
	   	$rs->MoveNext();

   	}

}	
	return $link_key;
}

function check_module_link_rights($module_key) 
{

	global $CONN, $CONFIG;
	$current_user_key = $_SESSION['current_user_key'];
	$userlevel_key	= $_SESSION['userlevel_key'];

	if ($userlevel_key=='1') {
	
		return true;
	
	}
	
	$sql = "SELECT edit_rights_key,owner_key FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
	$rs = $CONN->Execute($sql);
		
	while (!$rs->EOF) {
		
		$edit_rights_key = $rs->fields[0]; 
		$owner_key = $rs->fields[1];
		$rs->MoveNext();
			
	}

	$rs->Close();	
	
	if ($owner_key==$current_user_key) {
	
		return true;
		
	} 

   	switch ($edit_rights_key) {
	
		case 1:
		
			return true;
		
		break;
		
		
		case 2: 
		
			return true;
			
		break;
		
		case 3:
	
			$sql = "SELECT user_key From {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE user_key='$current_user_key' AND module_key='$module_key'";
			$rs = $CONN->Execute($sql);
			
			if (!$rs->EOF) {
			
				return true;
				
			} else {
			
				$sql = "SELECT {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}group_user_links,{$CONFIG['DB_PREFIX']}module_edit_right_links WHERE {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key AND ({$CONFIG['DB_PREFIX']}group_user_links.user_key='$current_user_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.module_key='$module_key')";
			
				$rs = $CONN->Execute($sql);
			
				if (!$rs->EOF) {
	
						return true;
				
				} else {
				
					return false;
					
				}
				
			}
			
	   		break;
	
			case 6:
			
				$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE module_key='$module_key'");
				while(!$rs->EOF) {
					if ($CONN->GetOne("SELECT user_key FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE space_key='{$rs->fields[0]}' AND user_key='$current_user_key' AND access_level_key='1'")) {
						return true;
					}
					$rs->MoveNext();
				} 
				return false;
			break;
			default:
			
	 	  		return false;
	 	
			break;
			default:
			
	 	  		return false;
	 	
			break;
	
		}
			
}

function check_module_edit_rights($module_key) 
{

	global $CONN,$accesslevel_key,$group_accesslevel, $CONFIG;

	$current_user_key = $_SESSION['current_user_key'];
	$userlevel_key	= $_SESSION['userlevel_key'];

	if ($userlevel_key=='1') {
		return true;
	}
	
	if($module_key) {
		$sql = "SELECT edit_rights_key,owner_key FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
		
		$rs = $CONN->Execute($sql);
			
		if (!$rs->EOF) {
			
			$edit_rights_key = $rs->fields[0]; 
			$owner_key = $rs->fields[1];
			if ($owner_key==$current_user_key) {
				return true;
			}
	
			switch ($edit_rights_key) {
				case 2:
					return true;
				break;
				
				case 3:
					$sql = "SELECT user_key From {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE user_key='$current_user_key' AND module_key='$module_key' AND edit_level='1'";
					$rs = $CONN->Execute($sql);
					
					if (!$rs->EOF) {
						return true;
					} else {
						$sql = "SELECT {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}group_user_links,{$CONFIG['DB_PREFIX']}module_edit_right_links WHERE {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key AND ({$CONFIG['DB_PREFIX']}group_user_links.user_key='$current_user_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.edit_level='1')";
						$rs = $CONN->Execute($sql);
		
						if (!$rs->EOF) {
							return true;
						} else {
							return false;
						}
					}
				break;
			
				case 6:
					if ($accesslevel_key==1||$accesslevel_key==3||$group_accesslevel==1) {
						return true;
					} else {
						return false;
					}
				break;
		
				default:
					return false;
				break;
			}
		} 
	}
}

function check_module_copy_rights($module_key) 
{

	global $CONN, $CONFIG, $accesslevel_key, $group_accesslevel;
	$current_user_key = $_SESSION['current_user_key'];
	$userlevel_key	= $_SESSION['userlevel_key'];

	if ($userlevel_key=='1') {
	
		return true;
	
	}
	
	$sql = "SELECT edit_rights_key,owner_key FROM {$CONFIG['DB_PREFIX']}modules WHERE module_key='$module_key'";
	$rs = $CONN->Execute($sql);
		
	while (!$rs->EOF) {
		
		$edit_rights_key = $rs->fields[0]; 
		$owner_key = $rs->fields[1];
		$rs->MoveNext();
			
	}

	$rs->Close();	
	
	if ($owner_key==$current_user_key) {
	
		return true;
		
	} 

   	switch ($edit_rights_key) {
	
		case 2:
		
			return true;
		
		break;
		
		
		case 3:
	
			$sql = "SELECT user_key From {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE user_key='$current_user_key' AND module_key='$module_key' AND edit_level='3'";
			$rs = $CONN->Execute($sql);
			
			if (!$rs->EOF) {
			
				return true;
				
			} else {
			
				$sql = "SELECT {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}group_user_links,ModuleEditRightlinks WHERE {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key AND ({$CONFIG['DB_PREFIX']}group_user_links.user_key='$current_user_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.edit_level='3')";
			
				$rs = $CONN->Execute($sql);
			
				if (!$rs->EOF) {
	
						return true;
				
				} else {
				
					return false;
					
				}
				
			}
			
	   		break;
	
			case 6:
			
				if ($accesslevel_key==1||$accesslevel_key==3||$group_accesslevel==1) {
				
					return true;
					
				} else {
				  
					return false;
					
				}
			
			break;
			default:
			
	 	  		return false;
	 	
			break;
	
		}
			
}

function check_link_edit_rights($link_key,$accesslevel_key,$group_accesslevel,$owner_key='',$edit_rights_key='') 
{

	global $CONN, $CONFIG;
	
	$current_user_key = $_SESSION['current_user_key'];
	$userlevel_key	= $_SESSION['userlevel_key'];

	if (empty($link_key)) {
		return false;
	}
	
	if ($userlevel_key=='1') {
	
		return true;
		
	}
					
	if ($owner_key=='' || $edit_rights_key=='') {
	
		$sql = "SELECT edit_rights_key,owner_key FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE link_key='$link_key'";
		
		$rs = $CONN->Execute($sql);
		
		while (!$rs->EOF) {
		
			$edit_rights_key = $rs->fields[0]; 
			$owner_key = $rs->fields[1];
			$rs->MoveNext();
			
		}

		$rs->Close();
		
	}
	
	if ($owner_key==$current_user_key) {
	
		return true;
		
	}

	switch ($edit_rights_key) {
	
  		case 1:
		
			if ($accesslevel_key=='1' || $accesslevel_key=='3' || $group_accesslevel=='1') {
			
				return true;
				
			} else {
			
				return false;
				
			}
		
		break;
		
		case 2: 
		
			$sql = "SELECT user_key From {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE user_key='$current_user_key' AND link_key='$link_key'";
		
			$rs = $CONN->Execute($sql);
			
			if (!$rs->EOF) {
			
				return true;
				
			} else {
			
				$sql = "SELECT {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}group_user_links,{$CONFIG['DB_PREFIX']}module_edit_right_links WHERE {$CONFIG['DB_PREFIX']}module_edit_right_links.group_key={$CONFIG['DB_PREFIX']}group_user_links.group_key AND ({$CONFIG['DB_PREFIX']}group_user_links.user_key='$current_user_key' AND {$CONFIG['DB_PREFIX']}module_edit_right_links.link_key='$link_key')";
			
				$rs = $CONN->Execute($sql);
			
				if (!$rs->EOF) {
			
					return true;
				
				} else {
				
					return false;
					
				}
				
			}
			
		break;
	
		case 4:
			return true;
		break;
		default:
			
			return false;
		
		break;
	
	}
}

function get_date_space_modified($space_key) 
{

	global $CONN, $CONFIG, $objDates, $general_strings;
	
	$sql = "SELECT date_added FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE space_key='$space_key' ORDER BY date_added DESC";
	$rs=$CONN->SelectLimit($sql,1);

	while (!$rs->EOF) {
	
		$date_modified = $rs->fields[0];
		
		$rs->MoveNext();
		
	}
	
	$rs->Close();

	$sql = "SELECT date_modified FROM {$CONFIG['DB_PREFIX']}module_space_links WHERE space_key='$space_key' ORDER BY date_modified DESC";
	$rs=$CONN->SelectLimit($sql,1);

	while (!$rs->EOF) {
	
		$date_modified2 = $rs->fields[0];
		
		if ($date_modified2>$date_modified) {
		
			$date_modified = $date_modified2;
			
		}
			
		$rs->MoveNext();		
		
	}
	
	$rs->Close();	

	$sql = "SELECT date_added FROM {$CONFIG['DB_PREFIX']}news WHERE space_key='$space_key' ORDER BY date_added DESC";
	$rs=$CONN->SelectLimit($sql,1);

	while (!$rs->EOF) {
	
		$date_modified2 = $rs2->fields[0];
		
		if ($date_modified2>$date_modified) {
		
			$date_modified = $date_modified2;
			
		}

		$rs->MoveNext();		
		
	}
	
	$rs->Close();
	
	if ($date_modified=='') {
	
		$date_modified = $general_strings['never'];
		
	} else {

		if (!is_object($objDates)) {

			if (!class_exists('InteractDate')) {

				require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
			}

			$objDates = new InteractDate();

		}
		
		$date_modified = $objDates->formatDate(strtotime($date_modified),'long');
			
	}
	
	return $date_modified;
	
}

	
function check_file_upload($name) 
{
		
	global $CONFIG;
	
	$mb_max_size = $CONFIG['MAX_FILE_UPLOAD_SIZE']/1000000;		

	switch ($_FILES[$name]['error']){
	
		case 0:
			
			return true;
				
		break;
			
		case 1:
			
			return "The uploaded file is too large. Files must not be larger than $mb_max_size MB";
				
		break;

		case 2:
			
			return "The uploaded file is too large. Files must not be larger than $mb_max_size MB";
				
		break;

		case 3:
			
			return "The uploaded file was only partially uploaded. Please try again";
				
		break;
			
		case 4:
			
			return "No file was uploaded, please try again";

		break;

		case 5:

			return "Uploaded file seems to be empty, please try again";
				
		break;

	}
	
} //end check_file_upload


/**
* Validate data returned by a form  
* 
* @param array $field_names an array of field names to check
* @param array $field_data an array of data fields returned by the form
* @param array $string_file array of language strings for current module
* @return $errors array an array of any error messages
*/

function check_form($field_names, $field_data, $string_file)
{
	global $CONN, $CONFIG, $general_strings;
	$errors = array();
	foreach($field_names as $value) {
		if (!isset($field_data[$value]) || $field_data[$value]=='') {
			if (isset($string_file[$value.'_error'])) {
				$errors[$value] = $string_file[$value.'_error'];
			} else if (isset($general_strings[$value.'_error'])) {
				$errors[$value] = $string_file[$value.'_error'];
			}else {
				$errors[$value] = $general_strings['required_field_empty'];
			}
		}
	}
	return $errors;
} //end check_form


function xmlhconn_ok() {
	$browser=browser_get_agent();
	return !($browser['agent']=='IE' && $browser['platform']=='Mac' );
}
class singleton
{

	function getInstance ($class)
    // implements the 'singleton' design pattern.
    {
        global $CONFIG;
    	static $instances = array();  // array of instance names

        if (!array_key_exists($class, $instances)) {
            // instance does not exist, so create it
            require_once $CONFIG['BASE_PATH'].'/includes/lib/'.$class.'.inc.php';
            $class_name = 'Interact'.ucfirst($class);
            $instances[$class] =& new $class_name;
        } 
        $instance =& $instances[$class];
        return $instance;

    } // getInstance

} // singleton


function interact_stripslashes($var) {
	if (empty($var)) {
 		//no need to do anything
 	} else if (is_string($var)) {
 		if (ini_get('magic_quotes_sybase')) { //only need to do single quotes
 			$var = str_replace("''", "'", $var);
  		} else { //do double quotes and backslashes
  			$var = str_replace("\\'", "'", $var);
  			$var = str_replace('\\"', '"', $var);
  			$var = str_replace('\\\\', '\\', $var);
  			$var = str_replace('\r\n', "\r\n", $var);
			$var = str_replace('\n', "\n", $var);
  		}
  	}
  	return $var;
}

function clean_strings(&$strings) {
	safe_ht_call($strings);
	escape_strings($strings);
}

function safe_ht_call(&$strings) {
	global $no_safehtml, $objSafeHTML;

	if (is_array($strings)) {
		$keys=array_keys($strings);
		foreach($keys as $val) {
			if(is_string($strings[$val])) {
				$objSafeHTML->clear();	
				$strings[$val.(in_array($val,$no_safehtml)?'_safe':'')]=$objSafeHTML->parse($strings[$val]);
			} else {
				safe_ht_call($strings[$val]);
			}
		}
	} else {
		if(is_string($strings)) {
			$objSafeHTML->clear();
			$strings=$objSafeHTML->parse($strings);
		}
	}
}


function escape_strings(&$strings) {
	global $CONN;
	if (is_array($strings)) {
		$keys=array_keys($strings);
		foreach($keys as $val) escape_strings($strings[$val]);
	} else {
		if(is_string($strings) && is_resource($CONN->_connectionID)) {
			$strings=mysql_real_escape_string(
				(ini_get("magic_quotes_gpc")?
					interact_stripslashes($strings):
					$strings)
				,$CONN->_connectionID
			);
		}
	}
}

function html_to_xml(&$html,$limit=4095) {
	global $general_strings;

	return str_replace("&", "&amp;",
		str_replace("<", "&lt;",
			substr(
				strip_tags(html_entity_decode(
					preg_replace('/\s?<(br|p)\b[^>]*>\s?/',chr(10),
						str_replace("&nbsp;", " ",
							preg_replace('/\s+/',' ',$html)
						)
					)
				,null,$general_strings['character_set']))
			,0,$limit)
		)
	);
}

?>
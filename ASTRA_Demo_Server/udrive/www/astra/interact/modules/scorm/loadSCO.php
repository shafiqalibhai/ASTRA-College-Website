<?php

require_once("../../local/config.inc.php");
require_once("lib.inc.php");

//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/scorm_strings.inc.php');

//set variables

$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= isset($_GET['group_key'])?$_GET['group_key']:'';
$message	 = isset($_GET['message'])?$_GET['message']:'';
$userlevel_key = isset($_SESSION['userlevel_key'])?$_SESSION['userlevel_key']:'';

//check we have the variables we need
check_variables(true,false,true);

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];
$group_accesslevel = isset($access_levels['group_accesslevel'][$group_key])?$access_levels['group_accesslevel'][$group_key]:'';

$is_admin=(check_module_edit_rights($module_key));


    $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
$rs=$CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key=$module_key");

$scorm=(object)$rs->fields;

$mode=getvar('mode');
$scoid=getvar('scoid');
//$currentorg=getvar('currentorg');

    if (!empty($scoid)) {
    //
    // Direct sco request
    //
	    $rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE id=$scoid");
        if ($rs && !$rs->EOF) {
       	   	$sco=(object)$rs->fields;
            if ($sco->launch == '') {
                // Search for the next launchable sco
                
			    $rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key=$module_key AND launch<>'' AND id>".$sco->id." ORDER BY id ASC");
                if($rs && !$rs->EOF) {
                    $sco = (object)$rs->fields;
                }
            }
        }
    } else {
		$sco=find_a_sco();
	}

    $CONN->SetFetchMode(ADODB_FETCH_NUM);

    $CONN->Close();

    //
    // Forge SCO URL
    //
    $connector = '';
//    $version = substr($scorm->version,0,4);
    if (!empty($sco->parameters)) {
        if (stripos($sco->launch,'?') !== false) {
            $connector = '&';
        } else {
            $connector = '?';
        }
    }
    
	$launcher=$sco->launch.htmlspecialchars($connector.$sco->parameters);
    if (scorm_external_link($sco->launch)) {
        header("Location: ".$launcher);
    } else {
    	header("Location: ".$CONFIG['FULL_URL'].$CONFIG['VIEWFILE_PATH'].$space_key.'/scorm/'.$scorm->file_path.'/'.$launcher);
    }
?>
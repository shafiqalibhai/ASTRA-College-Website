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
    
    if ($usertrack=scorm_get_tracks($scoid,$USER->id)) {
        $userdata = $usertrack;
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }
    $userdata->student_id = $_SESSION['current_user_key'];
    $userdata->student_name = $USER->lastname .', '. $USER->firstname;
    $userdata->mode = 'normal';
    if (isset($mode)) {
        $userdata->mode = $mode;
    }
    if ($userdata->mode == 'normal') {
        $userdata->credit = 'credit';
    } else {
        $userdata->credit = 'no-credit';
    }
    
    if ($sco = get_record('scorm_scoes','id',$scoid)) {
        $userdata->datafromlms = $sco->datafromlms;
        $userdata->masteryscore = $sco->masteryscore;
        $userdata->maxtimeallowed = $sco->maxtimeallowed;
        $userdata->timelimitaction = $sco->timelimitaction;
    } else {
    //    ('Sco not found');
    }

    switch ($scorm->version) {
        case 'SCORM_1.2':
            include_once ('datamodels/scorm1_2.js.php');
        break;
        case 'SCORM_1.3':
            include_once ('datamodels/scorm1_3.js.php');
        break;
        case 'AICC':
            include_once ('datamodels/aicc.js.php');
        break;
        default:
            include_once ('datamodels/scorm1_2.js.php');
        break;
    }
?>

var errorCode = "0";

function underscore(str) {
    return str.replace(/\./g,"__");
}

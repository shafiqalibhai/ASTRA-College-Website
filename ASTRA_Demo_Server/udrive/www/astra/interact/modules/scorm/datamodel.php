<?php

require_once("../../local/config.inc.php");
require_once("lib.inc.php");

if(isset($_SESSION['current_user_firstname'])) {   //don't track if not logged in
	//set variables
	$space_key 	= $_POST['space_key'];
	$module_key	= $_POST['module_key'];
	//$link_key 	= get_link_key($module_key,$space_key);
	//$group_key	= isset($_GET['group_key'])?$_GET['group_key']:'';
	//$message	 = isset($_GET['message'])?$_GET['message']:'';
	$userlevel_key = isset($_SESSION['userlevel_key'])?$_SESSION['userlevel_key']:'';
	$current_user_key = isset($_SESSION['current_user_key'])?$_SESSION['current_user_key']:'';
	//check we have the variables we need
	//check_variables(true,false,true);
	
	//autenticate the user.
	//$access_levels = authenticate();


    if (isset($_POST['scoid'])) {
        $scoid = $_POST['scoid'];
        $result = true;
		$ctime=time(); 
		$total=0;   	
        foreach ($_POST as $element => $value) {
            if (substr($element,0,3) == 'cmi') {
                $element = str_replace('__','.',$element);
                $element = preg_replace('/_(\d+)/',".\$1",$element);
                $rs=$CONN->Execute("SELECT userid FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE userid='{$_SESSION['current_user_key']}' AND module_key='$module_key' AND scoid='$scoid' AND element='$element'");
                if($rs && !$rs->EOF) {
//				if (get_record_select('scorm_scoes_track',"userid='$USER->id' AND scormid='$scorm->id' AND scoid='$scoid' AND element='$element'")) {
					$sql_update = "UPDATE {$CONFIG['DB_PREFIX']}scorm_scoes_track SET value=$value, rime=$ctime} WHERE userid='{$_SESSION['current_user_key']}' AND module_key='$module_key' AND scoid='$scoid' AND element='$element'";
					$result=$CONN->Execute($sql_update)&&$result;
		
				} else {
					$sql_insert = "INSERT INTO {$CONFIG['DB_PREFIX']}scorm_scoes_track(userid,module_key,scoid,element,value,timemodified) VALUES ('{$_SESSION['current_user_key']}', $module_key,$scoid,'$element','$value',$ctime )";
					$result=$CONN->Execute($sql_insert)&&$result;
				}
        	}
        	
        	if($element== 'cmi.core.score.raw') {
        		$total = $total+$value;
        	}
 		}
 		update_gradebook($total,$ctime, $current_user_key, $module_key);
        if ($result) {
            echo "true\n0";
        } else {
            echo "false\n101";
        }
    }
}
?>
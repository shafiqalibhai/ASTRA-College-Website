<?php  // $Id: report.php,v 1.11 2007/01/07 22:25:27 glendavies Exp $

// This script uses installed report plugins to print quiz reports


require_once("../../local/config.inc.php");
require_once("lib.inc.php");

require_once($CONFIG['LANGUAGE_CPATH'].'/scorm_strings.inc.php');

//set variables

$space_key 	= get_space_key();
$module_key	= $_GET['module_key'];
$link_key 	= get_link_key($module_key,$space_key);
$group_key	= isset($_GET['group_key'])?$_GET['group_key']:'';
$message	 = isset($_GET['message'])?$_GET['message']:'';
$userlevel_key = isset($_SESSION['userlevel_key'])?$_SESSION['userlevel_key']:'';

//check we have the variables we need
check_variables(true,true,true);

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];
$group_accesslevel = isset($access_levels['group_accesslevel'][$group_key])?$access_levels['group_accesslevel'][$group_key]:'';

$is_admin=(check_module_edit_rights($module_key));


//get the required templates for this page
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);
$t->set_file(array(
'header'	 => 'header.ihtml',
'navigation' => 'navigation.ihtml',
'footer'	 => 'footer.ihtml'
));


// get details of this page, space name, module name, etc.
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$output='';


$scoid=getvar('scoid');
$user=getvar('user');

    $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
$rs=$CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key=$module_key");

$scorm=(object)$rs->fields;
    $CONN->SetFetchMode(ADODB_FETCH_NUM);
 
    if (!$is_admin) {
        $output.= "You are not allowed to use this script";exit;
    }

        $strscorms = get_string("modulenameplural", "scorm");
        $strscorm  = get_string("modulename", "scorm");
        $strreport  = get_string("report", "scorm");
        $strname  = get_string('name');

		if (!class_exists('InteractUser')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');
	
		}

		$userobj = new InteractUser();

$t->parse('CONTENTS', 'header', true);
$t->set_var('BREADCRUMBS',$scorm_strings['report'],true);

get_navigation();

$t->set_var('CONTENTS','<h2 id="moduleHeading"><img src="'.$CONFIG['PATH'].'/images/scorm.gif" height="16" width="16" alt="Icon"> '.$page_details['module_name'].': '.$scorm_strings['report']."</h2><div align=\"center\"><a href=\"scorm.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key\">{$general_strings['back_to']} {$page_details['module_name']}</a>",true);



//    add_to_log($course->id, "scorm", "report", "report.php?id=$cm->id", "$scorm->id");

/// Print the page header
/*    if (empty($noheader)) {
        if ($course->category) {
            $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        } else {
            $navigation = '';
        }

        if (!empty($id)) {
            print_header("$course->shortname: ".format_string($scorm->name), "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strscorms</a>
                      -> <a href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a> -> $strreport",
                     "", "", true);
        } else {
            print_header("$course->shortname: ".format_string($scorm->name), "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strscorms</a>
                      -> <a href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>
              -> <a href=\"report.php?id=$cm->id\">$strreport</a> -> $sco->title",
                     "", "", true);
        }
        print_heading(format_string($scorm->name));
    }
*/    if (is_null($user)) {

//	if (!empty($module_key)) {   //check variable already redirects if module_key is missing.
   $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
    $scoArray = $CONN->GetAssoc("SELECT id,title FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key=$module_key AND launch>'' order by id ASC");
    $rs= $CONN->Execute("SELECT DISTINCT userid FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE module_key=$module_key ORDER BY userid");
    $CONN->SetFetchMode(ADODB_FETCH_NUM);

	if ($scoArray) {
	
	    $tabl="<table><tr><th>$strname</th>";
    	foreach($scoArray as $title) {
    		$tabl.="<th>$title&nbsp;&nbsp;</th>";
    	}
    	$tabl.="</tr>";


			while ($rs && !$rs->EOF) {
				$scouser=(object)$rs->fields;
				$uid=$scouser->userid;

				
$tabl.="<tr><td>".get_badge($uid);
   reset($scoArray);
	do {
	$tabl.="<td>";
	//	while (current($scoArray) && $scouser->userid=$uid) {
			                    $anchorstart = '';
                                $anchorend = '';
                                $scoreview = '';
                                if ($trackdata = scorm_get_tracks(key($scoArray),$uid)) {
                                    if ($trackdata->score_raw != '') {
                                        $scoreview = '<br />'.get_string('score','scorm').':&nbsp;'.$trackdata->score_raw;
                                    }
                                    if ($trackdata->status == '') {
                                        $trackdata->status = 'notattempted';
                                    } else {
                                        $anchorstart = "<a href=\"report.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&scoid=".key($scoArray)."&user=$uid\" title=\"".get_string('details','scorm').'">';
                                        $anchorend = '</a>';
                                    }
                                } else {
                                    $trackdata->status = 'notattempted';
                                    $trackdata->total_time = '';
                                }
                                $strstatus = get_string($trackdata->status,'scorm');
                                
                                $tabl .= $anchorstart.'<img style="border:none" src="pix/'.$trackdata->status.'.gif" alt="'.$strstatus.'" title="'.$strstatus.'">&nbsp;'.$trackdata->total_time.$scoreview.$anchorend;

		
	$tabl.="</td>";




}while (next($scoArray));
$tabl.='</tr>';
$rs->MoveNext();
                }
                $output.= $tabl.'</table>';
            } else {
                $output.=('No users to report');
            }
//        } else {
//            $output.=('Missing script parameter');
//        }
    } else {
//            if ($userdata = scorm_get_user_data($user)) {
//                print_simple_box_start('center');
//                print_heading(format_string($sco->title));
                $output.= '<div align="center"><table><tr><th colspan="2">'.$CONN->GetOne("SELECT title FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE id=$scoid").'</th></tr><tr><td colspan="2">'.get_badge($user).'</td></tr><tr><td colspan="2">';
//                print_user_picture($user, $course->id, $userdata->picture, false, false);
//                $output.= "<a href=\"$CFG->wwwroot/user/view.php?id=$user&course=$course->id\">".
//                     "$userdata->firstname $userdata->lastname</a><br />";
                $scoreview = '';
                if ($trackdata = scorm_get_tracks($scoid,$user)) {
                    if ($trackdata->score_raw != '') {
                        $scoreview = get_string('score','scorm').':&nbsp;'.$trackdata->score_raw;
                    }
                    if ($trackdata->status == '') {
                        $trackdata->status = 'notattempted';
                    }
                } else {
                    $trackdata->status = 'notattempted';
                    $trackdata->total_time = '';
                }
                $strstatus = get_string($trackdata->status,'scorm');
                $output.= '<img src="pix/'.$trackdata->status.'.gif" alt="'.$strstatus.'" title="'.
                $strstatus.'">&nbsp;'.$trackdata->total_time.'<br />'.$scoreview.'<br /><br /></td></tr>';
                foreach($trackdata as $element => $value) {
                    if (substr($element,0,3) == 'cmi') {
                   	  	$output.='<tr><th>';
                    	if (substr($element,0,8) == 'cmi.core') {
                    		$output.= substr($element,9);
                    	} else {
                        	$output.= substr($element,4);
                    	}
               			$output.= '&nbsp;</th><td>'.$value.'</td></tr>';
                    }
                }
                $output.= '</table></div>'."\n";

                //                print_simple_box_end();
//            }
		$t->set_var('CONTENTS',"&nbsp;&nbsp;&nbsp;<a href=\"report.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key\">{$general_strings['back_to']} {$scorm_strings['report']}</a>",true);
    }

$t->set_var('CONTENTS',"</div><br />$output",true);
$t->parse('CONTENTS', 'footer', true);

//output page
$t->p('CONTENTS');
exit;


function get_badge($uid) {
	global $CONFIG,$CONN,$userobj,$space_key;

	$rs=$CONN->Execute("SELECT first_name,last_name FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$uid'");
	return '<span style="float:left">'.$userobj->getUserphotoTag($uid, '35', $space_key)."</span>&nbsp;".$rs->fields[0].' '.$rs->fields[1];
}

?>
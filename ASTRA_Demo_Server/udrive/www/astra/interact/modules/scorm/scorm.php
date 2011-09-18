<?php  
// converted from view.php,v 1.28.2.1 2005/07/03 07:12:36 bobopinna Exp
//


/// This page prints a particular instance of scorm

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
check_variables(true,true,true);

//autenticate the user.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

$group_access = $access_levels['groups'];
$group_accesslevel = isset($access_levels['group_accesslevel'][$group_key])?$access_levels['group_accesslevel'][$group_key]:'';

$is_admin=(check_module_edit_rights($module_key));

//update statistics
statistics('read');

//get the required templates for this page
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);
$t->set_file(array(
'header'	 => 'header.ihtml',
'navigation' => 'navigation.ihtml',
'footer'	 => 'footer.ihtml'
));

$t->set_var('MODULE_STYLES','<link rel="stylesheet" href="styles.css" type="text/css"><script type="text/javascript" language="javascript">

function playSCO(scoid) {
    nf=document.getElementById("navform");
    nf.scoid.value=scoid;
    nf.submit();
}
</script>',true);


// get details of this page, space name, module name, etc.
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
//set page variables
$t->parse('CONTENTS', 'header', true);

//create the left hand navigation
get_navigation();

//    optional_variable($id);    // Course Module ID, or
//    optional_variable($a);     // scorm ID

$CONN->SetFetchMode(ADODB_FETCH_ASSOC);
$rs=$CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key=$module_key");

$scorm=(object)$rs->fields;

/*     if ($id) { */
/*         if (! $cm = get_record("course_modules", "id", $id)) { */
/*             error("Course Module ID was incorrect"); */
/*         } */
/*         if (! $course = get_record("course", "id", $cm->course)) { */
/*             error("Course is misconfigured"); */
/*         } */
/*         if (! $scorm = get_record("scorm", "id", $cm->instance)) { */
/*             error("Course module is incorrect"); */
/*         } */
/*     } else { */
/*         if (! $scorm = get_record("scorm", "id", $a)) { */
/*             error("Course module is incorrect"); */
/*         } */
/*         if (! $course = get_record("course", "id", $scorm->course)) { */
/*             error("Course is misconfigured"); */
/*         } */
/*         if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) { */
/*             error("Course Module ID was incorrect"); */
/*         } */
/*     } */
/*  */
/*     require_login($course->id, false, $cm); */
/*  */
/*     if (isset($SESSION->scorm_scoid)) { */
/*         unset($SESSION->scorm_scoid); */
/*     } */

$strscorms = get_string("modulenameplural", "scorm");
$strscorm  = get_string("modulename", "scorm");

/*     if ($course->category) { */
/*         $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> */
/*                        <a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->"; */
/*     } else { */
/*         $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->"; */
/*     } */
/*  */
/*     $pagetitle = strip_tags($course->shortname.': '.format_string($scorm->name)); */
/*  */
/*     add_to_log($course->id, 'scorm', 'pre-view', 'view.php?id='.$cm->id, "$scorm->id"); */

//
// Print the page header
//
//    if (!$cm->visible and !isteacher($course->id)) {
/*         print_header($pagetitle, "$course->fullname", "$navigation ".format_string($scorm->name), '', '', true, */
/*                      update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm)); */
/*         notice(get_string('activityiscurrentlyhidden')); */
/*     } else { */
/*         print_header($pagetitle, "$course->fullname", */
/*                      "$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>", */
/*                      '', '', true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm)); */

if ($is_admin) {
	if (($rcount=$CONN->GetOne("SELECT COUNT(DISTINCT(userid)) FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE module_key=$module_key")) > 0) {
		$replink="<a  href=\"report.php?space_key=$space_key&module_key=$module_key&link_key=$link_key\">".sprintf($scorm_strings['viewallreports'],$rcount).'</a>';
	} else {
		$replink=$scorm_strings['noreports'];
	}
}
// Print the main part of the page

//        print_heading(format_string($scorm->name));

//        print_simple_box(format_text($scorm->summary), 'center', '70%', '', 5, 'generalbox', 'intro');

/*       if (isguest()) {
print_heading(get_string("guestsno", "scorm"));
print_footer($course);
exit;
}
*/
//       print_simple_box_start('center');

$output='';
//$output.=get_string('coursestruct','scorm');
$rs=$CONN->Execute("SELECT id,title FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key=$module_key AND organization='' AND launch='' ORDER BY id");
$organization = getvar("organization");
if(empty($organization)) {$organization=$scorm->launch;}

if ($rs && $rs->RecordCount()>1) {
	if(empty($organization)){$organization=$rs->fields['id'];}
	$output.="<div class='center'>".get_string('organizations','scorm')."<form name='changeorg' method='post' action='scorm.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key'>\n                     <select id='menuorganization' name='organization' onchange='submit()'>"; 

                while (!$rs->EOF){
                	$output.= "<option value='{$rs->fields['id']}'".($organization==$rs->fields['id']?' selected="selected"':'').">{$rs->fields['title']}</option>";
                	$rs->MoveNext();
                }
                $output.= '</select></form> </div>';
}

$orgidentifier = '';

// ?? if organization is a scorm_sco id, then try to get the proper identifier and go to that org ??   wacky.
if ($rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE id=$organization")) {
	$org=(object)$rs->fields;
	if (($org->organization == '') && ($org->launch == '')) {
		$orgidentifier = $org->identifier;
	} else {
		$orgidentifier = $org->organization;
	}
}
$CONN->SetFetchMode(ADODB_FETCH_NUM);

$output2='';
$incomplete = scorm_display_structure($output2,$scorm,'structurelist',$orgidentifier);

$output3='';
if ($scorm->browsemode) {
	$output3.=get_string("mode","scorm");
	$output3.=': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse','scorm').'</label>'."\n";
	if ($incomplete === true) {
		$output3.= '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal','scorm')."</label>\n";
	} else {
		$output3.= '<input type="radio" id="r" name="mode" value="review" checked="checked" /><label for="r">'.get_string('review','scorm')."</label>\n";
	}
	$output3.='<br />';
} else {
	if ($incomplete === true) {
		$output3.= '<input type="hidden" name="mode" value="normal" />'."\n";
	} else {
		$output3.= '<input type="hidden" name="mode" value="review" />'."\n";
	}
}



//$t->parse('GENERAL_SETTINGS', 'general', true);

$t->set_var('CONTENTS','
	<div align="center">
		<div class="message">'.$message.'</div>
	
<div align="right">'.$replink.'</div>'.$output.'
<table><tr><td width="310" align="left">
'."<div id='SPECIAL_navList' style='padding:10px'>".$output2.'</div></td></tr></table>
        <form name="navform" id="navform" method="post" action="playscorm.php?space_key='.$space_key.'&module_key='.$module_key.'&link_key='.$link_key.'&group_key='.$group_key.'">
'.$output3.'<input type="hidden" name="scoid" />
            <input type="hidden" name="currentorg" value="'.$orgidentifier.'" />
            <input type="submit" value="'.$scorm_strings['entercourse'].'" />
        </form>
	</div>
',true);

$t->parse('CONTENTS', 'footer', true);

//output page
$t->p('CONTENTS');
$CONN->Close();
?>
<?PHP  // $Id: playscorm.php,v 1.34 2007/05/21 02:04:19 websterb4 Exp $

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



//get the required templates for this page
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);
$t->set_file(array(
'header'	 => 'header.ihtml',
'navigation' => 'navigation.ihtml',
'footer'	 => 'footer.ihtml'
));

    $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
$rs=$CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm WHERE module_key=$module_key");

$scorm=(object)$rs->fields;
    $CONN->SetFetchMode(ADODB_FETCH_NUM);
 
    $strscorms = get_string('modulenameplural', 'scorm');
    $strscorm  = get_string('modulename', 'scorm');


    //
    // Checkin script parameters
    //
    $modestring = '';
    $scoidstring = '';
    $currentorgstring = '';
    $currentwidth=getvar('currentwidth');
if($mode=getvar('mode')) {
        $modestring = '&mode='.$mode;
}

$scoid = getvar('scoid');
if (empty($scoid)) {
	$sco=find_a_sco();
	$scoid=$sco->id;
}
$scoidstring = '&scoid='.$scoid;


 if(   $currentorg = getvar('currentorg')) {
    $currentorgstring = '&currentorg='.$currentorg;
 }

    $strexpand = get_string('expcoll','scorm');
    $strpopup = get_string('popup','scorm');


    $sco = scorm_display_structure($output,$scorm,'structurelist',$currentorg,$scoid,$mode,true);

    if ($mode == 'normal') {
    if ($trackdata = scorm_get_tracks($USER->id,$sco->id)) {
        if (($trackdata->status == 'completed') || ($trackdata->status == 'passed') || ($trackdata->status == 'failed')) {
        $mode = 'review';
        }
    }
    }
//    add_to_log($course->id, 'scorm', 'view', "playscorm.php?id=$cm->id&scoid=$sco->id", "$scorm->id");
    $scoidstring = '&scoid='.$sco->id;
    $modestring = '&mode='.$mode;




// get details of this page, space name, module name, etc.
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

$t->set_var('MODULE_STYLES','<link rel="stylesheet" href="styles.css" type="text/css">

<script language="JavaScript" type="text/javascript" src="request.js"></script>
<script language="JavaScript" type="text/javascript">


var exit_scorm=false;

var i_obj=null;
var i_minw='.$scorm->width.';
var i_minh='.$scorm->height.';
var marginw=20;  //right-hand margin
var marginh=25;  //bottom margin
var pagefootersize;


function playSCO(scoid) {
	if(i_obj) {i_obj.src="empty.html";}
    nf=document.getElementById("navform");
    nf.scoid.value=scoid;
    nf.submit();
}

function innersize() {
	if (self.innerHeight) // all except Explorer
	{
			w = self.innerWidth;
			h = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight)
			// Explorer 6 Strict Mode
	{
			w = document.documentElement.clientWidth;
			h = document.documentElement.clientHeight;
	}
	else if (document.body) // other Explorers - pesky IE needs 2 checks.
	{
			w = document.body.clientWidth;
			h = document.body.clientHeight;
	}
	return {w:w,h:h}
}


function getpos(i_obj) {
	var curleft = 0;
	var curtop = 0;
	if (i_obj.offsetParent)
	{
		while (i_obj.offsetParent)
		{
			curleft += i_obj.offsetLeft;
			curtop += i_obj.offsetTop;

			i_obj = i_obj.offsetParent;
		}
	} else if (i_obj.x) {
		curleft+=i_obj.x;
		curtop+=i_obj.y;
	}
	return {x:curleft,y:curtop}
}


function stretch_obj() {
 if(i_obj.src.substr(-10)!="empty.html") {
 
	var setw=i_minw+marginw;
	var seth=i_minh+marginh;

	var size=innersize();
	var posxy=getpos(i_obj);

//now how much more space do we need...	
	var w=setw-(size.w-posxy.x);
	var h=seth-(size.h-posxy.y);

//alert("pos is "+posxy.x+"x"+posxy.y+"\nspace we need:"+w+"x"+h);
	
//allow i_obj to grow beyond minimum size
	if(w<0) {i_obj.width=i_minw-w;w=0;} else {i_obj.width=i_minw;}
   	document.getElementById("navform").currentwidth.value=i_obj.width;

	if(i_obj.contentWindow) {

		var scheight=i_obj.contentWindow.document.body.scrollHeight;
		var newh=Math.max(Math.max(i_minh,scheight),(i_minh-h)-pagefootersize);
		if(newh!=i_obj.height) {
			i_obj.height=newh;}
	}

	if(h<0) {h=0;}

	return {w:w,h:h}
	}
	return {w:100,h:100}
}


function resizetofit(frame_obj) {
	i_obj=frame_obj;
	i_obj.height=i_minh;
	if(footer_obj=document.getElementById("footer")) {
		pagefootersize=footer_obj.offsetHeight;
	} else {pagefootersize=60;}
	
	if(i_obj.contentWindow) {
		i_minh=Math.max(i_minh,i_obj.contentWindow.document.body.scrollHeight+16);
	}

	bloat=stretch_obj();
	
	'.($currentwidth?'/* window resize code skipped, as window has been resized already.*/
':'
	
	var w=bloat.w;
	var h=bloat.h;
	
	if (w || h) {
		var x=self.screenLeft;	if (x==undefined) {x=self.screenX;}
		var y=self.screenTop;	if (y==undefined) {y=self.screenY;}
//alert("x is "+x+"  y is "+y);

		var mx=Math.max(-x,-w);
		var my=Math.max(-y,-h);

		var size=innersize();
		if (mx || my) {
//			top.window.moveBy(mx,my);
top.window.moveTo(x+mx,y+my);		
			var size2=innersize();
			if (size2.w!=size.w || size2.h!=size.h) {

				//stupid browser resized when I said move!!  darn Firefox!?
				//re-check required resize
				
				w=Math.max(0,w-(size2.w-size.w));
				h=Math.max(0,h-(size2.h-size.h));
				if (!w && !h) {return;}
			}
		}
		
		ie_size_snap=false;
		if(navigator.userAgent.toLowerCase().indexOf("msie") + 1) {
		//ie snap to full width/height to stop stupid oversized windows.
			if ((w+size.w>screen.availWidth-33) || (h+size.h>screen.availHeight-202))
				ie_size_snap=true;
		}
		
		if (!ie_size_snap) {
			top.window.resizeBy(w,h);
			size2=innersize();
		}
		if (ie_size_snap || (size2.w==size.w && size2.h==size.h)) {
		                    //not enough space so browser refused to try - stubborn Safari??
		//go big...
top.window.resizeTo(Math.min(screen.availWidth,size.w+w+20),Math.min(screen.availHeight,size.h+h+150));
			stretch_obj();
		}
	}').'
	top.window.onresize=stretch_obj;
}
               </script>
               <script language="JavaScript" type="text/javascript" src="api.php?module_key='.$module_key.$scoidstring.$modestring.'"></script>    <script language="javascript" type="text/javascript">
    <!--

/*        function popup(win,image) {
            win = window.open("loadSCO.php?id= $cm->id.$scoidstring.$modestring ","","width= $scorm->width ,height= $scorm->height ,scrollbars=1");
            image.src = "pix/popdown.gif";
            return win;
        }*/

        function prevSCO() {
            playSCO('.$sco->prev.');
        }

        function nextSCO() {
        	'.(($sco->next==0)?'':'if (!exit_scorm) {playSCO('.$sco->next.');}').'}

    -->
    </script>',true);

$t->set_var('SCRIPT_BODY','onunload="exit_scorm=true" onbeforeunload="exit_scorm=true"',true);


//set page variables
$t->parse('CONTENTS', 'header', true);

$pbutton='<input name="prev" class="smallbutton" type="'. ((($sco->prev == 0) || ($sco->showprev == 1))? 'hidden':'button').'" value="'.get_string('prev','scorm').'" onClick="prevSCO();" />';
$nbutton='<input class="smallbutton" name="next" type="'. ((($sco->next == 0) || ($sco->shownext == 1))?'hidden':'button').'" value="'.get_string('next','scorm').'" onClick="nextSCO();" />';

$t->set_var('SPECIAL_NAV', '<ul class="navlevel"><li><strong  style="background-image: url(pix/scorm2.gif)" id="activelink" class="navlinks"'.($is_admin?' onMouseOver="showhide(this,\'admin_'.$module_key.'\',\'visible\')" onmouseout="showhide(this,\'admin_'.$module_key.'\',\'hidden\')"':'').'>'.$page_details['module_name'].'</strong></li></ul><div id="SPECIAL_navList">'.$output.'<form name="navform" id="navform" method="post" action="playscorm.php?'."space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key".'" target="_top">
                   <input name="scoid" type="hidden" />
                   <input name="currentorg" type="hidden" value="'.$currentorg.'" />
                   <input name="mode" type="hidden" value="'.$mode.'" />
                   <input name="currentwidth" type="hidden" />
                   <table cellspacing=0 cellpadding=0 width="99%" style="border:0px;"><tr><td align="left">'.$pbutton.'</td><td align="right">'.$nbutton.'</td></tr></table>                   <div align="center"><input class="smallbutton" name="exit" style="margin:0px;" type="button" value="'.get_string('exit','scorm').'" onClick="document.location=\'scorm.php?'."space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key".'\'" /></div></form></div>');

$t->set_var('BREADCRUMBS',$sco->title,true);
//create the left hand navigation
get_navigation(false);


$t->set_var('CONTENTS',($mode == 'browse' ? '<span class="message">'.get_string('browsemode','scorm').'</span><br />' : '').'<div style="margin-top:4px;margin-bottom:0px;display:block;width:'.$scorm->width.'px"><span style="float:right">'.$nbutton.'</span>'.$pbutton.'</div>
        <iframe class="scormiframe" onload="resizetofit(this)" name="main" width="'.($currentwidth?$currentwidth:$scorm->width).'" height="'.$scorm->height.'" src="loadSCO.php?module_key='.$module_key.$scoidstring.$modestring.'"></iframe>',true);

$t->parse('CONTENTS', 'footer', true);

//output page
$t->p('CONTENTS');
?>

</body>
</html>

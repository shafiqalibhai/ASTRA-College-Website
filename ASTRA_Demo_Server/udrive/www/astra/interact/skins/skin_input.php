<?php

/**
* Skin input page
*
* Display the page for adding and modifying new skins 
*
* 
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');

//get language strings
require_once($CONFIG['LANGUAGE_CPATH'].'/skin_strings.inc.php');

//set variables
$space_key 	= get_space_key();
$current_user_key	= $_SESSION['current_user_key'];
$action = isset($_POST['action'])?$_POST['action']:'';
$message = isset($_GET['message'])?$_GET['message']:'';

if ($_SERVER['REQUEST_METHOD']=='GET') {
	$referer = isset($_GET['referer'])?urldecode($_GET['referer']):'';
} else {
	$referer = isset($_POST['referer'])?urldecode($_POST['referer']):'';
}
//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];

if (!isset($objSkins)) {
	if (!class_exists('InteractSkins')) {
		require_once('lib.inc.php');
	}
	$objSkins = new InteractSkins();
}

if ($_SERVER['REQUEST_METHOD']='POST') {

	switch ($_POST['action']) {
		
		case 'add':
			$skin_data = $_POST;
			if(!isset($skin_data['scope_key'])) {
				$skin_data['scope_key']=0;
			}
			if (!isset($_POST['name']) || $_POST['name']=='') {
				$name_error = sprint_error($general_strings['no_name']);
				$message = $general_strings['problem_below'];
			} else {
				$add = $objSkins->addSkin($skin_data);
				if ($add===true) {
					$message = urlencode($general_strings['add_success']);
					header("Location: {$CONFIG['SERVER_URL']}$referer&space_key=$space_key&message=$message&set_skin_key=".$skin_data['skin_key']."#skin");
				} else {
					$message = $general_strings['problem_below'].'<br />'.$add;
				}	
			}			
		break;
		
		case 'modify':
			if ($objSkins->checkEditRights($_POST['skin_key'])===false) {
				$message = urlencode($general_strings['no_edit_rights']);
				header("Location: {$CONFIG['SERVER_URL']}$referer&space_key=$space_key&message=$message#skin");
			}
			$skin_data = $objSkins->getSkinData($_POST['skin_key']);
		break;
		
		case 'modify2':
			
			if ($objSkins->checkEditRights($_POST['skin_key'])===false) {
				$message = urlencode($general_strings['no_edit_rights']);
				header("Location: {$CONFIG['SERVER_URL']}$referer&space_key=$space_key&message=$message#skin");
			}
			switch($_POST['submit']) {
				case $general_strings['modify']:
					
					if (!isset($_POST['name']) || $_POST['name']=='') {
						$name_error = sprint_error($general_strings['no_name']);
						$message = $general_strings['problem_below'];
					} else {
						
						$modify = $objSkins->modifySkin($_POST);
						if ($modify==true) {
							$message = urlencode($general_strings['modify_success']);
							header("Location: {$CONFIG['SERVER_URL']}$referer&space_key=$space_key&message=$message#skin");
						} else {
							$message = $general_strings['problem_below'].'<br />'.$modify;
						}	
					}			
				break;
		
				case $general_strings['delete']:
					$delete = $objSkins->deleteSkin($_POST['skin_key']);
					if ($delete==true) {
						$message = urlencode($general_strings['delete_success']);
						header("Location: {$CONFIG['SERVER_URL']}$referer&space_key=$space_key&message=$message#skin");
					} else {
						$message = $general_strings['problem_below'].'<br />'.$delete;
					}
				break;
			}
		break;
	}
}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		=> 'header.ihtml',
	'navigation'	=> 'navigation.ihtml',
	'form'			=> 'skins/skin_input.ihtml',
	'footer'		=> 'footer.ihtml'
));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
$page_details['breadcrumbs'] = $page_details['breadcrumbs'].' <a href="skin_select.php?space_key='.$space_key.'">Skins</a> &raquo;';
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!isset($action) || $action=='' || $action=='add') {
	$t->set_var('ACTION','add');
	$t->set_var('SUBMIT_BUTTON',$general_strings['add']);
	$t->set_block('form', 'DeleteBlock', 'DelBlock');
	$t->set_var('DelBlock','');
} else {
	$t->set_var('ACTION','modify2');
	$t->set_var('SUBMIT_BUTTON',$general_strings['modify']);
}
//generate the template menu

$d = dir($CONFIG['BASE_PATH'].'/local/skins');
$templates_array = array();
while (false !== ($entry = $d->read())) {
	if($entry == "." || $entry == "..") { 
		continue; 
	} 
	if (is_dir($CONFIG['BASE_PATH'].'/local/skins/'.$entry) && file_exists($CONFIG['BASE_PATH'].'/local/skins/'.$entry.'/interactstyle.css')) {
		$templates_array[$entry] = $entry;
	}
}
$d->close();
if (!is_object($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once('../../includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}

// foreach($templates_array as $value) {
// 	$t->set_var('MODULE_STYLES','<link rel="alternate stylesheet" type="text/css" href="'.$CONFIG['PATH'].'/skins/skin.php?template='.$value.'" title="'.$value.'" />',true);
// }
$templates_menu = $objHtml->arrayToMenu($templates_array,'template',(!empty($skin_data['template'])?$skin_data['template']:'default'),false,'',false,'id="template" onChange="refreshStyleSheet(this.value)"');

$t->set_var('SCRIPT_INCLUDES','<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/dialogs.js" ></script>
<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/use_dialogs.js" ></script>

<script language="javascript">
pop_path=fullUrl+"/includes/editor/popups/insert_image.php?basic=1";
function insert_image(src,w,h) {
	var outparam = null;
	
	if (src) {
		outparam = {
		f_url : src}

		if (w) outparam.f_width=w;
		if (h) outparam.f_height=h;
	}
	
	Dialog(pop_path, function(param) {
		if (!param) {	// user must have pressed Cancel
			return false;
		}
		document.getElementById("header_logo").value=param.f_url;
		if(param.f_width) {document.getElementById("header_logo_width").value=param.f_width+"px";}
		if(param.f_height) {
			document.getElementById("header_logo_height").value=param.f_height+"px";
			document.getElementById("header_height").value=param.f_height+"px";
		}
		document.getElementById("header_logo").focus();
	}, outparam);
};

function dumpCSS(item) {
	var cssbox=document.getElementById("raw_css");
	var dumpval="";
	
	switch(item) {


case "inline_personal_box":
	dumpval="#personalBox {"+String.fromCharCode(13)+"	position: static; float:left; width:15.5em;"+String.fromCharCode(13)+"	padding:0 0 .5em 7px; margin:0;"+String.fromCharCode(13)+"}"+String.fromCharCode(13)+"* html #personalBox {position:absolute; left:0em; top:auto;}"+String.fromCharCode(13)+"#navigationBox {clear:left;}"+String.fromCharCode(13)+"* html #navigationBox {top:auto; margin-top:6.1em;}";
	break;
		
	
case "nav_buttons":
	dumpval="ul.navlevel .navHeadingLI {background-image:none;}"+String.fromCharCode(13)+".button, .inlineButton, .folderNavigation a, ul.navlevel .navlinks {"+String.fromCharCode(13)+"	border:1px solid;border-color:{COLOR2*140} {COLOR2*105} {COLOR2*67} {COLOR2*150};"+String.fromCharCode(13)+"	text-decoration:none;"+String.fromCharCode(13)+"}"+String.fromCharCode(13)+".button, .inlineButton, .folderNavigation a, ul.navlevel li {"+String.fromCharCode(13)+"	background-color:{COLOR2*140};"+String.fromCharCode(13)+"	background-image:url({" + "SKIN_PATH}/images/button_bg.png) !important;"+String.fromCharCode(13)+"	background-image:url({" + "SKIN_PATH}/images/button_bg.gif);"+String.fromCharCode(13)+"	background-repeat: repeat-x;background-position:top left;"+String.fromCharCode(13)+"}"+String.fromCharCode(13)+"ul.navlevel .navSpacer {background:transparent none !important;}"+String.fromCharCode(13)+String.fromCharCode(13)+"/* IE<7 hacks for buttons - widths are not quite .4ems apart due to IE border handling */"+String.fromCharCode(13)+"/* hide from MacIE \*/"+String.fromCharCode(13)+"* html #navList .navlevel .navlinks, * html .spaceMapButton {width:12.37em;}"+String.fromCharCode(13)+"* html #navList .navlevel .navlevel .navlinks {width:11.97em;}"+String.fromCharCode(13)+"* html #navList .navlevel .navlevel .navlevel .navlinks {width:11.63em;}"+String.fromCharCode(13)+"* html #navList .navlevel .navlevel .navlevel .navlevel .navlinks {width:11.2em;}"+String.fromCharCode(13)+"/* end hide */";
	break;
		
	}
	

	if(dumpval) {
		dumpval="/* START "+item+" */"+String.fromCharCode(13)+dumpval+String.fromCharCode(13)+"/* END "+item+" */"+String.fromCharCode(13);

		var re=new RegExp("/[*] START "+item+" [*]/[^]+/[*] END "+item+" [*]/");
		if(re.test(cssbox.value)) {
			cssbox.value=cssbox.value.replace(re,dumpval);
		} else {
			cssbox.value=cssbox.value+dumpval+String.fromCharCode(13);
		}
	}
}

function skinChangeStyle(element, input, elementStyle) {
	value = document.getElementById(input).value;
	
	if(element=="serverNameLink" && elementStyle=="color") {
		if(value=="none") {
			elementStyle="display";
		} else {
			changeStyle(element,"","display");
		}
	}
	
	if(elementStyle=="borderColor") {
		elementStyle="border";
		if(value) value="1px solid "+value;
	}
	if(elementStyle=="height") {
		if(!value.match(/[0-9]+[%a-z]+/) && value!="") {
			value=parseInt(value)+"px";
			document.getElementById(input).value=value;
		}
	}

	if(elementStyle=="backgroundColor" && element=="navigationBox") {
		var bval=(value?"none":"");
		changeStyle("navigationBox",bval,"backgroundImage");
		changeStyle("navigation",bval,"backgroundImage");
	}
	
	if(elementStyle=="backgroundImage") {
		var bval=(value?"0 0":"");
		changeStyle(element,bval,"backgroundPosition");

		if(element=="logo") {
			if(value=="none") {
				elementStyle="display";
			} else {
				changeStyle(element,"","display");
				if(value) {
					changeStyle(element,document.getElementById("header_logo_width").value,"width");
					changeStyle(element,document.getElementById("header_logo_height").value,"height");
	 			} else {
					changeStyle(element,"","width");
					changeStyle(element,"","height");
 				}
 			}
			changeStyle("header",document.getElementById("header_height").value,"height");
		}
	}
	
	changeStyle(element, value, elementStyle);
}

function refreshStyleSheet() {
	
	var nc1=document.getElementById("colour1").value.substr(1);
	var nc2=document.getElementById("colour2").value.substr(1);
	
	var tnode=document.getElementById("template");
	var t=tnode.options[tnode.selectedIndex].value;

	if(nc1!=this.oc1 || nc2!=this.oc2 || t!=this.ot) {
		this.oc1=nc1;this.oc2=nc2;this.ot=t;
		changeStyleSheet(t,"c1="+nc1+"&c2="+nc2);
	}
}

</script>
');

if (isset($_POST['skin_key'])) {
	$t->set_var('SKIN_KEY',$_POST['skin_key']);
}
$t->parse('CONTENTS', 'header', true);
$font_array = array(''=>'Default','Arial, Helvetica, sans-serif'=>'Arial, Helvetica, sans-serif',
'Verdana, Arial, Helvetica, sans-serif'=> 'Verdana, Arial, Helvetica, sans-serif',
'Georgia, Times New Roman, Times, serif'=> 'Georgia, Times New Roman, Times, serif',
'Geneva, Arial, Helvetica, sans-serif'=> 'Geneva, Arial, Helvetica, sans-serif');
$default_font = isset($skin_data['body_font'])?$skin_data['body_font']:'Default';
$font_menu = $objHtml->arrayToMenu($font_array,'body_font',$default_font,false,'',false,"id=\"body_font\" onChange=skinChangeStyle('pageBody','body_font','fontFamily')");

$t->set_var('NAME_ERROR',isset($name_error)?$name_error:'');

$t->set_var('TEMPLATES_MENU',$templates_menu);

//remove skope flag if user not server admin
if ($_SESSION['userlevel_key']!=1){
	$t->set_block('form', 'ScopeBlock', 'SBlock');
	$t->set_var('SBlock','');
} else {
	if($skin_data['scope_key']) {
		$t->set_var('MAKE_AVAILABLE_CHECKED',' checked');
	}
}

$t->set_var('FONT_MENU',$font_menu);
$t->set_var('REFERER',$referer);

$t->set_strings('form', $skin_strings, $skin_data);

get_navigation();

$t->parse('CONTENTS', 'form', true);
$t->parse('CONTENTS', 'footer', true);

print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

?>
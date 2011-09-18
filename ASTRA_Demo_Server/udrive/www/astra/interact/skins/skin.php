<?php
header("Content-Type: text/css");

header("Content-disposition: inline; filename=skin.css");

header("Cache-Control: public");
header('Cache-Control: max-age=604800');


$CONFIG['NO_SESSION']=1;

require_once('../local/config.inc.php');
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$skin_key = (!empty($_GET['skin_key'])?$_GET['skin_key']: $CONFIG['DEFAULT_SKIN_KEY']);
$template = (isset($_GET['template']))?$_GET['template']: ''; 
$module_code = (isset($_GET['module_code']))?$_GET['module_code']: '';


if (!isset($objSkins)) {
	if (!class_exists('InteractSkins')) {
		require_once('../skins/lib.inc.php');
	}
	$objSkins = new InteractSkins();
}



$skin_data = $objSkins->getSkinData($skin_key);

if (empty($template) || $template=='default') {$template=$skin_data['template'];}

//see if it is a module css first
if (!empty($module_code)) {
	
	//include default module css first
	$t->set_file('module_css','../modules/'.$module_code.'/'.$module_code.'.css');
	$t->parse('CONTENTS','module_css', true);

	//now see if any skin overrides
	if (is_file($CONFIG['DATA_PATH'].'/skins/'.$template.'/modules/'.$module_code.'.css')) {
		$t->set_root($CONFIG['DATA_PATH']);
		$t->set_file('module_over','skins/'.$template.'/modules/'.$module_code.'.css');
		$t->parse('CONTENTS','module_over', true);
	} 	
	$t->p('CONTENTS');
	exit;
}
//always include the default template first
$t->set_file('default','../local/skins/default/interactstyle.css');
$t->set_var('SKIN_PATH','../local/skins/default');
set_colours('default');
$t->parse('CONTENTS','default');
if (!(empty($template) || $template=='default')) {
	
	if(is_dir($CONFIG['DATA_PATH'].'/skins/'.$template)) {
		$t->set_root($CONFIG['DATA_PATH']);
		$t->set_file('overskin','skins/'.$template.'/interactstyle.css');
		$t->set_var('SKIN_PATH',$CONFIG['PATH'].$CONFIG['VIEWFILE_PATH'].'skin_link/'.$template);
	} else {
		$t->set_file('overskin','../local/skins/'.$template.'/interactstyle.css');
		$t->set_var('SKIN_PATH','../local/skins/'.$template);
	}
	set_colours('overskin');
	$t->parse('CONTENTS','overskin',true);
}

$t->set_var('over','');
	
if (!empty($skin_data['body_font'])) {$t->set_var('over','
body,th, td, ol, ul, li ,p {
font-family: '.$skin_data['body_font'].';}',true);}

if (!empty($skin_data['body_background'])) {$t->set_var('over','
body {background-color: '.$skin_data['body_background'].';}',true);}



$t->set_var('over','

#outerBox {',true);

if (!empty($skin_data['outer_box_background'])) {$t->set_var('over','
background-color: '.$skin_data['outer_box_background'].';
',true);}

if (!empty($skin_data['outer_box_border_colour'])) {$t->set_var('over','
border: 1px solid '.$skin_data['outer_box_border_colour'].';',true);}

$t->set_var('over','}

#header {',true);

if (!empty($skin_data['header_background'])) {$t->set_var('over','background-color: '.$skin_data['header_background'].';
',true);}

if (!empty($skin_data['header_height'])) {$t->set_var('over','height: '.$skin_data['header_height'].';
',true);}

if (!empty($skin_data['header_border_colour'])) {$t->set_var('over','border: 1px solid '.$skin_data['header_border_colour'].';',true);}

$t->set_var('over','}',true);
// IE6 can't do transparent shading if colour has changed.
if (!empty($skin_data['header_background'])) $t->set_var('over','
* html #header {background-image:none;}',true); 


if (!empty($skin_data['header_logo'])) {
	if($skin_data['header_logo']!='none') {
		// for IE6 (because we hacked a gif over the png logo in the default skin)
		$t->set_var('over','
* html #logo {background-image:url('.$skin_data['header_logo'].');}',true);
	}

	$t->set_var('over','
#logo {',true);
	if($skin_data['header_logo']=='none') {
		$t->set_var('over','display:none;',true);
	} else {
		$t->set_var('over','
background-position:0 0;
background-image: url('.$skin_data['header_logo'].');',true);
		if (!empty($skin_data['header_logo_height'])) {$t->set_var('over','height: '.$skin_data['header_logo_height'].';',true);}

		if (!empty($skin_data['header_logo_width'])) {$t->set_var('over','width: '.$skin_data['header_logo_width'].';',true);}
	}
	$t->set_var('over','}',true);
}


if (!empty($skin_data['server_name_colour'])) {
	$t->set_var('over','
#serverNameLink {',true);
	if($skin_data['server_name_colour']=='none') {
		$t->set_var('over','display:none;',true);
	} else {
		$t->set_var('over','color: '.$skin_data['server_name_colour'].';',true);
	}
	$t->set_var('over','}',true);
}

$t->set_var('over','
#contentBox {',true);

if (!empty($skin_data['inner_box_background'])) {$t->set_var('over','background-color: '.$skin_data['inner_box_background'].';
',true);}

if (!empty($skin_data['inner_box_border_colour'])) {$t->set_var('over','border: 1px solid '.$skin_data['inner_box_border_colour'].';
',true);}

$t->set_var('over','}

#navigationBox {',true);
if (!empty($skin_data['nav_background'])) {$t->set_var('over','background: '.$skin_data['nav_background'].' none;',true);}

if (!empty($skin_data['nav_border_colour'])) {$t->set_var('over','border: 1px solid '.$skin_data['nav_border_colour'].';',true);}

$t->set_var('over','}',true);

if (!empty($skin_data['nav_background'])) {$t->set_var('over','
#navigation {background-image:none;}

',true);}

$t->set_var('over',$skin_data['raw_css'],true);
set_colours('over');

$t->parse('CONTENTS','over',true);

$t->p('CONTENTS');

function set_colours($file) {
global $t,$skin_data;

	$colours=array(1=>'857E72',2=>'758DAF');
	for($i=3;$i--;) {
		if(!empty($_GET['c'.$i])) {
			$colours[$i]=$_GET['c'.$i];
		} else {
			if(!empty($skin_data['colour'.$i])) $colours[$i]=substr($skin_data['colour'.$i],1);
		}
		if(strlen($colours[$i])==3) { //handle 12-bit #... format
			$colours[$i]=$colours[$i]{0}.$colours[$i]{0}.$colours[$i]{1}.$colours[$i]{1}.
				$colours[$i]{2}.$colours[$i]{2};
		}
	}

	foreach($colours as $key=>$value) {
		$colours[$key]=array(
			hexdec(substr($value,0,2)),
			hexdec(substr($value,2,2)),
			hexdec(substr($value,4,2))
		);
	}

	foreach($t->get_undefined($file) as $key=> $value) {
		if(preg_match('/^COLOU?R([0-9]+)\*([0-9]+)$/',$key,$m)) {
			$newcolour='#';
			for($i=0;$i<3;$i++){
				$nval=$colours[$m[1]][$i];
				$fac=$m[2]/100;
				$nval=($fac<1)? $nval*$fac : $nval+(255-$nval)*($fac-1);
				$newcolour.=substr('0'.dechex(max(0,min(255,$nval))),-2);
			}
		}
		$t->set_var($key,$newcolour);
	}
}
?>
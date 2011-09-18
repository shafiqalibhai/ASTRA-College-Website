<?php
header("Content-Type: text/css");
$CONFIG['NO_SESSION']=1;

require_once('../local/config.inc.php');

if (!isset($objSkins)) {
	if (!class_exists('InteractSkins')) {
		require_once('../skins/lib.inc.php');
	}
	$objSkins = new InteractSkins();
}
$skin_key = (isset($_GET['skin_key'])&&($_GET['skin_key']))?$_GET['skin_key']: $CONFIG['DEFAULT_SKIN_KEY']; 
//always include the default template first
require_once('../local/skins/default/interactstyle.css');
$skin_data = $objSkins->getSkinData($skin_key);
if (is_file('../local/skins/'.$skin_data['template'].'/print.css')) {
	require_once('../local/skins/'.$skin_data['template'].'/print.css');
} else {
	require_once('../local/skins/default/print.css');
}
?>
body,th, td, ol, ul, li ,p{ 
	<?php 
		if (isset($skin_data['body_font']) && $skin_data['body_font']!='') {
			echo 'font-family: '.$skin_data['body_font'].';';
		}
	?>
}

body {
	<?php 
		if (isset($skin_data['body_background']) && $skin_data['body_background']!='') {
			echo 'background-color: '.$skin_data['body_background'].';';
		}
	?>
}
#mainBox {
	<?php 
		if (isset($skin_data['main_box_background']) && $skin_data['main_box_background']!='') {
			echo 'background-color: '.$skin_data['main_box_background'].';';
		}
	?>
	<?php 
		if (isset($skin_data['main_box_border_colour']) && $skin_data['main_box_border_colour']!='') {
			echo 'border-color: '.$skin_data['main_box_border_colour'].';';
		}
	?>	
}

#header {
	<?php 
		if (isset($skin_data['header_background']) && $skin_data['header_background']!='') {
			echo 'background-color: '.$skin_data['header_background'].';';
		}
		if (isset($skin_data['header_height']) && $skin_data['header_height']!='') {
			echo 'height: '.$skin_data['header_height'].';';
		}		
	?>
	<?php 
		if (isset($skin_data['header_border_colour']) && $skin_data['header_border_colour']!='') {
			echo 'border-color: '.$skin_data['header_border_colour'].';';
		}
	?>	
}
#logo {
	<?php 
		if (isset($skin_data['header_logo']) && $skin_data['header_logo']!='') {
			echo 'background-image: url('.$skin_data['header_logo'].');';
		}
		if (isset($skin_data['header_logo_height']) && $skin_data['header_logo_height']!='') {
			echo 'height: '.$skin_data['header_logo_height'].';';
		}
		if (isset($skin_data['header_logo_width']) && $skin_data['header_logo_width']!='') {
			echo 'width: '.$skin_data['header_logo_width'].';';
		}						
	?>
}

#contentBox {
	<?php 
		if (isset($skin_data['content_box_background']) && $skin_data['content_box_background']!='') {
			echo 'background-color: '.$skin_data['content_box_background'].';';
		}
	?>
	<?php 
		if (isset($skin_data['content_box_border_colour']) && $skin_data['content_box_border_colour']!='') {
			echo 'border-color: '.$skin_data['content_box_border_colour'].';';
		}
	?>	

}
#navigation {
	<?php 
		if (isset($skin_data['nav_background']) && $skin_data['nav_background']!='') {
			echo 'background-color: '.$skin_data['nav_background'].';';
		}
	?>
	<?php 
		if (isset($skin_data['nav_border_colour']) && $skin_data['nav_border_colour']!='') {
			echo 'border-color: '.$skin_data['nav_border_colour'].';';
		}
	?>	
	
}



<?php

	## error reporting ##
	error_reporting(E_ALL);
 	
	## setting: debug flag ##
	#$debug_main_flag = true; // NOT IN USE
	
	## setting: include header/footer ##
	$include_header = false;
	$include_footer = false;
		
	## import init file
	require_once("libraries/general.bootstrap.php");
	
?>
<html>
<?php
	## DO NOT REMOVE THIS CODE ##
	include("config/version.php");
	print("<!-- \n\ $version\n\t \n\t\n -->\n");
?>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title><?php echo $settings['gallery_title']?></title>
	
	<!-- START CPAINT:JSRS ( http://cpaint.sourceforge.net/ ) -->
	<script type="text/javascript" src="libraries/cpaint2.inc.compressed.js"></script>
	<!-- END CPAINT:JSRS -->
	
	<!-- START AJAX SCRIPTS -->
	<script src="libraries/script.loader.php?load=init" type="text/javascript"></script>
	<script src="libraries/ajax.functions.js" type="text/javascript"></script>
	<!-- END AJAX SCRIPTS -->
	
	<?php if($settings['preview_mode'] == 1
		&& file_exists("extensions/slimbox/" )) { ?>
	<!-- START SLIMBOX -->
	<script type="text/javascript" src="extensions/slimbox/js/mootools.r83.js"></script>
	<script type="text/javascript" src="extensions/slimbox/js/slimbox.js"></script>
	
	<link rel="stylesheet" href="extensions/slimbox/css/slimbox.css" type="text/css" media="screen" />
	<!-- END SLIMBOX -->
	
	<?php } else if($settings['preview_mode'] == 2
		&& file_exists("extensions/thickbox_2/" )) { ?>
	<!-- START THICKBOX_2 -->
	<script type="text/javascript" src="extensions/thickbox_2/jquery.js"></script>
	<script type="text/javascript" src="extensions/thickbox_2/thickbox.js"></script>
	
	<link rel="stylesheet" href="extensions/thickbox_2/thickbox.css" type="text/css" media="screen" />
	<!-- END THICKBOX_2 -->
	<?php } ?>
	
	<!-- START GALLERY CSS -->
	<link rel="stylesheet" href="libraries/script.loader.php?load=gallery" type="text/css" media="screen" />
	<!-- END GALLERY CSS -->
	
	<?php if ($include_header | $include_footer) { ?>
	<!-- START HEADER/FOOTER CSS -->
	<link rel="stylesheet" href="styles/includes.css" type="text/css" media="screen" />
	<!-- END HEADER/FOOTER CSS -->
	<?php } ?>
	
	<!-- START SCRIPTS/STYLESHEETS FOR IE PC -->
	<!--[if IE]>
		<link href="styles/gallery_ie.css" rel="stylesheet" type="text/css" media="screen" />
		<!--[if gte IE 5.5]>
			<![if lt IE 7]>
				<style type="text/css">
					div#msc_image {
						/* IE5.5+/Win - this is more specific
						than the IE 5.0 version */
						left: expression( ( ignoreMe2 = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ) + 'px' );
						top: expression( ( ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ) + 'px' );
						right: auto;
						bottom: auto;
					}
				</style>
			<![endif]>
		<![endif]-->
	<![endif]-->
	<!-- END SCRIPTS/STYLESHEETS FOR IE PC -->
</head>

<body>
	<a id="hash" name="null"></a>
	<?php
		if ($include_header
			&& file_exists("add_header.php")) {
			include("add_header.php");
		}
	?>
	
	<div id="global_container">
	
	<div id="debug"><p>[DEBUG] <span id="debug_content"><?php echo $_SERVER["HTTP_USER_AGENT"];?></span></p></div>
	
	<!-- tooltip -->
	<div id="tooltip"></div>
	
	<!-- alert container -->
	<div id="alert">
		<span id="msg"><!-- Alert goes here --></span>
		<p><a id="dismiss" href="javascript:;" onClick="hideDiv('alert');"><?php echo $lang['alert_dismiss']?>...</a></p>
	</div>
	<!-- alert container -->
	
	
	<div id="msc_container">
		
		<!-- menu div -->
		<div id="msc_menu">
			<h1>Galleries</h1>
		
			<!-- menu items div -->
			<div id="galleries_menu"></div>
		</div>
		<!-- menu div -->
	
		<!-- thumbs div -->
		<div id="msc_thumbs">
			
			<!-- top menu -->
			<div id="top_menu" class="<?php echo ($settings['use_select_menu'])?'menu_bg':'';?>">
				<!-- galleries menu -->
				<div id="galleries_select">&nbsp;</div>
				<!-- tools menu -->
				<div id="tools_menu">tools: 
					<span id="tools_slideshow"></span>
					<span id="tools_permalink"></span>
				</div>
			</div>
			
			<!-- gallery info div -->
			<div id="gallery_data">
				<div id="gallery_title"></div>
			</div>
			
			<!-- gallery description div -->
			<div id="gallery_description">
			<?php
				if (file_exists("galleries/".$settings['info_file'])) {
					echo get_include_contents("galleries/".$settings['info_file']);
				}
			?></div>
			
			<!-- thumbs wrapper -->
			<div id ="thumbs_div">
			
				<!-- gallery block nav div -->
				<div id="gallery_nav">
					<div id="gallery_block"></div>
				</div>
				
				<!-- thumbs loader data -->
				<div id="thumbs_load"></div>
				
				<!-- thumbs container -->
				<div id="thumbs_cont"></div>
				
			</div>
			
			<!-- footer div -->
			<div id="msc_foot">
				<span id="footer_cont"><?php echo $settings['gallery_footer']?></span>
			</div>
			
		</div>
		<!-- thumbs div -->
	
	</div>
	<!-- msc_container div -->

	<!-- image container -->
	<div id="msc_image">
		
		<div id="image_url"></div>
		
		<!-- image div -->
		<div id="image_div">
			<div id="image_container">
				<div id="image_menu">
					<span id="close_win">
						<a href="javascript:;" onClick="closeImageWin();">&#171; <?php echo $lang['lightbox_back']?></a>
					</span>
					
					<div id="nav_container">
						<div id="prev">
							<a id="a_prev" href="javascript:;" onClick="prevImage()"><img src="./themes/<?php echo $selected_theme?>/<?php echo $theme_image_prev?>" /></a>
						</div>
						<div id="next">
							<a id="a_next" href="javascript:;" onClick="nextImage()"><img src="./themes/<?php echo $selected_theme?>/<?php echo $theme_image_next?>" /></a>
						</div>
						<div id="nav_thumbs"></div>
					</div>
					
				</div>
				
				<div id="image_header">
				
					<div id="timer"><span id="time">[ <?php echo $settings['slideshow_seconds']?>]</span> | <span><a id="toggle_show" href="javascript:;" onClick="startSlideshow()"><?php echo $lang['slideshow_pause']?> <?php echo $lang['slideshow_name']?></a></span></div>
					<div id="image_title"></div>
					
				</div>
				
				<div id="img">
					<img id="mainimg" class="imagen" src="images/spacer.gif" />
				</div>
			</div>
		</div>
		<!-- image div -->
		
		<!-- image bg -->
		<div id="image_bg"></div>
		
	</div>
	<!-- image container -->
	
	</div>

	<?php
		if ($include_footer
			&& file_exists("add_footer.php")) {
			include("add_footer.php");
		}
	?>
	
	<?php
		/**** DEBUG INFO ****/
		if ($settings['gallery_debug']) {
			echo ('<div id="debug_information">');
			phpinfo(INFO_CONFIGURATION);
			phpinfo(INFO_CONFIGURATION);
			phpinfo(INFO_ENVIRONMENT);
			phpinfo(INFO_VARIABLES);
			echo ('</div>');
		}
	?>
	
</body>
</html>
<?php
/**
* HTML functions
*
* Contains any html related functions
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: html.inc.php,v 1.86 2007/07/24 05:25:25 websterb4 Exp $
* 
*/

/**
* A class that contains methods related to html functions 
* 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods simple html functions, creating menus, tables, etc. 
* 
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteractHtml {

	function InteractHtml() {
		$this->mediaPlaceholder_pat='/<[^>]*\?XX([A-Z0-9]+)XX(size=[0-9]+:)?(dur=[0-9]+:)?([0-9,]+:)?(DL=[a-zA-Z0-9,]+:)?fixed:([^:]*):([^:]*):([^\"]*)XX\\1XX\"[^>]*\>/';
	}
	/**
	* Create a select menu from an array  
	* 
	* @param array $array array to create menu from
	* @param string $name name of menu
	* @param string/array $selected items to be shown as selected in menu
	* @param string $multiple true if multiple selection allowed
	* @param string $size size of menu
	* @return $menu html code for menu
	*/

	function arrayToMenu($array, $name, $selected='', $multiple=false, $size='', $blank=false, $javascript='', $id='')
	{
		$menu =  '<select '.$javascript.' name="'.$name.'"';

		//If set to null, dont include id.  if unset, set id to $name.
		if ($id!=='') {$menu .= ' id="'.(empty($id)?$name:$id).'"';}

		if ($multiple===true) {
			$menu .= ' multiple';
		} 
		
		if ($size!='') {
			$menu .= ' size="'.$size.'"';
		}
		 
		$menu .= '>'; 
		
		if ($blank==true) {
			 $menu .= '<option value="" selected>-----</option>';
		}
		
		foreach ($array as $key => $value) {
		
			$menu .= '<option value="'.$key.'"';
			
			if (is_array($selected)? in_array($key, $selected) : $key==$selected) {
				$menu .= ' selected';
			}
			$menu .= '>'.$value.'</option>';
		}
						
		$menu .= '</select>'; 
	
		return $menu;
	
	} //end arrayToMenu

	/**
	* Add required javascript to template for running editor  
	* 
	* @param object $t template object
	* @param int $auto_editor 1 if editor to auto load
	* @param string $form_field field id to load editor in
	* @return true
	*/

	function setTextEditor(&$t, $auto_editor, $form_field='', $full_page=false)
	{

		 global $CONFIG, $general_strings;

		 $browser=browser_get_agent();
		 if (($browser['agent']=='IE' && $browser['version']>=5.5 && $browser['platform']=='Win') || $browser['gecko_version']>='20030210' 
//Safari still too useless... and it hangs when you try to drag an image around..  test again after build 420 is out.
//		|| ($browser['agent']=='SAFARI' && $browser['version']>=420)
		){

			$init_ed="init_editor('".$form_field."','Editor2', {});";

			if(strpos($t->get_var('SCRIPT_EDITOR'),'dojo_ed.js')===false) {
				$t->set_var('SCRIPT_EDITOR','<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/dojo/dojo_ed.js?v2"></script>',true);
			}

			if ($auto_editor && $form_field) {
				$t->set_var('SCRIPT_EDITOR','<script type="text/javascript">',true);
				$t->set_var('SCRIPT_EDITOR',
					'dojo.addOnLoad(function() {'.$init_ed.'});',true);
				$t->set_var('SCRIPT_EDITOR','</script>',true);

				$t->set_var('EDITOR_BUTTONS_'.$form_field,'');
				$t->set_var('EDITOR_STYLE_'.$form_field,''); //hidden?
				
			} else {
				$t->set_var('EDITOR_STYLE_'.$form_field,'');
				$t->set_var('EDITOR_BUTTONS_'.$form_field,'<span class="ed_button" id="button_for_'.$form_field.'"><input type="button" name="Button" value="'.$general_strings['easy_edit'].'" class="small" onClick="'.$init_ed.'"></span>');
			}
		} else {
			$t->set_var('EDITOR_STYLE_'.$form_field,'');
			$t->set_var('EDITOR_BUTTONS_'.$form_field,'<div class="ed_button"><input type="button" name="link" value="'.$general_strings['insert_link'].'" class="small" onClick="make_link(\''.$form_field.'\')"/>
<input type="button" name="elink" value="'.$general_strings['email_link'].'" class="small" onClick="make_email_link(\''.$form_field.'\')" /> 
<input type="button" value="'.$general_strings['preview'].'" name="preview" class="small" onClick="display_html(\''.$form_field.'\')"/> 
<input type="button" name="Italics" value="'.$general_strings['italic_abb'].'" class="small" style="font-style:italic" onClick="make_italics(\''.$form_field.'\')" /> 
<input type="button" name="Bols" value="'.$general_strings['bold_abb'].'" class="small" style="font-weight:bold" onClick="make_bold(\''.$form_field.'\')"/> ');

			$t->set_var('EDITOR_BUTTONS_'.$form_field,'<input type="button" name="image" value="'.$general_strings['add_image'].'" class="small" onClick="get_image(\''.$CONFIG['PATH'].'\',\''.$form_field.'\');" />',true);

			if (!($t->get_var('SCRIPT_EDITOR'))) {
// 				$t->set_var('SCRIPT_EDITOR','<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/popups/popup.js" ></script>
// 				<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/use_dialogs.js" ></script>', true);

				$t->set_var('SCRIPT_INCLUDES','<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/popups/popup.js" ></script>
				<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/dialogs.js" ></script>
				<script type="text/javascript" language="javascript"  src="'.$CONFIG['PATH'].'/includes/editor/use_dialogs.js" ></script>
				', true);
			}
			$t->set_var('EDITOR_BUTTONS_'.$form_field,'</div>',true);
		}
		return true;
	
	} //end setTextEditor


	/**
	* Parse text to replace any video and sound placeholders, turn links to html, etc.  
	* 
	* @param string $text text to convert links in
	* @return string $parsed_text
	*/
	function parseText($text) {
	
	
		$parsed_text = $this->urlsTolinks($text);
		
		//if no paragraph or break tags replace \n with <br />
		
		if (!eregi('(<p|<br)', $parsed_text)) {
			
			$parsed_text = nl2br($parsed_text);
		
   		} 
		
		$parsed_text = $this->replaceMediaPlaceholders($parsed_text);
		$parsed_text = $this->replaceUserVariables($parsed_text);
		$parsed_text = $this->textoGif($parsed_text);
		//$parsed_text = $this->popuplinks($parsed_text);
		 		
		return $parsed_text;
	
	}
	
	/**
	* Convert links in a string to html  
	* 
	* @param string $text text to convert links in
	* @return string $linked_text
	*/
	function urlsTolinks($text) {
	
		// Make long URLs into links
		// Regexp improved by Bruce.
		$linked_text = eregi_replace("([[:space:].,\(\[])([[:alnum:]]{3,})://([[:alnum:]_-]+\.)([[:alnum:]_-]{2,}[[:alnum:]/&=#?_\.-]*)",
						  "\\1<a href=\"\\2://\\3\\4\" target=\"newpage\">\\2://\\3\\4</a>", $text);

	   // Make short urls into links
		$linked_text = eregi_replace("([[:space:],\(\[])www\.([[:alnum:]_-]+\.)([[:alnum:]_-]{2,}[[:alnum:]/&=#?_\.-]*)", "\\1<a href=\"http://www.\\2\\3\" target=\"newpage\">www.\\2\\3</a>", $linked_text);
						  
		return $linked_text;
						  
			
	}//end urlsTolinks()


// 	function url_exists($url){
// 		global $CONFIG;
//		if(!strstr($url, "http://")) {
//			$url = str_replace("http://", "", $url);
//			$url "http://". $url;
//		} else {
// 			if(substr($url,0,1)=='/') {
// 				$url=str_replace("http://", "",$CONFIG['SERVER_URL']).$url;
// 			}
// 			echo $url;
// 		}
// 			
// 		if($fp=fsockopen('http://'.$url,80)) {
// fputs($fp, "HEAD /foo HTTP/1.0\r\n\r\n");
// while(!feof($fp))
// {
// echo fgets($fp);
// }
// } else {echo 'bah';}
//		$fp = @fsockopen($url, 80);
//		if($fp === false) {echo 'f';return false;}
//		return true;
// if(substr($url,0,1)=='/') {$url=$CONFIG['SERVER_URL'].$url;}
// if($handle = fopen($url, "rb")) {return true;}
// return false;
// 	}



	function _parseMediaCallback($m) {
		global $CONFIG;
		
		static $nn=0;
		$nn++;

		//<param name="wmode" value="transparent" /> wmode="transparent" 

		$po=array('player'=>$m[1],'id'=>$m[1].$nn,'flashvars'=>$m[8]);

		if($m[2]) $po['size']=substr($m[2],5,-1); // dump size= and final :
		if($m[3]) $po['dur'] =substr($m[3],4,-1); // dump dur= and final :
		if($m[4]) $po['v']   =substr($m[4],0,-1); // version - dump final :
		if($m[5]) $po['DL']  =substr($m[5],3,-1); // dump DL= and final :

		$po['w']=$m[6]?$m[6]:320;
		$po['h']=$m[7]?$m[7]:280;

		switch($po['player']) {
		case 'CUSTOMSWFU':
			preg_match('/[a-z]+=([^&]+)/',$po['flashvars'],$m);
			$po['player_url']=$m[1];
			break;
		case 'MP3STREAM':   //fix old mp3 player objects
			$po['v']='7,0,0,0';
			$po['player']='AVPLAYER';
			if($po['h']==52) {$po['h']=40; $po['DL']='mp3';}
			$po['w']=$po['h']<<1;
		case 'AVPLAYER':
			if($po['h']<=40) {
				$po['wmode']='transparent';  //screws up in FF preview layer iframe (moreso than 'window').
			} else {
				$po['v']='8,0,0,0';
				$control_width=80
					-(strpos($po['flashvars'],'showvolume=0')!==false?40:0)
					-(strpos($po['flashvars'],'showplay=0')!==false?40:0)
					+(strpos($po['flashvars'],'showprogress=1')!==false?320:0);

				$po['bgcolor']='#000000';
				if($control_width) {
					$po['h']+= $po['w']>$control_width ? 40:
						40 * $po['w']/$control_width;
				}
			}
			if(preg_match('/(sndname=|medianame=)([^&]+)/',$po['flashvars'],$m)) {
				$po['medianame']=$m[2];
				
				// qualify url (needed for external embeds)
				if(preg_match('|^[a-zA-Z]{3,4}://|',$po['medianame'])===0) {
					$po['flashvars']=preg_replace('"'.$m[2].'"',$CONFIG['SERVER_URL'].$m[2],$po['flashvars']);
				}

				$extn_start=strrpos($m[2],'.');
				$po['mediatype']=substr($m[2],$extn_start+1);
			}

		default:
			$po['player_url']="{$CONFIG['FULL_URL']}/includes/players/{$po['player']}.swf";
		}
		if(!$po['v']) $po['v']='6,0,0,0';

		return $po;
	}
	
	function matchToMedia($m) {
		return $this->Media_toSWF($this->_parseMediaCallback($m));
	}
	
	function parseMediaPlaceholder($text) {
		return preg_match($this->mediaPlaceholder_pat, $text, $m)?
			$this->_parseMediaCallback($m):
			false;	
	}

	/**
	* Replace media place holders with flash objects  
	* 
	* @param string $text text to replace placeholders in
	* @return string $text
	*
	* placeholder datastring format:
	* ...?XX{PLAYER}XX[size=n:][dur=n:][{version}:][DL={extn[,extn...]}:]fixed:{width}:{height}:{FlashVars}XX{PLAYER}XX
	*/
	
	function replaceMediaPlaceholders($text) {
		return preg_replace_callback($this->mediaPlaceholder_pat,array($this,"matchToMedia"), $text);
	}//end replaceMediaPlaceholders()

//	function createMediaPlaceholder() {}
	
	function Media_toSWF($po) {
		global $CONFIG,$general_strings;
		
		$po['flashvars'].='&w='.$po['w'].'&h='.$po['h'];
		if($po['dur']) $po['flashvars'].='&dur='.$po['dur'];  //help out Flash player by providing duration

		$embed="<embed style=\"width:{$po['w']}px; height:{$po['h']}px\" src=\"{$po['player_url']}\" FlashVars=\"{$po['flashvars']}\" scale=\"noscale\" salign=\"lt\"".($po['wmode']?" wmode=\"{$po['wmode']}\"":'').($po['bgcolor']?" bgcolor=\"{$po['bgcolor']}\"":'')." allowFullScreen=\"true\"
pluginspage=\"http://www.adobe.com/go/getflashplayer\"
type=\"application/x-shockwave-flash\" />";

		if($po['DL']) {
			$fi=substr($po['medianame'],0,strlen($po['medianame'])-strlen($po['mediatype'])-1);
			$links='';$add_embed=false;
			foreach(explode(',',$po['DL']) as $extn) {
				if($extn=='embed') {$add_embed=true;
				} else {
					$links.="<a href=\"".str_replace(' ','+',$fi).".$extn?DL\" title=\"{$general_strings['download']} $extn file\">$extn</a> ";
				}
			}
			if(!empty($links)) $links='<span>'.$general_strings['download'].' '.$links.'</span>';
			
			if($add_embed) {
				$links=(empty($links)?'':$links.'&middot;').' <a class="embedLink" title="'.$general_strings['copy_embed'].'" style="background-image:url('.$CONFIG['PATH'].'/images/disclosure_closed.gif)" onclick="disclose_and_select(\''.$po['id'].'_embed\',this.style,\'backgroundImage\');"><span>&nbsp;</span></a> 
				<div style="display:none" id="'.$po['id'].'_embed">'.$general_strings['copy_embed'].'<br /><textarea id="'.$po['id'].'_embed_text" cols="38" rows="6" wrap="off" onClick="this.focus();this.select()">'.$embed.'<br />'.$links.'
<a href="'.$CONFIG['FULL_URL'].'" style="margin-left:1em;">
'.$CONFIG['SERVER_NAME'].'</a>
</textarea></div>';
			}
		}
		if(!empty($links)) $links='<br />'.$links;

		return "<div class=\"media\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version={$po['v']}\" width=\"{$po['w']}\" height=\"{$po['h']}\">
<param name=\"movie\" value=\"{$po['player_url']}\" />
<param name=\"FlashVars\" value=\"{$po['flashvars']}\" />
<param name=\"scale\" value=\"noscale\" />
<param name=\"salign\" value=\"lt\" />
<param name=\"allowFullScreen\" value=\"true\" />
".($po['wmode']?"<param name=\"wmode\" value=\"{$po['wmode']}\"":'').
($po['bgcolor']?"<param name=\"bgcolor\" value=\"{$po['bgcolor']}\" />":'').$embed."
</object>{$links}</div>";
	}

	
	/**
	* Replace user variables  
	* 
	* @param string $text text to replace user variables in
	* @return string $text
	*/
	function replaceUserVariables($text) {
	
		global $CONFIG;
		
		if (isset($_SESSION['current_user_key'])) {
		
			$text = str_replace('[first_name]', $_SESSION['current_user_firstname'], $text);
			$text = str_replace('[last_name]', $_SESSION['current_user_lastname'], $text);
			
		} else {
		
			$text = str_replace('[first_name]', '', $text);
			$text = str_replace('[last_name]', '', $text);
		
		}
						 
		return $text;
						  
			
	}//end replaceUserVariables()	

	/**
	* Replace tex forumla  
	* 
	* @param string $text text to replace user variables in
	* @return string $text
	*/
	
	function textoGif($text) 
	{
    
	
	preg_match_all('/\[EQ(.+?)EQ\]|\$\$(.+?)\$\$/is', $text, $matches);  
    for ($i=0; $i<count($matches[0]); $i++) {
        $texexp = $matches[1][$i] . $matches[2][$i];
		$texexp = str_replace('<br />','',$texexp);
		$texexp = str_replace('<br />','',$texexp);
		//$texexp = urlencode($texexp);
        $text = str_replace( $matches[0][$i],  "<img src=\"/cgi-bin/mimetex.cgi?$texexp\">", $text);
    }

	return $text; 
	
	
	}
	
	/**
	* Replace popup links  
	* 
	* @param string $text text to replace popup links
	* @return string $text
	*/
	/*
	function popuplinks($text) 
	{
    
	$text = preg_replace('/\[POPUP:([^:]*):([^:]*):([^:]*):([^\]]*)\]/i', "<span class=\"vlink\" onClick=\"window.open ('$2', '30', 'toolbar=no, scrollbars=yes, width=$3, height=$4, menubar=no, location=no, resizable=yes')\">$1</span>",$text);
	//'/\[POPUP:([^:]*):([^:]*):([^:]*):([^\]]*)\]/i'
	//preg_match_all('/\[POPUP([^X]*)\]/i', $text, $matches);  
    //for ($i=0; $i<count($matches[0]); $i++) {
         
    	//$parts = explode(':',$matches[1][$i]);
        //$text = str_replace( $matches[0][$i],  "<span class=\"vlink\" onClick=\"window.open ('".$parts[2]."', '30', 'toolbar=no, scrollbars=yes, width=".$parts[3].", height=".$parts[4].", menubar=no, location=no, resizable=yes')\">".$parts[1]."</span>", $text);
    //}

	return $text; 
	
	
	}
	*/
	/**
	* Generate an icon tag for non-standard icons  
	* 
	* @param int $icon_key key of icon 
	* @param string $type small or large
	* @return string $icon_tag icon tag string
	*/
	function getIconTag($icon_key, $type='small') {
	
  		global $CONN, $CONFIG;
		
		if ($type=='small') {
			$field = 'small_icon';
		} else {
			$field = 'large_icon';
		}
		$rs = $CONN->Execute("SELECT $field FROM {$CONFIG['DB_PREFIX']}icons WHERE icon_key='$icon_key'");
		
		while (!$rs->EOF) {
			$icon = $rs->fields[0];		
			$rs->MoveNext();			
		} 
		$rs->Close();
		
		if (is_file($CONFIG['BASE_PATH'].'/local/modules/icons/'.$icon)) {
			
			$icon = '<img class="icon" src="'.$CONFIG['MODULE_FILE_VIEW_PATH'].'icons/'.$icon.'">';
			return $icon;	
		} else {
			return false;
		}
			
	}//end getIconTag()
	
	/**
	* Generate an icon url for non-standard icons  
	* 
	* @param int $icon_key key of icon 
	* @param string $type small or large
	* @return string $icon_url icon tag string
	*/
	function getIconurl($icon_key, $type='small') {
	
  		global $CONN, $CONFIG;
		
		if ($type=='small') {
		
			$field = 'small_icon';
		
		} else {
		
			$field = 'large_icon';
		
		}
		$rs = $CONN->Execute("SELECT $field FROM {$CONFIG['DB_PREFIX']}icons WHERE icon_key='$icon_key'");
  
		$icon = $rs->fields[0];		
		
		$rs->Close();
		
		if (is_file($CONFIG['MODULE_FILE_SAVE_PATH'].'/icons/'.$icon)) {
		

			$icon_url = $CONFIG['MODULE_FILE_VIEW_PATH'].'icons/'.$icon;
			return $icon_url;
				
		
		} else {
		
			return false;
		
		}
			
	}//end getIconurl()	
		/**
	* Generate an html header with link to relevant style sheet, etc. 
	* @return string $html_header full html header
	*/
	function getemailHeader() {
	
  		global  $CONFIG;
		
		$html_header = '
		
		<head>
		<title>'.$CONFIG['SERVER_NAME'].'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="'.$CONFIG['FULL_URL'].'/skins/skin.php?skin_key=1&t=10" type="text/css">
		</head>
		<body bgcolor="#FFFFFF" id="emailBody" >
		<style type="text/css">
		<!--
		#emailBody{
			padding:10px;
		}
		#emailBody a{
			text-decoration:none;
		}
		#emailBody h1{
			font-size:small;
			font-weight:bolder;
		}
		#emailBreadcrumbs{
			padding:5px;
			border: 1px solid #747482;
			background-color: #EBEBEB;
			font-size: x-small;
		}
		.emailOptions{
			text-align:right;
			font-size: x-small;
		}
		-->
		</style>
		';
		return $html_header;
	
	}//end getHtmlHeader()	


} //end InteractHtml

?>
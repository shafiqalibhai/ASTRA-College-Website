<?php

$CONFIG['LANGUAGE_PATH']=$CONFIG['BASE_PATH'].'/language';

function grab_strings($lang,$file,&$sArray) {
	global $CONFIG;

	$full_path=$CONFIG['LANGUAGE_PATH'].'/'.$lang.'/strings/'.$file.'.txt';
	if(file_exists($full_path) && ($handle = fopen($full_path,"rb"))) {
		$allstr=fread($handle,100000);
		fclose($handle);	
		$list=explode("\n",$allstr);
		
		reset($list);
		while($a=current($list)){
			$dpos=strpos($a,'=');
			if($dpos!==false) {$sArray[substr($a,0,$dpos)]=substr($a,$dpos+1);}
			next($list); 
		}
		return true;
	} else {
		return false;
	}
}

function lang_desc($lang) {
	$genR=array();
	grab_strings($lang,'general_strings',$genR);
	return (isset($genR['language_description'])?$genR['language_description']:'');
}



function amount_done($lang,$file) {
	global $CONFIG;
	$lR=array();$defR=array();
	if (grab_strings($lang,$file,$lR)) {
		grab_strings('default',$file,$defR);
		$clR=0;
		foreach($defR as $key=>$val) {if (isset($lR[$key])) $clR++;}
		$cdefR=count($defR);
		return '<span class="small" style="color:'.($clR==$cdefR?'#0A0':'#F80').'">'.$clR.'/'.$cdefR.' ('.floor($clR*100/$cdefR).'%)</span>';
	} else return '<strong style="color:#F00">0</strong>';
}


function get_all_inheritance($lang,$file,&$pArray,&$sourceArray,$depth=0) {
	global $CONFIG;
	if($lang!='default') {

// use 'source-...' for dialects within a language.  Use ...(source) to inherit from another related language.
		if(substr($lang,-1)==')') {
	get_all_inheritance(substr($lang,strpos($lang,'(')+1,-1),$file,$pArray,$sourceArray,$depth+1);
		} else {
			if($unpos=strrpos($lang,'-')){
				get_all_inheritance(substr($lang,0,$unpos),$file,$pArray,$sourceArray,$depth+1);	
			} else {get_all_inheritance('default',$file,$pArray,$sourceArray,$depth+1);}
		}
	}

	$tArray=array();
	if($lang==''){$lang=$CONFIG['DEFAULT_LANGUAGE'];}
	grab_strings($lang,$file,$tArray);
	if($depth) {
		foreach($tArray as $key=>$val) {
			$sourceArray[$key]=$lang;
			$pArray[$key]=$val;
		}
	} else {
		return $tArray;
	}
}

function get_full_lang($lang,$file,&$full) {
	$sa=array();
	get_all_inheritance($lang,$file,$full,$sa,1);
}

function get_listing($full) {
	global $CONFIG;
	$full_path=$CONFIG['LANGUAGE_PATH'].($full?'':'/default/strings');
	$list=array();
	if($handle = opendir($full_path)) {
	while($a=readdir($handle)) {
		if($a!='readme.txt' && $a[0]!='.' && $a!='CVS') {
			if($full) {
				if(is_dir("$full_path/$a") && preg_match("/^\w{2,3}(-|\(|$)/",$a)) { 
					array_push($list,$a);
				}
			} else if (is_file("$full_path/$a")) {
				array_push($list,substr($a,0,-4));
			}
		}
	}}
	closedir($handle);	
	natcasesort($list);
	reset($list);
	return $list;
}

function lang_menu($menu_name,$current_lang='') {
	global $CONFIG;
	if($current_lang=='') {$current_lang=$CONFIG['DEFAULT_LANGUAGE'];}

	$lang_select='';
	$lang_list=get_listing(true);
	foreach($lang_list as $val) {if($val!='default' && is_dir($CONFIG['LANGUAGE_PATH']."/$val/strings/compiled")){
		$lang_select.="<option value=\"$val\"".($current_lang==$val?' selected':'').">".lang_desc($val)."</option>";}}
	return('<select name="'.$menu_name.'">'.$lang_select.'</select>');
}
?>
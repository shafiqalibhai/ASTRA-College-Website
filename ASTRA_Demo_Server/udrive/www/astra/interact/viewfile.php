<?php
/**
* View file
*
* Used by any modules that need to give users access to files 
*
*/

/**
* Include main system config file 
*/
require_once('local/config.inc.php');
require_once('includes/mimetypes.inc.php');

//get language strings
//require_once($CONFIG['LANGUAGE_CPATH'].'/file_strings.inc.php');

if (isset($_GET['path'])) {
	$the_url=urldecode($_GET['path']);
	if(strpos($the_url,'?')>-1) {$the_url=substr($the_url,0,strpos($the_url,'?'));}

	$path_array=explode('/',$the_url);
	if (in_array('..',$path_array)) {$path_array=array();}
} else {
	$the_url=urldecode(str_replace($CONFIG['PATH'],"",$_SERVER['REQUEST_URI']));
	if(strpos($the_url,'?')>-1) {$the_url=substr($the_url,0,strpos($the_url,'?'));}

	$path_array=explode("/",$the_url);   
	array_shift($path_array);
	array_shift($path_array);
}

$count = count($path_array);
$file_area=$path_array[0];
switch($file_area) {
	case 'users':
		$file_name = $path_array[$count-1];
		unset($path_array[0], $path_array[$count-1]);
		$file_path = implode('/',$path_array);
		$full_path = $CONFIG['USERS_PATH'].'/'.$file_path.'/'.$file_name;
	break;
	case 'library':
		$file_name = $path_array[$count-1];
		unset($path_array[0], $path_array[$count-1]);
		$file_path = implode('/',$path_array);
		$full_path = $CONFIG['LIBRARY_PATH'].'/'.$file_path.'/'.$file_name;
	break;
	case 'icons':
		$file_name = $path_array[$count-1];
		$full_path = $CONFIG['DATA_PATH'].'/modules/icons/'.$file_name;
		
	break;	
	
	case 'skin_link':
		$file_name = $path_array[$count-1];
		unset($path_array[0], $path_array[$count-1]);
		$file_path = implode('/',$path_array);
		$full_path = $CONFIG['DATA_PATH'].'/skins/'.$file_path.'/'.$file_name;
	break;

	case 'favicon.ico':
		$full_path = $CONFIG['DATA_PATH'].'/favicon.ico';
	break;
	
	// this case is solely for http progressive download access to flash communication server recordings
	case 'recordings':
		if(isset($CONFIG['FLASH_COM'])) {
			unset($path_array[0]);
			$full_path = $CONFIG['FLASH_COM'].'recordings/streams/'.implode('/',$path_array);
			break;
		}
	default:
		$space_key = $file_area;
		$file_area='modules';
		$file_name = $path_array[$count-1];
		$module_key = $path_array[3];
		$type_code = $path_array[1];
		unset($path_array[0], $path_array[$count-1]);
		$file_path = implode('/',$path_array);
		$full_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/'.$file_path.'/'.$file_name;
		check_variables(true,false,true);		
	break;

}

$mime_type = getMimeInfo('type', $file_name);
$link_key 	= get_link_key($module_key,$space_key);


//update statistics 
//statistics('read');

//autenticate the user.
if (!isset($space_key)) {
	$space_key = get_space_key();
}
//$access_levels = authenticate();
//$accesslevel_key = $access_levels['accesslevel_key'];
//$group_access = $access_levels['groups'];
//$group_accesslevel = $access_levels['group_accesslevel'][$group_key];

if ($mime_type=='text/html' && $type_code=='file') {
	
	$sql = "select embedded from {$CONFIG['DB_PREFIX']}files where module_key='$module_key'";
	$rs = $CONN->Execute($sql);
	while (!$rs->EOF) {
		$embedded  = $rs->fields[0];	
		$rs->MoveNext();	
	}
} else {
	$embedded=='0';
}

if ($embedded!=1) {
	if($CONFIG['FILE_MIRROR'] && in_array($file_area, $CONFIG['FILE_MIRROR_AREAS']) && !IPmatch() && !isset($_GET['notmirrored']) && !in_array(substr($full_path,-4),array('.jpg','.gif','.png'))) { //don't mirror img
		$remote_path=$CONFIG['FILE_MIRROR'].substr($full_path,strlen($CONFIG['DATA_PATH']))."?from=".urlencode($CONFIG['FULL_URL'].$CONFIG['VIEWFILE_PATH']).(isset($_GET['DL'])?'&DL':'');

//		echo $remote_path;exit;
		header("HTTP/1.0 307 Temporary Redirect");
		header("Location: $remote_path"); exit;
	} else {

		if (file_exists($full_path)) {
			$lastmodified = filemtime($full_path);
			header('Pragma: cache');
			header('Cache-Control: public, must-revalidate, max-age=0');
			header('Connection: close');
			header('Expires: '.date('r', time()+60*60));
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
			header("Content-length: ".filesize($full_path));
	
			if(isset($_GET['DL'])){
				$mime_type="application/octet-stream";
				header("Content-disposition:attachment;filename=$file_name");
			} else {
				header("Content-disposition:inline;filename=$file_name");	
			}
	
			header("Content-type: $mime_type");
			set_time_limit(0);
			//readfile($full_path);
			readfile_chunked ($full_path);
			exit;
		} else {
			header("HTTP/1.0 404 not found");
			echo 'The file you requested could not be found';
			exit;
		}
	}
	
} else {

	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array(
		'header'	 => 'header.ihtml',
		'navigation' => 'navigation.ihtml',
		'file'	   => 'files/loadfile.ihtml',
		'footer'	 => 'footer.ihtml'
	));
	$page_details = get_page_details($space_key,$link_key);
	set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
	$t->set_block('file', 'RefreshBlock', 'RFBlock');
	$file_content = file_get_contents($full_path);
	$t->set_var('RFBlock',$file_content);	
	$t->parse('CONTENTS', 'header', true); 
	//create the left hand navigation 

	get_navigation();

	$t->parse('CONTENTS', 'file', true);
	$t->parse('CONTENTS', 'footer', true);

	print_headers();

	//output page
	$t->p('CONTENTS');
	exit;
}
function readfile_chunked ($filename) {
  $chunksize = 1*(1024*1024); // how many bytes per chunk
  $buffer = '';
  $handle = fopen($filename, 'rb');
  if ($handle === false) {
   return false;
  }
  while (!feof($handle)) {
   $buffer = fread($handle, $chunksize);
   print $buffer;
  }
  return fclose($handle);
}

function IPmatch() {
	global $CONFIG;
	foreach($CONFIG['FILE_MIRROR_IPMASKS'] as $mask) {
		if($match=bit_comp($_SERVER['REMOTE_ADDR'],$mask[0],$mask[1])) break;
	}
	return !empty($match);
}

function bit_comp($ad1,$ad2,$bmask) {
	$ex1=explode('.',$ad1);
	$ex2=explode('.',$ad2);
	
	for($i=0;$i<count($ex1);$i++) {
		$bbbits=min(8,$bmask);
		if(($ex1[$i] & ((1<<$bbbits)-1)<<(8-$bbbits)) != $ex2[$i]) return false;
		if(($bmask-=8)<1) break;
	}
	return true;
}
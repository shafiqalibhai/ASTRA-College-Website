<?php
/**
* Direct access url page
*
* Provides email/search engine friendly url access to certain modules
* 
*
*/

/**
* Include main configuration file
*/
//Used by admin/setup to test operation:
if (substr($_SERVER['REQUEST_URI'],-10)=="testDirect") {
	header("HTTP/1.0 200"); echo "TEST OK!"; exit;
}
require_once('local/config.inc.php');

//BREAK UP THE URL PATH USING '/' as delimiter
if(isset($_GET['m'])) {   //  ?m=... access
	$url_array=explode("/",$_GET['m']);array_unshift($url_array,'','');
} else {
	//  direct[.php]/... access
	$url_array=explode("/",str_replace($CONFIG['PATH'],"",$_SERVER['REQUEST_URI']));
	
	//  404 redirect to direct access
	if(substr($url_array[1],0,6)!='direct') {array_unshift($url_array,'');}
	if(substr($url_array[1],0,14)=='direct.php?404') {		
		array_shift($url_array);
		array_shift($url_array);
	}
}

//extract the module we are dealing from the url_array
$module=$url_array[2];	  

switch($module) {
	case 'prompt':
		redirect_auto_prompt($url_array,$CONFIG['FULL_URL']);
		break;
	
	case 'post':
		redirect_post($url_array,$CONFIG['FULL_URL']);
		break;

	case 'space':
		redirect_space($url_array,$CONFIG['FULL_URL']);
		break;

	case 'kb':
		redirect_kb($url_array,$CONFIG['FULL_URL']);
		break;
				
	case 'viewf':
		$_GET['path']=substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],'viewf')+6);
		require('viewfile.php');
		break;
	
	case 'rss':
//	echo 'rss';
		redirect_rss($url_array);
		break;
	default:
		if($module!="module") array_unshift($url_array,"");
		redirect_module($url_array,$CONFIG['FULL_URL'],$CONN);
		break;
}
exit; 

/**
* Display Error
*
* Displays an error message if the url access can't be interpreted
* 
* 
*/
function display_error($message) {


	global $CONFIG, $objSkins;
	
	require_once('includes/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	
	$t->set_file(array(
		'header'  => 'header.ihtml',
		'errors'  => 'errors.ihtml',
		'footer'  => 'footer.ihtml'));

	if (!is_object($objSkins)) {
		if (!class_exists('InteractSkins')) {
			require_once($CONFIG['BASE_PATH'].'/skins/lib.inc.php');
		}
		$objSkins = new InteractSkins();
	}
	$skin_key = $objSkins->getskin_key($space_key);
	$t->set_var('SKIN_KEY',$skin_key[0]);
	$t->set_var('SKIN_VERSION',$skin_key[1]);

	$t->set_var('PATH',$CONFIG['PATH']);
	$t->set_var('SERVER_NAME',$CONFIG['SERVER_NAME']);
	$t->set_block('footer', 'TagBlock', 'TGSBlock');
	$t->set_var('TGSBlock','');
	$t->set_var('HEADER_HOME_LINK',$CONFIG['FULL_URL']);
	$t->set_var('MESSAGE',$message);
	$t->set_var('PAGE_TITLE','No such Page!');
	$t->set_var('MESSAGE_COUNT','0');
	$t->parse('CONTENTS', 'header', true); 
	$t->parse('CONTENTS', 'errors', true);
	$t->parse('CONTENTS', 'footer', true); 
	print_headers();
	//output page
	$t->p('CONTENTS');

}

/**
* Redirect to autoprompt page
*
* Gets postkey from url_array, retrieves post information and redirects
* to prompt response page 
*
* @param $url_array request url broken into section
* 
*/
function redirect_auto_prompt($url_array,$FULL_URL) {
	header("Location: $FULL_URL/modules/forum/autoprompt/promptaction.php?post_key={$url_array[3]}");
} //end redirect_prompt 


/**
* Redirect to a thread page to view post
*
* Gets postkey and space key from url_array, retrieves post information and redirects
* to thread view page 
*
* @param $url_array request url broken into section
* 
*/
function redirect_post($url_array,$FULL_URL) {

	global $CONN, $CONFIG;
	$space_key = $url_array[3];
	$post_key = $url_array[4];
	

	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}module_space_links.module_key, {$CONFIG['DB_PREFIX']}posts.thread_key, {$CONFIG['DB_PREFIX']}modules.type_code, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}posts.user_key FROM {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND {$CONFIG['DB_PREFIX']}posts.post_key='$post_key'");	

	echo $CONN->ErrorMsg();
	if ($rs->EOF) {
	
		$message = 'The post you are trying to access no longer exists - it has probably been deleted';
		display_error($message);
	
	} else {
		$module_key  = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$module_type = $rs->fields[2];
		$parent_key = $rs->fields[3];
		
	}
	if ($module_type=='journal') {
		$journal_user_key=$rs->fields[4];
		header("Location: {$CONFIG['FULL_URL']}/modules/journal/entry.php?space_key=$space_key&module_key=$module_key&post_key=$post_key&journal_user_key=$journal_user_key");  //was parent_key!? fixed.
	} else {
		header("Location: {$CONFIG['FULL_URL']}/modules/forum/viewpost.php?space_key=$space_key&module_key=$module_key&post_key=$post_key");
	}
	
	
} //end redirect_post 



function redirect_space($url_array,$FULL_URL) {

	global $CONN, $CONFIG;
	$space_key = $url_array[3];

	header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key");
	
} //end redirect_space 



function redirect_kb($url_array,$FULL_URL) {
	global $CONN, $CONFIG;

	$space_key = $url_array[3];
	$module_key = $url_array[4];
	$entry_key = $url_array[5];
	header("Location: {$CONFIG['FULL_URL']}/modules/kb/singleentry.php?space_key=$space_key&module_key=$module_key&entry_key=$entry_key");

} //end redirect_kb 



/**
* Redirect to a space 
*
* Gets module_key, space_key, link_key from url_array, and redirects
* to module page 
*
* @param $url_array request url broken into sections
* 
*/
function redirect_module($url_array,$FULL_URL,$CONN) {
	global $CONFIG,$general_strings;
	
	if (!isset($url_array[4]) || $url_array[4]=='') {
	
		//must be a space the are wanting to access rather than a module
		
		$code = str_replace("/","",$url_array[3]);
		$rs = $CONN->Execute("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE code='$code'");
		if ($rs->EOF) {
			$message = 'Sorry. The '.$general_strings['space_text'].' you are trying to access doesn\'t seem to exist. Perhaps it has been deleted. Contact the page owner to find out.';
			display_error($message);
		
		} else {
			while (!$rs->EOF) {
				$space_key     = $rs->fields[0];
				$rs->MoveNext();
			
			}
			$rs->Close();
			header("Location: {$CONFIG['FULL_URL']}/spaces/space.php?space_key=$space_key");
		}	
		
	} else {
		$space_key = $url_array[3];
		$module_key = $url_array[4];
		
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.link_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key'");
		if ($rs->EOF) {
	
			$message = 'Sorry. The page you are trying to access doesn\'t seem to exist. Perhaps it has been deleted. Contact the page owner to find out.';
			display_error($message);
		
		} else {
	
			while (!$rs->EOF) {
		
				$code	  = $rs->fields[0];
				$link_key  = $rs->fields[1];
				$group_key = $rs->fields[2];
			
				$rs->MoveNext();
			
			}
		
			$rs->Close();
			if ($code=='space') {
				$space_key = $CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE module_key='$module_key'");
				header("Location: $FULL_URL/spaces/$code.php?space_key=$space_key");			
			} else {
				header("Location: $FULL_URL/modules/$code/$code.php?space_key=$space_key&module_key=$module_key&link_key=$link_key&group_key=$group_key");
			}
		
		}
	}	
} //end redirect_module

function redirect_rss($url_array) {
	global $CONN, $CONFIG;
	$space_key = $url_array[3];
	$module_key = $url_array[4];
	$ju_key=$tags='';
	if(isset($url_array[5])) {
		if(substr($url_array[5],0,1)=='t') {
			$tags='&tag_key='.substr($url_array[5],1);
		} else {
			$ju_key='&journal_user_key='.$url_array[5];
			if(isset($url_array[6])) {
				$tags='&tag_key='.substr($url_array[6],1);
			}
		}
	}

	$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}modules.type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}modules.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key'");

	$code	  = $rs->fields[0];

	header("Location: {$CONFIG['FULL_URL']}/modules/$code/rss.php?space_key=$space_key&module_key=$module_key$ju_key$tags");
}
?>
<?php
/**
* Search
*
* Provides a search function across all spaces
*/

/**
* Include main config file 
*/
require_once('local/config.inc.php');



//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$search_terms = $_GET['search_terms'];
$search_terms_raw = strip_tags($_GET['search_terms']);
$search_terms_highlight = '<strong><em>'.interact_stripslashes($search_terms_raw).'</em></strong>';
$rule		 = $_GET['rule'];
$current_user_key = $_SESSION['current_user_key'];

//check to see if user is logged in. If not refer to Login page.
authenticate_home();

if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once('includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  

$t->set_file(array(
	'header' => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'body'   => 'search.ihtml',
	'footer' => 'footer.ihtml'
));

$space_key=get_space_key();
$page_details = get_page_details($space_key,'');
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('PAGE_TITLE','Search');

$t->set_var('BREADCRUMBS','');
$t->set_block('navigation', 'ModuleHeadingBlock', 'ModuleHeadBlock');

$t->set_var('SEARCH_HEADING', sprintf($space_strings['search_heading2'], $general_strings['space_text']));
$t->set_var('YOUR_LINKS_STRING', sprintf($general_strings['your_links'], $general_strings['space_plural']));
$t->set_var('SPACE_STRING', $general_strings['space_plural']);

$t->set_var('SEARCH_STRING', $general_strings['search']);
$t->set_var('ALL_WORDS_STRING', $general_strings['all_words']);
$t->set_var('ANY_WORDS_STRING', $general_strings['any_words']);
$t->set_var('EXACT_PHRASE_STRING', $general_strings['exact_phrase']);
$t->set_var('SITE_CONTENT_STRING', $general_strings['site_content']);
$t->set_var('FORUM_POSTINGS_STRING', $general_strings['forum_postings']);

$t->parse('CONTENTS', 'header', true);  
get_navigation();

if (!$search_terms_raw) {

	$t->set_block('body', 'ResultsBlock', 'RBlock');
	$t->set_var('RBlock', '');

} else {

	if ($search_terms_raw!='') {

		//find out what groups user is a member of

		$groups_sql='(';
		$n=1;
		$sql = "select group_key from {$CONFIG['DB_PREFIX']}group_user_links where user_key='$current_user_key'";
		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {

			$groups_sql.='-1)';

		} else {
			
			$record_count=$rs->RecordCount();

			while (!$rs->EOF) {

				$current_row=$rs->CurrentRow();
				$group_key = $rs->fields[0];
				$group_access[$n]=$group_key;

				if(++$current_row==$record_count) {

					$groups_sql.="$group_key ";

				} else {

					$groups_sql.="$group_key, ";

				}

				$n++;
				$rs->MoveNext();

			}
			
			$rs->Close();

			$groups_sql.=')';

		}
 
		//$space_search_string = create_space_search_string($search_terms_raw, $rule);

		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}spaces.space_key,name FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' AND  {$CONFIG['DB_PREFIX']}spaces.type_key!='1' AND MATCH(name,short_name,code,description) AGAINST('$search_terms_raw') ORDER BY {$CONFIG['DB_PREFIX']}spaces.name";  
		$rs = $CONN->Execute($sql);
		
		echo $CONN->ErrorMsg();
		if ($rs->EOF) {

			$t->set_block('body', 'SpaceBlock', 'SBlock');
			$t->set_var('SBlock', sprintf($space_strings['space_search_fail'],$general_strings['space_text'],$search_terms_highlight));

		} else {

			$t->set_block('body', 'SpaceBlock', 'SBlock');
			$t->set_var('MESSAGE',sprintf($space_strings['search_results'],$search_terms_highlight));
			$number=1;

			while (!$rs->EOF) {

				$space_key = $rs->fields[0];
			 	$space_name=$rs->fields[1];
			   								
				$t->set_var('SPACE_KEY',$space_key);
				$t->set_var('SPACE_NAME',$space_name);
		 		$t->set_var('NUMBER',$number);
				$t->Parse('SBlock', 'SpaceBlock', true);
				$number++;
				$rs->MoveNext();

				}
				
			$rs->Close();

			}
		
		//if not superadmin limit search to own or open spaces
		$content_limit = ($_SESSION['userlevel_key']!=1)?" AND ({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key' OR {$CONFIG['DB_PREFIX']}spaces.access_level_key='1' OR {$CONFIG['DB_PREFIX']}spaces.access_level_key='3') AND ({$CONFIG['DB_PREFIX']}modules.status_key='1' OR {$CONFIG['DB_PREFIX']}modules.status_key='3') AND (group_key='0' OR group_key in $groups_sql) ":''; 
		
		//$content_search_string = create_content_search_string($search_terms, $rule);
$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}modules.type_code, {$CONFIG['DB_PREFIX']}modules.description,{$CONFIG['DB_PREFIX']}module_space_links.space_key,{$CONFIG['DB_PREFIX']}module_space_links.link_key FROM {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}space_user_links WHERE  {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}modules.type_code!='space' AND {$CONFIG['DB_PREFIX']}modules.type_code!='heading' AND   {$CONFIG['DB_PREFIX']}spaces.type_key!='1' $content_limit AND MATCH({$CONFIG['DB_PREFIX']}modules.name,{$CONFIG['DB_PREFIX']}modules.description) AGAINST('$search_terms_raw')  AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='2') ";

		$rs = $CONN->Execute($sql);
		
		if ($rs->EOF) {
			$t->set_block('body', 'ContentBlock', 'CBlock');
			$t->set_var('CBlock', sprintf($space_strings['content_search_fail'],$search_terms_highlight));
		
		} else {
		
			$t->set_block('body', 'ContentBlock', 'CBlock');
			$t->set_var(MESSAGE,sprintf($space_strings['search_results'],$search_terms_highlight));
			$number=1;
	
			while (!$rs->EOF) {

				$module_key = $rs->fields[0];
				$group_key = $rs->fields[1];
				$name = $rs->fields[2];
				$url = $rs->fields[3];
				
				if ($rs->fields[4]=='') {
				
					$description = $rs->fields[4];
				
				} else {
			
					$description = ' - '.$rs->fields[4];
		
				}
				
				$space_key = $rs->fields[5];
				$link_key = $rs->fields[6];								
				$t->set_var('GROUP_KEY',$group_key);
				$t->set_var('MODULE_KEY',$module_key);
				$t->set_var('LINK_KEY',$link_key);				
				$t->set_var('MODULE_NAME',$name);
				$t->set_var('CODE',$url);
				$t->set_var('PATH',$CONFIG['PATH']);
				$t->set_var('SPACE_KEY',$space_key);				
				$t->set_var('DESCRIPTION',$description);
				$t->set_var('NUMBER',$number);
				$t->Parse('CBlock', 'ContentBlock', true);
				$number++;
				$rs->MoveNext();
				
				}
				
			$rs->Close();	
				
			}

		//now search posts

		$postmodules="{$CONFIG['DB_PREFIX']}modules.type_code='forum'";
		if(!empty($CONFIG['SEARCH_ALL_JOURNALS'])){ 
			$postmodules="($postmodules OR {$CONFIG['DB_PREFIX']}modules.type_code='journal')";
		}

		//$forum_search_string = create_forum_search_string($search_terms, $rule);
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}posts.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}posts.post_key,{$CONFIG['DB_PREFIX']}posts.thread_key,{$CONFIG['DB_PREFIX']}posts.subject,first_name,last_name,{$CONFIG['DB_PREFIX']}posts.date_added,{$CONFIG['DB_PREFIX']}module_space_links.space_key FROM {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users,{$CONFIG['DB_PREFIX']}spaces,{$CONFIG['DB_PREFIX']}space_user_links, {$CONFIG['DB_PREFIX']}modules WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND  {$CONFIG['DB_PREFIX']}module_space_links.space_key={$CONFIG['DB_PREFIX']}space_user_links.space_key AND {$CONFIG['DB_PREFIX']}space_user_links.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}modules.module_key AND $postmodules AND ({$CONFIG['DB_PREFIX']}space_user_links.user_key='$current_user_key' OR {$CONFIG['DB_PREFIX']}spaces.access_level_key='1' OR {$CONFIG['DB_PREFIX']}spaces.access_level_key='3') AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) AND MATCH(subject,body) AGAINST('$search_terms_raw') AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='2')";

		$rs = $CONN->Execute($sql);
		echo $CONN->ErrorMsg();

		if ($rs->EOF) {

			$t->set_block('body', 'ForumBlock', 'FBlock');
			$t->set_var('FBlock', sprintf($space_strings['forum_search_fail'],$search_terms_highlight));

		} else {

			$t->set_block('body', 'ForumBlock', 'FBlock');
			$t->set_var('MESSAGE',sprintf($space_strings['search_results'],$search_terms_highlight));
			$number=1;
	
			while (!$rs->EOF) {

				$module_key = $rs->fields[0];
				$group_key = $rs->fields[1];
				$post_key = $rs->fields[2];
				$thread_key = $rs->fields[3];				
				$subject = $rs->fields[4];
				$added_by = $rs->fields[5].' '.$rs->fields[6];
				$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[7]),'short');
				$space_key = $rs->fields[8];
												
				$t->set_var('GROUP_KEY',$group_key);
				$t->set_var('MODULE_KEY',$module_key);
				$t->set_var('POST_KEY',$post_key);
				$t->set_var('THREAD_KEY',$thread_key);
				$t->set_var('SPACE_KEY',$space_key);				
				$t->set_var('PATH',$CONFIG['PATH']);
				$t->set_var('SUBJECT',$subject);
				$t->set_var('NUMBER',$number);
				$t->set_var('ADDED_BY',$added_by);
				$t->set_var('DATE_ADDED',$date_added);				
				$t->Parse('FBlock', 'ForumBlock', true);
				$number++;
				$rs->MoveNext();
				
			}
			
			$rs->Close();
		
		}

	} else {

		$t->set_block('body', 'ResultsBlock', 'RBlock');
		$t->set_var('RBlock', '');
		$t->set_var('MESSAGE', $space_strings['no_search_terms']);

	}

}

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);

$t->p('CONTENTS');
$CONN->Close();
exit;

/*
function create_content_search_string($search_terms, $rule) { 
	 

	global $CONFIG;
	// Split up $keywords by the delimiter (" ") 

	$arg = split(' ', $search_terms); 

	if ($rule == 'all') { 

		$joiner = 'AND'; 

	} elseif ($rule == 'any') { 

		$joiner = 'OR';  

	} 
	
	if ($rule != 'exact') { 
	
		for($i=0; $i<count($arg); $i++) { 

			if ($i==0) {

				$cond = "(({$CONFIG['DB_PREFIX']}modules.name LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}modules.description LIKE '%$arg[$i]%'))"; 

			} else {

				$cond = "$cond $joiner (({$CONFIG['DB_PREFIX']}modules.name LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}modules.description LIKE '%$arg[$i]%'))"; 

			}	   
						
		} 

	} else {
 
			$cond = "(({$CONFIG['DB_PREFIX']}modules.name LIKE '%$search_terms%') OR ". 
					"({$CONFIG['DB_PREFIX']}modules.description LIKE '$search_terms%'))"; 

	} 

	return $cond;

} // end function create_content_search_string

function create_space_search_string($search_terms, $rule) { 
	 
	global $CONFIG;
	
	// Split up $keywords by the delimiter (" ") 
	$arg = split(' ', $search_terms); 
	if ($rule == 'all') { 
	
		$joiner = 'AND'; 
	
	} elseif ($rule == 'any') { 
		
		$joiner = 'OR';  

	}
	 
	if ($rule != 'exact') {
	 
		for($i=0; $i<count($arg); $i++) { 
	
			if ($i==0) {
	
				$cond = "(({$CONFIG['DB_PREFIX']}spaces.name LIKE '%$arg[$i]%' OR {$CONFIG['DB_PREFIX']}spaces.code LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}spaces.description LIKE '%$arg[$i]%'))"; 
	
			} else {
	
				$cond = "$cond $joiner (({$CONFIG['DB_PREFIX']}spaces.name LIKE '%$arg[$i]%' OR {$CONFIG['DB_PREFIX']}spaces.code LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}spaces.description LIKE '%$arg[$i]%'))"; 
	
			}	   
						
	
		} 
	
	} else {
 
			$cond = "(({$CONFIG['DB_PREFIX']}spaces.name LIKE '%$search_terms%' OR {$CONFIG['DB_PREFIX']}spaces.code LIKE '%$arg[$i]%') OR ". 
					"({$CONFIG['DB_PREFIX']}spaces.description LIKE '$search_terms%'))"; 
	
	} 

	return $cond;

} // end function create_space_search_string

function create_forum_search_string($search_terms, $rule) { 
	 
	global $CONFIG;
	// Split up $keywords by the delimiter (" ") 

	$arg = split(' ', $search_terms); 
	if ($rule == 'all') { 

		$joiner = 'AND'; 

	} elseif ($rule == 'any') { 
		
		$joiner = 'OR';  
	
	} 
	
	if ($rule != 'exact') { 
	
		for($i=0; $i<count($arg); $i++) { 

			if ($i==0) {

				$cond = "(({$CONFIG['DB_PREFIX']}posts.subject LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}posts.body LIKE '%$arg[$i]%'))"; 

			} else {

				$cond = "$cond $joiner (({$CONFIG['DB_PREFIX']}posts.subject LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}posts.body LIKE '%$arg[$i]%'))"; 

			}	   
						
		} 

	} else {
 
			$cond = "(({$CONFIG['DB_PREFIX']}posts.subject LIKE '%$search_terms%') OR ". 
					"({$CONFIG['DB_PREFIX']}posts.body LIKE '$search_terms%'))"; 

	} 

	return $cond;

} // end function create_forum_search_string
*/
?>
<?php
// +------------------------------------------------------------------------+
// | This file is part of Interact.											|
// |																	  	| 
// | This program is free software; you can redistribute it and/or modify 	|
// | it under the terms of the GNU General Public License as published by 	|
// | the Free Software Foundation (version 2)							 	|
// |																	  	|	 
// | This program is distributed in the hope that it will be useful, but  	|
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   		|
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 	|
// | General Public License for more details.							 	|
// |																	  	|	 
// | You should have received a copy of the GNU General Public License		|
// | along with this program; if not, you can view it at				  	|
// | http://www.opensource.org/licenses/gpl-license.php				   		|
// +------------------------------------------------------------------------+

/**
* Search a space
*
* Provides search functionality to search a specific space
*
* @package Spaces
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: searchspace.php,v 1.21 2007/07/30 01:57:07 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/


require_once('../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');

$space_key	= $_GET['space_key'];
$search_terms = $_GET['search_terms'];
$rule		 = $_GET['rule'];

//check we have the required variables
check_variables(true,false);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];	 


require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'body'			=> 'spaces/searchspace.ihtml',
 	'footer'		  => 'footer.ihtml'
));
 
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!class_exists('InteractDate')) {

	require_once('../includes/lib/date.inc.php');
	
}
$dates = new InteractDate();

$t->parse('CONTENTS', 'header', true);
$t->set_var('SEARCH_HEADING', sprintf($space_strings['search_heading'], $general_strings['space_text']));
$t->set_var('SEARCH_STRING', $general_strings['search']);
$t->set_var('ALL_WORDS_STRING', $general_strings['all_words']);
$t->set_var('ANY_WORDS_STRING', $general_strings['any_words']);
$t->set_var('EXACT_PHRASE_STRING', $general_strings['exact_phrase']);
$t->set_var('SITE_CONTENT_STRING', $general_strings['site_content']);
$t->set_var('FORUM_POSTINGS_STRING', $general_strings['forum_postings']);

get_navigation();

if (!$search_terms) {

	$t->set_block('body', 'ResultsBlock', 'RBlock');
	$t->set_var('RBlock', '');
	
} else {

	if ($search_terms!='') {
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
			
			$groups_sql.=')';
		
		}
 
		$content_search_string = create_content_search_string($search_terms, $rule);
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}modules.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}modules.name, {$CONFIG['DB_PREFIX']}modules.type_code, {$CONFIG['DB_PREFIX']}modules.description, {$CONFIG['DB_PREFIX']}module_space_links.link_key FROM {$CONFIG['DB_PREFIX']}modules,  {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}spaces WHERE  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND ({$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key') AND ({$CONFIG['DB_PREFIX']}modules.status_key='1' OR {$CONFIG['DB_PREFIX']}modules.status_key='3') AND (group_key='0' OR group_key in $groups_sql) AND ($content_search_string) AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='2') ORDER BY {$CONFIG['DB_PREFIX']}modules.name";

		$rs = $CONN->Execute($sql);
		
		if ($rs->EOF) {
		
			$t->set_block('body', 'ContentBlock', 'CBlock');
			$t->set_var('CBlock', sprintf($space_strings['content_search_fail'],$search_terms));
		
		} else {
	
			$t->set_block('body', 'ContentBlock', 'CBlock');
			$t->set_var('MESSAGE',sprintf($space_strings['search_results'],$search_terms));
			$number=1;
			
			while (!$rs->EOF) {

				$module_key = $rs->fields[0];
				$group_key = $rs->fields[1];
				$name = $rs->fields[2];
				$code = $rs->fields[3];
				
				if ($rs->fields[4]=='') {
				
					$description = $rs->fields[4];
				
				} else {
					
					$description = ' - '.$rs->fields[4];
				
				}
				
				$link_key = $rs->fields[5];				
				$t->set_var('GROUP_KEY',$group_key);
				$t->set_var('MODULE_KEY',$module_key);
				$t->set_var('LINK_KEY',$link_key);				
				$t->set_var('MODULE_NAME',$name);
				$t->set_var('CODE',$code);
				$t->set_var('PATH',$CONFIG['PATH']);
				$t->set_var('DESCRIPTION',$description);
				$t->set_var('NUMBER',$number);
				$t->Parse('CBlock', 'ContentBlock', true);
				$number++;
				$rs->MoveNext();
				
			}
			
		}

		//now search Forum postings
		
		$forum_search_string = create_forum_search_string($search_terms, $rule);
		$sql = "SELECT DISTINCT {$CONFIG['DB_PREFIX']}posts.module_key,{$CONFIG['DB_PREFIX']}module_space_links.group_key,{$CONFIG['DB_PREFIX']}posts.post_key,{$CONFIG['DB_PREFIX']}posts.thread_key,{$CONFIG['DB_PREFIX']}posts.subject, first_name, last_name, {$CONFIG['DB_PREFIX']}posts.date_added FROM {$CONFIG['DB_PREFIX']}module_space_links, {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}module_space_links.module_key={$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND ({$CONFIG['DB_PREFIX']}module_space_links.group_key='0' OR {$CONFIG['DB_PREFIX']}module_space_links.group_key in $groups_sql) AND ($forum_search_string) AND ({$CONFIG['DB_PREFIX']}module_space_links.status_key!='2') ORDER BY {$CONFIG['DB_PREFIX']}posts.subject";

		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
			$t->set_block('body', 'ForumBlock', 'FBlock');
			$t->set_var('FBlock', sprintf($space_strings['forum_search_fail'],$search_terms));
		} else {
			$t->set_block('body', 'ForumBlock', 'FBlock');
			$t->set_var('MESSAGE',sprintf($space_strings['search_results'],$search_terms));
			$number=1;
	
			while (!$rs->EOF) {

				$module_key = $rs->fields[0];
				$group_key = $rs->fields[1];
				$post_key = $rs->fields[2];
				$thread_key = $rs->fields[3];				
				$subject = $rs->fields[4];
				$added_by = $rs->fields[5].' '.$rs->fields[6];
				$date_added = $dates->formatDate($CONN->UnixTimeStamp($rs->fields[7]),'short', true);								
				$t->set_var('GROUP_KEY',$group_key);
				$t->set_var('MODULE_KEY',$module_key);
				$t->set_var('POST_KEY',$post_key);
				$t->set_var('THREAD_KEY',$thread_key);
				$t->set_var('PATH',$CONFIG['PATH']);
				$t->set_var('SUBJECT',$subject);
				$t->set_var('NUMBER',$number);
				$t->set_var('ADDED_BY',$added_by);
				$t->set_var('DATE_ADDED',$date_added);				
				$t->Parse('FBlock', 'ForumBlock', true);
				$number++;
				$rs->MoveNext();
				
				}
			
			}
	
	}else {
	
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
?>
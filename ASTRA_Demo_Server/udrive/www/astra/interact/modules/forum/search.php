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
* @version $Id: search.php,v 1.18 2007/07/30 01:56:59 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/


require_once('../../local/config.inc.php');


//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/space_strings.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

$space_key	= $_GET['space_key'];
$module_key   = $_GET['module_key'];
$search_terms = $_GET['search_terms'];
$rule		 = $_GET['rule'];
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,false,true);

//check to see if user is logged in. If not refer to Login page.
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];	 


require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header'		  => 'header.ihtml',
	'navigation'	  => 'navigation.ihtml',
	'body'			=> 'forums/search.ihtml',
 	'footer'		  => 'footer.ihtml'
));
 
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);

if (!is_object($objDates)) {

	if (!class_exists('InteractDate')) {

		require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
	}

	$objDates = new InteractDate();

}

$t->parse('CONTENTS', 'header', true);
$t->set_var('SEARCH_HEADING', sprintf($forum_strings['search_heading'], $page_details['module_name']));
$t->set_var('SEARCH_STRING', $general_strings['search']);
$t->set_var('ALL_WORDS_STRING', $general_strings['all_words']);
$t->set_var('ANY_WORDS_STRING', $general_strings['any_words']);
$t->set_var('EXACT_PHRASE_STRING', $general_strings['exact_phrase']);
$t->set_var('FORUM_POSTINGS_STRING', $general_strings['forum_postings']);
$t->set_var('BACK_STRING', $general_strings['back']);
$t->set_var('MODULE_KEY',$module_key);

get_navigation();

if (!$search_terms) {

	$t->set_block('body', 'ResultsBlock', 'RBlock');
	$t->set_var('RBlock', '');
	$t->set_var('MESSAGE', $space_strings['no_search_terms']);
	
} else {

	//search Forum postings
		
	$forum_search_string = create_forum_search_string($search_terms, $rule);
	$sql = "SELECT {$CONFIG['DB_PREFIX']}posts.post_key,{$CONFIG['DB_PREFIX']}posts.thread_key,{$CONFIG['DB_PREFIX']}posts.subject, FirstName, LastName, {$CONFIG['DB_PREFIX']}posts.date_added FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE   {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}posts.module_key AND {$CONFIG['DB_PREFIX']}posts.module_key='$module_key' AND ($forum_search_string) ORDER BY {$CONFIG['DB_PREFIX']}posts.subject";

	$rs = $CONN->Execute($sql);

	if ($rs->EOF) {
	
		$t->set_block('body', 'ForumBlock', 'FBlock');
		$t->set_var('FBlock', sprintf($space_strings['forum_search_fail'],$search_terms));
	
	} else {
	
		$t->set_block('body', 'ForumBlock', 'FBlock');
		$t->set_var('MESSAGE',sprintf($space_strings['search_results'],$search_terms));
		$number=1;
	
		while (!$rs->EOF) {

		  	$post_key = $rs->fields[0];
			$thread_key = $rs->fields[1];				
			$subject = $rs->fields[2];
			$added_by = $rs->fields[3].' '.$rs->fields[4];
			$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[5]),'short', true);								
			$t->set_var('GROUP_KEY',$group_key);
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
	
} 

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);

$t->p('CONTENTS');
$CONN->Close();
exit;


function create_forum_search_string($search_terms, $rule) { 
	 
	global $CONFIG;
	// Split up $keywords by the delimiter (" ") 
	$arg = split(' ', $search_terms); 
	
	if ($rule == 'all' || $rule=='') { 
		
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
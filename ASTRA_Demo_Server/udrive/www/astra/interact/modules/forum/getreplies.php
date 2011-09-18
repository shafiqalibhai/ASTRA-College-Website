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
* Forum retrieve replies 
*
* get a formatted thread of replies for a given post 
*
* @package Forum
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2006 
* @version $Id: getreplies.php,v 1.1 2007/01/12 00:33:24 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once('../../includes/pear/JSON.php');
//get language strings

require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');

//set variables

$post_key	= $_GET['post_key'];



$CONN->SetFetchMode('ADODB_FETCH_ASSOC');
$subject = $CONN->GetOne("SELECT {$CONFIG['DB_PREFIX']}posts.subject FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='$post_key'");
$rs_all = $CONN->GetArray("SELECT {$CONFIG['DB_PREFIX']}posts.thread_key, {$CONFIG['DB_PREFIX']}posts.post_key, {$CONFIG['DB_PREFIX']}posts.subject,{$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}posts.parent_key, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}posts.added_by_key,{$CONFIG['DB_PREFIX']}posts.body FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}posts.thread_key='$post_key'");
$thread = array();
//$thread['replies'] = array();
formatThread($rs_all,$post_key,$indent=10, '', $subject, $thread);
//create a new instance of Services_JSON
$json = new Services_JSON();
$output = $json->encode($thread);
print($output);
exit;

$CONN->Close();	   
function formatThread($replies,$parent_key,$indent=0, $can_edit=false, $subject, &$thread) {
	
		global $t, $general_strings, $CONN, $CONFIG;
		
		
		$objDate = singleton::getInstance('date');
		$count = count($replies);
		
		for($i=0; $i<$count; $i++) {
			
			if ($replies[$i]['parent_key']==$parent_key) { 

				$post = array();
				$post['post_key'] = $replies[$i]['post_key'];
				$post['thread_key'] = $replies[$i]['thread_key'];
				
				if ($replies[$i]['subject']=='Re: '.$subject){
					
					$post['subject'] = substr(strip_tags($replies[$i]['body']),0,40);
					
				} else {
					$post['subject'] = $replies[$i]['subject'];
					
				}
				
				$post['added_by']   = $replies[$i]['first_name'].' '.$replies[$i]['last_name'];
				$post['indent'] =  $indent.'px';
				$post['date_added'] = $objDate->formatDate($CONN->UnixTimeStamp($replies[$i]['date_added']),'short',true);
				array_push($thread, $post);
				formatThread($replies, $replies[$i]['post_key'], $indent+10, $can_edit, $subject, $thread);
				
			
			
				
							
			}
		}

		return true;
		
	} //end formatThread()
exit;

?>
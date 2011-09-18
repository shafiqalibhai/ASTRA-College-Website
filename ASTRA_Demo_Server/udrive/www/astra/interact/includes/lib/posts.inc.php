<?php

// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education				  |
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
* Post  functions
*
* Contains any functions related to adding, modifying, displaying posts
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2005 
* @version $Id: posts.inc.php,v 1.29 2007/07/17 23:25:01 websterb4 Exp $
* 
*/

/**
* A class that contains methods related to post functions 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying posts, for forums,
* news, blogs, etc. 
* 
* @package Posts
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

/**
* A class that contains methods for retieving and displaying and updating 
* posts
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying posts 
* 
* @package Posts
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractPosts {

	/**
	* array of posts that have replies - index = post_key
	* @access private
	* @var array 
	*/
	var $_has_replies = array();

	/**
	* key of current module
	* @access private
	* @var array 
	*/
	var $_module_key = '';
		
	/**
	* key of current space
	* @access private
	* @var array 
	*/
	var $_space_key = '';

	/**
	* A method of class Posts to initialise any required variables
	* @param  int $module_key key of current module 
	* @param  int $space_key key of current space
	* @return true
	*/
	
	function setVars($module_key='', $space_key='') {
	
		$this->_space_key = $space_key;
		$this->_module_key = $module_key;
		return true;
			
	}
	/**
	* A method of class Posts to count entries for a given user or module or both
	* @param  int $module_key key of module to count posts for
	* @param  int $user_key key of user to count posts for
	* @return int $post_count number of posts for given user or module 
	*/
	
	function countPosts($module_key='', $user_key='', $post_key='', $parent_key='') {
	
		global $CONN, $CONFIG;
		
		$module_limit 	= ($module_key!='') ? 'module_key=\''.$module_key.'\'' : '';
		$user_limit 	= ($user_key!='') ? 'user_key=\''.$user_key.'\'' : '';
		$operator 		= ($user_key!='' && $module_key!='') ? 'AND' : '';
		if ($post_key!='') {
			$post_limit = ($user_key!='' ||  $module_key!='') ? ' AND (parent_key=\''.$post_key.'\' OR thread_key=\''.$post_key.'\')' : '(parent_key=\''.$post_key.'\' OR thread_key=\''.$post_key.'\' AND post_key!=\''.$post_key.'\')';
			
		}
		
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE $module_limit $operator $user_limit $post_limit");
		return $rs->RecordCount();
		
	} //end countPosts()
	
	/**
	* A method of class Posts to add a new post
	* @param  array $field_data an array of field names to insert
	* @param  array $post_data array of data for new post
	* @return true true if add successful 
	*/
	
	function addPost($field_data,$post_data) {
	
		global $CONN, $CONFIG, $objDb, $objTags;
		
		if (!isset($objDb) || !is_object($objDb)) {
			if (!class_exists('InteractDb')) {
				require_once('../../includes/lib/db.inc.php');
			}
			$objDb = new InteractDb();
		}
		
		$post_data['date_added'] = date('Y-m-d H:i:s');
		if (isset($post_data['date_published_year'])){
			$post_data['date_published'] = $post_data['date_published_year'].'-'.$post_data['date_published_month'].'-'.$post_data['date_published_day'].' '.$post_data['date_published_hour'].':'.$post_data['date_published_minute']; 
		
		} else {
			$post_data['date_published'] = $post_data['date_added'];
		}

		if ($CONN->Execute($objDb->getInsertSql('posts',$field_data,$post_data))===false) {
			return $CONN->ErrorMsg();
		} else {		
			$post_key = $CONN->Insert_ID();
			//if parent post add thread key
			if (!isset($post_data['parent_key']) || $post_data['parent_key']==0 || $post_data['parent_key']=='') {
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}posts SET thread_key='$post_key' WHERE post_key='$post_key'");	
			}
			
			//see if any tags to add
			if (isset($post_data['tag_list']) && $post_data['tag_list']!='') {
				if(!isset($objTags) || !is_object($objTags)) {
					if (!class_exists('InteractTags')){
						require_once($CONFIG['BASE_PATH'].'/includes/lib/tags.inc.php');
					}
					$objTags = new InteractTags();
				}	
				$objTags->addTags($post_data['tag_list'], $post_data['module_key'],$post_data['added_by_key'], $post_key);
				
			}
			//add monitor flag if needed
			if (isset($post_data['monitor_post']) && $post_data['monitor_post']==1) {

				$CONN->Execute("INSERT into {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, monitor_post) VALUES ('{$post_data['module_key']}','$post_key','{$post_data['added_by_key']}','{$post_data['monitor_post']}')");

			}
			//email anybody monitoring replies
			$this->emailPostMonitors($post_data['parent_key'],$post_key);
			return $post_key;
		}		
		
	} //end addPost()

	/**
	* A method of class Posts to modify an existing post
	* @param  array $field_data an array of field names to modify
	* @param  array $post_data array of data for post
	* @return true true if modify successful 
	*/
	
	function modifyPost($field_data,$post_data) {
	
		global $CONN, $CONFIG, $objDb, $objTags;
		
		if (!isset($objDb) || !is_object($objDb)) {
			if (!class_exists('InteractDb')) {
				require_once('../../includes/lib/db.inc.php');
			}
			$objDb = new InteractDb();
		}
		
		$post_data['date_modified'] = date('Y-m-d H:i:s');

		if (isset($post_data['date_published_year'])){
			$post_data['date_published'] = $post_data['date_published_year'].'-'.$post_data['date_published_month'].'-'.$post_data['date_published_day'].' '.$post_data['date_published_hour'].':'.$post_data['date_published_minute']; 
		} else {
			$post_data['date_published'] == $post_data['date_added'];
		}
		$key['name'] = 'post_key';
		$key['value'] = $post_data['post_key'];
		$sql = $objDb->getUpdateSql('posts',$field_data,$post_data, $key);
		if (isset($post_data['multi_entry_key']) && $post_data['multi_entry_key']>0) {
			if (is_array($post_data['user_keys'])) {
				$count = count($post_data['user_keys']);
				$multi_entry_sql='(';
				for($i=0;$i<$count;$i++) {
					if ($i!=$count-1) {
						$multi_entry_sql .= $post_data['user_keys'][$i].',';
					} else {
						$multi_entry_sql .= $post_data['user_keys'][$i].')';
					}
				}
			}
			$sql .= ' OR (user_key IN'.$multi_entry_sql.' AND multi_entry_key=\''.$post_data['multi_entry_key'].'\')';	
		}
		if ($CONN->Execute($sql)===false) {
			return $CONN->ErrorMsg();	
		} else {		
			//add mnitor if required
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='{$post_data['post_key']}'");
			if (isset($post_data['monitor_post']) && $post_data['monitor_post']==1) {
				$CONN->Execute("INSERT into {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, monitor_post) VALUES ('{$post_data['module_key']}','$post_key','{$post_data['modified_by_key']}','{$post_data['monitor_post']}')");
			} 
			//delete any existing tags
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}tag_links WHERE module_key='{$post_data['module_key']}' AND entry_key='{$post_data['post_key']}'");
			//see if any tags to add
			if (!empty($post_data['tag_list'])) {
				if(!isset($objTags) || !is_object($objTags)) {
					if (!class_exists('InteractTags')){
						require_once($CONFIG['BASE_PATH'].'/includes/lib/tags.inc.php');
					}
					$objTags = new InteractTags();
				}	
				$objTags->addTags($post_data['tag_list'], $post_data['module_key'],$post_data['added_by_key'], $post_data['post_key']);
				
			}
			return true;
		}		
		
	} //end modifyPost()

	/**
	* A method of class Posts to delete an existing post
	* @param  array $post_data array of data for post
	* @return true true if delete successful 
	*/
	function deletePost($post_data) {
	
		global $CONN, $CONFIG;
		
		$sql = "DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE post_key='{$post_data['post_key']}'";
		
		if (isset($post_data['multi_entry_key']) && $post_data['multi_entry_key']>0) {
			if (is_array($post_data['user_keys'])) {
				$count = count($post_data['user_keys']);
				$multi_entry_sql='(';
				for($i=0;$i<$count;$i++) {
					if ($i!=$count-1) {
						$multi_entry_sql .= $post_data['user_keys'][$i].',';
					} else {
						$multi_entry_sql .= $post_data['user_keys'][$i].')';
					}
				}
			}
			//$sql .= ' OR (user_key IN'.$multi_entry_sql.' AND multi_entry_key=\''.$post_data['multi_entry_key'].'\')';
			//get an array of post_keys for siblings
			$rs_siblings = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE user_key IN".$multi_entry_sql." AND multi_entry_key='".$post_data['multi_entry_key']."' AND post_key!='".$post_data['post_key']."'");	
			
		}
		if ($CONN->Execute($sql)===false) {
			return $CONN->ErrorMsg();	
		} else {		
			//delete any existing tags
			if (!empty($post_data['post_key']) && !empty($post_data['module_key'])){
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}tag_links WHERE module_key='{$post_data['module_key']}' AND entry_key='{$post_data['post_key']}'");
			}
			
      		//delete any post user links
      		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key='{$post_data['post_key']}'");
			//now delete any siblings and children
			$child_data['multi_entry_key']=0;
			if (isset($post_data['post_key']) && $post_data['post_key']!=0) {
				$rs_children = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts where parent_key='{$post_data['post_key']}' AND module_key='$this->_module_key'");
			
				while(!$rs_children->EOF) {
					$child_data['post_key'] = $rs_children->fields[0];
					$this->deletePost($child_data);
					$rs_children->MoveNext();
				}
			}
			
			if(isset($rs_siblings) && !$rs_siblings->EOF) {
				$sibling_data['multi_entry_key']=0;
				while(!$rs_siblings->EOF) {
					$sibling_data['post_key'] = $rs_siblings->fields[0];
					$this->deletePost($sibling_data);
					$rs_siblings->MoveNext();
				}
			}
			
			return true;
		}		
		
	} //end deletePost()

	/**
	* A method of class Posts to retrieve an array of post data
	* @param  int $module_key key of module to retrieve posts for
	* @param  int $user_key key of user to retrieve posts for
	* @param  int $post_key key of post if only single post required
	* @param  int $parent_key key of parent if children of specific post required
	* @param  int $thread_key key of thread if full thread required
	* @param  int $array set to true if result set required as array
	* @return array $post_data either an array or adodb recordsetof post data
	*/
	
	function getPostData($limits, $array=false, $sort_order='') {
	
		global $CONN, $CONFIG, $objTags;
		$post_limit   = !empty($limits['post_key'])?"AND post_key='{$limits['post_key']}'":'';	
		$user_limit   = !empty($limits['user_key'])?"AND {$CONFIG['DB_PREFIX']}posts.user_key='{$limits['user_key']}'":'';	
		$parent_limit = isset($limits['parent_key'])?"AND parent_key='{$limits['parent_key']}'":'';	
		$thread_limit = !empty($limits['thread_key'])?"AND thread_key='{$limits['thread_key']}'":'';	
		$module_limit = !empty($limits['module_key'])?"AND {$CONFIG['DB_PREFIX']}posts.module_key='{$limits['module_key']}'":'';
		
		if (isset($limits['selected_posts']) && $limits['selected_posts']!='' && is_array($limits['selected_posts'])) {
			$n=0;
			$posts_limit = '(';
			foreach($limits['selected_posts'] as $value){
				if ($n==0) {
					$posts_limit .= $value;
				} else {
					$posts_limit .= ','.$value;
				}
				$n++;
			}
			$posts_limit .= ')';
			$selected_posts = "AND post_key IN $posts_limit";	
		} else {
			$selected_posts = '';
		}
		$CONN->SetFetchMode('ADODB_FETCH_ASSOC');
		if ($array===true) {
			$method = 'GetArray';	
		} else {
			if (isset($limits['row_limit'])) {
				$method = 'SelectLimit';
			} else {
				$method = 'Execute';	
			}
		}

		if (isset($limits['tag_key']) && $limits['tag_key']!='' && $limits['tag_key']!='0') {
			$posts = $CONN->$method("SELECT added_by_key,post_key, thread_key, parent_key, added_by_key, modified_by_key, subject, body, extended_body, {$CONFIG['DB_PREFIX']}posts.date_added, date_published, status_key, multi_entry_key, {$CONFIG['DB_PREFIX']}users.first_name as first_name,{$CONFIG['DB_PREFIX']}users.last_name as last_name FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}tag_links WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}posts.post_key={$CONFIG['DB_PREFIX']}tag_links.entry_key $module_limit AND {$CONFIG['DB_PREFIX']}tag_links.tag_key='{$limits['tag_key']}' $user_limit $post_limit $parent_limit $selected_posts ORDER BY date_published $sort_order", $limits['row_limit']);
						
		} else {
			
			$posts = $CONN->$method("SELECT added_by_key,post_key, thread_key, parent_key, added_by_key, modified_by_key, subject, body, extended_body, {$CONFIG['DB_PREFIX']}posts.date_added, date_published, status_key, multi_entry_key, {$CONFIG['DB_PREFIX']}users.first_name as first_name,{$CONFIG['DB_PREFIX']}users.last_name as last_name FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key $module_limit $user_limit $post_limit $parent_limit $thread_limit $selected_posts {$limits['date_limit']} ORDER BY date_published $sort_order", $limits['row_limit']); 
			
		}
		echo $CONN->ErrorMsg();
		$CONN->SetFetchMode('ADODB_FETCH_NUM');
		return $posts;
		
	} //end getPostData()
	


	/**
	* A method of class Posts to format a thread
	* @param  array $replies an array of reply data
	* @param  int $parent_key key of parent to show replies for
	* @param  int $indent indent to show nesting of replies
	* @return true
	*/
	
	function formatThread($replies,$parent_key,$indent=0, $can_edit=false) {
	
		global $t, $general_strings, $objDate,  $CONN, $CONFIG;
		
		if (!isset($objDate) || !is_object($objDate)){
			if (!class_exists('InteractDate')) {
				require_once('../../includes/lib/date.inc.php');
			}
			$objDate = new InteractDate();
		}
		$count = count($replies);
		
		for($i=0; $i<$count; $i++) {
			
			if ($replies[$i]['parent_key']==$parent_key) { 

				$this->_has_replies[$replies[$i]['parent_key']]=true;
				$t->set_var('COMMENT_SUBJECT_VALUE', $replies[$i]['subject']);
				$t->set_var('COMMENT_BODY_VALUE', $replies[$i]['body']);
				$t->set_var('POST_KEY', $replies[$i]['post_key']);
			
				$fname=empty($replies[$i]['unauth_name'])?$replies[$i]['first_name']:$replies[$i]['unauth_name'];

				$t->set_var('POSTED_BY_DETAILS',$_SESSION['current_user_key']?
					((empty($replies[$i]['unauth_name'])?	
	"<a href=\"{$CONFIG['PATH']}/users/userdetails.php?user_key={$replies[$i]['added_by_key']}\"":
	"<a href=\"{$CONFIG['PATH']}/spaces/emailuser.php?unauth_post_key={$replies[$i]['post_key']}\""). "target=\"{$replies[$i]['added_by_key']}\">$fname</a>"):
					$fname);

				if(!empty($replies[$i]['unauth_url'])) {
					$uview=$ulink=$replies[$i]['unauth_url'];
					if(preg_match('|^https?://|',$uview)) {
						$uview=substr($uview,strpos($uview,'/')+2);
					} else {
						$ulink="http://".$ulink;
					}
					$t->set_var('POSTED_BY_DETAILS'," (<a href=\"{$ulink}\" rel=\"nofollow\">{$uview}</a>)",true);
				}

				$t->set_var('MARGIN_LEFT', $indent);
				$t->set_var('ADDED_BY_KEY',$replies[$i]['added_by_key']);
				
				$t->set_var('POSTED_TIME', $objDate->formatDate($CONN->UnixTimestamp($replies[$i]['date_added']),'short',true));

				if ($can_edit==true || (!empty($_SESSION['current_user_key']) && $_SESSION['current_user_key']==$replies[$i]['added_by_key'])) {
					
					$t->set_var('EDIT_LINK', get_admin_tool($CONFIG['PATH'].'/modules/general/commentinput.php?space_key='.$this->_space_key.'&module_key='.$this->_module_key.'&group_key=0&post_key='.$replies[$i]['post_key'].'&action=Modify&referer='.urlencode($_SERVER['REQUEST_URI'])));
					
				} else {
					$t->set_var('EDIT_LINK','');					
				}
				$t->parse('CommBlock', 'CommentBlock', true);
				$this->formatThread($replies, $replies[$i]['post_key'], $indent+10, $can_edit);			
			
				$t->parse('CommEndBlock', 'CommentEndBlock', true);

			}
		}

		return $this->_has_replies;
		
	} //end formatThread()
	
	/**
	* A method of class Posts to format a single post
	* @param  array $postdata an array of post data
	* @return true
	*/
	
	function formatPost($post_data) {
	
		global $t, $general_strings, $objDate, $CONN;
		
		if (!isset($objDate) || !is_object($objDate)){
			if (!class_exists('InteractDate')) {
				require_once('../../includes/lib/date.inc.php');
			}
			$objDate = new InteractDate();
		}
		
		$t->set_var('COMMENT_SUBJECT_VALUE', $post_data[0]['subject']);
		$t->set_var('COMMENT_BODY_VALUE', $post_data[0]['body']);
		
		$t->set_var('USER_FIRST_NAME', $post_data[0]['first_name']);
		$t->set_var('POSTED_BY_DETAILS', $post_data[0]['first_name']);
		$t->set_var('MARGIN_LEFT', '');
		$t->set_var('POSTED_TIME', $objDate->formatDate($CONN->UnixTimestamp($post_data[0]['date_added']),'short',true));

		$t->parse('CommBlock', 'CommentBlock', true);
		$t->parse('CommEndBlock', 'CommentEndBlock', true);
		return true;
		
	} //end formatPost()
	
	/**
	* Method called to email notifications to people monitoring post 
	*
	* @param int $parent_key key of post being replied to 
	* @param int $post_key key of new post 
	* @return true if link exists 
	*/

	function emailPostMonitors($parent_key, $post_key) {

		global $CONN, $CONFIG;

		require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
		$rs = $CONN->Execute("SELECT email FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}post_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}post_user_links.user_key AND post_key='$parent_key' AND monitor_post='1' AND {$CONFIG['DB_PREFIX']}users.user_key!='{$_SESSION['current_user_key']}'");
		
		if ($rs->EOF) {
		
			return false;
		
		} else {
		
			require_once($CONFIG['INCLUDES_PATH'].'/pear/Mail.php');

			if ($CONFIG['EMAIL_TYPE']=='sendmail') {
	
				$params['sendmail_path'] = $CONFIG['EMAIL_SENDMAIL_PATH'];
				$params['sendmail_args'] = $CONFIG['EMAIL_SENDMAIL_ARGS'];
 		
			} else if ($CONFIG['EMAIL_TYPE']=='smtp') {
	
				$params['host']	 = $CONFIG['EMAIL_HOST']; 
				$params['port']	 = $CONFIG['EMAIL_PORT'] ; 
				$params['auth']	 = $CONFIG['EMAIL_AUTH'];  
				$params['username'] = $CONFIG['EMAIL_USERNAME']; 
				$params['password'] = $CONFIG['EMAIL_PASSWORD'];
		
			}



$postaddress=$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH']."post/".$this->_space_key.'/'.$post_key;

	

			$mail_object =& Mail::factory($CONFIG['EMAIL_TYPE'], $params);
			$body	= sprintf($forum_strings['monitor_post_email'], $CONFIG['FULL_URL']);
			$body	.= "\n\n";
			$body	.= $postaddress;
			$headers['From']	= $CONFIG['NO_REPLY_EMAIL'];
			$headers['Subject'] = sprintf($forum_strings['monitor_post_subject'], $CONFIG['SERVER_NAME']);
	   
			while(!$rs->EOF) {
			
				$headers['To'] = $rs->fields[0];
				$to = $rs->fields[0];
				$mail_object->send($to, $headers, $body);
				$rs->MoveNext();
				
			}
		}

	} //end emailPostMonitors() method	

}
?>
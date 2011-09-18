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
* Forum Class
*
* Contains the Forum class for all methods and datamembers related
* to adding, modifying and viewing forum posts
*
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.40 2007/07/26 22:10:52 glendavies Exp $
* 
*/

/**
* A class that contains methods for retrieving and displaying and updating 
* forum posts
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying forum posts 
* 
* @package Journals
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractForum {

	/**
	* space key of current forum
	* @access private
	* @var int 
	*/
	var $_space_key = '';

	/**
	* module key of current forum
	* @access private
	* @var int 
	*/
	var $_module_key = '';
	
	/**
	* group key of current forum
	* @access private
	* @var int 
	*/
	var $_group_key = '';
	
	/**
	* user key of current user
	* @access private
	* @var int 
	*/
	var $_user_key = '';
	
	/**
	* admin status of current user
	* @access private
	* @var true/false 
	*/
	var $_is_admin = '';
	
	/**
	* settings for current forum
	* @access private
	* @var array 
	*/
	var $_forum_settings = '';
	

	/**
	* array of language strings for forum module
	* @access private
	* @var array 
	*/
	var $_forum_strings = '';
	
 		
	
	/**
	* Constructor for Forum Class. Sets required variables
	*
	* @param  int $space_key  key of current space
	* @param  int $module_key  key of current module	
	* @param  int $group_key  key of current group
	* @param  boolean $is_admin  true if user is admin
	* @param  array $forum_strings  array of forum strings
	* 
	*/
	
	function InteractForum($space_key,$module_key,$group_key,$is_admin,$forum_strings) {
	
		$this->_space_key	 = $space_key;
		$this->_module_key	= $module_key;
		$this->_group_key	 = $group_key;
		$this->_is_admin	  = $is_admin;
		$this->_forum_strings = $forum_strings;				
		$this->_user_key	  = $_SESSION['current_user_key'];						
		
	} //end InteractForum method

	
	/**
	* Method of InteractForum Class to retrieve threaded display of posts
	*
	* @param  int $module_key  key of current module	
	* @param  int $parent_key  key of parent post
	* @param  string $space space to use to give required indenting
	* @param  array $forum_data  array of forum data
	* 
	*/

	function getThreads($module_key, $parent_key,$space,$forum_data)
	{
	
		global $CONN,$CONFIG, $t, $objDates;

   
		if (!is_object($objDates)) {

			if (!class_exists('InteractDate')) {

				require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
			}

			$objDates = new InteractDate();

		}

		if (!$forum_data['offset'] || $forum_data['offset']=='') {
   
			$forum_data['offset']='0';

		}

   
		if (!$forum_data['display'] || $forum_data['display']=='') {

	
			$forum_data['display'] = 15;   


		} else if ($forum_data['display']=='all') {

	
			$forum_data['display'] = $forum_data['total_threads'];

   
		}   
 
		if ($parent_key==0) {
		
			$sort_order = 'DESC';
		
		}
		
		$sql = "SELECT post_key, thread_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.user_key,parent_key, {$CONFIG['DB_PREFIX']}users.prefered_name FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE  {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND (module_key='$module_key' AND parent_key='$parent_key') ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added  $sort_order";


		if ($parent_key==0) {

			$rs = $CONN->SelectLimit($sql,$forum_data['display'],$forum_data['offset']);


		} else {

			$rs = $CONN->Execute($sql);

		}



	echo $CONN->ErrorMsg();
		
		$record_count=$rs->RecordCount();

		while (!$rs->EOF) {
	
		
		$current_row=$rs->CurrentRow();
			$pict='tf_out.gif';
			$pictnext='tf_down.gif';
		
		if(++$current_row==$record_count){
			
				$pict='tf_last.gif';
				$pictnext='pix.gif';
		
		}

			$post_key = $rs->fields[0];
			$thread_key = $rs->fields[1];
			$type_key = $rs->fields[2];
			$subject = $rs->fields[3];
			$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[4]),'short', true);
			$full_name = (!empty($rs->fields[9]))?$rs->fields[9]:$rs->fields[5].' '.$rs->fields[6];
			$user_key = $rs->fields[7];
			$parent_key = $rs->fields[8];

			if ($parent_key==0) {
				 if ($rs->CurrentRow()&1) {
				 	$class='tableOddRow';
				} else {
					$class='tableEvenRow';
				}
			} else {
	
				$class='';
		
			} 
		
		if ($this->checkAutoPrompting($post_key) === true && ($this->_is_admin==true || $this->_current_user_key==$user_key)) {
		
				$t->set_var("AUTO_PROMPTING","<a href=\"{$CONFIG['PATH']}/modules/forum/threadmanagement.php?space_key=$this->_space_key&module_key=$module_key&post_key=$post_key&parent_key=$parent_key&thread_key=$thread_key\" title=\"".$forum_strings['autoprompting_on']."\">*</a>"); 
			
		} else {
		
				$t->set_var('AUTO_PROMPTING',''); 
		
		}
	

			
		if (isset($forum_data['post_statuses']['read'][$post_key]) && $forum_data['post_statuses']['read'][$post_key]==1) {

			$t->set_var('POST_CLASS','small');
			$t->set_var('READ_TAG','span');
			
		} else {
		
			if ($user_key!=$this->_user_key) {
			
				$t->set_var('READ_TAG','strong');
				
			} else {
			
				$t->set_var('READ_TAG','span');
						
			}
		
		}				

 		if (isset($forum_data['post_statuses']['flags'][$post_key])) {
		
				switch ($forum_data['post_statuses']['flags'][$post_key]) {
					
				case 1:
				
					$t->set_var('STATUS_IMAGE','<img src="'.$CONFIG['PATH'].'/images/modules/forum/red_flag.gif">');
					
				break;
				
				case 2:
				
					$t->set_var('STATUS_IMAGE','<img src="'.$CONFIG['PATH'].'/images/modules/forum/white_flag.gif">');
					
				break;
				
				default:
				
					$t->set_var('STATUS_IMAGE','');
					
				break;				
				
			}	
		
		} else {
		
			$t->set_var('STATUS_IMAGE','');
			
		}
			$t->set_var('SPACE',$space);
			$t->set_var('IMAGE',$pict);
			$t->set_var('CLASS',$class);
			$t->set_var('SUBJECT',$subject);
			$t->set_var('SUBJECT_URL',$subject_url);
			$t->set_var('POST_KEY',$post_key);
			$t->set_var('PARENT_KEY',$parent_key);
			$t->set_var('THREAD_KEY',$thread_key);
			$t->set_var('FULL_NAME',$full_name);
			$t->set_var('FULL_NAME_URL',$full_name_url);
			$t->set_var('DATE_ADDED',$date_added);
			$t->set_var('TYPE',$this->_forum_strings['post_type_'.$type_key]);
			$t->set_var('USER_KEY',$user_key);


			//if user has selected to expand or collapse then update relevant tables
		
			if (!isset($_SESSION['collapse_posts'])) {

				$_SESSION['collapse_posts']=array();

			}

			if (!isset($_SESSION['expand_posts'])) {

				$_SESSION['expand_posts']=array();

			}

			if ($forum_data['expand_post']==$post_key) {

		 
				unset($_SESSION['collapse_posts'][$post_key]);
				$_SESSION['expand_posts'][$post_key] = $post_key;

		
			}

			if ($forum_data['collapse_post']==$post_key) {

				unset($_SESSION['expand_posts'][$post_key]);
				$_SESSION['collapse_posts'][$post_key] = $post_key;
			
			}

		
			//see if there are any replies to this post
		
			if ($this->checkPostReplies($post_key)===false) {

				$t->set_var('EXPAND_LINK','');
				$t->parse('LIST_POSTS', 'listposts', true);
		
			} else {


				//see if there are any current posts below, if so retrieve


			   if ($forum_data['expand_all']==1 ||  (($this->checkCurrentPosts($thread_key)===true && !in_array($post_key, $_SESSION['collapse_posts'])) || in_array($post_key, $_SESSION['expand_posts']) && $forum_data['expand_all']!=2)) {

					$t->set_var('EXPAND_LINK','<a href="'.$CONFIG['PATH'].'/modules/forum/forum.php?space_key='.$space_key.'&module_key='.$module_key.'&collapse_post='.$post_key.'&offset='.$forum_data['offset'].'#'.$post_key.'" class="NoUnderline">-</a>');

					$t->parse('LIST_POSTS', 'listposts', true);
					$this->getThreads($module_key, $post_key,$space.'<img src="../../images/'.$pictnext.'" width="20" height="20" align="top">',$forum_data);
			

				} else {

		  
					$t->set_var('EXPAND_LINK','<a href="'.$CONFIG['PATH'].'/modules/forum/forum.php?space_key='.$space_key.'&module_key='.$module_key.'&expand_post='.$post_key.'&offset='.$forum_data['offset'].'#'.$post_key.'" class="NoUnderline">+</a>');
					$t->parse('LIST_POSTS', 'listposts', true);

				}

			}


			$rs->MoveNext();
	
		}
	

		$rs->Close();

		return $forum_data;

	} //end getThreads method
	
	/**
	* Method to get the total number of threads in a given forum
	*
	* @param  int $module_key  key of module to get number of threads for
	* @return int $total_threads  total number of threads in given forum
	* 
	*/
	
	function getTotalThreads($module_key) {

	   global $CONN, $CONFIG;
	   
	   //get the total number of threads in this forum
  
	   $rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key' AND parent_key='0'");
   
	   $total_threads=$rs->RecordCount(); 

	   return $total_threads;

	} //end getTotalThreads method


	/**
	* Method to get array of posts that user has read
	*
	* @param  int $module_key  key of module to get read posts from
	* @param  int $user_key  key of user to get read posts for
	* @return array $readposts_array  array of read posts
	* 
	*/

	function getPostStatusArray($module_key='',$user_key)
	{
	
		global $CONN, $CONFIG;
		$n=0;
		$post_statuses = array();
		$read_statuses = array();
		$flag_statuses = array();
		$monitor_posts = array();		
	
		if ($module_key=='') {
		
			$sql = "SELECT post_key, read_status, flag_status, monitor_post FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE user_key='$user_key'";
	
		} else {
		
			$sql = "SELECT post_key, read_status, flag_status, monitor_post FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE module_key='$module_key' AND user_key='$user_key'";
	
		}

		$rs = $CONN->Execute($sql);
	echo $CONN->ErrorMsg();
		while (!$rs->EOF) {
		
			$read_statuses[$rs->fields[0]] = $rs->fields[1];
			$flag_statuses[$rs->fields[0]] = $rs->fields[2];
			$monitor_posts[$rs->fields[0]] = $rs->fields[3];
			$rs->MoveNext();
		
		}
	
		$post_statuses['read']  = $read_statuses;
		$post_statuses['flags'] = $flag_statuses;
		$post_statuses['monitor'] = $monitor_posts;
		return $post_statuses;

	} //end getReadPostsArray method

					
	/**
	* Method to of InteractForum class to see if autoprompting is activated in a post
	*
	* @param  int $post_key  key of post to check
	* @return true if autoprompting activated
	* 
	*/
	function checkAutoPrompting($post_key) {

		global $CONN, $CONFIG;
	
	$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}forum_thread_management WHERE post_key='$post_key'");
	echo $CONN->ErrorMsg();
	if ($rs->EOF) {
	
		return false;
		
	} else {
	
		return true;
	  	 
	}
	
	} //end check_auto_prompting

	/**
	* method of InteractForum class to see if a post has replies
	* 
	*
	* @param int $post_key key of post to check
	* @return true if there are replies
	*/

	function checkPostReplies($post_key) {

		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE parent_key='$post_key'");


		if ($rs->EOF) {

			return false;

		} else {

	
		return true;
	
		}

	} //end checkPostReplies method

	/**
	* Function called to see if there are any posts less than 5 days old
	* within a thread
	*
	* @param int $thread_key key of thread being checked
	* @return true if there are current posts in this thread
	*/

	function checkCurrentPosts($thread_key) {

		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE thread_key='$thread_key' AND date_added>=DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY)");


		if ($rs->EOF) {

			return false;

		} else {

	
		return true;

	
		}

	} //end checkCurrentPosts method
	
	/**
	* Method called to get threadkey for next thread in a forum
	*
	* @param int $thread_key key of thread being checked
	* @param string $action next or previous	
	* @return int $thread_key 
	*/

	function getNextThread($thread_key, $action) {

		global $CONN, $CONFIG;
	
		if ($action=='next') {
		
			$sql = "SELECT thread_key FROM {$CONFIG['DB_PREFIX']}posts WHERE thread_key>'$thread_key' AND module_key='$this->_module_key' ORDER BY thread_key";
		
		} else {
			
			$sql = "SELECT thread_key FROM {$CONFIG['DB_PREFIX']}posts WHERE thread_key<'$thread_key' AND module_key='$this->_module_key' ORDER BY thread_key DESC";
			
		}

		$rs = $CONN->SelectLimit($sql, 1);
		
		if ($rs->EOF) {

			return false;

		} else {

			while (!$rs->EOF) {
			
				$next_thread = $rs->fields[0];
				$rs->MoveNext();
			
			}
			 
			$rs->Close();
			
			return $next_thread;
	
		}

	} //end getNextThread() method
	
	/**
	* Method called see if a PostUserStatus entry alreay exists 
	*
	* @param int $post_key key of post being checked
	* @param int $user_key key of user being checked	
	* @return true if link exists 
	*/

	function checkPostStatus($post_key, $user_key) {

		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE user_key='$user_key' AND post_key='$post_key'");
		echo $CONN->ErrorMsg();
		if ($rs->EOF) {
		
			return false;
		
		} else {
		
			return true;
		
		}

	} //end checkPostStatus() method	
	
	/**
	* Method called to email notifications to people monitoring post 
	*
	* @param int $parent_key key of post being replied to 
	* @return true if link exists 
	*/

	function emailPostMonitors($post_data) {

		global $CONN, $CONFIG;

		$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}post_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}post_user_links.user_key AND post_key='{$post_data['parent_key']}' AND monitor_post='1' AND {$CONFIG['DB_PREFIX']}users.user_key!='{$_SESSION['current_user_key']}'";
	
		$member_keys = get_userkey_array($sql);
	
		require_once($CONFIG['BASE_PATH'].'/includes/email.inc.php');
		$post_email = $this->formatPostEmail($post_data, 'monitor');
		
		email_users($CONFIG['SERVER_NAME'].'-'.$post_data['post_subject'], $post_email['html'], $member_keys,'',$post_data['attachment'],'',$CONFIG['NO_REPLY_EMAIL'],$post_email['plain_text']);
		
		return true;
		
	} //end emailPostMonitors() method	
	
	/**
	* Method display a full thread of posts 
	*
	* @param int $parent_key key of current post
	* @param string $space spacer to show nesting 	
	* @param int $entry_key key of entry commented on if not a Forum based thread	
	* @param object $t template object	 
	* @return true  
	*/

	function getFullThread($parent_key, $space, &$t, $post_statuses, $entry_key='', $thread_key='')
{
	
	global $CONN, $CONFIG, $objDates, $objHtml;

	//$forum_settings = getForumData($module_key);

	if (isset($entry_key) && $entry_key!='') {
	
		$sql_limit = "entry_key='$entry_key'";
	
	} else {
	
		$sql_limit = "thread_key='$thread_key'";
	
	}
	
	$sql = "SELECT post_key, thread_key, parent_key, {$CONFIG['DB_PREFIX']}posts.type_key, subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, {$CONFIG['DB_PREFIX']}users.first_name, {$CONFIG['DB_PREFIX']}users.last_name, {$CONFIG['DB_PREFIX']}users.email,{$CONFIG['DB_PREFIX']}users.user_key,{$CONFIG['DB_PREFIX']}users.file_path,{$CONFIG['DB_PREFIX']}users.photo,{$CONFIG['DB_PREFIX']}posts.settings, {$CONFIG['DB_PREFIX']}posts.attachment FROM {$CONFIG['DB_PREFIX']}posts,{$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND ($sql_limit AND parent_key='$parent_key') ORDER BY {$CONFIG['DB_PREFIX']}posts.date_added ASC";

	$rs = $CONN->Execute($sql);

	if (!is_object($objDates)) {

		if (!class_exists('InteractDate')) {

			require_once($CONFIG['BASE_PATH'].'/includes/lib/date.inc.php');
	
		}

		$objDates = new InteractDate();

	}
	
	if (!class_exists('InteractHtml')) {
	
		require_once('../../includes/lib/html.inc.php');
	
	}
	
	if (!is_object($objHtml)) {
	
		$objHtml = new InteractHtml();
		
	}
	
	while (!$rs->EOF) {
	
		$t->set_var('PHOTO','');
		$t->set_var('PHOTO_WIDTH','');
		$t->set_var('MONITOR_CHECKED','');
		$post_key2 = $rs->fields[0];
		$thread_key = $rs->fields[1];
		$parent_key = $rs->fields[2];
		$type_key = $rs->fields[3];
		$subject = $rs->fields[4];
		$subject_url = urlencode($subject);
		
		$body = $objHtml->urlsToLinks($rs->fields[5]);
	
		if (!eregi('(<p|<br)', $body )) {
			
			$body = nl2br($body);
		
   		}
				
		$date_added = $objDates->formatDate($CONN->UnixTimeStamp($rs->fields[6]),'short');		
		$time_added = date('H:i', $CONN->UnixTimestamp($rs->fields[6]));
		$unix_date_added = $CONN->UnixTimestamp($rs->fields[6]);
		$date_now = mktime();
		$editable_date = $date_now-1800;				
		$full_name = $rs->fields[7].' '.$rs->fields[8];
		$email = $rs->fields[9];
		$user_key = $rs->fields[10];
		$file_path = $rs->fields[11];
		$photo = $rs->fields[12];
		$show_photo = $rs->fields[13];
		$attachment = $rs->fields[14];

		$attachment_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/forum/'.$forum_settings['file_path'].'/'.$attachment;
		 
		if (is_file($attachment_path)){
		
			$attachment_view_path = $CONFIG['MODULE_FILE_VIEW_PATH'].$space_key.'/forum/'.$forum_settings['file_path'].'/'.$attachment;			
			$t->set_var('VIEW_ATTACHMENT','<a href="'.$attachment_view_path.'">'.$forum_strings['view_attachment'].'</a>');
				
		} else {
		
			$t->set_var('VIEW_ATTACHMENT','');
		
		}
				
		
		if ($show_photo=='1') {
		
			$photo_path=$CONFIG['USERS_PATH'].'/'.$file_path.'/'.$photo;
			$relative_path=$CONFIG['USERS_VIEW_PATH'].'/'.$file_path.'/'.$photo;
			
			if (is_file($photo_path)) {
			
				$image_array = GetImageSize($photo_path); // Get image dimensions
				$image_width = $image_array[0]; // Image width
				$image_height = $image_array[1]; // Image height
				
				if ($image_width>80) {
				
					$factor=80/$image_width; 
					$image_height=round($image_height*$factor);
					$image_width = '80';
					
				}
				
				$image_tag = "<a href=\"{$CONFIG['PATH']}/users/userdetails.php?user_key=$user_key&space_key=$space_key\" target=\"_$user_key \"><img src=\"$relative_path\" height=\"$image_height\" width=\"$image_width\" border=\"0\"></a>";
				$t->set_var('PHOTO',$image_tag);
				$t->set_var('PHOTO_WIDTH',$image_width);
								
			}
			
		}
					
 		if ((isset($post_statuses['read'][$post_key2]) && $post_statuses['read'][$post_key2]==1) || $current_user_key==$user_key) {
		
			$t->set_var('READ_CHECKED','checked');
			$t->set_var('READ_TAG','span class="small"');
			
			if ($post_key2==$post_key) {
		
				$inner_cell_class='activeForumPostingReadInner';
				$outer_cell_class='activeForumPostingReadOuter';
	   
			} else {
	   
			   $inner_cell_class='ForumPostingReadInner';
			   $outer_cell_class='ForumPostingReadOuter';
	   
			}  	
			
		} else {
		
		
			if ($post_key2==$post_key) {
		
				$inner_cell_class='activeForumPostingInner';
				$outer_cell_class='activeForumPostingOuter';
					  
			} else {
	   
				$inner_cell_class='ForumPostingInner';
				$outer_cell_class='ForumPostingOuter';
	   
			}  
				
			$t->set_var('READ_TAG','strong');
		
		
		}			
			
		
		if (isset($post_statuses['flags'][$post_key2])) {
		
			switch ($post_statuses['flags'][$post_key2]) {
			
				case 1:
				
					$t->set_var('FLAG_CHECKED','checked');
					$t->set_var('STATUS_IMAGE','<img src="'.$CONFIG['PATH'].'/images/modules/forum/red_flag.gif">');					
					
				break;
				
				case 2:
				
					$t->set_var('FLAG_CHECKED','');
					$t->set_var('FINISHED_CHECKED','checked');
					$t->set_var('STATUS_IMAGE','<img src="'.$CONFIG['PATH'].'/images/modules/forum/white_flag.gif">');
								
				break;
				
				default:
				
					$t->set_var('STATUS_IMAGE','');
					$t->set_var('FLAG_CHECKED','');
					$t->set_var('STATUS_IMAGE','');
					$t->set_block('fullposts', 'FinishedBlock', 'FBlock');
					$t->set_var('FBlock','');
					
				break;
				
			}
			
			if (isset($post_statuses['monitor'][$post_key2])  && $post_statuses['monitor'][$post_key2]==1) {
			
				$t->set_var('MONITOR_CHECKED',checked);
			
			} else {
			
				$t->set_var('MONITOR_CHECKED','');
			
			}
	
		
		} else {
		
			$t->set_var('STATUS_IMAGE','');
			$t->set_var('FLAG_CHECKED','');
			$t->set_block('fullposts', 'FinishedBlock', 'FBlock');
			$t->set_var('FBlock','');
		
		}
		
		$t->set_var('SPACE',$space);
		$t->set_var('SUBJECT',$subject);
		$t->set_var('SUBJECT_URL',$subject_url); 
		$t->set_var('POST_KEY',$post_key2);
		$t->set_var('THREAD_KEY',$thread_key);
		$t->set_var('PARENT_KEY',$parent_key);
		$t->set_var('FULL_NAME',$full_name);
		$t->set_var('FULL_NAME_URL',$full_name_url);
		$t->set_var('USER_KEY',$user_key);
		$t->set_var('DATE_ADDED',$date_added);
		$t->set_var('TIME_ADDED',$time_added);
		$t->set_var('EMAIL',$email);
		$t->set_var('BODY',$body);
		$t->set_var('TYPE',$forum_strings['post_type_'.$type_key]);
		
		if ($parent_key=='0' && (!isset($entry_key) || $entry_key=='')) {
			
			$t->set_var('NEW_THREAD','<strong>'.$forum_strings['new_thread2'].': '.$general_strings['subject']." = $subject</strong>"); 
		
		} else {
			
			$t->set_var('NEW_THREAD','');	   
		
		}
		

		$t->set_var('OUTER_CELL_CLASS',$outer_cell_class);
		$t->set_var('INNER_CELL_CLASS',$inner_cell_class);

		//if user is an administrator show admin tool and full post details		
		if ($this->_is_admin==true || ($user_key==$this->_user_key && $unix_date_added>$editable_date) || ($user_key==$current_user_key && $forum_settings['edit_level']==2)) {

		   
			if (!isset($entry_key) || $entry_key=='') {
			
				$post_details = $forum_strings['post_no'].' '.$post_key2.', '.$forum_strings['thread_no'].' '.$thread_key.', '.$forum_strings['parent_no'].' '.$parent_key;
				$t->set_var('POST_DETAILS',$post_details);
				
			} else {

				$t->set_var('POST_DETAILS','');
			
			}	
			
		} else {
		 
			$t->set_block('fullposts', 'EditPostBlock', 'EDPBlock');
			$t->set_var('EDPBlock','');   			
			if (!isset($entry_key) || $entry_key=='') {
		
				$post_details = $forum_strings['post_no'].' '.$post_key2;
				$t->set_var('POST_DETAILS',$post_details);			
		
			} else {
				
				$t->set_var('POST_DETAILS','');
			
			}	
		
		}
		//if user wants all displayed posts flagged as read do that now
		
		if ($_SESSION['read_posts_flag']==1 && !isset($post_statuses[$post_key2])) {
		
			$t->set_var('READ_CHECKED','checked');
			
			if ($this->checkPostStatus($post_key, $current_user_key)===false) {
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}post_user_links(module_key, post_key, user_key, read_status) VALUES ('$module_key', '$post_key2', '$current_user_key','1')");
				
			} else {
			
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}post_user_links SET read_status='1' WHERE user_key='$current_user_key' AND post_key='$post_key2'");		
			
			}
			
		}
		
		$t->parse('FULL_POSTS', 'fullposts', true);

		$this->getFullthread($post_key2,$space."<td rowspan=\"3\" width=\"20\"><img src=\"{CONFIG['PATH']}/images/tf_last.gif\" width=\"20\" height=\"20\" vspace=\"0\" hspace=\"0\" align=\"top\"></td>",$t, $post_statuses, $entry_key, $thread_key);
		
		$rs->MoveNext();
		
	}
	
	$rs->Close();
	return true;

	} //end getFullThread

	/**
	* Method to set common thread display strings 
	*
	* @param obj $t template object
	* @return true  
	*/

	function setThreadDisplayStrings(&$t, $entry_key='')
	{
		
		global $general_strings;
		
		$t->set_var('ACTION_SELECTED_STRING',$this->_forum_strings['action_selected']);
		$t->set_var('FLAG_AS_READ_STRING',$this->_forum_strings['flag_as_read']);
		$t->set_var('FLAG_AS_NOT_READ_STRING',$this->_forum_strings['flag_as_not_read']);
		$t->set_var('PRINT_SAVE_STRING',$this->_forum_strings['print_save']);
		$t->set_var('POSTED_BY_STRING',$this->_forum_strings['posted_by']);
		$t->set_var('BACK_TO_STRING',$general_strings['back_to']);
		$t->set_var('HOME_STRING',$general_strings['home']);
		$t->set_var('SUBJECT_STRING',$general_strings['subject']);
		$t->set_var('ON_STRING',$general_strings['on']);
		$t->set_var('AT_STRING',$general_strings['at']);
		$t->set_var('SELECT_STRING',$general_strings['select']);
		$t->set_var('REPLY_STRING',$general_strings['reply']);
		$t->set_var('REPLY_QUOTED_STRING',$general_strings['reply_quoted']);
		$t->set_var('MODULE_NAME',$page_details['module_name']);
		$t->set_var('EDIT_POST_STRING',$this->_forum_strings['edit_post']);		
		
		if ($entry_key=='') {
		
			$t->set_var('PREVIOUS_STRING',$general_strings['previous']);
			$t->set_var('NEXT_STRING',$general_strings['next']);
			
		}
			
		$t->set_var('SELECT_ALL_STRING',$general_strings['select_all']);
		$t->set_var('CLEAR_ALL_STRING',$general_strings['clear_all']);
		//$t->set_var('REFERER',$_SERVER['HTTP_REFERER']);
		$t->set_var('SUBMIT_CHANGES_STRING',$this->_forum_strings['submit_changes']);
		$t->set_var('READ_STRING',$this->_forum_strings['read']);
		$t->set_var('NOT_READ_STRING',$this->_forum_strings['not_read']);
		$t->set_var('FOLLOW_UP_STRING',$this->_forum_strings['follow_up']);
		$t->set_var('FINISHED_STRING',$this->_forum_strings['finished']);
		$t->set_var('STATUS_STRING',$general_strings['status']);
		$t->set_var('MONITOR_POST_STRING',$this->_forum_strings['monitor_post']);	
	
	} //end setThreadDisplayStrings

	/**
	* Method to get data for a given post 
	*
	* @param int $post_key key of post to get data for
	* @return array $posr_data array of post data  
	*/

	function getPostData($post_key)
	{
		
		global $CONN, $CONFIG, $objHtml;
		
		
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}posts.subject, body, {$CONFIG['DB_PREFIX']}posts.date_added, first_name, last_name FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}posts.added_by_key={$CONFIG['DB_PREFIX']}users.user_key AND post_key='$post_key'");
		
		$post_data = array();
		
		if (!is_object($objHtml)) {
		
			if (!function_exists('InteractHtml')) {
			
				require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');
				
			}
			
			$objHtml = new InteractHtml();
		
		}
		while (!$rs->EOF) {
		
			$post_data['subject'] = $rs->fields[0];
			$post_data['body'] = $objHtml->parseText($rs->fields[1]);
			$post_data['date_added_unix'] = $CONN->UnixTimestamp($rs->fields[2]);
			$post_data['added_by'] = $rs->fields[3].' '.$rs->fields[4];
			$rs->MoveNext();
		
		}
		
		$rs->Close();
		
		return $post_data;
	
	} //end getPostData

		/**
	* Method to format a forum post to be emailed out 
	*
	* @param int $post_data array of post subject, body, user_full_name, user_key
	* @param string $type subscription, monitor, email_all
	* @return array $email_post aray of formats for emailing - plain_text and html  
	*/

	function formatPostEmail($post_data,$type='subscription')
	{
		
		global $CONFIG, $forum_strings, $general_strings;
		
		if (!isset($this->_objHtml) || !is_object($this->_objHtml)) {
			
			if (!class_exists('InteractHtml')) {
				require_once($CONFIG['BASE_PATH'].'/includes/lib/html.inc.php');		
			}
			$this->_objHtml  = new InteractHtml();
		}
		if (!isset($this->_objUser) || !is_object($this->_objUser)) {
			
			if (!class_exists('InteractUser')) {
				require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');		
			}
			$this->_objUser  = new InteractUser();
		}
		if (empty($this->_email_html_header)) {
			
			$this->_email_html_header = $this->_objHtml->getEmailHeader();
		}
		
		$post_email = array();
		$post_address	=$CONFIG['FULL_URL'].$CONFIG['DIRECT_PATH']."post/".$post_data['space_key'].'/'.$post_data['post_key'];
				
		$heading = '<table border="0" cellpadding="5" cellspacing="0" width="100%" style="border:1px solid #CCCCCC"><tr><td bgcolor="#EBEBEB" colspan="2"><a href="'.$CONFIG['FULL_URL'].'">'.$CONFIG['SERVER_NAME'].'</a> &raquo; <a href="'.$CONFIG['FULL_URL'].'/spaces/space.php?space_key='.$post_data['space_key'].'">'.$post_data['space_name'].'</a> &raquo; <a href="'.$CONFIG['FULL_URL'].'/modules/forum/forum.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'">'.$post_data['module_name'].'</a></td></tr>';
		
		$email_options = '<tr bgcolor="#EBEBEB"><td align="left" ><a href="'.$CONFIG['FULL_URL'].'/modules/forum/thread.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&thread_key='.$post_data['thread_key'].'post_key='.$post_data['post_key'].'#'.$post_data['post_key'].'">'.$forum_strings['view_in_context'].'</a> - <a href="'.$CONFIG['FULL_URL'].'/modules/forum/postinput.php?action=reply&space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'&thread_key='.$post_data['thread_key'].'&parent_key='.$post_data['post_key'].'#reply">'.$general_strings['reply'].'</a> </td><td align="right" >';
		
		switch($type) {
			case 'subscription':
				$email_options .= '<a href="'.$CONFIG['FULL_URL'].'/modules/general/subscribe.php?space_key=1&module_key=11&action=unsubscribe&referer=modules/forum/forum.php?space_key='.$post_data['space_key'].'&module_key='.$post_data['module_key'].'">'.$general_strings['unsubscribe'].'</a></td></tr>';
			break;
			case 'monitor':
				
			break;
			
			case 'email_all':
				$email_options .= '</td></tr>';
			break;
		}
		
		$user_image = $this->_objUser->getUserPhotoTag($post_data['added_by_key'],40,$post_data['space_key']);
		
		$post_body = '<tr><td colspan="2" ><table border="0" cellpadding="0"><tr><td width="40">'.$user_image.'</td><td><strong>'.$post_data['post_subject'].'</strong><br />';
		$post_body .= '<div class="small">'.$forum_strings['posted_by'].' <a href="'.$CONFIG['FULL_URL'].'/users/userdetails.php?user_key='.$post_data['added_by_key'].'&space_key='.$post_data['space_key'].'">'.$post_data['first_name'].' '.$post_data['last_name'].'</a> - '.date('l,j F, g:iA',$post_data['date_added']).'</td></tr></table></td></tr>';
		$post_body .='<tr ><td colspan="2" style="border-top:1px solid #CCCCCC">'.strip_tags($post_data['post_body'],'<strong>,<div>,<br><p><font>,<blockquote>,<b>,<span>,<em>,<i>').'</td></tr>'.$email_options.'</table>';
		
			
		$post_email['html'] = $this->_email_html_header.$heading.$post_body;
			
		//now do the plain text version
		$post_email['plain_text'] = $CONFIG['SERVER_NAME'].' &raquo; '.$post_data['space_name'].' &raquo; '.$post_data['module_name']."\n\n";
		$post_email['plain_text'] .= $forum_strings['posted_by'].': '.$post_data['first_name'].' '.$post_data['last_name'].' - '.date('l,j F, gA',$post_data['date_added'])."\n\n";
		$post_email['plain_text'] .= strip_tags($post_data['post_body'])."\n\n";
		$post_email['plain_text'] .= $forum_strings['view_and_post']."\n".$post_address;
		

		return $post_email;
	
	} //end formatPostEmail

	/**
	* Method to email a post to all members 
	*
	* @param int $post_data array of post subject, body, user_full_name, user_key
	* @return true   
	*/
	function emailAllMembers($post_data) {

		global $CONFIG;
	
		if (!empty($post_data['group_key'])) {
			$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}group_user_links.user_key AND (GroupKey='{$post_data['group_key']}' AND {$CONFIG['DB_PREFIX']}users.user_key!='{$post_data['added_by_key']}')";
		} else {
			$sql = "SELECT {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}space_user_links.user_key AND (space_key='{$post_data['space_key']}' AND {$CONFIG['DB_PREFIX']}users.user_key!='{$post_data['added_by_key']}')";
		}

		$member_keys = get_userkey_array($sql);
		
		require_once($CONFIG['BASE_PATH'].'/includes/email.inc.php');
		$post_email = $this->formatPostEmail($post_data, 'email_all');
		
		email_users($CONFIG['SERVER_NAME'].'-'.$post_data['post_subject'], $post_email['html'], $member_keys,'',$post_data['attachment'],'',$CONFIG['NO_REPLY_EMAIL'],$post_email['plain_text']);
		
		return true;

	} //end emailAllMembers	
	
	/**
	* Method count number of posts and users, etc. in a given forum 
	*
	* @param int $module_key module key of forum to return stats for
	* @return true   
	*/
	function getForumStats($module_key='') {

		global  $CONN, $CONFIG;
		
		
		if (empty($module_key)){$module_key = $this->_module_key;}
		$objDate = singleton::getInstance('date');
		$total_posts = $CONN->SelectLimit("SELECT date_added FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key' ORDER BY date_added DESC");
		$total_users = $CONN->Execute("SELECT DISTINCT added_by_key FROM {$CONFIG['DB_PREFIX']}posts WHERE module_key='$module_key' ORDER BY date_added DESC");
		$last_post = ($CONN->UnixTimestamp($total_posts->fields[0])>0)?$objDate->formatDate($CONN->UnixTimestamp($total_posts->fields[0]),'short'):'';
		$forum_stats = array('total_posts' => $total_posts->RecordCount(), 'last_post' => $last_post, 'total_users' => $total_users->RecordCount());
		$total_users->Close();
		$total_posts->Close();
		return $forum_stats;

	} //end getForumStats


} //end InteractForum class	
?>
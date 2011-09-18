<?php
/**
* User functions
*
* Contains any common functions related to users
*
* @package Common
*/

/**
* A class that contains common methods related to users 
* 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for accessing user details, etc. 
* 
* @package Common
*/
class InteractUser {

	/**
	* Create an array of user data for given user_key  
	* 
	* @param int $user_key user_key of user to retrieve data for
	* @return $user_data array of user data, name, email, etc.
	*/

	function getUserData($user_key)
	{
	
		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT username,email,first_name,last_name,level_key,date_added,photo,file_path,last_use,use_count,account_status, user_id_number, prefered_name, password, language_key, auto_load_editor, read_posts_flag, details, skin_key, address, phone_no, branch, attendance, backlogs FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$user_key'");

		if ($rs->EOF) {
			return false;
		} else {
			while (!$rs->EOF) {
				if ($CONFIG['AUTH_TYPE']=='dbplain') {
					$password = $rs->fields[13];
				} else {
					$password1 = '';
				}
				$user_data = array( 
					'username'	  => $rs->fields[0],
					'email'		  => $rs->fields[1],
					'first_name'	 => $rs->fields[2],
					'last_name'	  => $rs->fields[3],
					'level_key'	  => $rs->fields[4],
					'date_added'	 => $rs->fields[5],
					'photo'		  => $rs->fields[6],
					'file_path'	  => $rs->fields[7],
					'last_use'	   => $rs->fields[8],
					'use_count'	  => $rs->fields[9],
					'account_status' => $rs->fields[10],
					'user_id_number' => $rs->fields[11],	
					'prefered_name' => $rs->fields[12],
					'password' => $password,		
					'language_key' => $rs->fields[14],
					'auto_editor' => $rs->fields[15],	
					'flag_posts' => $rs->fields[16],	
					'details' => $rs->fields[17],
					'skin_key' => $rs->fields[18],
					'address' => $rs->fields[19],
					'phone_no' => $rs->fields[20],
					'branch' => $rs->fields[21]	,
					'attendance' => $rs->fields[22],
					'backlogs' => $rs->fields[23]																
				);
				$rs->MoveNext();
			}
			$rs->Close();
			
			//now get array of user_groups that user is a member of
			$n=1;
			$rs = $CONN->Execute("SELECT user_group_key FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_key='$user_key'");
			$user_group_keys = array();
			while (!$rs->EOF) {
				$user_group_keys[$n]=$rs->fields[0];
				$n++;
				$rs->MoveNext();
			}
			$rs->Close();
			
			$user_data['user_group_keys'] = $user_group_keys;
			$user_data['photo_tag'] = $this->getUserphotoTag($user_key, '60');
			return $user_data;
		}
 
	} //end getUserData
	
	/**
	* Create an sql in () string and an array of group_keys user is a member of 
	* 
	* @param int $user_key user_key of user to retrieve data for
	* @param int $space_key space_key space_key to retrive groups for. If not set retrieve all
	* @return array $groups_data string and array of groupkeys user is a member of
	*/

	function getGroupsData($user_key, $space_key='')

	{
	
		global $CONN, $CONFIG;
	
		$groups_array=array();

		if (isset($space_key) && $space_key!=''  && $space_key!=$CONFIG['DEFAULT_SPACE_KEY']) {
			$sql = "SELECT {$CONFIG['DB_PREFIX']}group_user_links.group_key FROM {$CONFIG['DB_PREFIX']}group_user_links, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}group_user_links.group_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.space_key='$space_key' AND user_key='$user_key'";
		} else {
			$sql = "SELECT group_key FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE user_key='$user_key'";
		}
		$groups_sql='(';
		$n=1;
		$rs = $CONN->Execute($sql);
echo $CONN->ErrorMsg();
		if ($rs->EOF) {
			$groups_sql.='-1)';
		} else {
			$record_count=$rs->RecordCount();
			while (!$rs->EOF) {
				$current_row=$rs->CurrentRow();
				$group_key = $rs->fields[0];
				$groups_array[$n]=$group_key;
				if(++$current_row==$record_count) {
					$groups_sql.="$group_key ";
				} else {
					$groups_sql.="$group_key, ";
				}
				$n++;
				$rs->MoveNext();
		   }
		   $groups_sql.=')';
		   $rs->Close();
		}
		$groups_data['groups_sql']	= $groups_sql;
		$groups_data['groups_array']  = $groups_array;		
		return $groups_data;
				
	} //end getGroupsData	

	/**
	* Create an sql in () string and an array of space_keys user is a member of 
	* 
	* @param int $user_key user_key of user to retrieve data for
	* @return array $spaces_data string and array of spacekeys user is a member of
	*/

	function getSpacesData($user_key)
	{
	
		global $CONN, $CONFIG;
	
		$spaces_array=array();

		$spaces_sql='(';
		$n=1;
		$sql = "select space_key from {$CONFIG['DB_PREFIX']}space_user_links where user_key='$user_key'";
		$rs = $CONN->Execute($sql);

		if ($rs->EOF) {
			$spaces_sql.='-1)';
		} else {
			$record_count=$rs->RecordCount();
			while (!$rs->EOF) {
				$current_row=$rs->CurrentRow();
				$space_key = $rs->fields[0];
				$spaces_array[$n]=$space_key;
				if(++$current_row==$record_count) {
					$spaces_sql.="$space_key ";
				} else {
					$spaces_sql.="$space_key, ";
				}
				$n++;
				$rs->MoveNext();
		   }
		   $spaces_sql.=')';
		   $rs->Close();
		}
		$spaces_data['spaces_sql'] = $spaces_sql;
		$spaces_data['spaces_array'] = $spaces_array;		
		return $spaces_data;

	} //end getSpacesData	
	
	/**
	* Delete a user from the system 
	* 
	* @param int $user_key user_key of user to delete
	* @return true
	*/

	function deleteUser($user_key)
	{
	
		global $CONN, $CONFIG;
		
		//get deleteuser userkey
		$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE username='_interact_deleted'");
		if ($rs->EOF) {
			$message =  "There is no _interact_deleted user user set up!";
			return $message;
		} else {
			while (!$rs->EOF) {
				$deleted_user = $rs->fields[0];
				$rs->MoveNext();
			}
		}
		$rs->Close();
		$user_data = $this->getUserData($user_key);
		if ($user_data==false) {
			$message = 'There is no user with user_key='.$user_key;
			return $message;
		}
		//set any news added by this user as added by deleted user
		
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}news SET user_key='$deleted_user' WHERE user_key='$user_key'");

		//delete any UserNotes added for this user
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}tagged_urls WHERE added_for_key='$user_key'");
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}tagged_urls SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
		
		//update any Modules/ModuleSpacelinks added by this user
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}modules SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}modules SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}modules SET owner_key='$deleted_user' WHERE owner_key='$user_key'");
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET added_by_key='$deleted_user' WHERE added_by_key='$user_key'");
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET modified_by_key='$deleted_user' WHERE modified_by_key='$user_key'");
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET owner_key='$deleted_user' WHERE owner_key='$user_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}module_edit_right_links WHERE user_key='$user_key'");		
				
		//delete statistics for user
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}statistics WHERE user_key='$user_key'");
		
		//delete any usergroup links
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_key='$user_key'");
		
		//delete any space links
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}space_user_links WHERE user_key='$user_key'");
		
		//delete any group links
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}group_user_links WHERE user_key='$user_key'");	
		//delete any skins the user added
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}skins WHERE user_key='$user_key' AND scope_key='0'");						
				
		//now run user_delete functions for each module
		$rs = $CONN->Execute("SELECT code FROM {$CONFIG['DB_PREFIX']}module_types");

		while (!$rs->EOF) {
			$code		= $rs->fields[0];
			$module_file = $CONFIG['BASE_PATH'].'/modules/'.$code.'/'.$code.'.inc.php';
			if (file_exists($module_file)) {
				include_once($module_file);
				$user_delete_function = 'user_delete_'.$code;
				if (function_exists($user_delete_function)) {
					if (!$user_delete_function($user_key, $deleted_user)) {
						echo "Error: could not run user delete functions for  $code <br \>\n";
					}
				}
			}
			$rs->MoveNext();
		}
		
		//delete the users directory
		if ($CONFIG['USERS_PATH'] && $CONFIG['USERS_PATH']!='' && $user_data['file_path'] && $user_data['file_path']!='') {
			$directory_path = $CONFIG['USERS_PATH'].'/'.$user_data['file_path'];
			if (Is_Dir($directory_path)) {
				delete_directory($directory_path);		
			}
		}			

		//now delete the user account
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$user_key'");
		return true;
		
	} //end deleteUser
	
	/**
	* Generate an html img tag for a given users photo 
	* 
	* @param int $user_key user_key of user to retrieve photo for
	* @param int $width maximum width of photo	
	* @return string $user_photo string of users img tag
	*/

	function getUserphotoTag($user_key, $width='40', $space_key='', $align='top')
	{
	
		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT photo, file_path FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$user_key'");

		if ($rs->EOF) {
			return false;
		} else {
			while (!$rs->EOF) {
				$photo	 = $rs->fields[0];
				$file_path = $rs->fields[1];
				$rs->MoveNext();
		   }
		}
		$photo_path=$CONFIG['USERS_PATH'].'/'.$file_path.'/'.$photo;
		$relative_path=$CONFIG['USERS_VIEW_PATH'].'/'.$file_path.'/'.$photo;
		
		if (is_file($photo_path)) {
			$image_array = GetImageSize($photo_path); // Get image dimensions
			$image_width = $image_array[0]; // Image width
			$image_height = $image_array[1]; // Image height
			if ($image_width>$width) {
				$factor=$width/$image_width; 
				$image_height=round($image_height*$factor);
				$image_width = $width;
			}
			$user_photo = "<a href=\"{$CONFIG['FULL_URL']}/users/userdetails.php?user_key=$user_key&space_key=$space_key\" ><img src=\"{$CONFIG['SERVER_URL']}$relative_path\" height=\"$image_height\" width=\"$image_width\" border=\"0\" class=\"userphoto\" align=\"$align\"></a>";
		} else {
			$photo_path=$CONFIG['BASE_PATH'].'/images/defaultuser.gif';
			$relative_path=$CONFIG['FULL_URL'].'/images/defaultuser.gif';
			if (is_file($photo_path)) {
				$image_array = GetImageSize($photo_path); // Get image dimensions
				$image_width = $image_array[0]; // Image width
				$image_height = $image_array[1]; // Image height
				if ($image_width>$width) {
					$factor=$width/$image_width; 
					$image_height=round($image_height*$factor);
					$image_width = $width;
				}
				$user_photo = "<a href=\"{$CONFIG['FULL_URL']}/users/userdetails.php?user_key=$user_key&space_key=$space_key\" ><img src=\"$relative_path\" height=\"$image_height\" width=\"$image_width\" border=\"0\" class=\"userphoto\" align=\"$align\"></a>";
			}
		}
		return $user_photo;
				
	} //end getUserphotoTag		
	
	/**
	* Add a new user account 
	* 
	* @param int $user_data array of user data
	* @param int $width maximum width of photo	
	* @return true return true if add successful
	*/

	function addUser($user_data, $email_user=false, $return_data=false)
	{
		global $CONN, $CONFIG;

		
		if (isset($user_data['auto_password']) && $user_data['auto_password']==1) {
			$user_data['password'] = $this->generatepassword(6);
		} 
		if (isset($user_data['auto_username']) && $user_data['auto_username']==1) {
			$user_data['username'] = $this->generateusername($user_data['first_name'],$user_data['last_name']);
			$username =$user_data['username'];
		} else {
			$username	= $user_data['username'];
		}
		if ($CONFIG['AUTH_TYPE']=='dbplain') {
			$password = $user_data['password'];
		} else {
			$password = md5($user_data['password']);
		}
		$first_name	= $user_data['first_name'];
		$last_name	= $user_data['last_name'];
		$prefered_name	= $user_data['prefered_name'];
		$email		= $user_data['email'];
		$user_id_number		= $user_data['user_id_number'];
		$details	= $user_data['details'];
		$skin_key	= isset($user_data['skin_key'])?$user_data['skin_key']:0;
		$level_key	= isset($user_data['level_key'])?$user_data['level_key']:3; 
		$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));
 		$address = $user_data['address'];
 		$phone_no = $user_data['phone_no'];
 		$branch = $user_data['branch'];
 		$attendance = $user_data['attendance'];
 		$backlogs = $user_data['backlogs'];
		
		if (!isset($user_data['language_key'])) {
			$language_key=$CONFIG['DEFAULT_LANGUAGE'];
			$user_data['language_key'] = $language_key;
		} else {
			$language_key=$user_data['language_key'];
		}
	
		$sql =  "INSERT INTO {$CONFIG['DB_PREFIX']}users(username, password, user_id_number, first_name, last_name, prefered_name, email, details, level_key,date_added, language_key, account_status, read_posts_flag, skin_key, address, phone_no, branch, attendance, backlogs)  VALUES ('$username','$password','$user_id_number','$first_name','$last_name','$prefered_name', '$email','$details','$level_key',$date_added,'$language_key', '1','$flag_posts', '$skin_key', '$address', '$phone_no', '$branch', '$attendance' ,'$backlogs')";
	
		if ($CONN->Execute($sql) === false) {
			$message =  'There was an error adding your account: '.$CONN->ErrorMsg().' <br />';
			return $message;
		} else {
		
			$user_key = $CONN->Insert_ID();
			//add to selected user groups
			$this->addToUserGroups($user_key, $user_data['user_group_keys'], $date_added);
			
	   		// now create user diretories
			$users_file_path = $this->createUserDirectory($user_key);
			
			if ($users_file_path==false) {
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$user_key'");
				$message = 'There was an error adding your account - user directory could not be created';
				return $message;
			} else {
				if (isset($_FILES['photo']) && $_FILES['photo']['name']!='') {
					$photo_name = $this->uploadUserphoto($_FILES['photo'],$user_key,$users_file_path);
					$photo_sql = ", photo='$photo_name'";
				} else {
					$photo_sql = '';
				}
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}users SET file_path='$users_file_path' $photo_sql WHERE user_key='$user_key'");
				if ($CONFIG['USER_SPACES']==1) {
					$this->addMySpace($user_key, $user_data);
				}
				
				if ($email_user==true) {
					$this->emailUserdetails($user_data);
				}
				
				if ($return_data==true) {
					$user_data['user_key'] = $user_key;
					$user_data['file_path'] = $users_file_path;
					return $user_data;
				} else {
					return true;
				}
			}
			//now see if personal space needed

		}

	} //end addUser	
	
	/**
	* Add a new user account 
	* 
	* @param int $user_data array of user data
	* @param int $width maximum width of photo	
	* @return true return true if add successful
	*/

	function modifyUser($user_data, $current_user_key)
	{
	
		global $CONN,$CONFIG;			 
	
		$username		= $user_data['username'];
		if (isset($user_data['password']) && $user_data['password']!='') {
			if ($CONFIG['AUTH_TYPE']=='dbplain') {
				$password = $user_data['password'];
			} else {
				$password = md5($user_data['password']);
			}
			$password_sql = ", password='$password'";			
		} else {
			$password_sql = '';		
		}
 		$first_name		= $user_data['first_name'];
		$last_name		= $user_data['last_name'];
		$prefered_name	= $user_data['prefered_name'];
		$email			= $user_data['email'];
		$user_id_number	= $user_data['user_id_number'];
		$details		= $user_data['details']; 
		$date_modified		= $CONN->DBDate(date('Y-m-d H:i:s'));
		$language_key 		= $user_data['language_key'];
		$auto_editor 		= $user_data['auto_editor'];
		$flag_posts 		= $user_data['flag_posts'];
		$skin_key			= isset($user_data['skin_key'])?$user_data['skin_key']:0; 
 		$address 		= $user_data['address'];
 		$phone_no 		= $user_data['phone_no'];
 		$branch 		= $user_data['branch'];
		
		if (!isset($user_data['language_key'])) {
			$language_key=$CONFIG['DEFAULT_LANGUAGE'];
		} else {
			$language_key=$user_data['language_key'];
		}
	
		if (isset($_FILES['photo']) && $_FILES['photo']['name']!='') {
			$sql = "select file_path from {$CONFIG['DB_PREFIX']}users where user_key='$current_user_key'";
			$rs = $CONN->Execute($sql);
			while (!$rs->EOF) {
				$file_path = $rs->fields[0];
				$rs->MoveNext();
			}
			$rs->Close();
			$photo_name = $this->uploadUserphoto($_FILES['photo'],$current_user_key,$file_path);
			$photo_sql  = ", photo='$photo_name'";
		} else {
			$photo_sql = '';
		}
		$sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET user_id_number='$user_id_number',first_name='$first_name',last_name='$last_name', prefered_name='$prefered_name', email='$email',details='$details', address='$address', phone_no='$phone_no', branch='$branch', language_key='$language_key', auto_load_editor='$auto_editor', read_posts_flag='$flag_posts', skin_key='$skin_key' $photo_sql $password_sql WHERE user_key='$current_user_key'";
	
		if ($CONN->Execute($sql) === false) {
			$message =  'There was an error modifying your details: '.$CONN->ErrorMsg().' <br />';
			return $message;
		} else {
			//first remove from existing user groups
			if (is_array($user_data['user_group_keys']) && count($user_data['user_group_keys']>0)){
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_key='$current_user_key'");
				$this->addToUserGroups($current_user_key, $user_data['user_group_keys'], $date_modified);
			}
	   		//set the language variable again
//	   		$rs = $CONN->Execute("SELECT code FROM {$CONFIG['DB_PREFIX']}Languages WHERE language_key='$language_key'");
	   
	   		$_SESSION['language'] = $language_key;

//	   		$rs->Close();
	   			   	 	   	   
	   		return true;  
		}

	} //end modifyUser

	/**
	* Add a user to selected usergroups 
	* 
	* @param int $user_key key of user to add
	* @param array $user_group_keys array of user group keys	
	* @return true return true if add successful
	*/

	function addToUserGroups($user_key, $user_group_keys, $date_added)
	{

		global $CONN, $CONFIG;
		
		foreach ($user_group_keys as $value) {
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}user_usergroup_links(user_key, user_group_key) VALUES('$user_key','$value')");
			$rs = $CONN->Execute("SELECT space_key from {$CONFIG['DB_PREFIX']}default_space_user_links where user_group_key='$value'");
			echo $CONN->ErrorMsg();
			while (!$rs->EOF) {
				$space_key=$rs->fields[0];
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}space_user_links(space_key, user_key, access_level_key, date_added) VALUES ('$space_key','$user_key','2', $date_added)");
				$rs->MoveNext();
			}
			$rs->Close();
	   	}
		return true;

	} //end addToUserGroups
	
	/**
	* Create a users home directory 
	* 
	* @param int $user_key key of user to add directory for
	* @return string $users_file_path path to user subdirectory
	*/

	function createUserDirectory($user_key)
	{

		global $CONFIG;
		
		// get random subdirectory number
		mt_srand ((float) microtime() * 1000000);
		$subdirectory = mt_rand(1,50);
		$users_file_path = $subdirectory.'/'.$user_key;
		$subdirectory_path=$CONFIG['USERS_PATH'].'/'.$subdirectory;
		if (!is_dir($subdirectory_path)) {
			mkdir($subdirectory_path,0777);
		}
		$full_path=$CONFIG['USERS_PATH'].'/'.$users_file_path;
		mkdir($full_path,0777);
		
		if (!is_dir($full_path)) {
			return false;
		} else {
			return $users_file_path;
		}
		
	} //end createUserDirectory	
	
	
	/**
	* Upload a user's photo 
	* 
	* @param array $photo array of photo file details
	* @param int $user_key key of user to add photo for
	* @return string $users_file_path path to user subdirectory
	*/
	function uploadUserphoto($photo,$user_key,$users_file_path)
	{
	
		global $CONN, $CONFIG;
		 
		$full_path=$CONFIG['USERS_PATH'].'/'.$users_file_path;		
		$my_max_file_size = '102400'; 
		$image_max_width = '200';
		$image_max_height = '160';
		$size = getimagesize($photo['tmp_name']);
	
		list($foo,$width,$bar,$height) = explode("\"",$size[3]);
		if ($width > $image_max_width) {
			$newwidth=200;
			$factor=$image_max_width/$width;
			$newheight=$height*$factor;
			exec("mogrify -geometry \"$newwidth x $newheight\" \"{$photo['tmp_name']}\"");
		}
	
		if(ereg('gif',$photo['type'])){
			$extension = '.gif';
		} else {
			$extension = '.jpg';
		}
	
		$photo_name = $user_key.$extension;	
		if ($action='modify2') {
			$current_photo_gif=$full_path.'/'.$user_key.'gif';
			$current_photo_jpg=$full_path.'/'.$current_photo.'jpg';
			if (file_exists($current_photo_gif)) {
				unlink($current_photo_gif);
			}
			
			if (file_exists($current_photo_jpg)) {
				unlink($current_photo_jpg);
			}		
		}
		copy($photo['tmp_name'],$full_path.'/'.$photo_name);
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}users SET photo='$photo_name' WHERE user_key='$user_key'");
		return $photo_name;  

	} //end uploadUserphoto
	
	
	/**
	* email account details to user 
	* 
	* @param array $user_data array of user data
	* @return true
	*/
	function emailUserdetails($user_data) {

		global $CONFIG, $general_strings;

		//now email the user their details
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
		$mail_object =& Mail::factory($CONFIG['EMAIL_TYPE'], $params);
		$subject = $CONFIG['SERVER_NAME'].' '.$general_strings['login_details'];
		$message = sprintf($general_strings['login_details_email'], $user_data['first_name'], $user_data['last_name'], $CONFIG['SERVER_NAME'], $CONFIG['SERVER_URL'].$CONFIG['PATH'], $user_data['username'], $user_data['password']);
	
		$headers['From']	= $CONFIG['NO_REPLY_EMAIL'];
		$headers['To']	  	= $user_data['email'];
		$headers['Subject'] = $subject;
		$result = $mail_object->send($user_data['email'], $headers, $message);

		if (PEAR::isError($result)) {
			print 'mail error: '.$result->getMessage()."<br />\n";
		} else {
			return true;
		}
	
	}//end emailUserdetails
	
	/**
	* Generate a random password 
	* 
	* @param int $length length of random string to generate
	* @return $password
	*/
	function generatepassword ($length = 6)
	{

  		$adjectives_array = array('blue','green','fast','red','big');
		$nouns_array = array('cat','dog','car','hat','cap','kid');
		$adj_key = array_rand($adjectives_array);
		$noun_key = array_rand($nouns_array);
		$num = rand(10,99);
		$password = $adjectives_array[$adj_key].$nouns_array[$noun_key].$num;
		return $password;
		// start with a blank password
  		$password = "";

  		// define possible characters
  		$possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOP"; 
	
  		// set up a counter
  		$i = 0; 
	
  		// add random characters to $password until $length is reached
  		while ($i < $length) { 
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) { 
	  			$password .= $char;
	  			$i++;
			}
  		}
  		// done!
  		return $password;

	} //end generatepassword
	
	/**
	* Check is a user is subscribed to a particular module 
	* 
	* @param int $module_key key of module to check subscription data for
	* @param int $user_key key of user to check subscription data for
	* @return int $type_key type of subscription 1='all', 2='daily digest',3='weekly digest'
	*/
	function isSubscribed ($module_key, $user_key)
	{
		global $CONN, $CONFIG;
		$type_key = $CONN->GetOne("SELECT type_key FROM {$CONFIG['DB_PREFIX']}module_subscription_links WHERE module_key='$module_key' AND user_key='$user_key'");
		return $type_key; 		
	}
	
	/**
	* Get an array of users for a given space or group
	* 
	* @param int $space_key key of key of space to get users for
	* @param int $group_key key of group to get users for
	* @return array $user_array associative array of users $user_array[$user_key] = 'last_name, first_name 
	*/
	function getUserArray($space_key, $group_key=0)
	{
		global $CONN, $CONFIG;
		
		$concat = $CONN->Concat("{$CONFIG['DB_PREFIX']}users.last_name",'\', \'',"{$CONFIG['DB_PREFIX']}users.first_name");
		if ($space_key==$CONFIG['DEFAULT_SPACE_KEY']){ 
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}users.user_key FROM {$CONFIG['DB_PREFIX']}users WHERE {$CONFIG['DB_PREFIX']}users.account_status='1'";
		} else if ($group_key==0 || $group_key=='') {
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}space_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}space_user_links WHERE {$CONFIG['DB_PREFIX']}space_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND space_key='$space_key' AND {$CONFIG['DB_PREFIX']}space_user_links.access_level_key!='3'";
		}  else {
			$user_sql = "SELECT $concat, {$CONFIG['DB_PREFIX']}group_user_links.user_key FROM {$CONFIG['DB_PREFIX']}users, {$CONFIG['DB_PREFIX']}group_user_links WHERE {$CONFIG['DB_PREFIX']}group_user_links.user_key={$CONFIG['DB_PREFIX']}users.user_key AND {$CONFIG['DB_PREFIX']}group_user_links.group_key='$group_key'";
		} 
		$rs = $CONN->Execute($user_sql);

		if ($rs->EOF) {
			return false;
		} else {
			$user_array = array();
			while (!$rs->EOF) {
			   $user_array[$rs->fields[1]] = $rs->fields[0];
			   $rs->MoveNext(); 
			}
			$rs->Close();
			asort($user_array);			
			return $user_array;
		}
	}
	/**
	* Add a MySpace for a user
	* 
	* @param int $user_key key of user to add MySpace for
	* @param array $user_data array of data to for user to add MySpace for
	* @return true if successful
	*/
	function addMySpace($user_key, $user_data)
	{
		global $CONN, $CONFIG;
		
		//first see if myspace already exists
		if ($CONN->GetOne("SELECT space_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE owned_by_key='$user_key'")===false) {
			
			$date_added = $CONN->DBDate(date('Y-m-d H:i:s'));
			$name = $user_data['first_name'].' '.$user_data['last_name'];
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}modules(type_code,status_key,name,added_by_key, owner_key, date_added) VALUES ('space',1,'$name','1','$user_key',$date_added)");
			$space_data['module_key']       = $CONN->Insert_ID();
			$space_data['short_name']       = '';
			$space_data['name']             = $user_data['first_name'].' '.$user_data['last_name'];
			$space_data['description']      = '';
			$space_data['access_code']      = rand(1001,9999);
			$space_data['combine_names']    = 0;
			$space_data['access_level_key'] = 2;
			$space_data['visibility_key']   = 1;	
			$space_data['show_members']     = 1;
			$space_data['code']             = '';						
			$space_data['current_user_key'] = 1;
			$space_data['owned_by_key']     = $user_key;
			$space_data['skin_key']         = $CONFIG['DEFAULT_SKIN_KEY'];
			$space_data['new_user_alert']   = 1;
			$space_data['type_key']         = 0;
			$space_data['space_admin_key']  = $user_key;
			require_once($CONFIG['BASE_PATH'].'/spaceadmin/lib.inc.php');
			$objSpaceAdmin = new InteractSpaceAdmin();
			$objSpaceAdmin->addSpace($space_data);
			return true;		
		} else {
			return false;			
		}
		
		
	}//end addMySpace
	
	/**
	* Generate a username based on firstname and last name
	* 
	* @param string $first_name users first name
	* @param string $last_name users last name
	* @param int $n number to append to end to make unique	
	* @return $username
	*/
	function generateusername ($first_name, $last_name)
	{

		global $CONN, $CONFIG;
		
		$first_name = ereg_replace("[^a-zA-Z]*","",$first_name);
        $last_name = ereg_replace("[^a-zA-Z]*","",$last_name);
		$username = strtolower($last_name.substr($first_name, 0, 1));
		
		$rs = $CONN->Execute("SELECT user_key from {$CONFIG['DB_PREFIX']}users WHERE username='$username'");
		
		if ($rs->EOF) {
		
			return $username;
			
		} else {
		
			$n = 1;
			$exists = true;
			while ($exists==true) {
			
				$username = strtolower($last_name.substr($first_name, 0, 1).$n);
				$rs = $CONN->Execute("SELECT user_key from {$CONFIG['DB_PREFIX']}users WHERE username='$username'");
				if ($rs->EOF) {
					$exists = false;
					return $username;
				} else {
				
					$n++;
				
				}
				
			}
				
		}
			
	} //end generateusername
		
} //end InteractUser
?>
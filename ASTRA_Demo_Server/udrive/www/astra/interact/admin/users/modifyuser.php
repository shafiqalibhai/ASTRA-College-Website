<?php
// | Modify a users key details										   |

require_once('../../local/config.inc.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');

//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

if ($_POST['user_key']) {

	switch($_POST['submit']) {
	
		case Modify:
		
			$username = $_POST['users_name'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$email = $_POST['email'];
			$address = $_POST['address'];
			$phone_no = $_POST['phone_no'];
			$branch = $_POST['branch'];
			$attendance = $_POST['attendance'];
			$backlogs = $_POST['backlogs'];
			
			if (isset($_POST['password']) && $_POST['password']!='') {
	
				if ($CONFIG['AUTH_TYPE']=='dbplain') {
					$password = $_POST['password'];
				} else {
					$password = md5($_POST['password']);
				}
				
				$sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET first_name='$first_name',last_name='$last_name',password='$password', email='$email',username='$username',level_key='{$_POST['level_key']}',address='$address',phone_no='$phone_no',branch='$branch' ,attendance='$attendance',backlogs='$backlogs',account_status='{$_POST['status_key']}', user_id_number='{$_POST['user_id_number']}' WHERE user_key='{$_POST['user_key']}'";
		
			} else {

				$sql = "UPDATE {$CONFIG['DB_PREFIX']}users SET first_name='$first_name',last_name='$last_name',email='$email',username='$username',address='$address',phone_no='$phone_no',branch='$branch',attendance='$attendance',backlogs='$backlogs',level_key='{$_POST['level_key']}', account_status='{$_POST['status_key']}', user_id_number='{$_POST['user_id_number']}' WHERE user_key='{$_POST['user_key']}'";	
	
			}

			if ($CONN->Execute($sql) === false) {
	
  				$error = urlencode($CONN->ErrorMsg());
				header("Location: {$CONFIG['FULL_URL']}/admin/users/userlookup.php?message=There+was+a+problem+$error");
				exit;

			} else {
	
				//create user object if it doesn't already exist
				if (!class_exists('InteractUser')) {
					require_once('../../includes/lib/user.inc.php');
				}
				if (!is_object($objUser)) {
					$objUser = new InteractUser();
				}
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}user_usergroup_links WHERE user_key='{$_POST['user_key']}'");	
				$date_added=$CONN->DBDate(date('Y-m-d H:i:s'));			
				$objUser->addToUserGroups($_POST['user_key'], $_POST['user_group_keys'], $date_added);
	
				header("Location: {$CONFIG['FULL_URL']}/admin/users/userlookup.php?message=User+updated+successfully");
				exit;
				
			}
			
		break;
		
		case Delete:
		
			require_once('../../includes/lib/user.inc.php');
			$user = new InteractUser();
			
			$message = $user->deleteUser($_POST['user_key']);
			if ($message===true) {
			
				header("Location: {$CONFIG['FULL_URL']}/admin/users/userlookup.php?message=User+deleted+successfully");
				exit;
				
			} else {

				urlencode($message);
				header("Location: {$CONFIG['FULL_URL']}/admin/users/userlookup.php?message=$message");
				exit;
				
			}			
		
		break;	
		
	}

} else {

	header("Location: {$CONFIG['FULL_URL']}/admin/users/userlookup.php?message=You+did+not+select+a+user");
	exit;

}

$CONN->Close();
?>
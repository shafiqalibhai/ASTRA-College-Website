<?php
/**
* Db authentication with an md5 encrypted password
*
* Authenticates users against the default Interact db with md5 encrypted password
*
* @package Authentication
* 
*/

/**
* Check password against md5 encrypted password from interact database 
* 
* @param string $username not actually required for this auth method
* @param string $password password entered by user
* @param string $password2 md5 password from database
* @return true if passwords match
*/
function auth_dbencrypt($username, $password, $password2='', $level_key='', $localaccount=true) {

	global $CONFIG;
	
	if ($localaccount && md5($password)==$password2) {
	
		return true;
		
	} else {
	
		return false;
	
	}

} //end auth_dbencrypt

function show_password_change($level_key) {
	
	return true;

}

function get_default_user_level() {
	
	return 3;
	
}
?>
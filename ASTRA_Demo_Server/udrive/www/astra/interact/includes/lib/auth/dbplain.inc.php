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
* Db authentication with plain text password
*
* Authenticates users against the default Interact db with plain text passwords 
*
* @package Authentication
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2001 
* @version $Id: dbplain.inc.php,v 1.4 2007/06/14 23:58:10 websterb4 Exp $
* 
*/

/**
* Check password against plain text password from interact database 
* 
* @param string $username not actually required for this auth method
* @param string $password password entered by user
* @param string $password2 plain text password from database
* @return true if passwords match
*/
function auth_dbplain($username, $password, $password2='', $level_key='', $localaccount=true) {

	if ($localaccount && $password==$password2) {
	
		return true;
		
	} else {
	
		return false;
	
	}

} //end auth_dbplain

function show_password_change($level_key) {
	
	return true;

} 

function get_default_user_level() {
	
	return 3;
	
}
?>
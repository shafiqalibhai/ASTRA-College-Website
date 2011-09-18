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
* Form functions
*
* Contains any functions related to database
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: form.inc.php,v 1.1 2005/10/20 00:58:25 glendavies Exp $
* 
*/

/**
* A class that contains methods related to form processing 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for displying and checking forms. 
* 
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteractForm {

	/**
	* Validate data returned by a form  
	* 
	* @param array $field_names an array of field names to check
	* @param array $field_data an array of data fields returned by the form
	* @param array $string_file array of language strings for current module
	* @return $errors array an array of any error messages
	*/

	function checkForm($field_names, $field_data, $string_file)
	{
		global $CONN, $CONFIG, $general_strings;
		$errors = array();
		foreach($field_names as $value) {
			if (!isset($field_data[$value]) || $field_data[$value]=='') {
				if (isset($string_file[$value.'_error'])) {
					$errors[$value] = $string_file[$value.'_error'];
				} else if (isset($general_strings[$value.'_error'])) {
					$errors[$value] = $string_file[$value.'_error'];
				}else {
					$errors[$value] = $general_strings['required_field_empty'];
				}
			}
		}
		return $errors;
	} //end checkForm

} //end InteractForm
?>
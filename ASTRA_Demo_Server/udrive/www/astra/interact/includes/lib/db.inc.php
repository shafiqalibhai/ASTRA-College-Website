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
* Database  functions
*
* Contains any functions related to database
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: db.inc.php,v 1.5 2007/01/25 03:11:26 glendavies Exp $
* 
*/

/**
* A class that contains methods related to database functions 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying data in database. 
* 
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteractDb {

	/**
	* Create an insert string  
	* 
	* @param string $table_name name of table to insert into
	* @param array $field_array array of fields to insert
	* @param array $data array/object of data to insert
	* @return $update_sql string of sql to perform update
	*/

	function getInsertSql($table_name, $field_array, $data)
	{
		global $CONN, $CONFIG;

		$total_fields = count($field_array);
		$n=1;
		$insert_sql ='INSERT INTO '.$CONFIG['DB_PREFIX'].$table_name;
		$fields_sql = '(';
		$data_sql   = '(';
		
		foreach ($field_array as $value) {
			if ($item=(is_object($data)? 
			  (isset($data->$value)?$data->$value:null) : 
			  (isset($data[$value])?$data[$value]:null))) {
				if ($n>1) {
					$fields_sql .=','; 
					$data_sql   .= ',';
				}
				
				$fields_sql .= $value;
				if (strpos($value,'date')===false){
					$data_sql   .= "'$item'";
				} else {
					$data_sql   .= $CONN->DBdate($item);
				}
				$n++;
			}
		}
		$fields_sql .=')'; $data_sql   .= ')';

		$insert_sql .= $fields_sql.' VALUES '.$data_sql;	
		return $insert_sql;
	} //end getInsertSql

	
	/**
	* Create an update string  
	* 
	* @param string $table_name name of table to update
	* @param array $field_array array of fields to update
	* @param array $data_array array of data to update
	* @param array $key array of key data of record to be updated $key['name'], $key['value']	
	* @return $update_sql string of sql to perform update
	*/

	function getUpdateSql($table_name, $field_array, $data_array, $key='')
	{
		global $CONN, $CONFIG;
		
		$total_fields = count($field_array);
		$n=1;
		$update_sql='UPDATE '.$CONFIG['DB_PREFIX'].$table_name.' SET ';
		
		foreach ($field_array as $value) {
		
			if (isset($data_array[$value])) {
				if($n==$total_fields) {
					if (strpos($value,'date')===false){
						$update_sql .= $value.'='."'".$data_array[$value]."'";
					} else {
						$update_sql .= $value.'='.$CONN->DBdate($data_array[$value]);
					}
				} else {
					if (strpos($value,'date')===false){
						$update_sql .= $value.'='."'".$data_array[$value]."',";
					} else {
						$update_sql .= $value.'='.$CONN->DBdate($data_array[$value]).',';
					}
				}
			}
			$n++;
		}
		if (is_array($key)) {
			$update_sql .= ' WHERE '.$key['name'].'=\''.$key['value'].'\'';
		}
	
		return $update_sql;
	} //end getUpdateSql

} //end InteractDb
?>
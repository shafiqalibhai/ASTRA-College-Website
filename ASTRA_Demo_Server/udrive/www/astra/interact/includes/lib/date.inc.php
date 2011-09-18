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
* Date functions
*
* Contains any functions related to dates
*
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: date.inc.php,v 1.18 2007/05/09 03:02:51 glendavies Exp $
* 
*/

/**
* A class that contains methods related to date functions 
* 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying dates, date selection menus, etc. 
* 
* @package Common
* @author Glen Davies <glen.davies@cce.ac.nz>
*/
class InteractDate {

	/**
	* Create a select menu of month day year  
	* 
	* @param string $in_name name of menu
	* @param date $use_date unix timestamp of preselected date
	* @param boolean $include_time if true include time select option
	* @return $date_menu html code for date select menu
	*/

	function createDateSelect($in_name, $use_date=0, $include_time=false, $start_year='', $finish_year='')
	{

		// create array so we can name months 
	
		$month_name = array(1=>'January', 'February', 'March',
		'April', 'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December');
	 
		// if date invalid or not supplied, use current time 
		// make day selector
	
		$date_menu = "<select name=\"$in_name"."_day\" id=\"$in_name"."_day\">\n";
	
		if($use_date == 0) {
			
			$date_menu = $date_menu."<option value=\"\" selected=\"selected\"></option>"; 
	
		} else {
	
			$date_menu = $date_menu."<option value=\"\" ></option>";
	
		}


		for($current_day=1; $current_day <= 31; $current_day++){
		
			$date_menu = $date_menu."<option value=\"$current_day\"";
		
			if ($use_date!= 0) {
			
				if(intval(date('d', $use_date))==$current_day){
			
					$date_menu = $date_menu." selected=\"selected\"";
			
				}
	   
			}
		
			$date_menu = $date_menu.">$current_day"."</option>\n";
	
		}

		$date_menu = $date_menu."</select>";

		
		// make month selector

		$date_menu = $date_menu."<select name=\"$in_name"."_month\" id=\"$in_name"."_month\">\n";
	
		if($use_date == 0) {
	
			$date_menu = $date_menu."<option value=\"\" selected=\"selected\"></option>"; 
	
		} else {
	
			$date_menu = $date_menu."<option value=\"\" ></option>";
	
		}

		for($current_month = 1; $current_month <= 12; $current_month++){
		
			$date_menu = $date_menu."<option value=\"";
			$date_menu = $date_menu.intval($current_month);
			$date_menu = $date_menu."\"";
		
			if ($use_date!= 0) {
		
				if(intval(date('m', $use_date))==$current_month){
				
					$date_menu = $date_menu." selected=\"selected\"";
			
				}
		
			}
		
			$date_menu = $date_menu.">" . $month_name[$current_month] . "</option>\n";
	
		}
	
		$date_menu = $date_menu."</select>";

		// make year selector 
		if($use_date!=0 && date('Y',$use_date)<date('Y')) {
			$start_year = date('Y',$use_date);
		} else if ($start_year=='') {
			$start_year = date('Y')-10;
		}
		if ($finish_year=='') {
			$finish_year = date('Y')+10;
		}
		$date_menu = $date_menu."<select name=\"$in_name"."_year\" id=\"$in_name"."_year\">\n";
		//$start_year = date('Y');
	
		if($use_date == 0) {
	
			$date_menu = $date_menu."<option value=\"$start_year\" selected=\"selected\">$start_year</option>"; 
	
		} else {
	
			$date_menu = $date_menu."<option value=\"$start_year\" >$start_year</option>";
	
		}

		for($current_year = $start_year+1; $current_year <= $finish_year;$current_year++){

			$date_menu = $date_menu."<option value=\"$current_year\"";

			if ($use_date!= 0) {

				if (date('Y', $use_date)==$current_year){

					$date_menu = $date_menu." selected=\"selected\"";

				}
		
			}
		
			$date_menu = $date_menu.">$current_year"."</option>\n";
	
		}
	
		$date_menu = $date_menu."</select>";
	
		//make time menus if required
	
		if ($include_time==true) {
		
			$date_menu = $date_menu." &nbsp;<span id=\"$in_name"."_time\">Time: <select name=\"$in_name"."_hour\" id=\"$in_name"."_hour\">\n";
		
			for ($i = 0; $i < 24; $i++){
		
				//add leadning 0 to amounts less than 10
			
				if ($i<10) {
				
					$i = '0'.$i;
			
				}
			
				$date_menu = $date_menu."<option value=\"$i\"";
		
				if ($use_date!= 0) {
		
					if (date('H', $use_date)==$i){
		
						$date_menu = $date_menu." selected=\"selected\"";
		
					}
		
				}
		
				$date_menu = $date_menu.">$i"."</option>\n";
		
			}
		
			//now add 24 as 0
			
			$date_menu = $date_menu."</select>";
		
			$date_menu = $date_menu."<select name=\"$in_name"."_minute\" id=\"$in_name"."_minute\">\n";
		
			for ($i = 00; $i < 60; $i=$i+1){
		
				//add leadning 0 to amounts less than 10
			
				if ($i<10) {
				
					$i = '0'.$i;
			
				}
				
				$date_menu = $date_menu."<option value=\"$i\"";
		
				if ($use_date!= 0) {
		
					if (date('i', $use_date)==$i){
		
						$date_menu = $date_menu." selected=\"selected\"";
		
					}
		
				}
		
				$date_menu = $date_menu.">$i"."</option>\n";
		
			}
		
			$date_menu = $date_menu."</select></span>";		

		}
		
		return $date_menu;
	
	} //end createDateSelect

	/**
	* Work out days until a future date  
	* 
	* @param string $str_start start date in format 1978-04-26 02:00:00
	* @param date $str_end end date in format 1978-04-26 02:00:00
	* @return $date_difference string of date difference
	*/
	function dateDiff($str_start, $str_end, $hours=false, $minutes=false, $seconds=false) 
	{ 

		global $general_strings;
		
		$str_start = strtotime($str_start); // The start date becomes a timestamp 
		$str_end = strtotime($str_end); // The end date becomes a timestamp 

		$nseconds = $str_end - $str_start; // Number of seconds between the two dates 
		$ndays = round($nseconds / 86400); // One day has 86400 seconds 
		$nseconds = $nseconds % 86400; // The remainder from the operation 
		$nhours = round($nseconds / 3600); // One hour has 3600 seconds 
		$nseconds = $nseconds % 3600; 
		$nminutes = round($nseconds / 60); // One minute has 60 seconds, duh! 
		$nseconds = $nseconds % 60; 

		$date_difference =  $ndays.' '.$general_strings['days'];
		
		if ($hours==true) {
		
			$date_difference .= ' '.$nhours.' '.$general_strings['hours'];
			
		} 
		
		if ($minutes==true) {
		
			$date_difference .= ' '.$nminutes.' '.$general_strings['minutes'];
			
		}
		
		if ($seconds==true) {
		
			$date_difference .= ' '.$nseconds.' '.$general_strings['seconds'];
			
		}
		
		return $date_difference; 

	} //end dateDiff

	/**
	* Check to see if a date is valid and if not change to closest valid date
	* eg. changes 30 Sep to 1 Oct
	* 
	* @param string $full_date start date in format 1978-04-26 02:00:00
	* @return $date validated date
	*/
	function checkDateValid($full_date)
	{
		
		$t=strtotime($full_date); 
		return date('Y-m-d H:i',$t); 

	} //end checkDateValid
	
	/**
	* Format a unixtimestamp to a date based on default date format from config.inc.php
	* 
	* @param string $timestamp unix timestamp to be formated
	* @param string $format optional date format string to override config setting	
	* @return string $date date in required format 
	*/
	function formatDate($timestamp, $type='short', $time=false)
	{
		 global $CONFIG, $space_key,$CONN;   
	
			//if we have a space_key then get space date format setting
			/*
			if ($space_key && $space_key!='') {
			
				$rs = $CONN->Execute("SELECT short_date_format_key, long_date_format_key FROM {$CONFIG['DB_PREFIX']}spaces WHERE space_key='$space_key'");
				
				if ($rs->EOF) {
					
					$short_date_format = $CONFIG['SHORT_DATE_FORMAT'];
					$long_date_format  = $CONFIG['LONG_DATE_FORMAT'];
					
				} else {
				
					while (!$rs->EOF) {
					
						$short_date_format = $rs->fields[0];
						$long_date_format  = $rs->fields[1];
						$rs->MoveNext();
						
					}
					
				}
			
			} else {
			*/
				$short_date_format = $CONFIG['SHORT_DATE_FORMAT'];
				$long_date_format  = $CONFIG['LONG_DATE_FORMAT'];
			
			//}
			
			if ($type=='short') {
				switch ($short_date_format) {
				
					case 0:
						$date = date('d-m-y', $timestamp);
					break;
					
					case 1:
						$date = date('m-d-y', $timestamp);
					break;

					case 2:
						$date = date('j-n-y', $timestamp);
					break;

					case 3:
						$date = date('n-j-y', $timestamp);
					break;					
					
					case 4:
						$date = date('d-m-Y', $timestamp);
					break;
					
					case 5:
						$date = date('m-d-Y', $timestamp);
					break;

					case 6:
						$date = date('j-n-Y', $timestamp);
					break;
					
					case 7:
						$date = date('n-j-Y', $timestamp);
					break;

					default:
						$date = date('m-d-y', $timestamp);
					break;
				}																													
					
			} else {
				switch ($long_date_format) {
					
					case 0:
						$date = date('j M Y', $timestamp);
					break;
					
					case 1:
						$date = date('j F Y', $timestamp);
					break;
					
					case 2:
						$date = date('M j Y', $timestamp);
					break;
					
					case 3:
						$date = date('F j Y', $timestamp);
					break;
					
					default:
						$date = date('F j Y', $timestamp);
					break;					
				}
			}
			
			//see if time also required
			if ($time==true) {
				$date = $date.' '.$this->formatTime($timestamp);
			}
			
			return $date; 
	} //end formatDate
	
	function formatTime($timestamp) {
		return date('g:ia',$timestamp);
	}
	function convertMonthNumtoTxt($num) {
		
		global $CONFIG, $calendar_strings;
		
		$months = array('',$calendar_strings['jan_abb'],$calendar_strings['feb_abb'],$calendar_strings['mar_abb'],$calendar_strings['apr_abb'],$calendar_strings['may_abb'],$calendar_strings['jun_abb'],
$calendar_strings['jul_abb'],$calendar_strings['aug_abb'],$calendar_strings['sep_abb'] = 'Sep',$calendar_strings['oct_abb'],$calendar_strings['nov_abb'],$calendar_strings['dec_abb']);

		return $months[$num];
	}

} //end InteractDate
?>
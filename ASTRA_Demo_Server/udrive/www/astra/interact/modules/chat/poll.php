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
* Chat poll
*
*
* @package Chat
* @author Bruce Webster <b@cce.ac.nz>
* @copyright Christchurch College of Education 2006
* @version $Id: poll.php,v 1.8 2007/05/30 13:34:48 websterb4 Exp $
* 
*/

/**
* Include main system config file 
*/
require_once '../../local/config.inc.php';
//require_once $CONFIG['LANGUAGE_CPATH'].'/chat_strings.inc.php';

if(isset($HTTP_RAW_POST_DATA) || isset($_POST['data'])){

	$this_time=time();
	$event_time=$this_time;
	$handle=$_SESSION['current_user_firstname'].' '.$_SESSION['current_user_lastname'];
	$status=0;
	
	$xml_parser = xml_parser_create();
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, true);
	xml_set_element_handler($xml_parser, "XMLstartElement", "XMLendElement");
	xml_set_character_data_handler($xml_parser, "XMLcharacterElement");
	
	if (!xml_parse($xml_parser, 
		(isset($HTTP_RAW_POST_DATA)?$HTTP_RAW_POST_DATA:stripslashes($_POST['data'])), true)) {
	   die(sprintf("XML error: %s at line %d",
			 xml_error_string(xml_get_error_code($xml_parser)),
			 xml_get_current_line_number($xml_parser)));
	}
	xml_parser_free($xml_parser);
	
	$pollID=0;
	if(!($status&1)) {
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		if($new_join || !$last_poll) {  //just dump out last 30 mins.
			spitOutEvents($CONN->Execute("SELECT id,for_user, data FROM {$CONFIG['DB_PREFIX']}chat_events WHERE module_key='$module_key' AND time > ".$CONN->DBTimeStamp($this_time-1800)." ORDER BY id"));
			$rs=$CONN->Execute("SELECT user_key, handle, role FROM {$CONFIG['DB_PREFIX']}chat_users WHERE module_key='$module_key' AND status=0");
			while (!$rs->EOF) {
				echo'<status user_key="'.$rs->fields[0].'" handle="'.$rs->fields[1].'" role="'.$rs->fields[2].'" />';
				$rs->MoveNext();
			}
		} else {
			spitOutEvents($CONN->Execute("SELECT id,for_user, data FROM {$CONFIG['DB_PREFIX']}chat_events WHERE module_key='$module_key' AND id > $last_poll ORDER BY id"));
		}

		
		echo '</events>
';
	}
	if($last_poll && $pollID==0){$pollID=$last_poll;}
		
	if($last_poll!==false) {
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}chat_users SET handle='$handle', role='$role', last_poll=".$CONN->DBTimeStamp($this_time).", last_poll_id='$pollID', status='$status' WHERE user_key='{$_SESSION['current_user_key']}' AND module_key='$module_key'");
	} else {
		$last_poll="";
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}chat_users (`module_key`,`user_key`,`handle`,`role`,`last_poll`,`last_poll_id`,`status`) VALUES ('$module_key','{$_SESSION['current_user_key']}','$handle','$role',".$CONN->DBTimeStamp($this_time).",'$pollID','$status')");
	}
}

function spitOutEvents($rs) {
	global $pollID;
	echo '<events';
	if(!$rs->EOF) {echo ' id="'.$rs->fields[0].'"';}
	echo '>';
	while (!$rs->EOF) {
		if(!$rs->fields[1] || strstr($rs->fields[1],$_SESSION['current_user_key'])!==false) {
			echo $rs->fields[2];
		}
		$pollID=$rs->fields[0];
		$rs->MoveNext();
	}
}

function wipe_user($ukey) {
	global $module_key,$CONN,$CONFIG;
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}chat_users WHERE module_key = '$module_key' AND user_key = '{$ukey}'");
}

function setGlobals($attrs) {
global $space_key,$module_key,$group_key,$link_key,$access_levels,$CONN,$CONFIG,$handle,$role,$last_poll,$this_time,$xml,$event_time;
	$space_key=$attrs['space_key'];
	$module_key=$attrs['module_key'];
	$link_key 	= get_link_key($module_key,$space_key);
	$group_key	= $attrs['group_key'];

//check we have the variables we need
	check_variables(true,true,true);
	$access_levels = authenticate();
	
	$sql="SELECT Role,LastPollID FROM {$CONFIG['DB_PREFIX']}chat_users WHERE user_key='{$_SESSION['current_user_key']}' AND module_key='$module_key'";
	$rs=$CONN->Execute("SELECT role,last_poll_id,status FROM {$CONFIG['DB_PREFIX']}chat_users WHERE user_key='{$_SESSION['current_user_key']}' AND module_key='$module_key'");
	if($rs->fields){
		$role=$rs->fields[0];
		$last_poll=$rs->fields[1];
	} else {
		$role='none';   //auto-set initial role here?
		$last_poll=false;
	}

	$last_clean=$CONN->GetOne("SELECT last_clean FROM {$CONFIG['DB_PREFIX']}chat WHERE module_key='$module_key'");
	if($this_time-$CONN->UnixTimeStamp($last_clean)>30) {
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}chat SET last_clean=".$CONN->DBTimeStamp($this_time)." WHERE module_key='$module_key'");
		$rs=$CONN->Execute("SELECT user_key,handle,last_poll FROM {$CONFIG['DB_PREFIX']}chat_users WHERE module_key='$module_key' AND status=0 AND last_poll<".$CONN->DBTimeStamp($this_time-45));
		if($rs) {
			while (!$rs->EOF) {
				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}chat_users SET status='1' WHERE user_key='{$rs->fields[0]}' AND module_key='$module_key'");
				$xml='<vanish user_key="'.$rs->fields[0].'" handle="'.$rs->fields[1].'" />';
				$event_time=$CONN->UnixTimeStamp($rs->fields[2])+1;
				writeToDB();
				$rs->MoveNext();
			}	
		}
	}
}

function XMLstartElement($parser, $name, $attrs) {
	global $xml,$for_users,$handle,$role,$status,$new_join;
	
	$xml='';$for_users='';

	$text_attr=' user_key="'.$_SESSION['current_user_key'].'" handle="'.$handle.'"';

// 	if($status&1) {
//		$text_attr.=' role="'.$role.'"';
// 		$status=$status&254;
// 	}

	if($name=='events') {setGlobals($attrs);} else {
		switch($name) {
		case 'exit':
			$status|=1;
		case 'join':
			$new_join=true;
			$text_attr.= ' role="'.$role.'"';
		}
		if($attrs) {
			foreach ($attrs as $key=>$val) {
				switch($key) {
				case 'for_users':
					$for_users=$val.','.$_SESSION['current_user_key'];
					break;
				case 'role':
					$role=$val;
				default:
					$text_attr.= ' '.$key.'="'.$val.'"';
				}
			}
		}
		$xml.= "<$name".$text_attr.">";
	}
}

function writeToDB() {
	global $CONFIG,$CONN,$xml,$for_users, $module_key,$event_time;
	if($xml) {
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}chat_events(module_key,time,for_user,data) VALUES ('$module_key',".$CONN->DBTimeStamp($event_time).",'$for_users','$xml')");
	}
}

function XMLendElement($parser, $name) {
	global $xml;
	if($name!='events'){$xml.= "</$name>";writeToDB();}
}


function XMLcharacterElement($parser, $text) {
	global $xml;
	if($text) {
		$xml.= $text;
	}
}
?>
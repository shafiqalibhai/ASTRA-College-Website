<?php
/**
* Cleanup
*
* Runs any automated functions for each module
* 
*/

/**
* Include main config file 
*/
$CONFIG['NO_SESSION'] = 1;
$ACONFIG['CONFIG_NUM'] = 0;
$ACONFIG['MAX_CONFIG_NUM'] = 1;

//see if main confing file has already been included, if not include it
//if (!isset($CONFIG['FULL_URL'])) {
	require_once('../local/config.inc.php');
//}

//run autoprompter functions (will be integrated into Forum module autofunctions at some stage!)
//require_once('autoprompter.php');

require_once($CONFIG['BASE_PATH'].'/includes/modules.inc.php');

for(;$ACONFIG['CONFIG_NUM']<$ACONFIG['MAX_CONFIG_NUM'];$ACONFIG['CONFIG_NUM']++) {
	if($ACONFIG['CONFIG_NUM'] || !isset($CONFIG['SERVER_URL'])) {$CONN=get_data_config();}

	//get the time of the last cron
	$last_cron = $CONN->UnixTimestamp($CONN->GetOne("SELECT last_run FROM {$CONFIG['DB_PREFIX']}cron"));
	
	//get all module codes and run autofunctions
	
	$rs = $CONN->Execute("SELECT code FROM {$CONFIG['DB_PREFIX']}module_types");
	
	while (!$rs->EOF) {
	
		$code		= $rs->fields[0];

		$module_file = $CONFIG['BASE_PATH'].'/modules/'.$code.'/'.$code.'.inc.php';
		
		if (file_exists($module_file)) {
		
			include_once($module_file);
			$auto_function = 'autofunctions_'.$code;
		   
			if (function_exists($auto_function)) {
			
				if (!$auto_function($last_cron)) {
				
					echo "Error: could not run auto functions for  $code\n";
							
				}
			
			}
		}
		
		$rs->MoveNext();
		
	}

	//run any other misc automated routines
	
	// change status of modules
	$date_modified = date('Y-m-d H:i:s');
	$time = time();
	$w_time = $time+3600;
	$current_date  = date('Y-m-d',$w_time);
	
	$rs = $CONN->Execute("SELECT link_key,change_status_to_key,type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.change_status_date='$current_date' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'");
	
	while (!$rs->EOF) {
	
		$link_key=$rs->fields[0];
		$change_to_key=$rs->fields[1];
		$type_code = $rs->fields[2];
		
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='$change_to_key',date_modified='$date_modified' WHERE link_key='$link_key'");
			
		if ($type_code=='folder' || $type_code=='group') {
		
			change_child_status($link_key,$change_to_key);
		
		}
		
		$rs->MoveNext();
	
	}
	
	$rs->Close();
	
	//delete items stale from trash
	$modules = new InteractModules();

	//delete flagged links
	
	$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.type_code,{$CONFIG['DB_PREFIX']}module_space_links.module_key,link_key,space_key from {$CONFIG['DB_PREFIX']}modules, {$CONFIG['DB_PREFIX']}module_space_links where  {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='4' AND {$CONFIG['DB_PREFIX']}module_space_links.date_modified<DATE_SUB(CURRENT_DATE, INTERVAL {$CONFIG['KEEP_TRASH']} DAY)";
	
	//$sql = "SELECT {$CONFIG['DB_PREFIX']}module_types.code,{$CONFIG['DB_PREFIX']}module_space_links.module_key,link_key,space_key from {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_types, {$CONFIG['DB_PREFIX']}module_space_links where {$CONFIG['DB_PREFIX']}modules.module_type_key={$CONFIG['DB_PREFIX']}module_types.module_type_key AND {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='4'";
	
	$rs = $CONN->Execute($sql);
	
	while (!$rs->EOF) {
	
		$module_code = $rs->fields[0];
		$module_key = $rs->fields[1];
		$link_key = $rs->fields[2];
		$space_key = $rs->fields[3];
		$modules->delete_module($module_key,$space_key,$link_key,'link_only',$module_code);
		$rs->MoveNext();
		
	}
	
	$rs->Close();
	
	//delete flagged modules
	
	$sql = "SELECT {$CONFIG['DB_PREFIX']}modules.type_code,module_key from {$CONFIG['DB_PREFIX']}modules where status_key='4' AND {$CONFIG['DB_PREFIX']}modules.date_modified<DATE_SUB(NOW(), INTERVAL {$CONFIG['KEEP_TRASH']} DAY)";
	
	//$sql = "SELECT {$CONFIG['DB_PREFIX']}module_types.code,module_key from {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_types where {$CONFIG['DB_PREFIX']}modules.module_type_key={$CONFIG['DB_PREFIX']}module_types.module_type_key AND status_key='4'";
	
	$rs = $CONN->Execute($sql);
	
	
		while (!$rs->EOF) {
	
			$module_code = $rs->fields[0];
			$module_key = $rs->fields[1];
			$modules->delete_module($module_key,'','','all',$module_code);
			$rs->MoveNext();
		
		}
	
	$rs->Close();
	
	//delete stale news items
	
	$CONN->Execute("DELETE from {$CONFIG['DB_PREFIX']}news WHERE remove_date < CURDATE() AND remove_date > '0000-00-00'");
	
	//delete stale accounts
	require_once('../includes/lib/user.inc.php');
	
	$user = new InteractUser();
	
	if ($CONFIG['KEEP_STALE_ACCOUNTS']>0) {
	
		$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}users WHERE ((last_use>'0000-00-00' AND last_use<DATE_SUB(CURRENT_DATE, INTERVAL {$CONFIG['KEEP_STALE_ACCOUNTS']} DAY)) OR  (last_use='0000-00-00' AND date_added<DATE_SUB(CURRENT_DATE, INTERVAL {$CONFIG['KEEP_STALE_ACCOUNTS']} DAY))) AND username NOT LIKE '_interact_%'");
		
		if (!$rs->EOF) {
		
			while (!$rs->EOF) {
			
				$user->deleteUser($rs->fields[0]);
				$rs->MoveNext();
				
			}
		
		}
	
		$rs->Close();
			
	}

	//now delete accounts flagged to be deleted
	
	$rs = $CONN->Execute("SELECT user_key FROM {$CONFIG['DB_PREFIX']}users Where account_status='3'");
	
	while (!$rs->EOF) {
			
		$user->deleteUser($rs->fields[0]);
		$rs->MoveNext();
				
	}
	$rs->Close();
	
	//update cron table
	$date_time = $CONN->DBDate(date('Y-m-d H:i:s'));
	if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}cron SET last_run=$date_time")==false) {
		echo "There was a problem updating the time of the last cron job";	
	}
	
	$CONFIG=$ACONFIG['CONFIG'];
}

function change_child_status($link_key,$status_key) 
{
	global $CONN, $CONFIG;
	
	$rs=$CONN->Execute("SELECT link_key,type_code FROM {$CONFIG['DB_PREFIX']}modules,{$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}modules.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.parent_key='$link_key' AND {$CONFIG['DB_PREFIX']}module_space_links.status_key!='4'");
	
	while (!$rs->EOF) {
	
		$child_link_key = $rs->fields[0];
		$type_code = $rs->fields[1];
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}module_space_links SET status_key='$status_key' WHERE link_key='$child_link_key'");
				
		if ($type_code=='folder' || $type_code=='group') {
		
			change_child_status($child_link_key,$status_key);
		
		}
		
		$rs->MoveNext();
	
	}
	
	$rs->Close();
	return true;

} //end change_child_status
?>
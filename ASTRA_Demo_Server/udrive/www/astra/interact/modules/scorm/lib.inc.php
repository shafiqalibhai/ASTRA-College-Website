<?php  // $Id: lib.inc.php,v 1.26 2007/01/04 22:09:14 glendavies Exp $

/// Library of functions and constants for module scorm

// significant parts of this SCORM module were taken 
// with permission from Roberto Pinna's moodle module -- thanks Bobo!



function print_string ($identifier,$module,$a=NULL) {
	global $scorm_strings;
	echo $scorm_strings[$identifier];
}

function get_string($identifier,$module=NULL,$a=NULL) {
	global $scorm_strings;
	return $scorm_strings[$identifier];
}

function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields='*') {

    global $CONFIG ;

    $select = 'WHERE '. $field1 .' = \''. $value1 .'\'';

    if ($field2) {
        $select .= ' AND '. $field2 .' = \''. $value2 .'\'';
        if ($field3) {
            $select .= ' AND '. $field3 .' = \''. $value3 .'\'';
        }
    }

    return get_record_sql('SELECT '.$fields.' FROM '. $CONFIG['DB_PREFIX']. $table .' '. $select);
}

function get_record_sql($sql, $expectmultiple=false) {

    global $CONN;

    if (!$rs = $CONN->Execute($sql . ' LIMIT 1')) {
        return false;
    }
    
$recordcount = $rs->RecordCount();

    if ($recordcount == 1) {          // Found one record
        return (object)$rs->fields;
    } else {
    	return false;                 // Found no records
    }
}

function get_record_select($table, $select='', $fields='*') {

    global $CONFIG;

    if ($select) {
        $select = 'WHERE '. $select;
    }
    return get_record_sql('SELECT '. $fields .' FROM '. $CONFIG['DB_PREFIX'] . $table .' '. $select);
}

function p($var) {
    if ($var == '0') {  // for integer 0, boolean false, string '0'
        echo '0';
    } else {
        echo htmlSpecialChars(stripslashes_safe($var));
    }
}

function stripslashes_safe($string) {

    $string = str_replace("\\'", "'", $string);
    $string = str_replace('\\"', '"', $string);
    $string = str_replace('\\\\', '\\', $string);
    return $string;
}

function getvar($name) {
   if (!empty($_POST[$name])) {
        return $_POST[$name];
    }
    
    if (!empty($_GET[$name])) {
        return $_GET[$name];
    }
	return null;
}

require_once($CONFIG['BASE_PATH'].'/modules/scorm/xml2array.class.php');  // Used to parse manifest

define('VALUESCOES', '0');
define('VALUEHIGHEST', '1');
define('VALUEAVERAGE', '2');
define('VALUESUM', '3');
$SCORM_GRADE_METHOD = array (VALUESCOES => get_string('gradescoes', 'scorm'),
                             VALUEHIGHEST => get_string('gradehighest', 'scorm'),
                             VALUEAVERAGE => get_string('gradeaverage', 'scorm'),
                             VALUESUM => get_string('gradesum', 'scorm'));



function scorm_get_resources($blocks) {
    foreach ($blocks as $block) {
        if ($block['name'] == 'RESOURCES') {
            foreach ($block['children'] as $resource) {
                if ($resource['name'] == 'RESOURCE') {
                    $resources[addslashes($resource['attrs']['IDENTIFIER'])] = $resource['attrs'];
                }
            }
        }
    }
    return $resources;
}

function scorm_get_manifest($blocks,$scoes) {
    static $parents = array();
    static $resources;

    static $manifest;
    static $organization;

    if (count($blocks) > 0) {
        foreach ($blocks as $block) {
            switch ($block['name']) {
                case 'METADATA':
                    if (isset($block['children'])) {
                        foreach ($block['children'] as $metadata) {
                            if ($metadata['name'] == 'SCHEMAVERSION') {
                                if (empty($scoes->version)) {
                                    if (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches)) {
                                        $scoes->version = 'SCORM_'.$matches[count($matches)-1];
                                    } else {
                                        $scoes->version = 'SCORM_1.2';
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'MANIFEST':
                    $manifest = addslashes($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $resources = array();
                    $resources = scorm_get_resources($block['children']);
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    if (count($scoes->elements) <= 0) {
                        foreach ($resources as $item => $resource) {
                            if (!empty($resource['HREF'])) {
                                $sco = new stdClass();
                                $sco->identifier = $item;
                                $sco->title = $item;
                                $sco->parent = '/';
                                $sco->launch = addslashes($resource['HREF']);
                                $sco->scormtype = addslashes($resource['ADLCP:SCORMTYPE']);
                                $scoes->elements[$manifest][$organization][$item] = $sco;
                            }
                        }
                    }
                break;
                case 'ORGANIZATIONS':
                    if (!isset($scoes->defaultorg)) {
                        $scoes->defaultorg = addslashes($block['attrs']['DEFAULT']);
                    }
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                break;
                case 'ORGANIZATION':
                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = '/';
                    $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                    $scoes->elements[$manifest][$organization][$identifier]->scormtype = '';

                    $parents = array();
                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);
                    $organization = $identifier;

                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    
                    array_pop($parents);
                break;
                case 'ITEM':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    
                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
                    if (!isset($block['attrs']['ISVISIBLE'])) {
                        $block['attrs']['ISVISIBLE'] = 'true';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->isvisible = addslashes($block['attrs']['ISVISIBLE']);
                    if (!isset($block['attrs']['PARAMETERS'])) {
                        $block['attrs']['PARAMETERS'] = '';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->parameters = addslashes($block['attrs']['PARAMETERS']);
                    if (!isset($block['attrs']['IDENTIFIERREF'])) {
                        $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
                    } else {
                        $idref = addslashes($block['attrs']['IDENTIFIERREF']);
                        $scoes->elements[$manifest][$organization][$identifier]->launch = addslashes($resources[$idref]['HREF']);
                        if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
                            $resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = addslashes($resources[$idref]['ADLCP:SCORMTYPE']);
                    }

                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);

                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    
                    array_pop($parents);
                break;
                case 'TITLE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = addslashes($block['tagData']);
                break;
                case 'ADLCP:PREREQUISITES':
                    if ($block['attrs']['TYPE'] == 'aicc_script') {
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = addslashes($block['tagData']);
                    }
                break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = addslashes($block['tagData']);
                break;
                case 'ADLCP:TIMELIMITACTION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = addslashes($block['tagData']);
                break;
                case 'ADLCP:DATAFROMLMS':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = addslashes($block['tagData']);
                break;
                case 'ADLCP:MASTERYSCORE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = addslashes($block['tagData']);
                break;
            }
        }
    }

    return $scoes;
}


function scorm_get_tracks($scoid,$userid) {
/// Gets all tracks of specified sco and user
    global $CONFIG,$CONN;

    $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs=$CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE userid=$userid AND scoid=$scoid ORDER BY element ASC");
    $CONN->SetFetchMode(ADODB_FETCH_NUM);
	if ($rs && !$rs->EOF) {
        $usertrack->userid = $userid;
        $usertrack->scoid = $scoid;
        $usertrack->score_raw = '';
        $usertrack->status = '';
        $usertrack->total_time = '00:00:00';
        $usertrack->session_time = '00:00:00';
        $usertrack->timemodified = 0;
        while ($rs && !$rs->EOF) {
	    	$track = (object)$rs->fields;
            $element = $track->element;
            $usertrack->{$element} = $track->value;
            switch ($element) {
                case 'cmi.core.lesson_status':
                case 'cmi.completion_status':
                    if ($track->value == 'not attempted') {
                        $track->value = 'notattempted';
                    }
                    $usertrack->status = $track->value;
                break;
                case 'cmi.core.score.raw':
                case 'cmi.score.raw':
                    $usertrack->score_raw = $track->value;
                break;
                case 'cmi.core.session_time':
                case 'cmi.session_time':
                    $usertrack->session_time = $track->value;
                break;
                case 'cmi.core.total_time':
                case 'cmi.total_time':
                    $usertrack->total_time = $track->value;
                break;
            }
            if (isset($track->timemodified) && ($track->timemodified > $usertrack->timemodified)) {
                $usertrack->timemodified = $track->timemodified;
            }
            $rs->MoveNext();
        }
       	$rs->Close();
        return $usertrack;
    } else {
        return false;
    }
}



function scorm_add_time($a, $b) {
    $aes = explode(':',$a);
    $bes = explode(':',$b);
    $aseconds = explode('.',$aes[2]);
    $bseconds = explode('.',$bes[2]);
    $change = 0;

    $acents = 0;  //Cents
    if (count($aseconds) > 1) {
        $acents = $aseconds[1];
    }
    $bcents = 0;
    if (count($bseconds) > 1) {
        $bcents = $bseconds[1];
    }
    $cents = $acents + $bcents;
    $change = floor($cents / 100);
    $cents = $cents - ($change * 100);
    if (floor($cents) < 10) {
        $cents = '0'. $cents;
    }

    $secs = $aseconds[0] + $bseconds[0] + $change;  //Seconds
    $change = floor($secs / 60);
    $secs = $secs - ($change * 60);
    if (floor($secs) < 10) {
        $secs = '0'. $secs;
    }

    $mins = $aes[1] + $bes[1] + $change;   //Minutes
    $change = floor($mins / 60);
    $mins = $mins - ($change * 60);
    if ($mins < 10) {
        $mins = '0' .  $mins;
    }

    $hours = $aes[0] + $bes[0] + $change;  //Hours
    if ($hours < 10) {
        $hours = '0' . $hours;
    }

    if ($cents != '0') {
        return $hours . ":" . $mins . ":" . $secs . '.' . $cents;
    } else {
        return $hours . ":" . $mins . ":" . $secs;
    }
}

function scorm_external_link($link) {
// check if a link is external
    $result = false;
    $link = strtolower($link);
    if (substr($link,0,7) == 'http://') {
        $result = true;
    } else if (substr($link,0,8) == 'https://') {
        $result = true;
    } else if (substr($link,0,4) == 'www.') {
        $result = true;
    }
    return $result;
}

function scorm_count_launchable($scormid,$organization) {
	global $CONN,$CONFIG;
	
	$rs=$CONN->Execute("SELECT COUNT FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key=$scormid AND organization=$organization AND launch<>''");
	$c=$rs->fields[0];
    return $c;
}

function scorm_display_structure(&$output,$scorm,$liststyle=null,$currentorg='',$scoid='',$mode='normal',$play=false) {
    global $CONFIG, $CONN, $module_key;

    $organizationsql = '';
    if (!empty($currentorg)) {$organizationsql = "AND organization='$currentorg'";}
    $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key=$module_key $organizationsql order by id ASC");
    $CONN->SetFetchMode(ADODB_FETCH_NUM);
	$result->prev = $result->next = 0;
	$result->incomplete=false;
	$result->id=$scoid;
	$output.="<ul id='scof0'>";
	do_sco_tree($rs,$result,$output,$play);
	$output.='</ul>';
   	$rs->Close();
    if ($play) {
        return $result;
    } else {
        return $result->incomplete;
    }
}

function do_sco_tree(&$rs,&$result,&$output,$play) {
	global $CONFIG;

	$openparent=false;
	$pop=false;
	while (!$rs->EOF && !$pop) {
		$sco=(object)$rs->fields;
		$rs->MoveNext();
		$nextsco = ($rs->EOF? false:(object)$rs->fields);

		if (empty($sco->title)) {$sco->title = $sco->identifier;}

		$namelink=get_sco_namelink($sco,$result,$play);

		$samelevel=true;
		$output.='<li>';
		if (($nextsco !== false) && ($sco->parent != $nextsco->parent)) {
			if ($nextsco->parent == $sco->identifier) {
				$innerlist='';
				$openthis=do_sco_tree($rs,$result,$innerlist,$play);
				$openparent|=$openthis;
				$hclosed=($openthis?false:(isset($_SESSION['disclose_statuses']['sco'.$sco->id])?
					$_SESSION['disclose_statuses']['sco'.$sco->id]:$play));

				$strexpand = get_string('expcoll','scorm');
				$output.= '<span class="expcoll" onClick="disclose_it(\'scof'.$sco->id.'\',this.firstChild,\'src\');"><img width="13" height="18" id="scoimg'.$sco->id.'" src="'.$CONFIG['PATH'].'/modules/scorm/pix/disclosure_'.($hclosed?'closed':'open').'.gif" alt="'.$strexpand.'" title="'.$strexpand.'"/></span>'.$namelink."<ul id='scof{$sco->id}'".($hclosed?' style="display:none"':'').">".$innerlist.'</ul>';$samelevel=false;
			} else {$pop=true;}
		}
		if ($samelevel) {$output.= '<img width="9" height="9" src="'.$CONFIG['PATH'].'/modules/scorm/pix/spacer.gif" />'.$namelink;}
		
		$output.='</li>';
		
		if ($sco->id == $result->id) {
			$openparent=true;
		
			if ($nextsco !== false) {$result->next = $nextsco->id;}

			$result->showprev = $sco->previous;
			$result->shownext = $sco->next;
			$result->title=$sco->title;

			if ($lastid) {$result->prev = $lastid;}
		}

		if ($sco->launch) {$lastid=$sco->id;}
		$sco=$nextsco;
	}
	return $openparent;
}


function get_sco_namelink(&$sco,&$result,$play) {
	global $CONFIG;
	
	if ($sco->launch) {
		$score = '';
		if (empty($result->id) && ($mode != 'normal')) {$result->id = $sco->id;}
	
		$linktext="<a href='javascript:playSCO(".$sco->id.");'>";
		if ($usertrack=scorm_get_tracks($sco->id,$_SESSION['current_user_key'])) {
		
			if ($usertrack->status == '') {$usertrack->status = 'notattempted';}
			$strstatus = get_string($usertrack->status,'scorm');
	
			$namelink= $linktext.'<img width="17" height="16" src="'.$CONFIG['PATH'].'/modules/scorm/pix/'.$usertrack->status.'.gif" alt="'.$strstatus.'" title="'.$strstatus.'" />';
			if (($usertrack->status == 'notattempted') || ($usertrack->status == 'incomplete') || ($usertrack->status == 'browsed')) {
				$result->incomplete = true;
				if ($play && empty($result->id)) {$result->id = $sco->id;}
			}
			if ($usertrack->score_raw != '') {
				$score = '('.get_string('score','scorm').':&nbsp;'.$usertrack->score_raw.')';
			}
		} else {
			if ($play && empty($result->id)) {$result->id = $sco->id;}
			
			if ($sco->scormtype == 'sco') {
				$namelink= $linktext.'<img width="17" height="16" src="'.$CONFIG['PATH'].'/modules/scorm/pix/notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
			} else {
				$namelink= '<img width="5" height="16" src="'.$CONFIG['PATH'].'/modules/scorm/pix/asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />'.$linktext;
			}
		}
		$bbold=($play && $sco->id == $result->id);
		$namelink.= ($bbold?'<strong>':'')."$sco->title".($bbold?'</strong>':'').
			"</a> ".($bbold?'<strong>':'')."$score".($bbold?'</strong>':'')."\n";
	} else {$namelink= "&nbsp;$sco->title\n";}
	return $namelink;
}


function find_a_sco() {
	global $CONN,$CONFIG,$module_key,$mode;
    //
    // Search for first incomplete sco
    //
    $CONN->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes_track WHERE userid={$_SESSION['current_user_key']} AND module_key=".$module_key." AND (element='cmi.core.lesson_status' OR element='cmi.completion_status') ORDER BY scoid ASC");
	while (($rs && !$rs->EOF) && (($rs->fields['value']== "completed") || ($rs->fields['value'] == "passed") || ($rs->fields['value'] == "failed")) && ($mode == "normal")) {
		$rs->MoveNext();
	}
	if ($rs && !$rs->EOF) {
		$sco_track=(object)$rs->fields; 
		$rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE id={$sco_track->scoid}");
		if ($rs && !$rs->EOF) {return (object)$rs->fields;}
	}

    //
    // If no sco was found get the first of SCORM package
    //
    $rs = $CONN->Execute("SELECT * FROM {$CONFIG['DB_PREFIX']}scorm_scoes WHERE module_key=$module_key AND launch<>'' ORDER BY id ASC LIMIT 1");
 	if ($rs && !$rs->EOF) {
       	return (object)$rs->fields;
 	}
}
	/**
	* Delete a category
	*
	* @param  int $total score of quiz
	* @param  date $time_finished time quiz finished
	* @return true 
	* 
	*/
	function update_gradebook($total_score,$time_finished, $user_key, $module_key) {
		
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT item_key FROM {$CONFIG['DB_PREFIX']}gradebook_items WHERE content_module_key='$module_key'");

		if (!$rs->EOF) {
		
			while (!$rs->EOF) {
			
				$gradebook_item_key = $rs->fields[0];
				$rs->MoveNext();
				
			}
			$rs->Close();
			
			//see if there is already a gradebook entry for this user
			
			$rs = $CONN->Execute("SELECT grade_key FROM {$CONFIG['DB_PREFIX']}gradebook_item_user_links WHERE item_key='$gradebook_item_key' AND user_key='$user_key'");

	
			if (!$rs->EOF) {
			
				while (!$rs->EOF) {
				
					$grade_key = $rs->fields[0];
					$rs->MoveNext();
					
				} 
				$rs->Close();
				

				$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}gradebook_item_user_links SET grade_key='$total_score', date_modified=$time_finished WHERE item_key='$gradebook_item_key' AND user_key='$user_key'");
							
			
			} else {
			
				//no existing entry so add a new one
			
				$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}gradebook_item_user_links(item_key, user_key, date_added, grade_key) VALUES ('$gradebook_item_key','$user_key', $time_finished, '$total_score')");
				
				echo $CONN->ErrorMsg();
				
			}
			
		}
		return true;
}//end updategradeBook
?>
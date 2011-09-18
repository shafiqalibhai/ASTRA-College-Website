<?php
/**
* Nologin homepage
*
* Displays an Interact site homepage, if user is not logged in and site 
* is not restricted
*
*/

/**
* Include main config file 
*/
require_once('local/config.inc.php');



require_once('includes/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(
	'header' => 'header.ihtml',
	'body'   => 'Nologin.ihtml',
	'footer' => 'footer.ihtml'
));
$page_details=get_page_details($space_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('TOP_BREADCRUMBS','');
$t->set_var('PAGE_TITLE',$CONFIG['SERVER_NAME'].' - '.$general_strings['home']);
$t->set_var('SPACE_TITLE',$CONFIG['SERVER_NAME'].' - '.$general_strings['home']);

top_level_navigation();
$sql = "select {$CONFIG['DB_PREFIX']}spaces.space_key,short_name,name, description,{$CONFIG['DB_PREFIX']}spaces.sort_order, combine_names FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}SpaceChildlinks  WHERE {$CONFIG['DB_PREFIX']}SpaceChildlinks.space_key={$CONFIG['DB_PREFIX']}spaces.space_key AND visibility_key='1' AND {$CONFIG['DB_PREFIX']}SpaceChildlinks.parent_key='$space_key' ORDER BY sort_order, name";

$rs = $CONN->Execute($sql);

if ($rs->EOF && $_SESSION['userlevel_key']!='1') {

	$t->set_block('body', 'SubSpaceBlock', 'SubSBlock');
	$t->set_var('SubSBlock','');	

} else {

	$navlinks='';
	$spaces = '<ul>';
	while (!$rs->EOF) {

		$space_key = $rs->fields[0];
		$short_name = $rs->fields[1];
		$name = $rs->fields[2];
		$description = $rs->fields[3];
		$sort_order = $rs->fields[4];
		$combine_name = $rs->fields[5];		

		if ($short_name!='' && $combine_name==1) {
		
			$full_name=$rs->fields[1].' - '.$rs->fields[2];
	
		} else {
		
			$full_name=$rs->fields[2];		
	
		}
	
	
		if ($sort_order>0) {
	
			$spaces .= "<li><a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\" title=\"$description\">$full_name</a> ";
		

	
		} else {
	
			$spaces .= "<li><a href=\"{$CONFIG['PATH']}/spaces/space.php?space_key=$space_key\" title=\"$description\">$full_name</a> ";
		
			
	
		}
		//getChildSpaces($space_key, $spaces);
		//$spaces .= '<br /><br />';
		$rs->MoveNext();

	}
	
}


$spaces.='</ul>';
	
$t->set_var('OTHER_SPACES',$spaces);
$t->set_var('WELCOME_STRING',$general_strings['no_login']);
$t->set_var('SEARCH_STRING',$general_strings['search']);
$t->set_var('BROWSE_STRING',sprintf($general_strings['category_heading'],$general_strings['space_plural']));
$t->set_var('ENQUIRIES_STRING',sprintf($general_strings['enquiries'],$CONFIG['ERROR_EMAIL']));
$t->parse('CONTENTS', 'header', true); 
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();
exit;
?>
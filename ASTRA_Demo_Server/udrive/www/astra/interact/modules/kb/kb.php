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
* KnowledgeBase homepage
*
* Displays a knowledgebase homepage. 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: kb.php,v 1.30 2007/07/16 22:27:14 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/kb_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

if ($_SERVER['REQUEST_METHOD']=='GET') {

	$module_key	= $_GET['module_key'];
	$group_key	= $_GET['group_key'];
	$message	= $_GET['message'];
	
} else {

	$module_key	= $_POST['module_key'];
	$group_key	= $_POST['group_key'];
	
}
if (!empty($_POST['show_new'])) {
	$show_new = $_POST['show_new'];
	$last_use_seconds = $_POST['show_new']*86400;
	$last_use = $CONN->DBDate(date('Y-m-d H:i:s',time()-$last_use_seconds));
} else if ($_SESSION['last_use']>0) {
	$last_use = $CONN->DBDate($_SESSION['last_use']);
} else {
	//if they have not logged in before set their last login to today
	$last_use = $CONN->DBDate(date('Y-m-d H:i:s'));
}

$space_key 	= get_space_key();
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,false);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

$objKb = new InteractKB($space_key, $module_key, $group_key, $is_admin, $kb_strings);
$kb_data = $objKb->getKbData($module_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 	=> 'header.ihtml',
	'navigation' 	=> 'navigation.ihtml',
	'body'	   		=> 'kb/kb.ihtml',
	'footer'	 	=> 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
$t->set_var('LINK_KEY',$link_key);
foreach(array('whatsNew') as $value) {
	if (in_array($value.'=0',$disclosures)){
		$t->set_var(strtoupper($value).'_CONTENT_DEFAULTS','style="display:none"');
		$t->set_var(strtoupper($value).'_DEFAULT_STYLE','Closed');
	} else {
		$t->set_var(strtoupper($value).'_DEFAULT_STYLE','Open');		
	}
}
$show_new_menu = $objHtml->arrayToMenu(array('0' => $general_strings['since_last_login'], '1' => $general_strings['today'], '3' => sprintf($general_strings['for_last_days'],3), '7' => sprintf($general_strings['for_last_days'],7), '30' => sprintf($general_strings['for_last_days'],30)),'show_new',$show_new,'','',true,'class="formTxtInput small" onchange="this.form.submit();"');
$t->set_var('SHOW_NEW_MENU',$show_new_menu);
$t->set_var('SHOW_NEW_ITEMS',$general_strings['show_new']);

if ($is_admin==true) {
	
	$admin_string = sprintf($kb_strings['kb_admin'], $page_details['module_name']);
	$admin_links = get_admin_tool('admin.php?space_key='.$space_key.'&module_key='.$module_key.'&group_key='.$group_key,true,$admin_string,'spanner');
	
}

if (isset($_SESSION['current_user_key']) && ($is_admin==true || $kb_data['access_level_key']==2)) {

	$admin_links .= get_admin_tool("{$CONFIG['PATH']}/modules/kb/entryinput.php?space_key=$space_key&module_key=$module_key",false,$kb_strings['add_entry'],'plus');
	
}

$t->set_var('CURRENT_MODULE_ADMIN_LINKS',$admin_links);
$t->set_var('MODULE_KEY',$module_key);
$t->set_var('ADD_TEMPLATES_STRING',$kb_strings['add_templates']		);
$t->set_var('ADD_CATEGORIES_STRING',$general_strings['add_categories']	);
$t->set_var('ADD_ENTRY_STRING',$kb_strings['add_entry']);

if ($is_admin!=true) {

	$t->set_block('body', 'KBAdminlinksBlock', 'KBABlock');
	$t->set_var('KBABlock','');


}
$t->parse('CONTENTS', 'header', true); 
get_navigation();

//now get category links


$rs = $CONN->Execute("SELECT category_key, name FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE parent_key='0' AND module_key='$module_key' ORDER By name");

if ($rs->EOF) {

	$t->set_block('body', 'SubCategoryBlock', 'SCTBlock');
	$t->set_var('SCTBlock','');
	
} else {

	$t->set_block('body', 'CategoryLinkBlock', 'CLSBlock');
	$t->set_var('CATEGORIES_STRING',$general_strings['categories']);
	$n=0;
	
	while (!$rs->EOF) {
	
		$t->set_var('CATEGORY_KEY',$rs->fields[0]);
		
		if ($n>0){
		
			$t->set_var('SPACER',' - ');
			
		} else {
		
			$t->set_var('SPACER','');
		
		}
				
		$entry_count = $objKb->getEntryCount($module_key, $rs->fields[0]);
			 
		 
		$t->set_var('CATEGORY_NAME',$rs->fields[1]);
		$t->set_var('ENTRY_COUNT',$entry_count);
		
		$t->parse('CLSBlock', 'CategoryLinkBlock', true);
		$n++;
		$rs->MoveNext();
	
	}

}

//now get any entries without a category
$t->set_block('body', 'EntryLinksBlock', 'ELSBlock');
$t->set_block('body', 'TemplateTypeBlock', 'TTBlock');
$t->set_block('body', 'NewEntryBlock', 'NewEntBlock');
$t->set_block('body', 'TopEntriesBlock', 'TopEntries');
$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_entries.entry_key, {$CONFIG['DB_PREFIX']}kb_entries.template_key, {$CONFIG['DB_PREFIX']}kb_templates.name, {$CONFIG['DB_PREFIX']}kb_entries.added_by_key, {$CONFIG['DB_PREFIX']}kb_entries.status_key FROM {$CONFIG['DB_PREFIX']}kb_entries LEFT JOIN {$CONFIG['DB_PREFIX']}kb_entry_category_links ON {$CONFIG['DB_PREFIX']}kb_entries.entry_key={$CONFIG['DB_PREFIX']}kb_entry_category_links.entry_key, {$CONFIG['DB_PREFIX']}kb_templates WHERE  {$CONFIG['DB_PREFIX']}kb_entries.template_key={$CONFIG['DB_PREFIX']}kb_templates.template_key AND {$CONFIG['DB_PREFIX']}kb_entries.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}kb_entry_category_links.entry_key IS NULL ORDER BY {$CONFIG['DB_PREFIX']}kb_templates.name");

if ($rs->EOF) {

	$t->set_block('body', 'EntriesBlock', 'NoEntries');
	$t->set_var('NoEntries','');
	
} else {

	$n=0;
	$field_array=array();
	$entries_array=array();
	$template_type='';
	$template_count = $objKb->countTemplates($module_key);
	
	while (!$rs->EOF) {
	
		$t->set_var('ELSBlock','');
		
		if ($template_type!='' && $template_type!=$rs->fields[2]) {
		
			natcasesort($field_array);
			$t->set_var('TEMPLATE_NAME',$template_type);
			
			foreach ($field_array as $value) {
	
				$t->set_var('SUMMARY_FIELDS',$entries_array[$value]);
				$t->parse('ELSBlock', 'EntryLinksBlock', true);
		
			}
			
			$t->parse('TTBlock', 'TemplateTypeBlock', true);
			$field_array=array();
			$entries_array=array();
			$n=0;
		
		}
		
		$entry_key 		= $rs->fields[0];
		$template_key 	= $rs->fields[1];
		$template_type  = $rs->fields[2];
		$added_by_key  	= $rs->fields[3];
		$status_key  	= $rs->fields[4];
		
		if ($status_key==2 || (isset($_SESSION['current_user_key']) && $added_by_key==$_SESSION['current_user_key'])) {
				
			$summary_fields = $objKb->getsummary_fields($entry_key, $template_key, $category_key);
			$field_array[$n] = $summary_fields['name'];
$entries_array[$summary_fields['name']] = ($status_key==2)?$summary_fields['fields']:$summary_fields['fields'].' - '.$general_strings['draft'];
			$n++;
			
		}
		
		$rs->MoveNext();
	
	}
	
	natcasesort($field_array);
	
	if ($template_count>1) {
	
		$t->set_var('TEMPLATE_NAME',$template_type);
	}
	
	$t->set_var('ELSBlock','');			
	foreach ($field_array as $value) {
	
		$t->set_var('SUMMARY_FIELDS',$entries_array[$value]);
		$t->parse('ELSBlock', 'EntryLinksBlock', true);
		
	}

	$t->parse('TTBlock', 'TemplateTypeBlock', true);

}

//now get latest entries
$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_entries.entry_key FROM {$CONFIG['DB_PREFIX']}kb_entries  WHERE date_added>$last_use AND {$CONFIG['DB_PREFIX']}kb_entries.module_key='$module_key' AND {$CONFIG['DB_PREFIX']}kb_entries.status_key='2' ORDER BY {$CONFIG['DB_PREFIX']}kb_entries.date_added");

while(!$rs->EOF) {
	$entry_key = $rs->fields[0];
	$t->set_var('ENTRY_KEY',$entry_key);
	$t->set_var('ENTRY_TITLE',$CONN->GetOne("SELECT data,{$CONFIG['DB_PREFIX']}kb_fields.name FROM {$CONFIG['DB_PREFIX']}kb_entry_data, {$CONFIG['DB_PREFIX']}kb_fields WHERE {$CONFIG['DB_PREFIX']}kb_entry_data.field_key={$CONFIG['DB_PREFIX']}kb_fields.field_key AND {$CONFIG['DB_PREFIX']}kb_entry_data.entry_key='$entry_key' ORDER BY display_order"));
	$t->parse('NewEntBlock', 'NewEntryBlock', true);
	$rs->MoveNext();
}

$t->set_strings('body',  $kb_strings);
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

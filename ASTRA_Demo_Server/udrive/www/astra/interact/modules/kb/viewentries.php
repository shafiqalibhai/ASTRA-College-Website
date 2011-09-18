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
* KnowledgeBase view entries page
*
* Displays a knowledgebase entries and subcategories for given category. 
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: viewentries.php,v 1.21 2007/07/30 01:57:03 glendavies Exp $
* 
*/

/**
* Include main system config file 
*/
require_once('../../local/config.inc.php');
require_once($CONFIG['LANGUAGE_CPATH'].'/kb_strings.inc.php');
require_once('lib.inc.php');


//set the required variables

$module_key		= $_GET['module_key'];
$space_key		= $_GET['space_key'];
$category_key	= $_GET['category_key'];	
$link_key 	= get_link_key($module_key,$space_key);

//check we have the required variables
check_variables(true,true,true);

//check to see if user is logged in. 
$access_levels = authenticate();
$accesslevel_key = $access_levels['accesslevel_key'];
$group_access = $access_levels['groups'];
$group_accesslevel = $access_levels['group_accesslevel'][$group_key];
$is_admin = check_module_edit_rights($module_key);

$objKb = new InteractKB($space_key, $module_key, $group_key, $is_admin, $quiz_strings);
$kb_data = $objKb->getKbData($module_key);

require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	 	=> 'header.ihtml',
	'navigation' 	=> 'navigation.ihtml',
	'body'	   		=> 'kb/viewentries.ihtml',
	'footer'	 	=> 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
$page_details = get_page_details($space_key,$link_key);
set_common_template_vars($space_key,$module_key,$page_details, $message, $accesslevel_key, $group_accesslevel);
if (isset($_SESSION['current_user_key']) && ($is_admin==true || $kb_data['access_level_key']==2)) {

	$add_entry_link .= get_admin_tool("{$CONFIG['PATH']}/modules/kb/entryinput.php?space_key=$space_key&module_key=$module_key&category_key=$category_key",false,$kb_strings['add_entry'],'plus');
	$t->set_var('ADD_ENTRY_LINK',$add_entry_link);
	
}

$t->set_var('MODULE_KEY',$module_key);
$t->parse('CONTENTS', 'header', true); 
get_navigation();

//get category name

$category_data = $objKb->getCategoryData($category_key);

$t->set_var('CATEGORY_HEADING',$category_data['name']);


//now get subcategory links


$rs = $CONN->Execute("SELECT category_key, name FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE parent_key='$category_key' AND module_key='$module_key' ORDER By name");

if ($rs->EOF) {

	$t->set_block('body', 'SubCategoryBlock', 'SCBlock');
	$t->set_var('SCBlock','');
	
} else {
	
	$t->set_var('SUBCATEGORIES_STRING',$kb_strings['sub_categories']);
	$t->set_block('body', 'CategoryLinkBlock', 'CLSBlock');
	$t->set_var('SUBCATEGORIES_STRING',$general_strings['sub_categories']);
	while (!$rs->EOF) {

		$t->set_var('CATEGORY_KEY',$rs->fields[0]);
		$t->set_var('CATEGORY_NAME',$rs->fields[1]);
		$entry_count = $objKb->getEntryCount($module_key, $rs->fields[0]);
		$t->set_var('ENTRY_COUNT',$entry_count);
		$t->parse('CLSBlock', 'CategoryLinkBlock', true);
		$rs->MoveNext();
	
	}

}

//now get any entries in this category
$t->set_block('body', 'EntryLinksBlock', 'ELSBlock');
$t->set_block('body', 'TemplateTypeBlock', 'TTBlock');

$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_entry_category_links.entry_key, {$CONFIG['DB_PREFIX']}kb_templates.template_key, {$CONFIG['DB_PREFIX']}kb_templates.name, {$CONFIG['DB_PREFIX']}kb_entries.added_by_key, {$CONFIG['DB_PREFIX']}kb_entries.status_key FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links, {$CONFIG['DB_PREFIX']}kb_entries, {$CONFIG['DB_PREFIX']}kb_templates WHERE  {$CONFIG['DB_PREFIX']}kb_entries.template_key={$CONFIG['DB_PREFIX']}kb_templates.template_key AND {$CONFIG['DB_PREFIX']}kb_entry_category_links.entry_key={$CONFIG['DB_PREFIX']}kb_entries.entry_key AND category_key='$category_key' ORDER BY {$CONFIG['DB_PREFIX']}kb_templates.name");

if ($rs->EOF) {

	$t->set_var('ELSBlock',$kb_strings['no_entries']);
	$t->parse('TTBlock', 'TemplateTypeBlock', true);
	
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
$kb_trail = '';
$objKb->getTrail($category_key, true, $kb_trail);
$t->set_var('KB_TRAIL',$kb_trail);
$t->set_strings('body',  $kb_strings);
$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;	
?>

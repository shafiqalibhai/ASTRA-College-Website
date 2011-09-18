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
* InteractKB Class
*
* Contains the InteractKB class for all methods and datamembers related
* to adding, modifying and viewing Knowledgebase items
*
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: lib.inc.php,v 1.37 2007/01/25 03:11:30 glendavies Exp $
* 
*/

/**
* A class that contains methods for adding/modifying KnowledgeBase info
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for retrieving,displying and modifying KnowledgeBase data 
* 
* @package KnowledgeBase
* @author Glen Davies <glen.davies@cce.ac.nz>
*/

class InteractKB {

	/**
	* space key of current knowledgebase
	* @access private
	* @var int 
	*/
	var $_space_key = '';

	/**
	* module key of current knowledgebase
	* @access private
	* @var int 
	*/
	var $_module_key = '';
	
	/**
	* group key of current knowledgebase
	* @access private
	* @var int 	
	*/
	var $_group_key = '';
	
	/**
	* user key of current user
	* @access private
	* @var int 
	*/
	var $_user_key = '';
	
	/**
	* admin status of current user
	* @access private
	* @var true/false 	
	*/
	var $_is_admin = '';
	
	/**
	* array of language strings for quiz module
	* @access private
	* @var array 
	*/
	var $_kb_strings = '';
	
	/**
	* array of settings for current quiz
	* @access private
	* @var array 	
	*/
	var $_kb_data = '';
	
		
	
	/**
	* Constructor for InteractQuiz Class. Sets required variables
	*
	* @param  int $space_key  key of current space
	* @param  int $module_key  key of current module	
	* @param  int $group_key  key of current group
	* 
	*/
	
	function InteractKB($space_key,$module_key,$group_key,$is_admin,$kb_strings) {
	
		$this->_space_key	= $space_key;
		$this->_module_key  = $module_key;
		$this->_group_key	= $group_key;
		$this->_is_admin 	= $is_admin;
		$this->_kb_strings  = $kb_strings;				
		$this->_user_key 	= $_SESSION['current_user_key'];						
		
	} //end InteractKB
	
	
	/**
	* Constructor for InteractQuiz Class. Sets required variables
	*
	* @param  int $module_key  key of current module	
	* @param  return $kb_data  array of selected kb settings
	* 
	*/
	
	function getKbData($module_key) {
	
		global $CONN, $CONFIG;		
		
		$sql = "SELECT access_level_key, file_path FROM {$CONFIG['DB_PREFIX']}kb_settings WHERE module_key='$module_key'";

		$rs = $CONN->Execute($sql);
		while (!$rs->EOF) {
	 
			$kb_data['access_level_key']	= $rs->fields[0];
			$kb_data['file_path']			= $rs->fields[1];
			$rs->MoveNext();
		 
		}

		$this->_kb_data = $kb_data;
		return $kb_data;			
		
	} //end getKbData
	
	/**
	* Check form input for a inputing a category
	*
	* @param  int $category_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  string $category_name  name field of category		
	* @return array $errors array of any errors found
	* 
	*/
	
	function checkFormCategory($category_name, $category_key='', $parent_key='') {
	
		global $general_strings;
		$errors = array();
		
		//check that we have name
		if (!$category_name || $category_name=='') {
		
			$errors['name'] = $general_strings['no_name'];
		
		}
		
		//check that we have a question
		if ((isset($category_key) && $category_key!='') && ($category_key==$parent_key)) {
		
			$errors['parent'] = $this->_kb_strings['own_parent'];
		
		} 

		return $errors;
							
		
	} //end checkFormCategory
	
	/**
	* Add a new category
	*
	* @param  int $category_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  string $category_name  name field of category		
	* @return array $errors array of any errors found
	* 
	*/
	
	function addCategory($category_name, $parent_key='') {
	
		global $CONN, $CONFIG;

		$name	 = $category_name;
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_categories(name, parent_key,  module_key) VALUES ('$name', '$parent_key', '$this->_module_key')");
		echo  $CONN->ErrorMsg();
		$category_key = $CONN->Insert_ID();
			
		return $category_key;
						
	} //end addCategory
	
	/**
	* Get name and parent data for a given category
	*
	* @param  int $category_key  key of category
	* @param  string $category_name  name field of category		
	* @return array $category_data array of category data
	* 
	*/
	
	function getCategoryData($category_key) {
	
		global $CONN, $CONFIG;

		$rs = $CONN->Execute("SELECT name, parent_key FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE category_key='$category_key'");

		while (!$rs->EOF) {

			$category_data['name']	   = $rs->fields[0];
			$category_data['parent_key'] = $rs->fields[1];
			$rs->MoveNext();
			
		}
		$rs->Close();	
		return $category_data;
						
	} //end getCategoryData		

	/**
	* modify and existing category
	*
	* @param  int $category_key  key of category
	* @param  int $parent_key  key of parent category
	* @param  string $category_name  name field of category
	* @return true
	* 
	*/
	
	function modifyCategory($category_key, $category_name, $parent_key) {
	
		global $CONN, $CONFIG;

		$name	 = $category_name;
		$CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_categories SET name='$name', parent_key='$parent_key' WHERE category_key='$category_key'");

		return true;
						
	} //end modifyCategory
	
	/**
	* Delete a category
	*
	* @param  int $category_key  key of category to delete
	* @param  string $delete_option action to take with attached items
	* @param  int $move_to_category if moving attached items category to move them to		
	* @return true 
	* 
	*/
	
	function deleteCategory($category_key, $delete_option, $move_to_key) {
	
		global $CONN, $CONFIG;
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE category_key='$category_key'");
		 
		if ($delete_option=='delete' && !empty($category_key)) {
		
			$rs = $CONN->Execute("SELECT entry_key FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links WHERE category_key='$category_key'");
			
			while (!$rs->EOF) {
			
				$entry_key = $rs->fields[0];
				$this->deleteEntry($entry_key);
				$rs->MoveNext();
			
			}
			
			$rs->Close();
			
			//now delete any sub categories
			
			$rs = $CONN->Execute("SELECT category_key FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE parent_key='$category_key'");
			
			while (!$rs->EOF) {
			
				$child_key = $rs->fields[0];
				$this->deleteCategory($child_key, $delete_option, $move_to_key);
				$rs->MoveNext();
			
			}						
		
		} else if (!empty($category_key)){
		
			 $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_entry_category_links SET category_key='$move_to_key' WHERE category_key='$category_key'");
			 $CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_categories SET parent_key='$move_to_key' WHERE parent_key='$category_key'");
		
		}		
		
		return true ;
		
	} //end deleteCategories()			
	
	/**
	* Check form input for a inputing a template
	*
	* @param  int $template_key  key of template
	* @param  int $parent_key  key of parent template
	* @param  string $template_name  name field of template		
	* @return array $errors array of any errors found
	* 
	*/
	
	function checkFormTemplate($template_data) {
	
		global $general_strings;
		$errors = array();
		
		//check that we have name
		if (!$template_data['name'] || $template_data['name']=='') {
		
			$errors['name'] = $general_strings['no_name'];
		
		}
		

		return $errors;
							
		
	} //end checkFormTemplate
	
	/**
	* Add a new template
	*
	* @param  int $template_key  key of template
	* @param  string $template_name  name field of template		
	* @return array $errors array of any errors found
	* 
	*/
	
	function addTemplate($template_data) {
	
		global $CONN, $CONFIG;

		$name			= $template_data['name'];
		$description	= $template_data['description'];
		$summary_fields	= $template_data['summary_fields'];
		$date_added	 = $CONN->DBDate(date('Y-m-d H:i:s'));	
		
		if ($template_data['referer']=='admin') {
		
			$type_key=0;
		
		} else {
		
			$type_key = 1;
		
		}
		
		$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_templates(type_key, name, description, summary_fields, added_by_key, date_added) VALUES ('$type_key', '$name', '$description', '$summary_fields', '$this->_user_key', $date_added)");
		
		echo  $CONN->ErrorMsg();
		
		$template_key = $CONN->Insert_ID();
		return $template_key;
						
	} //end addTemplate
	

	/**
	* modify and existing template
	*
	* @param  int $template_data  data of template to modify
	* @return true
	* 
	*/
	
	function modifyTemplate($template_data) {
	
		global $CONN, $CONFIG;

		$name	 		= $template_data['name'];
		$description	= $template_data['description'];
		$summary_fields	= $template_data['summary_fields'];		
		$template_key	= $template_data['template_key'];

		if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_templates SET name='$name', description='$description', summary_fields='$summary_fields' WHERE template_key='$template_key'")==true) {
		
			return $this->_kb_strings['template_modified'];
		
		} else {
		
			$message = $this->_kb_strings['template_modify_error'].' - '.$CONN->ErrorMsg();
			return $message;
			
		}
						
	} //end modifyTemplate
	
	/**
	* Remove template from current Knowledge base
	*
	* @param  array $template_keys  keys of templates to remove
	* @param  int $module_key key of kb to remove templates from
	* @param  boolean $remove_entries if one then also remove entries attached to this template		
	* @return true 
	* 
	*/
	
	function removeTemplatesFromKb($template_keys, $module_key, $remove_entries=0) {
	
		global $CONN, $CONFIG;
		
		if (isset($template_keys) && is_array($template_keys)) {
		
			foreach ($template_keys as $value) {
			
				$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_module_template_links WHERE template_key='$value' AND module_key='$module_key'");

				if ($remove_entries==1) {
		
					$rs = $CONN->Execute("SELECT entry_key FROM {$CONFIG['DB_PREFIX']}kb_entries WHERE template_key='$value' AND module_key='$module_key'");
			
					while (!$rs->EOF) {
			
						$entry_key = $rs->fields[0];
						$this->deleteEntry($entry_key);
						$rs->MoveNext();
			
					}
					
					$rs->Close();
					
				}
			
			}			
		
		}
	
		return true ;
		
	} //end removeTemplatesFromKb()			
	
	/**
	* Delete a template from the system
	*
	* @param  int $template_key  key of template to remove
	* @param  boolean true if successful		
	* @return true 
	* 
	*/
	
	function deleteTemplate($template_key) {
	
		global $CONN, $CONFIG;
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_templates WHERE template_key='$template_key'");
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_module_template_links WHERE template_key='$template_key'");
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_fields WHERE template_key='$template_key'");		

		$rs = $CONN->Execute("SELECT entry_key FROM {$CONFIG['DB_PREFIX']}kb_entries WHERE template_key='$template_key'");
		
		while (!$rs->EOF) {
			
			$entry_key = $rs->fields[0];
			$this->deleteEntry($entry_key);
			$rs->MoveNext();
			
		}
					
		$rs->Close();
		return true ;
		
	} //end removeTemplatesFromKb()		
	
	/**
	* Get an array of available templates
	*
	* @return array $template_array an array of available templates 
	* 
	*/
	
	function getTemplateArray($space_key='', $scope='all', $module_key='', $referer='', $level_key='3') {
	
		global $CONN, $CONFIG;
		
		if (isset($space_key) && $space_key!='') {
		
			switch ($scope) {
			
				case all:
				
					if ($referer=='admin' && $level_key==1) {
					
						$sql = "SELECT template_key, name FROM {$CONFIG['DB_PREFIX']}kb_templates WHERE (type_key='0') ORDER BY name";
						
					} else {
					
						$sql = "SELECT template_key, name FROM {$CONFIG['DB_PREFIX']}kb_templates WHERE (added_by_key='$this->_user_key') AND type_key!='0' ORDER BY name";
					
					}
					
				break;
				
				case not_selected:

	$sql = "SELECT {$CONFIG['DB_PREFIX']}kb_templates.template_key, name, {$CONFIG['DB_PREFIX']}kb_module_template_links.module_key FROM  {$CONFIG['DB_PREFIX']}kb_templates LEFT JOIN {$CONFIG['DB_PREFIX']}kb_module_template_links ON {$CONFIG['DB_PREFIX']}kb_templates.template_key={$CONFIG['DB_PREFIX']}kb_module_template_links.template_key WHERE ((added_by_key='$this->_user_key') OR (type_key='0')) AND (module_key!='$module_key' OR module_key IS NULL) ORDER BY name";	
							
				break;
				
				case module_only:
				
					$sql = "SELECT {$CONFIG['DB_PREFIX']}kb_templates.template_key, name FROM {$CONFIG['DB_PREFIX']}kb_module_template_links, {$CONFIG['DB_PREFIX']}kb_templates WHERE {$CONFIG['DB_PREFIX']}kb_templates.template_key={$CONFIG['DB_PREFIX']}kb_module_template_links.template_key AND (module_key='$this->_module_key') ORDER BY name";
					
				break;
								
			} 
			
			
		} else {
			
			$sql = "SELECT template_key, name FROM {$CONFIG['DB_PREFIX']}kb_templates WHERE type_key='0' ORDER BY name";
			
		}
					
		$rs = $CONN->Execute($sql);
		echo $CONN->ErrorMsg();
		$template_array = array();
		while (!$rs->EOF) {
			
			$template_array[$rs->fields[0]] = $rs->fields[1];
			$rs->MoveNext();
			
		}
			
		$rs->Close();
			
		
		return $template_array;

		
	} //end getTemplateArray()		
	
	/**
	* Add an selected templates to the current module
	*
	* @param array $template_keys array of template keys to add to current module
	* @param int $module_key  key of module to add templates to	
	* @return true if successful 
	* 
	*/
	
	function addTemplatesToKb($template_keys, $module_key) {
	
		global $CONN, $CONFIG;

		if (is_array($template_keys)) {
		
			foreach ($template_keys as $value) {
			
				$rs = $CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_module_template_links(module_key, template_key) VALUES ('$module_key','$value')");
				
			}
			
			return true;
			
		} else {
		
			return false;
		
		}
		
	} //end addTemplatesToKb()		
	
	/**
	* Get template data
	*
	* @param int $template_key key of template to get data for
	* @return array $template_data array of data for selected template 
	* 
	*/
	
	function getTemplateData($template_key) {
	
		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT name, description, summary_fields FROM {$CONFIG['DB_PREFIX']}kb_templates WHERE template_key='$template_key'");

		if ($rs->EOF) {
		
			return false;
		
		} else {
		
			$template_data=array();
			
			while (!$rs->EOF) {
			
				$template_data['name'] = $rs->fields[0];
				$template_data['description'] = $rs->fields[1];
				$template_data['summary_fields'] = $rs->fields[2];				
				$rs->MoveNext();
			
			}
			
			return $template_data;
		
		}
		
				
	} //end getTemplatesData()		
	
	/**
	* Get template fields
	*
	* @param int $template_key key of template to get fields for
	* @return array $template_fields array of fields for selected template 
	* 
	*/
	
	function getTemplateFields($template_key) {
	
		global $CONN, $CONFIG;
	
		$rs = $CONN->Execute("SELECT field_key, type_key FROM {$CONFIG['DB_PREFIX']}kb_fields WHERE template_key='$template_key' ORDER By display_order");

		if ($rs->EOF) {
		
			return false;
		
		} else {
		
			$field_data=array();
			
			while (!$rs->EOF) {
			
				$field_data[$rs->fields[0]] = $rs->fields[1];
				$rs->MoveNext();
			
			}
			
			return $field_data;
		
		}
		
				
	} //end getTemplateFields()						
	
	/**
	* Add a new field
	*
	* @param  array $field_data  array of field data
	* @return string $message success or failure message 
	* 
	*/
	
	function addField($field_data) {
	
		global $CONN, $CONFIG;

		$name			= $field_data['name'];
		$description	= $field_data['description'];
		$display_order	= $field_data['display_order'];
		$template_key	= $field_data['template_key'];
		$type_key		= isset($field_data['type_key'])? $field_data['type_key']: '1';
		$lines			= isset($field_data['lines'])? $field_data['lines']: '1';		
		$date_added	 = $CONN->DBDate(date('Y-m-d H:i:s'));	
			
		if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_fields(template_key, name, description, type_key, display_order, number_of_lines, added_by_key, date_added) VALUES ('$template_key', '$name', '$description', '$type_key', '$display_order','$lines', '$this->_user_key', $date_added)")==true) {
		
			return $this->_kb_strings['field_added'];				
		
		} else {
		
			return $this->_kb_strings['field_add_error'].' - '.$CONN->ErrorMsg();		
		
		}

						
	} //end addField	
	
	/**
	* Modify fields
	*
	* @param  array $field_data  array of field data
	* @return string $message success or failure message 
	* 
	*/
	
	function modifyFields($field_data) {
	
		global $CONN, $CONFIG;

		if (isset($field_data['field_keys']) && is_array($field_data['field_keys'])) {
		
			foreach ($field_data['field_keys'] as $field_key) {

				if (isset($field_data[$field_key.'_remove']) && $field_data[$field_key.'_remove']==1) {
				
					$this->deleteField($field_key);
				
				} else {
				
					$name			= $field_data[$field_key.'_name'];
					$description	= $field_data[$field_key.'_description'];
					$display_order	= $field_data[$field_key.'_display_order'];
					$lines			= isset($field_data[$field_key.'_lines'])? $field_data[$field_key.'_lines']: '1';		
					$date_modified	= $CONN->DBDate(date('Y-m-d H:i:s'));	
			
					if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_fields SET name='$name', description='$description', display_order='$display_order',  number_of_lines='$lines', modified_by_key='$this->_user_key', date_modified=$date_modified WHERE field_key='$field_key'")==false) {
		
						return $this->_kb_strings['field_modify_error'].' - '.$CONN->ErrorMsg();				
		
					}
				
				} 
			
			}
				
			return $this->_kb_strings['fields_modifed'];
		
		}
						
	} //end modifyFields		
	
	/**
	* Generate template input form
	*
	* @param  int $template_key key of template to generate form for
	* @param  obj $t template object
	* @return true 
	* 
	*/
	
	function getTemplateInputForm($template_key, &$t, $entry_key='', $action='') {
	
		global $CONN, $CONFIG;

		$rs = $CONN->Execute("SELECT field_key, type_key, name, number_of_lines, description FROM {$CONFIG['DB_PREFIX']}kb_fields WHERE template_key='$template_key' ORDER BY display_order");
		
		if ($rs->EOF) {
		
			$t->set_block('body', 'TemplateFormBlock', 'TFBlock');
			$t->set_var('TFBlock',$this->_kb_strings['no_fields']);	
		
		} else {
		if (!class_exists('InteractHtml')) {

	require_once('../../includes/lib/html.inc.php');
	
}

			$html = new InteractHtml();
			while (!$rs->EOF) {
			

				$t->set_var('HTEBlock','');
				$t->set_var('FIELD_DESCRIPTION','');
				
				if ($action=='modify' && $entry_key!='') {
				
					$field_data = $this->getFieldData($rs->fields['0'], $entry_key);
					
				} else {
				
					$field_data = '';
					
				}
				
				$t->set_var('FIELD_NAME',$rs->fields['2']);
				$t->set_var('FIELD_KEY',$rs->fields['0']);
				$field_description = nl2br($rs->fields[4]);
				$field_description = preg_replace ('/\r/', '', $field_description);
				$field_description = preg_replace ('/\n/', '', $field_description);
				$field_description = preg_replace ('/;/', '\;', $field_description);
				$field_description = preg_replace ('/"/', '\'', $field_description);
				
				$field_description = addslashes($field_description);
				$t->set_var('FIELD_DESCRIPTION',$field_description);
				
				if ($rs->fields['3']<=1) {
				
					$t->set_var('HTEBlock','');
					
				} else {
				
					$t->parse('HTEBlock', 'HtmlEditorBlock', true);
					
				}
				
				switch($rs->fields[1]) {
				
					case 1:
					
						if ($rs->fields[3]<2) { 
							
							$t->set_var('FIELD_INPUT','<input type="text" id="'.$rs->fields['0'].'" name="'.$rs->fields['0'].'" value="'.$field_data.'" size="40"/>');
							
						} else {
						
							$t->set_var('FIELD_INPUT','<textarea id="'.$rs->fields['0'].'" name="'.$rs->fields['0'].'" cols="58" rows="'.$rs->fields['3'].'">'.$field_data.'</textarea>');
$html->setTextEditor($t, 0, $rs->fields['0']);
						
						}
					
					break;
					
					case 2:
					
						$t->set_var('FIELD_INPUT','<input type="text" id="'.$rs->fields['0'].'" name="'.$rs->fields['0'].'" value="'.$field_data.'" />');
											
					break;
					
					case 3:
					
						$t->set_var('FIELD_INPUT','<input type="file" id="'.$rs->fields['0'].'" name="'.$rs->fields['0'].'" />');
											
					break;
				
				}
				
				$t->parse('TFBlock', 'TemplateFieldBlock', true);			
				$rs->MoveNext();
			
			}
			
		}

						
	} //end getTemplateInputForm()		
	
	/**
	* Get data for given field and entry
	*
	* @param  int $field_key  key of field to get data for
	* @param  int $entry_key  key of entry to get data for	
	* @return string $data data for given field and entry 
	* 
	*/
	
	function getFieldData($field_key, $entry_key) {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT data FROM {$CONFIG['DB_PREFIX']}kb_entry_data WHERE entry_key='$entry_key' AND field_key='$field_key'");
		
		while (!$rs->EOF) {
		
			$data = $rs->fields[0];
			$rs->MoveNext();
		
		}
		
		return $data;
		
	} //end getFieldData
	
	/**
	* Get array of categories for given entry
	*
	* @param  int $entry_key  key of entry to get data for	
	* @return array $category_keys array of categories entry is attached to 
	* 
	*/
	
	function getEntryCategories($entry_key) {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT category_key FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links WHERE entry_key='$entry_key'");
		
		$category_keys = array();
		$n=0;
		while (!$rs->EOF) {
		
			$category_keys[$n] = $rs->fields[0];
			$n++;
			$rs->MoveNext();
		
		}
		
		return $category_keys;
		
	} //end getEntryCategories

	/**
	* Add a new entry
	*
	* @param  array $entry_data  array of entry data
	* @return string $message success or failure message 
	* 
	*/
	
	function addEntry($entry_data) {
	
		global $CONN, $CONFIG, $general_strings;

		$name			= $field_data['name'];
		$date_added	 = $CONN->DBDate(date('Y-m-d H:i:s'));	
		$template_key	= $entry_data['template_key'];
		$module_key		= $entry_data['module_key'];
		if (!isset($entry_data['status_key']) || $entry_data['status_key']==0) {
		
			$status_key	= 2;
			
		} else {
		
			$status_key	= $entry_data['status_key'];
		
		}
		
		if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_entries(template_key, module_key,  added_by_key, date_added, status_key) VALUES ('$template_key', '$module_key','$this->_user_key', $date_added, '$status_key')")===false) {

			return $general_strings['error'].' - '.$CONN->ErrorMsg();				
		
		}
		 
		$entry_key = $CONN->Insert_ID();
		
		//now get all the field details for this template
		
		$template_fields = $this->getTemplateFields($template_key);
		
		foreach ($template_fields as $key => $value) {
		
			//see if it is a file field
			
			if ($value==3 && isset($_FILES[$key]['name']) && $_FILES[$key]['name']!='') {
			
				$data = $this->addFile($key, $entry_key, $module_key);
			
			} else {
			
				$data = $entry_data[$key];
			
			}
			
			if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_entry_data(entry_key, field_key, data) VALUES ('$entry_key', '$key','$data')")===false) {
		
				return $general_strings['error'].' - '.$CONN->ErrorMsg();				
		
			}		
				
		}
		
		//now add any category links
		
		if (is_array($entry_data['category_keys'])) {
		
			$this->addEntryCategorylinks($entry_key, $entry_data['category_keys']);	
		
		}
		
		return $this->_kb_strings['entry_add_success'];
						
	} //end addEntry		
	
	/**
	* Modify an existing entry
	*
	* @param  array $entry_data  array of entry data
	* @return string $message success or failure message 
	* 
	*/
	
	function modifyEntry($entry_data) {
	
		global $CONN, $CONFIG, $general_strings;

		$kb_data 	= $this->getKbData($this->_module_key);
		$file_path 	= $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$kb_data['file_path'].'/';
		$date_modified  = $CONN->DBDate(date('Y-m-d H:i:s'));	
		$template_key	= $entry_data['template_key'];
		$module_key		= $entry_data['module_key'];
		$entry_key 		= $entry_data['entry_key'];
		
		if (!isset($entry_data['status_key']) || $entry_data['status_key']==0) {
		
			$status_key	= 2;
			
		} else {
		
			$status_key	= $entry_data['status_key'];
		
		}
		
		if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_entries SET modified_by_key='$this->_user_key', date_modified=$date_modified, status_key='$status_key' WHERE entry_key='$entry_key'")===false) {

			return $general_strings['error'].' - '.$CONN->ErrorMsg();				
		
		}
		 
		//now get all the field details for this template
		
		$template_fields = $this->getTemplateFields($template_key);
		
		foreach ($template_fields as $key => $value) {
		
			//see if it is a file field
			
			if ($value==3) {
			
				if (isset($_FILES[$key]['name']) && $_FILES[$key]['name']!='') {
					
					$rs = $CONN->Execute("SELECT data FROM {$CONFIG['DB_PREFIX']}kb_entry_data WHERE  {$CONFIG['DB_PREFIX']}kb_entry_data.field_key='$field_key' AND {$CONFIG['DB_PREFIX']}kb_entry_data.entry_key='$entry_key'");
		
					while(!$rs->EOF) {
		
						$full_file_path = $file_path.$rs->fields[2];

						if (is_file($full_file_path)) {
				
							unlink($full_file_path);
				
						}
			
					
						$rs->MoveNext();
		
					}
				
					$data = $this->addFile($key, $entry_key, $module_key);
					$update = true;
				} else {

					$update = false;
				}
				
			
			} else {
			
				$data = $entry_data[$key];
				$update = true;
			
			}
			
			//see if field exists (may have been added to template since entry added
			if ($update==true) {
				
				$rs = $CONN->Execute("SELECT entry_key FROM {$CONFIG['DB_PREFIX']}kb_entry_data WHERE entry_key='$entry_key' AND field_key='$key'");
			
				if (!$rs->EOF) {
			
					if ($CONN->Execute("UPDATE {$CONFIG['DB_PREFIX']}kb_entry_data SET data='$data' WHERE entry_key='$entry_key' AND field_key='$key'")===false) {
		
					return $general_strings['error'].' - '.$CONN->ErrorMsg();				
		
					}
				
				} else {
			
					if ($CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_entry_data(entry_key, field_key, data) VALUES ('$entry_key','$key', '$data')")===false) {
		
						return $general_strings['error'].' - '.$CONN->ErrorMsg();	
			
			
					}
				
				}		
				
			}
			
		}
		
		//delete existing category links and  add any new category links
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links WHERE entry_key='$entry_key'");
		if (is_array($entry_data['category_keys'])) {
		
			$this->addEntryCategorylinks($entry_key, $entry_data['category_keys']);	
		
		}
		
		return $this->_kb_strings['entry_modify_success'];
						
	} //end modifyEntry
	
	/**
	* Get data for an entry
	*
	* @param  int $entry_key  key of entry o get data for
	* @return array $entry_data array of entry data 
	* 
	*/
	
	function getEntryData($entry_key) {
	
		global $CONN, $CONFIG;
		
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_entries.template_key, {$CONFIG['DB_PREFIX']}kb_entries.module_key, {$CONFIG['DB_PREFIX']}kb_entries.added_by_key, {$CONFIG['DB_PREFIX']}kb_entries.modified_by_key, {$CONFIG['DB_PREFIX']}kb_entries.date_added, {$CONFIG['DB_PREFIX']}kb_entries.date_modified, {$CONFIG['DB_PREFIX']}kb_templates.name, {$CONFIG['DB_PREFIX']}kb_entries.status_key FROM {$CONFIG['DB_PREFIX']}kb_entries, {$CONFIG['DB_PREFIX']}kb_templates WHERE {$CONFIG['DB_PREFIX']}kb_entries.template_key={$CONFIG['DB_PREFIX']}kb_templates.template_key AND entry_key='$entry_key'");
		
		if ($rs->EOF) {
		
			return false;
		
		} else {
		
			$entry_data = array();
			
			while (!$rs->EOF) {
			
				$entry_data['template_key'] = $rs->fields[0];
				$entry_data['module_key'] = $rs->fields[1];
				$entry_data['added_by_key'] = $rs->fields[2];
				$entry_data['modified_by_key'] = $rs->fields[3];
				$entry_data['date_added'] = $CONN->UnixTimestamp($rs->fields[4]);
				$entry_data['date_modified'] = $CONN->UnixTimestamp($rs->fields[5]);	
				$entry_data['template_name'] = $rs->fields[6];	
				$entry_data['status_key'] = $rs->fields[7];			
				$rs->MoveNext();
			
			}
			
			return $entry_data;
			
		}
	
	} //end getEntryData()
	/**
	* Add a file for an entry
	*
	* @param  int $field_key  key of file field
	* @return string $file_path path to file 
	* 
	*/
	
	function addFile($field_key, $entry_key, $module_key) {
	
		global $CONN, $CONFIG;

		$file_name	= $_FILES[$field_key]['name'];
		$tmp_name	= $_FILES[$field_key]['tmp_name'];
		$file_name=str_replace("cgi","html",$file_name);
		$file_name=str_replace("pl","html",$file_name);
		$file_name=str_replace("phtml","html",$file_name);
		$file_name=str_replace("shtml","html",$file_name);
		$file_name=str_replace("iphp","html",$file_name);

		if ($CONFIG['ALLOW_PHP']==0) {
		
			$file_name=str_replace("php","html",$file_name);
			
		}

		$file_name=ereg_replace("[^a-z0-9A-Z._]","",$file_name);
		
		//create a diretcory to store the file in
	  
		$kb_data = $this->getKbData($module_key);
		mt_srand ((float) microtime() * 1000000);
		$subdirectory = mt_rand(1,100);
		$subdirectory_path = $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$kb_data['file_path'].'/'.$subdirectory;

		if (!Is_Dir($subdirectory_path)) {

			if (!mkdir($subdirectory_path,0777)) {
		
				$message = 'There was an error adding your file';
				return $message;
						
			}
		
		} 
		
		$file_path = $subdirectory.'/'.$file_name;

  		if (!copy($tmp_name,$subdirectory_path.'/'.$file_name)) {

			$message = 'There was an error adding your file';
			return $message;
					
		} 
		
		return $file_path;
						
	} //end addFile			
	
	/**
	* Add category links for an entry
	*
	* @param  int $entry_key  key of entry to add links for
	* @param  array $category_keys  array of category keys to link to	
	* @return true
	* 
	*/
	
	function addEntryCategorylinks($entry_key, $category_keys) {
	
		global $CONN, $CONFIG;

		//first delete any existing entries
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links WHERE entry_key='$entry_key'");
		
		//now add new entries

		foreach ($category_keys as $value) {
		
			$CONN->Execute("INSERT INTO {$CONFIG['DB_PREFIX']}kb_entry_category_links(entry_key, category_key) VALUES ('$entry_key','$value')"); 
			echo $CONN->ErrorMsg();
		
		}			
		
		return true;
		
	} //end addEntryCategorylinks
	
	/**
	* Retrieve summary fields for given entry
	*
	* @param  int $entry_key  key of entry to add links for
	* @param  int $template_key  key of template for given entry	
	* @return string $summary_fields summary fields for given entry
	* 
	*/
	
	function getsummary_fields($entry_key, $template_key, $category_key) {
	
		global $CONN, $CONFIG, $general_strings;

		$template_data = $this->getTemplateData($template_key);
		
		$rs = $CONN->SelectLimit("SELECT data,{$CONFIG['DB_PREFIX']}kb_fields.name FROM {$CONFIG['DB_PREFIX']}kb_entry_data, {$CONFIG['DB_PREFIX']}kb_fields WHERE {$CONFIG['DB_PREFIX']}kb_entry_data.field_key={$CONFIG['DB_PREFIX']}kb_fields.field_key AND {$CONFIG['DB_PREFIX']}kb_entry_data.entry_key='$entry_key' ORDER BY display_order", $template_data['summary_fields']);
		
		$n=0;
		
		$fields_array=array();
		
		while (!$rs->EOF) {
								
			if ($n==0) {
			
				if ($rs->fields[0]=='') {
				
					$rs->fields[0] = 'blank entry';
				
				}
				$fields_array['name'] = $rs->fields[0];
				$fields_array['fields'] .= '<a href="entry.php?space_key='.$this->_space_key.'&module_key='.$this->_module_key.'&entry_key='.$entry_key.'&category_key='.$category_key.'">'.$rs->fields[0].'</a>';
			
			} else {
			
				$fields_array['fields'] .= '<div class="small"><strong>'.$rs->fields[1].'</strong>: '.$rs->fields[0].'</div>';
												
			}
			
			$n++;
			$rs->MoveNext();
			
		}
		
		//now count number of comments and add comments link
		$comment_count = $this->countcomments($entry_key);
		
		if ($comment_count>0) {
		
			$fields_array['fields'] .= "<br /><span class=\"small\"><a href=\"entry.php?space_key=$this->_space_key&module_key=$this->_module_key&entry_key=$entry_key#entry_key\">".$general_strings['comments'].'</a>('.$comment_count.')</span>';
			
		}
		
		return $fields_array;
				
	} //end getsummary_fields				
	
	/**
	* Generate a breadcrumb trail
	*
	* @param  int $category_key  key of current category
	* @param  int $template_key  key of template for given entry	
	* @return string $kb_trail linked breadcrumb trail
	* 
	*/
	
	function getTrail($category_key, $home=true, &$kb_trail) {
	
		global $CONN, $CONFIG;

		$rs = $CONN->Execute("SELECT name, parent_key FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE category_key='$category_key'");

		while (!$rs->EOF) {
		
			$name 		= $rs->fields[0];
			$parent_key	= $rs->fields[1];
			$rs->MoveNext();
					
		}
		
		if ($home==true) {
		
			$kb_trail = '<strong>'.$name.'</strong> &raquo; '.$kb_trail;
			
		
		} else {
		
			$kb_trail = '<a href="viewentries.php?space_key='.$this->_space_key.'&module_key='.$this->_module_key.'&entry_key='.$entry_key.'&category_key='.$category_key.'">'.$name.'</a> &raquo; '.$kb_trail;
				
		}

		if ($parent_key!=0) {
		
			$this->getTrail($parent_key, false, $kb_trail);
		
		} else {
		
			return true;
			
		}
				
	} //end getTrail					
	
	/**
	* Delete an entry
	*
	* @param  int $entry_key  key of entry to delete
	* @return true
	* 
	*/
	
	function deleteEntry($entry_key) {
	
		global $CONN, $CONFIG;
			
		$kb_data 	= $this->getKbData($this->_module_key);
		$file_path 	= $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$kb_data['file_path'].'/';		 
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_entries WHERE entry_key=$entry_key");
		$rs = $CONN->Execute("SELECT data_key, type_key, data FROM {$CONFIG['DB_PREFIX']}kb_entry_data, {$CONFIG['DB_PREFIX']}kb_fields WHERE {$CONFIG['DB_PREFIX']}kb_entry_data.field_key={$CONFIG['DB_PREFIX']}kb_fields.field_key AND entry_key=$entry_key");

		//see if entry has file attached, if so remove it
		
		while(!$rs->EOF) {
		
			if ($rs->fields[1]==3) {
			
				$full_file_path = $file_path.$rs->fields[2];

				if (is_file($full_file_path)) {
				
					unlink($full_file_path);
				
				}
			
			} 
					
			$rs->MoveNext();
		
		}
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_entry_data WHERE entry_key=$entry_key");
		
		//find any comments and remove postuserlinks
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE entry_key=$entry_key");
		while(!$rs->EOF) {
		
			$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}post_user_links WHERE post_key={$rs->fields[0]}");
			$rs->MoveNext();
		
		}
		
		$rs->Close();
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}posts WHERE entry_key=$entry_key AND module_key='$this->_module_key'");		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links WHERE entry_key=$entry_key");
		return true;
				
	} //end deleteEntry()					
	
	/**
	* Delete a field from a template
	*
	* @param  int $field_key  key of field to delete
	* @return true
	* 
	*/
	
	function deleteField($field_key) {
	
		global $CONN, $CONFIG;
			
		$kb_data 	= $this->getKbData($this->_module_key);
		$file_path 	= $CONFIG['MODULE_FILE_SAVE_PATH'].'/kb/'.$kb_data['file_path'].'/';		 
		$rs = $CONN->Execute("SELECT data_key, type_key, data FROM {$CONFIG['DB_PREFIX']}kb_entry_data, {$CONFIG['DB_PREFIX']}kb_fields WHERE {$CONFIG['DB_PREFIX']}kb_entry_data.field_key={$CONFIG['DB_PREFIX']}kb_fields.field_key AND {$CONFIG['DB_PREFIX']}kb_entry_data.field_key='$field_key'");

		//see if entry has file attached, if so remove it
		
		while(!$rs->EOF) {
		
			if ($rs->fields[1]==3) {
			
				$full_file_path = $file_path.$rs->fields[2];

				if (is_file($full_file_path)) {
				
					unlink($full_file_path);
				
				}
			
			} 
					
			$rs->MoveNext();
		
		}
		
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_entry_data WHERE field_key='$field_key'");
		$CONN->Execute("DELETE FROM {$CONFIG['DB_PREFIX']}kb_fields WHERE field_key='$field_key'");
		
		return true;
				
	} //end deleteField()
	
	/**
	* Count the number of comments for a given entry
	*
	* @param  int $entry_key  key of entry to count comments for
	* @return int $count number of comments
	* 
	*/
	
	function countcomments($entry_key) {
	
		global $CONN, $CONFIG;
			
		$rs = $CONN->Execute("SELECT post_key FROM {$CONFIG['DB_PREFIX']}posts WHERE entry_key='$entry_key'");
		$count = $rs->RecordCount();

		return $count;
				
	} //end countcomments()	
	
	/**
	* Count the number of templates linked to a knowledgebase
	*
	* @param  int $module_key  key of module to count templates for
	* @return int $count number of templates
	* 
	*/
	
	function countTemplates($module_key) {
	
		global $CONN, $CONFIG;
			
		$rs = $CONN->Execute("SELECT template_key FROM {$CONFIG['DB_PREFIX']}kb_module_template_links WHERE module_key='$module_key'");
		$count = $rs->RecordCount();

		return $count;
				
	} //end countTemplates()	
	
	/**
	* Get the key of template for module that has only one template
	*
	* @param  int $module_key  key of module to count templates for
	* @return int $template_key key of template for given module
	* 
	*/
	
	function gettemplate_key($module_key) {
	
		global $CONN, $CONFIG;
			
		$rs = $CONN->Execute("SELECT template_key FROM {$CONFIG['DB_PREFIX']}kb_module_template_links WHERE module_key='$module_key'");
		
		while (!$rs->EOF) {
		
			$template_key = $rs->fields[0];
			$rs->MoveNext();
		
		}
		
		$rs->Close();
		
		return $template_key;
				
	} //end gettemplate_key()	
	
	/**
	* Count entries for a given category, of for entire knowledgebas 
	* @param  int $module_key  key of module to count entries for
	* @param  int $category_key  key of category to count entries for
	* @return int $entry_count total entrie
	* 
	*/
	
	function getEntryCount($module_key, $category_key='') {
	
		global $CONN, $CONFIG;
			
		if ($category_key=='') {
		
			$rs = $CONN->Execute("SELECT entry_key FROM {$CONFIG['DB_PREFIX']}kb_entries WHERE module_key='$module_key' AND status_key='2'");
		
			$entry_count = $rs->RecordCount();
			$rs->Close();
			return $entry_count;
		} else {
			
			$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}kb_entry_category_links.entry_key FROM {$CONFIG['DB_PREFIX']}kb_entry_category_links, {$CONFIG['DB_PREFIX']}kb_entries WHERE  {$CONFIG['DB_PREFIX']}kb_entries.entry_key={$CONFIG['DB_PREFIX']}kb_entry_category_links.entry_key AND category_key='$category_key' AND {$CONFIG['DB_PREFIX']}kb_entries.status_key='2'");
		
			$entry_count = $rs->RecordCount();
			$rs->Close();
			$sub_category_count = $CONN->GetOne("SELECT COUNT(category_key) FROM {$CONFIG['DB_PREFIX']}kb_categories WHERE  {$CONFIG['DB_PREFIX']}kb_categories.parent_key='$category_key'");
		
			$entry_count = $entry_count+$sub_category_count;
			
		}
		
		return $entry_count;
					
	} //end getEntryCount()	
		
	/**
	* Returns status key of given entry 
	* @param  int $entry_key  key of entry to return status of
	* @return  int $status_key  status key of given entry
	* 
	*/
	
	function getEntrystatus_key($entry_key) {
	
		global $CONN, $CONFIG;
			
		$entry_data = $this->getEntryData($entry_key);
		
		return $entry_data['status_key'];
		
	} //end getEntrystatus_key()	

	/**
	* Returns sql created from given search string 
	* @param  string $search_terms  key of entry to return status of
	* @return  string $cond  sql for search terms
	* 
	*/
	function createSearchLimit($search_terms, $rule='all') { 	
	 

		global $CONFIG;
		// Split up $keywords by the delimiter (" ") 

		$arg = split(' ', $search_terms); 

		if ($rule == 'all') { 

			$joiner = 'AND'; 

		} elseif ($rule == 'any') { 

			$joiner = 'OR';  

		} 
	
		if ($rule != 'exact') {
	 
			for($i=0; $i<count($arg); $i++) { 
	
				if ($i==0) {
	
					$cond = "(({$CONFIG['DB_PREFIX']}kb_entry_data.data LIKE '%$arg[$i]%' OR {$CONFIG['DB_PREFIX']}users.first_name LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}users.last_name LIKE '%$arg[$i]%'))"; 
	
				} else {
	
					$cond = "$cond $joiner (({$CONFIG['DB_PREFIX']}kb_entry_data.data LIKE '%$arg[$i]%' OR {$CONFIG['DB_PREFIX']}users.first_name LIKE '%$arg[$i]%') OR ". 
						"({$CONFIG['DB_PREFIX']}users.last_name LIKE '%$arg[$i]%'))"; 
	
				}	   
						
	
			} 
	
		} else {
 
				$cond = "(({$CONFIG['DB_PREFIX']}kb_entry_data.data LIKE '%$search_terms%' OR {$CONFIG['DB_PREFIX']}users.first_name LIKE '%$arg[$i]%') OR ". 
					"({$CONFIG['DB_PREFIX']}users.last_name LIKE '$search_terms%'))"; 
	
		} 

		return $cond;

	} // end function createSearchLimit	
					
} //end InteractKB class	
?>
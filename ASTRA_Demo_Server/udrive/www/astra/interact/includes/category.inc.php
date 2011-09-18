<?php
// +----------------------------------------------------------------------+
// | category.inc.php 1.0                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education	              |
// +----------------------------------------------------------------------+
// | This file is part of Interact.                                       |
// |                                                                      | 
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation (version 2)                             |
// |                                                                      | 
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     |
// | General Public License for more details.                             |
// |                                                                      | 
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, you can view it at                  |
// | http://www.opensource.org/licenses/gpl-license.php                   |
// |                                                                      |
// | This file contains any functions for creating and displaying         |
// |    category trees                                                    |
// |                                                                      |
// |                                                                      |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Authors: Original Author <glen.davies@cce.ac.nz>                     |
// | Last Modified 12/12/02                                               |
// +----------------------------------------------------------------------+

/**
* Get the list of categories with children indented under parents  
* @param [sql] sql to retrieve categories
* @return return list of categories in alpha order
*/	

function get_category_array()
{
    global $CONN,$group_key,$category_array, $CONFIG;
	
	$allowed_categories_sql = $_SESSION['allowed_categories_sql'];
	$userlevel_key = $_SESSION['userlevel_key'];

	$category_array = array();	

    if ($userlevel_key=='1') {

        $sql="select Categoryname, category_key, CategoryParent from {$CONFIG['DB_PREFIX']}Categories WHERE CategoryParent='0' ORDER BY Categoryname";
		
    } else {

	    $sql="select Categoryname, category_key, CategoryParent from {$CONFIG['DB_PREFIX']}Categories where category_key in $allowed_categories_sql and CategoryParent='0' ORDER BY Categoryname";

    }

	$rs=$CONN->Execute($sql);

	while (!$rs->EOF) {

        $category_name                 = $rs->fields[0];
        $category_key                  = $rs->fields[1];
        $category_parent               = $rs->fields[2];
		$category_array[$category_key] = $category_name;

        get_category_children($category_key," - ");      
        $rs->MoveNext();		

	}
    
	$rs->Close();
	
   //asort($category_array);
   return $category_array;

}

function get_category_children($category_key,$indent)
{
    global $CONN,$group_key,$category_array, $CONFIG;
 	
	$allowed_categories_sql = $_SESSION['allowed_categories_sql'];
	$userlevel_key = $_SESSION['userlevel_key'];

    if ($userlevel_key=='1') {

        $sql="select Categoryname, category_key, CategoryParent from {$CONFIG['DB_PREFIX']}Categories WHERE CategoryParent='$category_key' ORDER BY Categoryname";

    } else {

	    $sql="select Categoryname, category_key, CategoryParent from {$CONFIG['DB_PREFIX']}Categories where category_key in $allowed_categories_sql and CategoryParent='$category_key' ORDER BY Categoryname";

    }

	$rs=$CONN->Execute($sql);

	while (!$rs->EOF) {

        $category_name                  = $rs->fields[0];
        $category_key2                  = $rs->fields[1];
        $category_parent                = $rs->fields[2];
		$category_array[$category_key2] = $indent.$category_name;

        get_category_children($category_key2,$indent." - ");      
        $rs->MoveNext();		

	}
  
    $rs->Close();

} // end get_category_children


/**
* Create a select menu of available categories 
* 
* @param [category_array] array of categories
* @param [name] name of the menu
* @param [selected] array or single value of items preselected
* @param [multiple] if true multiple selections allowed
* @return html code for select menu
*/
function create_category_menu($category_array,$name='category_parent',$selected='',$multiple=true)
{
    
	$menu = "<select size=\"10\" name=\"$name\" ";
    
	if ($multiple === true) {
    
	    $menu = $menu.'multiple="multiple"';
    
	}      
    
	$menu = $menu.'>';
	
    while ( list ( $key,$val ) = each ($category_array)) {

    // if an array of selected items has been passed check to see if current key is to be selected

        if (is_array($selected))  {

            if (in_array($key,$selected)) {
        
		            $menu = $menu."<option value=\"$key\" selected>$val</option>";
        
		    } else {
        
		            $menu = $menu."<option value=\"$key\">$val</option>";
        
		    }
        
		 } else {

         //if a single value has been passed for preselection check to see if it matches current key

             if ($selected==$key) {
             
			     $menu = $menu."<option value=\"$key\" selected>$val</option>";
                 
		     } else {
            
			     $menu = $menu."<option value=\"$key\">$val</option>";
            
			 }
             
		 }

    }

    $menu = $menu.'</select>';
    return $menu;

} //end create_category_menu

/**
* Get an array of categories already selected  
* 
* @param [table] name of the table to query against 
* @param [column] name of the column to retrieve
* @param [key] key of the item to retrieve categories for
* @return array of category keys
*/

function get_selected_categories($table,$column,$key)
{
    global $CONN, $CONFIG;
	
    $sql = "select category_key from {$CONFIG['DB_PREFIX']}$table where $column='$key'";
    $rs = $CONN->Execute($sql);
    $selected_categories=array();
    $c=0;          

    while (!$rs->EOF) {          

        $selected_categories[$c]=$rs->fields[0];
        $c++;
        $rs->MoveNext();

    }

    $rs->Close();
    return $selected_categories;

}//end get_selected_categories

function get_linked_breadcrumbs($category_key,$category_parent)
{
    global $CONFIG, $trail;

    if ($category_parent==0) {
	
        $trail = "<a href=\"{$CONFIG['PATH']}/\">Home</a> > ";
        return $trail;
    
	} else {
    
	    get_parent_categories($category_parent);
        $trail = " <a href=\"{$CONFIG['PATH']}/\">Home</a> > ".$trail;
        return $trail;
    
	}

}


function get_parent_categories($category_parent)
{
    global $CONN,$trail, $CONFIG;

    $sql = "select category_key,Categoryname,CategoryParent from {$CONFIG['DB_PREFIX']}Categories where category_key='$category_parent'";

    $rs = $CONN->Execute($sql);

    while (!$rs->EOF) {          

        $category_key=$rs->fields[0]; 
        $category_name=$rs->fields[1];
        $category_parent=$rs->fields[2];
        $breadcrumbs="<a href=\"categories.php?category_key=$category_key\" >$category_name</a> > ".$breadcrumbs;

        if ($category_parent==0) {

            $trail =  $breadcrumbs.$trail;
             return true;

        } else {

            $trail =  $breadcrumbs.$trail;
            get_parent_categories($category_parent);

        }      

        $rs->MoveNext();

    }

    $rs->MoveNext();

} //end get_breadcrumb_links
?>

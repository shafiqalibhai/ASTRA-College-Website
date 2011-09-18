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
* Image  functions
*
* Contains any functions related to image manipulation
*
* @package Common
* @author Bruce Webster <bruce.webster@cce.ac.nz>
* @copyright Christchurch College of Education 2006 
* @version $Id: images.inc.php,v 1.1 2006/03/07 22:33:33 glendavies Exp $
* 
*/

/**
* A class that contains methods related to image manipulation 
* 
* This class is part of the Interact online learning and collaboration platform. 
* It contains methods for displaying, resizing, cropping, etc. if images 
* 
* @package Common
* @author Bruce Webster <bruce.webster@cce.ac.nz>
*/
class InteractImages {

	/**
	* Create a thumbnail  
	* 
	* @param int $max_width of thumb
	* @param int $max_height maximum height of thumb
	* @param string $path path to image
	* @param true/false $crop set to true if image to be cropped
	* @return array $thumb_data array of thumb data, $path, $height, $width
	*/

	function getThumb($max_width,$max_height,$path,$crop)
	{
		global $CONN, $CONFIG;

		return $thumb_data;
	
	} //end getThumb

	
	

} //end InteractImages
?>
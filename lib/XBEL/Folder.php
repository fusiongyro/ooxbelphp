<?php

namespace XBEL;

/** 
 * Represents a folder in XBEL. 
 * 
 * @package XBEL
 */
class Folder extends Node
{
	/**
	 * The list of child XBELNodes.
	 */
	var $children = array();
	
	function visit($visitor)
	{
		$visitor->visitFolder($visitor);
		
		foreach ($this->children as $child)
			$child->visit($visitor);
	}
}

?>
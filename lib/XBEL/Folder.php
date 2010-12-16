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
	 * The list of child Nodes.
	 */
	var $children = array();
	
	/**
	 * Whether or not this folder is currently collapsed.
	 */
	var $folded = false;
	
	/**
	 * Visit this folder. Passes the visitor down to all contained elements.
	 */
	function visit($visitor)
	{
		$visitor->visitFolder($visitor);
		
		foreach ($this->children as $child)
			$child->visit($visitor);
	}
}

?>
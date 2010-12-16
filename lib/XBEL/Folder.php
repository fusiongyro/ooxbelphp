<?php

/** @package XBEL */
namespace XBEL;

/** 
 * Represents a folder in XBEL. 
 * 
 * @package XBEL
 */
class Folder extends NamedNode
{
	/**
	 * The list of child Nodes.
	 * @var array(Node)
	 */
	var $children = array();
	
	/**
	 * Whether or not this folder is currently collapsed.
	 * @var bool
	 */
	var $folded = false;
	
	/**
	 * Visit this folder. Passes the visitor down to all contained elements.
	 * @param XBELVisitor $visitor the visitor
	 */
	function visit($visitor)
	{
	    $visitor->beforeVisitingFolder($this);
	    $visitor->visitFolder($this);
		
		foreach ($this->children as $child)
			$child->visit($visitor);
		$visitor->afterVisitingFolder($this);
	}
}

?>
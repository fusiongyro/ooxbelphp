<?php

namespace XBEL;

/** 
 * Parent class for all XBEL node types. 
 * 
 * @package XBEL
 */
class Node
{
	/**
	 * The title of this folder or bookmark.
	 */
	var $title;
	
	/**
	 * A textual description of this folder or bookmark.
	 */
	var $description;
	
	/**
	 * A textual identifier for this node. Aliases will use this to point to 
	 * this node.
	 */
	var $id;
	
	/**
	 * A string indicating the date and/or time when this node was added to the
	 * XBEL document.
	 */
	var $added;
	
	/**
	 * Process this branch of an XBEL document with the supplied XBELVisitor 
	 * <var>$visitor</var>.
	 * 
	 * Must be implemented by descendent classes.
	 * 
	 * @param XBELVisitor $visitor the visitor to be passed along the XBEL structure
	 */
	function visit($visitor) {}
}

?>
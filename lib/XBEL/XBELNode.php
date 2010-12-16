<?php

namespace XBEL;

/** 
 * Parent class for all XBEL node types. 
 *
 * @package OOTutorial
 */
class XBELNode
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
<?php

/** @package XBEL */
namespace XBEL;

/** 
 * Parent class for all XBEL node types. 
 * 
 * @package XBEL
 */
class Node
{
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
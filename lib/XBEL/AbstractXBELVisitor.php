<?php

/** @package XBEL */
namespace XBEL;

/** 
 * A Visitor pattern implementation for XBEL documents.
 * 
 * To use, subclass and override visitFolder or visitBookmark or both 
 * per your needs. Then pass your instance to the visit method on XBELNode.
 * 
 * @package XBEL
 */
class AbstractXBELVisitor implements XBELVisitor
{
	function beforeVisitingFolder($folder) {}
	function visitFolder($folder) {}
	function afterVisitingFolder($folder) {}

	function visitBookmark($bookmark) {}	
	function visitSeparator($separator) {}	
	function visitAlias($alias) {}
}

?>
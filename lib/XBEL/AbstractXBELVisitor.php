<?php

namespace XBEL;

/** 
 * A Visitor pattern implementation for XBEL documents.
 * 
 * To use, subclass and override visitFolder or visitBookmark or both 
 * per your needs. Then pass your instance to the visit method on XBELNode.
 *
 * @package OOTutorial
 */
class AbstractXBELVisitor implements XBELVisitor
{
	/**
	 * Called when a folder is visited.
	 * 
	 * @param XBELFolder $folder the folder
	 */
	function visitFolder($folder) {}
	
	/**
	 * Called when a bookmark is visited.
	 *
	 * @param XBELBookmark $bookmark the bookmark
	 */
	function visitBookmark($bookmark) {}
	
	/**
	 * Called when a separator is visited.
	 *
	 * @param XBELSeparator $separator the separator
	 */
	function visitSeparator($separator) {}
	
	/**
	 * Called when an alias is visited.
	 *
	 * <b>Note:</b> the target of an alias is not automatically visited. 
	 * However, because the target must have an ID, it will be visited in its
	 * natural setting at one point or another during processing, so this method
	 * is probably only interesting to you if you're concerned primarily with
	 * the structure of the bookmarks rather than the contents of them.
	 */
	function visitAlias($alias) {}
}

?>
<?php

namespace XBEL;

/** 
 * A Visitor pattern implementation for XBEL documents.
 * 
 * @package XBEL
 */
interface XBELVisitor
{
	/**
	 * Called when a folder is visited.
	 * 
	 * @param XBELFolder $folder the folder
	 */
	public function visitFolder($folder);

	/**
	 * Called when a bookmark is visited.
	 *
	 * @param XBELBookmark $bookmark the bookmark
	 */
	public function visitBookmark($bookmark);

	/**
	 * Called when a separator is visited.
	 *
	 * @param XBELSeparator $separator the separator
	 */
	public function visitSeparator($separator);
	
	/**
	 * Called when an alias is visited.
	 *
	 * <b>Note:</b> the target of an alias is not automatically visited. 
	 * However, because the target must have an ID, it will be visited in its
	 * natural setting at one point or another during processing, so this method
	 * is probably only interesting to you if you're concerned primarily with
	 * the structure of the bookmarks rather than the contents of them.
	 */
	public function visitAlias($alias);
}

?>
<?php

namespace XBEL;

/** 
 * @package XBEL
 */

/** 
 * A Visitor pattern implementation for XBEL documents.
 * @package XBEL
 */
interface XBELVisitor
{
	/**
	 * Called when a folder is visited.
	 * 
	 * @param Folder $folder the folder
	 */
	public function visitFolder($folder);

	/**
	 * Called when a bookmark is visited.
	 *
	 * @param Bookmark $bookmark the bookmark
	 */
	public function visitBookmark($bookmark);

	/**
	 * Called when a separator is visited.
	 *
	 * @param Separator $separator the separator
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
	 * 
	 * @param Alias $alias the alias
	 */
	public function visitAlias($alias);

	/**
	 * Called before visiting a folder.
	 * @param Folder $folder the folder
	 */
	public function beforeVisitingFolder($folder);

	/**
	 * Called after visiting a folder.
	 * @param Folder $folder the folder
	 */
	public function afterVisitingFolder($folder);
}

?>
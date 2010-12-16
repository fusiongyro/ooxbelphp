<?php

/** 
 * A Visitor pattern implementation for XBEL documents.
 *
 * @package OOTutorial
 */
interface XBELVisitor
{
	public function visitFolder($folder);
	public function visitBookmark($bookmark);
	public function visitSeparator($separator);
	public function visitAlias($alias);
}

?>
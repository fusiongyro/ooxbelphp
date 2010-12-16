<?php

/** 
 * Represents a bookmark in XBEL. 
 *
 * @package OOTutorial
 */
class XBELBookmark extends XBELNode
{
	/**
	 * The HTTP destination of this bookmark.
	 */
	var $href;
	
	function __construct($href)
	{
		// parent::__construct();
		$this->href = $href;
	}
	
	function visit($visitor)
	{
		$visitor->visitBookmark($this);
	}
}

?>
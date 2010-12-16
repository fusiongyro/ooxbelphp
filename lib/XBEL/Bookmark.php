<?php

namespace XBEL;

/** 
 * Represents a bookmark in XBEL. 
 * 
 * @package XBEL
 */
class Bookmark extends Node
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
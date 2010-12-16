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
	
	/**
	 * The last time this bookmark was visited.
	 */
	var $visited;
	
	/**
	 * The last time this bookmark was edited.
	 */
	var $modified;
	
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
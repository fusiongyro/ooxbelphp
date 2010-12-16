<?php

/** @package XBEL */
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
	
	/**
	 * Constructor
	 * @param string $href the URL of this bookmark's destination.
	 */
	function __construct($href)
	{
		//parent::__construct();
		$this->href = $href;
	}
	
	/**
	 * Visit this Bookmark
	 * @param XBELVisitor $visitor the visitor
	 */
	function visit($visitor)
	{
		$visitor->visitBookmark($this);
	}
}

?>
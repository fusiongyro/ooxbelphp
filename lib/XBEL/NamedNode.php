<?php

/** @package XBEL */
namespace XBEL;

/** 
 * Parent class for all nameable/identifiable XBEL node types. In
 * practice, limited to Bookmark and Folder.
 * 
 * @package XBEL
 */
class NamedNode extends Node
{
	/**
	 * The title of this folder or bookmark.
	 */
	var $title;
	
	/**
	 * A textual description of this folder or bookmark.
	 */
	var $description;
	
	/**
	 * A textual identifier for this node. Aliases will use this to point to 
	 * this node.
	 */
	var $id;
	
	/**
	 * A string indicating the date and/or time when this node was added to the
	 * XBEL document.
	 */
	var $added;
}

?>
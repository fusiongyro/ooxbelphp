<?php

/** @package XBEL */
namespace XBEL;

/**
 * Represents a simple separator in a list of bookmarks. Literally, a line.
 *
 * @package XBEL
 */
class Separator
{
	/**
	 * Visit this separator.
	 * @param XBELVisitor $visitor the visitor to be passed along the XBEL structure
	 */
	function visit($visitor) 
	{
		$visitor->visitSeparator($this);
	}
}


?>
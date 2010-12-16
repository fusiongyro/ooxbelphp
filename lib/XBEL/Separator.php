<?php

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
	 */
	function visit($visitor) 
	{
		$visitor->visitSeparator($this);
	}
}


?>
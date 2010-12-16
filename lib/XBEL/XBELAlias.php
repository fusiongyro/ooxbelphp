<?php

namespace XBEL;

class XBELAlias
{
	/** 
	 * @access private 
	 */
	var $target;
	
	function __construct($target)
	{
		$this->target = $target;
	}
	
	function visit($visitor)
	{
		$visitor->visitAlias($this);
		
		// I waffle on whether we should automatically propagate this. I decided
		// not to under the assumption that we already are processing it somewhere
		// else in the document and it's easier to add this behavior on a 
		// case-by-case basis than to remove it on a case-by-case basis.
		// $visitor->visit($this->target);
	}
}

?>
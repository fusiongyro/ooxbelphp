<?php

namespace XBEL;

/**
 * @package XBEL
 */
class Separator
{
	function visit($visitor) 
	{
		$visitor->visitSeparator($this);
	}
}


?>
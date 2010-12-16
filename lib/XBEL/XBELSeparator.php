<?php

namespace XBEL;

class XBELSeparator
{
	function visit($visitor) 
	{
		$visitor->visitSeparator($this);
	}
}


?>
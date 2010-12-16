<?php

/** 
 * Represents a folder in XBEL. 
 * 
 * @package OOTutorial
 */
class XBELFolder extends XBELNode
{
	/**
	 * The list of child XBELNodes.
	 */
	var $children = array();
	
	function visit($visitor)
	{
		$visitor->visitFolder($visitor);
		
		foreach ($this->children as $child)
			$child->visit($visitor);
	}
}

?>
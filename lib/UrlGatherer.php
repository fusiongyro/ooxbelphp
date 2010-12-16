<?php

/**
 * Visitor class for gathering up all the URLs in an XBEL document.
 *
 * @package OOTutorial
 */
class URLGatherer extends XBEL\AbstractXBELVisitor
{
	/** @access private */
	var $urls = array();

	/** 
	 * Gather up all the URLs under <var>$xbel</var>.
	 * 
	 * @param XBELNode $xbel the folder or bookmark to be processed
	 * @return array(string) a list of string URLs
	 */
	static function gather($xbel)
	{
		$gatherer = new URLGatherer();
		$xbel->visit($gatherer);
		return $gatherer->urls;
	}
	
	/** @access private */
	function visitBookmark($bookmark) 
	{
		$this->urls[] = $bookmark->href;
	}
}

?>
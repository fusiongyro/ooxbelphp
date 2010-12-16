<?php

/** 
 * Parses XBEL: the XML Bookmark Exchange Language. 
 * 
 * XBEL documents are relatively easy to read; an example is included in the 
 * distribution or can be found {@link http://www.retards.org/library/technology/computers/programming/xml/example-xbel.xml here}.
 * 
 * The basic idea is a nested set of folders with bookmark tags within. You 
 * can supplement the structure with aliases and separators, and each node
 * type can bear certain types of metadata. None of the fancy stuff is handled
 * by this trivial library.
 *
 * A brief example document:
 *
 * <pre>
 * &lt;!DOCTYPE xbel PUBLIC 
 *   "+//IDN python.org//DTD XML Bookmark Exchange Language 1.0//EN//XML" 
 *   "http://www.python.org/topics/xml/dtds/xbel-1.0.dtd">
 * &lt;xbel version="1.0">
 *  &lt;title>Daniel's Bookmarks&lt;/title>
 *  &lt;folder>
 *    &lt;title>Time Wasters&lt;/title>
 * 
 *    &lt;bookmark href="http://www.reddit.com/r/haskell">
 *      &lt;title>Haskell Subreddit&lt;/title>
 *      &lt;desc>You'll never be productive again.&lt;/desc>
 *    &lt;/bookmark>
 *   &lt;/folder>
 * &lt;/xbel>
 * </pre>
 *
 * @package OOTutorial
 */
class XBELParser extends SAXParser
{
	/** @access private */
	var $elementStack = array();
	
	/** @access private */
	var $currentElement;

	/**
	 * Empty constructor. Be sure to pass up if you subclass.
	 */
	function __construct()
	{
		parent::__construct();
		$this->elementStack[] = new XBELFolder();
	}
	
	/** 
	 * Convenience function. Parses the XBEL document given by $filename and
	 * returns the XBELFolder representing the root of the document.
	 * 
	 * @param string $filename the XBEL document.
	 */
	static function parseXBEL($filename)
	{
		$xbp = new XBELParser();
		$xbp->parse($filename);
		return $xbp->getRoot();
	}
	
	/**
	 * Returns the root folder for this parse.
	 * @return XBELFolder the root folder
	 */
	function getRoot()
	{
		return $this->elementStack[0];
	}
	
	/** @access private */
	function startElement($elementName, $attrs)
	{
		$this->currentElement = $elementName;
		
		switch ($elementName)
		{
			case 'FOLDER':
				$this->folder();
				break;
			
			case 'BOOKMARK':
				$this->bookmark($attrs['HREF']);
				break;
			
			case 'SEPARATOR':
				$this->separator();
				break;
		}
	}
	
	// creates a new folder and fixes up the stack
	private function folder()
	{
		$this->pushNode(new XBELFolder());
	}
	
	// creates a new bookmark and fixes up the stack
	private function bookmark($href)
	{
		$this->pushNode(new XBELBookmark($href));
	}
	
	// creates a separator
	private function separator()
	{
		$this->currentElement()->children[] = new XBELSeparator();
	}
	
	// fixes up the stack for a given node
	private function pushNode($node)
	{
		$this->currentElement()->children[] = $node;
		$this->elementStack[] = $node;
	}
	
	/** @access private */
	function characterData($data)
	{
		if ($this->currentElement == 'TITLE')
			$this->currentElement()->title = trim($this->currentElement()->title . $data);
		else if ($this->currentElement == 'DESC')
			$this->currentElement()->description = trim($this->currentElement()->description . $data);
	}
		
	/** @access private */
	function endElement($elementName)
	{
		if (in_array($elementName, array('FOLDER', 'BOOKMARK')))
			array_pop($this->elementStack);
	}
	
	private function currentElement()
	{
		return $this->elementStack[end(array_keys($this->elementStack))];
	}
}

?>
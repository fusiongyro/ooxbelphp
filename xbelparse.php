<?php

/**
 * This is an attempt to illustrate some principals of object-orientation.
 * 
 * What you'll find within:
 *
 * - A SAX-based generic XML parser class, {@link SAXParser}
 * - A simple XML structure displaying parser, {@link TagDisplay}
 * - A SAXParser derivative for parsing XBEL documents, {@link XBELParser}
 * - A set of XBEL structural classes, {@link XBELNode}, {@link XBELFolder} and {@link XBELBookmark}
 * - A simple XBEL Visitor-pattern implementation, {@link XBELVisitor}
 * - An example use of XBELVisitor, {@link URLGatherer}, which gathers up URLs from
 *     under all the nodes in the XBEL document.
 * 
 * @package OOTutorial
 */


error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', false);


/**
 * This is a simple OO wrapper around the PHP libxml SAX API to make it work
 * a little bit more like a "traditional" OO SAX API.
 * 
 * The basic idea for users is to subclass and then implement whichever 
 * methods are interesting out of startElement, endElement, characterData,
 * startDocument and endDocument.
 *
 * @property-read int $lineNumber the current line number during parsing
 * @property-read int $columnNumber the current offset into the line during parsing
 * @property-read int $currentByteIndex the number of bytes read so far during parsing
 * @property-read int $errorCode the libxml error code for the most recent error
 * @property-read string $errorMessage the message for the most recent error
 * 
 * @package OOTutorial
 */
class SAXParser
{
	/**
	 * @access private
	 */
	var $parser;
	
	/**
	 * Empty constructor.
	 * 
	 * <b>Important:</b> be sure to pass the call to this constructor up in your
	 * own code if you subclass this, e.g.:
	 *
	 * <code>
	 * class MyParser extends SAXParser {
	 *   function __construct() {
	 *     parent::__construct();
	 *     // do your stuff here
	 *   }
	 * }
	 * </code>
	 */
	function __construct()
	{
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		
		// set up the handlers
		xml_set_element_handler($this->parser, '_startElement', '_endElement');
		xml_set_character_data_handler($this->parser, '_characterData');
	}
	
	/**
	 * Destructor. Be sure to invoke this explicitly in your own destructors if
	 * you create them. 
	 * @see __construct
	 */
	function __destruct()
	{
		xml_parser_free($this->parser);
	}

	private function _startElement($parser, $elementName, $attrs) 
	{
		$this->startElement($elementName, $attrs); 
	}
	
	private function _endElement($parser, $elementName)
	{
		$this->endElement($elementName);
	}
	
	private function _characterData($parser, $data) 
	{
		$this->characterData($data);
	}

	/** 
	 * Start tag event.
	 * 
	 * Callback method. Called when the parser sees a start tag. Meant to be
	 * overridden.
	 *
	 * @param string $elementName the name of the start tag element
	 * @param array $attrs map of attribute name => string values
	 */
	function startElement($elementName, $attrs) { }
	
	/** 
	 * End tag event.
	 * 
	 * Callback method. Called when the parser sees an end tag. Meant to be 
	 * overridden.
	 *
	 * @param string $elementName the name of the end tag element
	 */
	function endElement($elementName) {}

	/** 
	 * Text received event.
	 *
	 * <b>Note:</b> this method will may be invoked multiple times within the 
	 * same tag. Be sure to non-destructively <b>append</b> text rather than 
	 * simply <b>assigning</b> text to a variable, because you are likely to 
	 * overwrite important text! You will probably also want to use 
	 * {@link trim trim($str)} on the returned text if your format is not 
	 * whitespace-specific.
	 * 
	 * Callback method. Called when the parser gets text between tags. Meant to 
	 * be overridden. 
	 *
	 * @param string $data the characters parsed so far
	 */
	function characterData($data) {}
	
	/** Document parsing is begun.
	 * 
	 * Callback method. Called when the parser starts parsing. Meant to be
	 * overridden. 
	 */
	function startDocument() {}
	
	/** Document parsing is complete.
	 *
	 * Callback method. Called when the parser starts parsing. Meant to be
	 * overridden. 
	 */	
	function endDocument() {}
	
	/**
	 * Handle errors during parsing.
	 *
	 * Default implementation:
	 * {@source}
	 *
	 * Callback method. Meant to be overridden. 	
	 */
  function handleError()
  {
  	die(sprintf("XML error: %s at line %d", 
  	            $this->errorMessage, 
  	            $this->lineNumber));
  }
	
	/** 
	 * Parse the file given by filename. 
	 * 
	 * <b>Note:</b> this method causes the callback methods to be executed in
	 * the appropriate order on your class. That's all that happens. If you
	 * want to do more, you'll have to provide some kind of interface or wrapper
	 * of your own around this functionality.
	 * 
	 * @see XBELParser
	 * @param string $filename path to an XML file to be parsed
	 */
	function parse($filename)
	{
		$fp = fopen($filename, 'r');
		while ($data = fread($fp, 4096)) 
		{
			if (!xml_parse($this->parser, $data, feof($fp))) 
			{
				// this should probably be handled better, but this is probably
				// sufficient for didactic purposes.s
				$this->handleError();
			}
		}
	}
	
	function __get($name)
	{
		switch ($name)
		{
			case 'lineNumber':
				return xml_get_current_line_number($this->parser);
			
			case 'columnNumber':
				return xml_get_current_column_number($this->parser);
			
			case 'currentByteIndex':
				return xml_get_current_byte_index($this->parser);
			
			case 'errorCode':
				return xml_get_error_code($this->parser);
			
			case 'errorMessage':
				return xml_error_string(xml_get_error_code($this->parser));
		}
	}
	
	/** 
	 * Parse some text as given in <var>$str</var> 
	 *
	 * @param string $str XML text to parse 
	 */
	function parseString($str)
	{
		$this->startDocument();
		xml_parse($this->parser, $data, true);
		$this->endDocument();
	}	
}

/** 
 * This is a simple example of a SAX parser using the above API. 
 * 
 * All it does is recursively print out the structure of the document,
 * so that, for example:
 *
 * <samp>
 *   <foo><bar>this is bar</bar></foo>
 * </samp>
 * 
 * is rendered as:
 *
 * <pre>
 * &lt;FOO>
 *   &lt;BAR>
 *   &lt;/BAR>
 * &lt;/FOO>
 * </pre>
 * 
 * @package OOTutorial
 */
class TagDisplay extends SAXParser
{
	/**
	 * @access private
	 */
	var $indent = 0;
	
	/** 
	 * Convenience function: display open/close tags for the elements of the 
	 * document <var>$filename</var> as parsed, relatively indented.
	 *
	 * @param string $filename the XML file to display
	 */
	static function run($filename)
	{
		$td = new TagDisplay();
		$td->parse($filename);
	}
	
	/** 
	 * Overridden to display the start element tag indented and then
	 * push the indentation up a level.
	 * @access private
	 */
	function startElement($elementName, $attrs)
	{
		echo str_repeat(" ", $this->indent);
		echo "<$elementName>\n";
		$this->indent += 2;
	}
	
	/** 
	 * Callback overridden to display the end element tag indented and then
	 * push the indentation back down a level.
	 * @access private
	 */
	function endElement($elementName)
	{
		$this->indent -= 2;
		echo str_repeat(" ", $this->indent);
		echo "</$elementName>\n";
	}
}

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

/** 
 * Parent class for all XBEL node types. 
 *
 * @package OOTutorial
 */
class XBELNode
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
	 * Process this branch of an XBEL document with the supplied XBELVisitor 
	 * <var>$visitor</var>.
	 * 
	 * Must be implemented by descendent classes.
	 * 
	 * @param XBELVisitor $visitor the visitor to be passed along the XBEL structure
	 */
	function visit($visitor) {}
}

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

/** 
 * Represents a bookmark in XBEL. 
 *
 * @package OOTutorial
 */
class XBELBookmark extends XBELNode
{
	/**
	 * The HTTP destination of this bookmark.
	 */
	var $href;
	
	function __construct($href)
	{
		// parent::__construct();
		$this->href = $href;
	}
	
	function visit($visitor)
	{
		$visitor->visitBookmark($this);
	}
}

/** 
 * A Visitor pattern implementation for XBEL documents.
 * 
 * To use, subclass and override visitFolder or visitBookmark or both 
 * per your needs. Then pass your instance to the visit method on XBELNode.
 *
 * @package OOTutorial
 */
class XBELVisitor
{
	/**
	 * Called when a folder is visited.
	 * 
	 * @param XBELFolder $folder the folder
	 */
	function visitFolder($folder) {}
	
	/**
	 * Called when a bookmark is visited.
	 *
	 * @param XBELBookmark $bookmark the bookmark
	 */
	function visitBookmark($bookmark) {}
}

/**
 * Visitor class for gathering up all the URLs in an XBEL document.
 *
 * @package OOTutorial
 */
class URLGatherer extends XBELVisitor
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

TagDisplay::run("example-xbel.xml");
$bookmarks = XBELParser::parseXBEL("example-xbel.xml");
var_dump($bookmarks);

// gather up all the URLs
echo "\n";
foreach (URLGatherer::gather($bookmarks) as $url)
	echo "  Got URL: $url\n";

?>
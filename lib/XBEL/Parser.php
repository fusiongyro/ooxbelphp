<?php

/** @package XBEL */
namespace XBEL;

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
 * @package XBEL
 */
class Parser extends \SAX\Parser
{
	/** 
	 * The stack of elements. This grows and shrinks while parsing and should 
	 * ultimately contain just one element if parsing is successful.
	 * 
	 * @var array(Node)
	 * @access private 
	 */
	var $elementStack = array();
	
	/**
	 * The currently-active element during parsing.
	 *
	 * @var Node
	 * @access private 
	 */
	var $currentElement;
	
	/**
	 * The mapping from ID strings to Node elements. Initially empty, as we see
	 * nodes with IDs during parsing this will be populated.
	 *
	 * array(string => Node)
	 * @access private 
	 */
	var $idmap = array();

	/**
	 * Empty constructor. Be sure to pass up if you subclass.
	 */
	function __construct()
	{
		parent::__construct();
		$this->elementStack[] = new Folder();
	}
	
	/** 
	 * Convenience function. Parses the XBEL document given by $filename and
	 * returns the XBELFolder representing the root of the document.
	 * 
	 * @param string $filename the XBEL document.
	 */
	static function parseXBEL($filename)
	{
		$xbp = new Parser();
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
		
		// all we really do here is hand off further processing of a given element
		// to supporting code elsewhere in this class.
		switch ($elementName)
		{
			case 'FOLDER':
				$this->folder($attrs);
				break;
			
			case 'BOOKMARK':
				$this->bookmark($attrs['HREF'], $attrs);
				break;
			
			case 'SEPARATOR':
				$this->separator();
				break;
				
			case 'ALIAS':
				$this->alias($attrs['REF']);
				break;
		}
	}
	
	/** 
	 * When we're done parsing, we need to use our {@link AliasResolver} to 
	 * fix the references of our aliases to point at the actual nodes. We do this
	 * in multiple steps to ensure that forward references work.
	 * 
	 * @access private 
	 */
	function endDocument()
	{
		$ar = new AliasResolver($this->idmap);
		$this->getRoot()->visit($ar);
	}
	
	/**
	 * Creates a new folder and fixes up the stack.
	 */
	private function folder($attrs)
	{
		$folder = new Folder();
		$folder->folded = isset($attrs['FOLDED']) && $attrs['FOLDED'] == 'true';
		$this->pushAndApplyNode($folder, $attrs);
	}
	
	/**
	 * Creates a new bookmark and fixes up the stack.
	 */
	private function bookmark($href, $attrs)
	{
		$bookmark = new Bookmark($href);
		
		// add our special attributes if they are set
		if (isset($attrs['VISITED']))  $bookmark->visited  = $attrs['VISITED'];
		if (isset($attrs['MODIFIED'])) $bookmark->modified = $attrs['MODIFIED'];
			
		$this->pushAndApplyNode($bookmark, $attrs);
	}
	
	/**
	 * Handles the common attributes for all node types.
	 */
	private function applyNodeAttributes($node, $attrs)
	{
		// if we have an ID, stick it in our map to connect with aliases later on.
		if (isset($attrs['ID']))
		{
			$node->id = $attrs['ID'];
			$this->idmap[$attrs['ID']] = $node;
		}
		
		// if we have the 'added', apply it as well
		if (isset($attrs['ADDED']))
			$node->added = $attrs['ADDED'];

		return $node;
	}
	
	/**
	 * Handles the default attributes for the node and then makes it the current
	 * node.
	 */
	private function pushAndApplyNode($node, $attrs)
	{
		$this->pushNode($this->applyNodeAttributes($node, $attrs));
		return $node;
	}
	
	/**
	 * Creates a separator. These are all alike.
	 */
	private function separator()
	{
		$this->currentElement()->children[] = new Separator();
	}
	
	/**
	 * Creates an alias with the given ID reference.
	 */
	private function alias($ref)
	{
		// To start with, we will generate the alias with the ID it points to.
		// When we're done parsing we'll walk through the tree and hook up the 
		// aliases to their true targets.
		$this->currentElement()->children[] = new Alias($ref);
	}
	
	/**
	 * Fixes up the stack for a given node.
	 * 
	 * This amounts to inserting the node into the children list of the parent 
	 * node and also making it the top of that stack.
	 *
	 * @access private
	 */
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
	
	/** 
	 * Locates the last element in the list of children of the top element of
	 * the stack.
	 *
	 * @return the current context element
	 * @access private 
	 */
	private function currentElement()
	{
		return $this->elementStack[end(array_keys($this->elementStack))];
	}	
}

/**
 * This is a simple fixer-upper routine that walks through the tree and 
 * replaces the target of the aliases, which at the end of parsing is just the
 * ID text of some other node in the document, with a reference to the actual
 * parsed node in the tree.
 *
 * @access private
 * @package XBEL
 */
class AliasResolver extends AbstractXBELVisitor
{
	/**
	 * Mapping from ID string to Node instance.
	 * @var array(string => Node)
	 */
	var $idmap;
	
	/**
	 * @param array(string, Node) $idmap the mapping from ID to Node
	 */
	public function __construct($idmap)
	{
		$this->idmap = $idmap;
	}
	
	/**
	 * Replaces $alias->target with the Node corresponding to the ID, looking in
	 * our private ID map.
	 */
	public function visitAlias($alias)
	{
		$alias->target = $this->idmap[$alias->target];
	}
}

?>

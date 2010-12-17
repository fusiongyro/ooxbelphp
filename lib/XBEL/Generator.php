<?php

/**
 * @package XBEL
 */
namespace XBEL;

/**
 * Converts XBEL nodes into their XML format.
 * @package XBEL
 */
class Generator extends AbstractXBELVisitor
{
	/** 
	 * @var XMLWriter $writer the output writer
	 * @access private
	 */
	var $writer;

	/**
	 * @var string PUBLIC_ID the public ID portion of the XBEL DTD declaration
	 */
	const PUBLIC_ID = "+//IDN python.org//DTD XML Bookmark Exchange Language 1.0//EN//XML";

	/**
	 * @var string SYSTEM_ID the system ID portion of the XBEL DTD declaration
	 */
	const SYSTEM_ID = "http://www.python.org/topics/xml/dtds/xbel-1.0.dtd";

	/**
	 * Outputs <var>$xbel</var> to <var>$destination</var>, using an XMLWriter.
	 * @param Node $xbel an XBEL document
	 * @param string $destination the destination URI to be written to
	 */
	public static function writeURI($xbel, $destination)
	{
		$gen = new Generator($destination);
		$gen->process($xbel);
	}

	/**
	 * Constructs a new Generator writing to <var>$destination</var>.
	 * @param string $destination the URI to be written to
	 */
	public function __construct($destination)
	{
		$this->writer = new \XMLWriter();
		$this->writer->openURI($destination);
	}

	/**
	 * Writes the XBEL document given in <var>$xbel</var> to the output URI
	 * specified at construction time.
	 * @param Node $xbel the Node to be written
	 */
	public function process($xbel)
	{
		// first, write the DTD
		$this->writer->writeDTD("xbel", $this->PUBLIC_ID, $this->SYSTEM_ID);
		
		// start the XBEL document
		$this->writer->startElement("xbel");
		$this->writer->writeAttribute('version', '1.0');
		
		// we have to manually handle each element of the root folder to avoid
		// getting all of the bookmarks in one big unnamed folder
		foreach ($xbel->children as $child)
			$child->visit($this);
			
		// end everything
		$this->writer->endElement();
		$this->writer->flush();
	}

	/** 
	 * @access private 
	 */
	public function visitSeparator($sep)
	{
		$this->writer->startElement('separator');
		$this->writer->endElement();
	}

	/** 
	 * @access private 
	 */
	public function visitAlias($alias)
	{
		$this->writer->startElement('alias');
		$this->writer->writeAttribute('ref', $alias->target->id);
		$this->writer->endElement();
	}

	/** 
	 * @access private 
	 */
	public function beforeVisitingFolder($folder)
	{
		$arr = array();
		if ($folder->folded) $arr['folded'] = 'true';
		$arr = array_merge($this->getNamedNodeAttributes($folder), $arr);
		$this->writer->startElement('folder');
		$this->writeAttributes($arr);
		$this->addNamedNodeElements($folder);
	}

	/** 
	 * @access private 
	 */
	public function afterVisitingFolder($folder)
	{
		$this->writer->endElement();
	}

	/** 
	 * @access private 
	 */
	public function visitBookmark($bookmark)
	{
		$arr = array('href' => $bookmark->href);
		$arr = array_merge($this->getNamedNodeAttributes($bookmark), $arr);

		$this->writer->startElement("bookmark");
		$this->writeAttributes($arr);
		$this->addNamedNodeElements($bookmark);		
		$this->writer->endElement();
	}

	/**
	 * Writes the <title> and <description> elements on behalf of the given
	 * NamedNode <var>$node</var>.
	 * @param NamedNode $node the named node
	 */
	private function addNamedNodeElements($node)
	{
		$this->addTextualElement('title', $node->title);
		$this->addTextualElement('description', $node->description);
	}

	/**
	 * Write the id, added and modified attributes of the given NamedNode into 
	 * an array and return it.
	 * @param NamedNode $node the named node
	 * @return array(string => string) the data
	 */
	private function getNamedNodeAttributes($node)
	{
		$arr = array();
		if ($node->id != null) $arr['id'] = $node->id;
		if ($node->added != null) $arr['added'] = $node->added;
		if ($node->modified != null) $arr['modified'] = $node->modified;
		return $arr;
	}

	/**
	 * If <var>$content</var> is not null, write the an element with name 
	 * <var>$name</var> and <var>$content</var> as the content to the output 
	 * stream.
	 * @param string $name the name of the element to be created
	 * @param string $content the content of the element to be created
	 */
	private function addTextualElement($name, $content)
	{
		if ($content != null)
		{
			$this->writer->startElement($name);
			$this->writer->text($content);
			$this->writer->endElement();
		}
	}

	/**
	 * Given an array of name => attribute pairs, write each pair as an 
	 * attribute to the current element on the output stream
	 * @param array(string => string) $attrs the attributes to write
	 */
	private function writeAttributes($attrs)
	{
		foreach ($attrs as $key => $value)
			$this->writer->writeAttribute($key, $value);
	}
}

?>
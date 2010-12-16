<?php

namespace XBEL;

class Generator extends AbstractXBELVisitor
{
    /** @var XMLWriter $writer the output writer
	@access private */
    var $writer;

    var $PUBLIC_ID = "+//IDN python.org//DTD XML Bookmark Exchange Language 1.0//EN//XML";
    var $SYSTEM_ID = "http://www.python.org/topics/xml/dtds/xbel-1.0.dtd";

    public static function writeURI($xbel, $destination)
    {
	$gen = new Generator($destination);
	$gen->process($xbel);
    }

    public function __construct($destination)
    {
	$this->writer = new \XMLWriter();
	$this->writer->openURI($destination);
    }

    public function process($xbel)
    {
	$this->writer->writeDTD("xbel", $this->PUBLIC_ID, $this->SYSTEM_ID);
	$this->writer->startElement("xbel");
	$this->writer->writeAttribute('version', '1.0');
	foreach ($xbel->children as $child)
	    $child->visit($this);
	$this->writer->endElement();
	$this->writer->flush();
    }

    public function visitSeparator($sep)
    {
	$this->writer->startElement('separator');
	$this->writer->endElement();
    }

    public function visitAlias($alias)
    {
	$this->writer->startElement('alias');
	$this->writer->writeAttribute('ref', $alias->target->id);
	$this->writer->endElement();
    }

    public function beforeVisitingFolder($folder)
    {
	$arr = array();
	if ($folder->folded) $arr['folded'] = 'true';
	$arr = array_merge($this->getNamedNodeAttributes($folder), $arr);
	$this->writer->startElement('folder');
	$this->writeAttributes($arr);
	$this->addNamedNodeElements($folder);
    }

    public function afterVisitingFolder($folder)
    {
	$this->writer->endElement();
    }

    public function visitBookmark($bookmark)
    {
	$arr = array('href' => $bookmark->href);
	$arr = array_merge($this->getNamedNodeAttributes($bookmark), $arr);

	$this->writer->startElement("bookmark");
	$this->writeAttributes($arr);
	$this->addNamedNodeElements($bookmark);	
	$this->writer->endElement();
    }

    private function addNamedNodeElements($node)
    {
	$this->addTextualElement('title', $node->title);
	$this->addTextualElement('description', $node->description);
    }

    private function getNamedNodeAttributes($node)
    {
	$arr = array();
	if ($node->id != null) $arr['id'] = $node->id;
	if ($node->added != null) $arr['added'] = $node->added;
	if ($node->modified != null) $arr['modified'] = $node->modified;
	return $arr;
    }

    private function addTextualElement($name, $content)
    {
	if ($content != null)
	{
	    $this->writer->startElement($name);
	    $this->writer->text($content);
	    $this->writer->endElement();
	}
    }

    private function writeAttributes($attrs)
    {
	foreach ($attrs as $key => $value)
	    $this->writer->writeAttribute($key, $value);
    }
}

?>
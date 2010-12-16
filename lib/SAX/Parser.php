<?php

namespace SAX;

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
 * @package SAX
 */
class Parser
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

?>
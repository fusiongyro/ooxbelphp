<?php

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
?>
<?php

/**
 * @package XBEL\Converters
 */

namespace XBEL\Converters;

/**
 * Converter for Firefox 3 JSON bookmarks.
 * @package XBEL\Converters
 */ 
class FirefoxJson
{
	/**
	 * Parses the Firefox 3 JSON bookmark file given in $filename.
	 * @param string $filename the file to attempt to parse.
	 * @return \XBEL\Node
	 */
	public static function parseFile($filename)
	{
		$json = json_decode(file_get_contents("bookmarks.json"));
		return FirefoxJson::parseJSON($json);
	}
	
	/**
	 * Parses the Firefox 3 JSON bookmark data given in $json.
	 * @param object $json nested objects, the output of {@link json_parse}
	 * @return \XBEL\Node
	 */
	public static function parseJSON($json)
	{
		switch ($json->type)
		{
			case "text/x-moz-place-container":
				$node = new \XBEL\Folder();
				$node->title = $json->title;
				foreach ($json->children as $child)
					$node->children[] = FirefoxJson::parseJSON($child);
				return $node;
			    
			case "text/x-moz-place":
				$node = new \XBEL\Bookmark($json->uri);
				$node->title = $json->title;
				return $node;
			    
			case "text/x-moz-place-separator":
				return new \XBEL\Separator();
				break;
		}
	}
}

?>
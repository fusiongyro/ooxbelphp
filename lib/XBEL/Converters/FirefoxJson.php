<?php

namespace XBEL\Converters;

class FirefoxJson
{
    public static function parseFile($filename)
    {
	$json = json_decode(file_get_contents("bookmarks.json"));
	return FirefoxJson::parseJSON($json);
    }

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
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

require_once 'lib/SAX.php';
require_once 'lib/XBEL.php';
require_once 'lib/UrlGatherer.php';

TagDisplay::run("example-xbel.xml");
$bookmarks = XBELParser::parseXBEL("example-xbel.xml");
var_dump($bookmarks);

// gather up all the URLs
echo "\n";
foreach (URLGatherer::gather($bookmarks) as $url)
	echo "  Got URL: $url\n";

?>
<?php

/**
 * Includes the entire XBEL library system so you don't have to worry about 
 * figuring out the correct order.
 * @package XBEL
 */

/** */

require_once 'SAX.php';
require_once 'XBEL/XBELVisitor.php';
require_once 'XBEL/AbstractXBELVisitor.php';
require_once 'XBEL/Node.php';
require_once 'XBEL/Alias.php';
require_once 'XBEL/Bookmark.php';
require_once 'XBEL/Folder.php';
require_once 'XBEL/Separator.php';
require_once 'XBEL/Parser.php';

?>
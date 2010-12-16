<?php

require_once 'lib/XBEL.php';

$data = XBEL\Converters\FirefoxJson::parseFile("bookmarks.json");
XBEL\Generator::writeURI($data, "php://output");

?>
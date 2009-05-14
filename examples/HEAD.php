<?php

require_once('./inc.php');
require_once('SimpleHttp.php');

/**
 * Example of issuing a simple POST request.
 * Fake a login to digg.com...
 */
$socket = new SimpleHttp('digg.com');
$socket->setConnectTimeout(1);
$socket->setStreamTimeout(1);
$socket->head('/apple');
var_dump($socket->getRequest()); 
var_dump($socket->getResponseHeaders()); 

?>
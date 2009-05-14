<?php

require_once('./inc.php');
require_once('SimpleHttp.php');

/**
 * Example of issuing a simple GET request
 */
 
$socket = new SimpleHttp('rspot.net');
$socket->get('/');
var_dump($socket->getResponseStatus()); 

?>
<?php

require_once('./inc.php');
require_once('Duckk/SimpleHTTP.php');

/**
 * Example of issuing a simple GET request
 */
 
$socket = new Duckk_SimpleHTTP('rspot.net');
$socket->get('/');
var_dump($socket->getResponseStatus()); 

?>
<?php

require_once('./inc.php');
require_once('Duckk/SimpleHTTP.php');

/**
 * Example of issuing a simple GET request
 */
 
$socket = new Duckk_SimpleHTTP('google.com');
$socket->setStreamTimeout(1);
$socket->get('/');
var_dump($socket); 

?>
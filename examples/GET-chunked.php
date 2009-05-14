<?php

require_once('./inc.php');
require_once('SimpleHttp.php');

/**
 * Example of issuing a GET request to a server that responds with
 * Transfer-Encoding: chunked
 */
$socket = new SimpleHttp('www.google.com');
$socket->get('/');
var_dump($socket); 

?>
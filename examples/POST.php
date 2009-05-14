<?php

require_once('./inc.php');
require_once('SimpleHttp.php');

/**
 * Example of issuing a simple POST request.
 * Fake a login to digg.com...
 */
$socket = new SimpleHttp('digg.com');
$socket->post('/login/verify/digg', 'username=foo&pasword=bar&persistent=on');
var_dump($socket); 

?>
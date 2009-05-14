<?php

require_once('./inc.php');
require_once('SimpleHttp/KeepAlive.php');

/**
 * Example of using the class with keep-alives
 * I have a vhost on my local machine that does Keep-Alives for me
 * called couchdb.local (a proxy to couch). You'll either have to make
 * that work for you or replace the hostname passed to the constructor
 */
$socket = new SimpleHttp_KeepAlive('couchdb.local');
$socket->setStreamTimeout(1);

$socket->get('/');
var_dump($socket); 

echo "---------------second request--------------------\n";

$socket->get('/_all_dbs');
var_dump($socket);

?>
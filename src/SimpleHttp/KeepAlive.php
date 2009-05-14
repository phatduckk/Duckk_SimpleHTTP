<?php

require_once 'SimpleHttp.php';

class SimpleHttp_KeepAlive extends SimpleHttp
{
    public function __construct()
    {
        parent::__construct();
        
        // setup the stuff for Connection: Keep-Alive
        $this->addHeader('Connection', 'Keep-Alive');
        $this->setHttpVersion(self::HTTP_VERSION_1_1);
    }
    
    public function setHttpVersion($httpVersion)
    {
        // noop - don't the http version to change
        // we need 1.1 for Keep-Alive
    }
}

?>
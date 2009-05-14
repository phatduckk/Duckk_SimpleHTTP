<?php

class SimpleHttp
{
    private $host           = null;
    private $port           = null;
    private $socket         = null;
    private $timeoutConnect = null;
    private $timeoutStream  = array('sec' => 60, 'ms' => 0);    
    private $connected      = false;
    private $headers        = array();
    private $httpMethod     = self::HTTP_METHOD_GET;
    private $httpVersion    = self::HTTP_VERSION_1_1;
    private $path           = '/';
    private $respHeaders    = array();
    private $respBody       = null;
    private $rawResponse    = null;
    private $rawResquest    = null;
    private $respStatus     = null;
    
    const HTTP_VERSION_1_0 = '1.0';
    const HTTP_VERSION_1_1 = '1.1';
    
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_HEAD   = 'HEAD';
    
    /**
     * Constructor
     *
     * @param string $host The http host
     * @param int    $port The port to connect to
     */
    public function __construct($host, $port = 80)
    {
        $this->host = $host;
        $this->port = $port;
        
        $this->addHeader('User-Agent', 'PHP: ' . get_class());
        $this->addHeader('Host', $host);
    }        
    
    /**
     * Destructor
     *
     * Close the connection
     */    
    public function __destruct()
    {
        $this->close();
    }        
    
    /**
     * Run an HTTP GET
     *
     * @param string $path The path to request
     * @param string $body The body of the request
     *
     * @return void
     */    
    public function get($path, $body = null)
    {
        return $this->doHttp(self::HTTP_METHOD_GET, $path, $body);
    }
    
    /**
     * Run an HTTP POST
     *
     * @param string $path The path to post to
     * @param string $body The body of the POST
     *
     * @return void
     */    
    public function post($path, $body = null)
    {
        $this->addHeader('Content-length', strlen($body));
        return $this->doHttp(self::HTTP_METHOD_POST, $path, $body);
    }    
    
    /**
     * Run an HTTP PUT
     *
     * @param string $path The path to PUT to
     * @param string $body The body of the PUT
     *
     * @return void
     */    
    public function put($path, $body = null)
    {
        $this->addHeader('Content-length', strlen($body));
        return $this->doHttp(self::HTTP_METHOD_PUT, $path, $body);
    }    
    
    /**
     * Run an HTTP DELETE
     *
     * @param string $path The path to DELETE
     * @param string $body The body of the DELETE
     *
     * @return void
     */    
    public function delete($path, $body = null)
    {
        return $this->doHttp(self::HTTP_METHOD_DELETE, $path, $body);
    }    
    
    /**
     * Run an HTTP HEAD
     *
     * @param string $path The path to HEAD
     * @param string $body The body of the HEAD
     *
     * @return void
     */    
    public function head($path, $body = null)
    {
        return $this->doHttp(self::HTTP_METHOD_HEAD, $path, $body);
    }    
    
    /**
     * Do the actually HTTP work
     *
     * Set up a few things, make sure we're connected, make the request
     * & finally read the response and save it
     *
     * @param string $method An HTTP method the server knows how to handle
     * @param string $path   The path
     * @param string $body   The body of the HTTP request
     */
    protected function doHttp($method, $path, $body = null) 
    {
        $this->httpMethod = $method;
        $this->setPath($path);        
        $this->connect();        
        $this->write($body);
        $this->read();        
    }
    
    /**
     * The the HTTP request as a string
     */ 
    public function getRequest()
    {
        return $this->rawResquest;
    }
    
    /**
     * The the HTTP status of the last request
     *
     * @return int The last HTTP status code
     */    
    public function getResponseStatus()
    {
        return (int) $this->respStatus;
    }    
    
    /**
     * The the HTTP headers from the last response
     *
     * @return array The headers as an associative array
     */    
    public function getResponseHeaders()
    {
        return $this->respHeaders;
    }
    
    /**
     * Get the body of the last HTTP response
     * 
     * @return string
     */
    public function getResponseBody()
    {
        return $this->respBody;
    }
    
    /**
     * Get the last HTTP response as a string
     *
     * The headers and body are includes as a big string... unparsed
     * 
     * @return string
     */    
    public function getRawResponse()
    {
        return $this->rawResponse;
    }    
    
    /**
     * Add a header for the next HTTP request
     *
     * example: $socket->addHeader('Connection', 'close');
     *
     * @param string $name  The name of the header
     * @param string $value The value of the header     
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }
    
    /**
     * Remove a header for the next HTTP request
     *
     * example: $socket->removeeader('Connection', 'close');
     *
     * @param string $nameThe name of the header you want removed
     */    
    public function removeHeader($name) 
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }
    }
    
    /**
     * Set the path to make the HTTP request to
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }
    
    /**
     * Set the HTTP version you want to use
     *
     * Should be self::HTTP_VERSION_1_1 or self::HTTP_VERSION_1_0
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }
    
    /**
     * Set the method to use for the next HTTP request
     *
     * see: self::HTTP_METHOD_GET, self::HTTP_METHOD_POST self::HTTP_METHOD_PUT
     *  self::HTTP_METHOD_DELETE, self::HTTP_METHOD_HEAD
     */    
    public function setMethod($httpMethod = self::HTTP_METHOD_GET)
    {
        $this->httpMethod = $httpMethod;
    }
    
    /**
     * Set the timeout for the socket connection
     *
     * @param int $sec The number of seconds before the connection times out
     */
    public function setConnectTimeout($sec)
    {
        $this->timeoutConnect = $sec;
    }
    
    /**
     * Set the timeout for the socket reads and writes
     *
     * @param int $sec The number of seconds before the stream io times out
     * @param int $ms  The number of ms before the stream io times out
     */    
    public function setStreamTimeout($sec, $ms = 0)
    {
        $this->timeoutStream = array('sec' => $sec, 'ms' => $ms);
    }
    
    /**
     * Set the socket to clocking or non-blocking
     *
     * @param bool $block Whether you want the socket to block or not
     */    
    public function setNonBlocking($block = false)
    {
        $this->connect();
        stream_set_blocking($this->socket, ((int) $block));
    }
    
    /**
     * Make the socket connection
     *
     * A tiemout can be specified by using $this->setConnectTimeout()
     */
    public function connect()
    {
        if ($this->connected) {
            return;
        }
        
        $timeout = ($this->timeoutConnect !== null) 
            ? $this->timeoutConnect 
            : ini_get("default_socket_timeout");
            
        $this->socket = fsockopen($this->host, $this->port, $errno, $err, $timeout);
        
        if (! $this->socket) {
            throw new Exception(
                "Could not connect to {$this->host}:{$this->port} (code: $errno). Message: $err"                
            );
        }
    }
    
    /**
     * Read from the socket connection
     *
     * We will be parsing the raw payload and splitting it into the body and headers.
     * You can get to that data using $this->getResponseBody() and $this->getResponseHeaders()
     *
     * A timeout for reads can be specified by using $this->setConnectTimeout()
     */    
    public function read()
    {        
        // read the header
        $rawHeaders = '';
        while ($b = fgets($this->socket, 8096)) {
            $rawHeaders .= $b;
            if (trim($b) == '') {
                $rawHeaders = rtrim($rawHeaders);
                break;
            }
        }
        
        $this->rawResponse   = "{$rawHeaders}\r\n\r\n";
        $headerParts         = explode("\r\n", $rawHeaders);
        $contentLengthKey    = null;
        $transferEncodingKey = null;
        
        if (! empty($headerParts)) {
            foreach ($headerParts as $header) {
                if (preg_match('(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $header, $matches)){
                    $this->respStatus  = (int) $matches['status'];
                } else {                                                            
                    list($headerName, $headerValue) = explode(':', $header, 2);
                    $this->respHeaders[$headerName] = trim($headerValue);
                    
                    if (strtolower($headerName) == 'content-length') {
                        $contentLengthKey = $headerName;
                    } else if (strtolower($headerName) == 'transfer-encoding') {
                        $transferEncodingKey = $headerName;
                    }
                }         
            }
        }        
             
        if ($transferEncodingKey != null
            && strtolower($this->respHeaders[$transferEncodingKey]) == 'chunked') {  // read chunks ???
                
            $this->respBody = null;
            $bytesToRead    = 0;
            
            do {
                $line               = fgets($this->socket);
                $this->rawResponse .= $line;
                $line               = rtrim($line);
                $matches            = array();
                
                if (preg_match( '(^([0-9a-f]+)(?:;.*)?$)', $line, $matches)) {
                    $bytesToRead = hexdec($matches[1]);
                    $bytesLeft   = $bytesToRead;
                    
                    while ($bytesLeft > 0) {
                        $read            = fread($this->socket, $bytesLeft + 2);
                        $this->respBody .= $read;
                        $bytesLeft      -= strlen($read);
                    }
                }
            } while ($bytesToRead > 0);            
        } else if ($contentLengthKey != null) { // read up to the Content-Length
            $length = (int) $this->respHeaders[$contentLengthKey];
            
            $this->respBody = ($length > 0) 
                ? fread($this->socket, $length)
                : null;
        } else { // no Content-Length so read brute force               
            $this->respBody = stream_get_contents($this->socket);
        }
        
        $this->rawResponse .= $this->respBody;
        $this->respBody     = rtrim($this->respBody);        
    }
    
    /**
     * Close the socket and perform some housekeeping
     */
    public function close()
    {
        // fclose($this->socket);
        
        $this->connected = false;
        $this->socket    = null;                
    }
    
    /**
     * Write a request to the connection
     *
     * @param string $body The body of the request ex: foo=bar&bla=deezNuts
     */
    private function write($body = null)
    {
        $this->connect();
        
        $writeMe = array("{$this->httpMethod} {$this->path} HTTP/{$this->httpVersion}");
        
        foreach ($this->headers as $header => $v) {
            $writeMe[] = "{$header}: $v";
        }
        
        $this->rawResquest = implode("\r\n", $writeMe) . "\r\n\r\n{$body}";
        
        if (fwrite($this->socket, $this->rawResquest) === false) {
            throw new Exception(
                "Could not write to {$this->host}:{$this->port} (code: $errno). Message: $err"                
            );            
        } 
        
        stream_set_timeout($this->socket, $this->timeoutStream['sec'], $this->timeoutStream['ms']);
        // echo $this->rawResquest;
    }
}


?>
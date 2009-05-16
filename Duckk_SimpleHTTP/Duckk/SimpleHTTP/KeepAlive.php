<?php
/**
 * Config
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Duckk
 * @package   SimpleHTTP
 * @author    Arin Sarkissian <arin@rspot.net>
 * @copyright 2009 Arin Sarkissian
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   CVS: $Id$
 */
 
require_once 'Duckk/SimpleHTTP.php';

/**
 * A simple class that decorates Duckk_SimpleHTTP to "force" Keep-Alive support.
 * Essentially it just sets the Connection header and forces HTTP 1.1 
 *
 * @category  SimpleHTTP
 * @package   SimpleHTTP
 * @author    Arin Sarkissian <arin@rspot.net>
 * @copyright 2009 Arin Sarkissian 
 */
class Duckk_SimpleHTTP_KeepAlive extends SimpleHTTP
{
    /**
     * Constructor
     *
     * Call the parent, set Connection header to Keep-Alive, and finally
     * set the HTTP version to 1.1
     */
    public function __construct($host, $port = self::DEFAULT_PORT, 
        $keepAliveTimeout = self::DEFAULT_KEEPALIVE_TIMEOUT,
        $keepAliveConnections = self::DEFAULT_KEEPALIVE_CONNECTIONS)
    {
        parent::__construct($host, $port);
        
        $this->setKeepAvlive($keepAliveTimeout, $keepAliveConnections);
        $this->setHttpVersion(self::HTTP_VERSION_1_1);
    }    
    
    /**
     * Override parent::setHttpVersion() in order to not
     * allow HTTP 1.0
     *
     * @param int $httpVersion Doesn't fuckin matter yo
     */
    public function setHttpVersion($httpVersion)
    {
        // noop - don't the http version to change
        // we need 1.1 for Keep-Alive
    }
}

?>
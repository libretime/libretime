<?php
set_include_path(
    get_include_path()
    . PATH_SEPARATOR
    . realpath(dirname(__FILE__) . '/../')
);

require_once 'Services/Soundcloud.php';

/**
 * Extended class of the Soundcloud class in order to expose protected methods
 * for testing.
 *
 * @category Services
 * @package Services_Soundcloud
 * @author Anton Lindqvist <anton@qvister.se>
 * @copyright 2010 Anton Lindqvist <anton@qvister.se>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link http://github.com/mptre/php-soundcloud
 */
class Services_Soundcloud_Expose extends Services_Soundcloud {

    /**
     * Class constructor. See parent constructor for further reference.
     *
     * @param string $clientId Application client id
     * @param string $clientSecret Application client secret
     * @param string $redirectUri Application redirect uri
     * @param boolean $development Sandbox mode
     *
     * @return void
     * @see Soundcloud
     */
    function __construct($clientId, $clientSecret, $redirectUri = null, $development = false) {
        parent::__construct($clientId, $clientSecret, $redirectUri, $development);
    }

    /**
     * Construct default http headers including response format and authorization.
     *
     * @return array
     * @see Soundcloud::_buildDefaultHeaders()
     */
    function buildDefaultHeaders() {
        return $this->_buildDefaultHeaders();
    }

    /**
     * Construct a url.
     *
     * @param string $path Relative or absolute uri
     * @param array $params Optional query string parameters
     * @param boolean $includeVersion Include the api version
     *
     * @return string
     * @see Soundcloud::_buildUrl()
     */
    function buildUrl($path, $params = null, $includeVersion = true) {
        return $this->_buildUrl($path, $params, $includeVersion);
    }

    /**
     * Get http user agent.
     *
     * @return string
     * @see Soundcloud::_getUserAgent()
     */
    function getUserAgent() {
        return $this->_getUserAgent();
    }

    /**
     * Parse HTTP response headers.
     *
     * @param string $headers
     *
     * @return array
     * @see Soundcloud::_parseHttpHeaders()
     */
    function parseHttpHeaders($headers) {
        return $this->_parseHttpHeaders($headers);
    }

    /**
     * Validates http response code.
     *
     * @return boolean
     * @see Soundcloud::_validResponseCode()
     */
    function validResponseCode($code) {
        return $this->_validResponseCode($code);
    }

}

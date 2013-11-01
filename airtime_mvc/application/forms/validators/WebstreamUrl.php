<?php

class Validate_WebstreamUrl extends Zend_Validate_Abstract
{
    const INVALID_WEBSTREAM_URL = 'webstream_url';
    const INVALID_MIME_TYPE = 'webstream_mime';
 
    protected $_messageTemplates;
    
    protected $_validMimeTypePatterns = array(
    	"/x-mpegurl/" => false,
    	"/xspf\+xml/" => false,
    	"/pls\+xml|x-scpls/" => false,
    	"/(mpeg|ogg|audio\/aacp)/" => true
    );
    
    public function __construct()
    {
    	$this->_messageTemplates = array(
	        self::INVALID_WEBSTREAM_URL => _("Invalid webstream url"),
    		self::INVALID_MIME_TYPE => _("Invalid webstream mime type"),
	    );
    }
    
    private function validateNoContentLength($headers)
    {
    	return isset($headers["Content-Length"]) ? false : true;
    }
 
    public function isValid($value)
    {
    	Logging::info("checking if $value is valid");
    	
    	// By default get_headers uses a GET request to fetch the headers. If you
    	// want to send a HEAD request instead, you can do so using a stream context:
    	//using max redirects to avoid mixed headers, 
    	//can manually follow redirects if a Location header exists.
    	stream_context_set_default(
    		array(
    			'http' => array(
    				'method' => 'HEAD'
    			)
    		)
    	);
    	
    	try {
    		//get an associative array of headers.
    		$headers = get_headers($value, 1);
    		
    		if (empty($headers["Content-Type"])) {
    			$this->_error(self::INVALID_MIME_TYPE);
    			return false;
    		}
    		
    		$contentType = $headers["Content-Type"];
    		Logging::info($contentType);
    		
    		$isValid = false;
    		
    		foreach ($this->_validMimeTypePatterns as $pattern => $doContentLengthCheck) {
    			
    			if (preg_match($pattern, $contentType)) {
    				
    				if ($doContentLengthCheck) {
    					$isValid = self::validateNoContentLength($headers);
    				}
    				else {
    					$isValid = true;
    				}
    				
    				break;
    			}
    		}
    		
    		if ($isValid === false) {
    			$this->_error(self::INVALID_MIME_TYPE);
    		}
    		
    		return $isValid;
    	}
    	catch (Exception $e) {
    		//url is not real
    		$this->_error(self::INVALID_WEBSTREAM_URL);
    		return false;
    	}
    }
}
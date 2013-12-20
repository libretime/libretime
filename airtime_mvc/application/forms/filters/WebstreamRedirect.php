<?php

class Filter_WebstreamRedirect implements Zend_Filter_Interface
{
    public function filter($value)
    {
    	Logging::info("checking if $value passes filter");
    	
    	//empty when first creating the form.
    	if (empty($value)) {
    		return $value;
    	}
    	
    	// By default get_headers uses a GET request to fetch the headers.
    	//using max redirects to avoid mixed headers, 
    	//can manually follow redirects if a Location header exists.
    	stream_context_set_default(
    		array(
    			'http' => array(
    				'method' => 'GET',
    				'max_redirects' => '1'
    			)
    		)
    	);
    	
    	try {
    		
    		$url = $value;
    		
    		while (true) {
    			//get an associative array of headers.
    			$headers = get_headers($url, 1);
    			Logging::info($headers);
    			
    			if (empty($headers["Location"])) {
    				//is not a redirect;
    				break;
    			}
    			else {
    				$url = $headers["Location"];
    			}
    		}
    		
    		return $url;		
    	}
    	catch (Exception $e) {
    		throw new Zend_Filter_Exception( _("Invalid webstream url"));
    	}
    }  
}
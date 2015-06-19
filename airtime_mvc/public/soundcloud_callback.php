<?php

class Router {

    /**
     * Parse the URL query string and store the key->val pairs
     * into an array, then redirect
     */
    public function reroute() {
        $params = array();
        parse_str($_SERVER['QUERY_STRING'], $params);
        $this->_redirect($params);
    }

    /**
     * Redirect to the URL passed in the 'state' parameter
     * when we're redirected here from SoundCloud
     *
     * @param $params array array of URL query parameters
     */
    private function _redirect($params) {
        $url = urldecode($params['state']);
        header("Location: $url?" . $_SERVER['QUERY_STRING']);
    }

}

(new Router())->reroute();

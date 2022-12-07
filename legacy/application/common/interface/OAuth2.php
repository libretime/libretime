<?php

declare(strict_types=1);

interface OAuth2
{
    /**
     * Check whether an OAuth access token exists.
     *
     * @return bool true if an access token exists, otherwise false
     */
    public function hasAccessToken();

    /**
     * Get the OAuth authorization URL.
     *
     * @return string the authorization URL
     */
    public function getAuthorizeUrl();

    /**
     * Request a new OAuth access token and store it in CcPref.
     *
     * @param $code string exchange authorization code for access token
     */
    public function requestNewAccessToken($code);

    /**
     * Regenerate the OAuth access token.
     */
    public function accessTokenRefresh();
}

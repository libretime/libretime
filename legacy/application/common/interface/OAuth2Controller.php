<?php

declare(strict_types=1);

interface OAuth2Controller
{
    /**
     * Send user to a third-party service to authorize before being redirected.
     */
    public function authorizeAction();

    /**
     * Clear the previously saved request token from the preferences.
     */
    public function deauthorizeAction();

    /**
     * Called when user successfully completes third-party authorization
     * Store the returned request token for future requests.
     */
    public function redirectAction();
}

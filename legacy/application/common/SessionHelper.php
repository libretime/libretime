<?php

declare(strict_types=1);

class SessionHelper
{
    public static function reopenSessionForWriting()
    {
        // PHP will send double Set-Cookie headers if we reopen the
        // session for writing, and this breaks IE8 and some other browsers.
        // This hacky workaround prevents double headers. Background here:
        // https://bugs.php.net/bug.php?id=38104
        ini_set('session.cache_limiter', null);
        session_start(); // Reopen the session for writing (without resending the Set-Cookie header)
    }
}

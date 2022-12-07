<?php

declare(strict_types=1);

class SecurityHelper
{
    public static function htmlescape_recursive(&$arr)
    {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                self::htmlescape_recursive($arr[$key]);
            } elseif (is_string($val)) {
                $arr[$key] = htmlspecialchars($val, ENT_QUOTES);
            }
        }

        return $arr;
    }

    public static function verifyCSRFToken($observedToken)
    {
        $current_namespace = new Zend_Session_Namespace('csrf_namespace');
        $observed_csrf_token = $observedToken;
        $expected_csrf_token = $current_namespace->authtoken;

        return $observed_csrf_token == $expected_csrf_token;
    }
}

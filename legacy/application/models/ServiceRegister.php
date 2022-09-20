<?php

class Application_Model_ServiceRegister
{
    public static function GetRemoteIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public static function Register($p_componentName, $p_ipAddress)
    {
        $component = CcServiceRegisterQuery::create()->findOneByDbName($p_componentName);

        if (is_null($component)) {
            $component = new CcServiceRegister();
            $component->setDbName($p_componentName);
        }

        // Need to convert ipv6 to ipv4 since Monit server does not appear
        // to allow access via an ipv6 address.
        // http://[::1]:2812 does not respond.
        if ($p_ipAddress == '::1') {
            $p_ipAddress = '127.0.0.1';
        }

        $component->setDbIp($p_ipAddress);
        $component->save();
    }
}

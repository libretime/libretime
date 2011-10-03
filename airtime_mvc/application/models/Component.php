<?php
class Application_Model_Component {

    public static function Register($p_componentName, $p_ipAddress){

        $component = CcServiceRegisterQuery::create()->findOneByDbName($p_componentName);

        if ($component == NULL){
            $component = new CcServiceRegister();
            $component->setDbName($p_componentName);
        }

        // Need to convert ipv6 to ipv4 since Monit server does not appear
        // to allow access via an ipv6 address.
        // http://[::1]:2812 does not respond.
        // Bug: http://savannah.nongnu.org/bugs/?27608
        if ($p_ipAddress == "::1"){
            $p_ipAddress = "127.0.0.1";
        }
        
        $component->setDbIp($p_ipAddress);
        $component->save();
    }

}

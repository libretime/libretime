<?php

class Application_Model_Webstream{

    public static function getName()
    {
        return "Default";
    }

    public static function getId()
    {
        return "id";
    }

    public static function getLastModified($p_type)
    {
        return "modified";
    }

    public static function getDefaultLength()
    {
        return "length";
    }

    public static function getDescription()
    {
        return "desc";
    }

    public static function save($request)
    {
        Logging::log($request->getParams());
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        Logging::log($userInfo);

        $length = trim($request->getParam("length"));
        preg_match("/^([0-9]{1,2})h ([0-5][0-9])m$/", $length, $matches);
        $hours = $matches[1];
        $minutes = $matches[2];
        $di = new DateInterval("PT{$hours}H{$minutes}M"); 
        $dblength = $di->format("%H:%I"); 

        #TODO: These should be validated by a Zend Form.
        $webstream = new CcWebstream();
        $webstream->setDbName($request->getParam("name"));
        $webstream->setDbDescription($request->getParam("description"));
        $webstream->setDbUrl($request->getParam("url"));

        $webstream->setDbLength($dblength);
        $webstream->setDbLogin($userInfo->id);
        $webstream->setDbUtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->save();
    }
}

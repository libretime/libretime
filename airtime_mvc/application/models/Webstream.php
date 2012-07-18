<?php

class Application_Model_Webstream{

    public static function getName(){
        return "Default";
    }

    public static function getId(){
        return "id";
    }

    public static function getLastModified($p_type){
        return "modified";
    }

    public static function getDefaultLength(){
        return "length";
    }

    public static function getDescription(){
        return "desc";
    }

    public static function save($request){
        Logging::log($request->getParams());
        $webstream = new CcWebstream();
        $webstream->setDbName($request->getParam("name"));
        $webstream->setDbDescription($request->getParam("description"));
        $webstream->setDbUrl($request->getParam("url"));
        $webstream->setDbLength("00:05:00");
        $webstream->setDbLogin("xxx");
        $webstream->setDbUtime(new DateTime());
        $webstream->setDbMtime(new DateTime());
        $webstream->save();
    }
}

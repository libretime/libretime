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
}

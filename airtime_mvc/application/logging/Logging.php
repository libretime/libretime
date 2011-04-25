<?php

class Logging {

    private static $_logger;
    private static $_path;

    public static function getLogger(){
        if (!isset(self::$logger)) {
            $writer = new Zend_Log_Writer_Stream(self::$_path);
            self::$_logger = new Zend_Log($writer);
        }
        return self::$_logger;
    }

    public static function setLogPath($path){
        self::$_path = $path;
    }
}

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
    
    public static function toString($p_msg){
        if (is_array($p_msg)){
            return print_r($p_msg, true);
        } else {
            return $p_msg;
        }
    }
    
    public static function log($p_msg){
        $logger = self::getLogger();
        $logger->info(self::toString($p_msg));
    }
    
    public static function debug($p_msg){
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == "development"){
            $logger = self::getLogger();
            $logger->debug(self::toString($p_msg));            
        }
    }
}

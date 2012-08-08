<?php

class Logging {

    private static $_logger;
    private static $_path;

    public static function getLogger()
    {
        if (!isset(self::$_logger)) {
            $writer = new Zend_Log_Writer_Stream(self::$_path);
            
            if (Zend_Version::compareVersion("1.11") > 0) {
                //Running Zend version 1.10 or lower. Need to instantiate our
                //own Zend Log class with backported code from 1.11.
                require_once __DIR__."/AirtimeLog.php";
                self::$_logger = new Airtime_Zend_Log($writer);
            } else {
                self::$_logger = new Zend_Log($writer);
            }
            self::$_logger->registerErrorHandler();
        }
        return self::$_logger;
    }

    public static function setLogPath($path)
    {
        self::$_path = $path;
    }
    
    public static function toString($p_msg)
    {
        if (is_array($p_msg) || is_object($p_msg)) {
            return print_r($p_msg, true);
        } else {
            return $p_msg;
        }
    }
    
    public static function log($p_msg)
    {
        $bt = debug_backtrace();

        $caller = array_shift($bt);
        $file = basename($caller['file']);
        $line = $caller['line'];
        
        $caller = array_shift($bt);
        $function = $caller['function'];
       
        $logger = self::getLogger();
        $logger->info("[$file : $function() : line $line] - ".self::toString($p_msg));
    }
    
    public static function debug($p_msg)
    {
        $bt = debug_backtrace();

        $caller = array_shift($bt);
        $file = basename($caller['file']);
        $line = $caller['line'];
        
        $caller = array_shift($bt);
        $function = $caller['function'];

        
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == "development") {
            $logger = self::getLogger();
            $logger->debug("[$file : $function() : line $line] - ".self::toString($p_msg));            
        }
    }
}

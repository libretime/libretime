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
        } else if (is_bool($p_msg)) {
            return $p_msg ? "true" : "false";
        } else {
            return $p_msg;
        }
    }
    
    public static function info($p_msg)
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

    public static function warn($p_msg)
    {
        $bt = debug_backtrace();

        $caller = array_shift($bt);
        $file = basename($caller['file']);
        $line = $caller['line'];
        
        $caller = array_shift($bt);
        $function = $caller['function'];
       
        $logger = self::getLogger();
        $logger->warn("[$file : $function() : line $line] - "
            . self::toString($p_msg));
    }

    public static function error($p_msg)
    {
        $bt = debug_backtrace();

        $caller = array_shift($bt);
        $file = basename($caller['file']);
        $line = $caller['line'];
        
        $caller = array_shift($bt);
        $function = $caller['function'];
       
        $logger = self::getLogger();
        $logger->err("[$file : $function() : line $line] - "
            . self::toString($p_msg));
    }
    
    public static function debug($p_msg)
    {
        if (!(defined('APPLICATION_ENV') && APPLICATION_ENV == "development")) {
            return;
        }

        $bt = debug_backtrace();

        $caller = array_shift($bt);
        $file = basename($caller['file']);
        $line = $caller['line'];
        
        $caller = array_shift($bt);
        $function = $caller['function'];

        $logger = self::getLogger();
        $logger->debug("[$file : $function() : line $line] - ".self::toString($p_msg));            
    }
    // kind of like debug but for printing arrays more compactly (skipping
    // empty elements

    public static function debug_sparse(array $p_msg)
    {
        Logging::debug("Sparse output:");
        Logging::debug( array_filter($p_msg) );
    }

    public static function enablePropelLogging()
    {
        $logger = Logging::getLogger();
        Propel::setLogger($logger);

        $con = Propel::getConnection();
        $con->useDebug(true);

        $config = Propel::getConfiguration(PropelConfiguration::TYPE_OBJECT);
        $config->setParameter('debugpdo.logging.details.method.enabled', true);
        $config->setParameter('debugpdo.logging.details.time.enabled', true);
        $config->setParameter('debugpdo.logging.details.mem.enabled', true);
    }

    public static function disablePropelLogging()
    {
        $con = Propel::getConnection();
        $con->useDebug(false);
        Propel::setLogger(null);
    }

}

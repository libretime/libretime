<?php

declare(strict_types=1);

class Logging
{
    private static $_logger;
    private static $_path;

    public static function getLogger()
    {
        if (!isset(self::$_logger)) {
            $writer = new Zend_Log_Writer_Stream(self::$_path);

            if (Zend_Version::compareVersion('1.11') > 0) {
                // Running Zend version 1.10 or lower. Need to instantiate our
                // own Zend Log class with backported code from 1.11.
                require_once __DIR__ . '/AirtimeLog.php';
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
        }
        if (is_bool($p_msg)) {
            return $p_msg ? 'true' : 'false';
        }

        return $p_msg;
    }

    /** @param debugMode Prints the function name, file, and line number. This is slow as it uses debug_backtrace()
     *                   so don't use it unless you need it.
     * @param mixed $debugMode
     */
    private static function getLinePrefix($debugMode = false)
    {
        $linePrefix = '';

        if ($debugMode) {
            // debug_backtrace is SLOW so we don't want this invoke unless there was a real error! (hence $debugMode)
            $bt = debug_backtrace();
            $caller = $bt[1];
            $file = basename($caller['file']);
            $line = $caller['line'];
            $function = 'Unknown function';
            if (array_key_exists(2, $bt)) {
                $function = $bt[2]['function'];
            }
            $linePrefix .= "[{$file}:{$line} - {$function}()] - ";
        }

        return $linePrefix;
    }

    public static function info($p_msg)
    {
        $logger = self::getLogger();
        $logger->info(self::getLinePrefix() . self::toString($p_msg));
    }

    public static function warn($p_msg)
    {
        $logger = self::getLogger();
        $logger->warn(self::getLinePrefix() . self::toString($p_msg));
    }

    public static function error($p_msg)
    {
        $logger = self::getLogger();
        $logger->err(self::getLinePrefix(true) . self::toString($p_msg));

        // Escape the % symbols in any of our errors because Sentry chokes (vsprint formatting error).
        $msg = self::toString($p_msg);
        $msg = str_replace('%', '%%', $msg);
    }

    public static function debug($p_msg)
    {
        if (!(defined('APPLICATION_ENV') && APPLICATION_ENV == 'development')) {
            return;
        }

        $logger = self::getLogger();
        $logger->debug(self::getLinePrefix(true) . self::toString($p_msg));
    }
    // kind of like debug but for printing arrays more compactly (skipping
    // empty elements

    public static function debug_sparse(array $p_msg)
    {
        Logging::debug('Sparse output:');
        Logging::debug(array_filter($p_msg));
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

    public static function loggingShutdownCallback()
    {
        // Catch the types of errors that PHP doesn't normally let us catch and
        // would otherwise log to the apache log. We route these to our Airtime log to improve the modularity
        // and reliability of our error logging. (All errors are in one spot!)
        $err = error_get_last();
        if (!is_array($err) || !array_key_exists('type', $err)) {
            return;
        }

        switch ($err['type']) {
            case E_ERROR:
            case E_WARNING:
            case E_PARSE:
            case E_NOTICE:
            case E_CORE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_ERROR:
            case E_COMPILE_WARNING:
                // error_log("Oh noes, a fatal: " . var_export($err, true), 1, 'fatals@example.com');
                $errorStr = '';
                if (array_key_exists('message', $err)) {
                    $errorStr .= $err['message'];
                }
                if (array_key_exists('file', $err)) {
                    $errorStr .= ' at ' . $err['file'];
                }
                if (array_key_exists('line', $err)) {
                    $errorStr .= ':' . $err['line'];
                }

                $errorStr .= "\n" . var_export($err, true);
                Logging::error($errorStr);

                break;
        }
    }

    public static function setupParseErrorLogging()
    {
        // Static callback:
        register_shutdown_function('Logging::loggingShutdownCallback');
    }
}

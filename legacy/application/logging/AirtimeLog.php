<?php

declare(strict_types=1);

class Airtime_Zend_Log extends Zend_Log
{
    /**
     * @var bool
     */
    protected $_registeredErrorHandler = false;

    /**
     * @var array|bool
     */
    protected $_errorHandlerMap = false;

    /**
     * @var callable
     */
    protected $_origErrorHandler;

    public function __construct(Zend_Log_Writer_Abstract $writer = null)
    {
        parent::__construct($writer);
    }

    /**
     * Error Handler will convert error into log message, and then call the original error handler.
     *
     * @see https://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     *
     * @return bool
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $errorLevel = error_reporting();

        if ($errorLevel && $errno) {
            if (isset($this->_errorHandlerMap[$errno])) {
                $priority = $this->_errorHandlerMap[$errno];
            } else {
                $priority = Zend_Log::INFO;
            }
            $this->log($errstr, $priority, ['errno' => $errno, 'file' => $errfile, 'line' => $errline, 'context' => $errcontext]);
        }

        if ($this->_origErrorHandler !== null) {
            return call_user_func($this->_origErrorHandler, $errno, $errstr, $errfile, $errline, $errcontext);
        }

        return false;
    }

    /**
     * Register Logging system as an error handler to log php errors
     * Note: it still calls the original error handler if set_error_handler is able to return it.
     *
     * Errors will be mapped as:
     *   E_NOTICE, E_USER_NOTICE => NOTICE
     *   E_WARNING, E_CORE_WARNING, E_USER_WARNING => WARN
     *   E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_RECOVERABLE_ERROR => ERR
     *   E_DEPRECATED, E_STRICT, E_USER_DEPRECATED => DEBUG
     *   (unknown/other) => INFO
     *
     * @see https://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     *
     * @return Zend_Log
     */
    public function registerErrorHandler()
    {
        // Only register once.  Avoids loop issues if it gets registered twice.
        if ($this->_registeredErrorHandler) {
            return $this;
        }

        $this->_origErrorHandler = set_error_handler([$this, 'errorHandler']);

        // Contruct a default map of phpErrors to Zend_Log priorities.
        // Some of the errors are uncatchable, but are included for completeness
        $this->_errorHandlerMap = [
            E_NOTICE => Zend_Log::NOTICE,
            E_USER_NOTICE => Zend_Log::NOTICE,
            E_WARNING => Zend_Log::WARN,
            E_CORE_WARNING => Zend_Log::WARN,
            E_USER_WARNING => Zend_Log::WARN,
            E_ERROR => Zend_Log::ERR,
            E_USER_ERROR => Zend_Log::ERR,
            E_CORE_ERROR => Zend_Log::ERR,
            E_RECOVERABLE_ERROR => Zend_Log::ERR,
            E_STRICT => Zend_Log::DEBUG,
        ];
        // PHP 5.3.0+
        if (defined('E_DEPRECATED')) {
            $this->_errorHandlerMap['E_DEPRECATED'] = Zend_Log::DEBUG;
        }
        if (defined('E_USER_DEPRECATED')) {
            $this->_errorHandlerMap['E_USER_DEPRECATED'] = Zend_Log::DEBUG;
        }

        $this->_registeredErrorHandler = true;

        return $this;
    }
}

<?php

require_once dirname(__DIR__, 2) . '/application/preload.php';

/**
 * Class Setup.
 *
 * @author sourcefabric
 *
 * Abstract superclass for the setup and installation process
 */
abstract class Setup
{
    protected static $_section;

    /**
     * Array of key->value pairs for airtime.conf.
     *
     * @var array
     */
    protected static $_properties;

    abstract public function __construct($settings);

    abstract public function runSetup();

    /**
     * Write new property values to a given section in airtime.conf.temp.
     */
    protected function writeToTemp()
    {
        if (!file_exists(INSTALLER_CONFIG_FILEPATH)) {
            copy(SAMPLE_CONFIG_FILEPATH, INSTALLER_CONFIG_FILEPATH);
        }
        //Logging::info(CONFIG_TEMP_FILEPATH);
        $this->_write(INSTALLER_CONFIG_FILEPATH);
    }

    protected function _write($filePath)
    {
        $file = file($filePath);
        $fileOutput = '';

        $inSection = false;

        foreach ($file as $line) {
            if (strpos($line, static::$_section) !== false) {
                $inSection = true;
            } elseif (strpos($line, '[') !== false) {
                $inSection = false;
            }

            if (substr(trim($line), 0, 1) == '#') {
                /* Workaround to strip comments from airtime.conf.
                 * We need to do this because python's ConfigObj and PHP
                 * have different (and equally strict) rules about comment
                 * characters in configuration files.
                 */
            } elseif ($inSection) {
                $key = trim(@substr($line, 0, strpos($line, '=')));
                $fileOutput .= $key && isset(static::$_properties[$key]) ?
                    $key . ' = ' . static::$_properties[$key] . "\n" : $line;
            } else {
                $fileOutput .= $line;
            }
        }

        file_put_contents($filePath, $fileOutput);
    }

    /**
     * Generates a random string.
     *
     * @param int    $p_len
     *                        length of the output string
     * @param string $p_chars
     *                        characters to use in the output string
     *
     * @return string the generated random string
     */
    protected function generateRandomString($p_len = 20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $string = '';
        for ($i = 0; $i < $p_len; ++$i) {
            $pos = random_int(0, strlen($p_chars) - 1);
            $string .= $p_chars[$pos];
        }

        return $string;
    }
}

/**
 * Class AirtimeDatabaseException.
 *
 * @author sourcefabric
 *
 * Exception class for database setup errors
 */
class AirtimeDatabaseException extends Exception
{
    protected $message = 'Unknown Airtime database exception';
    protected $errors = [];

    public function __construct($message = null, $errors = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrorFields()
    {
        return $this->errors;
    }
}

// Import Setup subclasses
require_once 'database-setup.php';

require_once 'rabbitmq-setup.php';

require_once 'general-setup.php';

require_once 'media-setup.php';

// If airtime.conf exists, we shouldn't be here
if (!file_exists('/etc/airtime/airtime.conf')) {
    if (isset($_GET['obj']) && $objType = $_GET['obj']) {
        $obj = new $objType($_POST);
        if ($obj instanceof Setup) {
            try {
                $response = $obj->runSetup();
            } catch (AirtimeDatabaseException $e) {
                $response = [
                    'message' => $e->getMessage(),
                    'errors' => $e->getErrorFields(),
                ];
            }

            echo json_encode($response);
        }
    }
}

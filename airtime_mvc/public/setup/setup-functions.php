<?php
define("BUILD_PATH", dirname(dirname(__DIR__)) . "/build/");
define("AIRTIME_CONF_TEMP_PATH", "/tmp/airtime.conf.temp");
define("RMQ_INI_TEMP_PATH", "/tmp/rabbitmq.ini.tmp");

/**
 * Class Setup
 *
 * @author sourcefabric
 * 
 * Abstract superclass for the setup and installation process
 */
abstract class Setup {

    protected static $_section;

    /**
     * Array of key->value pairs for airtime.conf
     *
     * @var array
     */
    protected static $_properties;

    abstract function __construct($settings);

    abstract function runSetup();

    /**
     * Write new property values to a given section in airtime.conf.temp
     */
    protected function writeToTemp() {
        if (!file_exists(AIRTIME_CONF_TEMP_PATH)) {
            copy(BUILD_PATH . "airtime.example.conf", AIRTIME_CONF_TEMP_PATH);
        }

        $this->_write(AIRTIME_CONF_TEMP_PATH);
    }

    protected function _write($filePath) {
        $file = file($filePath);
        $fileOutput = "";

        $inSection = false;

        foreach ($file as $line) {
            if (strpos($line, static::$_section) !== false) {
                $inSection = true;
            } else if (strpos($line, "[") !== false) {
                $inSection = false;
            }

            if (substr(trim($line), 0, 1) == "#") {
                /* Workaround to strip comments from airtime.conf.
                 * We need to do this because python's ConfigObj and PHP
                 * have different (and equally strict) rules about comment
                 * characters in configuration files.
                 */
            } else if ($inSection) {
                $key = trim(@substr($line, 0, strpos($line, "=")));
                $fileOutput .= $key && isset(static::$_properties[$key]) ?
                    $key . " = " . static::$_properties[$key] . "\n" : $line;
            } else {
                $fileOutput .= $line;
            }
        }

        file_put_contents($filePath, $fileOutput);
    }

    /**
     * Generates a random string.
     *
     * @param integer $p_len
     *            length of the output string
     * @param string $p_chars
     *            characters to use in the output string
     * @return string the generated random string
     */
    protected function generateRandomString($p_len = 20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
        $string = '';
        for($i = 0; $i < $p_len; $i++) {
            $pos = mt_rand(0, strlen($p_chars) - 1);
            $string .= $p_chars{$pos};
        }
        return $string;
    }

}

/**
 * Class AirtimeDatabaseException
 * 
 * @author sourcefabric
 *
 * Exception class for database setup errors
 */
class AirtimeDatabaseException extends Exception {
    
    protected $message = "Unknown Airtime database exception";
    protected $errors = array();

    public function __construct($message = null, $errors = array(), $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrorFields() {
        return $this->errors;
    }

}

// Import Setup subclasses
require_once ('database-setup.php');
require_once ('rabbitmq-setup.php');
require_once ('general-setup.php');
require_once ('media-setup.php');

// If airtime.conf exists, we shouldn't be here
if (!file_exists("/etc/airtime/airtime.conf")) {
    if (isset($_GET["obj"]) && $objType = $_GET["obj"]) {
        $obj = new $objType($_POST);
        if ($obj instanceof Setup) {
            try {
                $response = $obj->runSetup();
            } catch (AirtimeDatabaseException $e) {
                $response = array(
                        "message" => $e->getMessage(),
                        "errors" => $e->getErrorFields() 
                );
            }
            
            echo json_encode($response);
        }
    }
}

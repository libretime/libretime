<?php

/**
 * Class Setup
 *
 * Abstract superclass for the setup and installation process
 */
abstract class Setup {

    abstract function __construct($settings);

    abstract function runSetup();

    /**
     * Generates a random string.
     *
     * @param integer $p_len length of the output string
     * @param string $p_chars characters to use in the output string
     * @return string the generated random string
     */
    protected function generateRandomString($p_len=20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $string = '';
        for ($i = 0; $i < $p_len; $i++)
        {
            $pos = mt_rand(0, strlen($p_chars)-1);
            $string .= $p_chars{$pos};
        }
        return $string;
    }

}

require_once('database-setup.php');
require_once('rabbitmq-setup.php');

// If airtime.conf exists, we shouldn't be here
if (!file_exists(dirname(dirname(__DIR__)) . '/build/airtime.conf')) {
    if (isset($_GET["obj"]) && $objType = $_GET["obj"]) {
        $obj = new $objType($_POST);
        if ($obj instanceof Setup) {
            echo json_encode($obj->runSetup());
        }
    }
}

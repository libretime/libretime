<?php
/*
require('/path/to/Raven/Autoloader.php');

Raven_Autoloader::register();
*/

class SentryLogger
{
    private static $instance = null;
    private $sentryClient;

    /** Singleton getter */
    public static function getInstance()
    {
        if (!is_null(self::$instance)) {
            return self::$instance;
        } else {
            self::$instance = new SentryLogger();
            return self::$instance;
        }
    }

    private function __construct()
    {
        // Instantiate a new client with a compatible DSN
        $dsn = 'TODO: FILL ME IN';
        $this->sentryClient = new Raven_Client($dsn,
            array(
                //FIXME: This doesn't seem to be working...
                'processorOptions' => array(
                    'Raven_SanitizeDataProcessor' => array(
                        'fields_re' => '/(user_password|user_token|user_secret)/i',
                        'values_re' => '/^(?:\d[ -]*?){15,16}$/'
                    )
                )
            ));
        $client = $this->sentryClient;

        /* The Raven docs suggest not enabling these because they're "too noisy".
        // Install error handlers and shutdown function to catch fatal errors
        $error_handler = new Raven_ErrorHandler($client);
        $error_handler->registerExceptionHandler(true);
        $error_handler->registerErrorHandler(true);
        $error_handler->registerShutdownFunction(true);
        */
        $error_handler = new Raven_ErrorHandler($client);
        $error_handler->registerExceptionHandler();
    }

    public function captureMessage($msg)
    {
        $client = $this->sentryClient;

        // Capture a message
        $event_id = $client->getIdent($client->captureMessage($msg));
        if ($client->getLastError() !== null) {
            //printf('There was an error sending the event to Sentry: %s', $client->getLastError());
        }
    }

    public function captureException($exception)
    {
        $client = $this->sentryClient;
        self::addUserData($client);

        $event_id = $client->getIdent($client->captureException($exception, array(
            'extra' => $this->getExtraData(),
            'tags' => $this->getTags(),
        )));
        $client->context->clear();
    }

    public function captureError($errorMessage)
    {
        $client = $this->sentryClient;

        // Provide some additional data with an exception
        self::addUserData($client);
        $event_id = $client->getIdent($client->captureMessage($errorMessage, array(
            'extra' => $this->getExtraData(),
            'tags' => $this->getTags(),
        )));
        $client->context->clear();
    }

    private static function getTags()
    {
        $tags = array();
        $tags['dev_env'] = Config::getConfig()["dev_env"];
        return $tags;
    }

    private static function addUserData($client)
    {
        $userData = array();
        $userData['client_id'] = Application_Model_Preference::GetClientId();
        $userData['station_url'] = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : "";
        $client->user_context($userData);
    }

    /** Extra data to log with Sentry */
    private function getExtraData()
    {
        $extraData = array();
        $extraData['php_version'] = phpversion();
        $extraData['client_id'] = Application_Model_Preference::GetClientId();
        return $extraData;
    }

}
<?php

define("RMQ_INI_SECTION", "rabbitmq");

function booleanReduce($a, $b) {
    return $a && $b;
}

/**
 * Check to see if Airtime is properly configured.
 *
 * @return boolean true if all Airtime dependencies and services are
 *                 properly configured and running
 */
function checkConfiguration() {
    $r1 = array_reduce(checkPhpDependencies(), "booleanReduce", true);
    $r2 = array_reduce(checkExternalServices(), "booleanReduce", true);
    return $r1 && $r2;
}

/**
 * Check for Airtime's PHP dependencies and return an associative
 * array with the results
 *
 * @return array associative array of dependency check results
 */
function checkPhpDependencies() {
    return array(
        "postgres" => checkDatabaseDependencies()
    );
}

/**
 * Check that the PHP dependencies for the database exist
 *
 * @return boolean true if the database dependencies exist
 */
function checkDatabaseDependencies() {
    global $extensions;
    // Check the PHP extension list for the Postgres db extensions
    return (in_array('pdo_pgsql', $extensions)
        && in_array('pgsql', $extensions));
}

/**
 * Check that all external services are configured correctly and return an associative
 * array with the results
 *
 * @return array associative array of external service check results
 */
function checkExternalServices() {
    return array(
            "database" => checkDatabaseConfiguration(),
            "analyzer" => checkAnalyzerService(),
            "pypo" => checkPlayoutService(),
            "liquidsoap" => checkLiquidsoapService(),
            "rabbitmq" => checkRMQConnection(),
            "celery" => checkCeleryService(),
    );
}

/**
 * Check the database configuration by fetching a connection from Propel
 *
 * @return boolean true if a connection is made to the database
 */
function checkDatabaseConfiguration() {
    configureDatabase();

    try {
        // Try to establish a database connection. If something goes
        // wrong, the database is improperly configured
        Propel::getConnection();
        Propel::close();
    } catch (Exception $e) {
        return false;
    }

    return true;
}

/**
 * Initialize Propel to configure the Airtime database
 */
function configureDatabase() {
    Propel::init(CONFIG_PATH . 'airtime-conf-production.php');
}

/**
 * Check that we can connect to RabbitMQ
 *
 * @return true if the RabbitMQ connection can be established
 */
function checkRMQConnection() {
    // Check for airtime.conf in /etc/airtime/ first, then check in the build directory,
    if (file_exists(AIRTIME_CONFIG_STOR . AIRTIME_CONFIG)) {
        $ini = parse_ini_file(AIRTIME_CONFIG_STOR . AIRTIME_CONFIG, true);
    } else {
        $ini = parse_ini_file(BUILD_PATH . "airtime.example.conf", true);
    }

    $conn = new \PhpAmqpLib\Connection\AMQPConnection($ini[RMQ_INI_SECTION]["host"],
                               $ini[RMQ_INI_SECTION]["port"],
                               $ini[RMQ_INI_SECTION]["user"],
                               $ini[RMQ_INI_SECTION]["password"],
                               $ini[RMQ_INI_SECTION]["vhost"]);
    return isset($conn);
}

/**
 * Check if airtime-analyzer is currently running
 *
 * @return boolean true if airtime-analyzer is running
 */
function checkAnalyzerService() {
    exec("pgrep -f libretime-analyzer", $out, $status);
    if (($out > 0) && $status == 0) {
        return posix_kill(rtrim($out[0]), 0);
    }
    return $status == 0;
}

/**
 * Check if airtime-playout is currently running
 *
 * @return boolean true if airtime-playout is running
 */
function checkPlayoutService() {
    exec("pgrep -f airtime-playout", $out, $status);
    if ($out > 0) {
        return posix_kill(rtrim($out[0]), 0);
    }
    return $status == 0;
}

/**
 * Check if airtime-liquidsoap is currently running
 *
 * @return boolean true if airtime-liquidsoap is running
 */
function checkLiquidsoapService() {
    exec("pgrep -f airtime-liquidsoap", $out, $status);
    if ($out > 0) {
        return posix_kill(rtrim($out[0]), 0);
    }
    return $status == 0;
}

/**
 * Check if airtime-celery is currently running
 *
 * @return boolean true if airtime-celery is running
 */
function checkCeleryService() {
    exec("pgrep -f -u celery airtime-celery", $out, $status);
    if (array_key_exists(0, $out) && $status == 0) {
        return 1;
    }
    return $status == 0;
}

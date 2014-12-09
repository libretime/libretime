<?php

define("RMQ_INI_SECTION", "rabbitmq");
require_once dirname(dirname( __DIR__)) . '/library/php-amqplib/amqp.inc';

/**
 * Check to see if Airtime is properly configured.
 *
 * @return boolean true if all Airtime dependencies and services are
 *                 properly configured and running
 */
function checkConfiguration() {
    return checkPhpDependencies()
        && checkDatabaseConfiguration()
        && checkRMQConnection();
}

/**
 * Check for Airtime's PHP dependencies and return an associative
 * array with the results
 *
 * @return array associative array of dependency check results
 */
function checkPhpDependencies() {
    return array(
        "zend" => checkMvcDependencies(),
        "postgres" => checkDatabaseDependencies()
    );
}

/**
 * Check that the Zend framework libraries are installed
 *
 * @return boolean true if Zend exists in /usr/share/php
 */
function checkMvcDependencies() {
    return file_exists('/usr/share/php/libzend-framework-php')
        || file_exists('/usr/share/php/zendframework'); // Debian version
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
 */
function checkRMQConnection() {
    // Check for airtime.conf in /etc/airtime/ first, then check in the build directory,
    if (file_exists(AIRTIME_CONFIG_STOR . AIRTIME_CONFIG)) {
        $ini = parse_ini_file(AIRTIME_CONFIG_STOR . AIRTIME_CONFIG, true);
    } else if (file_exists(BUILD_PATH . AIRTIME_CONFIG)) {
        $ini = parse_ini_file(BUILD_PATH . AIRTIME_CONFIG, true);
    } else {
        $ini = parse_ini_file(BUILD_PATH . "airtime.example.conf", true);
    }

    $conn = new AMQPConnection($ini[RMQ_INI_SECTION]["host"],
                               $ini[RMQ_INI_SECTION]["port"],
                               $ini[RMQ_INI_SECTION]["user"],
                               $ini[RMQ_INI_SECTION]["password"],
                               $ini[RMQ_INI_SECTION]["vhost"]);
    return isset($conn);
}
<?php

/**
 * Check to see if Airtime is properly configured.
 *
 * @return boolean true if all Airtime dependencies and services are
 *                 properly configured and running
 */
function airtimeCheckConfiguration() {
    return airtimeCheckPhpDependencies()
        && airtimeCheckDatabaseConfiguration();
}

/**
 * Check for Airtime's PHP dependencies and return an associative
 * array with the results
 *
 * @return array associative array of dependency check results
 */
function airtimeCheckPhpDependencies() {
    return array(
        "zend" => airtimeCheckMvcDependencies(),
        "postgres" => airtimeCheckDatabaseDependencies()
    );
}

/**
 * Check that the Zend framework libraries are installed
 *
 * @return boolean true if Zend exists in /usr/share/php
 */
function airtimeCheckMvcDependencies() {
    return file_exists('/usr/share/php/libzend-framework-php')
        || file_exists('/usr/share/php/zendframework'); // Debian version
}

/**
 * Check that the PHP dependencies for the database exist
 *
 * @return boolean true if the database dependencies exist
 */
function airtimeCheckDatabaseDependencies() {
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
function airtimeCheckDatabaseConfiguration() {
    airtimeConfigureDatabase();

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
function airtimeConfigureDatabase() {
    Propel::init(CONFIG_PATH . 'airtime-conf-production.php');
}
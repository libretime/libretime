<?php

$ini = parse_ini_file('/etc/airtime/airtime.conf', true);

return array(
    'dbname' => $ini['database']['dbname'],
    'user' => $ini['database']['dbuser'],
    'password' => $ini['database']['dbpass'],
    'host' => 'localhost',
    'driver' => 'pdo_pgsql',
);

<?php

/* This file is only needed during upgrades when we need the database parameters from /etc/airtime/airtime.conf.
 * The reason we don't just use conf.php is because conf.php may try to load configuration parameters that aren't
 * yet available because airtime.conf hasn't been updated yet. This situation ends up throwing a lot of errors to stdout.
 * airtime*/

require_once("conf.php");

$CC_CONFIG = Config::getConfig();

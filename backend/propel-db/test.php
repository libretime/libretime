<?php
// Include the main Propel script
require_once (__DIR__."/../../3rd_party/php/propel/runtime/lib/Propel.php");

// Initialize Propel with the runtime configuration
Propel::init(__DIR__."/build/conf/campcaster-conf.php");

// Add the generated 'classes' directory to the include path
set_include_path(__DIR__."/build/classes" . PATH_SEPARATOR . get_include_path());

$pl = new CcPlaylist();
$pl->setName("Playlist in Campcaster!");
$pl->save();


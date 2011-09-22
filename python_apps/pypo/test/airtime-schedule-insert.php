<?php

// Define path to application directory
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));
echo APPLICATION_PATH.PHP_EOL;

// Ensure library/ is on include_path
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(APPLICATION_PATH . '/../library'));

set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH . '/models');
echo get_include_path().PHP_EOL;

//Pear classes.
set_include_path(APPLICATION_PATH . get_include_path() . PATH_SEPARATOR . '/../library/pear');

//Controller plugins.
set_include_path(APPLICATION_PATH . get_include_path() . PATH_SEPARATOR . '/controllers/plugins');


require_once APPLICATION_PATH.'/configs/conf.php';
require_once 'DB.php';
require_once(APPLICATION_PATH.'/../library/propel/runtime/lib/Propel.php');

require_once 'Soundcloud.php';
require_once 'Playlist.php';
require_once 'StoredFile.php';
require_once 'Schedule.php';
require_once 'Shows.php';
require_once 'Users.php';
require_once 'RabbitMq.php';
require_once 'Preference.php';
//require_once APPLICATION_PATH.'/controllers/plugins/RabbitMqPlugin.php';

// Initialize Propel with the runtime configuration
Propel::init(__DIR__."/../../../application/configs/airtime-conf.php");


$dsn = $CC_CONFIG['dsn'];

$CC_DBC = DB::connect($dsn, FALSE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);


$playlistName = "pypo_playlist_test";
$secondsFromNow = 30;

echo " ************************************************************** \n";
echo " This script schedules a playlist to play $secondsFromNow minute(s) from now.\n";
echo " This is a utility to help you debug the scheduler.\n";
echo " ************************************************************** \n";
echo "\n";
echo "Deleting playlists with the name '$playlistName'...";
// Delete any old playlists
$pl2 = Playlist::findPlaylistByName($playlistName);
foreach ($pl2 as $playlist) {
    //var_dump($playlist);
    $playlist->delete();
}
echo "done.\n";

// Create a new playlist
echo "Creating new playlist '$playlistName'...";
$pl = new Playlist();
$pl->create($playlistName);


$mediaFile = Application_Model_StoredFile::findByOriginalName("Peter_Rudenko_-_Opening.mp3");
if (is_null($mediaFile)) {
    echo "Adding test audio clip to the database.\n";
    $v = array("filepath" => __DIR__."/../../../audio_samples/vorbis.com/Hydrate-Kenny_Beltrey.ogg");
    $mediaFile = Application_Model_StoredFile::Insert($v);
    if (PEAR::isError($mediaFile)) {
    	var_dump($mediaFile);
    	exit();
    }
}
$pl->addAudioClip($mediaFile->getId());
echo "done.\n";


//$pl2 = Playlist::findPlaylistByName("pypo_playlist_test");
//var_dump($pl2);

// Get current time
// In the format YYYY-MM-DD HH:MM:SS.nnnnnn
$startTime = date("Y-m-d H:i:s");
$endTime = date("Y-m-d H:i:s", time()+(60*60));

echo "Removing everything from the scheduler between $startTime and $endTime...";


// Check for succces
$scheduleClear = Schedule::isScheduleEmptyInRange($startTime, "01:00:00");
if (!$scheduleClear) {
    echo "\nERROR: Schedule could not be cleared.\n\n";
    var_dump(Schedule::GetItems($startTime, $endTime));
    exit;
}
echo "done.\n";

// Schedule the playlist for two minutes from now
echo "Scheduling new playlist...\n";
//$playTime = date("Y-m-d H:i:s", time()+(60*$minutesFromNow));
$playTime = date("Y-m-d H:i:s", time()+($secondsFromNow));

//$scheduleGroup = new ScheduleGroup();
//$scheduleGroup->add($playTime, null, $pl->getId());

//$show = new ShowInstance($showInstanceId);
//$show->scheduleShow(array($pl->getId()));

//$show->setShowStart();
//$show->setShowEnd();

echo " SUCCESS: Playlist scheduled at $playTime\n\n";

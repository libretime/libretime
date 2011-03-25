<?php
require_once '../../application/configs/conf.php';
require_once 'DB.php';
require_once '../../application/models/Playlist.php';
require_once '../../application/models/StoredFile.php';
require_once(__DIR__.'/../../library/propel/runtime/lib/Propel.php');
// Initialize Propel with the runtime configuration
Propel::init(__DIR__."/../../application/configs/propel-config.php");
// Add the generated 'classes' directory to the include path
set_include_path(__DIR__."/../../application/models" . PATH_SEPARATOR . get_include_path());

$dsn = $CC_CONFIG['dsn'];

$CC_DBC = DB::connect($dsn, TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);


$playlistName = "pypo_playlist_test";
$minutesFromNow = 1;

echo " ************************************************************** \n";
echo " This script schedules a playlist to play $minutesFromNow minute(s) from now.\n";
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


$mediaFile = StoredFile::findByOriginalName("Peter_Rudenko_-_Opening.mp3");
if (is_null($mediaFile)) {
    echo "Adding test audio clip to the database.\n";
    $v = array("filepath" => __DIR__."/../../audio_samples/OpSound/Peter Rudenko - Opening.mp3");
    $mediaFile = StoredFile::Insert($v);
    if (PEAR::isError($mediaFile)) {
    	var_dump($mediaFile);
    	exit();
    }
}
$pl->addAudioClip($mediaFile->getId());
echo "done.\n";

$mediaFile = StoredFile::findByOriginalName("Manolo Camp - Morning Coffee.mp3");
if (is_null($mediaFile)) {
    echo "Adding test audio clip to the database.\n";
    $v = array("filepath" => __DIR__."/../../audio_samples/OpSound/Manolo Camp - Morning Coffee.mp3");
    $mediaFile = StoredFile::Insert($v);
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
// Scheduler: remove any playlists for the next hour
//Schedule::RemoveItemsInRange($startTime, $endTime);
// Check for succcess
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
$playTime = date("Y-m-d H:i:s", time()+(20*$minutesFromNow));
$scheduleGroup = new ScheduleGroup();
$scheduleGroup->add($playTime, null, $pl->getId());

echo " SUCCESS: Playlist scheduled at $playTime\n\n";

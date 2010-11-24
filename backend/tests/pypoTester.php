<?php
require_once '../../conf.php';
require_once '../Playlist.php';
require_once '../StoredFile.php';
require_once(__DIR__.'/../../3rd_party/php/propel/runtime/lib/Propel.php');
// Initialize Propel with the runtime configuration
Propel::init(__DIR__."/../propel-db/build/conf/campcaster-conf.php");
// Add the generated 'classes' directory to the include path
set_include_path(__DIR__."/../propel-db/build/classes" . PATH_SEPARATOR . get_include_path());

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

// Add a media clip
$mediaFile = StoredFile::findByOriginalName("test10001.mp3");
if (is_null($mediaFile)) {
    echo "Adding test audio clip to the database.\n";
    $v = array("filepath" => __DIR__."/test10001.mp3");
    $mediaFile = StoredFile::Insert($v);
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
Schedule::RemoveItemsInRange($startTime, $endTime);
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
$playTime = date("Y-m-d H:i:s", time()+(60*$minutesFromNow));
$scheduleGroup = new ScheduleGroup();
$scheduleGroup->add($playTime, null, $pl->getId());

echo " SUCCESS: Playlist scheduled at $playTime\n\n";
?>
<?php
set_include_path(__DIR__.'/..' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../Shows.php';
require_once __DIR__.'/../StoredFile.php';
require_once __DIR__.'/../Playlist.php';
require_once __DIR__.'/../Schedule.php';
require_once __DIR__.'/../Preference.php';
require_once __DIR__.'/../RabbitMq.php';
require_once __DIR__.'/../../configs/conf.php';
require_once __DIR__.'/../../../install/include/AirtimeIni.php';
require_once __DIR__.'/../../../install/include/AirtimeInstall.php';
require_once __DIR__.'/../../../library/propel/runtime/lib/Propel.php';

Propel::init(__DIR__.'/../../configs/airtime-conf.php');


AirtimeInstall::DbConnect(true);

// Create a playlist
$playlist = new Playlist();
$playlist->create("Calendar Load test playlist ".uniqid());

// Add a file
$values = array("filepath" => __DIR__."/test10001.mp3");
$storedFile = StoredFile::Insert($values, false);
$result = $playlist->addAudioClip($storedFile->getId());

// Add a file
$values = array("filepath" => __DIR__."/test10002.mp3");
$storedFile2 = StoredFile::Insert($values, false);

$result = $playlist->addAudioClip($storedFile2->getId());
$result = $playlist->addAudioClip($storedFile2->getId());

echo "Created playlist ".$playlist->getName()." with ID ".$playlist->getId()."\n";

// Create the shows

$data = array();

$currentDate = date("Y\\-m\\-d");

$year = date("Y");
$month = date("m");
$day = date("d");

$nextDay = $currentDate;

#echo $currentDate;
$currentHour = date("H");
$setHour = $currentHour + 1;

$showNumber = 1;
for ($days=1; $days<100; $days=$days+1)
{
    // Adding shows until the end of the day
    while ($setHour < 24)
    {
      echo 'Adding show: '.$nextDay. '   '.$setHour.":00\n";
      $data['add_show_name'] = 'automated show '.$showNumber;
      $data['add_show_start_date'] = $nextDay;
      $data['add_show_start_time'] = $setHour.':00';
      $showNumber = $showNumber + 1;
      $data['add_show_duration'] = '1:00';
      $data['add_show_no_end'] = 0;
      $data['add_show_repeats'] = 0;
      $data['add_show_description'] = 'automated show';
      $data['add_show_url'] = 'http://www.OfirGal.com';
      $data['add_show_color'] = "";
      $data['add_show_background_color'] = "";
      $data['add_show_record'] = 0;
      $data['add_show_hosts'] ="";
      $showId = Show::create($data);
      Show::populateShowUntil($showId, "2012-01-01 00:00:00");

      // populating the show with a playlist
      $show = new ShowInstance($showId);
      $show->scheduleShow(array($playlist->getId()));

      $setHour = $setHour + 1;
    }
    // set the next day
    $setHour = 0;
    if ($day<30) {
      $day = $day + 1;
    } else {
      $day = 1;
      if ($month<12)
      {
        $month = $month + 1;
      } else {
        $month = 1;
        $year = $year + 1;
      }
    }
    $nextDay = $year."-".$month."-".$day;
}

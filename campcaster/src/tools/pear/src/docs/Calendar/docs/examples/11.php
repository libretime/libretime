<?php
/**
* Description: demonstrates a decorator used to "attach a payload" to a selection
* to make it available when iterating over calendar children
*/
if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT','../../');
}
require_once CALENDAR_ROOT.'Day.php';
require_once CALENDAR_ROOT.'Hour.php';
require_once CALENDAR_ROOT.'Decorator.php';

// Decorator to "attach" functionality to selected hours
class DiaryEvent extends Calendar_Decorator {
    var $entry;
    function DiaryEvent($calendar) {
        Calendar_Decorator::Calendar_Decorator($calendar);
    }
    function setEntry($entry) {
        $this->entry = $entry;
    }
    function getEntry() {
        return $this->entry;
    }
}

// Create a day to view the hours for
$Day = & new Calendar_Day(2003,10,24);

// A sample query to get the data for today (NOT ACTUALLY USED HERE)
$sql = "
        SELECT
            *
        FROM
            diary
        WHERE
            eventtime >= '".$Day->thisDay(TRUE)."'
        AND
            eventtime < '".$Day->nextDay(TRUE)."';";

// An array simulating data from a database
$result = array (
    array('eventtime'=>mktime(9,0,0,10,24,2003),'entry'=>'Meeting with sales team'),
    array('eventtime'=>mktime(11,0,0,10,24,2003),'entry'=>'Conference call with Widget Inc.'),
    array('eventtime'=>mktime(15,0,0,10,24,2003),'entry'=>'Presentation to board of directors')
    );

// An array to place selected hours in
$selection = array();

// Loop through the "database result"
foreach ( $result as $row ) {
    $Hour = new Calendar_Hour(2000,1,1,1); // Create Hour with dummy values
    $Hour->setTimeStamp($row['eventtime']); // Set the real time with setTimeStamp

    // Create the decorator, passing it the Hour
    $DiaryEvent = new DiaryEvent($Hour);

    // Attach the payload
    $DiaryEvent->setEntry($row['entry']);

    // Add the decorator to the selection
    $selection[] = $DiaryEvent;
}

// Build the hours in that day, passing the selection
$Day->build($selection);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Passing a Selection Payload with a Decorator </title>
</head>
<body>
<h1>Passing a Selection "Payload" using a Decorator</h1>
<table>
<caption><b>Your Schedule for <?php echo ( date('D nS F Y',$Day->thisDay(TRUE)) ); ?></b></caption>
<tr>
<th width="5%">Time</th>
<th>Entry</th>
</tr>
<?php
while ( $Hour = & $Day->fetch() ) {

    $hour = $Hour->thisHour();
    $minute = $Hour->thisMinute();

    // Office hours only...
    if ( $hour >= 8 && $hour <= 18 ) {
        echo ( "<tr>\n" );
        echo ( "<td>$hour:$minute</td>\n" );

        // If the hour is selected, call the decorator method...
        if ( $Hour->isSelected() ) {
            echo ( "<td bgcolor=\"silver\">".$Hour->getEntry()."</td>\n" );
        } else {
            echo ( "<td>&nbsp;</td>\n" );
        }
        echo ( "</tr>\n" );
    }
}
?>
</table>
<p>The query to fetch this data, with help from PEAR::Calendar, might be;</p>
<pre>
<?php echo ( $sql ); ?>
</pre>
</body>
</html>
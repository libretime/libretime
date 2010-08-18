<?php
/**
* Description: Demonstrates building a calendar for a month using the Week class
* Uses UnixTs engine
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

// Force UnixTs engine (default setting)
define('CALENDAR_ENGINE','UnixTS');

if (!@include 'Calendar'.DIRECTORY_SEPARATOR.'Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Month/Weeks.php';
require_once CALENDAR_ROOT.'Day.php';

// Initialize GET variables if not set
if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('m');
if (!isset($_GET['d'])) $_GET['d'] = date('d');

// Build a month object
$Month = new Calendar_Month_Weeks($_GET['y'], $_GET['m']);

// Create an array of days which are "selected"
// Used for Week::build() below
$selectedDays = array (
    new Calendar_Day($_GET['y'],$_GET['m'], $_GET['d']),
    new Calendar_Day($_GET['y'], 12, 25),
    new Calendar_Day(date('Y'), date('m'), date('d')),
    );

// Instruct month to build Week objects
$Month->build();

// Construct strings for next/previous links
$PMonth = $Month->prevMonth('object'); // Get previous month as object
$prev = $_SERVER['PHP_SELF'].'?y='.$PMonth->thisYear().'&m='.$PMonth->thisMonth().'&d='.$PMonth->thisDay();
$NMonth = $Month->nextMonth('object');
$next = $_SERVER['PHP_SELF'].'?y='.$NMonth->thisYear().'&m='.$NMonth->thisMonth().'&d='.$NMonth->thisDay();
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Calendar </title>
<style text="text/css">
table {
    background-color: silver;
}
caption {
    font-family: verdana;
    font-size: 12px;
    background-color: while;
}
.prevMonth {
    font-size: 10px;
    text-align: left;
}
.nextMonth {
    font-size: 10px;
    text-align: right;
}
th {
    font-family: verdana;
    font-size: 11px;
    color: navy;
    text-align: right;
}
td {
    font-family: verdana;
    font-size: 11px;
    text-align: right;
}
.selected {
    background-color: yellow;
}
.empty {
    color: white;
}
</style>
</head>

<body>
<h2>Build with Calendar_Month_Weeks::build() then Calendar_Week::build()</h2>
<table class="calendar">
<caption>
<?php echo date('F Y', $Month->getTimeStamp()); ?>
</caption>
<tr>
<th>M</th>
<th>T</th>
<th>W</th>
<th>T</th>
<th>F</th>
<th>S</th>
<th>S</th>
</tr>
<?php
while ($Week = $Month->fetch()) {
    echo "<tr>\n";
    // Build the days in the week, passing the selected days
    $Week->build($selectedDays);
    while ($Day = $Week->fetch()) {

        // Build a link string for each day
        $link = $_SERVER['PHP_SELF'].
                    '?y='.$Day->thisYear().
                    '&m='.$Day->thisMonth().
                    '&d='.$Day->thisDay();

        // Check to see if day is selected
        if ($Day->isSelected()) {
            echo '<td class="selected">'.$Day->thisDay().'</td>'."\n";
        // Check to see if day is empty
        } else if ($Day->isEmpty()) {
            echo '<td class="empty">'.$Day->thisDay().'</td>'."\n";
        } else {
            echo '<td><a href="'.$link.'">'.$Day->thisDay().'</a></td>'."\n";
        }
    }
    echo '</tr>'."\n";
}
?>
<tr>
<td>
<a href="<?php echo $prev; ?>" class="prevMonth"><< </a>
</td>
<td colspan="5">&nbsp;</td>
<td>
<a href="<?php echo $next; ?>" class="nextMonth"> >></a>
</td>
</tr>
</table>
<?php
echo '<p><b>Took: '.(getmicrotime()-$start).' seconds</b></p>';
?>
</body>
</html>
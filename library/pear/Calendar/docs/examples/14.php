<?php
/**
* Description: same as 3.php, but using the PEAR::Date engine
* Note: make sure PEAR::Date is a stable release!!!
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

// Switch to PEAR::Date engine
define('CALENDAR_ENGINE', 'PearDate');

if (!@include 'Calendar'.DIRECTORY_SEPARATOR.'Calendar.php') {
    define('CALENDAR_ROOT','../../');
}
require_once CALENDAR_ROOT.'Month/Weekdays.php';
require_once CALENDAR_ROOT.'Day.php';

// Initialize GET variables if not set
if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('m');
if (!isset($_GET['d'])) $_GET['d'] = date('d');

// Build the month
$month = new Calendar_Month_Weekdays($_GET['y'], $_GET['m']);

// Create an array of days which are "selected"
// Used for Week::build() below
$selectedDays = array (
    new Calendar_Day($_GET['y'], $_GET['m'], $_GET['d']),
    new Calendar_Day($_GET['y'], 12, 25),
    );

// Build the days in the month
$month->build($selectedDays);

// Construct strings for next/previous links
$PMonth = $month->prevMonth('object'); // Get previous month as object
$prev = $_SERVER['PHP_SELF'].'?y='.$PMonth->thisYear().'&m='.$PMonth->thisMonth().'&d='.$PMonth->thisDay();
$NMonth = $month->nextMonth('object');
$next = $_SERVER['PHP_SELF'].'?y='.$NMonth->thisYear().'&m='.$NMonth->thisMonth().'&d='.$NMonth->thisDay();

$thisDate = new Date($month->thisMonth('timestamp'));
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Calendar using PEAR::Date Engine </title>
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
</style>
</head>

<body>

<h2>Calendar using PEAR::Date Engine</h2>
<table class="calendar">
<caption>
<?php echo $thisDate->format('%B %Y'); ?>
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
while ($day = $month->fetch()) {
    // Build a link string for each day
    $link = $_SERVER['PHP_SELF'].
                '?y='.$day->thisYear().
                '&m='.$day->thisMonth().
                '&d='.$day->thisDay();

    // isFirst() to find start of week
    if ($day->isFirst())
        echo "<tr>\n";

    if ($day->isSelected()) {
       echo '<td class="selected">'.$day->thisDay().'</td>'."\n";
    } else if ($day->isEmpty()) {
        echo '<td>&nbsp;</td>'."\n";
    } else {
        echo '<td><a href="'.$link.'">'.$day->thisDay().'</a></td>'."\n";
    }

    // isLast() to find end of week
    if ($day->isLast()) {
        echo "</tr>\n";
    }
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
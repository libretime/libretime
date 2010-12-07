<?php
/**
* Description: Performs same behaviour as 2.php but uses Month::buildWeekDays()
* and is faster
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT','../../');
}
require_once CALENDAR_ROOT.'Month/Weekdays.php';
require_once CALENDAR_ROOT.'Day.php';

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('m');
if (!isset($_GET['d'])) $_GET['d'] = date('d');

// Build the month
$Month = new Calendar_Month_Weekdays($_GET['y'],$_GET['m']);

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
</style>
</head>

<body>

<?php
$selectedDays = array (
    new Calendar_Day($_GET['y'],$_GET['m'],$_GET['d']),
    new Calendar_Day($_GET['y'],12,25),
    );

// Build the days in the month
$Month->build($selectedDays);
?>
<h2>Built with Calendar_Month_Weekday::build()</h2>
<table class="calendar">
<caption>
<?php echo ( date('F Y',$Month->getTimeStamp())); ?>
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
while ( $Day = $Month->fetch() ) {

    // Build a link string for each day
    $link = $_SERVER['PHP_SELF'].
                '?y='.$Day->thisYear().
                '&m='.$Day->thisMonth().
                '&d='.$Day->thisDay();

    // isFirst() to find start of week
    if ( $Day->isFirst() )
        echo ( "<tr>\n" );

    if ( $Day->isSelected() ) {
       echo ( "<td class=\"selected\">".$Day->thisDay()."</td>\n" );
    } else if ( $Day->isEmpty() ) {
        echo ( "<td>&nbsp;</td>\n" );
    } else {
        echo ( "<td><a href=\"".$link."\">".$Day->thisDay()."</a></td>\n" );
    }

    // isLast() to find end of week
    if ( $Day->isLast() )
        echo ( "</tr>\n" );
}
?>
<tr>
<td>
<a href="<?php echo ($prev);?>" class="prevMonth"><< </a>
</td>
<td colspan="5">&nbsp;</td>
<td>
<a href="<?php echo ($next);?>" class="nextMonth"> >></a>
</td>
</tr>
</table>
<?php
echo ( '<p><b>Took: '.(getmicrotime()-$start).' seconds</b></p>' );
?>
</body>
</html>
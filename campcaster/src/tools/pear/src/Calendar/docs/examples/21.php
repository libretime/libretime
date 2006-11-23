<?php
/**
* Description: a complete year with numeric week numbers
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

if (!@include 'Calendar/Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}

require_once CALENDAR_ROOT.'Year.php';
require_once CALENDAR_ROOT.'Month/Weeks.php';

define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKS);

if (!isset($_GET['year'])) $_GET['year'] = date('Y');

$week_types = array(
    'n_in_year',
    'n_in_month',
);

if (!isset($_GET['week_type']) || !in_array($_GET['week_type'],$week_types) ) {
    $_GET['week_type'] = 'n_in_year';
}

$Year = new Calendar_Year($_GET['year']);

$Year->build();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> <?php echo $Year->thisYear(); ?> </title>
<style type="text/css">
body {
    font-family: Georgia, serif;
}
caption.year {
    font-weight: bold;
    font-size: 120%;
    font-color: navy;
}
caption.month {
    font-size: 110%;
    font-color: navy;
}
table.month {
    border: thin groove #800080
}
tr {
    vertical-align: top;
}
th, td {
    text-align: right;
    font-size: 70%;
}
#prev {
    float: left;
    font-size: 70%;
}
#next {
    float: right;
    font-size: 70%;
}
#week_type {
    float: none;
    font-size: 70%;
}
.weekNumbers {
    background-color: #e5e5f5;
    padding-right: 3pt;
}
</style>
</head>
<body>
<table>
<caption class="year">
<?php echo $Year->thisYear(); ?>
<div id="next">
<a href="?year=<?php echo $Year->nextYear(); ?>&week_type=<?php echo $_GET['week_type']; ?>">>></a>
</div>
<div id="prev">
<a href="?year=<?php echo $Year->prevYear(); ?>&week_type=<?php echo $_GET['week_type']; ?>"><<</a>
</div>
<div id="week_type">
<a href="?year=<?php echo $Year->thisYear(); ?>&week_type=n_in_year">Weeks by Year</a> : 
<a href="?year=<?php echo $Year->thisYear(); ?>&week_type=n_in_month">Weeks by Month</a> 
</div>
</caption>
<?php
$i = 0;
while ($Month = $Year->fetch()) {

    switch ($i) {
        case 0:
            echo "<tr>\n";
            break;
        case 3:
        case 6:
        case 9:
            echo "</tr>\n<tr>\n";
           break;
        case 12:
            echo "</tr>\n";
            break;
    }

    echo "<td>\n<table class=\"month\">\n";
    echo '<caption class="month">'.date('F', $Month->thisMonth(TRUE)).'</caption>';
    echo '<colgroup><col class="weekNumbers"><col span="7"></colgroup>'."\n";
    echo "<tr>\n<th>Week</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th>\n</tr>";
    $Month->build();
    while ($Week = $Month->fetch()) {
        echo "<tr>\n";
        echo '<td>'.$Week->thisWeek($_GET['week_type'])."</td>\n";
        $Week->build();

        while ($Day = $Week->fetch()) {
            if ($Day->isEmpty()) {
                echo "<td>&nbsp;</td>\n";
            } else {
                echo "<td>".$Day->thisDay()."</td>\n";
            }
        }
    }
    echo "</table>\n</td>\n";

    $i++;
}
?>
</table>
<p>Took: <?php echo ((getmicrotime()-$start)); ?></p>
</body>
</html>
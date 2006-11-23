<?php
/**
* Description: a complete year
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT','../../');
}

require_once CALENDAR_ROOT.'Year.php';

define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS);

if ( !isset($_GET['year']) ) $_GET['year'] = date('Y');

$Year = new Calendar_Year($_GET['year']);

$Year->build();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> <?php echo ( $Year->thisYear() ); ?> </title>
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
</style>
</head>
<body>
<table>
<caption class="year">
<?php echo ( $Year->thisYear() ); ?>
<div id="next">
<a href="?year=<?php echo ( $Year->nextYear() ); ?>">>></a>
</div>
<div id="prev">
<a href="?year=<?php echo ( $Year->prevYear() ); ?>"><<</a>
</div>
</caption>
<?php
$i = 0;
while ( $Month = $Year->fetch() ) {

    switch ( $i ) {
        case 0:
            echo ( "<tr>\n" );
            break;
        case 3:
        case 6:
        case 9:
            echo ( "</tr>\n<tr>\n" );
            break;
        case 12:
            echo ( "</tr>\n" );
            break;
    }

    echo ( "<td>\n<table class=\"month\">\n" );
    echo ( "<caption class=\"month\">".date('F',$Month->thisMonth(TRUE))."</caption>" );
    echo ( "<tr>\n<th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th>\n</tr>" );
    $Month->build();
    while ( $Day = $Month->fetch() ) {
        if ( $Day->isFirst() ) {
            echo ( "<tr>\n" );
        }
        if ( $Day->isEmpty() ) {
            echo ( "<td>&nbsp;</td>\n" );
        } else {
            echo ( "<td>".$Day->thisDay()."</td>\n" );
        }
        if ( $Day->isLast() ) {
            echo ( "</tr>\n" );
        }
    }
    echo ( "</table>\n</td>\n" );

    $i++;
}
?>
</table>
<p>Took: <?php echo ((getmicrotime()-$start)); ?></p>
</body>
</html>
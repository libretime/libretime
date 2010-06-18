<?php
/**
* Description: simple example on i18N
*/
if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT','../../');
}
require_once CALENDAR_ROOT.'Day.php';

$Day = & new Calendar_Day(2003,10,23);

setlocale (LC_TIME, "de_DE"); // Unix based (probably)
// setlocale (LC_TIME, "ge"); // Windows

echo ( strftime('%A %d %B %Y',$Day->getTimeStamp()));
?>
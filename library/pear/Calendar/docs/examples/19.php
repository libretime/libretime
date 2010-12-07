<?php
/**
* Description: demonstrates using the Weekday decorator
*/
if (!@include 'Calendar'.DIRECTORY_SEPARATOR.'Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Day.php';
require_once CALENDAR_ROOT.'Decorator/Weekday.php';

$Day = new Calendar_Day(date('Y'), date('n'),date('d'));
$WeekDay = & new Calendar_Decorator_Weekday($Day);
// $WeekDay->setFirstDay(0); // Make Sunday first Day

echo 'Yesterday: '.$WeekDay->prevWeekDay().'<br>';
echo 'Today: '.$WeekDay->thisWeekDay().'<br>';
echo 'Tomorrow: '.$WeekDay->nextWeekDay().'<br>';

$WeekDay->build();
echo 'Hours today:<br>';
while ( $Hour = $WeekDay->fetch() ) {
    echo $Hour->thisHour().'<br>';
}
?>
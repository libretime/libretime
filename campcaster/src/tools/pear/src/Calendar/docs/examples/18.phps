<?php
/**
* Description: demonstrates using the Wrapper decorator
*/

if (!@include 'Calendar/Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Month.php';
require_once CALENDAR_ROOT.'Decorator.php'; // Not really needed but added to help this make sense
require_once CALENDAR_ROOT.'Decorator/Wrapper.php';

class MyBoldDecorator extends Calendar_Decorator
{
    function MyBoldDecorator(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

    function thisDay()
    {
        return '<b>'.parent::thisDay().'</b>';
    }
}

$Month = new Calendar_Month(date('Y'), date('n'));

$Wrapper = & new Calendar_Decorator_Wrapper($Month);
$Wrapper->build();

echo '<h2>The Wrapper decorator</h2>';
echo '<i>Day numbers are rendered in bold</i><br /> <br />';
while ($DecoratedDay = $Wrapper->fetch('MyBoldDecorator')) {
    echo $DecoratedDay->thisDay().'<br />';
}
?>
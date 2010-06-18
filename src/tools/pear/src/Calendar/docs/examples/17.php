<?php
/**
* Description: demonstrates using the Textual decorator
*/

if (!@include 'Calendar'.DIRECTORY_SEPARATOR.'Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Day.php';
require_once CALENDAR_ROOT.'Month'.DIRECTORY_SEPARATOR.'Weekdays.php';
require_once CALENDAR_ROOT.'Decorator'.DIRECTORY_SEPARATOR.'Textual.php';

// Could change language like this
// setlocale (LC_TIME, "de_DE"); // Unix based (probably)
// setlocale (LC_TIME, "ge"); // Windows

echo "<hr>Calling: Calendar_Decorator_Textual::monthNames('long');<pre>";
print_r(Calendar_Decorator_Textual::monthNames('long'));
echo '</pre>';

echo "<hr>Calling: Calendar_Decorator_Textual::weekdayNames('two');<pre>";
print_r(Calendar_Decorator_Textual::weekdayNames('two'));
echo '</pre>';

echo "<hr>Creating: new Calendar_Day(date('Y'), date('n'), date('d'));<br />";
$Calendar = new Calendar_Day(date('Y'), date('n'), date('d'));

// Decorate
$Textual = & new Calendar_Decorator_Textual($Calendar);

echo '<hr>Previous month is: '.$Textual->prevMonthName('two').'<br />';
echo 'This month is: '.$Textual->thisMonthName('short').'<br />';
echo 'Next month is: '.$Textual->nextMonthName().'<br /><hr />';
echo 'Previous day is: '.$Textual->prevDayName().'<br />';
echo 'This day is: '.$Textual->thisDayName('short').'<br />';
echo 'Next day is: '.$Textual->nextDayName('one').'<br /><hr />';

echo "Creating: new Calendar_Month_Weekdays(date('Y'), date('n'), 6); - Saturday is first day of week<br />";
$Calendar = new Calendar_Month_Weekdays(date('Y'), date('n'), 6);

// Decorate
$Textual = & new Calendar_Decorator_Textual($Calendar);
?>
<p>Rendering calendar....</p>
<table>
<caption><?php echo $Textual->thisMonthName().' '.$Textual->thisYear(); ?></caption>
<tr>
<?php
$dayheaders = $Textual->orderedWeekdays('short');
foreach ($dayheaders as $dayheader) {
    echo '<th>'.$dayheader.'</th>';
}
?>
</tr>
<?php
$Calendar->build();
while ($Day = $Calendar->fetch()) {
    if ($Day->isFirst()) {
        echo "<tr>\n";
    }
    if ($Day->isEmpty()) {
        echo '<td>&nbsp;</td>';
    } else {
        echo '<td>'.$Day->thisDay().'</td>';
    }
    if ($Day->isLast()) {
        echo "</tr>\n";
    }
}
?>
</table>
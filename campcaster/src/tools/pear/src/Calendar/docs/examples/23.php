<?php
/**
* Description: demonstrates using the Textual util
*/

if (!@include 'Calendar'.DIRECTORY_SEPARATOR.'Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Day.php';
require_once CALENDAR_ROOT.'Month'.DIRECTORY_SEPARATOR.'Weekdays.php';
require_once CALENDAR_ROOT.'Util'.DIRECTORY_SEPARATOR.'Textual.php';

// Could change language like this
// setlocale (LC_TIME, "de_DE"); // Unix based (probably)
// setlocale (LC_TIME, "ge"); // Windows

echo "<hr>Calling: Calendar_Util_Textual::monthNames('long');<pre>";
print_r(Calendar_Util_Textual::monthNames('long'));
echo '</pre>';

echo "<hr>Calling: Calendar_Util_Textual::weekdayNames('two');<pre>";
print_r(Calendar_Util_Textual::weekdayNames('two'));
echo '</pre>';

echo "<hr>Creating: new Calendar_Day(date('Y'), date('n'), date('d'));<br />";
$Calendar = new Calendar_Day(date('Y'), date('n'), date('d'));

echo '<hr>Previous month is: '.Calendar_Util_Textual::prevMonthName($Calendar,'two').'<br />';
echo 'This month is: '.Calendar_Util_Textual::thisMonthName($Calendar,'short').'<br />';
echo 'Next month is: '.Calendar_Util_Textual::nextMonthName($Calendar).'<br /><hr />';
echo 'Previous day is: '.Calendar_Util_Textual::prevDayName($Calendar).'<br />';
echo 'This day is: '.Calendar_Util_Textual::thisDayName($Calendar,'short').'<br />';
echo 'Next day is: '.Calendar_Util_Textual::nextDayName($Calendar,'one').'<br /><hr />';

echo "Creating: new Calendar_Month_Weekdays(date('Y'), date('n'), 6); - Saturday is first day of week<br />";
$Calendar = new Calendar_Month_Weekdays(date('Y'), date('n'), 6);

?>
<p>Rendering calendar....</p>
<table>
<caption><?php echo Calendar_Util_Textual::thisMonthName($Calendar).' '.$Calendar->thisYear(); ?></caption>
<tr>
<?php
$dayheaders = Calendar_Util_Textual::orderedWeekdays($Calendar,'short');
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
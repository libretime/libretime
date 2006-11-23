<?php
/**
* Description: demonstrates using the Uri decorator
*/
if (!@include 'Calendar/Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Month/Weekdays.php';
require_once CALENDAR_ROOT.'Decorator/Uri.php';

if (!isset($_GET['jahr'])) $_GET['jahr'] = date('Y');
if (!isset($_GET['monat'])) $_GET['monat'] = date('m');

// Build the month
$Calendar = new Calendar_Month_Weekdays($_GET['jahr'], $_GET['monat']);

echo ( '<p>The current month is '
        .$Calendar->thisMonth().' of year '.$Calendar->thisYear().'</p>');

$Uri = & new Calendar_Decorator_Uri($Calendar);
$Uri->setFragments('jahr','monat');
// $Uri->setSeperator('/'); // Default is &
// $Uri->setScalar(); // Omit variable names
echo ( "<pre>Previous Uri:\t".$Uri->prev('month')."\n" );
echo ( "This Uri:\t".$Uri->this('month')."\n" );
echo ( "Next Uri:\t".$Uri->next('month')."\n</pre>" );
?>
<p>
<a href="<?php echo($_SERVER['PHP_SELF'].'?'.$Uri->prev('month'));?>">Prev</a> :
<a href="<?php echo($_SERVER['PHP_SELF'].'?'.$Uri->next('month'));?>">Next</a>
</p>
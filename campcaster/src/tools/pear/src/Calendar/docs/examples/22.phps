<?php
/**
* Description: demonstrates using the Uri util
*/
if (!@include 'Calendar/Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Month/Weekdays.php';
require_once CALENDAR_ROOT.'Util/Uri.php';

if (!isset($_GET['jahr'])) $_GET['jahr'] = date('Y');
if (!isset($_GET['monat'])) $_GET['monat'] = date('m');

// Build the month
$Calendar = new Calendar_Month_Weekdays($_GET['jahr'], $_GET['monat']);

echo ( '<p>The current month is '
        .$Calendar->thisMonth().' of year '.$Calendar->thisYear().'</p>');

$Uri = & new Calendar_Util_Uri('jahr','monat');
$Uri->setFragments('jahr','monat');

echo "\"Vector\" URIs<pre>";
echo ( "Previous Uri:\t".htmlentities($Uri->prev($Calendar, 'month'))."\n" );
echo ( "This Uri:\t".htmlentities($Uri->this($Calendar,  'month'))."\n" );
echo ( "Next Uri:\t".htmlentities($Uri->next($Calendar, 'month'))."\n" );
echo "</pre>";

// Switch to scalar URIs
$Uri->separator = '/'; // Default is &amp;
$Uri->scalar = true; // Omit variable names

echo "\"Scalar\" URIs<pre>";
echo ( "Previous Uri:\t".$Uri->prev($Calendar, 'month')."\n" );
echo ( "This Uri:\t".$Uri->this($Calendar,  'month')."\n" );
echo ( "Next Uri:\t".$Uri->next($Calendar, 'month')."\n" );
echo "</pre>";

// Restore the vector URIs
$Uri->separator = '&amp;';
$Uri->scalar = false;
?>
<p>
<a href="<?php echo($_SERVER['PHP_SELF'].'?'.$Uri->prev($Calendar, 'month'));?>">Prev</a> :
<a href="<?php echo($_SERVER['PHP_SELF'].'?'.$Uri->next($Calendar, 'month'));?>">Next</a>
</p>
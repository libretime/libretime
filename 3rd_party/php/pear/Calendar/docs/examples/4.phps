<?php
/**
* Description: shows how to perform validation with PEAR::Calendar
*/
function getmicrotime(){
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Second.php';

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('n');
if (!isset($_GET['d'])) $_GET['d'] = date('j');
if (!isset($_GET['h'])) $_GET['h'] = date('H');
if (!isset($_GET['i'])) $_GET['i'] = date('i');
if (!isset($_GET['s'])) $_GET['s'] = date('s');

$Unit = & new Calendar_Second($_GET['y'], $_GET['m'], $_GET['d'], $_GET['h'], $_GET['i'], $_GET['s']);

echo '<p><b>Result:</b> '.$Unit->thisYear().'-'.$Unit->thisMonth().'-'.$Unit->thisDay().
        ' '.$Unit->thisHour().':'.$Unit->thisMinute().':'.$Unit->thisSecond();
if ($Unit->isValid()) {
    echo ' is valid!</p>';
} else {
    $V= & $Unit->getValidator();
    echo ' is invalid:</p>';
    while ($error = $V->fetch()) {
        echo $error->toString() .'<br />';
    }
}
?>
<p>Enter a date / time to validate:</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
Year:   <input type="text" name="y" value="2039"><br />
Month:  <input type="text" name="m" value="13"><br />
Day:    <input type="text" name="d" value="32"><br />
Hour:   <input type="text" name="h" value="24"><br />
Minute: <input type="text" name="i" value="-1"><br />
Second: <input type="text" name="s" value="60"><br />
<input type="submit" value="Validate">
</form>
<p><b>Note:</b> Error messages can be controlled with the constants <code>CALENDAR_VALUE_TOOSMALL</code> and <code>CALENDAR_VALUE_TOOLARGE</code> - see <code>Calendar_Validator.php</code></p>

<?php echo '<p><b>Took: '.(getmicrotime()-$start).' seconds</b></p>'; ?>
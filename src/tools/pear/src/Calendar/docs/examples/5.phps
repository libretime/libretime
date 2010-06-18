<?php
/**
* Description: generating elements of a form with PEAR::Calendar, using
* selections as well as validating the submission
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
require_once CALENDAR_ROOT.'Month.php';
require_once CALENDAR_ROOT.'Day.php';
require_once CALENDAR_ROOT.'Hour.php';
require_once CALENDAR_ROOT.'Minute.php';
require_once CALENDAR_ROOT.'Second.php';

// Initialize if not set
if (!isset($_POST['y'])) $_POST['y'] = date('Y');
if (!isset($_POST['m'])) $_POST['m'] = date('n');
if (!isset($_POST['d'])) $_POST['d'] = date('j');
if (!isset($_POST['h'])) $_POST['h'] = date('H');
if (!isset($_POST['i'])) $_POST['i'] = date('i');
if (!isset($_POST['s'])) $_POST['s'] = date('s');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Select and Update </title>
</head>
<body>
<h1>Select and Update</h1>
<?php
if ( isset($_POST['update']) ) {
    $Second = & new Calendar_Second($_POST['y'],$_POST['m'],$_POST['d'],$_POST['h'],$_POST['i'],$_POST['s']);
    if ( !$Second->isValid() ) {
        $V= & $Second->getValidator();
        echo ('<p>Validation failed:</p>' );
        while ( $error = $V->fetch() ) {
            echo ( $error->toString() .'<br>' );
        }
    } else {
        echo ('<p>Validation success.</p>' );
        echo ( '<p>New timestamp is: '.$Second->getTimeStamp().' which could be used to update a database, for example');
    }
} else {
$Year = new Calendar_Year($_POST['y']);
$Month = new Calendar_Month($_POST['y'],$_POST['m']);
$Day = new Calendar_Day($_POST['y'],$_POST['m'],$_POST['d']);
$Hour = new Calendar_Hour($_POST['y'],$_POST['m'],$_POST['d'],$_POST['h']);
$Minute = new Calendar_Minute($_POST['y'],$_POST['m'],$_POST['d'],$_POST['h'],$_POST['i']);
$Second = new Calendar_Second($_POST['y'],$_POST['m'],$_POST['d'],$_POST['h'],$_POST['i'],$_POST['s']);
?>
<p><b>Set the alarm clock</p></p>
<form action="<?php echo ( $_SERVER['PHP_SELF'] ); ?>" method="post">
Year: <input type="text" name="y" value="<?php echo ( $_POST['y'] ); ?>" size="4">&nbsp;
Month:<select name="m">
<?php
$selection = array($Month);
$Year->build($selection);
while ( $Child = & $Year->fetch() ) {
    if ( $Child->isSelected() ) {
        echo ( "<option value=\"".$Child->thisMonth()."\" selected>".$Child->thisMonth()."\n" );
    } else {
        echo ( "<option value=\"".$Child->thisMonth()."\">".$Child->thisMonth()."\n" );
    }
}
?>
</select>&nbsp;
Day:<select name="d">
<?php
$selection = array($Day);
$Month->build($selection);
while ( $Child = & $Month->fetch() ) {
    if ( $Child->isSelected() ) {
        echo ( "<option value=\"".$Child->thisDay()."\" selected>".$Child->thisDay()."\n" );
    } else {
        echo ( "<option value=\"".$Child->thisDay()."\">".$Child->thisDay()."\n" );
    }
}
?>
</select>&nbsp;
Hour:<select name="h">
<?php
$selection = array($Hour);
$Day->build($selection);
while ( $Child = & $Day->fetch() ) {
    if ( $Child->isSelected() ) {
        echo ( "<option value=\"".$Child->thisHour()."\" selected>".$Child->thisHour()."\n" );
    } else {
        echo ( "<option value=\"".$Child->thisHour()."\">".$Child->thisHour()."\n" );
    }
}
?>
</select>&nbsp;
Minute:<select name="i">
<?php
$selection = array($Minute);
$Hour->build($selection);
while ( $Child = & $Hour->fetch() ) {
    if ( $Child->isSelected() ) {
        echo ( "<option value=\"".$Child->thisMinute()."\" selected>".$Child->thisMinute()."\n" );
    } else {
        echo ( "<option value=\"".$Child->thisMinute()."\">".$Child->thisMinute()."\n" );
    }
}
?>
</select>&nbsp;
Second:<select name="s">
<?php
$selection = array($Second);
$Minute->build($selection);
while ( $Child = & $Minute->fetch() ) {
    if ( $Child->isSelected() ) {
        echo ( "<option value=\"".$Child->thisSecond()."\" selected>".$Child->thisSecond()."\n" );
    } else {
        echo ( "<option value=\"".$Child->thisSecond()."\">".$Child->thisSecond()."\n" );
    }
}
?>
</select>&nbsp;
<input type="submit" name="update" value="Set Alarm"><br>
<?php
}
?>
<?php echo ( '<p><b>Took: '.(getmicrotime()-$start).' seconds</b></p>' ); ?>
</body>
</html>
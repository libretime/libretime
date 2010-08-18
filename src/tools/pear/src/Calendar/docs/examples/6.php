<?php
/**
* Description: A "personal planner" with some WML for fun
* Note this is done the stupid way - a giant if/else for WML or HTML
* could be greatly simplified with some HTML/WML rendering classes...
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT','../../');
}
require_once CALENDAR_ROOT.'Month/Weekdays.php';
require_once CALENDAR_ROOT.'Day.php';

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('n');
if (!isset($_GET['d'])) $_GET['d'] = date('j');

$Month = & new Calendar_Month_Weekdays($_GET['y'],$_GET['m']);
$Day = & new Calendar_Day($_GET['y'],$_GET['m'],$_GET['d']);
$selection = array($Day);

#-----------------------------------------------------------------------------#
if ( isset($_GET['mime']) && $_GET['mime']=='wml' ) {
    header ('Content-Type: text/vnd.wap.wml');
    echo ( '<?xml version="1.0"?>' );
?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
<wml>
<big><strong>Personal Planner Rendered with WML</strong></big>
<?php
if ( isset($_GET['viewday']) ) {
?>
<p><strong>Viewing <?php echo ( date('l, jS of F, Y',$Day->getTimeStamp()) ); ?></strong></p>
<p>
<anchor>
Back to Month View
<go href="<?php
echo ( "?y=".$Day->thisYear()."&amp;m=".
        $Day->thisMonth()."&amp;d=".$Day->thisDay()."&amp;mime=wml" );
?>"/>
</anchor>
</p>
<table>
<?php
    $Day->build();
    while ( $Hour = & $Day->fetch() ) {
        echo ( "<tr>\n" );
        echo ( "<td>".date('g a',$Hour->getTimeStamp())."</td><td>Free time!</td>\n" );
        echo ( "</tr>\n" );
    }
?>
</table>
<?php
} else {
?>
<p><strong><?php echo ( date('F Y',$Month->getTimeStamp()) ); ?></strong></p>
<table>
<tr>
<td>M</td><td>T</td><td>W</td><td>T</td><td>F</td><td>S</td><td>S</td>
</tr>
<?php
$Month->build($selection);
while ( $Day = $Month->fetch() ) {
    if ( $Day->isFirst() ) {
        echo ( "<tr>\n" );
    }
    if ( $Day->isEmpty() ) {
        echo ( "<td></td>\n" );
    } else if ( $Day->isSelected() ) {
        echo ( "<td><anchor><strong><u>".$Day->thisDay()."</u></strong>\n<go href=\"".$_SERVER['PHP_SELF']."?viewday=true&amp;y=".
            $Day->thisYear()."&amp;m=".$Day->thisMonth()."&amp;d=".$Day->thisDay().
            "&amp;mime=wml\" />\n</anchor></td>\n" );
    } else {
        echo ( "<td><anchor>".$Day->thisDay()."\n<go href=\"?viewday=true&amp;y=".
            $Day->thisYear()."&amp;m=".$Day->thisMonth()."&amp;d=".$Day->thisDay().
            "&amp;mime=wml\" /></anchor></td>\n" );
    }
    if ( $Day->isLast() ) {
        echo ( "</tr>\n" );
    }
}
?>
<tr>
<td>
<anchor>
&lt;&lt;
<go href="<?php
echo ( "?y=".$Month->thisYear()."&amp;m=".
        $Month->prevMonth()."&amp;d=".$Month->thisDay()."&amp;mime=wml" );
?>"/>
</anchor>
</td>
<td></td><td></td><td></td><td></td><td></td>
<td>
<anchor>
&gt;&gt;
<go href="<?php
echo ( "?y=".$Month->thisYear()."&amp;m=".
        $Month->nextMonth()."&amp;d=".$Month->thisDay()."&amp;mime=wml" );
?>"/>
</anchor>
</td>
</tr>
</table>

<?php
}
?>
<p><a href="<?php echo ( $_SERVER['PHP_SELF'] ); ?>">Back to HTML</a></p>
<?php echo ( '<p>Took: '.(getmicrotime()-$start).' seconds</p>' ); ?>
</wml>
<?php
#-----------------------------------------------------------------------------#
} else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> HTML (+WML) Personal Planner </title>
</head>
<body>
<h1>Personal Planner Rendered with HTML</h1>
<p>To view in WML, click <a href="<?php echo ( $_SERVER['PHP_SELF'] ); ?>?mime=wml">here</a> or place a ?mime=wml at the end of any URL.
Note that <a href="http://www.opera.com/download">Opera</a> supports WML natively and Mozilla / Firefox has the WMLBrowser
plugin: <a href="http://wmlbrowser.mozdev.org">wmlbrowser.mozdev.org</a></p>
<?php
if ( isset($_GET['viewday']) ) {
?>
<p><strong>Viewing <?php echo ( date('l, jS of F, Y',$Day->getTimeStamp()) ); ?></strong></p>
<p>
<anchor>
<a href="<?php
echo ( "?y=".$Day->thisYear()."&amp;m=".
        $Day->thisMonth()."&amp;d=".$Day->thisDay());
?>">Back to Month View</a>
</p>
<table>
<?php
    $Day->build();
    while ( $Hour = & $Day->fetch() ) {
        echo ( "<tr>\n" );
        echo ( "<td>".date('g a',$Hour->getTimeStamp())."</td><td>Free time!</td>\n" );
        echo ( "</tr>\n" );
    }
?>
</table>
<?php
} else {
?>
<p><strong><?php echo ( date('F Y',$Month->getTimeStamp()) ); ?></strong></p>
<table>
<tr>
<td>M</td><td>T</td><td>W</td><td>T</td><td>F</td><td>S</td><td>S</td>
</tr>
<?php
$Month->build($selection);
while ( $Day = $Month->fetch() ) {
    if ( $Day->isFirst() ) {
        echo ( "<tr>\n" );
    }
    if ( $Day->isEmpty() ) {
        echo ( "<td></td>\n" );
    } else if ( $Day->isSelected() ) {
        echo ( "<td><a href=\"".$_SERVER['PHP_SELF']."?viewday=true&amp;y=".
            $Day->thisYear()."&amp;m=".$Day->thisMonth()."&amp;d=".$Day->thisDay().
            "&amp;wml\"><strong><u>".$Day->thisDay()."</u></strong></a></td>\n" );
    } else {
        echo ( "<td><a href=\"".$_SERVER['PHP_SELF']."?viewday=true&amp;y=".
            $Day->thisYear()."&amp;m=".$Day->thisMonth()."&amp;d=".$Day->thisDay().
            "\">".$Day->thisDay()."</a></td>\n" );
    }
    if ( $Day->isLast() ) {
        echo ( "</tr>\n" );
    }
}
?>
<tr>
<td>
<a href="<?php
echo ( "?y=".$Month->thisYear()."&amp;m=".
        $Month->prevMonth()."&amp;d=".$Month->thisDay() );
?>">
&lt;&lt;</a>
</td>
<td></td><td></td><td></td><td></td><td></td>
<td>
<a href="<?php
echo ( "?y=".$Month->thisYear()."&amp;m=".
        $Month->nextMonth()."&amp;d=".$Month->thisDay() );
?>">&gt;&gt;</a>
</td>
</tr>
</table>

<?php
}
?>


<?php echo ( '<p><b>Took: '.(getmicrotime()-$start).' seconds</b></p>' ); ?>
</body>
</html>
<?php
}
?>
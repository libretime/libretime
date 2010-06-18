<?php
/**
* Description: demonstrates a decorator to provide simple output formatting
* on the month while still allowing the days to be accessed via the decorator
* In practice you _wouldn't_ do this - each decorator comes with a performance
* hit for extra method calls. For this example some simple functions could help
* format the month while the days are accessed via the normal Month object
*/
if ( !@include 'Calendar/Calendar.php' ) {
    define('CALENDAR_ROOT','../../');
}
require_once CALENDAR_ROOT.'Month/Weekdays.php';
require_once CALENDAR_ROOT.'Decorator.php';

// Decorate a Month with methods to improve formatting
class MonthDecorator extends Calendar_Decorator {
    /**
    * @param Calendar_Month
    */
    function MonthDecorator(& $Month) {
        parent::Calendar_Decorator($Month);
    }
    /**
    * Override the prevMonth method to format the output
    */
    function prevMonth() {
        $prevStamp = parent::prevMonth(TRUE);
        // Build the URL for the previous month
        return $_SERVER['PHP_SELF'].'?y='.date('Y',$prevStamp).
            '&m='.date('n',$prevStamp).'&d='.date('j',$prevStamp);
    }
    /**
    * Override the thisMonth method to format the output
    */
    function thisMonth() {
        $thisStamp = parent::thisMonth(TRUE);
        // A human readable string from this month
        return date('F Y',$thisStamp);
    }
    /**
    * Override the nextMonth method to format the output
    */
    function nextMonth() {
        $nextStamp = parent::nextMonth(TRUE);
        // Build the URL for next month
        return $_SERVER['PHP_SELF'].'?y='.date('Y',$nextStamp).
            '&m='.date('n',$nextStamp).'&d='.date('j',$nextStamp);
    }
}

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('n');

// Creata a month as usual
$Month = new Calendar_Month_Weekdays($_GET['y'],$_GET['m']);

// Pass it to the decorator and use the decorator from now on...
$MonthDecorator = new MonthDecorator($Month);
$MonthDecorator->build();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> A Simple Decorator </title>
</head>
<body>
<h1>A Simple Decorator</h1>
<table>
<caption><?php echo ( $MonthDecorator->thisMonth() ); ?></caption>
<?php
while ( $Day = $MonthDecorator->fetch() ) {
    if ( $Day->isFirst() ) {
        echo ( "\n<tr>\n" );
    }
    if ( $Day->isEmpty() ) {
        echo ( "<td>&nbsp;</td>" );
    } else {
        echo ( "<td>".$Day->thisDay()."</td>" );
    }
    if ( $Day->isLast() ) {
        echo ( "\n</tr>\n" );
    }
}
?>
<tr>
<td><a href="<?php echo ($MonthDecorator->prevMonth()); ?>">Prev</a></td>
<td colspan="5">&nbsp;</td>
<td><a href="<?php echo ($MonthDecorator->nextMonth()); ?>">Next</a></td>
</tr>
</table>
</body>
</html>
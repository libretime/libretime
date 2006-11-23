<?php
/**
* Shows more on how a week can be used
*/
function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$start = getmicrotime();

if (!@include 'Calendar/Calendar.php') {
    define('CALENDAR_ROOT', '../../');
}
require_once CALENDAR_ROOT.'Week.php';

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('m');
if (!isset($_GET['d'])) $_GET['d'] = 1;

// Build the month
$Week = new Calendar_Week($_GET['y'], $_GET['m'], $_GET['d']);
/*
$Validator = $Week->getValidator();
if (!$Validator->isValidWeek()) {
    die ('Please enter a valid week!');
}
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Paging Weeks </title>
</head>
<body>
<h1>Paging Weeks</h1>
<h2>Week: <?php echo $Week->thisWeek().' '.date('F Y',$Week->thisMonth(true)); ?></h2>
<?php
$Week->build();
while ($Day = $Week->fetch()) {
    echo '<p>'.date('jS F',$Day->thisDay(true))."</p>\n";
}
$days = $Week->fetchAll();

$prevWeek = $Week->prevWeek('array');
$prevWeekLink = $_SERVER['PHP_SELF'].
                    '?y='.$prevWeek['year'].
                    '&m='.$prevWeek['month'].
                    '&d='.$prevWeek['day'];

$nextWeek = $Week->nextWeek('array');
$nextWeekLink = $_SERVER['PHP_SELF'].
                    '?y='.$nextWeek['year'].
                    '&m='.$nextWeek['month'].
                    '&d='.$nextWeek['day'];
?>
<p><a href="<?php echo $prevWeekLink; ?>"><<</a> | <a href="<?php echo $nextWeekLink; ?>">>></a></p>
</body>
</html>
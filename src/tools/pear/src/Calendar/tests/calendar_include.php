<?php
// $Id: calendar_include.php,v 1.4 2004/08/16 12:56:10 hfuecks Exp $
if ( !@include 'Calendar/Calendar.php' ) {
    @define('CALENDAR_ROOT','../');
}
require_once(CALENDAR_ROOT . 'Year.php');
require_once(CALENDAR_ROOT . 'Month.php');
require_once(CALENDAR_ROOT . 'Day.php');
require_once(CALENDAR_ROOT . 'Week.php');
require_once(CALENDAR_ROOT . 'Hour.php');
require_once(CALENDAR_ROOT . 'Minute.php');
require_once(CALENDAR_ROOT . 'Second.php');
require_once(CALENDAR_ROOT . 'Month.php');
require_once(CALENDAR_ROOT . 'Decorator.php');
require_once(CALENDAR_ROOT . 'Month/Weekdays.php');
require_once(CALENDAR_ROOT . 'Month/Weeks.php');
require_once(CALENDAR_ROOT . 'Validator.php');
require_once(CALENDAR_ROOT . 'Engine/Interface.php');
require_once(CALENDAR_ROOT . 'Engine/UnixTs.php');
require_once(CALENDAR_ROOT . 'Engine/PearDate.php');
require_once(CALENDAR_ROOT . 'Table/Helper.php');
require_once(CALENDAR_ROOT . 'Decorator/Textual.php');
require_once(CALENDAR_ROOT . 'Decorator/Uri.php');
require_once(CALENDAR_ROOT . 'Decorator/Weekday.php');
require_once(CALENDAR_ROOT . 'Decorator/Wrapper.php');
require_once(CALENDAR_ROOT . 'Util/Uri.php');
require_once(CALENDAR_ROOT . 'Util/Textual.php');
?>
<?php
require_once "../application/configs/conf.php";
require_once "ShowService.php";
require_once "ShowServiceData.php";

class ShowServiceUnitTest extends PHPUnit_Framework_TestCase
{
    protected $_showService;

    public function setUp()
    {
        $this->_showService = new Application_Service_ShowService();
    }

    public function testFormatShowDurationOneHour()
    {
        $duration = Application_Service_ShowService::formatShowDuration("01h 00m");
        $this->assertEquals("01:00", $duration);
    }

    public function testFormatShowDurationLessThanOneHour()
    {
        $duration = Application_Service_ShowService::formatShowDuration("00h 05m");
        $this->assertEquals("00:05", $duration);
    }

    public function testFormatShowDurationMoreTHanOneHour()
    {
        $duration = Application_Service_ShowService::formatShowDuration("03h 55m");
        $this->assertEquals("03:55", $duration);
    }

    public function testCalculateEndDateNoRepeat()
    {
        $end = $this->_showService->calculateEndDate(ShowServiceData::getNoRepeatNoRRData());
        $this->assertEquals(null, $end);
    }

    public function testCalculateEndDateRepeatWithEndDate()
    {
        $end = $this->_showService->calculateEndDate(ShowServiceData::getWeeklyRepeatWithEndNoRRData());
        $this->assertEquals(new DateTime("2016-01-27", new DateTimeZone("UTC")), $end);
    }

    public function testCalculateEndDateRepeatWithNoEndDate()
    {
        $end = $this->_showService->calculateEndDate(ShowServiceData::getWeeklyRepeatNoEndNoRRData());
        $this->assertEquals(null, $end);
    }

    public function testGetMonthlyWeeklyRepeatIntervalFirstWeek()
    {
        $repeatInterval = $this->_showService->getMonthlyWeeklyRepeatInterval(
            new DateTime("2016-01-01"), new DateTimeZone("UTC"));
        $this->assertEquals(array("first", "Friday"), $repeatInterval);
    }

    public function testGetMonthlyWeeklyRepeatIntervalSecondWeek()
    {
        $repeatInterval = $this->_showService->getMonthlyWeeklyRepeatInterval(
            new DateTime("2016-01-12"), new DateTimeZone("UTC"));
        $this->assertEquals(array("second", "Tuesday"), $repeatInterval);
    }

    public function testGetMonthlyWeeklyRepeatIntervalThirdWeek()
    {
        $repeatInterval = $this->_showService->getMonthlyWeeklyRepeatInterval(
            new DateTime("2016-01-18"), new DateTimeZone("UTC"));
        $this->assertEquals(array("third", "Monday"), $repeatInterval);
    }

    public function testGetMonthlyWeeklyRepeatIntervalFifthWeek()
    {
        $repeatInterval = $this->_showService->getMonthlyWeeklyRepeatInterval(
            new DateTime("2016-01-30"), new DateTimeZone("UTC"));
        $this->assertEquals(array("fifth", "Saturday"), $repeatInterval);
    }

    public function testGetMonthlyWeeklyRepeatIntervalFourthWeek()
    {
        $repeatInterval = $this->_showService->getMonthlyWeeklyRepeatInterval(
            new DateTime("2016-01-28"), new DateTimeZone("UTC"));
        $this->assertEquals(array("fourth", "Thursday"), $repeatInterval);
    }

    public function testGetNextMonthlyMonthlyRepeatDate()
    {
        $next = $this->_showService->getNextMonthlyMonthlyRepeatDate(
            new DateTime("2016-01-01"), "UTC", "00:00");
        $this->assertEquals(new DateTime("2016-02-01", new DateTimeZone("UTC")), $next);
    }

    public function testGetNextMonthlyMonthlyRepeatDateSkipsMonth()
    {
        $next = $this->_showService->getNextMonthlyMonthlyRepeatDate(
            new DateTime("2016-01-30"), "UTC", "00:00");
        $this->assertEquals(new DateTime("2016-03-30", new DateTimeZone("UTC")), $next);
    }

    public function testCreateUTCStartEndDateTime()
    {
        
    }
}
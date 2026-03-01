<?php

require_once '../application/configs/conf.php';

/**
 * @internal
 *
 * @coversNothing
 */
class ShowServiceUnitTest extends PHPUnit_Framework_TestCase
{
    // needed for accessing private methods
    protected $_reflectionOfShowService;

    protected $_showService;

    public function setUp()
    {
        $this->_reflectionOfShowService = new ReflectionClass('Application_Service_ShowService');

        $this->_showService = new Application_Service_ShowService();
    }

    public function testFormatShowDuration()
    {
        $duration = Application_Service_ShowService::formatShowDuration('01h 00m');
        $this->assertEquals('01:00', $duration);

        $duration = Application_Service_ShowService::formatShowDuration('00h 05m');
        $this->assertEquals('00:05', $duration);

        $duration = Application_Service_ShowService::formatShowDuration('03h 55m');
        $this->assertEquals('03:55', $duration);
    }

    public function testCalculateEndDate()
    {
        $method = $this->_reflectionOfShowService->getMethod('calculateEndDate');
        $method->setAccessible(true);

        $end = $method->invokeArgs($this->_showService, [ShowServiceData::getNoRepeatNoRRData()]);
        $this->assertEquals(null, $end);

        $end = $method->invokeArgs($this->_showService, [ShowServiceData::getWeeklyRepeatWithEndNoRRData()]);
        $this->assertEquals(new DateTime('2044-01-27', new DateTimeZone('UTC')), $end);

        $end = $method->invokeArgs($this->_showService, [ShowServiceData::getWeeklyRepeatNoEndNoRRData()]);
        $this->assertEquals(null, $end);
    }

    public function testGetMonthlyWeeklyRepeatInterval()
    {
        $method = $this->_reflectionOfShowService->getMethod('getMonthlyWeeklyRepeatInterval');
        $method->setAccessible(true);

        $repeatInterval = $method->invokeArgs($this->_showService, [new DateTime('2044-01-01'), new DateTimeZone('UTC')]);
        $this->assertEquals(['first', 'Friday'], $repeatInterval);

        $repeatInterval = $method->invokeArgs($this->_showService, [new DateTime('2044-01-12'), new DateTimeZone('UTC')]);
        $this->assertEquals(['second', 'Tuesday'], $repeatInterval);

        $repeatInterval = $method->invokeArgs($this->_showService, [new DateTime('2044-01-18'), new DateTimeZone('UTC')]);
        $this->assertEquals(['third', 'Monday'], $repeatInterval);

        $repeatInterval = $method->invokeArgs($this->_showService, [new DateTime('2044-01-28'), new DateTimeZone('UTC')]);
        $this->assertEquals(['fourth', 'Thursday'], $repeatInterval);

        $repeatInterval = $method->invokeArgs($this->_showService, [new DateTime('2044-01-30'), new DateTimeZone('UTC')]);
        $this->assertEquals(['fifth', 'Saturday'], $repeatInterval);
    }

    public function testGetNextMonthlyMonthlyRepeatDate()
    {
        $method = $this->_reflectionOfShowService->getMethod('getNextMonthlyMonthlyRepeatDate');
        $method->setAccessible(true);

        $next = $method->invokeArgs($this->_showService, [new DateTime('2044-01-01'), 'UTC', '00:00']);
        $this->assertEquals(new DateTime('2044-02-01', new DateTimeZone('UTC')), $next);

        $next = $method->invokeArgs($this->_showService, [new DateTime('2044-01-30'), 'UTC', '00:00']);
        $this->assertEquals(new DateTime('2044-03-30', new DateTimeZone('UTC')), $next);
    }

    public function testGetNextMonthlyWeeklyRepeatDate()
    {
        $method = $this->_reflectionOfShowService->getMethod('getNextMonthlyWeeklyRepeatDate');
        $method->setAccessible(true);

        $next = $method->invokeArgs($this->_showService, [
            new DateTime('2044-02-01'), 'UTC', '00:00', 'first', 'Friday',
        ]);
        $this->assertEquals(new DateTime('2044-02-05', new DateTimeZone('UTC')), $next);

        $next = $method->invokeArgs($this->_showService, [
            new DateTime('2044-02-01'), 'UTC', '00:00', 'fifth', 'Saturday',
        ]);
        $this->assertEquals(new DateTime('2044-04-30', new DateTimeZone('UTC')), $next);

        $next = $method->invokeArgs($this->_showService, [
            new DateTime('2044-02-01'), 'UTC', '00:00', 'fourth', 'Monday',
        ]);
        $this->assertEquals(new DateTime('2044-02-22', new DateTimeZone('UTC')), $next);
    }

    public function testCreateUTCStartEndDateTime()
    {
        $method = $this->_reflectionOfShowService->getMethod('createUTCStartEndDateTime');
        $method->setAccessible(true);

        $utcTimezone = new DateTimeZone('UTC');

        // America/Toronto
        $localStartDT = new DateTime('2044-01-01 06:30', new DateTimeZone('America/Toronto'));
        $localEndDT = new DateTime('2044-01-01 07:30', new DateTimeZone('America/Toronto'));

        $dt = $method->invokeArgs($this->_showService, [$localStartDT, '01:00']);
        $this->assertEquals([
            $localStartDT->setTimezone($utcTimezone), $localEndDT->setTimezone($utcTimezone),
        ], $dt);

        // America/Toronto with offset for rebroadcast shows
        $localStartDT = new DateTime('2044-01-01 06:30', new DateTimeZone('America/Toronto'));
        $localEndDT = new DateTime('2044-01-01 07:30', new DateTimeZone('America/Toronto'));

        $localRebroadcastStartDT = new DateTime('2044-01-02 06:30', new DateTimeZone('America/Toronto'));
        $localRebroadcastEndDT = new DateTime('2044-01-02 07:30', new DateTimeZone('America/Toronto'));

        $dt = $method->invokeArgs($this->_showService, [
            $localStartDT, '01:00',
            ['days' => '1', 'hours' => '06', 'mins' => '30'],
        ]);
        $this->assertEquals([
            $localRebroadcastStartDT->setTimezone($utcTimezone), $localRebroadcastEndDT->setTimezone($utcTimezone),
        ], $dt);

        // Australia/Brisbane
        $localStartDT = new DateTime('2044-01-01 06:30', new DateTimeZone('Australia/Brisbane'));
        $localEndDT = new DateTime('2044-01-01 07:30', new DateTimeZone('Australia/Brisbane'));

        $dt = $method->invokeArgs($this->_showService, [$localStartDT, '01:00']);
        $this->assertEquals([
            $localStartDT->setTimezone($utcTimezone), $localEndDT->setTimezone($utcTimezone),
        ], $dt);

        // America/Vancouver
        $localStartDT = new DateTime('2044-01-01 06:30', new DateTimeZone('America/Vancouver'));
        $localEndDT = new DateTime('2044-01-01 07:30', new DateTimeZone('America/Vancouver'));

        $dt = $method->invokeArgs($this->_showService, [$localStartDT, '01:00']);
        $this->assertEquals([
            $localStartDT->setTimezone($utcTimezone), $localEndDT->setTimezone($utcTimezone),
        ], $dt);
    }
}

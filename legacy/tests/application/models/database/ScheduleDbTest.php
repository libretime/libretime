<?php

declare(strict_types=1);

/**
 * @internal
 *
 * @coversNothing
 */
class ScheduleDbTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;

    public function setUp()
    {
        TestHelper::installTestDatabase();

        $this->appBootstrap();

        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $this->application->bootstrap();
    }

    public function getConnection()
    {
        if ($this->_connectionMock == null) {
            $config = TestHelper::getDbZendConfig();

            $connection = Zend_Db::factory('pdo_pgsql', $config);

            $this->_connectionMock = $this->createZendDbConnection(
                $connection,
                'airtimeunittests'
            );
            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }

        return $this->_connectionMock;
    }

    /* Defines how the initial state of the database should look before each test is executed
     * Called once during setUp() and gets recreated for each new test
     */
    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            __DIR__ . '/datasets/seed_schedule.yml'
        );
    }

    public function testCheckOverlappingShows()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getOverlappingShowCheckTestData();
        $showService = new Application_Service_ShowService(null, $data);

        // Create shows to test against
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days order by id');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, modified_instance from cc_show_instances order by id');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        // Make sure shows were created correctly
        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_checkOverlappingShows.yml'),
            $ds
        );

        $utcTimezone = new DateTimeZone('UTC');

        /** Test that overlapping check works when creating a new show */
        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-02-01 00:00:00', $utcTimezone),
            new DateTime('2014-02-01 01:00:00', $utcTimezone)
        );
        $this->assertEquals($overlapping, false);

        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-01-05 00:00:00', $utcTimezone),
            new DateTime('2014-01-05 02:00:00', $utcTimezone)
        );
        $this->assertEquals($overlapping, true);

        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-01-05 01:00:00', $utcTimezone),
            new DateTime('2014-01-05 02:00:00', $utcTimezone)
        );
        $this->assertEquals($overlapping, false);

        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-01-31 00:30:00', $utcTimezone),
            new DateTime('2014-01-31 01:30:00', $utcTimezone)
        );
        $this->assertEquals($overlapping, true);

        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-01-20 23:55:00', $utcTimezone),
            new DateTime('2014-01-21 00:00:05', $utcTimezone)
        );
        $this->assertEquals($overlapping, true);

        /** Test overlapping check works when editing an entire show */
        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-01-05 00:00:00', $utcTimezone),
            new DateTime('2014-01-05 02:00:00', $utcTimezone),
            true,
            null,
            1
        );
        $this->assertEquals($overlapping, false);

        /** Delete a repeating instance, create a new show in it's place and
         *  test if we can modify the repeating show after */
        $ccShowInstance = CcShowInstancesQuery::create()->findPk(1);
        $ccShowInstance->setDbModifiedInstance(true)->save();

        $newShowData = ShowServiceData::getNoRepeatNoRRData();
        $newShowData['add_show_start_date'] = '2014-01-05';
        $newShowData['add_show_end_date_no_repeat'] = '2014-01-05';
        $newShowData['add_show_end_date'] = '2014-01-05';

        $showService->addUpdateShow($newShowData);

        $overlapping = Application_Model_Schedule::checkOverlappingShows(
            new DateTime('2014-01-06 00:00:00', $utcTimezone),
            new DateTime('2014-01-06 00:30:00', $utcTimezone),
            true,
            null,
            1
        );
        $this->assertEquals($overlapping, false);
    }
}

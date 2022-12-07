<?php

declare(strict_types=1);

require_once '../application/configs/conf.php';

/*
 * All dates in the xml files are hard coded and in the year 2044
 * It would have been nice to use 'PHPUnit/Extensions/Database/DataSet/ReplacementDataSet.php'
 * to be able to use variables in the xml dataset files so dates can be relative. This proved
 * not practical for Airtime; For repeating shows, the start times are always varying and would
 * require functions that calculate the start and end dates, and the next populate date. The
 * tests would be performing the same work as the application and require tests themselves.
 */
/**
 * @internal
 *
 * @coversNothing
 */
class ShowServiceDbTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;
    // private $_nowDT;

    public function setUp()
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();

        // $this->_nowDT = new DateTime("now", new DateTimeZone("UTC"));

        parent::setUp();
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
            __DIR__ . '/datasets/seed_show_service.yml'
        );
    }

    public function testCcShowInsertedIntoDatabase()
    {
        $showService = new Application_Service_ShowService();

        $data = [
            'add_show_id' => -1,
            'add_show_name' => 'test show',
            'add_show_description' => null,
            'add_show_url' => null,
            'add_show_genre' => null,
            'add_show_color' => 'ffffff',
            'add_show_background_color' => '364492',
            'cb_airtime_auth' => false,
            'cb_custom_auth' => false,
            'custom_username' => null,
            'custom_password' => null,
            'add_show_linked' => false,
            'add_show_has_autoplaylist' => 0,
            'add_show_autoplaylist_id' => null,
            'add_show_autoplaylist_repeat' => 0,
        ];

        $showService->setCcShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_ccShowInsertedIntoDatabase.yml'),
            $ds
        );
    }

    /* Tests that a non-repeating, non-record, and non-rebroadcast show
     * gets created properly
     */
    public function testCreateNoRepeatNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getNoRepeatNoRRData();
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createNoRepeatNoRRShow.yml'),
            $ds
        );
    }

    /* Tests that a weekly repeating, non-record, non-rebroadcast show
     *  with no end date gets created correctly
     */
    public function testCreateWeeklyRepeatNoEndNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createWeeklyRepeatNoEndNoRRShow.yml'),
            $ds
        );
    }

    public function testCreateBiWeeklyRepeatNoEndNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_repeat_type'] = '1';
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createBiWeeklyRepeatNoEndNoRRShow.yml'),
            $ds
        );
    }

    public function testCreateTriWeeklyRepeatNoEndNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_repeat_type'] = '4';
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createTriWeeklyRepeatNoEndNoRRShow.yml'),
            $ds
        );
    }

    public function testCreateQuadWeeklyRepeatNoEndNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_repeat_type'] = '5';
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createQuadWeeklyRepeatNoEndNoRRShow.yml'),
            $ds
        );
    }

    public function testCreateMonthlyMonthlyRepeatNoEndNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_repeat_type'] = '2';
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createMonthlyMonthlyRepeatNoEndNoRRShow.yml'),
            $ds
        );
    }

    public function testCreateMonthlyWeeklyRepeatNoEndNoRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_repeat_type'] = '3';
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createMonthlyWeeklyRepeatNoEndNoRRShow.yml'),
            $ds
        );
    }

    // Tests that a show instance gets deleted from it's repeating sequence properly
    public function testDeleteShowInstance()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $service_show = new Application_Service_ShowService(null, $data);
        $service_show->addUpdateShow($data);
        $service_show->deleteShow(3, true);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances order by id');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_deleteShowInstance.yml'),
            $ds
        );
    }

    /* Tests that when a user selects 'Delete this instance and all following
     * on the calendar the database gets updated correctly
     */
    public function testDeleteShowInstanceAndAllFollowing()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_day_check'] = [5, 1, 2];

        $service_show = new Application_Service_ShowService(null, $data);
        $service_show->addUpdateShow($data);
        // delete some single instances first
        $service_show->deleteShow(1, true);
        $service_show->deleteShow(6, true);
        $service_show->deleteShow(8, true);
        // delete all instances including and after where id=4
        $service_show->deleteShow(4);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days order by first_show');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances order by id');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_deleteShowInstanceAndAllFollowing.yml'),
            $ds
        );
    }

    public function testEditRepeatingShowInstance()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);
        // move the start date forward one week and the start time forward one hour
        $editData = ShowServiceData::getEditRepeatInstanceData();

        // need to create a new service so it gets constructed with the new data
        $showService = new Application_Service_ShowService(null, $editData);
        $showService->editRepeatingShowInstance($editData);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances order by id');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_editRepeatingShowInstance.yml'),
            $ds
        );
    }

    /* Tests the entire show gets deleted when the user selects 'Delete this
     * instance and all following' from the context menu on the calendar
     */
    public function testDeleteRepeatingShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);
        $showService->deleteShow(1);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances order by id');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_deleteRepeatingShow.yml'),
            $ds
        );
    }

    public function testRepeatShowCreationWhenUserMovesForwardInCalendar()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_repeat_type'] = '1';
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        // simulate the user moves forward in the calendar
        $end = new DateTime('2044-03-12', new DateTimeZone('UTC'));
        $showService->delegateInstanceCreation(null, $end, true);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_repeatShowCreationWhenUserMovesForwardInCalendar.yml'),
            $ds
        );
    }

    public function testLinkedShow()
    {
        TestHelper::loginUser();

        /** Test creating a linked show */
        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_linked'] = 1;
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createLinkedShow.yml'),
            $ds
        );
    }

    /** Test the creation of a single record and rebroadcast(RR) show */
    public function testCreateNoRepeatRRShow()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getNoRepeatRRData();
        $showService = new Application_Service_ShowService(null, $data);
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_createNoRepeatRRShow.yml'),
            $ds
        );
    }

    /** Test the creation of a weekly repeating, record and rebroadcast(RR) show */
    public function testEditRepeatingShowChangeNoEndOption()
    {
        TestHelper::loginUser();

        /** Test changing the no end option on a weekly repeating show */
        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);
        $showService->addUpdateShow($data);

        $data['add_show_end_date'] = '2044-01-09';
        $data['add_show_no_end'] = 0;
        $data['add_show_id'] = 1;

        $showService = new Application_Service_ShowService(null, $data, true);
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_editRepeatingShowChangeNoEndOption.yml'),
            $ds
        );
    }

    /**
     * Tests that when you remove the first repeat show day, which changes
     * the show's first instance start date, updates the scheduled content
     * correctly.
     */
    public function testRemoveFirstRepeatShowDayUpdatesScheduleCorrectly()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_start_date'] = '2044-01-29';
        $data['add_show_day_check'] = [5, 6];
        $data['add_show_linked'] = 1;
        $showService = new Application_Service_ShowService(null, $data);
        $showService->addUpdateShow($data);

        // insert some fake tracks into cc_schedule table
        $ccFiles = new CcFiles();
        $ccFiles
            ->setDbCueIn('00:00:00')
            ->setDbCueOut('00:04:32')
            ->save();

        $scheduleItems = [
            0 => [
                'id' => 0,
                'instance' => 1,
                'timestamp' => time(),
            ],
        ];
        $mediaItems = [
            0 => [
                'id' => 1,
                'type' => 'audioclip',
            ],
        ];
        $scheduler = new Application_Model_Scheduler();
        $scheduler->scheduleAfter($scheduleItems, $mediaItems);

        // delete the first repeat day
        $data['add_show_day_check'] = [6];
        $data['add_show_id'] = 1;
        $showService = new Application_Service_ShowService(null, $data, true);
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');
        $ds->addTable('cc_schedule', 'select id, starts, ends, file_id, clip_length, fade_in, fade_out, cue_in, cue_out, instance_id, playout_status from cc_schedule');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_removeFirstRepeatShowDayUpdatesScheduleCorrectly.yml'),
            $ds
        );
    }

    public function testChangeRepeatDayUpdatesScheduleCorrectly()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $data['add_show_start_date'] = '2044-01-29';
        $data['add_show_day_check'] = [5, 6];
        $data['add_show_linked'] = 1;
        $showService = new Application_Service_ShowService(null, $data);
        $showService->addUpdateShow($data);

        // insert some fake tracks into cc_schedule table
        $ccFiles = new CcFiles();
        $ccFiles
            ->setDbCueIn('00:00:00')
            ->setDbCueOut('00:04:32')
            ->save();

        $scheduleItems = [
            0 => [
                'id' => 0,
                'instance' => 1,
                'timestamp' => time(),
            ],
        ];
        $mediaItems = [
            0 => [
                'id' => 1,
                'type' => 'audioclip',
            ],
        ];
        $scheduler = new Application_Model_Scheduler();
        $scheduler->scheduleAfter($scheduleItems, $mediaItems);

        // delete the first repeat day
        $data['add_show_day_check'] = [6];
        $data['add_show_id'] = 1;
        $showService = new Application_Service_ShowService(null, $data, true);
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');
        $ds->addTable('cc_schedule', 'select id, starts, ends, file_id, clip_length, fade_in, fade_out, cue_in, cue_out, instance_id, playout_status from cc_schedule');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_changeRepeatDayUpdatesScheduleCorrectly.yml'),
            $ds
        );
    }

    public function testChangeRepeatTypeFromWeeklyToNoRepeat()
    {
        TestHelper::loginUser();

        // test change repeat type from weekly to no-repeat
        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);
        $showService->addUpdateShow($data);

        $data['add_show_repeats'] = 0;
        $data['add_show_id'] = 1;
        $showService = new Application_Service_ShowService(null, $data, true);
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_weeklyToNoRepeat.yml'),
            $ds
        );
    }

    public function testChangeRepeatTypeFromWeeklyToBiWeekly()
    {
        TestHelper::loginUser();

        // test change repeat type weekly to bi-weekly
        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);
        $showService->addUpdateShow($data);

        $data['add_show_id'] = 1;
        $data['add_show_repeat_type'] = 1;
        $showService = new Application_Service_ShowService(null, $data, true);
        $showService->addUpdateShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('cc_show', 'select * from cc_show');
        $ds->addTable('cc_show_days', 'select * from cc_show_days');
        $ds->addTable('cc_show_instances', 'select id, starts, ends, show_id, record, rebroadcast, instance_id, modified_instance from cc_show_instances');
        $ds->addTable('cc_show_rebroadcast', 'select * from cc_show_rebroadcast');
        $ds->addTable('cc_show_hosts', 'select * from cc_show_hosts');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/test_weeklyToBiWeekly.yml'),
            $ds
        );
    }
}

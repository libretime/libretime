<?php
require_once "Zend/Test/PHPUnit/DatabaseTestCase.php";
require_once "ShowService.php";
require_once "../application/configs/conf.php";
require_once "AirtimeInstall.php";
require_once "ShowServiceData.php";
require_once "TestHelper.php";

class ShowServiceDbTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;

    public function setUp()
    {
        //XXX: Zend_Test_PHPUnit_DatabaseTestCase doesn't use this for whatever reason:
        //$this->bootstrap = array($this, 'appBootstrap');
        //So instead we just manually call the appBootstrap here:
        //TODO: Use AirtimeInstall.php to create the database and database tables
        //Load Database parameters
        
        //We need to load the config before our app bootstrap runs. The config
        //is normally
        $CC_CONFIG = Config::getConfig();
        
        $dbuser = $CC_CONFIG['dsn']['username'];
        $dbpasswd = $CC_CONFIG['dsn']['password'];
        $dbname = $CC_CONFIG['dsn']['database'];
        $dbhost = $CC_CONFIG['dsn']['hostspec'];

        AirtimeInstall::createDatabase();
        AirtimeInstall::createDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost);
        AirtimeInstall::SetDefaultTimezone();
        
        $this->appBootstrap();
        
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH .'/configs/application.ini');
        $this->application->bootstrap();
    }

    public function getConnection()
    {
        if ($this->_connectionMock == null) {
            $config = new Zend_Config(
                array(
                    'host'     => '127.0.0.1',
                    'dbname'   => 'airtime_test',
                    'username' => 'airtime',
                    'password' => 'airtime'
                )
            );
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
        return $this->createXmlDataSet(
            dirname(__FILE__) . '/datasets/cc_show_seed.xml'
        );
    }

    public function testCcShowInsertedIntoDatabase()
    {
        $showService = new Application_Service_ShowService();

        $data = array(
            "add_show_id" => -1,
            "add_show_name" => "test show",
            "add_show_description" => null,
            "add_show_url" => null,
            "add_show_genre" => null,
            "add_show_color" => "ffffff",
            "add_show_background_color" => "364492",
            "cb_airtime_auth" => false,
            "cb_custom_auth" => false,
            "custom_username" => null,
            "custom_password" => null,
            "add_show_linked" => false
        );

        $showService->setCcShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');

        $this->assertDataSetsEqual(
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_ccShowInsertedIntoDatabase.xml"),
            $ds
        );
    }

    /* Tests that a non-repeating, non-record, and non-rebroadcast show
     * gets created properly
     */
    public function testNoRepeatNoRRShowCreated()
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
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_noRepeatNoRRShowCreated.xml"),
            $ds
        );
    }

    /* Tests that a weekly repeating, non-record, non-rebroadcast show
     *  with no end date gets created correctly
     */
    public function testWeeklyRepeatNoEndNoRRShowCreated()
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
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_weeklyRepeatNoEndNoRRShowCreated.xml"),
            $ds
        );
    }

    /* Tests that a show instance gets deleted from it's repeating sequence properly
     */
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
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_deleteShowInstance.xml"),
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
        $data["add_show_day_check"] = array(5,1,2);

        $service_show = new Application_Service_ShowService(null, $data);
        $service_show->addUpdateShow($data);
        //delete some single instances first
        $service_show->deleteShow(1, true);
        $service_show->deleteShow(6, true);
        $service_show->deleteShow(8, true);
        //delete all instances including and after where id=4
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
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_deleteShowInstanceAndAllFollowing.xml"),
            $ds
        );
    }

    public function testEditRepeatingShowInstance()
    {
        TestHelper::loginUser();

        $data = ShowServiceData::getWeeklyRepeatNoEndNoRRData();
        $showService = new Application_Service_ShowService(null, $data);

        $showService->addUpdateShow($data);
        $editData = ShowServiceData::getEditRepeatInstanceData();

        //need to create a new service so it gets constructed with the new data
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
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_editRepeatingShowInstance.xml"),
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
            $this->createXmlDataSet(dirname(__FILE__)."/datasets/test_deleteRepeatingShow.xml"),
            $ds
        );
    }
}

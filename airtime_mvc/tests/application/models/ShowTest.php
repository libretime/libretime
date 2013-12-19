<?php
require_once "Zend/Test/PHPUnit/DatabaseTestCase.php";

class ShowTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;

    public function setUp()
    {
        //$this->bootstrap = array($this, 'appBootstrap');
        $this->appBootstrap();
        //TODO: Use AirtimeInstall.php to create the database and database tables
        //AirtimeInstall::createDatabase(blah blah);
        //AirtimeInstall::createDatabaseTables(blah blah);
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH .'/configs/application.ini');
        Zend_Session::start();
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
            dirname(__FILE__) . '/files/cc_show_seed.xml'
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
            "cb_airtime_auth" => 0,
            "cb_custom_auth" => 0,
            "custom_username" => null,
            "custom_password" => null,
            "add_show_linked" => 0
        );

        $showService->setCcShow($data);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('cc_show', 'select * from cc_show');

        $this->assertDataSetsEqual(
            $this->createXmlDataSet(dirname(__FILE__)."/files/cc_show_insertIntoAssertion.xml"),
            $ds
        );
    }
}

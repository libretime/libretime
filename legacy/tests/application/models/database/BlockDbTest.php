<?php

/**
 * @internal
 *
 * @coversNothing
 */
class BlockDbTest extends Zend_Test_PHPUnit_DatabaseTestCase // PHPUnit_Framework_TestCase
{
    private $_connectionMock;

    public function setUp()
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();
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

    /**
     * Load a dataset into the database for the block database tests.
     *
     * Defines how the initial state of the database should look before each test is executed
     * Called once during setUp() and gets recreated for each new test
     */
    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/datasets/seed_files.yml');
    }

    /**
     * Test if the single newest file is added to the Database.
     */
    public function testGetListofFilesMeetCriteriaSingleMatch()
    {
        TestHelper::loginUser();
        $CC_CONFIG = Config::getConfig();
        $testqry = CcFilesQuery::create();
        $testout = $testqry->find();
        $vd = $testout->getData();
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $testCriteria = BlockModelData::getCriteriaSingleNewestLabelNada();
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria($testCriteria);
        $tracks = $bltest->getListOfFilesUnderLimit();
        // $tracks = $bltest->getLength();
        $this->assertNotEmpty($tracks);
        // need to load a example criteria into the database
    }

    /**
     * Test if the single newest file is added to the Database.
     */
    public function testMultiTrackandAlbumsGetLoaded()
    {
        TestHelper::loginUser();
        $CC_CONFIG = Config::getConfig();
        $testqry = CcFilesQuery::create();
        $testout = $testqry->find();
        $vd = $testout->getData();
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $testCriteria = BlockModelData::getCriteriaMultiTrackAndAlbum1Hour();
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria($testCriteria);
        $tracks = $bltest->getListOfFilesUnderLimit();
        // $tracks = $bltest->getLength();
        $this->assertNotEmpty($tracks);
        // add assertion that the length is less than 1 hour...
        // need to load a example criteria into the database
    }

    /**
     * Test that an OR group following an AND group on the same field stays grouped.
     *
     * alpha AND (beta OR gamma) must not be evaluated as (alpha AND beta) OR gamma.
     */
    public function testGetListofFilesMeetCriteriaOrGroupAfterAndGroup()
    {
        TestHelper::loginUser();
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria(BlockModelData::getCriteriaDescriptionAndOrGroup());

        $this->assertEquals([8], $this->getMatchingFileIds($bltest));
    }

    /**
     * Test that an OR group preceding an AND group on the same field stays grouped.
     */
    public function testGetListofFilesMeetCriteriaOrGroupBeforeAndGroup()
    {
        TestHelper::loginUser();
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria(BlockModelData::getCriteriaDescriptionAndOrGroup(true));

        $this->assertEquals([8], $this->getMatchingFileIds($bltest));
    }

    private function getMatchingFileIds(Application_Model_Block $block)
    {
        $ids = [];
        foreach ($block->getListofFilesMeetCriteria()['files'] as $file) {
            $ids[] = $file->getDbId();
        }
        sort($ids);

        return $ids;
    }
}

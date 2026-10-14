<?php

/**
 * @internal
 *
 * @coversNothing
 */
class BlockDbTest extends Zend_Test_PHPUnit_DatabaseTestCase // PHPUnit_Framework_TestCase
{
    private $_connectionMock;

    public function setUp(): void
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
    public function testGetListofFilesMeetCriteriaSingleMatch(): void
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
    public function testMultiTrackandAlbumsGetLoaded(): void
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
    public function testGetListofFilesMeetCriteriaOrGroupAfterAndGroup(): void
    {
        TestHelper::loginUser();
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria(BlockModelData::getCriteriaDescriptionAndOrGroup());

        $this->assertEquals([8], $this->getMatchingFileIds($bltest));
    }

    /**
     * Test that an OR group preceding an AND group on the same field stays grouped.
     */
    public function testGetListofFilesMeetCriteriaOrGroupBeforeAndGroup(): void
    {
        TestHelper::loginUser();
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria(BlockModelData::getCriteriaDescriptionAndOrGroup(true));

        $this->assertEquals([8], $this->getMatchingFileIds($bltest));
    }

    /**
     * Test that an ordered sort stops at the first track that does not fit instead of
     * pulling a later, shorter track out of order to fill the remaining time.
     *
     * Seed lengths: 3, 4, 5, 6, 7 are 1:30, 1 is 1:00, 2 is 5:00.
     */
    public function testGetListOfFilesUnderLimitKeepsSortOrder(): void
    {
        TestHelper::loginUser();
        $this->setLastPlayedOrder([3, 4, 5, 1, 6, 7, 2]);
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria(BlockModelData::getCriteriaLeastRecentlyPlayedLabelNada(4));

        $this->assertEquals([3, 4], $this->getScheduledFileIds($bltest));
    }

    /**
     * Test that a track longer than the whole block is skipped, so it cannot stall the block.
     */
    public function testGetListOfFilesUnderLimitSkipsTrackLongerThanBlock(): void
    {
        TestHelper::loginUser();
        $this->setLastPlayedOrder([2, 3, 4, 5, 1, 6, 7]);
        $bltest = new Application_Model_Block();
        $bltest->saveSmartBlockCriteria(BlockModelData::getCriteriaLeastRecentlyPlayedLabelNada(4));

        $this->assertEquals([3, 4], $this->getScheduledFileIds($bltest));
    }

    /**
     * Give the files increasing last played times, least recently played first.
     *
     * Also cues each file to its full length, as the analyzer would, since the block
     * limit is computed from the cue length.
     *
     * @param int[] $fileIds
     */
    private function setLastPlayedOrder(array $fileIds): void
    {
        // The dataset is reloaded for each test, don't get a cached Propel object
        CcFilesPeer::clearInstancePool();
        $lastPlayed = new DateTime('2020-01-01 00:00:00', new DateTimeZone('UTC'));
        foreach ($fileIds as $fileId) {
            $file = CcFilesQuery::create()->findPk($fileId);
            $file->setDbCuein('00:00:00')
                ->setDbCueout($file->getDbLength())
                ->setDbLPtime(clone $lastPlayed)
                ->save();
            $lastPlayed->modify('+1 hour');
        }
    }

    /**
     * @return int[]
     */
    private function getScheduledFileIds(Application_Model_Block $block): array
    {
        return array_map(fn (array $track): int => $track['id'], $block->getListOfFilesUnderLimit());
    }

    /**
     * @return mixed[]
     */
    private function getMatchingFileIds(Application_Model_Block $block): array
    {
        $ids = [];
        foreach ($block->getListofFilesMeetCriteria()['files'] as $file) {
            $ids[] = $file->getDbId();
        }
        sort($ids);

        return $ids;
    }
}

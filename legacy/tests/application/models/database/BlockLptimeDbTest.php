<?php

/**
 * Tests for the lptime-scoped duplicate suppression in
 * Application_Model_Block::getListofFilesMeetCriteria — covers anchoring
 * relative dates to $showStartTime and excluding in-pass picks and
 * scheduled cc_schedule rows whose airtime falls in [threshold, slot).
 *
 * @internal
 *
 * @coversNothing
 */
class BlockLptimeDbTest extends Zend_Test_PHPUnit_DatabaseTestCase
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

    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            __DIR__ . '/datasets/seed_block_lptime.yml'
        );
    }

    private function buildBlock(array $criteria)
    {
        $block = new Application_Model_Block();
        $block->saveSmartBlockCriteria($criteria);

        return $block;
    }

    private static function slotStart(): DateTime
    {
        return new DateTime('2024-01-10 12:00:00', new DateTimeZone('UTC'));
    }

    private static function extractIds(array $files): array
    {
        $ids = [];
        foreach ($files as $file) {
            $ids[] = (int) $file['id'];
        }
        sort($ids);

        return $ids;
    }

    /**
     * lptime BEFORE "1 hour ago" must anchor to $showStartTime, not wallclock now.
     * With slot = 2024-01-10 12:00 UTC the threshold is 11:00 UTC — file 102
     * (lptime 11:30) sits inside the excluded window and must be dropped;
     * the other three (old / NULL / 10:30) must remain eligible.
     */
    public function testLptimeBeforeAnchorsToShowStartTime()
    {
        TestHelper::loginUser();

        $block = $this->buildBlock(BlockModelData::getCriteriaLptimeBefore1Hour());
        $files = $block->getListOfFilesUnderLimit(null, self::slotStart());

        $this->assertEquals([101, 103, 104], self::extractIds($files));
    }

    /**
     * In-pass picks whose computed slot is inside [threshold, slot) must be
     * filtered out as if they were already in cc_schedule.
     */
    public function testInPassPicksWithinWindowAreExcluded()
    {
        TestHelper::loginUser();

        $block = $this->buildBlock(BlockModelData::getCriteriaLptimeBefore1Hour());

        $inPassPicks = [
            [
                'id' => 101,
                'slot' => new DateTime('2024-01-10 11:30:00', new DateTimeZone('UTC')),
            ],
        ];
        $files = $block->getListOfFilesUnderLimit(null, self::slotStart(), $inPassPicks);

        $this->assertEquals([103, 104], self::extractIds($files));
    }

    /**
     * In-pass picks whose slot falls outside [threshold, slot) must NOT be
     * filtered: too-old picks (before threshold) and picks at/after the slot
     * boundary are out of scope for duplicate prevention against this slot.
     */
    public function testInPassPicksOutsideWindowAreKept()
    {
        TestHelper::loginUser();

        $block = $this->buildBlock(BlockModelData::getCriteriaLptimeBefore1Hour());

        $inPassPicks = [
            // before threshold (11:00) — out of scope
            [
                'id' => 101,
                'slot' => new DateTime('2024-01-10 10:00:00', new DateTimeZone('UTC')),
            ],
            // at slot boundary (window is half-open) — out of scope
            [
                'id' => 103,
                'slot' => new DateTime('2024-01-10 12:00:00', new DateTimeZone('UTC')),
            ],
        ];
        $files = $block->getListOfFilesUnderLimit(null, self::slotStart(), $inPassPicks);

        $this->assertEquals([101, 103, 104], self::extractIds($files));
    }

    /**
     * cc_schedule rows whose starts fall in [threshold, slot) with an active
     * playout_status must be excluded from the candidate set, even though
     * they haven't aired yet (and so haven't updated cc_files.lptime).
     */
    public function testScheduledRowsWithinWindowAreExcluded()
    {
        TestHelper::loginUser();

        $sched = new CcSchedule();
        $sched->setDbStarts('2024-01-10 11:30:00')
            ->setDbEnds('2024-01-10 11:31:00')
            ->setDbFileId(104)
            ->setDbCueIn('00:00:00')
            ->setDbCueOut('00:01:00')
            ->setDbClipLength('00:01:00')
            ->setDbInstanceId(1)
            ->setDbPlayoutStatus(1)
            ->setDbBroadcasted(0)
            ->setDbPosition(0)
            ->save();

        $block = $this->buildBlock(BlockModelData::getCriteriaLptimeBefore1Hour());
        $files = $block->getListOfFilesUnderLimit(null, self::slotStart());

        $this->assertEquals([101, 103], self::extractIds($files));
    }

    /**
     * cc_schedule rows whose starts fall in the window but whose
     * playout_status is -1 (cancelled / filler) must NOT be excluded.
     */
    public function testCancelledScheduledRowsAreNotExcluded()
    {
        TestHelper::loginUser();

        $sched = new CcSchedule();
        $sched->setDbStarts('2024-01-10 11:30:00')
            ->setDbEnds('2024-01-10 11:31:00')
            ->setDbFileId(104)
            ->setDbCueIn('00:00:00')
            ->setDbCueOut('00:01:00')
            ->setDbClipLength('00:01:00')
            ->setDbInstanceId(1)
            ->setDbPlayoutStatus(-1)
            ->setDbBroadcasted(0)
            ->setDbPosition(0)
            ->save();

        $block = $this->buildBlock(BlockModelData::getCriteriaLptimeBefore1Hour());
        $files = $block->getListOfFilesUnderLimit(null, self::slotStart());

        $this->assertEquals([101, 103, 104], self::extractIds($files));
    }

    /**
     * Without an lptime BEFORE criterion the in-pass / cc_schedule
     * suppression must not kick in — the gate is the lptime criterion,
     * not the presence of $inPassPicks.
     */
    public function testInPassPicksIgnoredWithoutLptimeCriterion()
    {
        TestHelper::loginUser();

        $block = $this->buildBlock(BlockModelData::getCriteriaLabelOnly());

        $inPassPicks = [
            [
                'id' => 101,
                'slot' => new DateTime('2024-01-10 11:30:00', new DateTimeZone('UTC')),
            ],
            [
                'id' => 102,
                'slot' => new DateTime('2024-01-10 11:45:00', new DateTimeZone('UTC')),
            ],
        ];
        $files = $block->getListOfFilesUnderLimit(null, self::slotStart(), $inPassPicks);

        $this->assertEquals([101, 102, 103, 104], self::extractIds($files));
    }
}

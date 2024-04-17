<?php

/**
 * @copyright 2010 Sourcefabric O.P.S.
 * @license https://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Block implements Application_Model_LibraryEditable
{
    /**
     * propel connection object.
     */
    private $con;

    /**
     * unique id for the block.
     */
    private $id;

    private $block;

    /**
     * info needed to insert a new block element.
     */
    private $blockItem = [
        'id' => '',
        'pos' => '',
        'cliplength' => '',
        'cuein' => '00:00:00',
        'cueout' => '00:00:00',
        'fadein' => '0.0',
        'fadeout' => '0.0',
        'crossfadeDuration' => 0,
    ];

    // using propel's phpNames.
    private $categories = [
        'dc:title' => 'Name',
        'dc:creator' => 'Creator',
        'dc:description' => 'Description',
        'dcterms:extent' => 'Length',
    ];

    private static $modifier2CriteriaMap = [
        CriteriaModifier::CONTAINS => Criteria::ILIKE,
        CriteriaModifier::DOES_NOT_CONTAIN => Criteria::NOT_ILIKE,
        CriteriaModifier::IS => Criteria::EQUAL,
        CriteriaModifier::IS_NOT => Criteria::NOT_EQUAL,
        CriteriaModifier::STARTS_WITH => Criteria::ILIKE,
        CriteriaModifier::ENDS_WITH => Criteria::ILIKE,
        CriteriaModifier::IS_GREATER_THAN => Criteria::GREATER_THAN,
        CriteriaModifier::IS_LESS_THAN => Criteria::LESS_THAN,
        CriteriaModifier::IS_IN_THE_RANGE => Criteria::CUSTOM,
        CriteriaModifier::BEFORE => Criteria::CUSTOM,
        CriteriaModifier::AFTER => Criteria::CUSTOM,
        CriteriaModifier::BETWEEN => Criteria::CUSTOM,
    ];

    public function __construct($id = null, $con = null)
    {
        if (isset($id)) {
            $this->block = CcBlockQuery::create()->findPk($id);

            if (is_null($this->block)) {
                throw new BlockNotFoundException();
            }
        } else {
            $this->block = new CcBlock();
            $this->block->setDbUTime(new DateTime('now', new DateTimeZone('UTC')));
            $this->block->save();
        }

        $this->blockItem['fadein'] = Application_Model_Preference::GetDefaultFadeIn();
        $this->blockItem['fadeout'] = Application_Model_Preference::GetDefaultFadeOut();
        $this->blockItem['crossfadeDuration'] = Application_Model_Preference::GetDefaultCrossfadeDuration();

        $this->con = isset($con) ? $con : Propel::getConnection(CcBlockPeer::DATABASE_NAME);
        $this->id = $this->block->getDbId();
    }

    /**
     * Return local ID of virtual file.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Rename stored virtual block.
     *
     * @param string $p_newname
     */
    public function setName($p_newname)
    {
        $this->block->setDbName($p_newname);
        $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->block->save($this->con);
    }

    /**
     * Get mnemonic block name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->block->getDbName();
    }

    public function setDescription($p_description)
    {
        $this->block->setDbDescription($p_description);
        $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->block->save($this->con);
    }

    public function getDescription()
    {
        return $this->block->getDbDescription();
    }

    public function getCreator()
    {
        return $this->block->getCcSubjs()->getDbLogin();
    }

    public function getCreatorId()
    {
        return $this->block->getCcSubjs()->getDbId();
    }

    public function setCreator($p_id)
    {
        $this->block->setDbCreatorId($p_id);
        $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->block->save($this->con);
    }

    public function getLastModified($format = null)
    {
        return $this->block->getDbMtime($format);
    }

    public function getSize()
    {
        return $this->block->countCcBlockcontentss();
    }

    /**
     * Get the entire block as a two dimensional array, sorted in order of play.
     *
     * @param bool $filterFiles if this is true, it will only return files that has
     *                          file_exists flag set to true
     *
     * @return array
     */
    public function getContents($filterFiles = false)
    {
        $sql = <<<'SQL'
SELECT pc.id AS id,
       pc.position,
       pc.cliplength AS LENGTH,
       pc.cuein,
       pc.cueout,
       pc.fadein,
       pc.fadeout,
       pc.trackoffset,
       bl.type,
       f.LENGTH AS orig_length,
       f.id AS item_id,
       f.track_title,
       f.artist_name AS creator,
       f.file_exists AS EXISTS,
       f.filepath AS path,
       f.mime as mime
FROM cc_blockcontents AS pc
LEFT JOIN cc_files AS f ON pc.file_id=f.id
LEFT JOIN cc_block AS bl ON pc.block_id = bl.id
WHERE pc.block_id = :block_id

SQL;

        if ($filterFiles) {
            $sql .= <<<'SQL'
            AND f.file_exists = :file_exists
SQL;
        }
        $sql .= <<<'SQL'

ORDER BY pc.position
SQL;
        $params = [':block_id' => $this->id];
        if ($filterFiles) {
            $params[':file_exists'] = $filterFiles;
        }
        $rows = Application_Common_Database::prepareAndExecute($sql, $params);

        $offset = 0;
        foreach ($rows as &$row) {
            $clipSec = Application_Common_DateHelper::playlistTimeToSeconds($row['length']);

            $row['trackSec'] = $clipSec;

            $row['cueInSec'] = Application_Common_DateHelper::playlistTimeToSeconds($row['cuein']);
            $row['cueOutSec'] = Application_Common_DateHelper::playlistTimeToSeconds($row['cueout']);

            $trackoffset = $row['trackoffset'];
            $offset += $clipSec;
            $offset -= $trackoffset;
            $offset_cliplength = Application_Common_DateHelper::secondsToPlaylistTime($offset);

            // format the length for UI.
            $formatter = new LengthFormatter($row['length']);
            $row['length'] = $formatter->format();

            $formatter = new LengthFormatter($offset_cliplength);
            $row['offset'] = $formatter->format();

            // format the fades in format 00(.0)
            $fades = $this->getFadeInfo($row['position']);
            $row['fadein'] = $fades[0];
            $row['fadeout'] = $fades[1];

            // format the cues in format 00:00:00(.0)
            // we need to add the '.0' for cues and not fades
            // because propel takes care of this for us
            // (we use propel to fetch the fades)
            $row['cuein'] = str_pad(substr($row['cuein'], 0, 10), 10, '.0');
            $row['cueout'] = str_pad(substr($row['cueout'], 0, 10), 10, '.0');

            // format original length
            $formatter = new LengthFormatter($row['orig_length']);
            $row['orig_length'] = $formatter->format();

            // XSS exploit prevention
            $row['track_title'] = htmlspecialchars($row['track_title']);
            $row['creator'] = htmlspecialchars($row['creator']);
        }

        return $rows;
    }

    /**
     * The database stores fades in 00:00:00 Time format with optional millisecond resolution .000000
     * but this isn't practical since fades shouldn't be very long usually 1 second or less. This function
     * will normalize the fade so that it looks like 00.000000 to the user.
     *
     * @param mixed $fade
     */
    public function normalizeFade($fade)
    {
        // First get rid of the first six characters 00:00: which will be added back later for db update
        $fade = substr($fade, 6);

        // Second add .000000 if the fade does't have milliseconds format already
        $dbFadeStrPos = strpos($fade, '.');
        if ($dbFadeStrPos === false) {
            $fade .= '.000000';
        } else {
            while (strlen($fade) < 9) {
                $fade .= '0';
            }
        }

        // done, just need to set back the formated values
        return $fade;
    }

    public function getUnformatedLength()
    {
        $this->block->reload();
        if ($this->isStatic()) {
            $length = $this->block->getDbLength();
        } else {
            $length = $this->getDynamicBlockLength();
        }

        return $length;
    }

    public function getLength()
    {
        $this->block->reload();
        $prepend = '';
        if ($this->isStatic()) {
            $length = $this->block->getDbLength();
        } else {
            $length = $this->getDynamicBlockLength();
            if (!$this->hasItemLimit()) {
                $prepend = '~';
            }
        }
        $formatter = new LengthFormatter($length);

        return $prepend . $formatter->format();
    }

    public function getDynamicBlockLength()
    {
        [$value, $modifier] = $this->getLimitValueAndModifier();
        if ($modifier == 'items') {
            $length = $value . ' ' . _('items');
        } else {
            $hour = '00';
            $mins = '00';
            if ($modifier == 'minutes') {
                $mins = $value;
                if ($value > 59) {
                    $hour = intval($value / 60);
                    $mins = $value % 60;
                }
            } elseif ($modifier == 'hours') {
                $mins = $value * 60;
                if ($mins > 59) {
                    $hour = intval($mins / 60);
                    $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                    $mins %= 60;
                }
            }
            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $mins = str_pad($mins, 2, '0', STR_PAD_LEFT);
            $length = $hour . ':' . $mins . ':00';
        }

        return $length;
    }

    public function getLimitValueAndModifier()
    {
        $result = CcBlockcriteriaQuery::create()->filterByDbBlockId($this->id)
            ->filterByDbCriteria('limit')->findOne();
        if ($result) {
            $modifier = $result->getDbModifier();
            $value = $result->getDbValue();

            return [$value, $modifier];
        }
    }

    // this function returns sum of all track length under this block.
    public function getStaticLength()
    {
        $sql = <<<'SQL'
SELECT SUM(cliplength) AS LENGTH
FROM cc_blockcontents as bc
JOIN cc_files as f ON bc.file_id = f.id
WHERE block_id = :block_id
AND f.file_exists = true
SQL;
        $result = Application_Common_Database::prepareAndExecute($sql, [':block_id' => $this->id], 'all', PDO::FETCH_NUM);

        return $result[0][0];
    }

    private function insertBlockElement($info)
    {
        $row = new CcBlockcontents();
        $row->setDbBlockId($this->id);
        $row->setDbFileId($info['id']);
        $row->setDbPosition($info['pos']);
        $row->setDbCliplength($info['cliplength']);
        $row->setDbCuein($info['cuein']);
        $row->setDbCueout($info['cueout']);
        $row->setDbFadein(Application_Common_DateHelper::secondsToPlaylistTime($info['fadein']));
        $row->setDbFadeout(Application_Common_DateHelper::secondsToPlaylistTime($info['fadeout']));
        $row->setDbTrackOffset($info['crossfadeDuration']);
        $row->save($this->con);
        // above save result update on cc_block table on length column.
        // but $this->block doesn't get updated automatically
        // so we need to manually grab it again from DB so it has updated values
        // It is something to do FORMAT_ON_DEMAND( Lazy Loading )
        $this->block = CcBlockQuery::create()->findPK($this->id);
    }

    private function buildEntry($p_item, $pos)
    {
        $file = CcFilesQuery::create()->findPK($p_item, $this->con);

        if (isset($file) && $file->visible()) {
            $entry = $this->blockItem;
            $entry['id'] = $file->getDbId();
            $entry['pos'] = $pos;
            $entry['cueout'] = $file->getDbCueout();
            $entry['cuein'] = $file->getDbCuein();

            $cue_out = Application_Common_DateHelper::calculateLengthInSeconds($entry['cueout']);
            $cue_in = Application_Common_DateHelper::calculateLengthInSeconds($entry['cuein']);
            $entry['cliplength'] = Application_Common_DateHelper::secondsToPlaylistTime($cue_out - $cue_in);

            return $entry;
        }

        throw new Exception('trying to add a file that does not exist.');
    }

    public function isStatic()
    {
        return $this->block->getDbType() == 'static';
    }

    /*
     * @param array $p_items
    *     an array of audioclips to add to the block
    * @param int|null $p_afterItem
    *     item which to add the new items after in the block, null if added to the end.
    * @param string (before|after) $addAfter
    *      whether to add the clips before or after the selected item.
    */
    public function addAudioClips($p_items, $p_afterItem = null, $addType = 'after')
    {
        $this->con->beginTransaction();
        $contentsToUpdate = [];

        try {
            if (is_numeric($p_afterItem)) {
                Logging::info("Finding block content item {$p_afterItem}");

                $afterItem = CcBlockcontentsQuery::create()->findPK($p_afterItem);
                $index = $afterItem->getDbPosition();
                Logging::info("index is {$index}");
                $pos = ($addType == 'after') ? $index + 1 : $index;

                $contentsToUpdate = CcBlockcontentsQuery::create()
                    ->filterByDbBlockId($this->id)
                    ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                    ->orderByDbPosition()
                    ->find($this->con);

                Logging::info('Adding to block');
                Logging::info("at position {$pos}");
            } else {
                // add to the end of the block
                if ($addType == 'after') {
                    $pos = $this->getSize();
                }
                // add to the beginning of the block.
                else {
                    $pos = 0;

                    $contentsToUpdate = CcBlockcontentsQuery::create()
                        ->filterByDbBlockId($this->id)
                        ->orderByDbPosition()
                        ->find($this->con);
                }

                $contentsToUpdate = CcBlockcontentsQuery::create()
                    ->filterByDbBlockId($this->id)
                    ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                    ->orderByDbPosition()
                    ->find($this->con);

                Logging::info('Adding to block');
                Logging::info("at position {$pos}");
            }

            foreach ($p_items as $ac) {
                // Logging::info("Adding audio file {$ac[0]}");
                try {
                    if (is_array($ac) && $ac[1] == 'audioclip') {
                        $res = $this->insertBlockElement($this->buildEntry($ac[0], $pos));

                        // update is_playlist flag in cc_files to indicate the
                        // file belongs to a playlist or block (in this case a block)
                        $db_file = CcFilesQuery::create()->findPk($ac[0], $this->con);
                        $db_file->setDbIsPlaylist(true)->save($this->con);

                        ++$pos;
                    } elseif (!is_array($ac)) {
                        $res = $this->insertBlockElement($this->buildEntry($ac, $pos));
                        ++$pos;

                        $db_file = CcFilesQuery::create()->findPk($ac, $this->con);
                        $db_file->setDbIsPlaylist(true)->save($this->con);
                    }
                } catch (Exception $e) {
                    Logging::info($e->getMessage());
                }
            }

            // reset the positions of the remaining items.
            for ($i = 0; $i < count($contentsToUpdate); ++$i) {
                $contentsToUpdate[$i]->setDbPosition($pos);
                $contentsToUpdate[$i]->save($this->con);
                ++$pos;
            }

            $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->block->save($this->con);

            $this->con->commit();

            $this->updateBlockLengthInAllPlaylist();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    /**
     * Move audioClip to the new position in the block.
     *
     * @param array $p_items
     *                           array of unique ids of the selected items
     * @param int   $p_afterItem
     *                           unique id of the item to move the clip after
     */
    public function moveAudioClips($p_items, $p_afterItem = null)
    {
        $this->con->beginTransaction();

        try {
            $contentsToMove = CcBlockcontentsQuery::create()
                ->filterByDbId($p_items, Criteria::IN)
                ->orderByDbPosition()
                ->find($this->con);

            $otherContent = CcBlockcontentsQuery::create()
                ->filterByDbId($p_items, Criteria::NOT_IN)
                ->filterByDbBlockId($this->id)
                ->orderByDbPosition()
                ->find($this->con);

            $pos = 0;
            // moving items to beginning of the block.
            if (is_null($p_afterItem)) {
                Logging::info('moving items to beginning of block');

                foreach ($contentsToMove as $item) {
                    Logging::info("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    ++$pos;
                }
                foreach ($otherContent as $item) {
                    Logging::info("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    ++$pos;
                }
            } else {
                Logging::info("moving items after {$p_afterItem}");

                foreach ($otherContent as $item) {
                    Logging::info("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    ++$pos;

                    if ($item->getDbId() == $p_afterItem) {
                        foreach ($contentsToMove as $move) {
                            Logging::info("item {$move->getDbId()} to pos {$pos}");
                            $move->setDbPosition($pos);
                            $move->save($this->con);
                            ++$pos;
                        }
                    }
                }
            }

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }

        $this->block = CcBlockQuery::create()->findPK($this->id);
        $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->block->save($this->con);
    }

    /**
     * Remove audioClip from block.
     *
     * @param array $p_items
     *                       array of unique item ids to remove from the block..
     */
    public function delAudioClips($p_items)
    {
        $this->con->beginTransaction();

        try {
            // we need to get the file id of the item we are deleting
            // before the item gets deleted from the block
            $itemsToDelete = CcBlockcontentsQuery::create()
                ->filterByPrimaryKeys($p_items)
                ->filterByDbFileId(null, Criteria::NOT_EQUAL)
                ->find($this->con);

            CcBlockcontentsQuery::create()
                ->findPKs($p_items)
                ->delete($this->con);

            // now that the items have been deleted we can update the
            // is_playlist flag in cc_files
            Application_Model_StoredFile::setIsPlaylist($itemsToDelete, 'block', false);

            $contents = CcBlockcontentsQuery::create()
                ->filterByDbBlockId($this->id)
                ->orderByDbPosition()
                ->find($this->con);

            // reset the positions of the remaining items.
            for ($i = 0; $i < count($contents); ++$i) {
                $contents[$i]->setDbPosition($i);
                $contents[$i]->save($this->con);
            }

            $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->block->save($this->con);

            $this->con->commit();

            $this->updateBlockLengthInAllPlaylist();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    public function getFadeInfo($pos)
    {
        // Logging::info("Getting fade info for pos {$pos}");

        $row = CcBlockcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbBlockId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

        // Propel returns values in form 00.000000 format which is for only seconds.
        // We only want to display 1 decimal
        $fadeIn = substr($row->getDbFadein(), 0, 4);
        $fadeOut = substr($row->getDbFadeout(), 0, 4);

        return [$fadeIn, $fadeOut];
    }

    /*
     * create a crossfade from item in cc_playlist_contents with $id1 to item $id2.
    *
    * $fadeOut length of fade out in seconds if $id1
    * $fadeIn length of fade in in seconds of $id2
    * $offset time in seconds from end of $id1 that $id2 will begin to play.
    */
    public function createCrossfade($id1, $fadeOut, $id2, $fadeIn, $offset)
    {
        $this->con->beginTransaction();

        if (!isset($offset)) {
            $offset = Application_Model_Preference::GetDefaultCrossfadeDuration();
        }

        try {
            if (isset($id1)) {
                $this->changeFadeInfo($id1, null, $fadeOut);
            }
            if (isset($id2)) {
                $this->changeFadeInfo($id2, $fadeIn, null, $offset);
            }

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    /**
     * Change fadeIn and fadeOut values for block Element.
     *
     * @param string     $fadeIn
     *                            new value in ss.ssssss or extent format
     * @param string     $fadeOut
     *                            new value in ss.ssssss or extent format
     * @param mixed      $id
     * @param null|mixed $offset
     *
     * @return bool
     */
    public function changeFadeInfo($id, $fadeIn, $fadeOut, $offset = null)
    {
        // See issue CC-2065, pad the fadeIn and fadeOut so that it is TIME compatable with the DB schema
        // For the top level PlayList either fadeIn or fadeOut will sometimes be Null so need a gaurd against
        // setting it to nonNull for checks down below
        $fadeIn = $fadeIn ? '00:00:' . $fadeIn : $fadeIn;
        $fadeOut = $fadeOut ? '00:00:' . $fadeOut : $fadeOut;

        $this->con->beginTransaction();

        try {
            $row = CcBlockcontentsQuery::create()->findPK($id);

            if (is_null($row)) {
                throw new Exception('Block item does not exist.');
            }

            $clipLength = $row->getDbCliplength();

            if (!is_null($fadeIn)) {
                $sql = 'SELECT :fade_in::INTERVAL > :clip_length::INTERVAL';
                $params = [
                    ':fade_in' => $fadeIn,
                    ':clip_length' => $clipLength,
                ];

                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    // "Fade In can't be larger than overall playlength.";
                    $fadeIn = $clipLength;
                }
                $row->setDbFadein($fadeIn);

                if (!is_null($offset)) {
                    $row->setDbTrackOffset($offset);
                    Logging::info("Setting offset {$offset} on item {$id}");
                    $row->save($this->con);
                }
            }
            if (!is_null($fadeOut)) {
                $sql = 'SELECT :fade_out::INTERVAL > :clip_length::INTERVAL';
                $params = [
                    ':fade_out' => $fadeOut,
                    ':clip_length' => $clipLength,
                ];

                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    // "Fade Out can't be larger than overall playlength.";
                    $fadeOut = $clipLength;
                }
                $row->setDbFadeout($fadeOut);
            }

            $row->save($this->con);
            $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->block->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }

        return ['fadeIn' => $fadeIn, 'fadeOut' => $fadeOut];
    }

    public function setfades($fadein, $fadeout)
    {
        if (isset($fadein)) {
            Logging::info("Setting block fade in {$fadein}");
            $row = CcBlockcontentsQuery::create()
                ->filterByDbBlockId($this->id)
                ->filterByDbPosition(0)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), $fadein, null);
        }

        if (isset($fadeout)) {
            Logging::info("Setting block fade out {$fadeout}");
            $row = CcBlockcontentsQuery::create()
                ->filterByDbBlockId($this->id)
                ->filterByDbPosition($this->getSize() - 1)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), null, $fadeout);
        }
    }

    /**
     * Change cueIn/cueOut values for block element.
     *
     * @param string $cueIn
     *                       new value in ss.ssssss or extent format
     * @param string $cueOut
     *                       new value in ss.ssssss or extent format
     * @param mixed  $id
     *
     * @return bool or pear error object
     */
    public function changeClipLength($id, $cueIn, $cueOut)
    {
        $this->con->beginTransaction();

        $errArray = [];

        try {
            if (is_null($cueIn) && is_null($cueOut)) {
                $errArray['error'] = _('Cue in and cue out are null.');

                return $errArray;
            }

            $row = CcBlockcontentsQuery::create()
                ->joinWith(CcFilesPeer::OM_CLASS)
                ->filterByPrimaryKey($id)
                ->findOne($this->con);

            if (is_null($row)) {
                throw new Exception('Block item does not exist.');
            }

            $oldCueIn = $row->getDBCuein();
            $oldCueOut = $row->getDbCueout();
            $fadeIn = $row->getDbFadein();
            $fadeOut = $row->getDbFadeout();

            $file = $row->getCcFiles($this->con);
            $origLength = $file->getDbLength();

            if (!is_null($cueIn) && !is_null($cueOut)) {
                if ($cueOut === '') {
                    $cueOut = $origLength;
                }

                $sql = 'SELECT :cue_out::INTERVAL > :orig_length::INTERVAL';
                $params = [
                    ':cue_out' => $cueOut,
                    ':orig_length' => $origLength,
                ];
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    $errArray['error'] = _("Can't set cue out to be greater than file length.");

                    return $errArray;
                }

                $sql = 'SELECT :cue_in::INTERVAL > :cue_out::INTERVAL';
                $params = [
                    ':cue_in' => $cueIn,
                    ':cue_out' => $cueOut,
                ];
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    $errArray['error'] = _("Can't set cue in to be larger than cue out.");

                    return $errArray;
                }

                $sql = 'SELECT :cue_out::INTERVAL - :cue_in::INTERVAL';
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                $cliplength = $result;

                $row->setDbCuein($cueIn);
                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);
            } elseif (!is_null($cueIn)) {
                $sql = 'SELECT :cue_in::INTERVAL > :old_cue_out::INTERVAL';
                $params = [
                    ':cue_in' => $cueIn,
                    ':old_cue_out' => $oldCueOut,
                ];
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    $errArray['error'] = _("Can't set cue in to be larger than cue out.");

                    return $errArray;
                }

                $sql = 'SELECT :old_cue_out::INTERVAL - :cue_in::INTERVAL';
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                $cliplength = $result;

                $row->setDbCuein($cueIn);
                $row->setDBCliplength($cliplength);
            } elseif (!is_null($cueOut)) {
                if ($cueOut === '') {
                    $cueOut = $origLength;
                }

                $sql = 'SELECT :cue_out::INTERVAL > :orig_length::INTERVAL';
                $params = [
                    ':cue_out' => $cueOut,
                    ':orig_length' => $origLength,
                ];
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    $errArray['error'] = _("Can't set cue out to be greater than file length.");

                    return $errArray;
                }

                $sql = 'SELECT :cue_out::INTERVAL < :old_cue_in::INTERVAL';
                $params = [
                    ':cue_out' => $cueOut,
                    ':old_cue_in' => $oldCueIn,
                ];
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                if ($result) {
                    $errArray['error'] = _("Can't set cue out to be smaller than cue in.");

                    return $errArray;
                }

                $sql = 'SELECT :cue_out::INTERVAL - :old_cue_in::INTERVAL';
                $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
                $cliplength = $result;

                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);
            }

            $cliplength = $row->getDbCliplength();

            $sql = 'SELECT :fade_in::INTERVAL > :clip_length::INTERVAL';
            $params = [
                ':fade_in' => $fadeIn,
                ':clip_length' => $cliplength,
            ];
            $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
            if ($result) {
                $fadeIn = $cliplength;
                $row->setDbFadein($fadeIn);
            }

            $sql = 'SELECT :fade_out::INTERVAL > :clip_length::INTERVAL';
            $params = [
                ':fade_out' => $fadeOut,
                ':clip_length' => $cliplength,
            ];
            $result = Application_Common_Database::prepareAndExecute($sql, $params, 'column');
            if ($result) {
                $fadeOut = $cliplength;
                $row->setDbFadein($fadeOut);
            }

            $row->save($this->con);
            $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->block->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }

        return [
            'cliplength' => $cliplength, 'cueIn' => $cueIn, 'cueOut' => $cueOut, 'length' => $this->getUnformatedLength(),
            'fadeIn' => $fadeIn, 'fadeOut' => $fadeOut,
        ];
    }

    public function getAllPLMetaData()
    {
        $categories = $this->categories;
        $md = [];

        foreach ($categories as $key => $val) {
            $method = 'get' . $val;
            $md[$key] = $this->{$method}();
        }

        return $md;
    }

    public function getMetaData($category)
    {
        $cat = $this->categories[$category];
        $method = 'get' . $cat;

        return $this->{$method}();
    }

    public function setMetadata($category, $value)
    {
        $cat = $this->categories[$category];

        $method = 'set' . $cat;
        $this->{$method}($value);
    }

    public static function getBlockCount()
    {
        $sql = 'SELECT count(*) as cnt FROM cc_playlist';

        return Application_Common_Database::prepareAndExecute(
            $sql,
            [],
            Application_Common_Database::COLUMN
        );
    }

    /**
     * Delete the file from all blocks.
     *
     * @param string $p_fileId
     */
    public static function DeleteFileFromAllBlocks($p_fileId)
    {
        CcBlockcontentsQuery::create()->filterByDbFileId($p_fileId)->delete();
    }

    /**
     * Delete blocks that match the ids..
     *
     * @param array $p_ids
     * @param mixed $p_userId
     */
    public static function deleteBlocks($p_ids, $p_userId)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $isAdminOrPM = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER]);

        // get only the files from the blocks
        // we are about to delete
        $itemsToDelete = CcBlockcontentsQuery::create()
            ->filterByDbBlockId($p_ids)
            ->filterByDbFileId(null, Criteria::NOT_EQUAL)
            ->find();

        $updateIsPlaylistFlag = false;

        if (!$isAdminOrPM) {
            $leftOver = self::blocksNotOwnedByUser($p_ids, $p_userId);

            if (count($leftOver) == 0) {
                CcBlockQuery::create()->findPKs($p_ids)->delete();
                $updateIsPlaylistFlag = true;
            } else {
                throw new BlockNoPermissionException();
            }
        } else {
            CcBlockQuery::create()->findPKs($p_ids)->delete();
            $updateIsPlaylistFlag = true;
        }

        if ($updateIsPlaylistFlag) {
            // update is_playlist flag in cc_files
            Application_Model_StoredFile::setIsPlaylist(
                $itemsToDelete,
                'block',
                false
            );
        }
    }

    // This function returns that are not owen by $p_user_id among $p_ids
    private static function blocksNotOwnedByUser($p_ids, $p_userId)
    {
        $ownedByUser = CcBlockQuery::create()->filterByDbCreatorId($p_userId)->find()->getData();
        $selectedPls = $p_ids;
        $ownedPls = [];
        foreach ($ownedByUser as $pl) {
            if (in_array($pl->getDbId(), $selectedPls)) {
                $ownedPls[] = $pl->getDbId();
            }
        }

        return array_diff($selectedPls, $ownedPls);
    }

    /**
     * Delete all files from block.
     */
    public function deleteAllFilesFromBlock()
    {
        // get only the files from the playlist
        // we are about to clear out
        $itemsToDelete = CcBlockcontentsQuery::create()
            ->filterByDbBlockId($this->id)
            ->filterByDbFileId(null, Criteria::NOT_EQUAL)
            ->find();

        CcBlockcontentsQuery::create()->findByDbBlockId($this->id)->delete();

        // update is_playlist flag in cc_files
        Application_Model_StoredFile::setIsPlaylist(
            $itemsToDelete,
            'block',
            false
        );

        // $this->block->reload();
        $this->block->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->block->save($this->con);
        $this->con->commit();
    }

    // smart block functions start
    public function shuffleSmartBlock()
    {
        // if it here that means it's static pl
        $this->saveType('static');
        $contents = CcBlockcontentsQuery::create()
            ->filterByDbBlockId($this->id)
            ->orderByDbPosition()
            ->find();
        $shuffledPos = range(0, count($contents) - 1);
        shuffle($shuffledPos);
        foreach ($contents as $item) {
            $item->setDbPosition(array_shift($shuffledPos));
            $item->save();
        }

        return ['result' => 0];
    }

    public function saveType($p_blockType)
    {
        // saving dynamic/static flag
        CcBlockQuery::create()->findPk($this->id)->setDbType($p_blockType)->save();
    }

    public function setLength($value)
    {
        $this->block->setDbLength($value);
        $this->block->save($this->con);
        $this->updateBlockLengthInAllPlaylist();
    }

    /**
     * Saves smart block criteria.
     *
     * @param array $p_criteria
     */
    public function saveSmartBlockCriteria($p_criteria)
    {
        $data = $this->organizeSmartPlaylistCriteria($p_criteria);

        // saving dynamic/static flag
        $blockType = $data['etc']['sp_type'] == 0 ? 'dynamic' : 'static';
        $this->saveType($blockType);
        $this->storeCriteriaIntoDb($data);

        // if the block is dynamic, put null to the length
        // as it cannot be calculated
        if ($blockType == 'dynamic') {
            if ($this->hasItemLimit()) {
                $this->setLength(null);
            } else {
                $this->setLength($this->getDynamicBlockLength());
            }
        } else {
            $length = $this->getStaticLength();
            if (!$length) {
                $length = '00:00:00';
            }
            $this->setLength($length);
        }

        $this->updateBlockLengthInAllPlaylist();
    }

    public function hasItemLimit()
    {
        [$value, $modifier] = $this->getLimitValueAndModifier();

        return $modifier == 'items';
    }

    public function storeCriteriaIntoDb($p_criteriaData)
    {
        // delete criteria under $p_blockId
        CcBlockcriteriaQuery::create()->findByDbBlockId($this->id)->delete();
        // Logging::info($p_criteriaData);
        // insert modifier rows
        if (isset($p_criteriaData['criteria'])) {
            $critKeys = array_keys($p_criteriaData['criteria']);
            for ($i = 0; $i < count($critKeys); ++$i) {
                // in order to maintain separation of different criteria to preserve AND statements for criteria
                // that might contradict itself we group them based upon their original position on the form
                $criteriaGroup = $i;
                foreach ($p_criteriaData['criteria'][$critKeys[$i]] as $d) {
                    $field = $d['sp_criteria_field'];
                    $value = $d['sp_criteria_value'];
                    $modifier = $d['sp_criteria_modifier'];
                    if (isset($d['sp_criteria_extra'])) {
                        $extra = $d['sp_criteria_extra'];
                    }
                    if (isset($d['sp_criteria_datetime_select'])) {
                        $datetimeunit = $d['sp_criteria_datetime_select'];
                    }
                    if (isset($d['sp_criteria_extra_datetime_select'])) {
                        $extradatetimeunit = $d['sp_criteria_extra_datetime_select'];
                    }

                    if ($field == 'utime' || $field == 'mtime' || $field == 'lptime') {
                        // if the date isn't relative we  want to convert the value to a specific UTC date
                        if (!in_array($modifier, ['before', 'after', 'between'])) {
                            $value = Application_Common_DateHelper::UserTimezoneStringToUTCString($value);
                        } else {
                            $value = $value . ' ' . $datetimeunit . ' ago';
                            // Logging::info($value);
                        }
                    }

                    $qry = new CcBlockcriteria();
                    $qry->setDbCriteria($field)
                        ->setDbModifier($d['sp_criteria_modifier'])
                        ->setDbValue($value)
                        ->setDbBlockId($this->id);

                    if (isset($d['sp_criteria_extra'])) {
                        if ($field == 'utime' || $field == 'mtime' || $field == 'lptime') {
                            // if the date isn't relative we  want to convert the value to a specific UTC date
                            if (!in_array($modifier, ['before', 'after', 'between'])) {
                                $extra = Application_Common_DateHelper::UserTimezoneStringToUTCString($extra);
                            } else {
                                $extra = $extra . ' ' . $extradatetimeunit . ' ago';
                            }
                        }

                        $qry->setDbExtra($extra);
                    }
                    // save the criteria group so separation via new modifiers AND can be preserved vs. lumping
                    // them all into a single or later on
                    if (isset($criteriaGroup)) {
                        $qry->setDbCriteriaGroup($criteriaGroup);
                    }
                    $qry->save();
                }
            }
        }

        // insert sort info
        $qry = new CcBlockcriteria();
        $qry->setDbCriteria('sort')
            ->setDbModifier('N/A')
            ->setDbValue($p_criteriaData['etc']['sp_sort_options'])
            ->setDbBlockId($this->id)
            ->save();

        // insert limit info
        $qry = new CcBlockcriteria();
        $qry->setDbCriteria('limit')
            ->setDbModifier($p_criteriaData['etc']['sp_limit_options'])
            ->setDbValue($p_criteriaData['etc']['sp_limit_value'])
            ->setDbBlockId($this->id)
            ->save();

        // insert repeat track option
        $qry = new CcBlockcriteria();
        $qry->setDbCriteria('repeat_tracks')
            ->setDbModifier('N/A')
            ->setDbValue($p_criteriaData['etc']['sp_repeat_tracks'])
            ->setDbBlockId($this->id)
            ->save();

        // insert overflow track option
        $qry = new CcBlockcriteria();
        $qry->setDbCriteria('overflow_tracks')
            ->setDbModifier('N/A')
            ->setDbValue($p_criteriaData['etc']['sp_overflow_tracks'])
            ->setDbBlockId($this->id)
            ->save();
    }

    /**
     * generate list of tracks. This function saves criteria and generate
     * tracks.
     *
     * @param array $p_criteria
     * @param mixed $returnList
     */
    public function generateSmartBlock($p_criteria, $returnList = false)
    {
        $this->saveSmartBlockCriteria($p_criteria);
        $insertList = $this->getListOfFilesUnderLimit();
        $this->deleteAllFilesFromBlock();
        // construct id array
        $ids = [];
        foreach ($insertList as $ele) {
            $ids[] = $ele['id'];
        }
        $this->addAudioClips(array_values($ids));
        // update length in playlist contents.
        $this->updateBlockLengthInAllPlaylist();

        return ['result' => 0];
    }

    public function updateBlockLengthInAllPlaylist()
    {
        $blocks = CcPlaylistcontentsQuery::create()->filterByDbBlockId($this->id)->find();
        $blocks->getFirst();
        $iterator = $blocks->getIterator();
        while ($iterator->valid()) {
            $length = $this->getUnformatedLength();
            if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}/', $length)) {
                $iterator->current()->setDbClipLength(null);
            } else {
                $iterator->current()->setDbClipLength($length);
            }
            $iterator->current()->save();
            $iterator->next();
        }
    }

    public function getListOfFilesUnderLimit($show = null)
    {
        $info = $this->getListofFilesMeetCriteria($show);
        $files = $info['files'];
        $limit = $info['limit'];
        $repeat = $info['repeat_tracks'];
        $overflow = $info['overflow_tracks'];

        $insertList = [];
        $totalTime = 0;
        $totalItems = 0;

        if ($files->isEmpty()) {
            return $insertList;
        }

        // this moves the pointer to the first element in the collection
        $files->getFirst();
        $iterator = $files->getIterator();

        $isBlockFull = false;

        while ($iterator->valid()) {
            $id = $iterator->current()->getDbId();
            $fileLength = $iterator->current()->getCueLength();
            $length = Application_Common_DateHelper::calculateLengthInSeconds($fileLength);
            // if the block is setup to allow the overflow of tracks this will add the next track even if it becomes
            // longer than the time limit
            if ($overflow == 1) {
                $insertList[] = ['id' => $id, 'length' => $length];
                $totalTime += $length;
                ++$totalItems;
            }
            // otherwise we need to check to determine if the track will make the playlist exceed the totalTime before
            // adding it this could loop through a lot of tracks so I used the totalItems limit to prevent
            // the algorithm from parsing too many items.

            else {
                $projectedTime = $totalTime + $length;
                if ($projectedTime > $limit['time']) {
                    ++$totalItems;
                } else {
                    $insertList[] = ['id' => $id, 'length' => $length];
                    $totalTime += $length;
                    ++$totalItems;
                }
            }
            if ((!is_null($limit['items']) && $limit['items'] == count($insertList)) || $totalItems > 500 || $totalTime > $limit['time']) {
                $isBlockFull = true;

                break;
            }

            $iterator->next();
        }

        $sizeOfInsert = count($insertList);

        // if block is not full and repeat_track is check, fill up more
        // additionally still don't overflow the limit
        while (!$isBlockFull && $repeat == 1 && $sizeOfInsert > 0) {
            Logging::debug('adding repeated tracks.');
            Logging::debug('total time = ' . $totalTime);

            $randomEleKey = array_rand(array_slice($insertList, 0, $sizeOfInsert));
            // this will also allow the overflow of tracks so that time limited smart blocks will schedule until they
            // are longer than the time limit rather than never scheduling past the time limit
            if ($overflow == 1) {
                $insertList[] = $insertList[$randomEleKey];
                $totalTime += $insertList[$randomEleKey]['length'];
                ++$totalItems;
            } else {
                $projectedTime = $totalTime + $insertList[$randomEleKey]['length'];
                if ($projectedTime > $limit['time']) {
                    ++$totalItems;
                } else {
                    $insertList[] = $insertList[$randomEleKey];
                    $totalTime += $insertList[$randomEleKey]['length'];
                    ++$totalItems;
                }
            }

            if ((!is_null($limit['items']) && $limit['items'] == count($insertList)) || $totalItems > 500 || $totalTime > $limit['time']) {
                break;
            }
        }

        return $insertList;
    }

    /**
     * Parses each row in the database for the criteria associated with this block and renders human readable labels.
     * Returns it as an array with each criteria_name and modifier_name added based upon options array lookup.
     */
    public function getCriteria()
    {
        $allCriteria = BlockCriteria::criteriaMap();
        $allOptions = CriteriaModifier::mapToDisplay();

        // Load criteria from db
        $out = CcBlockcriteriaQuery::create()->orderByDbCriteria()->findByDbBlockId($this->id);
        $storedCrit = [];

        foreach ($out as $crit) {
            $criteria = $crit->getDbCriteria();
            $modifier = $crit->getDbModifier();
            $value = $crit->getDbValue();
            $extra = $crit->getDbExtra();
            $criteriagroup = $crit->getDbCriteriaGroup();

            if ($criteria == 'limit') {
                $storedCrit['limit'] = [
                    'value' => $value,
                    'modifier' => $modifier,
                    'display_modifier' => _($modifier),
                ];
            } elseif ($criteria == 'repeat_tracks') {
                $storedCrit['repeat_tracks'] = ['value' => $value];
            } elseif ($criteria == 'overflow_tracks') {
                $storedCrit['overflow_tracks'] = ['value' => $value];
            } elseif ($criteria == 'sort') {
                $storedCrit['sort'] = ['value' => $value];
            } else {
                $c = $allCriteria[$criteria];
                $storedCrit['crit'][$criteria][] = [
                    'criteria' => $criteria,
                    'value' => $value,
                    'modifier' => $modifier,
                    'extra' => $extra,
                    'criteria_group' => $criteriagroup,
                    'display_name' => $c->display,
                    'display_modifier' => $allOptions[$modifier],
                ];
            }
        }

        return $storedCrit;
    }

    /**
     * Parses each row in the database for the criteria associated with this block and renders human readable labels.
     * Returns it as an array with each criteria_name and modifier_name added based upon options array lookup.
     * Maintains original separation of similar criteria that were separated by and statements.
     */
    public function getCriteriaGrouped()
    {
        $criteriaOptions = BlockCriteria::displayCriteria();
        $modifierOptions = CriteriaModifier::mapToDisplay();

        // Load criteria from db
        $out = CcBlockcriteriaQuery::create()->orderByDbCriteria()->findByDbBlockId($this->id);
        $storedCrit = [];

        foreach ($out as $crit) {
            $criteria = $crit->getDbCriteria();
            $modifier = $crit->getDbModifier();
            $value = $crit->getDbValue();
            $extra = $crit->getDbExtra();
            $criteriagroup = $crit->getDbCriteriaGroup();

            if ($criteria == 'limit') {
                $storedCrit['limit'] = [
                    'value' => $value,
                    'modifier' => $modifier,
                    'display_modifier' => _($modifier),
                ];
            } elseif ($criteria == 'repeat_tracks') {
                $storedCrit['repeat_tracks'] = ['value' => $value];
            } elseif ($criteria == 'overflow_tracks') {
                $storedCrit['overflow_tracks'] = ['value' => $value];
            } elseif ($criteria == 'sort') {
                $storedCrit['sort'] = ['value' => $value];
            } else {
                $storedCrit['crit'][$criteria . $criteriagroup][] = [
                    'criteria' => $criteria,
                    'value' => $value,
                    'modifier' => $modifier,
                    'extra' => $extra,
                    'display_name' => $criteriaOptions[$criteria],
                    'display_modifier' => $modifierOptions[$modifier],
                ];
            }
        }

        return $storedCrit;
    }

    // this function return list of propel object
    public function getListofFilesMeetCriteria($showLimit = null)
    {
        $storedCrit = $this->getCriteria();

        $qry = CcFilesQuery::create();
        $qry->useFkOwnerQuery('subj', 'left join');

        $allCriteria = BlockCriteria::criteriaMap();

        // Logging::info($storedCrit);
        if (isset($storedCrit['crit'])) {
            foreach ($storedCrit['crit'] as $crit) {
                $i = 0;
                $prevgroup = null;
                $group = null;
                // now we need to sort based upon extra which contains the and grouping from the form
                usort($crit, function ($a, $b) {
                    return $a['criteria_group'] - $b['criteria_group'];
                });
                // we need to run the following loop separately for each criteria group inside of each array
                foreach ($crit as $criteria) {
                    $group = $criteria['criteria_group'];
                    $spCriteria = $criteria['criteria'];
                    $spCriteriaModifier = $criteria['modifier'];

                    $column = CcFilesPeer::getTableMap()->getColumnByPhpName($allCriteria[$spCriteria]->peer);

                    // data should already be in UTC, do we have to do anything special here anymore?
                    if ($column->getType() == PropelColumnTypes::TIMESTAMP) {
                        $spCriteriaValue = $criteria['value'];

                        if (isset($criteria['extra'])) {
                            $spCriteriaExtra = $criteria['extra'];
                        }
                    } elseif ($spCriteria == 'bit_rate' || $spCriteria == 'sample_rate') {
                        // multiply 1000 because we store only number value
                        // e.g 192kps is stored as 192000
                        $spCriteriaValue = $criteria['value'] * 1000;
                        if (isset($criteria['extra'])) {
                            $spCriteriaExtra = $criteria['extra'] * 1000;
                        }
                    /*
                    * If user is searching for an exact match of length we need to
                    * search as if it starts with the specified length because the
                    * user only sees the rounded version (i.e. 4:02.7 is 4:02.761625
                    * in the database)
                    */
                    } elseif (in_array($spCriteria, ['length', 'cuein', 'cueout']) && $spCriteriaModifier == 'is') {
                        $spCriteriaModifier = 'starts with';
                        $spCriteria .= '::text';
                        $spCriteriaValue = $criteria['value'];
                    } else {
                        /* Propel does not escape special characters properly when using LIKE/ILIKE
                         * We have to add extra slashes in these cases
                         */
                        $tempModifier = trim(self::$modifier2CriteriaMap[$spCriteriaModifier]);
                        if ($tempModifier == 'ILIKE') {
                            $spCriteriaValue = addslashes($criteria['value']);
                            // addslashes() does not esapce '%' so we have to do it manually
                            $spCriteriaValue = str_replace('%', '\%', $spCriteriaValue);
                        } else {
                            $spCriteriaValue = $criteria['value'];
                        }
                        $spCriteriaExtra = $criteria['extra'];
                    }

                    if ($spCriteriaModifier == 'starts with') {
                        $spCriteriaValue = "{$spCriteriaValue}%";
                    } elseif ($spCriteriaModifier == 'ends with') {
                        $spCriteriaValue = "%{$spCriteriaValue}";
                    } elseif ($spCriteriaModifier == 'contains' || $spCriteriaModifier == 'does not contain') {
                        $spCriteriaValue = "%{$spCriteriaValue}%";
                    } elseif ($spCriteriaModifier == 'is in the range') {
                        $spCriteriaValue = "{$spCriteria} >= '{$spCriteriaValue}' AND {$spCriteria} <= '{$spCriteriaExtra}'";
                    } elseif ($spCriteriaModifier == 'before') {
                        // need to pull in the current time and subtract the value or figure out how to make it relative
                        $relativedate = new DateTime($spCriteriaValue);
                        $dt = $relativedate->format(DateTime::ISO8601);
                        $spCriteriaValue = "COALESCE({$spCriteria}, DATE '-infinity') <= '{$dt}'";
                    } elseif ($spCriteriaModifier == 'after') {
                        $relativedate = new DateTime($spCriteriaValue);
                        $dt = $relativedate->format(DateTime::ISO8601);
                        $spCriteriaValue = "COALESCE({$spCriteria}, DATE '-infinity') >= '{$dt}'";
                    } elseif ($spCriteriaModifier == 'between') {
                        $fromrelativedate = new DateTime($spCriteriaValue);
                        $fdt = $fromrelativedate->format(DateTime::ISO8601);

                        $torelativedate = new DateTime($spCriteriaExtra);
                        $tdt = $torelativedate->format(DateTime::ISO8601);
                        $spCriteriaValue = "COALESCE({$spCriteria}, DATE '-infinity') >= '{$fdt}' AND COALESCE({$spCriteria}, DATE '-infinity') <= '{$tdt}'";
                    }

                    $spCriteriaModifier = self::$modifier2CriteriaMap[$spCriteriaModifier];

                    try {
                        if ($spCriteria == 'owner_id') {
                            $spCriteria = 'subj.login';
                        }
                        if ($i > 0 && $prevgroup == $group) {
                            $qry->addOr($spCriteria, $spCriteriaValue, $spCriteriaModifier);
                        } else {
                            $qry->addAnd($spCriteria, $spCriteriaValue, $spCriteriaModifier);
                        }
                        // only add this NOT LIKE null if you aren't also matching on another criteria
                        if ($i == 0) {
                            if ($spCriteriaModifier == Criteria::NOT_ILIKE || $spCriteriaModifier == Criteria::NOT_EQUAL) {
                                $qry->addOr($spCriteria, null, Criteria::ISNULL);
                            }
                        }
                    } catch (Exception $e) {
                        Logging::info($e);
                    }
                    $prevgroup = $group;
                    ++$i;
                }
            }
        }

        // check if file exists
        $qry->add('file_exists', 'true', Criteria::EQUAL);
        $qry->add('hidden', 'false', Criteria::EQUAL);

        $sortTracks = 'random';
        if (isset($storedCrit['sort'])) {
            $sortTracks = $storedCrit['sort']['value'];
        }
        if ($sortTracks == 'newest') {
            $qry->addDescendingOrderByColumn('utime');
        } elseif ($sortTracks == 'oldest') {
            $qry->addAscendingOrderByColumn('utime');
        }
        // these sort additions are needed to override the default postgres NULL sort behavior
        elseif ($sortTracks == 'mostrecentplay') {
            $qry->addDescendingOrderByColumn('(lptime IS NULL), lptime');
        } elseif ($sortTracks == 'leastrecentplay') {
            $qry->addAscendingOrderByColumn('(lptime IS NOT NULL), lptime');
        } elseif ($sortTracks == 'random') {
            $qry->addAscendingOrderByColumn('random()');
        } else {
            Logging::warning('Unimplemented sortTracks type in ' . __FILE__);
        }

        // construct limit restriction
        $limits = [];
        if (isset($storedCrit['limit'])) {
            if ($storedCrit['limit']['modifier'] == 'items') {
                $limits['time'] = 1440 * 60;
                $limits['items'] = $storedCrit['limit']['value'];
            } elseif ($storedCrit['limit']['modifier'] == 'remaining') {
                // show will be null unless being called inside a show instance
                if (!is_null($showLimit)) {
                    $limits['time'] = $showLimit;
                    $limits['items'] = null;
                } else {
                    $limits['time'] = 60 * 60;
                    $limits['items'] = null;
                }
            } else {
                $limits['time'] = $storedCrit['limit']['modifier'] == 'hours' ?
                    intval(floatval($storedCrit['limit']['value']) * 60 * 60) :
                    intval($storedCrit['limit']['value'] * 60);
                $limits['items'] = null;
            }
        }

        $repeatTracks = 0;
        $overflowTracks = 0;

        if (isset($storedCrit['repeat_tracks'])) {
            $repeatTracks = $storedCrit['repeat_tracks']['value'];
        }

        if (isset($storedCrit['overflow_tracks'])) {
            $overflowTracks = $storedCrit['overflow_tracks']['value'];
        }

        try {
            $out = $qry->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)->find();

            return ['files' => $out, 'limit' => $limits, 'repeat_tracks' => $repeatTracks, 'overflow_tracks' => $overflowTracks, 'count' => $out->count()];
        } catch (Exception $e) {
            Logging::info($e);
        }
    }

    public static function organizeSmartPlaylistCriteria($p_criteria)
    {
        $fieldNames = ['sp_criteria_field', 'sp_criteria_modifier', 'sp_criteria_value', 'sp_criteria_extra', 'sp_criteria_datetime_select', 'sp_criteria_extra_datetime_select'];
        $output = [];
        foreach ($p_criteria as $ele) {
            $index = strrpos($ele['name'], '_');

            /* Strip field name of modifier index
             * Ex: sp_criteria_field_0_0 -> sp_criteria_field_0
             */
            $fieldName = substr($ele['name'], 0, $index);

            // Get criteria row index.
            $tempName = $ele['name'];
            // Get the last digit in the field name
            preg_match('/^\D*(?=\d)/', $tempName, $r);
            if (isset($r[0])) {
                $critIndexPos = strlen($r[0]);
                $critIndex = $tempName[$critIndexPos];
            }
            $lastChar = substr($ele['name'], -1);

            // If lastChar is an integer we should strip it off
            if (!preg_match('/^[a-zA-Z]$/', $lastChar)) {
                /* Strip field name of criteria index
                 * Ex: sp_criteria_field_0 -> sp_criteria_field
                 * We do this to check if the field name is a criteria
                 * or the block type
                 */
                $n = strrpos($fieldName, '_');
                $fieldName = substr($fieldName, 0, $n);
            }

            if (in_array($fieldName, $fieldNames)) {
                $output['criteria'][$critIndex][$lastChar][$fieldName] = trim($ele['value']);
            } else {
                $output['etc'][$ele['name']] = $ele['value'];
            }
        }

        return $output;
    }

    public static function getAllBlockFiles()
    {
        $sql = <<<'SQL'
SELECT distinct(file_id)
FROM cc_blockcontents
SQL;

        $files = Application_Common_Database::prepareAndExecute($sql, []);

        $real_files = [];
        foreach ($files as $f) {
            $real_files[] = $f['file_id'];
        }

        return $real_files;
    }
    // smart block functions end
}

class BlockNotFoundException extends Exception {}
class BlockNoPermissionException extends Exception {}
class BlockOutDatedException extends Exception {}
class BlockDyanmicException extends Exception {}

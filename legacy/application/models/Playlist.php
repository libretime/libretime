<?php

/**
 * @copyright 2010 Sourcefabric O.P.S.
 * @license https://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Playlist implements Application_Model_LibraryEditable
{
    public const CUE_ALL_ERROR = 0;
    public const CUE_IN_ERROR = 1;
    public const CUE_OUT_ERROR = 2;

    /**
     * propel connection object.
     */
    private $con;

    /**
     * unique id for the playlist.
     */
    private $id;

    /**
     * propel object for this playlist.
     */
    private $pl;

    /**
     * info needed to insert a new playlist element.
     */
    private $plItem = [
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

    public function __construct($id = null, $con = null)
    {
        if (isset($id)) {
            $this->pl = CcPlaylistQuery::create()->findPK($id);

            if (is_null($this->pl)) {
                throw new PlaylistNotFoundException();
            }
        } else {
            $this->pl = new CcPlaylist();
            $this->pl->setDbUTime(new DateTime('now', new DateTimeZone('UTC')));
            $this->pl->save();
        }

        $this->plItem['fadein'] = Application_Model_Preference::GetDefaultFadeIn();
        $this->plItem['fadeout'] = Application_Model_Preference::GetDefaultFadeOut();
        $this->plItem['crossfadeDuration'] = Application_Model_Preference::GetDefaultCrossfadeDuration();

        $this->con = isset($con) ? $con : Propel::getConnection(CcPlaylistPeer::DATABASE_NAME);
        $this->id = $this->pl->getDbId();
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
     * Rename stored virtual playlist.
     *
     * @param string $p_newname
     */
    public function setName($p_newname)
    {
        $this->pl->setDbName($p_newname);
        $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->pl->save($this->con);
    }

    /**
     * Get mnemonic playlist name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->pl->getDbName();
    }

    public function setDescription($p_description)
    {
        $this->pl->setDbDescription($p_description);
        $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->pl->save($this->con);
    }

    public function getDescription()
    {
        return $this->pl->getDbDescription();
    }

    public function getCreator()
    {
        return $this->pl->getCcSubjs()->getDbLogin();
    }

    public function getCreatorId()
    {
        return $this->pl->getCcSubjs()->getDbId();
    }

    public function setCreator($p_id)
    {
        $this->pl->setDbCreatorId($p_id);
        $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->pl->save($this->con);
    }

    public function getLastModified($format = null)
    {
        // Logging::info($this->pl->getDbMtime($format));
        // Logging::info($this->pl);
        return $this->pl->getDbMtime($format);
    }

    public function getSize()
    {
        return $this->pl->countCcPlaylistcontentss();
    }

    /**
     * Get the entire playlist as a two dimentional array, sorted in order of play.
     *
     * @param bool $filterFiles if this is true, it will only return files that has
     *                          file_exists flag set to true
     *
     * @return array
     */
    public function getContents($filterFiles = false)
    {
        $sql = <<<'SQL'
  SELECT *
   FROM (
           (SELECT pc.id AS id,
                   pc.type,
                   pc.position,
                   pc.cliplength AS LENGTH,
                   pc.cuein,
                   pc.cueout,
                   pc.fadein,
                   pc.fadeout,
                   pc.trackoffset,
                   f.id AS item_id,
                   f.track_title,
                   f.artist_name AS creator,
                   f.file_exists AS EXISTS,
                   f.filepath AS path,
                   f.length AS orig_length,
                   f.mime AS mime
            FROM cc_playlistcontents AS pc
            JOIN cc_files AS f ON pc.file_id=f.id
            WHERE pc.playlist_id = :playlist_id1
SQL;

        if ($filterFiles) {
            $sql .= <<<'SQL'
            AND f.file_exists = :file_exists
SQL;
        }
        $sql .= <<<'SQL'
              AND TYPE = 0)
         UNION ALL
           (SELECT pc.id AS id,
                   pc.TYPE, pc.position,
                            pc.cliplength AS LENGTH,
                            pc.cuein,
                            pc.cueout,
                            pc.fadein,
                            pc.fadeout,
                            pc.trackoffset,
                            ws.id AS item_id,
                            (ws.name || ': ' || ws.url) AS title,
                            sub.login AS creator,
                            't'::boolean AS EXISTS,
                            ws.url AS path,
                            ws.length AS orig_length,
                            ws.mime as mime
            FROM cc_playlistcontents AS pc
            JOIN cc_webstream AS ws ON pc.stream_id=ws.id
            LEFT JOIN cc_subjs AS sub ON sub.id = ws.creator_id
            WHERE pc.playlist_id = :playlist_id2
              AND pc.TYPE = 1)
         UNION ALL
           (SELECT pc.id AS id,
                   pc.TYPE, pc.position,
                            pc.cliplength AS LENGTH,
                            pc.cuein,
                            pc.cueout,
                            pc.fadein,
                            pc.fadeout,
                            pc.trackoffset,
                            bl.id AS item_id,
                            bl.name AS title,
                            sbj.login AS creator,
                            't'::boolean AS EXISTS,
                            NULL::text AS path,
                            bl.length AS orig_length,
                            NULL::text as mime
            FROM cc_playlistcontents AS pc
            JOIN cc_block AS bl ON pc.block_id=bl.id
            JOIN cc_subjs AS sbj ON bl.creator_id=sbj.id
            WHERE pc.playlist_id = :playlist_id3
              AND pc.TYPE = 2)) AS temp
   ORDER BY temp.position;
SQL;
        // Logging::info($sql);

        $params = [
            ':playlist_id1' => $this->id, ':playlist_id2' => $this->id, ':playlist_id3' => $this->id,
        ];
        if ($filterFiles) {
            $params[':file_exists'] = $filterFiles;
        }

        $rows = Application_Common_Database::prepareAndExecute($sql, $params);

        $offset = 0;
        foreach ($rows as &$row) {
            // Logging::info($row);

            $clipSec = Application_Common_DateHelper::playlistTimeToSeconds($row['length']);
            $row['trackSec'] = $clipSec;

            $row['cueInSec'] = Application_Common_DateHelper::playlistTimeToSeconds($row['cuein']);
            $row['cueOutSec'] = Application_Common_DateHelper::playlistTimeToSeconds($row['cueout']);

            $trackoffset = $row['trackoffset'];
            $offset += $clipSec;
            $offset -= $trackoffset;
            $offset_cliplength = Application_Common_DateHelper::secondsToPlaylistTime($offset);

            // format the length for UI.
            if ($row['type'] == 2) {
                $bl = new Application_Model_Block($row['item_id']);
                $formatter = new LengthFormatter($bl->getLength());
            } else {
                $formatter = new LengthFormatter($row['length']);
            }
            $row['length'] = $formatter->format();

            $formatter = new LengthFormatter($offset_cliplength);
            $row['offset'] = $formatter->format();

            // format the fades in format 00(.000000)
            $fades = $this->getFadeInfo($row['position']);
            $row['fadein'] = $fades[0] ?? null;
            $row['fadeout'] = $fades[1] ?? null;

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
     * but this isn't practical since fades shouldn't be very long usuall 1 second or less. This function
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

    // returns true/false and ids of dynamic blocks
    public function hasDynamicBlock()
    {
        $ids = $this->getIdsOfDynamicBlocks();
        if (count($ids) > 0) {
            return true;
        }

        return false;
    }

    public function getIdsOfDynamicBlocks()
    {
        $sql = "SELECT bl.id FROM cc_playlistcontents as pc
                JOIN cc_block as bl ON pc.type=2 AND pc.block_id=bl.id AND bl.type='dynamic'
                WHERE playlist_id=:playlist_id AND pc.type=2";

        return Application_Common_Database::prepareAndExecute($sql, [':playlist_id' => $this->id]);
    }

    // aggregate column on playlistcontents cliplength column.
    public function getLength()
    {
        if ($this->hasDynamicBlock()) {
            $ids = $this->getIdsOfDynamicBlocks();
            $length = $this->pl->getDbLength();
            foreach ($ids as $id) {
                $bl = new Application_Model_Block($id['id']);
                if ($bl->hasItemLimit()) {
                    return 'N/A';
                }
            }
            $formatter = new LengthFormatter($length);

            return '~' . $formatter->format();
        }

        return $this->pl->getDbLength();
    }

    private function insertPlaylistElement($info)
    {
        $row = new CcPlaylistcontents();
        $row->setDbPlaylistId($this->id);
        $row->setDbPosition($info['pos']);
        $row->setDbCliplength($info['cliplength']);
        $row->setDbCuein($info['cuein']);
        $row->setDbCueout($info['cueout']);
        $row->setDbFadein(Application_Common_DateHelper::secondsToPlaylistTime($info['fadein']));
        $row->setDbFadeout(Application_Common_DateHelper::secondsToPlaylistTime($info['fadeout']));
        if ($info['ftype'] == 'audioclip') {
            $row->setDbFileId($info['id']);
            $row->setDbTrackOffset($info['crossfadeDuration']);
            $type = 0;
        } elseif ($info['ftype'] == 'stream') {
            $row->setDbStreamId($info['id']);
            $type = 1;
        } elseif ($info['ftype'] == 'block') {
            $row->setDbBlockId($info['id']);
            $type = 2;
        }
        $row->setDbType($type);
        $row->save($this->con);
        // above save result update on cc_playlist table on length column.
        // but $this->pl doesn't get updated automatically
        // so we need to manually grab it again from DB so it has updated values
        // It is something to do FORMAT_ON_DEMAND( Lazy Loading )
        $this->pl = CcPlaylistQuery::create()->findPK($this->id);
    }

    private function buildEntry($p_item, $pos)
    {
        $objType = $p_item[1];
        $objId = $p_item[0];
        if ($objType == 'audioclip') {
            $obj = CcFilesQuery::create()->findPK($objId, $this->con);
        } elseif ($objType == 'stream') {
            $obj = CcWebstreamQuery::create()->findPK($objId, $this->con);
        } elseif ($objType == 'block') {
            $obj = CcBlockQuery::create()->findPK($objId, $this->con);
        } else {
            throw new Exception('Unknown file type');
        }

        if (isset($obj)) {
            if (($obj instanceof CcFiles && $obj->visible())
                || $obj instanceof CcWebstream
                || $obj instanceof CcBlock
            ) {
                $entry = $this->plItem;
                $entry['id'] = $obj->getDbId();
                $entry['pos'] = $pos;
                $entry['cliplength'] = $obj->getDbLength();

                if ($obj instanceof CcFiles && $obj) {
                    $entry['cuein'] = isset($p_item['cuein']) ?
                        $p_item['cuein'] : $obj->getDbCuein();

                    $entry['cueout'] = isset($p_item['cueout']) ?
                        $p_item['cueout'] : $obj->getDbCueout();

                    $cue_in = isset($p_item['cueInSec']) ?
                        $p_item['cueInSec'] : Application_Common_DateHelper::calculateLengthInSeconds($entry['cuein']);

                    $cue_out = isset($p_item['cueOutSec']) ?
                        $p_item['cueOutSec'] : Application_Common_DateHelper::calculateLengthInSeconds($entry['cueout']);

                    $entry['cliplength'] = isset($p_item['length']) ?
                        $p_item['length'] : Application_Common_DateHelper::secondsToPlaylistTime($cue_out - $cue_in);
                } elseif ($obj instanceof CcWebstream && $obj) {
                    $entry['cuein'] = '00:00:00';
                    $entry['cueout'] = $entry['cliplength'];
                }
                $entry['ftype'] = $objType;

                $entry['fadein'] = isset($p_item['fadein']) ?
                    $p_item['fadein'] : $entry['fadein'];

                $entry['fadeout'] = isset($p_item['fadeout']) ?
                    $p_item['fadeout'] : $entry['fadeout'];
            }

            return $entry;
        }

        throw new Exception('trying to add a object that does not exist.');
    }

    /*
     * @param array $p_items
     *     an array of audioclips to add to the playlist
     * @param int|null $p_afterItem
     *     item which to add the new items after in the playlist, null if added to the end.
     * @param string (before|after) $addAfter
     *      whether to add the clips before or after the selected item.
     */
    public function addAudioClips($p_items, $p_afterItem = null, $addType = 'after')
    {
        $this->con->beginTransaction();
        $contentsToUpdate = [];

        try {
            if (is_numeric($p_afterItem)) {
                $afterItem = CcPlaylistcontentsQuery::create()->findPK($p_afterItem);
                $index = $afterItem->getDbPosition();

                $pos = ($addType == 'after') ? $index + 1 : $index;

                $contentsToUpdate = CcPlaylistcontentsQuery::create()
                    ->filterByDbPlaylistId($this->id)
                    ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                    ->orderByDbPosition()
                    ->find($this->con);
            } else {
                // add to the end of the playlist
                if ($addType == 'after') {
                    $pos = $this->getSize();
                }
                // add to the beginning of the playlist.
                else {
                    $pos = 0;

                    $contentsToUpdate = CcPlaylistcontentsQuery::create()
                        ->filterByDbPlaylistId($this->id)
                        ->orderByDbPosition()
                        ->find($this->con);
                }

                $contentsToUpdate = CcPlaylistcontentsQuery::create()
                    ->filterByDbPlaylistId($this->id)
                    ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                    ->orderByDbPosition()
                    ->find($this->con);
            }

            foreach ($p_items as $ac) {
                $res = $this->insertPlaylistElement($this->buildEntry($ac, $pos));
                // update is_playlist flag in cc_files to indicate the
                // file belongs to a playlist or block (in this case a playlist)
                if ($ac[1] == 'audioclip') {
                    $db_file = CcFilesQuery::create()->findPk($ac[0], $this->con);
                    $db_file->setDbIsPlaylist(true)->save($this->con);
                }

                $pos = $pos + 1;
            }

            // reset the positions of the remaining items.
            for ($i = 0; $i < count($contentsToUpdate); ++$i) {
                $contentsToUpdate[$i]->setDbPosition($pos);
                $contentsToUpdate[$i]->save($this->con);
                $pos = $pos + 1;
            }

            $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    /**
     * Move audioClip to the new position in the playlist.
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
            $contentsToMove = CcPlaylistcontentsQuery::create()
                ->filterByDbId($p_items, Criteria::IN)
                ->orderByDbPosition()
                ->find($this->con);

            $otherContent = CcPlaylistcontentsQuery::create()
                ->filterByDbId($p_items, Criteria::NOT_IN)
                ->filterByDbPlaylistId($this->id)
                ->orderByDbPosition()
                ->find($this->con);

            $pos = 0;
            // moving items to beginning of the playlist.
            if (is_null($p_afterItem)) {
                Logging::info('moving items to beginning of playlist');

                foreach ($contentsToMove as $item) {
                    Logging::info("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    $pos = $pos + 1;
                }
                foreach ($otherContent as $item) {
                    Logging::info("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    $pos = $pos + 1;
                }
            } else {
                Logging::info("moving items after {$p_afterItem}");

                foreach ($otherContent as $item) {
                    Logging::info("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    $pos = $pos + 1;

                    if ($item->getDbId() == $p_afterItem) {
                        foreach ($contentsToMove as $move) {
                            Logging::info("item {$move->getDbId()} to pos {$pos}");
                            $move->setDbPosition($pos);
                            $move->save($this->con);
                            $pos = $pos + 1;
                        }
                    }
                }
            }

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }

        $this->pl = CcPlaylistQuery::create()->findPK($this->id);
        $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->pl->save($this->con);
    }

    /**
     * Remove audioClip from playlist.
     *
     * @param array $p_items
     *                       array of unique item ids to remove from the playlist..
     */
    public function delAudioClips($p_items)
    {
        $this->con->beginTransaction();

        try {
            // we need to get the file id of the item we are deleting
            // before the item gets deleted from the playlist
            $itemsToDelete = CcPlaylistcontentsQuery::create()
                ->filterByPrimaryKeys($p_items)
                ->filterByDbFileId(null, Criteria::NOT_EQUAL)
                ->find($this->con);

            CcPlaylistcontentsQuery::create()
                ->findPKs($p_items)
                ->delete($this->con);

            // now that the items have been deleted we can update the
            // is_playlist flag in cc_files
            Application_Model_StoredFile::setIsPlaylist($itemsToDelete, 'playlist', false);

            $contents = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->orderByDbPosition()
                ->find($this->con);

            // reset the positions of the remaining items.
            for ($i = 0; $i < count($contents); ++$i) {
                $contents[$i]->setDbPosition($i);
                $contents[$i]->save($this->con);
            }

            $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }
    }

    public function getFadeInfo($pos)
    {
        $row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

        if (!$row) {
            return null;
        }
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
     * Change fadeIn and fadeOut values for playlist Element.
     *
     * @param int        $id
     *                            id of audioclip in playlist contents table
     * @param string     $fadeIn
     *                            new value in ss.ssssss or extent format
     * @param string     $fadeOut
     *                            new value in ss.ssssss or extent format
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
            $row = CcPlaylistcontentsQuery::create()->findPK($id);

            if (is_null($row)) {
                throw new Exception('Playlist item does not exist.');
            }

            $clipLength = $row->getDbCliplength();
            if (!is_null($fadeIn)) {
                $sql = "SELECT :fadein::INTERVAL > INTERVAL '{$clipLength}'";
                if (Application_Common_Database::prepareAndExecute($sql, [':fadein' => $fadeIn], 'column')) {
                    // "Fade In can't be larger than overall playlength.";
                    $fadeIn = $clipLength;
                }
                $row->setDbFadein($fadeIn);

                if (!is_null($offset)) {
                    $row->setDbTrackOffset($offset);
                    $row->save($this->con);
                }
            }
            if (!is_null($fadeOut)) {
                $sql = "SELECT :fadeout::INTERVAL > INTERVAL '{$clipLength}'";
                if (Application_Common_Database::prepareAndExecute($sql, [':fadeout' => $fadeOut], 'column')) {
                    // Fade Out can't be larger than overall playlength.";
                    $fadeOut = $clipLength;
                }
                $row->setDbFadeout($fadeOut);
            }
            $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->pl->save($this->con);

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
            Logging::info("Setting playlist fade in {$fadein}");
            $row = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->filterByDbPosition(0)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), $fadein, null);
        }
        if (isset($fadeout)) {
            Logging::info("Setting playlist fade out {$fadeout}");
            $row = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->filterByDbPosition($this->getSize() - 1)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), null, $fadeout);
        }
    }

    /**
     * Change cueIn/cueOut values for playlist element.
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
                $errArray['type'] = self::CUE_ALL_ERROR;

                return $errArray;
            }

            $row = CcPlaylistcontentsQuery::create()
                ->joinWith(CcFilesPeer::OM_CLASS)
                ->filterByPrimaryKey($id)
                ->findOne($this->con);

            if (is_null($row)) {
                throw new Exception('Playlist item does not exist.');
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

                $sql = 'SELECT :cueIn::INTERVAL > :cueOut::INTERVAL';
                if (Application_Common_Database::prepareAndExecute($sql, [':cueIn' => $cueIn, ':cueOut' => $cueOut], 'column')) {
                    $errArray['error'] = _("Can't set cue in to be larger than cue out.");
                    $errArray['type'] = self::CUE_IN_ERROR;

                    return $errArray;
                }

                $sql = 'SELECT :cueOut::INTERVAL > :origLength::INTERVAL';
                if (Application_Common_Database::prepareAndExecute($sql, [':cueOut' => $cueOut, ':origLength' => $origLength], 'column')) {
                    $errArray['error'] = _("Can't set cue out to be greater than file length.");
                    $errArray['type'] = self::CUE_OUT_ERROR;

                    return $errArray;
                }

                $sql = 'SELECT :cueOut::INTERVAL - :cueIn::INTERVAL';
                $cliplength = Application_Common_Database::prepareAndExecute($sql, [':cueOut' => $cueOut, ':cueIn' => $cueIn], 'column');

                $row->setDbCuein($cueIn);
                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);
            } elseif (!is_null($cueIn)) {
                $sql = 'SELECT :cueIn::INTERVAL > :oldCueOut::INTERVAL';
                if (Application_Common_Database::prepareAndExecute($sql, [':cueIn' => $cueIn, ':oldCueOut' => $oldCueOut], 'column')) {
                    $errArray['error'] = _("Can't set cue in to be larger than cue out.");
                    $errArray['type'] = self::CUE_IN_ERROR;

                    return $errArray;
                }

                $sql = 'SELECT :oldCueOut::INTERVAL - :cueIn::INTERVAL';
                $cliplength = Application_Common_Database::prepareAndExecute($sql, [':cueIn' => $cueIn, ':oldCueOut' => $oldCueOut], 'column');

                $row->setDbCuein($cueIn);
                $row->setDBCliplength($cliplength);
            } elseif (!is_null($cueOut)) {
                if ($cueOut === '') {
                    $cueOut = $origLength;
                }

                $sql = 'SELECT :cueOut::INTERVAL < :oldCueIn::INTERVAL';
                if (Application_Common_Database::prepareAndExecute($sql, [':cueOut' => $cueOut, ':oldCueIn' => $oldCueIn], 'column')) {
                    $errArray['error'] = _("Can't set cue out to be smaller than cue in.");
                    $errArray['type'] = self::CUE_OUT_ERROR;

                    return $errArray;
                }

                $sql = 'SELECT :cueOut::INTERVAL > :origLength::INTERVAL';
                if (Application_Common_Database::prepareAndExecute($sql, [':cueOut' => $cueOut, ':origLength' => $origLength], 'column')) {
                    $errArray['error'] = _("Can't set cue out to be greater than file length.");
                    $errArray['type'] = self::CUE_OUT_ERROR;

                    return $errArray;
                }

                $sql = 'SELECT :cueOut::INTERVAL - :oldCueIn::INTERVAL';
                $cliplength = Application_Common_Database::prepareAndExecute($sql, [':cueOut' => $cueOut, ':oldCueIn' => $oldCueIn], 'column');

                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);
            }

            $cliplength = $row->getDbCliplength();

            $sql = 'SELECT :fadeIn::INTERVAL > :cliplength::INTERVAL';
            if (Application_Common_Database::prepareAndExecute($sql, [':fadeIn' => $fadeIn, ':cliplength' => $cliplength], 'column')) {
                $fadeIn = $cliplength;
                $row->setDbFadein($fadeIn);
            }

            $sql = 'SELECT :fadeOut::INTERVAL > :cliplength::INTERVAL';
            if (Application_Common_Database::prepareAndExecute($sql, [':fadeOut' => $fadeOut, ':cliplength' => $cliplength], 'column')) {
                $fadeOut = $cliplength;
                $row->setDbFadein($fadeOut);
            }

            $row->save($this->con);
            $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();

            throw $e;
        }

        return [
            'cliplength' => $cliplength, 'cueIn' => $cueIn, 'cueOut' => $cueOut, 'length' => $this->getLength(),
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

    public static function getPlaylistCount()
    {
        $sql = 'SELECT count(*) as cnt FROM cc_playlist';

        return Application_Common_Database::prepareAndExecute(
            $sql,
            [],
            Application_Common_Database::COLUMN
        );
    }

    /**
     * Delete the file from all playlists.
     *
     * @param string $p_fileId
     */
    public static function DeleteFileFromAllPlaylists($p_fileId)
    {
        CcPlaylistcontentsQuery::create()->filterByDbFileId($p_fileId)->delete();
    }

    /**
     * Delete playlists that match the ids..
     *
     * @param array $p_ids
     * @param mixed $p_userId
     */
    public static function deletePlaylists($p_ids, $p_userId)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $isAdminOrPM = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER]);

        // get only the files from the playlists
        // we are about to delete
        $itemsToDelete = CcPlaylistcontentsQuery::create()
            ->filterByDbPlaylistId($p_ids)
            ->filterByDbFileId(null, Criteria::NOT_EQUAL)
            ->find();

        $updateIsPlaylistFlag = false;

        if (!$isAdminOrPM) {
            $leftOver = self::playlistsNotOwnedByUser($p_ids, $p_userId);
            if (count($leftOver) == 0) {
                CcPlaylistQuery::create()->findPKs($p_ids)->delete();
                $updateIsPlaylistFlag = true;
            } else {
                throw new PlaylistNoPermissionException();
            }
        } else {
            CcPlaylistQuery::create()->findPKs($p_ids)->delete();
            $updateIsPlaylistFlag = true;
        }

        if ($updateIsPlaylistFlag) {
            // update is_playlist flag in cc_files
            Application_Model_StoredFile::setIsPlaylist(
                $itemsToDelete,
                'playlist',
                false
            );
        }
    }

    // This function returns that are not owen by $p_user_id among $p_ids
    private static function playlistsNotOwnedByUser($p_ids, $p_userId)
    {
        $ownedByUser = CcPlaylistQuery::create()->filterByDbCreatorId($p_userId)->find()->getData();
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
     * Delete all files from playlist.
     */
    public function deleteAllFilesFromPlaylist()
    {
        // get only the files from the playlist
        // we are about to clear out
        $itemsToDelete = CcPlaylistcontentsQuery::create()
            ->filterByDbPlaylistId($this->id)
            ->filterByDbFileId(null, Criteria::NOT_EQUAL)
            ->find();

        CcPlaylistcontentsQuery::create()->findByDbPlaylistId($this->id)->delete();

        // update is_playlist flag in cc_files
        Application_Model_StoredFile::setIsPlaylist(
            $itemsToDelete,
            'playlist',
            false
        );

        $this->pl->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));
        $this->pl->save($this->con);
        $this->con->commit();
    }

    public function shuffle()
    {
        $sql = <<<'SQL'
SELECT max(position) from cc_playlistcontents WHERE playlist_id=:p1
SQL;
        $out = Application_Common_Database::prepareAndExecute($sql, ['p1' => $this->id]);
        $maxPosition = $out[0]['max'];

        $map = range(0, $maxPosition);
        shuffle($map);

        $currentPos = implode(',', array_keys($map));
        $sql = 'UPDATE cc_playlistcontents SET position = CASE position ';
        foreach ($map as $current => $after) {
            $sql .= sprintf('WHEN %d THEN %d ', $current, $after);
        }
        $sql .= "END WHERE position IN ({$currentPos}) and playlist_id=:p1";

        Application_Common_Database::prepareAndExecute($sql, ['p1' => $this->id]);

        return ['result' => 0];
    }

    public static function getAllPlaylistFiles()
    {
        $sql = <<<'SQL'
SELECT distinct(file_id)
FROM cc_playlistcontents
WHERE file_id is not null
SQL;
        $files = Application_Common_Database::prepareAndExecute($sql);
        $real_files = [];
        foreach ($files as $f) {
            $real_files[] = $f['file_id'];
        }

        return $real_files;
    }

    public static function getAllPlaylistStreams()
    {
        $sql = <<<'SQL'
SELECT distinct(stream_id)
FROM cc_playlistcontents
WHERE stream_id is not null
SQL;
        $streams = Application_Common_Database::prepareAndExecute($sql);
        $real_streams = [];
        foreach ($streams as $s) {
            $real_streams[] = $s['stream_id'];
        }

        return $real_streams;
    }

    /** Find out if a playlist contains any files that have been deleted from disk.
     *  This function relies on the "file_exists" column in the database being accurate and true,
     *  which it should.
     *
     * @return bool true if there are missing files in this playlist, false otherwise
     */
    public function containsMissingFiles()
    {
        $playlistContents = $this->pl->getCcPlaylistcontentss('type = 0'); // type=0 is only files, not other types of media

        // Slightly faster than the other Propel version below (this one does an INNER JOIN):
        $missingFiles = CcFilesQuery::create()
            ->join('CcFiles.CcPlaylistcontents')
            ->where('CcPlaylistcontents.DbPlaylistId = ?', $this->pl->getDbId())
            ->where('CcFiles.DbFileExists = ?', 'false')
            ->find();

        // Nicer Propel version but slightly slower because it generates a LEFT JOIN:
        /*
        $missingFiles = CcPlaylistcontentsQuery::create()
        ->filterByDbPlaylistId($this->pl->getDbId())
        ->useCcFilesQuery()
        ->filterByDbFileExists(false)
        ->endUse()
        ->find();
        */

        if (!$missingFiles->isEmpty()) {
            return true;
        }

        return false;
    }
} // class Playlist

class PlaylistNotFoundException extends Exception {}
class PlaylistNoPermissionException extends Exception {}
class PlaylistOutDatedException extends Exception {}

<?php

require_once 'formatters/LengthFormatter.php';

/**
 *
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Playlist
{
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
    private $plItem = array(
        "id" => "",
        "pos" => "",
        "cliplength" => "",
        "cuein" => "00:00:00",
        "cueout" => "00:00:00",
        "fadein" => "0.0",
        "fadeout" => "0.0",
    );

    //using propel's phpNames.
    private $categories = array(
        "dc:title" => "Name",
        "dc:creator" => "Creator",
        "dc:description" => "Description",
        "dcterms:extent" => "Length"
    );

    public function __construct($id=null, $con=null)
    {
        if (isset($id)) {
            $this->pl = CcPlaylistQuery::create()->findPK($id);

            if (is_null($this->pl)) {
                throw new PlaylistNotFoundException();
            }
        } else {
            $this->pl = new CcPlaylist();
            $this->pl->setDbUTime(new DateTime("now", new DateTimeZone("UTC")));
            $this->pl->save();
        }

        $defaultFade = Application_Model_Preference::GetDefaultFade();
        if ($defaultFade !== "") {
            //fade is in format SS.uuuuuu

            $this->plItem["fadein"] = $defaultFade;
            $this->plItem["fadeout"] = $defaultFade;
        }

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
     * Rename stored virtual playlist
     *
     * @param string $p_newname
     */
    public function setName($p_newname)
    {
        $this->pl->setDbName($p_newname);
        $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->pl->save($this->con);
    }

     /**
     * Get mnemonic playlist name
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
        $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
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
        $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->pl->save($this->con);
    }

    public function getLastModified($format = null)
    {
        //Logging::log($this->pl->getDbMtime($format));
        //Logging::log($this->pl);
        return $this->pl->getDbMtime($format);
    }

    public function getSize()
    {
        return $this->pl->countCcPlaylistcontentss();
    }

    /**
     * Get the entire playlist as a two dimentional array, sorted in order of play.
     * @param boolean $filterFiles if this is true, it will only return files that has
     *             file_exists flag set to true
     * @return array
     */
    public function getContents($filterFiles=false)
    {
        Logging::log("Getting contents for playlist {$this->id}");
        $files = array();

        $sql = <<<SQL
  (SELECT *
   FROM (
           (SELECT pc.id AS id,
                   pc.type,
                   pc.position,
                   pc.cliplength AS LENGTH,
                   pc.cuein,
                   pc.cueout,
                   pc.fadein,
                   pc.fadeout,
                   f.id AS item_id,
                   f.track_title,
                   f.artist_name AS creator,
                   f.file_exists AS EXISTS,
                   f.filepath AS path
            FROM cc_playlistcontents AS pc
            JOIN cc_files AS f ON pc.file_id=f.id
            WHERE pc.playlist_id = {$this->id}
              AND TYPE = 0)
         UNION ALL
           (SELECT pc.id AS id,
                   pc.TYPE, pc.position,
                            pc.cliplength AS LENGTH,
                            pc.cuein,
                            pc.cueout,
                            pc.fadein,
                            pc.fadeout,
                            ws.id AS item_id,
                            (ws.name || ': ' || ws.url) AS title,
                            sub.login AS creator,
                            't'::boolean AS EXISTS,
                            ws.url AS path
            FROM cc_playlistcontents AS pc
            JOIN cc_webstream AS ws ON pc.stream_id=ws.id
            LEFT JOIN cc_subjs AS sub ON sub.id = ws.creator_id
            WHERE pc.playlist_id = {$this->id}
              AND pc.TYPE = 1)
         UNION ALL
           (SELECT pc.id AS id,
                   pc.TYPE, pc.position,
                            pc.cliplength AS LENGTH,
                            pc.cuein,
                            pc.cueout,
                            pc.fadein,
                            pc.fadeout,
                            bl.id AS item_id,
                            bl.name AS title,
                            sbj.login AS creator,
                            't'::boolean AS EXISTS,
                            NULL::text AS path
            FROM cc_playlistcontents AS pc
            JOIN cc_block AS bl ON pc.block_id=bl.id
            JOIN cc_subjs AS sbj ON bl.creator_id=sbj.id
            WHERE pc.playlist_id = {$this->id}
              AND pc.TYPE = 2)) AS temp
   ORDER BY temp.position);
SQL;

        $con = Propel::getConnection();
        $rows = $con->query($sql)->fetchAll();

        $offset = 0;
        foreach ($rows as &$row) {
            $clipSec = Application_Common_DateHelper::playlistTimeToSeconds($row['length']);
            $offset += $clipSec;
            $offset_cliplength = Application_Common_DateHelper::secondsToPlaylistTime($offset);
    
            //format the length for UI.
            if ($row['type'] == 2) {
                $bl = new Application_Model_Block($row['item_id']);
                $formatter = new LengthFormatter($bl->getFormattedLength());
            } else {
                $formatter = new LengthFormatter($row['length']);
            }
            $row['length'] = $formatter->format();
    
            $formatter = new LengthFormatter($offset_cliplength);
            $row['offset'] = $formatter->format();
        }

        return $rows;
    }

    /**
    * The database stores fades in 00:00:00 Time format with optional millisecond resolution .000000
    * but this isn't practical since fades shouldn't be very long usuall 1 second or less. This function
    * will normalize the fade so that it looks like 00.000000 to the user.
    **/
    public function normalizeFade($fade)
    {
        //First get rid of the first six characters 00:00: which will be added back later for db update
        $fade = substr($fade, 6);
  
        //Second add .000000 if the fade does't have milliseconds format already
        $dbFadeStrPos = strpos( $fade, '.' );
        if ($dbFadeStrPos === false) {
             $fade .= '.000000';
        } else {
            while (strlen($fade) < 9) {
                 $fade .= '0';
            }
        }

        //done, just need to set back the formated values
        return $fade;
    }
    
    // returns true/false and ids of dynamic blocks
    public function hasDynamicBlock(){
        $ids = $this->getIdsOfDynamicBlocks();
        if (count($ids) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getIdsOfDynamicBlocks() {
        $sql = "SELECT bl.id FROM cc_playlistcontents as pc
                JOIN cc_block as bl ON pc.type=2 AND pc.block_id=bl.id AND bl.type='dynamic'
                WHERE playlist_id={$this->id} AND pc.type=2";
        $r = $this->con->query($sql);
        $result = $r->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //aggregate column on playlistcontents cliplength column.
    public function getLength()
    {
        if ($this->hasDynamicBlock()){
            $ids = $this->getIdsOfDynamicBlocks();
            $length = $this->pl->getDbLength();
            foreach ($ids as $id){
                $bl = new Application_Model_Block($id['id']);
                if ($bl->hasItemLimit()) {
                    return "N/A";
                }
            }
            $formatter = new LengthFormatter($length);
            return "~".$formatter->format();
        } else {
            return $this->pl->getDbLength();
        }
    }

    private function insertPlaylistElement($info)
    {
        $row = new CcPlaylistcontents();
        $row->setDbPlaylistId($this->id);
        $row->setDbPosition($info["pos"]);
        $row->setDbCliplength($info["cliplength"]);
        $row->setDbCuein($info["cuein"]);
        $row->setDbCueout($info["cueout"]);
        $row->setDbFadein($info["fadein"]);
        $row->setDbFadeout($info["fadeout"]);
        if ($info["ftype"] == "audioclip") {
            $row->setDbFileId($info["id"]);
            $type = 0;
        } else if ($info["ftype"] == "stream") {
            $row->setDbStreamId($info["id"]);
            $type = 1;
        } else if ($info["ftype"] == "block") {
            $row->setDbBlockId($info["id"]);
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

    /*
     *
     */
    private function buildEntry($p_item, $pos)
    {
        $objType = $p_item[1];
        $objId = $p_item[0];
        if ($objType == 'audioclip') {
            $obj = CcFilesQuery::create()->findPK($objId, $this->con);
        } else if ($objType == "stream") {
            $obj = CcWebstreamQuery::create()->findPK($objId, $this->con);
        } else if ($objType == "block") {
            $obj = CcBlockQuery::create()->findPK($objId, $this->con);
        } else {
            throw new Exception("Unknown file type");
        }

        if (isset($obj)) {
            if (($obj instanceof CcFiles && $obj->getDbFileExists()) || $obj instanceof CcWebstream || $obj instanceof CcBlock) {
                $entry = $this->plItem;
                $entry["id"] = $obj->getDbId();
                $entry["pos"] = $pos;
                $entry["cliplength"] = $obj->getDbLength();
                $entry["cueout"] = $obj->getDbLength();
                $entry["ftype"] = $objType;
            }
            return $entry;
        } else {
            throw new Exception("trying to add a object that does not exist.");
        }
    }

    /*
     * @param array $p_items
     *     an array of audioclips to add to the playlist
     * @param int|null $p_afterItem
     *     item which to add the new items after in the playlist, null if added to the end.
     * @param string (before|after) $addAfter
     *      whether to add the clips before or after the selected item.
     */
    public function addAudioClips($p_items, $p_afterItem=null, $addType = 'after')
    {
        $this->con->beginTransaction();
        $contentsToUpdate = array();

        try {

            if (is_numeric($p_afterItem)) {
                Logging::log("Finding playlist content item {$p_afterItem}");

                $afterItem = CcPlaylistcontentsQuery::create()->findPK($p_afterItem);
                $index = $afterItem->getDbPosition();
                Logging::log("index is {$index}");
                $pos = ($addType == 'after') ? $index + 1 : $index;

                $contentsToUpdate = CcPlaylistcontentsQuery::create()
                    ->filterByDbPlaylistId($this->id)
                    ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                    ->orderByDbPosition()
                    ->find($this->con);

            } else {

                //add to the end of the playlist
                if ($addType == 'after') {
                    $pos = $this->getSize();
                }
                //add to the beginning of the playlist.
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

            Logging::log("Adding to playlist");
            Logging::log("at position {$pos}");
 
            foreach ($p_items as $ac) {
                $res = $this->insertPlaylistElement($this->buildEntry($ac, $pos));
                $pos = $pos + 1;
                Logging::log("Adding $ac[1] $ac[0]");

            }

            //reset the positions of the remaining items.
            for ($i = 0; $i < count($contentsToUpdate); $i++) {
                $contentsToUpdate[$i]->setDbPosition($pos);
                $contentsToUpdate[$i]->save($this->con);
                $pos = $pos + 1;
            }

            $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    /**
     * Move audioClip to the new position in the playlist
     *
     * @param array $p_items
     *      array of unique ids of the selected items
     * @param int $p_afterItem
     *      unique id of the item to move the clip after
     */
    public function moveAudioClips($p_items, $p_afterItem=NULL)
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
            //moving items to beginning of the playlist.
            if (is_null($p_afterItem)) {
                Logging::log("moving items to beginning of playlist");

                foreach ($contentsToMove as $item) {
                    Logging::log("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    $pos = $pos + 1;
                }
                foreach ($otherContent as $item) {
                    Logging::log("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    $pos = $pos + 1;
                }
            } else {
                Logging::log("moving items after {$p_afterItem}");

                foreach ($otherContent as $item) {
                    Logging::log("item {$item->getDbId()} to pos {$pos}");
                    $item->setDbPosition($pos);
                    $item->save($this->con);
                    $pos = $pos + 1;

                    if ($item->getDbId() == $p_afterItem) {
                        foreach ($contentsToMove as $move) {
                            Logging::log("item {$move->getDbId()} to pos {$pos}");
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
        $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->pl->save($this->con);
    }

    /**
     * Remove audioClip from playlist
     *
     * @param array $p_items
     *         array of unique item ids to remove from the playlist..
     */
    public function delAudioClips($p_items)
    {

        $this->con->beginTransaction();

        try {

            CcPlaylistcontentsQuery::create()
                ->findPKs($p_items)
                ->delete($this->con);

            $contents = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->orderByDbPosition()
                ->find($this->con);

            //reset the positions of the remaining items.
            for ($i = 0; $i < count($contents); $i++) {
                $contents[$i]->setDbPosition($i);
                $contents[$i]->save($this->con);
            }

            $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }

    public function getFadeInfo($pos)
    {
        Logging::log("Getting fade info for pos {$pos}");

        $row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();
            
        if (!$row) {
            return NULL;
        }
        #Propel returns values in form 00.000000 format which is for only seconds.
        $fadeIn = $row->getDbFadein();
        $fadeOut = $row->getDbFadeout();
        return array($fadeIn, $fadeOut);
	}

    /**
     * Change fadeIn and fadeOut values for playlist Element
     *
     * @param int $pos
     *         position of audioclip in playlist
     * @param string $fadeIn
     *         new value in ss.ssssss or extent format
     * @param string $fadeOut
     *         new value in ss.ssssss or extent format
     * @return boolean
     */
    public function changeFadeInfo($id, $fadeIn, $fadeOut)
    {
        //See issue CC-2065, pad the fadeIn and fadeOut so that it is TIME compatable with the DB schema
        //For the top level PlayList either fadeIn or fadeOut will sometimes be Null so need a gaurd against
       //setting it to nonNull for checks down below
        $fadeIn = $fadeIn?'00:00:'.$fadeIn:$fadeIn;
        $fadeOut = $fadeOut?'00:00:'.$fadeOut:$fadeOut;

        $this->con->beginTransaction();

        $errArray= array();

        try {
            $row = CcPlaylistcontentsQuery::create()->findPK($id);

            if (is_null($row)) {
                throw new Exception("Playlist item does not exist.");
            }

            $clipLength = $row->getDbCliplength();

            if (!is_null($fadeIn)) {

                $sql = "SELECT INTERVAL '{$fadeIn}' > INTERVAL '{$clipLength}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    //"Fade In can't be larger than overall playlength.";
                    $fadeIn = $clipLength;
                }
                $row->setDbFadein($fadeIn);
            }
            if (!is_null($fadeOut)) {

                $sql = "SELECT INTERVAL '{$fadeOut}' > INTERVAL '{$clipLength}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    //Fade Out can't be larger than overall playlength.";
                    $fadeOut = $clipLength;
                }
                $row->setDbFadeout($fadeOut);
            }

            $row->save($this->con);
            $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }

        return array("fadeIn" => $fadeIn, "fadeOut" => $fadeOut);
    }

    public function setfades($fadein, $fadeout)
    {
        if (isset($fadein)) {
            Logging::log("Setting playlist fade in {$fadein}");
            $row = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->filterByDbPosition(0)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), $fadein, null);
        }

        if (isset($fadeout)) {
            Logging::log("Setting playlist fade out {$fadeout}");
            $row = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->filterByDbPosition($this->getSize()-1)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), null, $fadeout);
        }
    }

    /**
     * Change cueIn/cueOut values for playlist element
     *
     * @param int $pos
     *         position of audioclip in playlist
     * @param string $cueIn
     *         new value in ss.ssssss or extent format
     * @param string $cueOut
     *         new value in ss.ssssss or extent format
     * @return boolean or pear error object
     */
    public function changeClipLength($id, $cueIn, $cueOut)
    {
        $this->con->beginTransaction();

        $errArray= array();

        try {
            if (is_null($cueIn) && is_null($cueOut)) {
                $errArray["error"] = "Cue in and cue out are null.";

                return $errArray;
            }

            $row = CcPlaylistcontentsQuery::create()
                ->joinWith(CcFilesPeer::OM_CLASS)
                ->filterByPrimaryKey($id)
                ->findOne($this->con);

            if (is_null($row)) {
                throw new Exception("Playlist item does not exist.");
            }

            $oldCueIn = $row->getDBCuein();
            $oldCueOut = $row->getDbCueout();
            $fadeIn = $row->getDbFadein();
            $fadeOut = $row->getDbFadeout();

            $file = $row->getCcFiles($this->con);
            $origLength = $file->getDbLength();

            if (!is_null($cueIn) && !is_null($cueOut)) {

                if ($cueOut === "") {
                    $cueOut = $origLength;
                }

                $sql = "SELECT INTERVAL '{$cueIn}' > INTERVAL '{$cueOut}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    $errArray["error"] = "Can't set cue in to be larger than cue out.";

                    return $errArray;
                }

                $sql = "SELECT INTERVAL '{$cueOut}' > INTERVAL '{$origLength}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    $errArray["error"] = "Can't set cue out to be greater than file length.";

                    return $errArray;
                }

                $sql = "SELECT INTERVAL '{$cueOut}' - INTERVAL '{$cueIn}'";
                $r = $this->con->query($sql);
                $cliplength = $r->fetchColumn(0);

                $row->setDbCuein($cueIn);
                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);

            } elseif (!is_null($cueIn)) {

                $sql = "SELECT INTERVAL '{$cueIn}' > INTERVAL '{$oldCueOut}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    $errArray["error"] = "Can't set cue in to be larger than cue out.";

                    return $errArray;
                }

                $sql = "SELECT INTERVAL '{$oldCueOut}' - INTERVAL '{$cueIn}'";
                $r = $this->con->query($sql);
                $cliplength = $r->fetchColumn(0);

                $row->setDbCuein($cueIn);
                $row->setDBCliplength($cliplength);
            } elseif (!is_null($cueOut)) {

                if ($cueOut === "") {
                    $cueOut = $origLength;
                }

                $sql = "SELECT INTERVAL '{$cueOut}' < INTERVAL '{$oldCueIn}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    $errArray["error"] = "Can't set cue out to be smaller than cue in.";

                    return $errArray;
                }

                $sql = "SELECT INTERVAL '{$cueOut}' > INTERVAL '{$origLength}'";
                $r = $this->con->query($sql);
                if ($r->fetchColumn(0)) {
                    $errArray["error"] = "Can't set cue out to be greater than file length.";

                    return $errArray;
                }

                $sql = "SELECT INTERVAL '{$cueOut}' - INTERVAL '{$oldCueIn}'";
                $r = $this->con->query($sql);
                $cliplength = $r->fetchColumn(0);

                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);
            }

            $cliplength = $row->getDbCliplength();

            $sql = "SELECT INTERVAL '{$fadeIn}' > INTERVAL '{$cliplength}'";
            $r = $this->con->query($sql);
            if ($r->fetchColumn(0)) {
                $fadeIn = $cliplength;
                $row->setDbFadein($fadeIn);
            }

            $sql = "SELECT INTERVAL '{$fadeOut}' > INTERVAL '{$cliplength}'";
            $r = $this->con->query($sql);
            if ($r->fetchColumn(0)) {
                $fadeOut = $cliplength;
                $row->setDbFadein($fadeOut);
            }

            $row->save($this->con);
            $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->pl->save($this->con);

            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }

        return array("cliplength"=> $cliplength, "cueIn"=> $cueIn, "cueOut"=> $cueOut, "length"=> $this->getLength(),
                        "fadeIn"=> $fadeIn, "fadeOut"=> $fadeOut);
    }

    public function getAllPLMetaData()
    {
        $categories = $this->categories;
        $md = array();

        foreach ($categories as $key => $val) {
            $method = 'get' . $val;
            $md[$key] = $this->$method();
        }

        return $md;
    }

    public function getMetaData($category)
    {
        $cat = $this->categories[$category];
        $method = 'get' . $cat;

        return $this->$method();
    }

    public function setMetaData($category, $value)
    {
        $cat = $this->categories[$category];

        $method = 'set' . $cat;
        $this->$method($value);
    }

    public static function getPlaylistCount()
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = 'SELECT count(*) as cnt FROM '.$CC_CONFIG["playListTable"];

        return $con->query($sql)->fetchColumn(0);
    }

    /**
     * Delete the file from all playlists.
     * @param string $p_fileId
     */
    public static function DeleteFileFromAllPlaylists($p_fileId)
    {
        CcPlaylistcontentsQuery::create()->filterByDbFileId($p_fileId)->delete();
    }

    /**
     * Delete playlists that match the ids..
     * @param array $p_ids
     */
    public static function deletePlaylists($p_ids, $p_userId)
    {
        $leftOver = self::playlistsNotOwnedByUser($p_ids, $p_userId);
        if (count($leftOver) == 0) {
            CcPlaylistQuery::create()->findPKs($p_ids)->delete();
        } else {
            throw new PlaylistNoPermissionException;
        }
    }
    
    // This function returns that are not owen by $p_user_id among $p_ids
    private static function playlistsNotOwnedByUser($p_ids, $p_userId){
        $ownedByUser = CcPlaylistQuery::create()->filterByDbCreatorId($p_userId)->find()->getData();
        $selectedPls = $p_ids;
        $ownedPls = array();
        foreach ($ownedByUser as $pl) {
            if (in_array($pl->getDbId(), $selectedPls)) {
                $ownedPls[] = $pl->getDbId();
            }
        }
        
        $leftOvers = array_diff($selectedPls, $ownedPls);
        return $leftOvers;
    }
    
    /**
     * Delete all files from playlist
     * @param int $p_playlistId
     */
    public function deleteAllFilesFromPlaylist()
    {
        CcPlaylistcontentsQuery::create()->findByDbPlaylistId($this->id)->delete();
    }

} // class Playlist

class PlaylistNotFoundException extends Exception {}
class PlaylistNoPermissionException extends Exception {}
class PlaylistOutDatedException extends Exception {}

<?php

require_once 'formatters/LengthFormatter.php';

/**
 *
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
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
    private $blockItem = array(
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
    
    private static $modifier2CriteriaMap = array(
            "contains" => Criteria::ILIKE,
            "does not contain" => Criteria::NOT_ILIKE,
            "is" => Criteria::EQUAL,
            "is not" => Criteria::NOT_EQUAL,
            "starts with" => Criteria::ILIKE,
            "ends with" => Criteria::ILIKE,
            "is greater than" => Criteria::GREATER_THAN,
            "is less than" => Criteria::LESS_THAN,
            "is in the range" => Criteria::CUSTOM);
    
    private static $criteria2PeerMap = array(
            0 => "Select criteria",
            "album_title" => "DbAlbumTitle",
            "artist_name" => "DbArtistName",
            "bit_rate" => "DbBitRate",
            "bpm" => "DbBpm",
            "comments" => "DbComments",
            "composer" => "DbComposer",
            "conductor" => "DbConductor",
            "utime" => "DbUtime",
            "mtime" => "DbMtime",
            "lptime" => "DbLPtime",
            "disc_number" => "DbDiscNumber",
            "genre" => "DbGenre",
            "isrc_number" => "DbIsrcNumber",
            "label" => "DbLabel",
            "language" => "DbLanguage",
            "length" => "DbLength",
            "lyricist" => "DbLyricist",
            "mood" => "DbMood",
            "name" => "DbName",
            "orchestra" => "DbOrchestra",
            "rating" => "DbRating",
            "sample_rate" => "DbSampleRate",
            "track_title" => "DbTrackTitle",
            "track_number" => "DbTrackNumber",
            "year" => "DbYear"
    );
    
    public function __construct($id=null, $con=null)
    {
        if (isset($id)) {
            $this->block = CcBlockQuery::create()->findPk($id);
    
            if (is_null($this->block)) {
                throw new BlockNotFoundException();
            }
        } else {
            $this->block = new CcBlock();
            $this->block->setDbUTime(new DateTime("now", new DateTimeZone("UTC")));
            $this->block->save();
        }
    
        $defaultFade = Application_Model_Preference::GetDefaultFade();
        if ($defaultFade !== "") {
            //fade is in format SS.uuuuuu
    
            $this->blockItem["fadein"] = $defaultFade;
            $this->blockItem["fadeout"] = $defaultFade;
        }
    
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
     * Rename stored virtual block
     *
     * @param string $p_newname
     */
    public function setName($p_newname)
    {
        $this->block->setDbName($p_newname);
        $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->block->save($this->con);
    }
    
    /**
     * Get mnemonic block name
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
        $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
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
        $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
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
     * Get the entire block as a two dimentional array, sorted in order of play.
     * @param boolean $filterFiles if this is true, it will only return files that has
     *             file_exists flag set to true
     * @return array
     */
    public function getContents($filterFiles=false)
    {
        Logging::log("Getting contents for block {$this->id}");

        $files = array();
        $sql = <<<"EOT"
    SELECT pc.id as id, pc.position, pc.cliplength as length, pc.cuein, pc.cueout, pc.fadein, pc.fadeout, bl.type, f.length as orig_length,
    f.id as item_id, f.track_title, f.artist_name as creator, f.file_exists as exists, f.filepath as path FROM cc_blockcontents AS pc
    LEFT JOIN cc_files AS f ON pc.file_id=f.id
    LEFT JOIN cc_block AS bl ON pc.block_id = bl.id
    WHERE pc.block_id = {$this->id}
    ORDER BY pc.position;
EOT;
        $con = Propel::getConnection();
        $rows = $con->query($sql)->fetchAll();

        $offset = 0;
        foreach ($rows as &$row) {

            $clipSec = Application_Common_DateHelper::playlistTimeToSeconds($row['length']);
            $offset += $clipSec;
            $offset_cliplength = Application_Common_DateHelper::secondsToPlaylistTime($offset);
    
            //format the length for UI.
            $formatter = new LengthFormatter($row['length']);
            $row['length'] = $formatter->format();
    
            $formatter = new LengthFormatter($offset_cliplength);
            $row['offset'] = $formatter->format();
            
            //format the fades in format 00(.000000)
            $fades = $this->getFadeInfo($row['position']);
            $row['fadein'] = $fades[0];
            $row['fadeout'] = $fades[1];
            
            //format original length
            $formatter = new LengthFormatter($row['orig_length']);
            $row['orig_length'] = $formatter->format();
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
        if ( $dbFadeStrPos === False )
            $fade .= '.000000';
        else
            while( strlen( $fade ) < 9 )
            $fade .= '0';
    
        //done, just need to set back the formated values
        return $fade;
    }
    
    public function getUnformatedLength()
    {
        $this->block->reload();
        if ($this->isStatic()){
            $length = $this->block->getDbLength();
        } else {
            $length = $this->getDynamicBlockLength();
        }
        return $length;
    }
    
    public function getLength()
    {
        $this->block->reload();
        $prepend = "";
        if ($this->isStatic()){
            $length = $this->block->getDbLength();
        } else {
            $length = $this->getDynamicBlockLength();
            if (!$this->hasItemLimit()) {
                $prepend = "~";
            }
        }
        $formatter = new LengthFormatter($length);
        $length = $prepend.$formatter->format();
        return $length;
    }
    
    public function getDynamicBlockLength()
    {
        list($value, $modifier) = $this->getLimitValueAndModifier();
        if ($modifier == "items") {
            $length = $value." ".$modifier;
        } else {
            $hour = "00";
            if ($modifier == "minutes") {
                if ($value >59) {
                    $hour = intval($value/60);
                    $value = $value%60;
                    
                }
            } else if ($modifier == "hours") {
                $mins = $value * 60;
                if ($mins >59) {
                    $hour = intval($mins/60);
                    $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
                    $value = $mins%60;
                }
            }
            $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
            $value = str_pad($value, 2, "0", STR_PAD_LEFT);
            $length = $hour.":".$value.":00";
        }
        return $length;
    }
    
    public function getLimitValueAndModifier()
    {
        $result = CcBlockcriteriaQuery::create()->filterByDbBlockId($this->id)
                ->filterByDbCriteria('limit')->findOne();
        $modifier = $result->getDbModifier();
        $value = $result->getDbValue();
        return array($value, $modifier);
    }
    
    // this function returns sum of all track length under this block.
    public function getStaticLength(){
        $sql = "SELECT SUM(cliplength) as length FROM cc_blockcontents WHERE block_id={$this->id}";
        $r = $this->con->query($sql);
        $result = $r->fetchAll(PDO::FETCH_NUM);
        return $result[0][0];
    }
    
    private function insertBlockElement($info)
    {
        $row = new CcBlockcontents();
        $row->setDbBlockId($this->id);
        $row->setDbFileId($info["id"]);
        $row->setDbPosition($info["pos"]);
        $row->setDbCliplength($info["cliplength"]);
        $row->setDbCuein($info["cuein"]);
        $row->setDbCueout($info["cueout"]);
        $row->setDbFadein($info["fadein"]);
        $row->setDbFadeout($info["fadeout"]);
        $row->save($this->con);
        // above save result update on cc_block table on length column.
        // but $this->block doesn't get updated automatically
        // so we need to manually grab it again from DB so it has updated values
        // It is something to do FORMAT_ON_DEMAND( Lazy Loading )
        $this->block = CcBlockQuery::create()->findPK($this->id);
    }
    
    /*
     *
    */
    private function buildEntry($p_item, $pos)
    {
        $file = CcFilesQuery::create()->findPK($p_item, $this->con);
    
        if (isset($file) && $file->getDbFileExists()) {
            $entry = $this->blockItem;
            $entry["id"] = $file->getDbId();
            $entry["pos"] = $pos;
            $entry["cliplength"] = $file->getDbLength();
            $entry["cueout"] = $file->getDbLength();
    
            return $entry;
        } else {
            throw new Exception("trying to add a file that does not exist.");
        }
    }
    
    public function isStatic(){
        if ($this->block->getDbType() == "static") {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * @param array $p_items
    *     an array of audioclips to add to the block
    * @param int|null $p_afterItem
    *     item which to add the new items after in the block, null if added to the end.
    * @param string (before|after) $addAfter
    *      whether to add the clips before or after the selected item.
    */
    public function addAudioClips($p_items, $p_afterItem=NULL, $addType = 'after')
    {
        $this->con->beginTransaction();
        $contentsToUpdate = array();
    
        try {
    
            if (is_numeric($p_afterItem)) {
                Logging::log("Finding block content item {$p_afterItem}");
    
                $afterItem = CcBlockcontentsQuery::create()->findPK($p_afterItem);
                $index = $afterItem->getDbPosition();
                Logging::log("index is {$index}");
                $pos = ($addType == 'after') ? $index + 1 : $index;
    
                $contentsToUpdate = CcBlockcontentsQuery::create()
                ->filterByDbBlockId($this->id)
                ->filterByDbPosition($pos, Criteria::GREATER_EQUAL)
                ->orderByDbPosition()
                ->find($this->con);
    
                Logging::log("Adding to block");
                Logging::log("at position {$pos}");
            } else {
    
                //add to the end of the block
                if ($addType == 'after') {
                    $pos = $this->getSize();
                }
                //add to the beginning of the block.
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
    
                Logging::log("Adding to block");
                Logging::log("at position {$pos}");
            }
    
            foreach ($p_items as $ac) {
                Logging::log("Adding audio file {$ac}");
                
                if (is_array($ac) && $ac[1] == 'audioclip') {
                    $res = $this->insertBlockElement($this->buildEntry($ac[0], $pos));
                    $pos = $pos + 1;
                } elseif (!is_array($ac)) {
                    $res = $this->insertBlockElement($this->buildEntry($ac, $pos));
                    $pos = $pos + 1;
                }
            }
    
            //reset the positions of the remaining items.
            for ($i = 0; $i < count($contentsToUpdate); $i++) {
                $contentsToUpdate[$i]->setDbPosition($pos);
                $contentsToUpdate[$i]->save($this->con);
                $pos = $pos + 1;
            }
    
            $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->block->save($this->con);
    
            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }
    
    /**
     * Move audioClip to the new position in the block
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
            //moving items to beginning of the block.
            if (is_null($p_afterItem)) {
                Logging::log("moving items to beginning of block");
    
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
    
        $this->block = CcBlockQuery::create()->findPK($this->id);
        $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->block->save($this->con);
    }
    
    /**
     * Remove audioClip from block
     *
     * @param array $p_items
     *         array of unique item ids to remove from the block..
     */
    public function delAudioClips($p_items)
    {
    
        $this->con->beginTransaction();
    
        try {
    
            CcBlockcontentsQuery::create()
            ->findPKs($p_items)
            ->delete($this->con);
    
            $contents = CcBlockcontentsQuery::create()
            ->filterByDbBlockId($this->id)
            ->orderByDbPosition()
            ->find($this->con);
    
            //reset the positions of the remaining items.
            for ($i = 0; $i < count($contents); $i++) {
                $contents[$i]->setDbPosition($i);
                $contents[$i]->save($this->con);
            }
    
            $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->block->save($this->con);
    
            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }
    
    public function getFadeInfo($pos)
    {
        Logging::log("Getting fade info for pos {$pos}");
    
        $row = CcBlockcontentsQuery::create()
        ->joinWith(CcFilesPeer::OM_CLASS)
        ->filterByDbBlockId($this->id)
        ->filterByDbPosition($pos)
        ->findOne();
    
    
    
        #Propel returns values in form 00.000000 format which is for only seconds.
        $fadeIn = $row->getDbFadein();
        $fadeOut = $row->getDbFadeout();
        return array($fadeIn, $fadeOut);
    }
    
    /**
    * Change fadeIn and fadeOut values for block Element
    *
    * @param int $pos
    *         position of audioclip in block
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
            $row = CcBlockcontentsQuery::create()->findPK($id);
        
            if (is_null($row)) {
                throw new Exception("Block item does not exist.");
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
            $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->block->save($this->con);
    
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
            Logging::log("Setting block fade in {$fadein}");
            $row = CcBlockcontentsQuery::create()
            ->filterByDbBlockId($this->id)
            ->filterByDbPosition(0)
            ->findOne($this->con);
        
            $this->changeFadeInfo($row->getDbId(), $fadein, null);
        }
        
        if (isset($fadeout)) {
            Logging::log("Setting block fade out {$fadeout}");
            $row = CcBlockcontentsQuery::create()
            ->filterByDbBlockId($this->id)
            ->filterByDbPosition($this->getSize()-1)
            ->findOne($this->con);
            
            $this->changeFadeInfo($row->getDbId(), null, $fadeout);
        }
    }
    
    /**
    * Change cueIn/cueOut values for block element
    *
    * @param int $pos
    *         position of audioclip in block
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
    
            $row = CcBlockcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByPrimaryKey($id)
            ->findOne($this->con);
            
            if (is_null($row)) {
                throw new Exception("Block item does not exist.");
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
            $this->block->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->block->save($this->con);
    
            $this->con->commit();
        } catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    
        return array("cliplength"=> $cliplength, "cueIn"=> $cueIn, "cueOut"=> $cueOut, "length"=> $this->getUnformatedLength(),
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
    
    public function setMetadata($category, $value)
    {
        $cat = $this->categories[$category];
    
        $method = 'set' . $cat;
        $this->$method($value);
    }
    
    public static function getBlockCount()
    {
        global $CC_CONFIG;
         $con = Propel::getConnection();
         $sql = 'SELECT count(*) as cnt FROM '.$CC_CONFIG["playListTable"];
    
         return $con->query($sql)->fetchColumn(0);
     }
    
    /**
    * Delete the file from all blocks.
    * @param string $p_fileId
    */
    public static function DeleteFileFromAllBlocks($p_fileId)
    {
         CcBlockcontentsQuery::create()->filterByDbFileId($p_fileId)->delete();
    }
    
    /**
    * Delete blocks that match the ids..
    * @param array $p_ids
    */
    public static function deleteBlocks($p_ids, $p_userId)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
        
        if (!$isAdminOrPM) {
            $leftOver = self::blocksNotOwnedByUser($p_ids, $p_userId);
        
            if (count($leftOver) == 0) {
                CcBlockQuery::create()->findPKs($p_ids)->delete();
            } else {
                throw new BlockNoPermissionException;
            }
        } else {
            CcBlockQuery::create()->findPKs($p_ids)->delete();
        }
    }
    
    // This function returns that are not owen by $p_user_id among $p_ids
    private static function blocksNotOwnedByUser($p_ids, $p_userId)
    {
        $ownedByUser = CcBlockQuery::create()->filterByDbCreatorId($p_userId)->find()->getData();
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
    * Delete all files from block
    */
    public function deleteAllFilesFromBlock()
    {
        CcBlockcontentsQuery::create()->findByDbBlockId($this->id)->delete();
        $this->block->reload();
    }
    
    // smart block functions start
    public function shuffleSmartBlock(){
        // if it here that means it's static pl
        $this->saveType("static");
        $contents = CcBlockcontentsQuery::create()
        ->filterByDbBlockId($this->id)
        ->orderByDbPosition()
        ->find();
        $shuffledPos = range(0, count($contents)-1);
        shuffle($shuffledPos);
        foreach ($contents as $item) {
            $item->setDbPosition(array_shift($shuffledPos));
            $item->save();
        }
        return array("result"=>0);
    }
    
    public function saveType($p_blockType)
    {
        // saving dynamic/static flag
        CcBlockQuery::create()->findPk($this->id)->setDbType($p_blockType)->save();
    }
    
    public function setLength($value){
        $this->block->setDbLength($value);
        $this->block->save($this->con);
        $this->updateBlockLengthInAllPlaylist();
    }
    
    
    /**
     * Saves smart block criteria
     * @param array $p_criteria
     */
    public function saveSmartBlockCriteria($p_criteria)
    {
        $data = $this->organizeSmartPlyalistCriteria($p_criteria);
        // saving dynamic/static flag
        $blockType = $data['etc']['sp_type'] == 0 ? 'static':'dynamic';
        $this->saveType($blockType);
        $this->storeCriteriaIntoDb($data);
        //get number of files that meet the criteria
        $files = $this->getListofFilesMeetCriteria();
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
                $length = "00:00:00";
            }
            $this->setLength($length);
        }
        
        $this->updateBlockLengthInAllPlaylist();
    }
    
    public function hasItemLimit()
    {
        list($value, $modifier) = $this->getLimitValueAndModifier();
        if ($modifier == 'items') {
            return true;
        } else {
            return false;
        }
    }
    
    public function storeCriteriaIntoDb($p_criteriaData){
        // delete criteria under $p_blockId
        CcBlockcriteriaQuery::create()->findByDbBlockId($this->id)->delete();
        Logging::log($p_criteriaData);
        //insert modifier rows
        if (isset($p_criteriaData['criteria'])) {
            $critKeys = array_keys($p_criteriaData['criteria']);
            for ($i = 0; $i < count($critKeys); $i++) {
                foreach( $p_criteriaData['criteria'][$critKeys[$i]] as $d){
                    $qry = new CcBlockcriteria();
                    $qry->setDbCriteria($d['sp_criteria_field'])
                    ->setDbModifier($d['sp_criteria_modifier'])
                    ->setDbValue($d['sp_criteria_value'])
                    ->setDbBlockId($this->id);
            
                    if (isset($d['sp_criteria_extra'])) {
                        $qry->setDbExtra($d['sp_criteria_extra']);
                    }
                    $qry->save();
                }
            }
        }
    
        // insert limit info
        $qry = new CcBlockcriteria();
        $qry->setDbCriteria("limit")
        ->setDbModifier($p_criteriaData['etc']['sp_limit_options'])
        ->setDbValue($p_criteriaData['etc']['sp_limit_value'])
        ->setDbBlockId($this->id)
        ->save();
    }
    
    /**
     * generate list of tracks. This function saves creiteria and generate
     * tracks.
     * @param array $p_criteria
     */
    public function generateSmartBlock($p_criteria, $returnList=false)
    {
        $this->saveSmartBlockCriteria($p_criteria);
        $insertList = $this->getListOfFilesUnderLimit();
        $this->deleteAllFilesFromBlock();
        $this->addAudioClips(array_keys($insertList));
        // update length in playlist contents.
        $this->updateBlockLengthInAllPlaylist();
        return array("result"=>0);
    }
    
    public function updateBlockLengthInAllPlaylist()
    {
        $blocks = CcPlaylistcontentsQuery::create()->filterByDbBlockId($this->id)->find();
        $blocks->getFirst();
        $iterator = $blocks->getIterator();
        while ($iterator->valid()) {
            $length = $this->getUnformatedLength();
            if (!preg_match("/^[0-9]{2}:[0-9]{2}:[0-9]{2}/", $length)) {
                $iterator->current()->setDbClipLength(null);
            } else {
                $iterator->current()->setDbClipLength($length);
            }
            $iterator->current()->save();
            $iterator->next();
        }
    }
    
    public function getListOfFilesUnderLimit()
    {
        $info = $this->getListofFilesMeetCriteria();
        $files = $info['files'];
        $limit = $info['limit'];
    
        $insertList = array();
        $totalTime = 0;
        $totalItems = 0;
    
        // this moves the pointer to the first element in the collection
        $files->getFirst();
        $iterator = $files->getIterator();
        while ($iterator->valid() && $totalTime < $limit['time']) {
            $id = $iterator->current()->getDbId();
            $length = Application_Common_DateHelper::calculateLengthInSeconds($iterator->current()->getDbLength());
            $insertList[$id] = $length;
            $totalTime += $length;
            $totalItems++;
            
            if ((!is_null($limit['items']) && $limit['items'] == count($insertList)) || $totalItems > 500) {
                break;
            }
    
            $iterator->next();
        }
        return $insertList;
    }
    
    public function getCriteria()
    {
        $criteriaOptions = array(
                0 => "Select criteria",
                "album_title" => "Album",
                "bit_rate" => "Bit Rate",
                "bpm" => "Bpm",
                "comments" => "Comments",
                "composer" => "Composer",
                "conductor" => "Conductor",
                "artist_name" => "Creator",
                "disc_number" => "Disc Number",
                "genre" => "Genre",
                "isrc_number" => "ISRC",
                "label" => "Label",
                "language" => "Language",
                "mtime" => "Last Modified",
                "lptime" => "Last Played",
                "length" => "Length",
                "lyricist" => "Lyricist",
                "mood" => "Mood",
                "name" => "Name",
                "orchestra" => "Orchestra",
                "rating" => "Rating",
                "sample_rate" => "Sample Rate",
                "track_title" => "Title",
                "track_number" => "Track Number",
                "utime" => "Uploaded",
                "year" => "Year"
        );
        
        // Load criteria from db
        $out = CcBlockcriteriaQuery::create()->orderByDbCriteria()->findByDbBlockId($this->id);
        $storedCrit = array();

        foreach ($out as $crit) {
            $criteria = $crit->getDbCriteria();
            $modifier = $crit->getDbModifier();
            $value = $crit->getDbValue();
            $extra = $crit->getDbExtra();
        
            if ($criteria == "limit") {
                $storedCrit["limit"] = array("value"=>$value, "modifier"=>$modifier);
            } else {
                $storedCrit["crit"][$criteria][] = array("criteria"=>$criteria, "value"=>$value, "modifier"=>$modifier, "extra"=>$extra, "display_name"=>$criteriaOptions[$criteria]);
            }
        }
        
        return $storedCrit;
        
    }
    
    // this function return list of propel object
    public function getListofFilesMeetCriteria()
    {
        $storedCrit = $this->getCriteria();
    
        $qry = CcFilesQuery::create();
    
        if (isset($storedCrit["crit"])) {
            foreach ($storedCrit["crit"] as $crit) {
                $i = 0;
                foreach ($crit as $criteria) {
                    $spCriteriaPhpName = self::$criteria2PeerMap[$criteria['criteria']];
                    $spCriteria = $criteria['criteria'];
                    $spCriteriaModifier = $criteria['modifier'];
                    
                    $column = CcFilesPeer::getTableMap()->getColumnByPhpName(self::$criteria2PeerMap[$spCriteria]);
                    // if the column is timestamp, convert it into UTC
                    if ($column->getType() == PropelColumnTypes::TIMESTAMP) {
                        $spCriteriaValue = Application_Common_DateHelper::ConvertToUtcDateTimeString($criteria['value']);
                        /* Check if only a date was supplied and trim
                         * the time after it is converted to UTC time
                         */
                        if (strlen($criteria['value']) <= 10) {
                            //extract date only from timestamp in db
                            $spCriteria = 'date('.$spCriteria.')';
                            $spCriteriaValue = substr($spCriteriaValue, 0, 10);
                        }
                    } else if($spCriteria == "bit_rate") {
                        // multiply 1000 because we store only number value
                        // e.g 192kps is stored as 192000
                        $spCriteriaValue = $criteria['value']*1000;
                    } else {
                        $spCriteriaValue = addslashes($criteria['value']);
                    }
                    
                    if ($spCriteriaModifier == "starts with") {
                        $spCriteriaValue = "$spCriteriaValue%";
                    } else if ($spCriteriaModifier == "ends with") {
                        $spCriteriaValue = "%$spCriteriaValue";
                    } else if ($spCriteriaModifier == "contains" || $spCriteriaModifier == "does not contain") {
                        $spCriteriaValue = "%$spCriteriaValue%";
                    } else if ($spCriteriaModifier == "is in the range") {
                        $spCriteriaValue = "$spCriteria > '$spCriteriaValue' AND $spCriteria <= '$criteria[extra]'";
                    }
                    
                    $spCriteriaModifier = self::$modifier2CriteriaMap[$spCriteriaModifier];
                    try{
                        if ($i > 0) {
                            $qry->addOr($spCriteria, $spCriteriaValue, $spCriteriaModifier);
                        } else {
                            $qry->add($spCriteria, $spCriteriaValue, $spCriteriaModifier);
                        }
                    }catch (Exception $e){
                        Logging::log($e);
                    }
                    $i++;
                }
            }
            $qry->addAscendingOrderByColumn('random()');
        }
        // construct limit restriction
        $limits = array();
        if (isset($storedCrit['limit'])) {
            if ($storedCrit['limit']['modifier'] == "items") {
                $limits['time'] = 1440 * 60;
                $limits['items'] = $storedCrit['limit']['value'];
            } else {
                $limits['time'] = $storedCrit['limit']['modifier'] == "hours" ? intval(floatval($storedCrit['limit']['value']) * 60 * 60) : intval($storedCrit['limit']['value'] * 60);
                $limits['items'] = null;
            }
        }
        try{
            $out = $qry->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)->find();
            return array("files"=>$out, "limit"=>$limits, "count"=>$out->count());
        }catch(Exception $e){
            Logging::log($e);
        }
    
    }
    
    public static function organizeSmartPlyalistCriteria($p_criteria)
    {
        $fieldNames = array('sp_criteria_field', 'sp_criteria_modifier', 'sp_criteria_value', 'sp_criteria_extra');
        $output = array();
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
            if (!preg_match("/^[a-zA-Z]$/", $lastChar)) {
                /* Strip field name of criteria index
                 * Ex: sp_criteria_field_0 -> sp_criteria_field
                 * We do this to check if the field name is a criteria
                 * or the block type
                 */
                $n = strrpos($fieldName, '_');
                $fieldName = substr($fieldName, 0, $n);
            }
            
            if (in_array($fieldName, $fieldNames)) {
                $rowNum = intval(substr($ele['name'], $index+1));
                $output['criteria'][$critIndex][$lastChar][$fieldName] = trim($ele['value']);
            }else{
                $output['etc'][$ele['name']] = $ele['value'];
            }
        }

        return $output;
    }
    // smart block functions end
}

class BlockNotFoundException extends Exception {}
class BlockNoPermissionException extends Exception {}
class BlockOutDatedException extends Exception {}
class BlockDyanmicException extends Exception {}

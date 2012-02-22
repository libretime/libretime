<?php

/**
 *
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Playlist {

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
        }
        else {
            $this->pl = new CcPlaylist();
            $this->pl->setDbUTime("now", new DateTimeZone("UTC"));
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
    public function getId() {
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

    public function getCreator() {

        return $this->pl->getCcSubjs()->getDbLogin();
    }

    public function setCreator($p_id) {

        $this->pl->setDbCreatorId($p_id);
        $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->pl->save($this->con);
    }

    public function getLastModified($format = null) {
        return $this->pl->getDbMtime($format);
    }

    public function getSize() {

        return $this->pl->countCcPlaylistcontentss();
    }

    /**
     * Get the entire playlist as a two dimentional array, sorted in order of play.
     * @return array
     */
    public function getContents() {

        Logging::log("Getting contents for playlist {$this->id}");

        $files = array();
        $rows = CcPlaylistcontentsQuery::create()
            ->joinWith('CcFiles')
            ->orderByDbPosition()
            ->filterByDbPlaylistId($this->id)
            ->find($this->con);

        $i = 0;
        $offset = 0;
        foreach ($rows as $row) {
          $files[$i] = $row->toArray(BasePeer::TYPE_FIELDNAME, true, true);


          $clipSec = Application_Model_Playlist::playlistTimeToSeconds($files[$i]['cliplength']);
          //$files[$i]['cliplength'] = Application_Model_Playlist::secondsToPlaylistTime($clipSec);
          $offset += $clipSec;
          $files[$i]['offset'] = Application_Model_Playlist::secondsToPlaylistTime($offset);

          $i++;
        }

        return $files;
    }

    /**
    * The database stores fades in 00:00:00 Time format with optional millisecond resolution .000000
    * but this isn't practical since fades shouldn't be very long usuall 1 second or less. This function
    * will normalize the fade so that it looks like 00.000000 to the user.
    **/
    public function normalizeFade($fade) {

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

    //aggregate column on playlistcontents cliplength column.
    public function getLength() {

        return $this->pl->getDbLength();
    }


    private function insertPlaylistElement($info)
    {
        $row = new CcPlaylistcontents();
        $row->setDbPlaylistId($this->id);
        $row->setDbFileId($info["id"]);
        $row->setDbPosition($info["pos"]);
        $row->setDbCliplength($info["cliplength"]);
        $row->setDbCuein($info["cuein"]);
        $row->setDbCueout($info["cueout"]);
        $row->setDbFadein($info["fadein"]);
        $row->setDbFadeout($info["fadeout"]);
        $row->save($this->con);
    }

    /*
     *
     */
    private function buildEntry($p_item, $pos)
    {
        $file = CcFilesQuery::create()->findPK($p_item, $this->con);

        if (isset($file) && $file->getDbFileExists()) {
            $entry = $this->plItem;
            $entry["id"] = $file->getDbId();
            $entry["pos"] = $pos;
            $entry["cliplength"] = $file->getDbLength();
            $entry["cueout"] = $file->getDbLength();

            return $entry;
        }
        else {
            throw new Exception("trying to add a file that does not exist.");
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
    public function addAudioClips($p_items, $p_afterItem=NULL, $addType = 'after')
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

                Logging::log("Adding to playlist");
                Logging::log("at position {$pos}");
            }
            else {

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

                Logging::log("Adding to playlist");
                Logging::log("at position {$pos}");
            }

            foreach($p_items as $ac) {
                Logging::log("Adding audio file {$ac}");

                $res = $this->insertPlaylistElement($this->buildEntry($ac, $pos));
                $pos = $pos + 1;
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
        }
        catch (Exception $e) {
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
            }
            else {
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
        }
        catch (Exception $e) {
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
     * 		array of unique item ids to remove from the playlist..
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
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }
    }


	public function getFadeInfo($pos) {

	    Logging::log("Getting fade info for pos {$pos}");

		$row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

            #Propel returns values in form 00.000000 format which is for only seconds.
            $fadeIn = $row->getDbFadein();
            $fadeOut = $row->getDbFadeout();
            return array($fadeIn, $fadeOut);
	}

    /**
     * Change fadeIn and fadeOut values for playlist Element
     *
     * @param int $pos
     * 		position of audioclip in playlist
     * @param string $fadeIn
     * 		new value in ss.ssssss or extent format
     * @param string $fadeOut
     * 		new value in ss.ssssss or extent format
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
            if (!is_null($fadeOut)){

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
        }
        catch (Exception $e) {
            $this->con->rollback();
            throw $e;
        }

        return array("fadeIn"=> $fadeIn, "fadeOut"=> $fadeOut);
    }

    public function setPlaylistfades($fadein, $fadeout) {

        if (isset($fadein)) {
            Logging::log("Setting playlist fade in {$fadein}");
            $row = CcPlaylistcontentsQuery::create()
                ->filterByDbPlaylistId($this->id)
                ->filterByDbPosition(0)
                ->findOne($this->con);

            $this->changeFadeInfo($row->getDbId(), $fadein, null);
        }

        if (isset($fadeout)) {
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
     * 		position of audioclip in playlist
     * @param string $cueIn
     * 		new value in ss.ssssss or extent format
     * @param string $cueOut
     * 		new value in ss.ssssss or extent format
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

            if (!is_null($cueIn) && !is_null($cueOut)){

                if ($cueOut === ""){
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
                if ($r->fetchColumn(0)){
                    $errArray["error"] = "Can't set cue out to be greater than file length.";
                    return $errArray;
                }

    			$sql = "SELECT INTERVAL '{$cueOut}' - INTERVAL '{$cueIn}'";
    			$r = $this->con->query($sql);
    			$cliplength = $r->fetchColumn(0);

                $row->setDbCuein($cueIn);
                $row->setDbCueout($cueOut);
                $row->setDBCliplength($cliplength);

            }
            else if (!is_null($cueIn)) {

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
            }
            else if (!is_null($cueOut)) {

                if ($cueOut === ""){
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
                if ($r->fetchColumn(0)){
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
            if ($r->fetchColumn(0)){
                $fadeIn = $cliplength;
                $row->setDbFadein($fadeIn);
            }

    		$sql = "SELECT INTERVAL '{$fadeOut}' > INTERVAL '{$cliplength}'";
    		$r = $this->con->query($sql);
            if ($r->fetchColumn(0)){
                $fadeOut = $cliplength;
                $row->setDbFadein($fadeOut);
            }

            $row->save($this->con);
            $this->pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
            $this->pl->save($this->con);

            $this->con->commit();
        }
        catch (Exception $e) {
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

        foreach($categories as $key => $val) {
            $method = 'get' . $val;
            $md[$key] = $this->$method();
        }

        return $md;
    }

    public function getPLMetaData($category)
    {
        $cat = $this->categories[$category];
        $method = 'get' . $cat;
        return $this->$method();
    }

    public function setPLMetaData($category, $value)
    {
        $cat = $this->categories[$category];

        $method = 'set' . $cat;
        $this->$method($value);
    }

    /**
     * This function is used for calculations! Don't modify for display purposes!
     *
     * Convert playlist time value to float seconds
     *
     * @param string $plt
     *         playlist interval value (HH:mm:ss.dddddd)
     * @return int
     *         seconds
     */
    public static function playlistTimeToSeconds($plt)
    {
        $arr =  preg_split('/:/', $plt);
        if (isset($arr[2])) {
          return (intval($arr[0])*60 + intval($arr[1]))*60 + floatval($arr[2]);
        }
        if (isset($arr[1])) {
            return intval($arr[0])*60 + floatval($arr[1]);
        }
        return floatval($arr[0]);
    }


    /**
     *  This function is used for calculations! Don't modify for display purposes!
     *
     * Convert float seconds value to playlist time format
     *
     * @param float $seconds
     * @return string
     *         interval in playlist time format (HH:mm:ss.d)
     */
    public static function secondsToPlaylistTime($p_seconds)
    {
        $info = explode('.', $p_seconds);
        $seconds = $info[0];
        if(!isset($info[1])){
            $milliStr = 0;
        }else{
            $milliStr = $info[1];
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $res = sprintf("%02d:%02d:%02d.%s", $hours, $minutes, $seconds, $milliStr);

       return $res;
    }

    public static function getPlaylistCount(){
        global $CC_CONFIG, $CC_DBC;
        $sql = 'SELECT count(*) as cnt FROM '.$CC_CONFIG["playListTable"];
        return $CC_DBC->GetOne($sql);
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
    public static function DeletePlaylists($p_ids)
    {
        CcPlaylistQuery::create()->findPKs($p_ids)->delete();
    }

} // class Playlist

class PlaylistNotFoundException extends Exception {}
class PlaylistOutDatedException extends Exception {}

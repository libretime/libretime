<?php

define('INDCH', ' ');

/**
 * Auxiliary class for GreenBox playlist editing methods.
 *
 * remark: dcterms:extent format: hh:mm:ss.ssssss
 *
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class Playlist {

 // *** Variable stored in the database ***

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * Can be 'ready', 'edited', 'incomplete'.
	 *
	 * @var string
	 */
	private $state;

	/**
	 * @var int
	 */
	private $currentlyaccessing;

	/**
	 * @var int
	 */
	private $editedby;

	/**
	 * @var timestamp
	 */
	private $mtime;

	/**
	 * @var MetaData
	 */
	public $md;

	//using propel's phpNames.
	private $categories = array("dc:title" => "DbName", "dc:creator" => "DbCreator", "dc:description" => "DbDescription", "dcterms:extent" => "length");


    public function __construct()
    {

    }

	 /**
-     * Convert playlist time value to float seconds
-     *
-     * @param string $plt
-     * 		playlist time value (HH:mm:ss.dddddd)
-     * @return int
-     * 		seconds
-     */
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
-     * Convert float seconds value to playlist time format
-     *
-     * @param float $seconds
-     * @return string
-     * 		time in playlist time format (HH:mm:ss.dddddd)
-     */
    public static function secondsToPlaylistTime($p_seconds)
    {
        $seconds = $p_seconds;
        $milliseconds = intval(($seconds - intval($seconds)) * 1000);
        $milliStr = str_pad($milliseconds, 6, '0');
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $res = sprintf("%02d:%02d:%02d.%s", $hours, $minutes, $seconds, $milliStr);

       return $res;
    }

    public static function Insert($p_values)
    {
         // Create the StoredPlaylist object
        $storedPlaylist = new Playlist();
        $storedPlaylist->name = isset($p_values['filename']) ? $p_values['filename'] : date("H:i:s");
    	$storedPlaylist->mtime = new DateTime("now");

    	$pl = new CcPlaylist();
    	$pl->setDbName($storedPlaylist->name);
    	$pl->setDbState("incomplete");
    	$pl->setDbMtime($storedPlaylist->mtime);
    	$pl->save();

    	$storedPlaylist->id = $pl->getDbId();
        $storedPlaylist->setState('ready');

	    return $storedPlaylist->id;

    }

    public static function Delete($id) {
		$pl = CcPlaylistQuery::create()->findPK($id);
		if($pl === NULL)
			return FALSE;

		$pl->delete();
		return TRUE;
    }


    /**
     * Delete the file from all playlists.
     * @param string $p_fileId
     */
    public static function DeleteFileFromAllPlaylists($p_fileId)
    {
    	CcPlaylistcontentsQuery::create()->filterByDbFileId($p_fileId)->delete();
    }


    public static function findPlaylistByName($p_name)
    {
 	    $res = CcPlaylistQuery::create()->findByDbName($p_name);
 	 	return $res;
 	}

 	/**
     * Fetch instance of Playlist object.<br>
     *
     * @param string $id
     * 		DB id of file
     * @return Playlist|FALSE
     *      Return FALSE if the object doesnt exist in the DB.
     */
    public static function Recall($id) {

        $pl = CcPlaylistQuery::create()->findPK($id);
    	if($pl === NULL)
    	    return FALSE;

        $storedPlaylist = new Playlist();
        $storedPlaylist->id = $id;
        $storedPlaylist->name = $pl->getDbName();
        $storedPlaylist->state = $pl->getDbState();
        $storedPlaylist->currentlyaccessing = $pl->getDbCurrentlyaccessing();
        $storedPlaylist->editedby = $pl->getDbEditedby();
        $storedPlaylist->mtime = $pl->getDbMtime();

        return $storedPlaylist;
    }

     /**
     * Rename stored virtual playlist
     *
     * @param string $p_newname
     * @return TRUE|PEAR_Error
     */
    public function setName($p_newname)
    {
        $pl = CcPlaylistQuery::create()->findPK($this->id);

    	if($pl === NULL)
    	    return FALSE;

    	$pl->setDbName($p_newname);
    	$pl->setDbMtime(new DateTime("now"));
    	$pl->save();

        $this->name = $p_newname;
        return TRUE;
    }

 	/**
     * Get mnemonic playlist name
     *
     * @param string $p_gunid
     * 		global unique id of playlist
     * @return string
     */
    public function getName($id=NULL)
    {
        if (is_null($id)) {
            return $this->name;
        }
        $pl = CcPlaylistQuery::create()->findPK($id);
        if($pl === NULL)
    	    return FALSE;

        return $pl->getDbName();
    }

	/**
     * Set state of virtual playlist
     *
     * @param string $p_state
     * 		'empty'|'incomplete'|'ready'|'edited'
     * @param int $p_editedby
     * 		 user id | 'NULL' for clear editedBy field
     * @return TRUE|PEAR_Error
     */
    public function setState($p_state, $p_editedby=NULL)
    {
        $pl = CcPlaylistQuery::create()->findPK($this->id);

    	if($pl === NULL)
    	    return FALSE;

    	$pl->setDbState($p_state);
    	$pl->setDbMtime(new DateTime("now"));

    	$eb = (!is_null($p_editedby) ? $p_editedby : NULL);
    	$pl->setDbEditedby($eb);

    	$pl->save();

        $this->state = $p_state;
        $this->editedby = $p_editedby;
        return TRUE;
    }

     /**
     * Get storage-internal file state
     *
     * @param string $p_gunid
     * 		global unique id of file
     * @return string
     * 		see install()
     */
    public function getState($id=NULL)
    {
        if (is_null($id)) {
            return $this->state;
        }

        $pl = CcPlaylistQuery::create()->findPK($id);
    	if($pl === NULL)
    	    return FALSE;

    	return $pl->getDbState();
    }

 	/**
     * Returns true if virtual file is currently in use.<br>
     * Static or dynamic call is possible.
     *
     * @param string $p_gunid
     * 		optional (for static call), global unique id
     * @return boolean|PEAR_Error
     */
    public function isAccessed($id=NULL)
    {
        if (is_null($id)) {
            return ($this->currentlyaccessing > 0);
        }

        $pl = CcPlaylistQuery::create()->findPK($id);
    	if (is_null($pl)) {
            return PEAR::raiseError(
                "StoredPlaylist::isAccessed: invalid id ($id)",
                GBERR_FOBJNEX
            );
    	}

    	return ($pl->getDbCurrentlyaccessing() > 0);
    }

    /**
     * Returns id of user editing playlist
     *
     * @param string $p_playlistId
     * 		playlist global unique ID
     * @return int id of user editing playlist
     */
    public function isEdited() {

        if($this->state === 'edited') {
            return $this->editedby;
        }
        return FALSE;
    }

/**
     * Set playlist edit flag
     *
     * @param string $p_playlistId
     * 		Playlist unique ID
     * @param boolean $p_val
     * 		Set/clear of edit flag
     * @param string $p_sessid
     * 		Session id
     * @param int $p_subjid
     * 		Subject id (if sessid is not specified)
     * @return boolean
     * 		TRUE on success.
     */

    public function setEditFlag($p_subjid, $p_val=TRUE) {

        if ($p_val) {
            $r = $this->setState('edited', $p_subjid);
        } else {
            $r = $this->setState('ready');
        }
        if ($r === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

     /**
     * Return local ID of virtual file.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    private function getNextPos() {

        $res = CcPlaylistQuery::create()
            ->findPK($this->id)
            ->computeLastPosition();

        if(is_null($res))
            return 0;

        return $res + 1;
    }

     public function getSize() {

        $res = CcPlaylistQuery::create()
            ->findPK($this->id)
            ->computeLastPosition();

        if(is_null($res))
            return 0;

        return $res;
    }

    /**
     * Get the entire playlist as a two dimentional array, sorted in order of play.
     * @return array
     */
    public function getContents() {
        $files = array();
        $rows = CcPlaylistcontentsQuery::create()
            ->joinWith('CcFiles')
            ->orderByDbPosition()
            ->filterByDbPlaylistId($this->id)
            ->find();

        foreach ($rows as $row) {
          $files[] = $row->toArray(BasePeer::TYPE_FIELDNAME, true, true);
        }

        return $files;
    }

    public function getLength() {
        $res = CcPlaylistQuery::create()
            ->findPK($this->id)
            ->computeLength();

        if(is_null($res))
            return '00:00:00.000000';

        return $res;
    }

    /**
     * Create instance of Playlist object and insert empty file
     *
     * @param string $fname
     * 		name of new file
     * @return instance of Playlist object
     */
    public function create($fname=NULL)
    {
        $values = array("filename" => $fname);
        $pl_id = Playlist::Insert($values);
        $this->id = $pl_id;
        return $this->id;
    }

    /**
     * Lock playlist for edit
     *
     * @param int $subjid
     * 		local subject (user) id
     * @param boolean $val
     * 		if false do unlock
     * @return boolean
     * 		previous state or error object
     */
    public function lock($subjid, $val=TRUE)
    {
        if ($val && $this->isEdited() !== FALSE) {
            return PEAR::raiseError(
                'Playlist::lock: playlist already locked'
            );
        }
        $r = $this->setEditFlag($subjid, $val);
        return $r;
    }


    /**
     * Unlock playlist
     *
     * @return boolean
     * 		previous state or error object
     */
    public function unlock($subjid)
    {
        $r = $this->lock($subjid, FALSE);
        return $r;
    }


    /**
     *  Add audio clip to the playlist
     *
     * @param string $p_id
     * 		local ID of added file
     * @param string $p_position
     *    optional, Which position in the playlist to insert the audio clip
     * @param string $p_fadeIn
     * 		optional, in time format hh:mm:ss.ssssss - total duration
     * @param string $p_fadeOut
     * 		optional, in time format hh:mm:ss.ssssss - total duration
     * @param string $p_clipLength
     * 		optional length in in time format hh:mm:ss.ssssss -
     *      for webstream (or for overrule length of audioclip)
     * @return true|PEAR_Error
     * 		TRUE on success
     */
    public function addAudioClip($p_mediaId, $p_position=NULL, $p_fadeIn=NULL, $p_fadeOut=NULL, $p_clipLength=NULL, $p_cuein=NULL, $p_cueout=NULL)
    {
        //get audio clip.
        $media = StoredFile::Recall($p_mediaId);
        if (is_null($media) || PEAR::isError($media)) {
        	return $media;
        }

        $metadata = $media->getMetadata();
        $length = $metadata["dcterms:extent"];

        if (!is_null($p_clipLength)) {
        	$length = $p_clipLength;
        }

        // insert at end of playlist.
        if (is_null($p_position))
          $p_position = $this->getNextPos();

	    // insert default values if parameter was empty
        $p_cuein = !is_null($p_cuein) ? $p_cuein : '00:00:00.000000';
        $p_cueout = !is_null($p_cueout) ? $p_cueout : $length;

		$con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME);
        $sql = "SELECT INTERVAL '{$p_cueout}' - INTERVAL '{$p_cuein}'";
		$r = $con->query($sql);
		$p_cliplength = $r->fetchColumn(0);

        $res = $this->insertPlaylistElement($this->id, $p_mediaId, $p_position, $p_cliplength, $p_cuein, $p_cueout, $p_fadeIn, $p_fadeOut);

        return TRUE;
    }


    /**
     * Remove audioClip from playlist
     *
     * @param int $position
     * 		position of audioclip in the playlist.
     * @return boolean
     */
    public function delAudioClip($pos)
    {
        if($pos < 0 || $pos >= $this->getNextPos())
            return FALSE;

        $row = CcPlaylistcontentsQuery::create()
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

        if(is_null($row))
            return FALSE;

        $row->delete();
        return $row;
    }

 	/**
     * Move audioClip to the new position in the playlist
     *
     * @param int $oldPos
     * 		old positioin in playlist
     * @param int $newPos
     * 		new position in playlist
     * @return mixed
     */
    public function moveAudioClip($oldPos, $newPos)
    {
        if($newPos < 0 || $newPos >= $this->getNextPos() || $oldPos < 0 || $oldPos >= $this->getNextPos())
            return FALSE;

        $row = $this->delAudioClip($oldPos);
        if($row === FALSE)
            return FALSE;

        $res = $this->addAudioClip($row->getDbFileId(), $newPos, $row->getDbFadein(), $row->getDbFadeout(), $row->getDbCliplength(), $row->getDbCuein(), $row->getDbCueout());
        if($res !== TRUE)
            return FALSE;

        return TRUE;
    }

	public function getFadeInfo($pos) {

		$row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

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
    public function changeFadeInfo($pos, $fadeIn, $fadeOut)
    {
        $errArray= array();
		$con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME);

        if(is_null($pos) || $pos < 0 || $pos >= $this->getNextPos()) {
            $errArray["error"]="Invalid position.";
            return $errArray;
        }

        $row = CcPlaylistcontentsQuery::create()
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

        $clipLength = $row->getDbCliplength();

        if(!is_null($fadeIn)) {

			$sql = "SELECT INTERVAL '{$fadeIn}' > INTERVAL '{$clipLength}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)) {
                //"Fade In can't be larger than overall playlength.";
                $fadeIn = $clipLength;
            }

            $row->setDbFadein($fadeIn);
        }
        if(!is_null($fadeOut)){

			$sql = "SELECT INTERVAL '{$fadeOut}' > INTERVAL '{$clipLength}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)) {
                //Fade Out can't be larger than overall playlength.";
                $fadeOut = $clipLength;
            }

            $row->setDbFadeout($fadeOut);
        }

        $row->save();

        return array("fadeIn"=>$fadeIn, "fadeOut"=>$fadeOut);
    }

	public function getCueInfo($pos) {

		$row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

        $file = $row->getCcFiles();
        $origLength = $file->getDbLength();
        $cueIn = $row->getDBCuein();
        $cueOut = $row->getDbCueout();

		return array($cueIn, $cueOut, $origLength);
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
    public function changeClipLength($pos, $cueIn, $cueOut)
    {
        $errArray= array();
		$con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME);

        if(is_null($cueIn) && is_null($cueOut)) {
            $errArray["error"]="Cue in and cue out are null.";
            return $errArray;
        }

        if(is_null($pos) || $pos < 0 || $pos >= $this->getNextPos()) {
            $errArray["error"]="Invalid position.";
            return $errArray;
        }

        $row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFilesPeer::OM_CLASS)
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();

        $oldCueIn = $row->getDBCuein();
        $oldCueOut = $row->getDbCueout();
        $fadeIn = $row->getDbFadein();
        $fadeOut = $row->getDbFadeout();

        $file = $row->getCcFiles();
        $origLength = $file->getDbLength();


        if(!is_null($cueIn) && !is_null($cueOut)){

            if($cueOut === ""){
                $cueOut = $origLength;
            }

			$sql = "SELECT INTERVAL '{$cueIn}' > INTERVAL '{$cueOut}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)) {
                $errArray["error"]= "Can't set cue in to be larger than cue out.";
                return $errArray;
            }

			$sql = "SELECT INTERVAL '{$cueOut}' > INTERVAL '{$origLength}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)){
                $errArray["error"] = "Can't set cue out to be greater than file length.";
                return $errArray;
            }

			$sql = "SELECT INTERVAL '{$cueOut}' - INTERVAL '{$cueIn}'";
			$r = $con->query($sql);
			$cliplength = $r->fetchColumn(0);

            $row->setDbCuein($cueIn);
            $row->setDbCueout($cueOut);
            $row->setDBCliplength($cliplength);

        }
        else if(!is_null($cueIn)) {

			$sql = "SELECT INTERVAL '{$cueIn}' > INTERVAL '{$oldCueOut}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)) {
                $errArray["error"] = "Can't set cue in to be larger than cue out.";
                return $errArray;
            }

            $sql = "SELECT INTERVAL '{$oldCueOut}' - INTERVAL '{$cueIn}'";
			$r = $con->query($sql);
			$cliplength = $r->fetchColumn(0);

            $row->setDbCuein($cueIn);
            $row->setDBCliplength($cliplength);
        }
        else if(!is_null($cueOut)) {

            if($cueOut === ""){
                $cueOut = $origLength;
            }

			$sql = "SELECT INTERVAL '{$cueOut}' < INTERVAL '{$oldCueIn}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)) {
                $errArray["error"] ="Can't set cue out to be smaller than cue in.";
                return $errArray;
            }

			$sql = "SELECT INTERVAL '{$cueOut}' > INTERVAL '{$origLength}'";
			$r = $con->query($sql);
            if($r->fetchColumn(0)){
                $errArray["error"] ="Can't set cue out to be greater than file length.";
                return $errArray;
            }

            $sql = "SELECT INTERVAL '{$cueOut}' - INTERVAL '{$oldCueIn}'";
			$r = $con->query($sql);
			$cliplength = $r->fetchColumn(0);

            $row->setDbCueout($cueOut);
            $row->setDBCliplength($cliplength);
        }

        $cliplength = $row->getDbCliplength();

		$sql = "SELECT INTERVAL '{$fadeIn}' > INTERVAL '{$cliplength}'";
		$r = $con->query($sql);
        if($r->fetchColumn(0)){
            $fadeIn = $cliplength;
            $row->setDbFadein($fadeIn);
        }

		$sql = "SELECT INTERVAL '{$fadeOut}' > INTERVAL '{$cliplength}'";
		$r = $con->query($sql);
        if($r->fetchColumn(0)){
            $fadeOut = $cliplength;
            $row->setDbFadein($fadeOut);
        }

        $row->save();

        return array("cliplength"=>$cliplength, "cueIn"=>$cueIn, "cueOut"=>$cueOut, "length"=>$this->getLength(),
                        "fadeIn"=>$fadeIn, "fadeOut"=>$fadeOut);
    }

    /**
     * Find info about clip at specified offset in playlist.
     *
     * @param string $offset
     * 		current playtime (hh:mm:ss.ssssss)
     * @param int $distance
     * 		0=current clip; 1=next clip ...
     * @return array of matching clip info:
     *   <ul>
     *      <li>gunid string, global unique id of clip</li>
     *      <li>elapsed string, already played time of clip</li>
     *      <li>remaining string, remaining time of clip</li>
     *      <li>duration string, total playlength of clip </li>
     *   </ul>
     */
    public function getPlaylistClipAtPosition($pos)
    {

    }

    public function getAllPLMetaData()
    {
        $categories = $this->categories;
        $row = CcPlaylistQuery::create()->findPK($this->id);
        $md = array();

        foreach($categories as $key => $val) {
            if($val === 'length') {
                $md[$key] = $this->getLength();
                continue;
            }

            $method = 'get' . $val;
            $md[$key] = $row->$method();
        }

        return $md;
    }

    public function getPLMetaData($category)
    {
        $cat = $this->categories[$category];

        if($cat === 'length') {
            return $this->getLength();
        }

        $row = CcPlaylistQuery::create()->findPK($this->id);
        $method = 'get' . $cat;
        return $row->$method();
    }

    public function setPLMetaData($category, $value)
    {
        $cat = $this->categories[$category];

        $row = CcPlaylistQuery::create()->findPK($this->id);
        $method = 'set' . $cat;
        $row->$method($value);
        $row->save();

        return TRUE;
    }

    /**
     * Return array with gunids of all sub-playlists and clips used in
     * the playlist
     *
     * @return array with hash elements:
     *              gunid - global id
     *              type  - playlist | audioClip
     */
    public function export()
    {

    }

    /**
     * Export playlist as simplified SMIL XML file.
     *
     * @param boolean $toString
     *		if false don't real export,
     *      return misc info about playlist only
     * @return string
     * 		XML string or hasharray with misc info
     */
    public function outputToSmil($toString=TRUE)
    {

    }


    /**
     * Export playlist as M3U file.
     *
     * @param boolean $toString
     * 		if false don't real export,
     *      return misc info about playlist only
     *  @return string|array
     * 		M3U string or hasharray with misc info
     */
    public function outputToM3u($toString=TRUE)
    {

    }


    /**
     * Export playlist as RSS XML file
     *
     * @param boolean $toString
     * 		if false don't really export,
     *      return misc info about playlist only
     * @return mixed
     * 		XML string or hasharray with misc info
     */
    public function outputToRss($toString=TRUE)
    {

    }


    /**
     * Insert a new playlist element.
     *
     * @param int $plId
     * 		id of Playlist
     * @param int $fileId
     * 		id of File
     * @param string $offset
     * 		relative offset in extent format
     * @param string $clipstart
     * 		audioClip clipstart in extent format
     * @param string $clipEnd
     * 		audioClip clipEnd in extent format
     * @param string $clipLength
     * 		 audioClip playlength in extent format (?)
     * @param string $acGunid
     * 		audioClip gunid
     * @param string $acLen
     * 		audioClip length in extent format
     * @param string $acTit
     * 		audioClip title
     * @param string $fadeIn
     * 		fadeIn value in ss.ssssss or extent format
     * @param string $fadeOut
     * 		fadeOut value in ss.ssssss or extent format

     * @return array with fields:
     *  <ul>
     *   <li>plElId int - record id of playlistElement</li>
     *   <li>plElGunid string - gl.unique id of playlistElement</li>
     *   <li>fadeInId int - record id</li>
     *   <li>fadeOutId int - record id</li>
     *  </ul>
     */
    private function insertPlaylistElement($plId, $fileId, $pos, $clipLength, $cuein, $cueout, $fadeIn=NULL, $fadeOut=NULL)
    {
        $defaultFade =  Application_Model_Preference::GetDefaultFade();

        if(is_null($fadeIn)) {
            if($defaultFade != "")
                $fadeIn = $defaultFade;
            else
                $fadeIn = '00:00:00.000';
        }
        if(is_null($fadeOut)) {
            if($defaultFade != "")
                $fadeOut = $defaultFade;
            else
                $fadeOut = '00:00:00.000';
        }

        $row = new CcPlaylistcontents();
        $row->setDbPlaylistId($plId);
        $row->setDbFileId($fileId);
        $row->setDbPosition($pos);
        $row->save();

        $row->setDbCliplength($clipLength);
        $row->setDbCuein($cuein);
        $row->setDbCueout($cueout);
        $row->setDbFadein($fadeIn);
        $row->setDbFadeout($fadeOut);


        return TRUE;
    }

} // class Playlist

/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @todo Rename this class PlaylistTag
 */
class PlaylistTagExport
{
    public static function OutputToSmil(&$pl, $plt, $ind='')
    {
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $ind4 = $ind3.INDCH;
        $res = "";
        foreach ($plt['children'] as $ple) {
            switch ($ple['elementname']) {
                case "playlistElement":
                    $r = PlaylistElementExport::OutputToSmil($pl, $ple, $ind4);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                    break;
                case "metadata":
                    $r = PlaylistMetadataExport::OutputToSmil($pl, $ple, $ind4);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                    break;
                default:
            }
        }
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<smil xmlns=\"http://www.w3.org/2001/SMIL20/Language\">\n".
            "$ind2<body>\n".
            "$ind3<par>\n".
            "$res".
            "$ind3</par>\n".
            "$ind2</body>\n".
            "$ind</smil>\n";
        return $res;
    }


    public static function OutputToM3u(&$pl, $plt, $ind='')
    {
        $res = "";
        foreach ($plt['children'] as $ple) {
            switch ($ple['elementname']) {
                case"playlistElement":
                    $r = PlaylistElementExport::OutputToM3u($pl, $ple);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
            }
        }
        $res = "#EXTM3U\n$res";
        return $res;
    }


    public static function OutputToRss(&$pl, $plt, $ind='')
    {
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $res = "";
        foreach ($plt['children'] as $ple) {
            switch ($ple['elementname']) {
                case "playlistElement":
                    $r = PlaylistElementExport::OutputToRss($pl, $ple, $ind3);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
                case "metadata":
                    $r = PlaylistMetadataExport::OutputToRss($pl, $ple, $ind3);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
                default:
            }
        }
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<rss version=\"2.0\">\n".
            "$ind2<channel>\n".
            "$res".
            "$ind2</channel>\n".
            "$ind</rss>\n";
        return $res;
    }
}


/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @todo Rename this class "PlaylistElement"
 */
class PlaylistElementExport {

    public static function OutputToSmil(&$pl, $ple, $ind='')
    {
        $acOrPl = NULL;
        $finfo = array('fi'=>0, 'fo'=>0);
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $anim = '';
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = PlaylistAudioClipExport::OutputToSmil($pl, $ac, $ind2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                    break;
                case "playlist":
                    $gunid = $ac['attrs']['id'];
                    $pl2 = StoredFile::RecallByGunid($gunid);
                    if (is_null($pl2) || PEAR::isError($pl2)) {
                    	return $pl2;
                    }
                    $r = $pl2->outputToSmil(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                    break;
                case "fadeInfo":
                    $r = PlaylistFadeInfoExport::OutputToSmil($pl, $ac, $ind2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$finfo = $r;
                    }
                    break;
                default:
                    return PEAR::raiseError(
                        "PlaylistElementExport::OutputToSmil:".
                        " unknown tag {$ac['elementname']}"
                    );
            }
        }
        $beginS = Playlist::playlistTimeToSeconds($ple['attrs']['relativeOffset']);
        $playlengthS = Playlist::playlistTimeToSeconds($acOrPl['playlength']);
        $fadeOutS = Playlist::playlistTimeToSeconds($finfo['fo']);
        $fiBeginS = 0;
        $fiEndS = Playlist::playlistTimeToSeconds($finfo['fi']);
        $foBeginS = ($playlengthS - $fadeOutS);
        $foEndS = Playlist::playlistTimeToSeconds($acOrPl['playlength']);
        foreach (array('fi','fo') as $ff) {
            if (${$ff."EndS"} - ${$ff."BeginS"} > 0) {
                $anim .= "{$ind2}<animate attributeName = \"soundLevel\"\n".
                    "{$ind3}from = \"".($ff == 'fi' ? 0 : 100)."%\"\n".
                    "{$ind3}to = \"".($ff == 'fi' ? 100 : 0)."%\"\n".
                    "{$ind3}calcMode = \"linear\"\n".
                    "{$ind3}begin = \"{${$ff."BeginS"}}s\"\n".
                    "{$ind3}end = \"{${$ff."EndS"}}s\"\n".
                    "{$ind3}fill = \"freeze\"\n".
                    "{$ind2}/>\n"
                ;
            }
        }
        $src = $acOrPl['src'];
        $str = "$ind<audio src=\"$src\" begin=\"{$beginS}s\"".
            ($anim ? ">\n$anim$ind</audio>" : " />").
            " <!-- {$acOrPl['type']}, {$acOrPl['gunid']}, {$acOrPl['playlength']}  -->".
            "\n";
        return $str;
    }


    public static function OutputToM3u(&$pl, $ple, $ind='')
    {
        $acOrPl = NULL;
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = PlaylistAudioClipExport::OutputToM3u($pl, $ac);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                	break;
                case "playlist":
                    $gunid = $ac['attrs']['id'];
                    $pl2 = StoredFile::RecallByGunid($gunid);
                    if (is_null($pl2) || PEAR::isError($pl2)) {
                    	return $pl2;
                    }
                    $r = $pl2->outputToM3u(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                	break;
            }
        }
        if (is_null($acOrPl)) {
        	return '';
        }
        $playlength = ceil(Playlist::playlistTimeToSeconds($acOrPl['playlength']));
        $title = $acOrPl['title'];
        $uri = (isset($acOrPl['uri']) ? $acOrPl['uri'] : '???' );
        $res  = "#EXTINF: $playlength, $title\n";
        $res .= "$uri\n";
        return $res;
    }


    public static function OutputToRss(&$pl, $ple, $ind='')
    {
        $acOrPl = NULL;
        $ind2 = $ind.INDCH;
        $anim = '';
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = PlaylistAudioClipExport::OutputToRss($pl, $ac, $ind2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                	break;
                case "playlist":
                    $gunid = $ac['attrs']['id'];
                    $pl2 = StoredFile::RecallByGunid($gunid);
                    if (is_null($pl2) || PEAR::isError($pl2)) {
                    	return $pl2;
                    }
                    $r = $pl2->outputToRss(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                	break;
                case "fadeInfo":
                	break;
                default:
                    return PEAR::raiseError(
                        "PlaylistElementExport::OutputToRss:".
                        " unknown tag {$ac['elementname']}"
                    );
            }
        }
        $title = (isset($acOrPl['title']) ? htmlspecialchars($acOrPl['title']) : '' );
        $desc = (isset($acOrPl['desc']) ? htmlspecialchars($acOrPl['desc']) : '' );
        $link = htmlspecialchars($acOrPl['src']);
        $desc = '';
        $str = "$ind<item>\n".
            "$ind2<title>$title</title>\n".
            "$ind2<description>$desc</description>\n".
            "$ind2<link>$link</link>\n".
            "$ind</item>\n";
        return $str;
    }
}


/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @todo Rename this class to PlaylistAudioClip (notice the caps)
 */
class PlaylistAudioClipExport
{

    public static function OutputToSmil(&$pl, $plac, $ind='')
    {
        $gunid = $plac['attrs']['id'];
        $ac = StoredFile::RecallByGunid($gunid);
        if (is_null($ac) || PEAR::isError($ac)) {
        	return $ac;
        }
        $RADext = $ac->getFileExtension();
        if (PEAR::isError($RADext)) {
        	return $RADext;
        }
        return array(
            'type'       => 'audioclip',
            'gunid'      => $gunid,
            'src'        => AC_URL_RELPATH."$gunid.$RADext",
            'playlength' => $plac['attrs']['playlength'],
        );
    }


    public static function OutputToM3u(&$pl, $plac, $ind='')
    {
        $gunid = $plac['attrs']['id'];
        $ac = StoredFile::RecallByGunid($gunid);
        if (is_null($ac) || PEAR::isError($ac)) {
        	return $ac;
        }
        $RADext = $ac->getFileExtension();
        if (PEAR::isError($RADext)) {
        	return $RADext;
        }
        return array(
            'playlength' => $plac['attrs']['playlength'],
            'title'      => $plac['attrs']['title'],
            'uri'        => AC_URL_RELPATH."$gunid.$RADext",
        );
    }


    public static function OutputToRss(&$pl, $plac, $ind='')
    {
        $id = $plac['attrs']['id'];
        $playlist = Playlist::Recall($id);
        if (is_null($playlist) || PEAR::isError($playlist)) {
        	return $playlist;
        }
        $RADext = $playlist->getFileExtension();
        if (PEAR::isError($RADext)) {
        	return $RADext;
        }
        $title = $playlist->getName();
        $desc = $playlist->getPLMetaData("dc:description");
        return array(
            'type'       => 'audioclip',
            'gunid'      => $id,
            'src'        => "http://XXX/YY/$id.$RADext",
            'playlength' => $plac['attrs']['playlength'],
            'title'      => $title,
            'desc'      => $desc,
        );
    }
}


/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @todo Rename this class "PlaylistFadeInfo" (notive the caps)
 */
class PlaylistFadeInfoExport
{

    public static function OutputToSmil(&$pl, $plfi, $ind='')
    {
        $r = array(
            'fi'=>$plfi['attrs']['fadeIn'],
            'fo'=>$plfi['attrs']['fadeOut'],
        );
        return $r;
    }


    public static function OutputToM3u(&$pl, $plfa, $ind='')
    {
    	return '';
    }


    public static function OutputToRss(&$pl, $plfa, $ind='')
    {
    	return '';
    }

}


/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @todo Rename this class to PlaylistMetadata (notice the caps)
 */
class PlaylistMetadataExport
{
    public static function OutputToSmil(&$pl, $md, $ind='')
    {
    	return NULL;
    }


    public static function OutputToM3u(&$pl, $md, $ind='')
    {
    	return NULL;
    }


    public static function OutputToRss(&$pl, $md, $ind='')
    {
    	return NULL;
    }
}


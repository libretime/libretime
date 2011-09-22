<?php
define('INDCH', ' ');

/**
 * remark: dcterms:extent format: hh:mm:ss.ssssss
 *
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Playlist {

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
-     * 		time in playlist time format (HH:mm:ss.d)
-     */
    public static function secondsToPlaylistTime($p_seconds)
    {
        $seconds = $p_seconds;
        $rounded = round($seconds, 1);
        $info = explode('.', $rounded);
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


    public static function Delete($id)
    {
		$pl = CcPlaylistQuery::create()->findPK($id);
		if($pl === NULL)
			return FALSE;

		$pl->delete();
		return TRUE;
    }

    public static function deleteAll()
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = 'DELETE FROM '.$CC_CONFIG["playListTable"];
		$CC_DBC->query($sql);
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

        $storedPlaylist = new Application_Model_Playlist();
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
    	$pl->setDbMtime(new DateTime("now"), new DateTimeZone("UTC"));
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
        if ($pl === NULL)
    	    return FALSE;

        return $pl->getDbName();
    }

    public function setDescription($p_description)
    {
        $pl = CcPlaylistQuery::create()->findPK($this->id);

    	if($pl === NULL)
    	    return FALSE;

    	$pl->setDbDescription($p_description);
    	$pl->setDbMtime(new DateTime("now"), new DateTimeZone("UTC"));
    	$pl->save();

        //$this->name = $p_newname;
        return TRUE;
    }

    public function getDescription()
    {
        $pl = CcPlaylistQuery::create()->findPK($this->id);
        if ($pl === NULL)
    	    return FALSE;

        return $pl->getDbDescription();
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
    	$pl->setDbMtime(new DateTime("now"), new DateTimeZone("UTC"));

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

        $i = 0;
        $offset = 0;
        foreach ($rows as $row) {
          $files[$i] = $row->toArray(BasePeer::TYPE_FIELDNAME, true, true);
          // display only upto 1 decimal place by calling secondsToPlaylistTime
          $clipSec = Application_Model_Playlist::playlistTimeToSeconds($files[$i]['cliplength']);
          $files[$i]['cliplength'] = Application_Model_Playlist::secondsToPlaylistTime($clipSec);
          $offset += $clipSec;
          $files[$i]['offset'] = Application_Model_Playlist::secondsToPlaylistTime($offset);
          $i++;
        }

        return $files;
    }

    public function getLength() {
        $res = CcPlaylistQuery::create()
            ->findPK($this->id)
            ->computeLength();

        if(is_null($res))
            return '00:00:00';
        
        // calling two functions to format time to 1 decimal place
        $sec = Application_Model_Playlist::playlistTimeToSeconds($res);
        $res = Application_Model_Playlist::secondsToPlaylistTime($sec); 
        return $res;
    }

    /**
     * Create instance of a Playlist object.
     *
     * @param string $p_fname
     * 		Name of the playlist
     * @return Playlist
     */
    public function create($p_fname=NULL)
    {
        $this->name = !empty($p_fname) ? $p_fname : date("H:i:s");

    	$pl = new CcPlaylist();
    	$pl->setDbName($this->name);
    	$pl->setDbState("incomplete");
    	$pl->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
    	$pl->save();

    	$this->id = $pl->getDbId();
        $this->setState('ready');

        return $this;
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
                'Application_Model_Playlist::lock: playlist already locked'
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
        $media = Application_Model_StoredFile::Recall($p_mediaId);
        if (is_null($media) || PEAR::isError($media)) {
        	return $media;
        }

        $metadata = $media->getMetadata();
        $length = $metadata['MDATA_KEY_DURATION'];

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

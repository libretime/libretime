<?php

define('INDCH', ' ');

/**
 * Auxiliary class for GreenBox playlist editing methods.
 *
 * remark: dcterms:extent format: hh:mm:ss.ssssss
 *
 * @package Campcaster
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


    public function __construct($p_gunid=NULL)
    {

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
        $storedPlaylist->id = $pl->getDbId();
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
     * TODO have to change this.
     * */

    /*
     public function isScheduled() {
        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT * "
            ." FROM ".$CC_CONFIG['scheduleTable']
            ." WHERE ends > now() and playlist=x'{$this->gunid}'::bigint";
        $scheduled = $CC_DBC->getAll($sql);

        return $scheduled;
    }
    */

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

    public function setEditFlag($p_val=TRUE, $p_sessid=NULL, $p_subjid=NULL) {

        if (!is_null($p_sessid)) {
            $p_subjid = Alib::GetSessUserId($p_sessid);
            if (PEAR::isError($p_subjid)) {
                return $p_subjid;
            }
        }

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

    /**
     * Get the entire playlist as a two dimentional array, sorted in order of play.
     * @return array
     */
    public function getContents() {
        
        $files;
        
        $rows = CcPlaylistcontentsQuery::create()
            ->joinWith('CcFiles')
            ->orderByDbPosition()
            ->filterByDbPlaylistId($this->id)
            ->find();
    	
        foreach ($rows as $row) {
                $files[] = $row->toArray(BasePeer::TYPE_PHPNAME, true, true);
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
        if (PEAR::isError($pl_id)) {
            return $pl_id;
        }
        $this->id = $pl_id;
        return $this->id;
    }

    /**
     * Lock playlist for edit
     *
     * @param string $sessid
     * 		session id
     * @param int $subjid
     * 		local subject (user) id
     * @param boolean $val
     * 		if false do unlock
     * @return boolean
     * 		previous state or error object
     */
    public function lock($sessid, $subjid=NULL, $val=TRUE)
    {
        if ($val && $this->isEdited() !== FALSE) {
            return PEAR::raiseError(
                'Playlist::lock: playlist already locked'
            );
        }
        $r = $this->setEditFlag($val, $sessid, $subjid);
        return $r;
    }


    /**
     * Unlock playlist
     *
     * @param sessId
     * 		reference to GreenBox object
     * @return boolean
     * 		previous state or error object
     */
    public function unlock($sessid)
    {
        $r = $this->lock($sessid, NULL, FALSE);
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
    public function addAudioClip($ac_id, $p_position=NULL, $p_fadeIn=NULL, $p_fadeOut=NULL, $p_clipLength=NULL, $p_cuein=NULL, $p_cueout=NULL)
    {
        
        $_SESSION['debug'] = "in add";
        
        //get audio clip.
        $ac = StoredFile::Recall($ac_id);
        if (is_null($ac) || PEAR::isError($ac)) {
        	return $ac;
        }
        // get information about audioClip
        $acInfo = $this->getAudioClipInfo($ac);
        if (PEAR::isError($acInfo)) {
        	return $acInfo;
        }
        extract($acInfo);   // 'acGunid', 'acLen', 'acTit', 'elType'
        if (!is_null($p_clipLength)) {
        	$acLen = $p_clipLength;
        }

        // insert at end of playlist.
        if (is_null($p_position))
          $p_position = $this->getNextPos();
        if (PEAR::isError($p_position)) {
        	return $p_position;
        }

	    // insert default values if parameter was empty
        $p_cuein = !is_null($p_cuein) ? $p_cuein : '00:00:00.000000';
        $p_cueout = !is_null($p_cueout) ? $p_cueout : $acLen;

        $acLengthS = $clipLengthS = self::playlistTimeToSeconds($acLen);
        if (!is_null($p_cuein)) {
            $clipLengthS = $acLengthS - self::playlistTimeToSeconds($p_cuein);
        }
        if (!is_null($p_cueout)) {
            $clipLengthS = $clipLengthS - ($acLengthS - self::playlistTimeToSeconds($p_cueout));
        }
        $p_clipLength = self::secondsToPlaylistTime($clipLengthS);
        
        $res = $this->insertPlaylistElement($this->id, $ac_id, $p_position, $p_clipLength, $p_cuein, $p_cueout, $p_fadeIn, $p_fadeOut);
        if (PEAR::isError($res)) {
        	return $res;
        }
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
        
        if(is_null($pos) || $pos < 0 || $pos >= $this->getNextPos()) {
            $errArray["error"]="Invalid position.";
            return $errArray;
        }
        
        $row = CcPlaylistcontentsQuery::create()
            ->filterByDbPlaylistId($this->id)
            ->filterByDbPosition($pos)
            ->findOne();
            
        $clipLength = $row->getDbCliplength();
        
        if(!is_null($fadeIn) && !is_null($fadeOut)) {
            
            if(Playlist::playlistTimeToSeconds($fadeIn) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade In can't be larger than overall playlength.";
                return $errArray;
            }
            if(Playlist::playlistTimeToSeconds($fadeOut) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade Out can't be larger than overall playlength.";
                return $errArray;
            }
        
            $row->setDbFadein($fadeIn);
            $row->setDbFadeout($fadeOut);
        }
        else if(!is_null($fadeIn)) {
            
            if(Playlist::playlistTimeToSeconds($fadeIn) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade In can't be larger than overall playlength.";
                return $errArray;
            }
           
            $row->setDbFadein($fadeIn);
        }
        else if(!is_null($fadeOut)){
            
            if(Playlist::playlistTimeToSeconds($fadeOut) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade Out can't be larger than overall playlength.";
                return $errArray;
            }
        
            $row->setDbFadeout($fadeOut);
        }
        
        $row->save();

        return array("fadeIn"=>$fadeIn, "fadeOut"=>$fadeOut);
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
       
        if(is_null($cueIn) && is_null($cueOut)) {
            $errArray["error"]="Cue in and cue out are null.";
            return $errArray;
        }
        
        if(is_null($pos) || $pos < 0 || $pos >= $this->getNextPos()) {
            $errArray["error"]="Invalid position.";
            return $errArray;
        }
       
        $row = CcPlaylistcontentsQuery::create()
            ->joinWith(CcFiles)
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
            if(Playlist::playlistTimeToSeconds($cueIn) > Playlist::playlistTimeToSeconds($cueOut)) {
                $errArray["error"]= "Can't set cue in to be larger than cue out.";
                return $errArray;
            }
            if(Playlist::playlistTimeToSeconds($cueOut) > Playlist::playlistTimeToSeconds($origLength)){
                $errArray["error"] = "Can't set cue out to be greater than file length.";
                return $errArray;
            }
            
            $row->setDbCuein($cueIn);
            $row->setDbCueout($cueOut);
            $row->setDBCliplength(Playlist::secondsToPlaylistTime(Playlist::playlistTimeToSeconds($cueOut) 
                    - Playlist::playlistTimeToSeconds($cueIn)));
                       
        }
        else if(!is_null($cueIn)) {
            
            if(Playlist::playlistTimeToSeconds($cueIn) > Playlist::playlistTimeToSeconds($oldCueOut)) {
                $errArray["error"] = "Can't set cue in to be larger than cue out.";
                return $errArray;
            }
            
            $row->setDbCuein($cueIn);
            $row->setDBCliplength(Playlist::secondsToPlaylistTime(Playlist::playlistTimeToSeconds($oldCueOut) 
                    - Playlist::playlistTimeToSeconds($cueIn)));
        }
        else if(!is_null($cueOut)) {
            
            if($cueOut === ""){
                $cueOut = $origLength;
            }
            
            if(Playlist::playlistTimeToSeconds($cueOut) < Playlist::playlistTimeToSeconds($oldCueIn)) {
                $errArray["error"] ="Can't set cue out to be smaller than cue in.";
                return $errArray;
            }
            
            if(Playlist::playlistTimeToSeconds($cueOut) > Playlist::playlistTimeToSeconds($origLength)){
                $errArray["error"] ="Can't set cue out to be greater than file length.";
                return $errArray;
            }
            
            $row->setDbCueout($cueOut);
            $row->setDBCliplength(Playlist::secondsToPlaylistTime(Playlist::playlistTimeToSeconds($cueOut) 
                    - Playlist::playlistTimeToSeconds($oldCueIn)));
        }

        $cliplength = $row->getDbCliplength();
        
        if(Playlist::playlistTimeToSeconds($fadeIn) > Playlist::playlistTimeToSeconds($cliplength)){
            $fadeIn = $cliplength;
            
            $row->setDbFadein($fadeIn);
        }
        if(Playlist::playlistTimeToSeconds($fadeOut) > Playlist::playlistTimeToSeconds($cliplength)){
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
     * Convert playlist time value to float seconds
     *
     * @param string $plt
     * 		playlist time value (HH:mm:ss.dddddd)
     * @return int
     * 		seconds
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
     * Convert float seconds value to playlist time format
     *
     * @param float $seconds
     * @return string
     * 		time in playlist time format (HH:mm:ss.dddddd)
     */
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
     * Get audioClip length and title
     *
     * @param int $acId
     * 		local id of audioClip inserted to playlist
     * @return array with fields:
     *  <ul>
     *   <li>acGunid, string - audioClip gunid</li>
     *   <li>acLen string - length of clip in dcterms:extent format</li>
     *   <li>acTit string - clip title</li>
     *   <li>elType string - audioClip | playlist</li>
     *  </ul>
     */
    private function getAudioClipInfo($ac)
    {
        $ac_id = BasicStor::IdFromGunid($ac->gunid);

        $r = $ac->md['dcterms:extent'];
        if (isset($r)) {
        	$acLen = $r;
        } else {
        	$acLen = '00:00:00.000000';
        }

        $r = $ac->md['dc:title'];
        if (isset($r)) {
        	$acTit = $r;
        } else {
        	$acTit = $acGunid;
        }
        $elType = BasicStor::GetObjType($ac_id);
        $trTbl = array('audioclip'=>'audioClip', 'webstream'=>'audioClip','playlist'=>'playlist');
        $elType = $trTbl[$elType];

        return compact('acGunid', 'acLen', 'acTit', 'elType');
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
        if(is_null($fadeIn))
            $fadeIn = '00:00:00.000';
        if(is_null($fadeOut))
            $fadeOut = '00:00:00.000';
        
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

    /**
     * Set playlist length - dcterm:extent
     *
     * @param string $newPlLen
     * 		new length in extent format
     * @param int $parid
     * 		playlist container record id
     * @param int $metaParid
     * 		metadata container record id
     * @return boolean
     */
    private function setPlaylistLength($newPlLen, $parid, $metaParid)
    {
        $mid = $this->_getMidOrInsert('playlength', $parid, $newPlLen, 'A');
        if (PEAR::isError($mid)) {
        	return $mid;
        }
        $r = $this->_setValueOrInsert(
            $mid, $newPlLen, $parid,  'playlength', 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $mid = $this->_getMidOrInsert('dcterms:extent', $metaParid, $newPlLen);
        if (PEAR::isError($mid)) {
        	return $mid;
        }
        $r = $this->_setValueOrInsert(
            $mid, $newPlLen, $metaParid,  'dcterms:extent');
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }

} // class Playlist


/**
 * Auxiliary class for GB playlist editing methods
 *
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class PlaylistElement {
    private $pl = NULL;
    private $plEl = NULL;

    public function PlaylistElement($pl, $plEl)
    {
        $this->pl = $pl;
        $this->plEl = $plEl;
    }


    public function analyze()
    {
        $plInfo = array(
            'acLen' => '00:00:00.000000',
            'acLenS' => 0,
            'fadeIn' => '00:00:00.000000',
            'fadeInS' => 0,
            'fadeOut' => '00:00:00.000000',
            'fadeOutS' => 0,
            'clipStart' => '00:00:00.000000',
            'clipStartS' => 0,
            'clipEnd' => '00:00:00.000000',
            'clipEndS' => 0
        );
        $plInfo['elOffset'] = $this->plEl['attrs']['relativeOffset'];
        $plInfo['elOffsetS'] = Playlist::playlistTimeToSeconds($plInfo['elOffset']);
        // cycle over tags inside playlistElement
        foreach ($this->plEl['children'] as $j => $acFi) {
            switch ($acFi['elementname']) {
	            case "playlist":
	                $plInfo['type'] = 'playlist';
	                break;
	            case "audioClip":
	                $plInfo['type'] = 'audioClip';
	                break;
	        }
	        switch ($acFi['elementname']) {
	            case "playlist":
	            case "audioClip":
	                $plInfo['acLen'] = $acFi['attrs']['playlength'];
	                $plInfo['acLenS'] = Playlist::playlistTimeToSeconds($plInfo['acLen']);
	                $plInfo['acGunid'] = $acFi['attrs']['id'];
	                break;
	            case "fadeInfo":
	                $plInfo['fadeIn'] = $acFi['attrs']['fadeIn'];
	                $plInfo['fadeInS'] = Playlist::playlistTimeToSeconds($plInfo['fadeIn']);
	                $plInfo['fadeOut'] = $acFi['attrs']['fadeOut'];
	                $plInfo['fadeOutS'] = Playlist::playlistTimeToSeconds($plInfo['fadeOut']);
	                break;
            }
            $plInfo['clipStart'] = $this->plEl['attrs']['clipStart'];
            $plInfo['clipStartS'] = Playlist::playlistTimeToSeconds($this->plEl['attrs']['clipStart']);
            $plInfo['clipEnd'] = $this->plEl['attrs']['clipEnd'];
            $plInfo['clipEndS'] = Playlist::playlistTimeToSeconds($this->plEl['attrs']['clipEnd']);
        }
        return $plInfo;
    }
} // class PlaylistElement


/**
 * @package Campcaster
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
 * @package Campcaster
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
 * @package Campcaster
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
        $gunid = $plac['attrs']['id'];
        $ac = StoredFile::RecallByGunid($gunid);
        if (is_null($ac) || PEAR::isError($ac)) {
        	return $ac;
        }
        $RADext = $ac->getFileExtension();
        if (PEAR::isError($RADext)) {
        	return $RADext;
        }
        $title = $pl->gb->bsGetMetadataValue($ac->getId(), 'dc:title');
        $desc = $pl->gb->bsGetMetadataValue($ac->getId(), 'dc:description');
        return array(
            'type'       => 'audioclip',
            'gunid'      => $gunid,
            'src'        => "http://XXX/YY/$gunid.$RADext",
            'playlength' => $plac['attrs']['playlength'],
            'title'      => $title,
            'desc'      => $desc,
        );
    }
}


/**
 * @package Campcaster
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
 * @package Campcaster
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

?>

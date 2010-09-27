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

	private $categories = array("dc:title" => "name", "dc:creator" => "creator", "dc:description" => "description", "dcterms:extent" => "length");


    public function __construct($p_gunid=NULL)
    {

    }

    public static function Insert($p_values)
    {
        global $CC_CONFIG, $CC_DBC;

        // Create the StoredPlaylist object
        $storedPlaylist = new Playlist();
        $storedPlaylist->name = isset($p_values['filename']) ? $p_values['filename'] : date("H:i:s");

        // NOTE: POSTGRES-SPECIFIC KEYWORD "DEFAULT" BEING USED, WOULD BE "NULL" IN MYSQL
      	$storedPlaylist->id = isset($p_values['id']) && is_integer($p_values['id'])?"'".$p_values['id']."'":'DEFAULT';

        // Insert record into the database
        $escapedName = pg_escape_string($storedPlaylist->name);

        $CC_DBC->query("BEGIN");
        $sql = "INSERT INTO ".$CC_CONFIG['playListTable']
                ."(id, name, state, mtime)"
                ." VALUES ({$storedPlaylist->id}, '{$escapedName}', "
                ." 'incomplete', now())";

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        if (!is_integer($storedPlaylist->id)) {
        	// NOTE: POSTGRES-SPECIFIC
			    $sql = "SELECT currval('".$CC_CONFIG["playListSequence"]."_seq')";
        	$storedPlaylist->id = $CC_DBC->getOne($sql);
        }

        // Save state
        $res = $storedPlaylist->setState('ready');

        // Commit changes
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        return $storedPlaylist->id;
    }

    public static function Delete($id) {
        global $CC_CONFIG, $CC_DBC;

        $CC_DBC->query("BEGIN");
        $sql = "DELETE FROM ".$CC_CONFIG['playListTable']. " WHERE id='{$id}'";

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        // Commit changes
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        return TRUE;
    }

 	/**
     * Fetch instance of Playlist object.<br>
     *
     * @param string $id
     * 		DB id of file
     * @return Playlist|NULL
     *      Return NULL if the object doesnt exist in the DB.
     */
    public static function Recall($id) {

        global $CC_DBC, $CC_CONFIG;

        $escapedID = pg_escape_string($id);

        $sql = "SELECT id,"
            ." name, state, currentlyaccessing, editedby, "
            ." mtime"
            ." FROM ".$CC_CONFIG['playListTable']
            ." WHERE id ='{$escapedID}'";
        $row = $CC_DBC->getRow($sql);

        if (PEAR::isError($row)) {
            return FALSE;
        }
        if (is_null($row)) {
            return FALSE;
        }

        $storedPlaylist = new Playlist($id);

        $storedPlaylist->id = $row['id'];
        $storedPlaylist->name = $row['name'];
        $storedPlaylist->state = $row['state'];
        $storedPlaylist->currentlyaccessing = $row['currentlyaccessing'];
        $storedPlaylist->editedby = $row['editedby'];
        $storedPlaylist->mtime = $row['mtime'];

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
        global $CC_CONFIG, $CC_DBC;
        $escapedName = pg_escape_string($p_newname);
        $sql = "UPDATE ".$CC_CONFIG['playListTable']
            ." SET name='$escapedName', mtime=now()"
            ." WHERE id='{$this->id}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
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
        global $CC_CONFIG, $CC_DBC;
        if (is_null($id)) {
            return $this->name;
        }
        $sql = "SELECT name FROM ".$CC_CONFIG['playListTable']
            ." WHERE id='$id'";
        return $CC_DBC->getOne($sql);
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
        global $CC_CONFIG, $CC_DBC;
        $escapedState = pg_escape_string($p_state);
        $eb = (!is_null($p_editedby) ? ", editedBy=$p_editedby" : '');
        $sql = "UPDATE ".$CC_CONFIG['playListTable']
            ." SET state='$escapedState'$eb, mtime=now()"
            ." WHERE id='{$this->id}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
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
        global $CC_CONFIG, $CC_DBC;
        if (is_null($id)) {
            return $this->state;
        }
        $sql = "SELECT state FROM ".$CC_CONFIG['playListTable']
            ." WHERE id='$id'";
        return $CC_DBC->getOne($sql);
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
        global $CC_CONFIG, $CC_DBC;
        if (is_null($id)) {
            return ($this->currentlyaccessing > 0);
        }
        $sql = "SELECT currentlyAccessing FROM ".$CC_CONFIG['playListTable']
            ." WHERE id='$id'";
        $ca = $CC_DBC->getOne($sql);
        if (is_null($ca)) {
            return PEAR::raiseError(
                "StoredPlaylist::isAccessed: invalid id ($id)",
                GBERR_FOBJNEX
            );
        }
        return ($ca > 0);
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

        $state = $this->state;
        if ($p_val) {
            $r = $this->setState('edited', $p_subjid);
        } else {
            $r = $this->setState('ready', 'NULL');
        }
        if (PEAR::isError($r)) {
            return $r;
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

        global $CC_CONFIG, $CC_DBC;

        $sql = "SELECT MAX(position) AS nextPos
        	FROM cc_playlistcontents
        	WHERE playlist_id='{$this->getId()}'";

        $res = $CC_DBC->getOne($sql);

        if(is_null($res))
            return 0;

        if(PEAR::isError($res)){
            return $res;
        }

        return $res + 1;
    }

    /**
     * Get the entire playlist as a two dimentional array, sorted in order of play.
     * @return array
     */
    public function getContents() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT *
        	FROM cc_playlistcontents C JOIN cc_files F ON C.file_id = F.id
        	WHERE C.playlist_id='{$this->getId()}' ORDER BY C.position";
        return $CC_DBC->getAll($sql);
    }

    public function getLength() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT SUM(cliplength) AS length FROM ".$CC_CONFIG['playListContentsTable']
                ." WHERE playlist_id='{$this->getId()}' group by playlist_id";
        $res =  $CC_DBC->getRow($sql);
        if (PEAR::isError($res)) {
        	return $res;
        }

        if(is_null($res))
            return '00:00:00.000000';

        return $res['length'];
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
    public function addAudioClip($p_id, $p_position=NULL, $p_fadeIn=NULL, $p_fadeOut=NULL, $p_clipLength=NULL, $p_cuein=NULL, $p_cueout=NULL)
    {
        //get audio clip.
        $ac = StoredFile::Recall($p_id);
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

        $res = $this->insertPlaylistElement($this->getId(), $p_id, $p_position, $p_clipLength, $p_cuein, $p_cueout, $p_fadeIn, $p_fadeOut);
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
        global $CC_CONFIG, $CC_DBC;
        
        if($pos < 0 || $pos >= $this->getNextPos())
            return FALSE;
            
        $pos = pg_escape_string($pos);

        $CC_DBC->query("BEGIN");
        $sql = "DELETE FROM ".$CC_CONFIG['playListContentsTable']." WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        // Commit changes
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        return TRUE;
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
        global $CC_CONFIG, $CC_DBC;
        
        if($newPos < 0 || $newPos >= $this->getNextPos() || $oldPos < 0 || $oldPos >= $this->getNextPos())
            return FALSE;
            
        $oldPos = pg_escape_string($oldPos);
        $newPos = pg_escape_string($newPos);

        $sql = "SELECT * FROM ".$CC_CONFIG['playListContentsTable']. " WHERE playlist_id='{$this->getId()}' AND position='{$oldPos}'";

        $ac = $CC_DBC->getRow($sql);
        if (PEAR::isError($ac)) {
            return $ac;
        }

        $res = $this->delAudioClip($oldPos);
        if($res !== TRUE)
            return FALSE;

        $res = $this->addAudioClip($ac['file_id'], $newPos, $ac['fadein'], $ac['fadeOut'], $ac['cliplength'], $ac['cuein'], $ac['cueout']);
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
        global $CC_CONFIG, $CC_DBC;
        $errArray= array();
        
        if(is_null($pos) || $pos < 0 || $pos >= $this->getNextPos()) {
            $errArray["error"]="Invalid position.";
            return $errArray;
        }
        
        $sql =  $sql = "SELECT cliplength
        	FROM cc_playlistcontents WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
        $clipLength = $CC_DBC->getOne($sql);
        
        if(!is_null($fadeIn) && !is_null($fadeOut)) {
            
            if(Playlist::playlistTimeToSeconds($fadeIn) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade In can't be larger than overall playlength.";
                return $errArray;
            }
            if(Playlist::playlistTimeToSeconds($fadeOut) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade Out can't be larger than overall playlength.";
                return $errArray;
            }
        
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. " SET fadein='{$fadeIn}', fadeout='{$fadeOut}' " .
        	"WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
        }
        else if(!is_null($fadeIn)) {
            
            if(Playlist::playlistTimeToSeconds($fadeIn) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade In can't be larger than overall playlength.";
                return $errArray;
            }
            
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. " SET fadein='{$fadeIn}' " .
        	"WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
        }
        else if(!is_null($fadeOut)){
            
            if(Playlist::playlistTimeToSeconds($fadeOut) > Playlist::playlistTimeToSeconds($clipLength)) {
                $errArray["error"]="Fade Out can't be larger than overall playlength.";
                return $errArray;
            }
        
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. " SET fadeout='{$fadeOut}' " .
        	"WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
        }

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $errArray["error"] =$res->getMessage();
            return $errArray;
        }

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
        global $CC_CONFIG, $CC_DBC;
        $errArray= array();
       
        if(is_null($cueIn) && is_null($cueOut)) {
            $errArray["error"]="Cue in and cue out are null.";
            return $errArray;
        }
        
        if(is_null($pos) || $pos < 0 || $pos >= $this->getNextPos()) {
            $errArray["error"]="Invalid position.";
            return $errArray;
        }
        
        $sql =  $sql = "SELECT length AS original_length, cuein, cueout, fadein, fadeout
        	FROM cc_playlistcontents C JOIN cc_files F ON C.file_id = F.id
        	WHERE C.playlist_id='{$this->getId()}' AND position='{$pos}'";
        $res = $CC_DBC->getRow($sql);
        
        $origLength = $res['original_length'];
        $oldCueIn = $res['cuein'];
        $oldCueOut = $res['cueout'];
        $fadeIn = $res['fadein'];
        $fadeOut = $res['fadeout'];
        
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
            
            $cueIn = pg_escape_string($cueIn);
            $cueOut = pg_escape_string($cueOut);
            
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. 
            " SET cuein='{$cueIn}', cueout='{$cueOut}', ".
            "cliplength=(interval '{$cueOut}' - interval '{$cueIn}') " .
            "WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
                       
        }
        else if(!is_null($cueIn)) {
            
            if(Playlist::playlistTimeToSeconds($cueIn) > Playlist::playlistTimeToSeconds($oldCueOut)) {
                $errArray["error"] = "Can't set cue in to be larger than cue out.";
                return $errArray;
            }
            
            $cueIn = pg_escape_string($cueIn);
                   
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. 
            " SET cuein='{$cueIn}', cliplength=(interval '{$oldCueOut}' - interval '{$cueIn}') " .
            "WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
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
            
            $cueOut = pg_escape_string($cueOut);
            
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. 
            " SET cueout='{$cueOut}', cliplength=(interval '{$cueOut}' - interval '{$oldCueIn}') " .
            "WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
        }
              
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $errArray["error"] =$res->getMessage();
            return $errArray;
        }
        
        $sql = "SELECT cliplength FROM ".$CC_CONFIG['playListContentsTable']." 
        WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
        $cliplength = $CC_DBC->getOne($sql);
        
        if(Playlist::playlistTimeToSeconds($fadeIn) > Playlist::playlistTimeToSeconds($cliplength)){
            $fadeIn = $cliplength;
            
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. 
            " SET fadein='{$fadeIn}' " .
            "WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
            
            $res = $CC_DBC->query($sql);
            if (PEAR::isError($res)) {
                $errArray["error"] =$res->getMessage();
                return $errArray;
            }
        }
        if(Playlist::playlistTimeToSeconds($fadeOut) > Playlist::playlistTimeToSeconds($cliplength)){
            $fadeOut = $cliplength;
            
            $sql = "UPDATE ".$CC_CONFIG['playListContentsTable']. 
            " SET fadeout='{$fadeOut}' " .
            "WHERE playlist_id='{$this->getId()}' AND position='{$pos}'";
            
            $res = $CC_DBC->query($sql);
            if (PEAR::isError($res)) {
                $errArray["error"] =$res->getMessage();
                return $errArray;
            }
        }

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
        global $CC_CONFIG, $CC_DBC;

        $cat = $this->categories[$category];
        if($cat === 'length') {
            return $this->getLength();
        }

        $sql = "SELECT {$cat} AS mdata FROM ".$CC_CONFIG['playListTable'].
        " WHERE id='{$this->getId()}'";

        $res = $CC_DBC->getOne($sql);
        if (PEAR::isError($res)) {
            return FALSE;
        }

        return $res;
    }

    public function setPLMetaData($category, $value)
    {
        global $CC_CONFIG, $CC_DBC;

         $cat = $this->categories[$category];

        $sql = "UPDATE ".$CC_CONFIG['playListTable']. " SET {$cat}='{$value}'" .
        " WHERE id='{$this->getId()}'";

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }

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
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        $plArr = array('els'=>array());
        // cycle over playlistElements inside playlist:
        foreach ($arr['children'] as $i => $plEl) {
            switch ($plEl['elementname']) {
            case "playlistElement":  // process playlistElement
                $plElObj = new PlaylistElement($this, $plEl);
                $plInfo = $plElObj->analyze();
                $plArr['els'][] = $plInfo;
                break;
            default:
            }
        }
        $res  = array(array('gunid'=>$plGunid, 'type'=>'playlist'));
        $dd = 0;
        $found = FALSE;
        foreach ($plArr['els'] as $el) {
            extract($el);   // acLen, elOffset, acGunid, fadeIn, fadeOut, playlist
            switch ($el['type']) {
            case "playlist":
                $pl = StoredFile::RecallByGunid($acGunid);
                if (is_null($pl) || PEAR::isError($pl)) {
                	return $pl;
                }
                $res2 = $pl->export();
                if (PEAR::isError($res2)) {
                	return $res2;
                }
                $res = array_merge($res, $res2);
                break;
            default:
                $res[]  = array('gunid'=>$acGunid, 'type'=>$el['type']);
                break;
            }
        }
        return $res;
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
     * Cyclic-recursion checking
     *
     * @param string $insGunid
     * 		gunid of playlist being inserted
     * @return boolean
     * 		true if recursion is detected
     */
    public function cyclicRecursion($insGunid)
    {
        if ($this->gunid == $insGunid) {
        	return TRUE;
        }
        $pl = StoredFile::RecallByGunid($insGunid);
        if (is_null($pl) || PEAR::isError($pl)) {
        	return $pl;
        }
        $arr = $pl->md->genPhpArray();
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        $els =& $arr['children'];
        if (!is_array($els)) {
        	return FALSE;
        }
        foreach ($els as $i => $plEl) {
            if ($plEl['elementname'] != "playlistElement") {
            	continue;
            }
            foreach ($plEl['children'] as $j => $elCh) {
                if ($elCh['elementname'] != "playlist") {
                	continue;
                }
                $nextGunid = $elCh['attrs']['id'];
                $res = $this->cyclicRecursion($nextGunid);
                if ($res) {
                	return TRUE;
                }
            }
        }
        return FALSE;
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
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        if ($toString) {
            $r = PlaylistTagExport::OutputToSmil($this, $arr);
            if (PEAR::isError($r)) {
            	return $r;
            }
            return $r;
        } else {
            return array(
                'type'       => 'playlist',
                'gunid'      => $plGunid,
                'src'        => PL_URL_RELPATH."$plGunid.smil",
                'playlength' => $arr['attrs']['playlength'],
            );
        }
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
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        if ($toString) {
            $r = PlaylistTagExport::OutputToM3u($this, $arr);
            if (PEAR::isError($r)) {
            	return $r;
            }
            return $r;
        } else {
            return array(
                'type'       => 'playlist',
                'gunid'      => $plGunid,
                'uri'        => PL_URL_RELPATH."$plGunid.m3u",
                'playlength' => $arr['attrs']['playlength'],
                'title'      => $arr['attrs']['title'],
            );
        }
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
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        if ($toString) {
            $r = PlaylistTagExport::OutputToRss($this, $arr);
            if (PEAR::isError($r)) {
            	return $r;
            }
            return $r;
        } else {
            return array(
                'type'       => 'playlist',
                'gunid'      => $plGunid,
                'src'        => PL_URL_RELPATH."$plGunid.smil",
                'playlength' => $arr['attrs']['playlength'],
            );
        }
    }


    /**
     * Set values of auxiliary metadata
     *
     * @return mixed
     * 		true or error object
     */
    private function setAuxMetadata()
    {
        // get info about playlist
        $plInfo = $this->getPlaylistInfo();
        if (PEAR::isError($plInfo)) {
        	return $plInfo;
        }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'
        // set gunid as id attr in playlist tag:
        $mid = $this->_getMidOrInsert('id', $parid, $this->gunid, 'A');
        if (PEAR::isError($mid)) {
        	return $mid;
        }
        $r = $this->_setValueOrInsert(
            $mid, $this->gunid, $parid,  'id', 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
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
     * Get info about playlist
     * @param $ac
     * audio clip from cc_files.
     *
     * @return array with fields:
     *  <ul>
     *   <li>plLen string - length of playlist in dcterms:extent format</li>
     *   <li>parid int - metadata record id of playlist container</li>
     *   <li>metaParid int - metadata record id of metadata container</li>
     *  </ul>
     */
    private function getPlaylistInfo($ac)
    {
        $parid = $this->getContainer('playlist');
        if (PEAR::isError($parid)) {
        	return $parid;
        }
        // get playlist length and record id:
        $r = $this->md->getMetadataElement('playlength', $parid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        if (isset($r[0])) {
            $plLen = $r[0]['value'];
        } else {
            $r = $this->md->getMetadataElement('dcterms:extent');
            if (PEAR::isError($r)) {
            	return $r;
            }
            if (isset($r[0])) {
                $plLen = $r[0]['value'];
            } else {
                $plLen = '00:00:00.000000';
            }
        }
        // get main playlist container
        $parid = $this->getContainer('playlist');
        if (PEAR::isError($parid)) {
        	return $parid;
        }
        // get metadata container (optionally insert it)
        $metaParid = $this->getContainer('metadata', $parid, TRUE);
        if (PEAR::isError($metaParid)) {
        	return $metaParid;
        }
        return compact('plLen', 'parid', 'metaParid');
    }


    /**
     * Get container record id, optionally insert new container
     *
     * @param string $containerName
     * @param int $parid
     * 		parent record id
     * @param boolean $insertIfNone - flag if insert may be done
     *      if container wouldn't be found
     * @return int
     * 		metadata record id of container
     */
    private function getContainer($containerName, $parid=NULL, $insertIfNone=FALSE)
    {
        $r = $this->md->getMetadataElement($containerName, $parid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $id = $r[0]['mid'];
        if (!is_null($id)) {
        	return $id;
        }
        if (!$insertIfNone || is_null($parid)) {
            return PEAR::raiseError(
                "Playlist::getContainer: can't find container ($containerName)"
            );
        }
        $id = $this->md->insertMetadataElement($parid, $containerName);
        if (PEAR::isError($id)) {
        	return $id;
        }
        return $id;
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
        global $CC_CONFIG, $CC_DBC;

        if(is_null($fadeIn))
            $fadeIn = '00:00:00.000';
        if(is_null($fadeOut))
            $fadeOut = '00:00:00.000';

        $CC_DBC->query("BEGIN");
        $sql = "INSERT INTO ".$CC_CONFIG['playListContentsTable']
                . "(playlist_id, file_id, position, cliplength, cuein, cueout, fadein, fadeout)"
                . "VALUES ('{$plId}', '{$fileId}', '{$pos}', '{$clipLength}', '{$cuein}', '{$cueout}', '{$fadeIn}', '{$fadeOut}')";

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        // Commit changes
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

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

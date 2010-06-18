<?php
require_once("StoredFile.php");

define('INDCH', ' ');

/**
 * Auxiliary class for GreenBox playlist editing methods.
 *
 * remark: dcterms:extent format: hh:mm:ss.ssssss
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class Playlist extends StoredFile {

    public function __construct($p_gunid=NULL)
    {
        parent::__construct($p_gunid);
    }


    /**
     * Create instance of Playlist object and insert empty file
     *
     * @param GreenBox $gb
     * 		reference to GreenBox object
     * @param global $plid
     * 		unique id
     * @param string $fname
     * 		name of new file
     * @param int $parid
     * 		local object id of parent folder
     * @return instance of Playlist object
     */
    public function create(&$gb, $plid, $fname=NULL, $parid=NULL)
    {
        $tmpFname = uniqid('');
        $oid = BasicStor::AddObj($tmpFname , 'playlist', $parid);
        if (PEAR::isError($oid)) {
        	return $oid;
        }
        $values = array(
            "id" => $oid,
            "metadata" => dirname(__FILE__).'/emptyPlaylist.xml',
            "gunid" => $plid,
            "filetype" => "playlist");
        $pl =& StoredFile::Insert($values);
        if (PEAR::isError($pl)) {
            $res = BasicStor::RemoveObj($oid);
            return $pl;
        }
        $fname = ($fname == '' || is_null($fname) ? "newFile.xml" : $fname );
        $res = $gb->bsRenameFile($oid, $fname);
        if (PEAR::isError($res)) {
        	return $res;
        }
        $res = $pl->setState('ready');
        if (PEAR::isError($res)) {
        	return $res;
        }
        $res = $pl->setMime('application/smil');
        if (PEAR::isError($res)) {
        	return $res;
        }
        $res = $pl->setAuxMetadata();
        if (PEAR::isError($res)) {
        	return $res;
        }
        return $pl;
    }


    /**
     * Lock playlist for edit
     *
     * @param GreenBox $gb
     * 		reference to GreenBox object
     * @param int $subjid
     * 		local subject (user) id
     * @param boolean $val
     * 		if false do unlock
     * @return boolean
     * 		previous state or error object
     */
    public function lock(&$gb, $subjid=NULL, $val=TRUE)
    {
        if ($val && $gb->isEdited($this->gunid) !== FALSE) {
            return PEAR::raiseError(
                'Playlist::lock: playlist already locked'
            );
        }
        $r = $gb->setEditFlag($this->gunid, $val, NULL, $subjid);
        return $r;
    }


    /**
     * Unlock playlist (+recalculate and pregenerate XML)
     *
     * @param GreenBox $gb
     * 		reference to GreenBox object
     * @return boolean
     * 		previous state or error object
     */
    public function unlock(&$gb)
    {
        $r = $this->recalculateTimes();
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->md->regenerateXmlFile();
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->lock($gb, $this->gunid, NULL, FALSE);
        return $r;
    }


    /**
     *  Add audioClip specified by local id to the playlist
     *
     * @param string $acId
     * 		local ID of added file
     * @param string $fadeIn
     * 		optional, in time format hh:mm:ss.ssssss - total duration
     * @param string $fadeOut
     * 		optional, in time format hh:mm:ss.ssssss - total duration
     * @param string $plElGunid
     * 		optional playlist element gunid
     * @param string $length
     * 		optional length in in time format hh:mm:ss.ssssss -
     *      for webstream (or for overrule length of audioclip)
     * @param string $clipstart
     *      optional clipstart in time format hh:mm:ss.ssssss - relative to begin
     * @param string $clipend
     *      optional $clipend in time format hh:mm:ss.ssssss - relative to begin
     * @return string
     * 		generated playlistElement gunid
     */
    public function addAudioClip($acId, $fadeIn=NULL, $fadeOut=NULL, $plElGunid=NULL,
        $length=NULL, $clipstart=NULL, $clipend=NULL)
    {
        $plGunid = $this->gunid;
        // get information about audioClip
        $acInfo = $this->getAudioClipInfo($acId);
        if (PEAR::isError($acInfo)) {
        	return $acInfo;
        }
        extract($acInfo);   // 'acGunid', 'acLen', 'acTit', 'elType'
        if (!is_null($length)) {
        	$acLen = $length;
        }
        // get information about playlist and containers
        $plInfo = $this->getPlaylistInfo();
        if (PEAR::isError($plInfo)) {
        	return $plInfo;
        }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'

        // insert new playlist element
        $offset = $plLen;
		
		 // insert default values if parameter was empty
        $clipStart = !is_null($clipstart) ? $clipstart : '00:00:00.000000';
        $clipEnd = !is_null($clipend) ? $clipend : $acLen;
        
        $acLengthS = $clipLengthS = self::playlistTimeToSeconds($acLen);
        if (!is_null($clipStart)) {
            $clipLengthS = $acLengthS - self::playlistTimeToSeconds($clipStart);    
        }
        if (!is_null($clipEnd)) {
            $clipLengthS = $clipLengthS - ($acLengthS - self::playlistTimeToSeconds($clipEnd));   
        }
        $clipLength = self::secondsToPlaylistTime($clipLengthS);
		
        $plElInfo = $this->insertPlaylistElement($parid, $offset, $clipStart, $clipEnd, $clipLength,
            $acGunid, $acLen, $acTit, $fadeIn, $fadeOut, $plElGunid,
            $elType);
        if (PEAR::isError($plElInfo)) {
        	return $plElInfo;
        }
        return $plElInfo['plElGunid'];
    }


    /**
     * Remove audioClip from playlist
     *
     * @param string $plElGunid
     * 		global id of deleted playlistElement
     * @return boolean
     */
    public function delAudioClip($plElGunid)
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlaylistInfo();
        if (PEAR::isError($plInfo)) {
        	return $plInfo;
        }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'

        // get array of playlist elements:
        $plElArr = $this->md->getMetadataElement('playlistElement', $parid);
        if (PEAR::isError($plElArr)) {
        	return $plElArr;
        }
        $found = FALSE;
        foreach ($plElArr as $el) {
            $plElGunidArr = $this->md->getMetadataElement('id', $el['mid']);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            // select playlist element to remove
            if ($plElGunidArr[0]['value'] == $plElGunid) {
                $acArr = $this->md->getMetadataElement('audioClip', $el['mid']);
                if (PEAR::isError($acArr)) {
                	return $acArr;
                }
                $storedAcMid = $acArr[0]['mid'];
                $acLenArr = $this->md->getMetadataElement('playlength', $storedAcMid);
                if (PEAR::isError($acLenArr)) {
                	return $acLenArr;
                }
                $acLen = $acLenArr[0]['value'];
                // remove playlist element:
                $r = $this->md->setMetadataElement($el['mid'], NULL);
                if (PEAR::isError($r)) {
                	return $r;
                }
                $found = TRUE;
            }
        }
        if (!$found) {
            return PEAR::raiseError(
                "Playlist::delAudioClip: playlistElement not found".
                " ($plElGunid)"
            );
        }
        return TRUE;
    }


    /**
     * Change fadeIn and fadeOut values for playlist Element
     *
     * @param string $plElGunid
     * 		playlistElement gunid
     * @param string $fadeIn
     * 		new value in ss.ssssss or extent format
     * @param string $fadeOut
     * 		new value in ss.ssssss or extent format
     * @return boolean
     */
    public function changeFadeInfo($plElGunid, $fadeIn, $fadeOut)
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlaylistInfo();
        if (PEAR::isError($plInfo)) {
        	return $plInfo;
        }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'

        // get array of playlist elements:
        $plElArr = $this->md->getMetadataElement('playlistElement', $parid);
        if (PEAR::isError($plElArr)) {
        	return $plElArr;
        }
        foreach ($plElArr as $el) {
            $plElGunidArr = $this->md->getMetadataElement('id', $el['mid']);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            // select playlist element:
            if ($plElGunidArr[0]['value'] != $plElGunid) {
            	continue;
            }
            $found = TRUE;
            // get fadeInfo:
            $fiMid = $this->_getMidOrInsert('fadeInfo', $el['mid']);
            if (PEAR::isError($fiMid)) {
            	return $fiMid;
            }
            $fiGunid = StoredFile::CreateGunid();
            $r = $this->_getMidOrInsert('id', $fiMid, $fiGunid, 'A');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $fadeInId = $this->_getMidOrInsert('fadeIn', $fiMid, $fadeIn, 'A');
            if (PEAR::isError($fadeInId)) {
            	return $fadeInId;
            }
            $fadeOutId = $this->_getMidOrInsert('fadeOut', $fiMid, $fadeOut, 'A');
            if (PEAR::isError($fadeOutId)) {
            	return $fadeOutId;
            }
            $r = $this->_setValueOrInsert(
                $fadeInId, $fadeIn, $fiMid, 'fadeIn');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $r = $this->_setValueOrInsert(
                $fadeOutId, $fadeOut, $fiMid, 'fadeOut');
            if (PEAR::isError($r)) {
            	return $r;
            }
            return true;
        }
        return false;
    }
    
        /**
     * Change cueIn/curOut values for playlist element
     *
     * @param string $plElGunid
     * 		playlistElement gunid
     * @param string $fadeIn
     * 		new value in ss.ssssss or extent format
     * @param string $fadeOut
     * 		new value in ss.ssssss or extent format
     * @return boolean or pear error object
     */
    public function changeClipLength($plElGunid, $clipStart, $clipEnd)
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlaylistInfo();
        if (PEAR::isError($plInfo)) {
        	return $plInfo;
        }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'

        // get array of playlist elements:
        $plElArr = $this->md->getMetadataElement('playlistElement', $parid);
        if (PEAR::isError($plElArr)) {
        	return $plElArr;
        }
        foreach ($plElArr as $el) {
            $plElGunidArr = $this->md->getMetadataElement('id', $el['mid']);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            // select playlist element:
            if ($plElGunidArr[0]['value'] != $plElGunid) {
            	continue;
            }
            $found = TRUE;
            // get clipStart Mid
            $clipStartMid = $this->_getMidOrInsert('clipStart', $el['mid']);
            if (PEAR::isError($clipStartMid)) {
            	return $clipStartMid;
            }
            // get clipEnd Mid
            $clipEndMid = $this->_getMidOrInsert('clipEnd', $el['mid']);
            if (PEAR::isError($clipEndMid)) {
            	return $clipEndMid;
            }
            // get clipLength Mid
            $clipLengthMid = $this->_getMidOrInsert('clipLength', $el['mid']);
            if (PEAR::isError($clipLengthMid)) {
            	return $clipLengthMid;
            }
            // set clipStart value
            $r = $this->md->setMetadataElement($clipStartMid, $clipStart);
            if (PEAR::isError($r)) {
            	return $r;
            }
            // setClipend value
            $r = $this->md->setMetadataElement($clipEndMid, $clipEnd);
            if (PEAR::isError($r)) {
            	return $r;
            }
            // set playlength value
            $clipLength = self::secondsToPlaylistTime(self::playlistTimeToSeconds($clipEnd) - self::playlistTimeToSeconds($clipStart));
            $r = $this->md->setMetadataElement($clipLengthMid, $clipLength);
            if (PEAR::isError($r)) {
            	return $r;
            }
            $this->recalculateTimes();
            return true;
        }
        return false;
    }


    /**
     * Move audioClip to the new position in the playlist
     *
     * @param string $plElGunid
     * 		playlistElement gunid
     * @param int $newPos
     * 		new position in playlist
     * @return mixed
     */
    public function moveAudioClip($plElGunid, $newPos)
    {
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        $els =& $arr['children'];
        foreach ($els as $i => $el) {
            if ($el['elementname'] != 'playlistElement') {
                $metadata = array_splice($els, $i, 1);
                continue;
            }
        }
        foreach ($els as $i => $el) {
            if ($el['attrs']['id'] == $plElGunid) {
                $movedi = $i;
            }
            $r = $this->delAudioClip($el['attrs']['id']);
            if (PEAR::isError($r)) {
            	return $r;
            }
        }
        if ($newPos < 1) {
        	$newPos = 1;
        }
        if ($newPos>count($els)) {
        	$newPos = count($els);
        }
        $movedel = array_splice($els, $movedi, 1);
        array_splice($els, $newPos-1, 0, $movedel);
        foreach ($els as $i => $el) {
            $plElGunid2 = $el['attrs']['id'];
            $fadeIn = NULL;
            $fadeOut = NULL;
            foreach ($el['children'] as $j => $af) {
                switch ($af['elementname']) {
                    case "audioClip":
                    case "playlist":
                        $acGunid = $af['attrs']['id'];
                        break;
                    case "fadeInfo":
                        $fadeIn = $af['attrs']['fadeIn'];
                        $fadeOut = $af['attrs']['fadeOut'];
                        break;
                    default:
                        return PEAR::raiseError(
                            "Playlist::moveAudioClip: unknown element type".
                            " in playlistElement ({$af['elementname']})"
                        );
                }
            }
            $acId = BasicStor::IdFromGunid($acGunid);
            if (PEAR::isError($acId)) {
            	return $acId;
            }
            if (is_null($acId)) {
                return PEAR::raiseError(
                    "Playlist::moveAudioClip: null audioClip gunid"
                );
            }
            $r = $this->addAudioClip($acId, $fadeIn, $fadeOut, $plElGunid2);
            if (PEAR::isError($r)) {
            	return $r;
            }
        }
        return TRUE;
    }


    /**
     * Recalculate total length of playlist and relativeOffset values
     * of all playlistElements according to legth and fadeIn values.
     * FadeOut values adjusted to next fadeIn.
     *
     * @return boolean
     */
    public function recalculateTimes()
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlaylistInfo();
        if (PEAR::isError($plInfo)) {
        	return $plInfo;
        }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'
        // get array of playlist elements:
        $plElArr = $this->md->getMetadataElement('playlistElement', $parid);
        if (PEAR::isError($plElArr)) {
        	return $plElArr;
        }
        $peArr = array();
        $len = 0;
        $nextOffset = $len;
        $prevFiMid = NULL;
        $lastLenS = NULL;
        foreach ($plElArr as $el) {
            $elId = $el['mid'];
            // get playlistElement gunid:
            $plElGunidArr = $this->md->getMetadataElement('id', $elId);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            $plElGunid = $plElGunidArr[0]['value'];
            // get relativeOffset:
            $offArr = $this->md->getMetadataElement('relativeOffset', $elId);
            if (PEAR::isError($offArr)) {
            	return $offArr;
            }
            // get clipStart:
            $clipStartArr = $this->md->getMetadataElement('clipStart', $elId);
            if (PEAR::isError($clipStartArr)) {
            	return $clipStartArr;
            }
            $clipStart = $clipStartArr[0]['value'];
            $clipStartS = Playlist::playlistTimeToSeconds($clipStart);
            // get clipEnd:
            $clipEndArr = $this->md->getMetadataElement('clipEnd', $elId);
            if (PEAR::isError($clipEndArr)) {
            	return $clipEndArr;
            }
            $clipEnd = $clipEndArr[0]['value'];
            $clipEndS = Playlist::playlistTimeToSeconds($clipEnd);
            // get clipLength:
            $lenArr = $this->md->getMetadataElement('clipLength', $elId);
            if (PEAR::isError($lenArr)) {
            	return $lenArr;
            }
            $offsetId = $offArr[0]['mid'];
            $offset = $offArr[0]['value'];
            // get audioClip:
            $acArr = $this->md->getMetadataElement('audioClip', $elId);
            if (is_array($acArr) && (!isset($acArr[0]) || is_null($acArr[0]))) {
                $acArr = $this->md->getMetadataElement('playlist', $elId);
            }
            if (PEAR::isError($acArr)) {
            	return $acArr;
            }
            $storedAcMid = $acArr[0]['mid'];
            // get playlength:
            $acLenArr = $this->md->getMetadataElement('playlength', $storedAcMid);
            if (PEAR::isError($acLenArr)) {
            	return $acLenArr;
            }
            $acLen = $acLenArr[0]['value'];
            // get fadeInfo:
            $fiArr = $this->md->getMetadataElement('fadeInfo', $elId);
            if (PEAR::isError($fiArr)) {
            	return $fiArr;
            }
            if (isset($fiArr[0]['mid'])) {
                $fiMid = $fiArr[0]['mid'];
                $fadeInArr = $this->md->getMetadataElement('fadeIn', $fiMid);
                if (PEAR::isError($fadeInArr)) {
                	return $fadeInArr;
                }
                $fadeIn = $fadeInArr[0]['value'];
                $fadeOutArr = $this->md->getMetadataElement('fadeOut', $fiMid);
                if (PEAR::isError($fadeOutArr)) {
                	return $fadeOutArr;
                }
                $fadeOut = $fadeOutArr[0]['value'];
            } else {
                $fiMid = NULL;
                $fadeIn = '00:00:00.000000';
                $fadeOut = '00:00:00.000000';
            }
            $fadeInS = Playlist::playlistTimeToSeconds($fadeIn);
            if (!is_null($lastLenS)) {
                if ($lastLenS < $fadeInS) {
                    return PEAR::raiseError(
                        "Playlist::recalculateTimes: fadeIn too big");
                }
            }
            /*
            this seems made for crossfade
            if ($len > 0) {
            	$len = $len - $fadeInS;
            }
            */
            
            $newOffset = Playlist::secondsToPlaylistTime($len);
            $r = $this->_setValueOrInsert($offsetId, $newOffset, $elId, 'relativeOffset');
            if (PEAR::isError($r)) {
            	return $r;
            }
            
            // commulate length for next offset
            $acLenS = Playlist::playlistTimeToSeconds($acLen);
            $len = $len + $acLenS;
            if (!is_null($prevFiMid)) {
                $foMid = $this->_getMidOrInsert('fadeOut', $prevFiMid, $fadeIn, 'A');
                if (PEAR::isError($foMid)){
                	return $foMid;
                }
                $r = $this->_setValueOrInsert(
                    $foMid, $fadeIn, $prevFiMid, 'fadeOut', 'A');
                if (PEAR::isError($r)) {
                	return $r;
                }
            }
            // respect clipStart and clipEnd
            if (!is_null($clipStart)) {
                $len = $len - $clipStartS;    
            }
            if (!is_null($clipEnd)) {
                $len = $len - ($acLenS - $clipEndS);   
            }
            
            $prevFiMid = $fiMid;
            $lastLenS = $acLenS;
        }
        $newPlLen = Playlist::secondsToPlaylistTime($len);
        $r = $this->setPlaylistLength($newPlLen, $parid, $metaParid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
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
    public function displayPlaylistClipAtOffset($offset, $distance=0)
    {
        $offsetS = Playlist::playlistTimeToSeconds($offset);
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
        $res = array('gunid'=>NULL, 'elapsed'=>NULL,
                     'remaining'=>NULL, 'duration'=>NULL);
        $dd = 0;
        $found = FALSE;
        foreach ($plArr['els'] as $el) {
            extract($el);   // acGunid, acLen, acLenS, clipEnd, clipEndS, clipStart, clipStartS,
                            // elOffset, elOffsetS, fadeIn, fadeInS, fadeOut, fadeOutS, type
            $lengthS = $acLenS;
            if ($clipEndS) {
                $lengthS = $clipEndS;
            }
            if ($clipStartS) {
                $lengthS = $lengthS - $clipStartS;
            }
            if ( ($offsetS >= $elOffsetS) && ($offsetS < ($elOffsetS + $lengthS)) ) {
            	$found = TRUE;
            }
            if ($found) {               // we've found offset
                switch ($el['type']) {
                case "playlist":
                    $pl = StoredFile::RecallByGunid($acGunid);
                    if (is_null($pl) || PEAR::isError($pl)) {
                    	return $pl;
                    }
                    if ($dd > 0) {
                        $offsetLoc = "00:00:00.000000";
                    } else {
                        $offsetLoc = Playlist::secondsToPlaylistTime($offsetS - $elOffsetS);
                    }
                    $distanceLoc = $distance - $dd;
                    $res2 = $pl->displayPlaylistClipAtOffset($offsetLoc, $distanceLoc);
                    if (PEAR::isError($res2)) {
                    	return $res2;
                    }
                    if (!is_null($res2['gunid'])) {
                    	return $res2;
                    }
                    $dd += $res2['dd'];
                    break;
                case "audioClip":
                    if ($dd == $distance) {
                        $playedS = $offsetS - $elOffsetS;
                        if ($playedS < 0) {
                        	$playedS = 0;
                        }
                        $remainS = $lengthS - $playedS;
                        $res  = array('gunid'=>$acGunid,
                            'elapsed'   => Playlist::secondsToPlaylistTime($playedS),
                            'remaining' => Playlist::secondsToPlaylistTime($remainS),
                            'duration'  => Playlist::secondsToPlaylistTime($lengthS),
                        );
                        return $res;
                    }
                    $res['dd'] = $dd;
                    break;
                }
                $dd++;
            }
        }
        return $res;
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
        $arr = split(':', $plt);
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
    private function getAudioClipInfo($acId)
    {
        $ac = StoredFile::Recall($acId);
        if (is_null($ac) || PEAR::isError($ac)) {
        	return $ac;
        }
        $acGunid = $ac->gunid;
        $r = $ac->md->getMetadataElement('dcterms:extent');
        if (PEAR::isError($r)) {
        	return $r;
        }
        if (isset($r[0]['value'])) {
        	$acLen = $r[0]['value'];
        } else {
        	$acLen = '00:00:00.000000';
        }
        $r = $ac->md->getMetadataElement('dc:title');
        if (PEAR::isError($r)) {
        	return $r;
        }
        if (isset($r[0]['value'])) {
        	$acTit = $r[0]['value'];
        } else {
        	$acTit = $acGunid;
        }
        $elType = BasicStor::GetObjType($acId);
        $trTbl = array('audioclip'=>'audioClip', 'webstream'=>'audioClip',
            'playlist'=>'playlist');
        $elType = $trTbl[$elType];
        if ($elType == 'webstream') {
        	$elType = 'audioClip';
        }
        return compact('acGunid', 'acLen', 'acTit', 'elType');
    }


    /**
     * Get info about playlist
     *
     * @return array with fields:
     *  <ul>
     *   <li>plLen string - length of playlist in dcterms:extent format</li>
     *   <li>parid int - metadata record id of playlist container</li>
     *   <li>metaParid int - metadata record id of metadata container</li>
     *  </ul>
     */
    private function getPlaylistInfo()
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
     * @param int $parid
     * 		parent record id
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
     * @param string $plElGunid
     * 		optional playlist element gunid
     * @param string $elType
     * 		optional 'audioClip' | 'playlist'
     * @return array with fields:
     *  <ul>
     *   <li>plElId int - record id of playlistElement</li>
     *   <li>plElGunid string - gl.unique id of playlistElement</li>
     *   <li>fadeInId int - record id</li>
     *   <li>fadeOutId int - record id</li>
     *  </ul>
     */
    private function insertPlaylistElement($parid, $offset, $clipStart, $clipEnd, $clipLength, $acGunid, $acLen, $acTit,
        $fadeIn=NULL, $fadeOut=NULL, $plElGunid=NULL, $elType='audioClip')
    {
        // insert playlistElement
        $r = $this->md->insertMetadataElement($parid, 'playlistElement');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $plElId = $r;
        // create and insert gunid (id attribute)
        if (is_null($plElGunid)) {
        	$plElGunid = StoredFile::CreateGunid();
        }
        $r = $this->md->insertMetadataElement($plElId, 'id', $plElGunid, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert relativeOffset
        $r = $this->md->insertMetadataElement(
            $plElId, 'relativeOffset', $offset, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert clipLength
        $r = $this->md->insertMetadataElement(
            $plElId, 'clipLength', $clipLength, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert clipStart
        $r = $this->md->insertMetadataElement(
            $plElId, 'clipStart', $clipStart, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert clipEnd
        $r = $this->md->insertMetadataElement(
            $plElId, 'clipEnd', $clipEnd, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert audioClip (or playlist) element into playlistElement
        $r = $this->md->insertMetadataElement($plElId, $elType);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $acId = $r;
        $r = $this->md->insertMetadataElement($acId, 'id', $acGunid, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->md->insertMetadataElement($acId, 'playlength', $acLen, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->md->insertMetadataElement($acId, 'title', $acTit, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $fadeInId=NULL;
        $fadeOutId=NULL;
        if (!is_null($fadeIn) || !is_null($fadeOut)) {
            // insert fadeInfo element into playlistElement
            $r = $this->md->insertMetadataElement($plElId, 'fadeInfo');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $fiId = $r;
            $fiGunid = StoredFile::CreateGunid();
            $r = $this->md->insertMetadataElement($fiId, 'id', $fiGunid, 'A');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $r = $this->md->insertMetadataElement($fiId, 'fadeIn', $fadeIn, 'A');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $fadeInId = $r;
            $r = $this->md->insertMetadataElement($fiId, 'fadeOut', $fadeOut, 'A');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $fadeOutId = $r;
        }
        return compact('plElId', 'plElGunid', 'fadeInId', 'fadeOutId');
    }


    /**
     * Return record id, optionally insert new record
     *
     * @param string $category
     * 		qualified name of metadata category
     * @param int $parid
     * 		parent record id
     * @param string $value
     * 		value for inserted record
     * @param string $predxml
     * 		'A' | 'T' (attribute or tag)
     * @return int
     * 		metadata record id
     */
    private function _getMidOrInsert($category, $parid, $value=NULL, $predxml='T')
    {
        $arr = $this->md->getMetadataElement($category, $parid);
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        $mid = NULL;
        if (isset($arr[0]['mid'])) {
        	$mid = $arr[0]['mid'];
        }
        if (!is_null($mid)) {
        	return $mid;
        }
        $mid = $this->md->insertMetadataElement($parid, $category, $value, $predxml);
        if (PEAR::isError($mid)) {
        	return $mid;
        }
        return $mid;
    }


    /**
     * Set value of metadata record, optionally insert new record
     *
     * @param int $mid
     * 		record id
     * @param string $value
     * 		value for inserted record
     * @param int $parid
     * 		parent record id
     * @param string $category
     * 		qualified name of metadata category
     * @param string $predxml
     * 		'A' | 'T' (attribute or tag)
     * @return boolean
     */
    private function _setValueOrInsert($mid, $value, $parid, $category, $predxml='T')
    {
        if (is_null($mid)) {
            $r = $this->md->insertMetadataElement(
                $parid, $category, $value, $predxml);
        } else {
            $r = $this->md->setMetadataElement($mid, $value);
        }
        if (PEAR::isError($r)) {
        	return $r;
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
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class PlaylistElement {
    private $pl = NULL;
    private $plEl = NULL;

    public function PlaylistElement(&$pl, $plEl)
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
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
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
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
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
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
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
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
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
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
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

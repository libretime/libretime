<?php
require_once("StoredFile.php");

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

    /**
     * Create instance of Playlist object and recall existing file
     * by gunid.<br/>
     *
     * @param GreenBox $gb
     * 		reference to GreenBox object
     * @param string $gunid
     * 		global unique id
     * @param string $className
     * 		optional classname to recall
     * @return Playlist
     * 		instance of Playlist object
     */
    public static function &recallByGunid(&$gb, $gunid, $className='Playlist')
    {
        return parent::recallByGunid($gb, $gunid, $className);
    }


    /**
     * Create instance of Playlist object and recall existing file
     * by access token.<br/>
     *
     * @param GreenBox $gb
     * 		reference to GreenBox object
     * @param string $token
     * 		access token
     * @param string $className
     * 		optional classname to recall
     * @return Playlist
     * 		instance of Playlist object
     */
    public static function &recallByToken(&$gb, $token, $className='Playlist')
    {
        return parent::recallByToken($gb, $token, $className);
    }


    /**
     * Create instance of Playlist object and insert new file
     *
     * @param GreenBox $gb
     * 		reference to GreenBox object
     * @param int $oid
     * 		local object id in the tree
     * @param string $fname
     * 		name of new file
     * @param string $mediaFileLP
     * 		ignored
     * @param string $metadata
     * 		local path to playlist XML file or XML string
     * @param string $mdataLoc
     * 		'file'|'string' (optional)
     * @param global $plid
     * 		unique id (optional) - for insert file with gunid
     * @param string $ftype
     * 		ignored
     * @return Playlist
     * 		instance of Playlist object
     */
    public static function &insert(&$gb, $oid, $fname,
        $mediaFileLP='', $metadata='', $mdataLoc='file',
        $plid=NULL, $ftype=NULL)
    {
        return parent::insert($gb, $oid, $fname,
            '', $metadata, $mdataLoc, $plid, 'playlist', 'Playlist');
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
    public function &create(&$gb, $plid, $fname=NULL, $parid=NULL)
    {
        $tmpFname = uniqid('');
        $oid = BasicStor::AddObj($tmpFname , 'playlist', $parid);
        if (PEAR::isError($oid)) {
        	return $oid;
        }
        $pl =&  Playlist::insert($gb, $oid, '', '',
            dirname(__FILE__).'/emptyPlaylist.xml',
            'file', $plid
        );
        if (PEAR::isError($pl)) {
            $res = $gb->removeObj($oid);
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
        $ac = StoredFile::recall($this->gb, $acId);
        if (PEAR::isError($ac)) {
        	return $ac;
        }
        $acGunid = $ac->gunid;
        $r = $ac->md->getMetadataEl('dcterms:extent');
        if (PEAR::isError($r)) {
        	return $r;
        }
        if (isset($r[0]['value'])) {
        	$acLen = $r[0]['value'];
        } else {
        	$acLen = '00:00:00.000000';
        }
        $r = $ac->md->getMetadataEl('dc:title');
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
        $r = $this->md->getMetadataEl('playlength', $parid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        if (isset($r[0])) {
            $plLen = $r[0]['value'];
        } else {
            $r = $this->md->getMetadataEl('dcterms:extent');
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
        $r = $this->md->getMetadataEl($containerName, $parid);
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
        $id = $this->md->insertMetadataEl($parid, $containerName);
        if (PEAR::isError($id)) {
        	return $id;
        }
        return $id;
    }


    /**
     * Inserting of new playlistEelement
     *
     * @param int $parid
     * 		parent record id
     * @param string $offset
     * 		relative offset in extent format
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
    private function insertPlaylistElement($parid, $offset, $acGunid, $acLen, $acTit,
        $fadeIn=NULL, $fadeOut=NULL, $plElGunid=NULL, $elType='audioClip')
    {
        // insert playlistElement
        $r = $this->md->insertMetadataEl($parid, 'playlistElement');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $plElId = $r;
        // create and insert gunid (id attribute)
        if (is_null($plElGunid)) {
        	$plElGunid = StoredFile::CreateGunid();
        }
        $r = $this->md->insertMetadataEl($plElId, 'id', $plElGunid, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert relativeOffset
        $r = $this->md->insertMetadataEl(
            $plElId, 'relativeOffset', $offset, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        // insert audioClip (or playlist) element into playlistElement
        $r = $this->md->insertMetadataEl($plElId, $elType);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $acId = $r;
        $r = $this->md->insertMetadataEl($acId, 'id', $acGunid, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->md->insertMetadataEl($acId, 'playlength', $acLen, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->md->insertMetadataEl($acId, 'title', $acTit, 'A');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $fadeInId=NULL;
        $fadeOutId=NULL;
        if (!is_null($fadeIn) || !is_null($fadeOut)) {
            // insert fadeInfo element into playlistElement
            $r = $this->md->insertMetadataEl($plElId, 'fadeInfo');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $fiId = $r;
            $fiGunid = StoredFile::CreateGunid();
            $r = $this->md->insertMetadataEl($fiId, 'id', $fiGunid, 'A');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $r = $this->md->insertMetadataEl($fiId, 'fadeIn', $fadeIn, 'A');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $fadeInId = $r;
            $r = $this->md->insertMetadataEl($fiId, 'fadeOut', $fadeOut, 'A');
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
        $arr = $this->md->getMetadataEl($category, $parid);
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
        $mid = $this->md->insertMetadataEl($parid, $category, $value, $predxml);
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
            $r = $this->md->insertMetadataEl(
                $parid, $category, $value, $predxml);
        } else {
            $r = $this->md->setMetadataEl($mid, $value);
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

    /**
     *  Add audioClip specified by local id to the playlist
     *
     * @param string $acId
     * 		local ID of added file
     * @param string $fadeIn
     * 		optional, in time format hh:mm:ss.ssssss
     * @param string $fadeOut
     * 		optional, in time format hh:mm:ss.ssssss
     * @param string $plElGunid
     * 		optional playlist element gunid
     * @param string $length
     * 		optional length in extent format -
     *      for webstream (or for overrule length of audioclip)
     * @return string
     * 		generated playlistElement gunid
     */
    public function addAudioClip($acId, $fadeIn=NULL, $fadeOut=NULL, $plElGunid=NULL,
        $length=NULL)
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
        $plElInfo = $this->insertPlaylistElement($parid, $offset,
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
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
        if (PEAR::isError($plElArr)) {
        	return $plElArr;
        }
        $found = FALSE;
        foreach ($plElArr as $el) {
            $plElGunidArr = $this->md->getMetadataEl('id', $el['mid']);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            // select playlist element to remove
            if ($plElGunidArr[0]['value'] == $plElGunid) {
                $acArr = $this->md->getMetadataEl('audioClip', $el['mid']);
                if (PEAR::isError($acArr)) {
                	return $acArr;
                }
                $storedAcMid = $acArr[0]['mid'];
                $acLenArr = $this->md->getMetadataEl('playlength', $storedAcMid);
                if (PEAR::isError($acLenArr)) {
                	return $acLenArr;
                }
                $acLen = $acLenArr[0]['value'];
                // remove playlist element:
                $r = $this->md->setMetadataEl($el['mid'], NULL);
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
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
        if (PEAR::isError($plElArr)) {
        	return $plElArr;
        }
        $found = FALSE;
        foreach ($plElArr as $el) {
            $plElGunidArr = $this->md->getMetadataEl('id', $el['mid']);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            // select playlist element:
            if ($plElGunidArr[0]['value'] != $plElGunid) {
            	continue;
            }
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
        }
        return TRUE;
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
                    case"audioClip":
                    case"playlist":
                        $acGunid = $af['attrs']['id'];
                        break;
                    case"fadeInfo":
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
     * Recalculate total length of playlist and  relativeOffset values
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
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
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
            $plElGunidArr = $this->md->getMetadataEl('id', $elId);
            if (PEAR::isError($plElGunidArr)) {
            	return $plElGunidArr;
            }
            $plElGunid = $plElGunidArr[0]['value'];
            // get relativeOffset:
            $offArr = $this->md->getMetadataEl('relativeOffset', $elId);
            if (PEAR::isError($offArr)) {
            	return $offArr;
            }
            $offsetId = $offArr[0]['mid'];
            $offset = $offArr[0]['value'];
            // get audioClip:
            $acArr = $this->md->getMetadataEl('audioClip', $elId);
            if (is_array($acArr) && (!isset($acArr[0]) || is_null($acArr[0]))) {
                $acArr = $this->md->getMetadataEl('playlist', $elId);
            }
            if (PEAR::isError($acArr)) {
            	return $acArr;
            }
            $storedAcMid = $acArr[0]['mid'];
            // get playlength:
            $acLenArr = $this->md->getMetadataEl('playlength', $storedAcMid);
            if (PEAR::isError($acLenArr)) {
            	return $acLenArr;
            }
            $acLen = $acLenArr[0]['value'];
            // get fadeInfo:
            $fiArr = $this->md->getMetadataEl('fadeInfo', $elId);
            if (PEAR::isError($fiArr)) {
            	return $fiArr;
            }
            if (isset($fiArr[0]['mid'])) {
                $fiMid = $fiArr[0]['mid'];
                $fadeInArr = $this->md->getMetadataEl('fadeIn', $fiMid);
                if (PEAR::isError($fadeInArr)) {
                	return $fadeInArr;
                }
                $fadeIn = $fadeInArr[0]['value'];
                $fadeOutArr = $this->md->getMetadataEl('fadeOut', $fiMid);
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
            // $peArr[] = array('id'=>$elId, 'gunid'=>$plElGunid, 'len'=>$acLen,
            //    'offset'=>$offset, 'offsetId'=>$offsetId,
            //    'fadeIn'=>$fadeIn, 'fadeOut'=>$fadeOut);
            // set relativeOffset:
            if ($len > 0) {
            	$len = $len - $fadeInS;
            }
            $newOffset = Playlist::secondsToPlaylistTime($len);
            $r = $this->_setValueOrInsert($offsetId, $newOffset, $elId, 'relativeOffset');
            if (PEAR::isError($r)) {
            	return $r;
            }
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
            case"playlistElement":  // process playlistElement
                $plElObj = new PlaylistElement($this, $plEl);
                $plInfo = $plElObj->analyze();
                $plArr['els'][] = $plInfo;
                break;
            default:
            }
        }
        $res  = array('gunid'=>NULL, 'elapsed'=>NULL,
            'remaining'=>NULL, 'duration'=>NULL);
        $dd = 0;
        $found = FALSE;
        foreach ($plArr['els'] as $el) {
            extract($el);   // acLen, elOffset, acGunid, fadeIn, fadeOut, playlist
            if ( ($offsetS >= $elOffsetS) && ($offsetS < ($elOffsetS + $acLenS)) ) {
            	$found = TRUE;
            }
            if ($found) {               // we've found offset
                switch ($el['type']) {
                case "playlist":
                    $pl = Playlist::recallByGunid($this->gb, $acGunid);
                    if (PEAR::isError($pl)) {
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
                        $remainS = $acLenS - $playedS;
                        $res  = array('gunid'=>$acGunid,
                            'elapsed'   => Playlist::secondsToPlaylistTime($playedS),
                            'remaining' => Playlist::secondsToPlaylistTime($remainS),
                            'duration'  => Playlist::secondsToPlaylistTime($acLenS),
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
                $pl = Playlist::recallByGunid($this->gb, $acGunid);
                if (PEAR::isError($pl)) {
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
     * @param int $s0
     * 		seconds
     * @return string
     * 		time in playlist time format (HH:mm:ss.dddddd)
     */
    public static function secondsToPlaylistTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        $res = sprintf("%02d:%02d:%02d.000000", $hours, $minutes, $seconds);
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
        $pl = Playlist::recallByGunid($this->gb, $insGunid);
        if (PEAR::isError($pl)) {
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

} // class Playlist


/**
 * Auxiliary class for GB playlist editing methods
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
        $this->pl   = $pl;
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
        }
        return $plInfo;
    }
} // class PlaylistElement

?>
<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.13 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Playlist.php,v $

------------------------------------------------------------------------------*/

/**
 *  Auxiliary class for GB playlist editing methods
 *
 *  remark: dcterms:extent format: hh:mm:ss.ssssss
 */
class Playlist extends StoredFile{
    
    /**
     *  Create instace of Playlist object and recall existing file
     *  by gunid.<br/>
     *
     *  @param gb reference to GreenBox object
     *  @param gunid string, global unique id
     *  @param className string, optional classname to recall
     *  @return instace of Playlist object
     */
    function recallByGunid(&$gb, $gunid, $className='Playlist')
    {
        return parent::recallByGunid($gb, $gunid, $className);
    }

    /**
     *  Create instace of Playlist object and recall existing file
     *  by access token.<br/>
     *
     *  @param gb reference to GreenBox object
     *  @param token string, access token
     *  @param className string, optional classname to recall
     *  @return instace of Playlist object
     */
    function recallByToken(&$gb, $token, $className='Playlist')
    {
        return parent::recallByToken($gb, $token, $className);
    }

    /**
     *  Get audioClip legth and title
     *
     *  @param acId int, local id of audioClip inserted to playlist
     *  @return array with fields:
     *  <ul>
     *   <li>acGunid, string - audioClip gunid</li>
     *   <li>acLen string - length of clip in dcterms:extent format</li>
     *   <li>acTit string - clip title</li>
     *   <li>elType string - audioClip | playlist</li>
     *  </ul>
     */
    function getAcInfo($acId)
    {
        $ac =& StoredFile::recall($this->gb, $acId);
        if(PEAR::isError($ac)){ return $ac; }
        $acGunid = $ac->gunid;
        $r = $ac->md->getMetadataEl('dcterms:extent');
        if(PEAR::isError($r)){ return $r; }
        if(isset($r[0]['value'])) $acLen = $r[0]['value'];
        else $acLen = '00:00:00.000000';
        $r = $ac->md->getMetadataEl('dc:title');
        if(PEAR::isError($r)){ return $r; }
        if(isset($r[0]['value'])) $acTit = $r[0]['value'];
        else $acTit = $acGunid;
        $elType = $this->gb->getObjType($acId);
        $trTbl = array('audioclip'=>'audioClip', 'webstream'=>'audioClip',
            'playlist'=>'playlist');
        $elType = $trTbl[$elType];
        if($elType == 'webstream') $elType = 'audioClip';
        return compact('acGunid', 'acLen', 'acTit', 'elType');
    }

    /**
     *  Get info about playlist
     *
     *  @return array with fields:
     *  <ul>
     *   <li>plLen string - length of playlist in dcterms:extent format</li>
     *   <li>parid int - metadata record id of playlist container</li>
     *   <li>metaParid int - metadata record id of metadata container</li>
     *  </ul>
     */
    function getPlInfo()
    {
        $parid = $this->getContainer('playlist');
        if(PEAR::isError($parid)){ return $parid; }
        // get playlist length and record id:
        $r = $this->md->getMetadataEl('playlength', $parid);
        if(PEAR::isError($r)){ return $r; }
        if(isset($r[0])){
            $plLen = $r[0]['value'];
        }else{
            $r = $this->md->getMetadataEl('dcterms:extent');
            if(PEAR::isError($r)){ return $r; }
            if(isset($r[0])){
                $plLen = $r[0]['value'];
            }else{
                $plLen = '00:00:00.000000';
            }
        }
        // get main playlist container
        $parid = $this->getContainer('playlist');
        if(PEAR::isError($parid)){ return $parid; }
        // get metadata container (optionally insert it)
        $metaParid = $this->getContainer('metadata', $parid, TRUE);
        if(PEAR::isError($metaParid)){ return $metaParid; }
        return compact('plLen', 'parid', 'metaParid');
    }
    
    /**
     *  Get container record id, optionally insert new container
     *
     *  @param containerName string
     *  @param parid int - parent record id
     *  @param insertIfNone boolean - flag if insert may be done
     *      if container wouldn't be found
     *  @return int - metadata record id of container
     */
    function getContainer($containerName, $parid=NULL, $insertIfNone=FALSE)
    {
        $r = $this->md->getMetadataEl($containerName, $parid);
        if(PEAR::isError($r)){ return $r; }
        $id = $r[0]['mid'];
        if(!is_null($id)) return $id;
        if(!$insertIfNone || is_null($parid)){
            return PEAR::raiseError(
                "Playlist::getContainer: can't find container ($containerName)"
            );
        }
        $id = $this->md->insertMetadataEl($parid, $containerName);
        if(PEAR::isError($id)){ return $id; }
        return $id;
    }
    
    /**
     *  Inserting of new playlistEelement
     *
     *  @param parid int - parent record id
     *  @param offset string - relative offset in extent format
     *  @param acGunid string - audioClip gunid
     *  @param acLen string - audiClip length in extent format
     *  @param acTit string - audioClip title
     *  @param fadeIn string - fadeIn value in ss.ssssss or extent format
     *  @param fadeOut string - fadeOut value in ss.ssssss or extent format
     *  @param plElGunid string - optional playlist element gunid
     *  @param elType string - optional 'audioClip' | 'playlist'
     *  @return array with fields:
     *  <ul>
     *   <li>plElId int - record id of playlistElement</li>
     *   <li>plElGunid string - gl.unique id of playlistElement</li>
     *   <li>fadeInId int - record id</li>
     *   <li>fadeOutId int - record id</li>
     *  </ul>
     */
    function insertPlaylistElement($parid, $offset, $acGunid, $acLen, $acTit,
        $fadeIn=NULL, $fadeOut=NULL, $plElGunid=NULL, $elType='audioClip')
    {
        // insert playlistElement
        $r = $this->md->insertMetadataEl($parid, 'playlistElement');
        if(PEAR::isError($r)){ return $r; }
        $plElId = $r;
        // create and insert gunid (id attribute)
        if(is_null($plElGunid)) $plElGunid = StoredFile::_createGunid();
        $r = $this->md->insertMetadataEl($plElId, 'id', $plElGunid, 'A');
        if(PEAR::isError($r)){ return $r; }
        // insert relativeOffset
        $r = $this->md->insertMetadataEl(
            $plElId, 'relativeOffset', $offset, 'A');
        if(PEAR::isError($r)){ return $r; }
        // insert audioClip (or playlist) element into playlistElement
        $r = $this->md->insertMetadataEl($plElId, $elType);
        if(PEAR::isError($r)){ return $r; }
        $acId = $r;
        $r = $this->md->insertMetadataEl($acId, 'id', $acGunid, 'A');
        if(PEAR::isError($r)){ return $r; }
        $r = $this->md->insertMetadataEl($acId, 'playlength', $acLen, 'A');
        if(PEAR::isError($r)){ return $r; }
        $r = $this->md->insertMetadataEl($acId, 'title', $acTit, 'A');
        if(PEAR::isError($r)){ return $r; }
        $fadeInId=NULL;
        $fadeOutId=NULL;
        if(!is_null($fadeIn) || !is_null($fadeOut)){
            // insert fadeInfo element into playlistElement
            $r = $this->md->insertMetadataEl($plElId, 'fadeInfo');
            if(PEAR::isError($r)){ return $r; }
            $fiId = $r;
            $fiGunid = StoredFile::_createGunid();
            $r = $this->md->insertMetadataEl($fiId, 'id', $fiGunid, 'A');
            if(PEAR::isError($r)){ return $r; }
            $r = $this->md->insertMetadataEl($fiId, 'fadeIn', $fadeIn, 'A');
            if(PEAR::isError($r)){ return $r; }
            $fadeInId = $r;
            $r = $this->md->insertMetadataEl($fiId, 'fadeOut', $fadeOut, 'A');
            if(PEAR::isError($r)){ return $r; }
            $fadeOutId = $r;
        }
        return compact('plElId', 'plElGunid', 'fadeInId', 'fadeOutId');
    }
    
    /**
     *  Return record id, optionally insert new record
     *
     *  @param category string - qualified name of metadata category
     *  @param parid int - parent record id
     *  @param value string - value for inserted record
     *  @param predxml string - 'A' | 'T' (attribute or tag)
     *  @return int - metadata record id
     */
    function _getMidOrInsert($category, $parid, $value=NULL, $predxml='T')
    {
        $arr = $this->md->getMetadataEl($category, $parid);
        if(PEAR::isError($arr)){ return $arr; }
        $mid = NULL;
        if(isset($arr[0]['mid'])) $mid = $arr[0]['mid'];
        if(!is_null($mid)) return $mid;
        $mid = $this->md->insertMetadataEl($parid, $category, $value, $predxml);
        if(PEAR::isError($mid)){ return $mid; }
        return $mid;
    }
    
    /**
     *  Set value of metadata record, optionally insert new record
     *
     *  @param mid int - record id
     *  @param value string - value for inserted record
     *  @param parid int - parent record id
     *  @param category string - qualified name of metadata category
     *  @param predxml string - 'A' | 'T' (attribute or tag)
     *  @return boolean
     */
    function _setValueOrInsert($mid, $value, $parid, $category, $predxml='T')
    {
        if(is_null($mid)){
            $r = $this->md->insertMetadataEl(
                $parid, $category, $value, $predxml);
        }else{
            $r = $this->md->setMetadataEl($mid, $value);
        }
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /**
     *  Set playlist length - dcterm:extent
     *
     *  @param newPlLen string - new length in extent format
     *  @param parid int - playlist container record id
     *  @param metaParid int - metadata container record id
     *  @return boolean
     */
    function setPlaylistLength($newPlLen, $parid, $metaParid)
    {
        $mid = $this->_getMidOrInsert('playlength', $parid, $newPlLen, 'A');
        if(PEAR::isError($mid)){ return $mid; }
        $r = $this->_setValueOrInsert(
            $mid, $newPlLen, $parid,  'playlength', 'A');
        if(PEAR::isError($r)){ return $r; }
        $mid = $this->_getMidOrInsert('dcterms:extent', $metaParid, $newPlLen);
        if(PEAR::isError($mid)){ return $mid; }
        $r = $this->_setValueOrInsert(
            $mid, $newPlLen, $metaParid,  'dcterms:extent');
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /**
     *  Add audioClip specified by local id to the playlist
     *
     *  @param acId string, local ID of added file
     *  @param fadeIn string, optional, in time format hh:mm:ss.ssssss
     *  @param fadeOut string, dtto
     *  @param plElGunid string - optional playlist element gunid
     *  @return string, generated playlistElement gunid
     */
    function addAudioClip($acId, $fadeIn=NULL, $fadeOut=NULL, $plElGunid=NULL)
    {
        $plGunid = $this->gunid;
        // get information about audioClip
        $acInfo = $this->getAcInfo($acId);
        if(PEAR::isError($acInfo)){ return $acInfo; }
        extract($acInfo);   // 'acGunid', 'acLen', 'acTit', 'elType'
        // get information about playlist and containers
        $plInfo = $this->getPlInfo();
        if(PEAR::isError($plInfo)){ return $plInfo; }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'
    
        // insert new playlist element
        $offset = $plLen;
        $plElInfo = $this->insertPlaylistElement($parid, $offset,
            $acGunid, $acLen, $acTit, $fadeIn, $fadeOut, $plElGunid,
            $elType);
        if(PEAR::isError($plElInfo)){ return $plElInfo; }
        extract($plElInfo); // 'plElId', 'plElGunid', 'fadeInId', 'fadeOutId'

        /* commented - maybe useless (C++ part doesn't do it)
        // set access to audio clip:
        $r = $this->bsAccess(NULL, '', $acGunid, 'access');
        if(PEAR::isError($r)){ return $r; }
        $acToken = $r['token'];
        // insert token attribute:
        $r = $this->md->insertMetadataEl($acId, 'accessToken', $acToken, 'A');
        if(PEAR::isError($r)){ return $r; }
        */
        return $plElGunid;
    }


    /**
     *  Remove audioClip from playlist
     *
     *  @param plElGunid string, global id of deleted playlistElement
     *  @return boolean
     */
    function delAudioClip($plElGunid)
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlInfo();
        if(PEAR::isError($plInfo)){ return $plInfo; }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'
    
        // get array of playlist elements:
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
        if(PEAR::isError($plElArr)){ return $plElArr; }
        $found = FALSE;
        foreach($plElArr as $el){
            $plElGunidArr = $this->md->getMetadataEl('id', $el['mid']);
            if(PEAR::isError($plElGunidArr)){ return $plElGunidArr; }
            // select playlist element to remove
            if($plElGunidArr[0]['value'] == $plElGunid){
                $acArr = $this->md->getMetadataEl('audioClip', $el['mid']);
                if(PEAR::isError($acArr)){ return $acArr; }
                $storedAcMid = $acArr[0]['mid'];
                $acLenArr = $this->md->getMetadataEl('playlength', $storedAcMid);
                if(PEAR::isError($acLenArr)){ return $acLenArr; }
                $acLen = $acLenArr[0]['value'];
                /*
                $acTokArr = $this->md->getMetadataEl('accessToken', $storedAcMid);
                if(PEAR::isError($acTokArr)){ return $acTokArr; }
                $acToken = $acTokArr[0]['value'];
                */
                // remove playlist element:
                $r = $this->md->setMetadataEl($el['mid'], NULL);
                if(PEAR::isError($r)){ return $r; }
                /*
                // release audioClip:
                $r = $this->bsRelease($acToken, 'access');
                if(PEAR::isError($r)){ return $r; }
                */
                $found = TRUE;
            }
        }
        if(!$found){
            return PEAR::raiseError(
                "Playlist::delAudioClip: playlistElement not found".
                " ($plElGunid)"
            );
        }
        return TRUE;
    }
    
    /**
     *  Change fadeIn and fadeOut values for plaulist Element
     *
     *  @param plElGunid string - playlistElement gunid
     *  @param fadeIn string - new value in ss.ssssss or extent format
     *  @param fadeOut string - new value in ss.ssssss or extent format
     *  @return boolean
     */
    function changeFadeInfo($plElGunid, $fadeIn, $fadeOut)
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlInfo();
        if(PEAR::isError($plInfo)){ return $plInfo; }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'
    
        // get array of playlist elements:
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
        if(PEAR::isError($plElArr)){ return $plElArr; }
        $found = FALSE;
        foreach($plElArr as $el){
            $plElGunidArr = $this->md->getMetadataEl('id', $el['mid']);
            if(PEAR::isError($plElGunidArr)){ return $plElGunidArr; }
            // select playlist element:
            if($plElGunidArr[0]['value'] != $plElGunid){ continue; }
            // get fadeInfo:
            $fiMid = $this->_getMidOrInsert('fadeInfo', $el['mid']);
            if(PEAR::isError($fiMid)){ return $fiMid; }
            $fiGunid = StoredFile::_createGunid();
            $r = $this->_getMidOrInsert('id', $fiMid, $fiGunid, 'A');
            if(PEAR::isError($r)){ return $r; }
            $fadeInId = $this->_getMidOrInsert('fadeIn', $fiMid, $fadeIn, 'A');
            if(PEAR::isError($fadeInId)){ return $fadeInId; }
            $fadeOutId = $this->_getMidOrInsert('fadeOut', $fiMid, $fadeOut, 'A');
            if(PEAR::isError($fadeOutId)){ return $fadeOutId; }
            $r = $this->_setValueOrInsert(
                $fadeInId, $fadeIn, $fiMid, 'fadeIn');
            if(PEAR::isError($r)){ return $r; }
            $r = $this->_setValueOrInsert(
                $fadeOutId, $fadeOut, $fiMid, 'fadeOut');
            if(PEAR::isError($r)){ return $r; }
        }
        return TRUE;
    }
    
    /**
     *  Move audioClip to the new position in the playlist
     *
     *  @param plElGunid string - playlistElement gunid
     *  @param newPos int - new position in playlist
     *  @return
     */
    function moveAudioClip($plElGunid, $newPos)
    {
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        $els =& $arr['children'];
        foreach($els as $i=>$el){
            if($el['elementname'] != 'playlistElement'){
                $metadata = array_splice($els, $i, 1);
                continue;
            }
        }
        foreach($els as $i=>$el){
            if($el['attrs']['id'] == $plElGunid){
                $movedi = $i;
            }
            $r = $this->delAudioClip($el['attrs']['id']);
            if(PEAR::isError($r)){ return $r; }
        }
        if($newPos<1) $newPos = 1;
        if($newPos>count($els)) $newPos = count($els);
        $movedel = array_splice($els, $movedi, 1);
        array_splice($els, $newPos-1, 0, $movedel);
//        var_dump($els);
        foreach($els as $i=>$el){
            $plElGunid2 = $el['attrs']['id'];
            $fadeIn = NULL;
            $fadeOut = NULL;
            foreach($el['children'] as $j=>$af){
                if($af['elementname'] == 'audioClip'){
                    $acGunid = $af['attrs']['id'];
                }elseif($af['elementname'] == 'fadeInfo'){
                    $fadeIn = $af['attrs']['fadeIn'];
                    $fadeOut = $af['attrs']['fadeOut'];
                }else{
                }
            }
            $acId = $this->gb->_idFromGunid($acGunid);
            if(PEAR::isError($acId)){ return $acId; }
            if(is_null($acId)){
                return PEAR::raiseError(
                    "Playlist::moveAudioClip: null audioClip gunid"
                );
            }
            $r = $this->addAudioClip($acId, $fadeIn, $fadeOut, $plElGunid2);
            if(PEAR::isError($r)){ return $r; }
        }
        return TRUE;
    }
    
    /**
     *  Recalculate total length of playlist and  relativeOffset values
     *  of all playlistElements according to legth and fadeIn values.
     *  FadeOut values adjusted to next fadeIn.
     *
     *  @return boolean
     */
    function recalculateTimes()
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlInfo();
        if(PEAR::isError($plInfo)){ return $plInfo; }
        extract($plInfo);   // 'plLen', 'parid', 'metaParid'
        // get array of playlist elements:
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
        if(PEAR::isError($plElArr)){ return $plElArr; }
        $peArr = array();
        $len = 0; $nextOffset = $len; $prevFiMid = NULL; $lastLenS = NULL;
        foreach($plElArr as $el){
            $elId = $el['mid'];
            // get playlistElement gunid:
            $plElGunidArr = $this->md->getMetadataEl('id', $elId);
            if(PEAR::isError($plElGunidArr)){ return $plElGunidArr; }
            $plElGunid = $plElGunidArr[0]['value'];
            // get relativeOffset:
            $offArr = $this->md->getMetadataEl('relativeOffset', $elId);
            if(PEAR::isError($offArr)){ return $offArr; }
            $offsetId = $offArr[0]['mid'];
            $offset = $offArr[0]['value'];
            // get audioClip:
            $acArr = $this->md->getMetadataEl('audioClip', $elId);
            if(is_array($acArr) && is_null($acArr[0]))
                $acArr = $this->md->getMetadataEl('playlist', $elId);
            if(PEAR::isError($acArr)){ return $acArr; }
            $storedAcMid = $acArr[0]['mid'];
            // get playlength:
            $acLenArr = $this->md->getMetadataEl('playlength', $storedAcMid);
            if(PEAR::isError($acLenArr)){ return $acLenArr; }
            $acLen = $acLenArr[0]['value'];
            // get fadeInfo:
            $fiArr = $this->md->getMetadataEl('fadeInfo', $elId);
            if(PEAR::isError($fiArr)){ return $fiArr; }
            if(isset($fiArr[0]['mid'])){
                $fiMid = $fiArr[0]['mid'];
                $fadeInArr = $this->md->getMetadataEl('fadeIn', $fiMid);
                if(PEAR::isError($fadeInArr)){ return $fadeInArr; }
                $fadeIn = $fadeInArr[0]['value'];
                $fadeOutArr = $this->md->getMetadataEl('fadeOut', $fiMid);
                if(PEAR::isError($fadeOutArr)){ return $fadeOutArr; }
                $fadeOut = $fadeOutArr[0]['value'];
            }else{
                $fiMid = NULL;
                $fadeIn = '00:00:00.000000';
                $fadeOut = '00:00:00.000000';
            }
            $fadeInS = $this->_plTimeToSecs($fadeIn);
            if(!is_null($lastLenS)){
                if($lastLenS < $fadeInS){
                    return PEAR::raiseError(
                        "Playlist::recalculateTimes: fadeIn too big");
                }
            }
            // $peArr[] = array('id'=>$elId, 'gunid'=>$plElGunid, 'len'=>$acLen,
            //    'offset'=>$offset, 'offsetId'=>$offsetId,
            //    'fadeIn'=>$fadeIn, 'fadeOut'=>$fadeOut);
            // set relativeOffset:
            if($len>0) $len = $len - $fadeInS;
            $newOffset = $this->_secsToPlTime($len);
            $r = $this->_setValueOrInsert(
                $offsetId, $newOffset, $elId, 'relativeOffset');
            if(PEAR::isError($r)){ return $r; }
            // $fadeInS = $this->_plTimeToSecs($fadeIn);
            $acLenS = $this->_plTimeToSecs($acLen);
            $len = $len + $acLenS;
            if(!is_null($prevFiMid)){
                $foMid = $this->_getMidOrInsert(
                    'fadeOut', $prevFiMid, $fadeIn, 'A');
                if(PEAR::isError($foMid)){ return $foMid; }
                $r = $this->_setValueOrInsert(
                    $foMid, $fadeIn, $prevFiMid, 'fadeOut', 'A');
                if(PEAR::isError($r)){ return $r; }
            }
            $prevFiMid = $fiMid;
            $lastLenS = $acLenS;
        }
        $newPlLen = $this->_secsToPlTime($len);
        $r = $this->setPlaylistLength($newPlLen, $parid, $metaParid);
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    
    /**
     *  Find info about clip at specified offset in playlist.
     *
     *  @param offset string, current playtime (hh:mm:ss.ssssss)
     *  @param distance int, 0=current clip; 1=next clip ...
     *  @return array of matching clip info:
     *   <ul>
     *      <li>gunid string, global unique id of clip</li>
     *      <li>elapsed string, already played time of clip</li>
     *      <li>remaining string, remaining time of clip</li>
     *      <li>duration string, total playlength of clip </li>
     *   </ul>
     */
    function displayPlaylistClipAtOffset($offset, $distance=0)
    {
        $offsetS = $this->_plTimeToSecs($offset);
        $plGunid = $this->gunid;
        $arr = $this->md->genPhpArray();
        if(PEAR::isError($arr)){ return $arr; }
        $plArr = array('els'=>array());
        foreach($arr[children] as $i=>$plEl){
            switch($plEl['elementname']){
            case"playlistElement":
                $plInfo = array(
                    'acLen'   => '00:00:00.000000', 'acLenS'   => 0,
                    'fadeIn'  => '00:00:00.000000', 'fadeInS'  => 0,
                    'fadeOut' => '00:00:00.000000', 'fadeOutS' => 0,
                );
                $plInfo['elOffset']  = $pom = $plEl['attrs']['relativeOffset'];
                $plInfo['elOffsetS'] = $this->_plTimeToSecs($pom);
                foreach($plEl['children'] as $j=>$acFi){
                    switch($acFi['elementname']){
                    case"audioClip":
                        $plInfo['acLen'] = $pom = $acFi['attrs']['playlength'];
                        $plInfo['acLenS'] = $this->_plTimeToSecs($pom);
                        $plInfo['acGunid'] = $pom = $acFi['attrs']['id'];
                        break;
                    case"fadeInfo":
                        $plInfo['fadeIn'] = $pom = $acFi['attrs']['fadeIn'];
                        $plInfo['fadeInS'] = $this->_plTimeToSecs($pom);
                        $plInfo['fadeOut'] = $pom = $acFi['attrs']['fadeOut'];
                        $plInfo['fadeOutS'] = $this->_plTimeToSecs($pom);
                        break;
                    }
                }
                $plArr['els'][] = $plInfo;
                break;
            case"metadata":
                foreach($plEl[children] as $j=>$ch){
                    switch($ch['elementname']){
                    case"dcterms:extent":
                        $plArr['length'] = $pom = $ch['content'];
                        $plArr['lengthS'] = $this->_plTimeToSecs($pom);
                        break;
                    }
                }
                break;
            }
        }
        if(isset($arr['attrs']['playlength'])){
            $plArr['length'] = $pom = $arr['attrs']['playlength'];
            $plArr['lengthS'] = $this->_plTimeToSecs($pom);
        }
        
        $res  = array('gunid'=>NULL, 'elapsed'=>NULL,
            'remaining'=>NULL, 'duration'=>NULL);
        $dd = -1;
        foreach($plArr['els'] as $el){
            extract($el);
            if($offsetS > $elOffsetS &&
                $offsetS < ($elOffsetS + $acLenS) &&
                $dd<0
            ) $dd=0;
            if($dd == $distance){
                $playedS = $offsetS - $elOffsetS;
                if($playedS < 0) $playedS = 0;
                $remainS = $acLenS - $playedS;
                $res  = array('gunid'=>$acGunid,
                    'elapsed'   => $this->_secsToPlTime($playedS),
                    'remaining' => $this->_secsToPlTime($remainS),
                    'duration'  => $this->_secsToPlTime($acLenS),
                );
                return $res;
            }
            if($dd >= 0) $dd++;
        }
        return $res;
    }
    
    /**
     *  Convert playlist time value to float seconds
     *
     *  @param plt string, playlist time value (HH:mm:ss.dddddd)
     *  @return int, seconds
     */
    function _plTimeToSecs($plt)
    {
        $arr = split(':', $plt);
        if(isset($arr[2])){
          return (intval($arr[0])*60 + intval($arr[1]))*60 + floatval($arr[2]);
        }
        if(isset($arr[1])){ return intval($arr[0])*60 + floatval($arr[1]); }
        return floatval($arr[0]);
    }

    /**
     *  Convert float seconds value to playlist time format
     *
     *  @param s0 int, seconds
     *  @return string, time in playlist time format (HH:mm:ss.dddddd)
     */
    function _secsToPlTime($s0)
    {
        $m  = intval($s0 / 60);
        $r0 = $s0 - $m*60;
        $h  = $m  / 60;
        $m  = $m  % 60;
        $s  = intval($r0);
        $r  = $r0 - $s;
        // $res = sprintf("%02d:%02d:%09.6f", $h, $m, $r);
        $res = sprintf("%02d:%02d:%02d", $h, $m, $s);
        $res .= str_replace('0.', '.', number_format($r, 6, '.', ''));
        return $res;
    }

    /**
     *  Cyclic-recursion checking
     *
     *  @param insGunid string, gunid of playlist beeing inserted
     *  @return boolean - true if recursion is detected
     */
    function _cyclicRecursion($insGunid)
    {
        if($this->gunid == $insGunid) return TRUE;
        $pl =& Playlist::recallByGunid($this->gb, $insGunid);
        if(PEAR::isError($pl)){ return $pl; }
        $arr = $pl->md->genPhpArray();
        if(PEAR::isError($arr)){ return $arr; }
        $els =& $arr['children'];
        if(!is_array($els)) return FALSE;
        foreach($els as $i=>$plEl){
            if($plEl['elementname'] != "playlistElement") continue;
            foreach($plEl['children'] as $j=>$elCh){
                if($elCh['elementname'] != "playlist") continue;
                $nextGunid = $elCh['attrs']['id'];
                $res = $this->_cyclicRecursion($nextGunid);
                if($res) return TRUE;
            }
        }
        return FALSE;
    }

    /**
     *
     * /
    function ()
    {
    }

    */
}

?>
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
    Version  : $Revision: 1.3 $
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
     *  by access token.<br/>
     *
     *  @param gb reference to GreenBox object
     *  @param token string, access token
     *  @param className string, optional classname to recall
     *  @return instace of StoredFile object
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
        return compact('acGunid', 'acLen', 'acTit');
    }

    /**
     *  Get info about playlist
     *
     *  @return array with fields:
     *  <ul>
     *   <li>plLen string - length of playlist in dcterms:extent format</li>
     *   <li>plLenMid int - metadata record id of dcterms:extent record</li>
     *   <li>parid int - metadata record id of playlist container</li>
     *   <li>metaParid int - metadata record id of metadata container</li>
     *  </ul>
     */
    function getPlInfo()
    {
        // get playlist length and record id:
        $r = $this->md->getMetadataEl('dcterms:extent');
        if(PEAR::isError($r)){ return $r; }
        if(isset($r[0])){
            $plLen = $r[0]['value'];
            $plLenMid = $r[0]['mid'];
        }else{
            $plLen = '00:00:00.000000';
            $plLenMid = NULL;
        }
        // get main playlist container
        $parid = $this->getContainer('playlist');
        if(PEAR::isError($parid)){ return $parid; }
        // get metadata container (optionally insert it)
        $metaParid = $this->getContainer('metadata', $parid, TRUE);
        if(PEAR::isError($metaParid)){ return $metaParid; }
        return compact('plLen', 'plLenMid', 'parid', 'metaParid');
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
     *  @param fadeIn string - fadein value in ss.ssssss or extent format
     *  @param fadeOut string - fadeout value in ss.ssssss or extent format
     *  @return array with fields:
     *  <ul>
     *   <li>plElId int - record id of playlistElement</li>
     *   <li>plElGunid string - gl.unique id of playlistElement</li>
     *   <li>fadeInId int - record id</li>
     *   <li>fadeOutId int - record id</li>
     *  </ul>
     */
    function insertPlaylistElement($parid, $offset,
        $acGunid, $acLen, $acTit, $fadeIn=NULL, $fadeOut=NULL)
    {
        // insert playlistElement
        $r = $this->md->insertMetadataEl($parid, 'playlistElement');
        if(PEAR::isError($r)){ return $r; }
        $plElId = $r;
        // create and insert gunid (id attribute)
        $plElGunid = StoredFile::_createGunid();
        $r = $this->md->insertMetadataEl($plElId, 'id', $plElGunid, 'A');
        if(PEAR::isError($r)){ return $r; }
        // insert relativeOffset
        $r = $this->md->insertMetadataEl(
            $plElId, 'relativeOffset', $offset, 'A');
        if(PEAR::isError($r)){ return $r; }
        // insert audioClip element into playlistElement
        $r = $this->md->insertMetadataEl($plElId, 'audioClip');
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
     *  @return boolean
     */
    function _setValueOrInsert($mid, $value, $parid, $category)
    {
        if(is_null($mid)){
            $r = $this->md->insertMetadataEl($parid, $category, $value);
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
     *  @param plLenMid int - playlist length record id
     *  @param metaParid int - metadata container record id
     *  @return boolean
     */
    function setPlaylistLength($newPlLen, $plLenMid, $metaParid)
    {
        $r = $this->_setValueOrInsert(
            $plLenMid, $newPlLen, $metaParid,  'dcterms:extent');
        if(PEAR::isError($r)){ return $r; }
        return $r;
    }
    
    /**
     *  Add audioclip specified by local id to the playlist
     *
     *  @param acId string, local ID of added file
     *  @param fadeIn string, optional, in time format hh:mm:ss.ssssss
     *  @param fadeOut string, dtto
     *  @return string, generated playlistElement gunid
     */
    function addAudioClip($acId, $fadeIn=NULL, $fadeOut=NULL)
    {
        $plGunid = $this->gunid;
        // get information about audioClip
        $acInfo = $this->getAcInfo($acId);
        if(PEAR::isError($acInfo)){ return $acInfo; }
        extract($acInfo);   // 'acGunid', 'acLen', 'acTit'
        // get information about playlist and containers
        $plInfo = $this->getPlInfo();
        if(PEAR::isError($plInfo)){ return $plInfo; }
        extract($plInfo);   // 'plLen', 'plLenMid', 'parid', 'metaParid'
    
        // insert new playlist element
        $offset = $plLen;
        $plElInfo = $this->insertPlaylistElement($parid, $offset,
            $acGunid, $acLen, $acTit, $fadeIn, $fadeOut);
        if(PEAR::isError($plElInfo)){ return $plElnfo; }
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
        // recalculate offsets and total length:
        $r = $this->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
        return $plElGunid;
    }


    /**
     *  Remove audioclip from playlist
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
        extract($plInfo);   // 'plLen', 'plLenMid', 'parid', 'metaParid'
    
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
        // recalculate offsets and total length:
        $r = $this->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
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
        extract($plInfo);   // 'plLen', 'plLenMid', 'parid', 'metaParid'
    
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
            $fadeInId = $this->_getMidOrInsert('fadeIn', $fiMid, NULL, 'A');
            if(PEAR::isError($fadeInId)){ return $fadeInId; }
            $fadeOutId = $this->_getMidOrInsert('fadeOut', $fiMid, NULL, 'A');
            if(PEAR::isError($fadeOutId)){ return $fadeOutId; }
            $r = $this->_setValueOrInsert(
                $fadeInId, $fadeIn, $fiMid, 'fadeIn');
            if(PEAR::isError($r)){ return $r; }
            $r = $this->_setValueOrInsert(
                $fadeOutId, $fadeOut, $fiMid, 'fadeOut');
            if(PEAR::isError($r)){ return $r; }
        }
        $r = $this->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /**
     *  Recalculate total length of playlist and  relativeOffset values
     *  of all playlistElements according to legth and fadeIn values
     *
     *  @return boolean
     */
    function recalculateTimes()
    {
        $plGunid = $this->gunid;
        // get information about playlist and containers
        $plInfo = $this->getPlInfo();
        if(PEAR::isError($plInfo)){ return $plInfo; }
        extract($plInfo);   // 'plLen', 'plLenMid', 'parid', 'metaParid'
        // get array of playlist elements:
        $plElArr = $this->md->getMetadataEl('playlistElement', $parid);
        if(PEAR::isError($plElArr)){ return $plElArr; }
        $peArr = array();
        $len = 0; $nextOffset = $len;
        foreach($plElArr as $el){
            $elId = $el['mid'];
            // get playlistElement gunid:
            $plElGunidArr = $this->md->getMetadataEl('id', $elId);
            if(PEAR::isError($plElGunidArr)){ return $plElGunidArr; }
            $plElGunid = $plElGunidArr[0]['value'];
            // get relativeoffset:
            $offArr = $this->md->getMetadataEl('relativeoffset', $elId);
            if(PEAR::isError($offArr)){ return $offArr; }
            $offsetId = $offArr[0]['mid'];
            $offset = $offArr[0]['value'];
            // get audioClip:
            $acArr = $this->md->getMetadataEl('audioClip', $elId);
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
                $fadeIn = '00:00:00.000000';
                $fadeOut = '00:00:00.000000';
            }
            // $peArr[] = array('id'=>$elId, 'gunid'=>$plElGunid, 'len'=>$acLen,
            //    'offset'=>$offset, 'offsetId'=>$offsetId,
            //    'fadeIn'=>$fadeIn, 'fadeOut'=>$fadeOut);
            // set relativeOffset:
            $fadeInS = $this->_plTimeToSecs($fadeIn);
            if($len>0) $len = $len - $fadeInS;
            $newOffset = $this->_secsToPlTime($len);
            $r = $this->_setValueOrInsert(
                $offsetId, $newOffset, $elId, 'relativeOffset');
            if(PEAR::isError($r)){ return $r; }
            // $fadeInS = $this->_plTimeToSecs($fadeIn);
            $acLenS = $this->_plTimeToSecs($acLen);
            $len = $len + $acLenS;
        }
        $newPlLen = $this->_secsToPlTime($len);
        $r = $this->setPlaylistLength($newPlLen, $plLenMid, $metaParid);
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
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
        if(isset($arr[2])){ return ($arr[0]*60 + $arr[1])*60 + $arr[2]; }
        if(isset($arr[1])){ return $arr[0]*60 + $arr[1]; }
        return $arr[0];
    }

    /**
     *  Convert float seconds value to playlist time format
     *
     *  @param s0 int, seconds
     *  @return string, time in playlist time format (HH:mm:ss.dddddd)
     */
    function _secsToPlTime($s0)
    {
        $m = intval($s0 / 60);
        $r = $s0 - $m*60;
        $h = $m  / 60;
        $m = $m  % 60;
        return sprintf("%02d:%02d:%09.6f", $h, $m, $r);
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
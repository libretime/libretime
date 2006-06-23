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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
require_once dirname(__FILE__)."/../../storageServer/var/xmlrpc/XR_LocStor.php";
require_once dirname(__FILE__)."/../../storageServer/var/Transport.php";

/**
 *  Extension to StorageServer to act as ArchiveServer
 */
class Archive extends XR_LocStor{

    /**
     *  Open upload transport (from station to hub)
     *
     *  @param sessid: string - session id
     *  @param chsum: string - checksum
     *  @return hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function uploadOpen($sessid, $chsum)
    {
        $owner = $r = $this->getSessUserId($sessid);
        if($this->dbc->isError($r)) return $r;
        $res = $r = $this->bsOpenPut($chsum, NULL, $owner);
        if($this->dbc->isError($r)) return $r;
        return array('url'=>$res['url'], 'token'=>$res['token']);
    }

    /**
     *  Check uploaded file
     *
     *  @param token: string - transport token
     *  @return array(md5h string, size int, url string)
     */
    function uploadCheck($token)
    {
        return $this->bsCheckPut($token);
    }

    /**
     *  Close upload transport
     *
     *  @param token: string - transport token
     *  @param trtype: string - transport type
     *  @param pars: array - transport parameters
     *  @return mixed
     */
    function uploadClose($token, $trtype, $pars=array())
    {
        $res = $r = $this->bsClosePut($token);
        if($this->dbc->isError($r)) return $r;
        extract($res);  // fname, owner
        switch($trtype){
            case"audioclip":
                $mdtoken = $pars['mdpdtoken'];
                $res = $r = $this->bsClosePut($mdtoken);
                if($this->dbc->isError($r)) return $r;
                $mdfname = $res['fname'];
                if($gunid=='') $gunid=NULL;
                $parid = $r = $this->_getHomeDirId($owner);
                if($this->dbc->isError($r)) return $r;
                $res = $r = $this->bsPutFile($parid, $pars['name'],
                    $fname, $mdfname,
                    $pars['gunid'], 'audioclip', 'file');
                if($this->dbc->isError($r)) return $r;
                @unlink($fname);   @unlink($mdfname);
                break;
            case"playlist":
                if($gunid=='') $gunid=NULL;
                $parid = $r = $this->_getHomeDirId($owner);
                if($this->dbc->isError($r)) return $r;
                $res = $r = $this->bsPutFile($parid, $pars['name'],
                    '', $fname,
                    $pars['gunid'], 'playlist', 'file');
                if($this->dbc->isError($r)) return $r;
                @unlink($fname);
                break;
            case"playlistPkg":
                $chsum = md5_file($fname);
                // importPlaylistOpen:
                $res = $r = $this->bsOpenPut($chsum, NULL, $owner);
                if($this->dbc->isError($r)) return $r;
                $dest = $res['fname'];
                $token = $res['token'];
                copy($fname, $dest);
                $r = $this->importPlaylistClose($token);
                if($this->dbc->isError($r)) return $r;
                @unlink($fname);
                return $r;
                break;
            case"searchjob":
                $crits = file_get_contents($fname);
                $criteria = unserialize($crits);
                @unlink($fname);
                $results = $r =$this->localSearch($criteria);
                if($this->dbc->isError($r)) return $r;
                $realfile = tempnam($this->accessDir, 'searchjob_');
                @chmod($realfile, 0660);
                $len = $r = file_put_contents($realfile, serialize($results));
                $acc = $r = $this->bsAccess($realfile, '', NULL, 'download');
                if($this->dbc->isError($r)) return $r;
                $url = $this->getUrlPart()."access/".basename($acc['fname']);
                $chsum = md5_file($realfile);
                $size = filesize($realfile); 
                $res = array(
                    'url'=>$url, 'token'=>$acc['token'],
                    'chsum'=>$chsum, 'size'=>$size,
                    'filename'=>$filename
                );
                return $res;
                break;
            case"metadata":
                break;
            default:
        }
        return $res;
    }

    /**
     *  Open download transport
     *
     *  @param sessid: string - session id
     *  @param trtype: string - transport type
     *  @param pars: array - transport parameters
     *  @return hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function downloadOpen($sessid, $trtype, $pars=array())
    {
        switch($trtype){
            case"unknown":
            case"audioclip":
            case"metadata":
            case"playlist":
            case"playlistPkg":
                if(!isset($pars['gunid']))
                    return PEAR::raiseError("Archive::downloadOpen: gunid not set");
                break;
        }
        $gunid = $pars['gunid'];
        // resolve trtype by object type:
        if($trtype == 'unknown' || $trtype == 'playlistPkg'){
            $trtype2 = $r = $this->_getType($gunid);
            if($this->dbc->isError($r)) return $r;
            // required with content:
            $trtype = ($trtype2 == 'playlist' && $trtype == 'playlistPkg'?
                'playlistPkg' : $trtype2);
#    return PEAR::raiseError("Archive::downloadOpen: TT=$trtype TT2=$trtype2 G=$gunid");
        }
        switch($trtype){
            case"audioclip":
                $res = $r = $this->downloadRawAudioDataOpen($sessid, $gunid);
                break;
            case"metadata":
                $res = $r = $this->downloadMetadataOpen($sessid, $gunid);
                break;
            case"playlist":
                $res = $r = $this->accessPlaylist($sessid, $gunid);
                break;
            case"playlistPkg":
                $res = $r = $this->bsExportPlaylistOpen($gunid);
                if($this->dbc->isError($r)) return $r;
                $tmpn = tempnam($this->transDir, 'plExport_');
                $plfpath = "$tmpn.lspl";
                copy($res['fname'], $plfpath);
                $res = $r = $this->bsExportPlaylistClose($res['token']);
                if(PEAR::isError($r)) return $r;
                $fname = "transported_playlist.lspl";
                $id = $this->_idFromGunid($gunid);
                $acc = $this->bsAccess($plfpath, 'lspl', NULL, 'download');
                if($this->dbc->isError($acc)){ return $acc; }
                $url = $this->getUrlPart()."access/".basename($acc['fname']);
                $chsum = md5_file($plfpath);
                $size = filesize($plfpath);
                $res = array(
                    'url'=>$url, 'token'=>$acc['token'],
                    'chsum'=>$chsum, 'size'=>$size,
                    'filename'=>$fname
                );
                break;
            case"searchjob":
                $res = $pars;
                break;
            case"file":
                $res = $r = array();
                break;
            default:
                return PEAR::raiseError("Archive::downloadOpen: NotImpl ($trtype)");
        }
        if($this->dbc->isError($r)) return $r;
        switch($trtype){
            case"audioclip":
            case"metadata":
            case"playlist":
            case"playlistPkg":
                $title = $r = $this->bsGetTitle(NULL, $gunid);
                break;
            case"searchjob":    $title = 'searchjob';       break;
            case"file":         $title = 'regular file';    break;
            default:
        }
        $res['title'] = $title;
        $res['trtype'] = $trtype;
        return $res;
    }

    /**
     *  Close download transport
     *
     *  @param token: string - transport token
     *  @param trtype: string - transport type
     *  @return hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function downloadClose($token, $trtype)
    {
        switch($trtype){
            case"audioclip":
                $res = $r = $this->downloadRawAudioDataClose($token);
                if($this->dbc->isError($r)) return $r;
                return $res;
                break;
            case"metadata":
                $res = $r = $this->downloadMetadataClose($token);
                if($this->dbc->isError($r)) return $r;
                return $res;
                break;
            case"playlist":
                $res = $r = $this->releasePlaylist(NULL/*$sessid*/, $token);
                if($this->dbc->isError($r)) return $r;
                return $res;
                break;
            case"playlistPkg":
                $res = $r = $this->bsRelease($token, 'download');
                if($this->dbc->isError($r)) return $r;
                $realFname = $r['realFname'];
                @unlink($realFname);
                if(preg_match("|(plExport_[^\.]+)\.lspl$|", $realFname, $va)){
                    list(,$tmpn) = $va; $tmpn = "{$this->transDir}/$tmpn";
                    if(file_exists($tmpn)) @unlink($tmpn);
                }
                return $res;
                break;
            case"searchjob":
                $res = $r = $this->bsRelease($token, 'download');
                if($this->dbc->isError($r)) return $r;
                return $res;
                break;
            case"file":
                return array();
                break;
            default:
                return PEAR::raiseError("Archive::downloadClose: NotImpl ($trtype)");
        }
    }

    /**
     *  Prepare hub initiated transport
     *
     *  @param target: string - hostname of transport target
     *  @param trtype: string - transport type
     *  @param direction: string - 'up' | 'down'
     *  @param pars: array - transport parameters
     *  @return
     */
    function prepareHubInitiatedTransfer(
        $target, $trtype='file', $direction='up',$pars=array())
    {
        $tr =& new Transport($this);
        $trec = $r = TransportRecord::create($tr, $trtype, $direction,
            array_merge($pars, array('target'=>$target))
        );
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }

    /**
     *  List hub initiated transports
     *
     *  @param target: string - hostname of transport target
     *  @param direction: string - 'up' | 'down'
     *  @param trtok: string - transport token
     *  @return
     */
    function listHubInitiatedTransfers(
        $target=NULL, $direction=NULL, $trtok=NULL)
    {
        $tr =& new Transport($this);
        $res = $r = $tr->getTransports($direction, $target, $trtok);
        if(PEAR::isError($r)){ return $r; }
        return $res;
    }

    /**
     *  Set state of hub initiated transport
     *
     *  @param target: string - hostname of transport target
     *  @param trtok: string - transport token
     *  @param state: string - transport state
     *  @return
     */
    function setHubInitiatedTransfer($target, $trtok, $state)
    {
        $tr =& new Transport($this);
        $trec = $r = TransportRecord::recall($tr, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $r = $trec->setState($state);
        if(PEAR::isError($r)){ return $r; }
        return $res;
    }

    /* ==================================================== auxiliary methods */

}
?>
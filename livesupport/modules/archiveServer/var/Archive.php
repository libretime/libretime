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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/Archive.php,v $

------------------------------------------------------------------------------*/
require_once "../../../storageServer/var/LocStor.php";

$config['archiveUrlPath'] = '/livesupport/modules/archiveServer/var';
$config['archiveXMLRPC'] = 'xmlrpc/xrArchive.php';
$config['archiveUrlHost'] = 'localhost';
$config['archiveUrlPort'] = 80;
$config['archiveAccountLogin'] = 'root';
$config['archiveAccountPass']  = 'q';

/**
 *  Extension to StorageServer to act as ArchiveServer
 */
class Archive extends LocStor{

    /**
     *  Open download 
     */
    function downloadOpen($sessid, $gunid)
    {
        // access
        $lnk = $this->accessRawAudioData($sessid, $gunid);
        if(PEAR::isError($lnk)) return $lnk;
        // return tmpurl, fname, md5h
    	$url = $this->_lnk2url($lnk);
        $md5h = $this->_md5sum($lnk);
        return array('url'=>$url, 'md5h'=>$md5h, 'fname'=>basename($lnk));
    }


    /**
     *  Close download
     */
    function downloadClose($sessid, $url)
    {
    	// release
    	$lnk = $this->_url2lnk($url);
    	$res = $this->releaseRawAudioData($sessid, $lnk);
        return $res;
    }


    /**
     *  Open upload
     */
    function uploadOpen($sessid, $gunid)
    {
        $fname = "{$this->storageDir}/buffer/$gunid";
        if(!$fp = fopen($fname, 'w')) return PEAR::raiseError(
            "Archive::uploadOpen: unable to create blank file"
        );
        fclose($fp);
        $res = $this->storeAudioClip($sessid, $gunid, $fname, '');
        if(PEAR::isError($res)) return $res;
        $lnk = $this->accessRawAudioData($sessid, $gunid);
        if(PEAR::isError($lnk)) return $lnk;
    	$url = $this->_lnk2url($lnk);
        return array('url'=>$url);
    }


    /**
     *  Abort upload
     */
    function uploadAbort($sessid, $url)
    {
    	$lnk = $this->_url2lnk($url);
    	$res = $this->releaseRawAudioData($sessid, $lnk);
    	return $res;
    }


    /**
     *  Check upload
     */
    function uploadCheck($sessid, $url)
    {
    	$lnk = $this->_url2lnk($url);
        $md5h = $this->_md5sum($lnk);
        $size = filesize($lnk);
        return array('md5h'=>$md5h, 'size'=>$size, 'url'=>$url);
    }


    /**
     *  Close upload
     */
    function uploadClose($sessid, $url, $type='file')
    {
        switch($type){
            default: // case"file":
                	// release
                	$lnk = $this->_url2lnk($url);
                	$res = $this->releaseRawAudioData($sessid, $lnk);
                    return $res;
                break;
            case"search":
                    // localSearch
                    // return results
                break;
        }
    }

    /**
     *  Translate local symlink to URL
     *
     */
    function _lnk2url($lnk)
    {
        return "http://{$this->config['archiveUrlHost']}:{$this->config['archiveUrlPort']}".
            "{$this->config['archiveUrlPath']}/access/".basename($lnk);
    }

    /**
     *  Traslate URL to local symlink
     *
     */
    function _url2lnk($url)
    {
        return $this->accessDir."/".basename($url);
    }
    function _md5sum($fpath)
    {
        $md5h = `md5sum $fpath`;
        $arr = split(' ', $md5h);
        return $arr[0];
    }
}
?>
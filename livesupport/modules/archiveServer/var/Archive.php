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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/Archive.php,v $

------------------------------------------------------------------------------*/
require_once "../../../storageServer/var/LocStor.php";

/**
 *  Extension to StorageServer to act as ArchiveServer
 */
class Archive extends LocStor{

    /**
     *  Constructor
     */
    function Archive(&$dbc, $config)
    {
        parent::LocStor(&$dbc, $config);
        $this->transDir  = $config['transDir'];
    }
    
    /* ======================================================= upload methods */
    
    /**
     *  Open file upload
     *
     *  @param sessid string - session id
     *  @param trid string - transport id
     *  @param type string - media|metadata|search
     *  @return array(url string) or error
     */
    function uploadOpen($sessid, $trid, $type)
    {
        $file = "{$this->transDir}/$trid";
        if(!$fp = fopen($file, 'w')) return PEAR::raiseError(
            "Archive::uploadOpen: unable to create blank file"
        );
        fclose($fp);
        $host = $this->config['archiveUrlHost'];
        $port = $this->config['archiveUrlPort'];
        $path = $this->config['archiveUrlPath'];
        $url = "http://$host:$port$path/trans/".basename($file);
        return array('url'=>$url);
    }

    /**
     *  Check uploaded file
     *
     *  @param sessid string - session id
     *  @param url string
     *  @return array(md5h string, size int, url string)
     */
    function uploadCheck($sessid, $url)
    {
        $file = "{$this->transDir}/".basename($url);
        $md5h = $this->_md5sum($file);
        $size = filesize($file);
        return array('md5h'=>$md5h, 'size'=>$size, 'url'=>$url);
    }


    /**
     *  Close file upload
     *
     *  @param sessid string - session id
     *  @param url string
     *  @param type string - media|metadata|search
     *  @param gunid string - global unique id
     *  @return boolean or error
     */
    function uploadClose($sessid, $url, $type, $gunid)
    {
        $file = "{$this->transDir}/".basename($url);
        $res = $this->processUploaded($sessid, $file, $type, $gunid);
        return $res;
    }

    /**
     *  Process uploaded file - insert to the storage
     *
     *  @param sessid string - session id
     *  @param file string - absolute local pathname
     *  @param type string - media|metadata|search
     *  @param gunid string - global unique id
     *  @return boolean or error
     */
    function processUploaded($sessid, $file, $type, $gunid='X')
    {
        switch($type){
            case 'media':
                if(!file_exists($file)) break;
                $res = $this->storeAudioClip($sessid, $gunid,
                    $file, '');
                if(PEAR::isError($res)) return $res;
                @unlink($file);
                break;
            case 'metadata':
            case 'mdata':
                if(!file_exists($file)) break;
                $res = $this->updateAudioClipMetadata($sessid, $gunid,
                    $file);
                if(PEAR::isError($res)){
                    // catch valid exception
                    if($res->getCode() == GBERR_FOBJNEX){
                        $res2 = $this->storeAudioClip($sessid, $gunid,
                            '', $file);
                        if(PEAR::isError($res2)) return $res2;
                    }else return $res;
                }
                @unlink($file);
                break;
            case 'search':
                return PEAR::raiseError("Archive::processUploaded: search not implemented");
                /*
                rename($file, $file."_");
                $criteria = unserialize(file_get_contents($file_));
                $res = $this->searchMetadata($sessid, $criteria);
                $fh = fopen($file, "w");
                fwrite($fh, serialize($res));
                fclose($fh); 
                @unlink($file."_");
                */
                break;
            default:
                return PEAR::raiseError("Archive::processUploaded: unknown type ($type)");
                break;
        }
        return TRUE;
    }

    /* ===================================================== download methods */
    /**
     *  Open file download 
     *
     *  @param sessid string - session id
     *  @param type string media|metadata|search
     *  @param par string - depends on type
     */
    function downloadOpen($sessid, $type, $par)
    {
        switch($type){
            case 'media':
            case 'metadata':
                $gunid = $par;
                $res = $this->prepareForTransport('', $gunid, $sessid);
                if(PEAR::isError($res)) return $res;  
                list($mediaFile, $mdataFile, $gunid) = $res;
            default:
        }
        switch($type){
            case 'media':
                $fname = $mediaFile;
                break;
            case 'metadata':
                $fname = $mdataFile;
                break;
            default:
        }
        $file = "{$this->transDir}/$fname";
        $host = $this->config['archiveUrlHost'];
        $port = $this->config['archiveUrlPort'];
        $path = $this->config['archiveUrlPath'];
        $url = "http://$host:$port$path/trans/$fname";
        $md5h = $this->_md5sum($file);
        return array('url'=>$url, 'md5h'=>$md5h, 'fname'=>$fname);
    }


    /**
     *  Close file download
     *
     *  @param sessid string - session id
     *  @param url string
     *  @return boolean
     */
    function downloadClose($sessid, $url)
    {
        $file = "{$this->transDir}/".basename($url);
        @unlink($file);
        return TRUE;
    }

    /* ==================================================== auxiliary methods */

    /**
     *  Returns md5 hash of external file
     *
     *  @param fpath string - local path to file
     *  @return string
     */
    function _md5sum($fpath)
    {
        $md5h = `md5sum $fpath`;
        $arr = split(' ', $md5h);
        return $arr[0];
    }
}
?>
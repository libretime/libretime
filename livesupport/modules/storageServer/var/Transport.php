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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Transport.php,v $

------------------------------------------------------------------------------*/
define('TRERR_', 70);
define('TRERR_MD', 71);
define('TRERR_TOK', 72);

include_once "XML/RPC.php";

/**
 *  Class for handling file tranport between StorageServer and ArchiveServer<br>
 *  over unreliable network and from behind firewall<br><br>
 */
class Transport{
    var $dbc;
    var $timeout=20;
    var $waitretry=6;
    var $retries=6;

    /**
     *  Constructor
     *
     *  @param dbc PEAR DB object reference
     *  @param gb LocStor object reference
     *  @param config config array
     */
    function Transport(&$dbc, &$gb, $config)
    {
        $this->dbc        =& $dbc;
        $this->config     = $config;
        $this->transTable = $config['tblNamePrefix'].'trans';
        $this->transDir   = $config['transDir'];
        $this->gb         =& $gb;
    }

    /* ================================================== status info methods */
    /**
     *  Return state of transport job
     *
     *  @param trtok string, transport token
     *  @return string, transport state
     */
    function getState($trtok)
    {
        $row = $this->dbc->getRow(
            "SELECT state FROM {$this->transTable} WHERE trtok='$trtok'"
        );
        if(PEAR::isError($row)) return $row;
        if(is_null($row)){
            return PEAR::raiseError("Transport::getState:".
                " invalid transport token ($trtok)", TRERR_TOK
            );
        }
        return $row['state'];
    }

    /**
     *  Return hash with useful information about transport
     *
     *  @param trtok string, transport token
     *  @return hash:
     *     trtype,
     *     direction,
     *     status,
     *     expectedsize,
     *     realsize,
     *     expectedsum,
     *     realsum
     */
    function getTransportInfo($trtok)
    {
        $row = $this->dbc->getRow("
            SELECT 
                trtype, direction, state,
                expectedsize, realsize,
                expectedsum, realsum
            FROM {$this->transTable}
            WHERE trtok='$trtok'
        ");
        if(PEAR::isError($row)){ return $row; }
        if(is_null($row)){
            return PEAR::raiseError("Transport::getTransportInfo:".
                " invalid transport token ($trtok)", TRERR_TOK
            );
        }
        $row['status'] == ($row['state'] == 'closed');
#        unset($row['state']);
        return $row;
    }

    /* ======================================================= upload methods */
    
    /**
     *  
     */
    function uploadToArchive($gunid, $sessid='')
    {
        $trtype = $this->gb->_getType($gunid);
        switch($trtype){
        case"audioclip":
            $ex = $this->gb->existsAudioClip($sessid, $gunid);
            break;
        case"playlist":
            $ex = $this->gb->existsPlaylist($sessid, $gunid);
            break;
        default:
            return PEAR::raiseError("Transport::uploadToArchive:".
                " unknown trtype ($trtype)"
            );
        }
        if(PEAR::isError($ex)) return $ex;
        if(!$ex){
            return PEAR::raiseError("Transport::uploadToArchive:".
                " $trtype not found ($gunid)"
            );
        }
        $trtok = $this->_createTrtok();
        $id = $this->dbc->nextId("{$this->transTable}_id_seq");
        $res = $this->dbc->query("
            INSERT INTO {$this->transTable}
                (id, trtok, direction, state, trtype,
                    gunid,
                    expectedsum, realsum, expectedsize, realsize
                )
            VALUES
                ($id, '$trtok', 'up', 'init', '$trtype',
                    x'$gunid'::bigint,
                    '?e', '?r', '0', '0'
                )
        ");
        if(PEAR::isError($res)) return $res;
#??        $this->uploadCron();
        return $trtok;
    }
    
    /**
     *  
     */
    function uploadCron()
    {
        // fetch all opened uploads
        $rows = $this->dbc->getAll("
            SELECT
                id, trtok, state, trtype,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, expectedsum, expectedsize, url
            FROM {$this->transTable}
            WHERE direction='up' AND state<>'closed' 
        ");
        if(PEAR::isError($rows)){ $this->trLogPear($rows); return FALSE; }
        if(count($rows)==0) return TRUE;
        // login to archive server
        $r = $this->loginToArchive();
        if(PEAR::isError($r)){ $this->trLog("Login error"); return FALSE; }
        $asessid = $r['sessid'];
        chdir($this->config['transDir']);
        // for all opened uploads:
        foreach($rows as $i=>$row){
            $row['pdtoken'] = StoredFile::_normalizeGunid($row['pdtoken']);
            $row['gunid'] = StoredFile::_normalizeGunid($row['gunid']);
            #var_dump($row);
            switch($row['state']){
                case"init":     // ------ new uploads
                    $ret = $this->uploadCronInit($row, $asessid);
                    if(PEAR::isError($ret)){
                        $this->trLogPear($ret, $row); break;
                    }
                    $row = array_merge($row, $ret);
                    // break;
                case"pending":  // ------ pending uploads
                    $ret = $this->uploadCronPending($row, $asessid);
                    if(PEAR::isError($ret)){ $this->trLogPear($ret, $row); }
                    break;
                case"finished": // ------ finished uploads
                    $ret = $this->uploadCronFinished($row, $asessid);
                    if(PEAR::isError($ret)){ $this->trLogPear($ret, $row); }
                    break;
                case"failed":   // ------ failed uploads
                    $ret = $this->uploadCronFailed($row, $asessid);
                    if(PEAR::isError($ret)){ $this->trLogPear($ret, $row); }
                    break;
                default:
                    $this->trLog("uploadCron: unknown state ".
                        "({$row['state']}, id={$row['id']})");
            } // switch state
        } // foreach opened
        $r = $this->logoutFromArchive($asessid);
        if(PEAR::isError($r)) $this->trLog("Logout error");
        return TRUE;
    }

    /**
     *  
     */
    function uploadCronInit($row, $asessid)
    {
        $this->trLog("INIT UP id={$row['id']}, trtok={$row['trtok']}");
        switch($row['trtype']){
            case"audioclip":
                $ac =& StoredFile::recallByGunid(&$this->gb, $row['gunid']);
                if(PEAR::isError($ac)) return $ac;
                $fpath = $ac->_getRealRADFname();
                $fname = $ac->_getFileName();
                $chsum = $this->_chsum($fpath);
                $size  = filesize($fpath);
                $metadata = file_get_contents($ac->_getRealMDFname());
                $ret = $this->xmlrpcCall( 'archive.storeAudioClipOpen',
                    array(
                       'sessid'     => $asessid,
                       'gunid'      => $row['gunid'],
                       'metadata'   => $metadata,
                       'fname'      => $fname,
                       'chsum'      => $chsum,
                    )
                );
                if(PEAR::isError($ret)) return $ret;
                $r = $this->dbc->query("
                    UPDATE {$this->transTable}
                    SET
                        state       = 'pending',
                        fname       = '$fname',
                        localfile   = '$fpath',
                        expectedsum = '$chsum',
                        expectedsize= $size,
                        url         = '{$ret['url']}',
                        pdtoken     = x'{$ret['token']}'::bigint
                    WHERE id={$row['id']}
                ");
                if(PEAR::isError($r)) return $r;
                return array(
                    'state'=>'pending',
                    'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
                    'localfile'=>$fpath, 'fname'=>$fname,
                    'expectedsum'=>$chsum, 'expectedsize'=>$size,
                );
                break;
            case"playlist":
                $ac =& StoredFile::recallByGunid(&$this->gb, $row['gunid']);
                if(PEAR::isError($ac)) return $ac;
                $fname = $ac->_getFileName();
                $size  = filesize($fpath);
                $metadata = file_get_contents($ac->_getRealMDFname());
                $ret = $this->xmlrpcCall( 'archive.createPlaylist',
                    array(
                       'sessid'     => $asessid,
                       'plid'      => $row['gunid'],
                       'fname'      => $fname,
                    )
                );
                if(PEAR::isError($ret)) return $ret;
                $ret = $this->xmlrpcCall( 'archive.editPlaylist',
                    array(
                       'sessid'     => $asessid,
                       'plid'      => $row['gunid'],
                       'metadata'   => $metadata,
                    )
                );
                if(PEAR::isError($ret)) return $ret;
#                $this->trLog("INIT UP after edit {$ret['token']}");
                $r = $this->xmlrpcCall( 'archive.savePlaylist',
                    array(
                       'sessid'     => $asessid,
                       'token'      => $ret['token'],
                       'newPlaylist'   => $metadata,
                    )
                );
                if(PEAR::isError($r)) return $r;
#                $this->trLog("INIT UP after save {$r['plid']}");
                $r = $this->dbc->query("
                    UPDATE {$this->transTable}
                    SET
                        state       = 'closed',
                        fname       = '$fname',
                        url         = '{$ret['url']}'
                    WHERE id={$row['id']}
                ");
                if(PEAR::isError($r)) return $r;
                return array(
                    'state'=>'closed',
                    'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
                    'fname'=>$fname,
                );
                break;
            case"searchjob":
                return array();
            default:
                return PEAR::raiseError("Transport::uploadCronInit:".
                    " unknown trtype ({$row['trtype']})"
                );
        } // switch $row['trtype']
    }
    
    /**
     *  
     */
    function uploadCronPending($row, $asessid)
    {
        if($row['trtype'] != 'audioclip') return;
        $check = $this->uploadCheck($row['pdtoken']);
        if(PEAR::isError($check)) return $check;
        #var_dump($row);
        #var_dump($check);
        // test filesize
        if(intval($check['size']) < $row['expectedsize']){
            // not finished - upload next part
            $command =
                "curl -s -C {$check['size']} --max-time 600".
                " --speed-time 20 --speed-limit 500".
                " --connect-timeout 20".
                " -T {$row['localfile']} {$row['url']}";
#            echo "$command\n";
            $res = system($command, $status);
        }else{
            // hmmm - we are finished? OK - continue
            $status = 0;
        }
        // status 18 - Partial file. Only a part of the file was transported.
        if($status == 0 || $status == 18){
            $check = $this->uploadCheck($row['pdtoken']);
            if(PEAR::isError($check)) return $check;
            #var_dump($check);
            // test checksum
            if($check['status'] == TRUE){
                // finished
                $res = $this->dbc->query("
                    UPDATE {$this->transTable}
                    SET state   ='finished',
                        realsum ='{$check['realsum']}',
                        realsize={$check['size']}
                    WHERE id='{$row['id']}'
                ");
                if(PEAR::isError($res)) return $res;
            }else{
                if(intval($check['size']) < $row['expectedsize']){
                    $res = $this->dbc->query("
                        UPDATE {$this->transTable}
                        SET
                            realsum ='{$check['realsum']}',
                            realsize={$check['realsize']}
                        WHERE id='{$row['id']}'
                    ");
                    if(PEAR::isError($res)) return $res;
                }else{
                    // wrong md5 at finish - TODO: start again
                    // $this->xmlrpcCall('archive.uploadReset', array());
                    return PEAR::raiseError("Transport::uploadCron:".
                        " file uploaded with bad md5 ".
                        "({$check['realsum']}/{$check['expectedsum']})"
                    );
                }
            }
        }
    }
    
    /**
     *  
     */
    function uploadCronFinished($row, $asessid)
    {
        if($row['trtype'] != 'audioclip') return;
        $res = $this->xmlrpcCall(
            'archive.storeAudioClipClose',
            array('sessid'=>$asessid, 'token'=>$row['pdtoken'])
        );
//        if(PEAR::isError($res)) return $res;
        if(PEAR::isError($res)){
            switch($res->getCode()){
                case GBERR_PUT:
                    // mark as failed
                    $this->dbc->query("
                        UPDATE {$this->transTable} SET state='failed'
                        WHERE id='{$row['id']}'
                    ");
                    break;
                    return FALSE;
                default:
                    return $res;
            }
        }
        // close upload in db
        $r = $this->dbc->query("
            UPDATE {$this->transTable} SET state='closed'
            WHERE id='{$row['id']}'
        ");
        /*
        $r = $this->dbc->query("
            DELETE FROM {$this->transTable}
            WHERE id='{$row['id']}'
        ");
        */
        if(PEAR::isError($r)) return $r;
        $this->trLog("FIN UP id={$row['id']}, trtok={$row['trtok']}".
            "\n   ".serialize($row));
    }
    
    /**
     *  
     */
    function uploadCronFailed($row, $asessid)
    {
        /*
        $r = $this->dbc->query("
            DELETE FROM {$this->transTable}
            WHERE id='{$row['id']}'
        ");
        if(PEAR::isError($r)) return $r;
        */
    }
    
    /**
     *  Check state of uploaded file
     *
     *  @param pdtoken string, put token
     *  @return hash: chsum, size, url
     */
    function uploadCheck($pdtoken)
    {
        $ret = $this->xmlrpcCall(
            'archive.uploadCheck', 
            array('token'=>$pdtoken)
        );
        return $ret;
    }

    /* ===================================================== download methods */
    /**
     *  
     */
    function downloadFromArchive($gunid, $sessid='')
    {
        // insert transport record to db
        $uid = $this->gb->getSessUserId($sessid);
        if(PEAR::isError($uid)) return $uid;
        if(is_null($uid)){
            return PEAR::raiseError("Transport::downloadFromArchive: ".
                "invalid session id", GBERR_SESS);
        }
        $parid = $this->gb->_getHomeDirId($sessid);
        if(PEAR::isError($parid)) return $parid;
        $trtok = $this->_createTrtok();
        $id = $this->dbc->nextId("{$this->transTable}_id_seq");
        if(PEAR::isError($id)) return $id;
        $res = $this->dbc->query("
            INSERT INTO {$this->transTable}
                (id, trtok, direction, state, trtype,
                    gunid,
                    expectedsum, realsum, expectedsize, realsize,
                    uid, parid
                )
            VALUES
                ($id, '$trtok', 'down', 'init', '?',
                    x'$gunid'::bigint,
                    '?e', '?r', '0', '0',
                    $uid, $parid
                )
        ");
        if(PEAR::isError($res)) return $res;
#??        $this->downloadCron();
        return $trtok;
    }
    
    /**
     *  
     */
    function downloadCron()
    {
        // fetch all opened downloads
        $rows = $this->dbc->getAll("
            SELECT
                id, trtok, state, trtype,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, expectedsum, expectedsize, url,
                uid, parid
            FROM {$this->transTable}
            WHERE direction='down' AND state<>'closed'
        ");
        if(PEAR::isError($rows)){ $this->trLogPear($rows); return FALSE; }
        if(count($rows)==0) return TRUE;
        // login to archive server
        $r = $this->loginToArchive();
        if(PEAR::isError($r)){ $this->trLog("Login error"); return FALSE; }
        $asessid = $r['sessid'];
        chdir($this->config['transDir']);
        // for all opened downloads:
        foreach($rows as $i=>$row){
            $row['pdtoken'] = StoredFile::_normalizeGunid($row['pdtoken']);
            $row['gunid'] = StoredFile::_normalizeGunid($row['gunid']);
            switch($row['state']){
                case"init":     // ------ new downloads
                    $ret = $this->downloadCronInit($row, $asessid);
                    if(PEAR::isError($ret)){
                        $this->trLogPear($ret, $row); break;
                    }
                    $row = array_merge($row, $ret);
                    #break;
                case"pending":  // ------ pending downloads
                    $ret = $this->downloadCronPending($row, $asessid);
                    if(PEAR::isError($ret)){ $this->trLogPear($ret, $row); }
                    break;
                case"finished": // ------ finished downloads
                    $ret = $this->downloadCronFinished($row, $asessid);
                    if(PEAR::isError($ret)){ $this->trLogPear($ret, $row); }
                    break;
                case"failed": // ------ failed downloads
                    $ret = $this->downloadCronFailed($row, $asessid);
                    if(PEAR::isError($ret)){ $this->trLogPear($ret, $row); }
                    break;
                default:
                    $this->trLog("downloadCron: unknown state ".
                        "({$row['state']}, id={$row['id']})");
            } // switch state
        } // foreach opened
        $r = $this->logoutFromArchive($asessid);
        if(PEAR::isError($r)) $this->trLog("Logout error");
        return TRUE;
    }
    
    /**
     *  
     */
    function downloadCronInit($row, $asessid)
    {
        $ret = $this->xmlrpcCall('archive.downloadRawAudioDataOpen',
            array('sessid'=>$asessid, 'gunid'=>$row['gunid'])
        );
        if(PEAR::isError($ret)){
            // catch 'not found' exception:
            if($ret->getCode() != 847) return $ret;
            else $trtype = '?';
        }else $trtype = 'audioclip';
        if($trtype == '?'){
            $ret = $this->xmlrpcCall('archive.existsPlaylist',
                array('sessid'=>$asessid, 'plid'=>$row['gunid'])
            );
            if(PEAR::isError($ret)){
                // catch 'not found' exception:
                if($ret->getCode() != 847) return $ret;
                else $trtype = '?';
            }else{
                $trtype = 'playlist';
                $r1 = $this->downloadMetadata($row['gunid'], $asessid);
                if(PEAR::isError($r1)) return $r1;
                $r2 = $this->gb->bsPutFile($row['parid'], $r1['filename'],
                    '', $r1['mdata'], $row['gunid'], $trtype, 'string');
                if(PEAR::isError($r2)) return $r2;
                $res = $this->dbc->query("
                    UPDATE {$this->transTable}
                    SET state='closed',
                        trtype='$trtype'
                    WHERE id='{$row['id']}'
                ");
                if(PEAR::isError($res)) return $res;
                return array(
                    'state'=>'closed', 'trtype'=>$trtype,
                    'fname'=>$r1['filename'],
                );
            }
        }
        if($trtype == '?'){
            return PEAR::raiseError("Transport::downloadCronInit:".
                " unknown trtype ({$row['trtype']})"
            );
        }
        $fpath = $this->transDir."/".basename($ret['url']);     // ***
        touch($fpath);
        $res = $this->dbc->query("
            UPDATE {$this->transTable}
            SET
                state       = 'pending',
                trtype      = '$trtype',
                fname       = '{$ret['filename']}',
                localfile   = '$fpath',
                expectedsum = '{$ret['chsum']}',
                expectedsize= '{$ret['size']}',
                url         = '{$ret['url']}',
                pdtoken     = x'{$ret['token']}'::bigint
            WHERE id='{$row['id']}'
        ");
        if(PEAR::isError($res)) return $res;
        $this->trLog("INIT DOWN id={$row['id']}, trtok={$row['trtok']}");
        return array(
            'state'=>'pending', 'trtype'=>$trtype,
            'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
            'localfile'=>$fpath, 'fname'=>$ret['filename'],
            'expectedsum'=>$ret['chsum'], 'expectedsize'=>$ret['size'],
        );
    }
    
    /**
     *  
     */
    function downloadCronPending($row, $asessid)
    {
        if($row['trtype'] != 'audioclip') return;
        // wget the file
        $command =
            "wget -q -c --timeout={$this->timeout}".
            " --waitretry={$this->waitretry}".
            " -t {$this->retries} {$row['url']}";
#        echo "$command\n";
        $res = system($command, $status);
        // check consistency
        $chsum  = $this->_chsum($row['localfile']);
        $size   = filesize($row['localfile']);
        if($status == 0){
            if($chsum == $row['expectedsum']){
                // mark download as finished
                $res = $this->dbc->query("
                    UPDATE {$this->transTable}
                    SET state='finished',
                        realsum ='{$chsum}',
                        realsize={$size}
                    WHERE id='{$row['id']}'
                ");
                if(PEAR::isError($res)) return $res;
            }else{
                @unlink($row['localfile']);
                $res = $this->dbc->query("
                    UPDATE {$this->transTable}
                    SET
                        realsum ='{$chsum}',
                        realsize={$size}
                    WHERE id='{$row['id']}'
                ");
                if(PEAR::isError($res)) return $res;
            }
        }
    }
    
    /**
     *  
     */
    function downloadCronFinished($row, $asessid)
    {
        if($row['trtype'] != 'audioclip') return;
        // call archive that downloads have been finished OK
        $res = $this->xmlrpcCall(
            'archive.downloadRawAudioDataClose',
            array('sessid'=>$asessid, 'token'=>$row['pdtoken'])
        );
        if(PEAR::isError($res)) return $res;
        $res2 = $this->downloadMetadata($row['gunid'], $asessid);
        if(PEAR::isError($res2)) return $res2;
        $mdata = $res2['mdata'];
        $name   = $row['fname'];
        $this->trLog("FIN1 DOWN id={$row['id']}, trtok={$row['trtok']}".
            "\n   ".serialize($row));
        $ac =& StoredFile::recallByGunid(&$this->gb, $row['gunid']);
        if(!PEAR::isError($ac)){
            // gunid exists - do replace
            $id = $ac->getId();
            $ac->replace(
                $id, $name, $row['localfile'], $mdata, 'string'
            );
            if(PEAR::isError($ac)) return $ac;
        }else{
            // gunid doesn't exists - do insert
            $id = $this->gb->addObj($name , 'File', $row['parid']);
            if(PEAR::isError($id)) return $id;
            $ac =&  StoredFile::insert(
                &$this->gb, $id, $name, $row['localfile'], $mdata, 'string',
                $row['gunid'], 'audioclip'
            );
            if(PEAR::isError($ac)) return $ac;
        }
        // close download in db
        $res = $this->dbc->query("
            UPDATE {$this->transTable}
            SET state='closed'
            WHERE id='{$row['id']}'
        ");
        /*
        $res = $this->dbc->query("
            DELETE FROM {$this->transTable}
            WHERE id='{$row['id']}'
        ");
        */
        if(PEAR::isError($res)) return $res;
        $this->trLog("FIN DOWN id={$row['id']}, trtok={$row['trtok']}".
            "\n   ".serialize($row));
    }
    
    /**
     *  
     */
    function downloadCronFailed($row, $asessid)
    {
        /*
        $r = $this->dbc->query("
            DELETE FROM {$this->transTable}
            WHERE id='{$row['id']}'
        ");
        if(PEAR::isError($r)) return $r;
        */
    }
    
    /**
     *  
     */
    function downloadMetadata($gunid, $asessid)
    {
        $ret = $this->xmlrpcCall('archive.downloadMetadataOpen',
            array('sessid'=>$asessid, 'gunid'=>$gunid)
        );
        if(PEAR::isError($ret)) return $ret;
        #echo "{$ret['url']}\n";
        if(($mdata = file_get_contents($ret['url'])) === FALSE){
            return PEAR::raiseError("Transport::downloadCronInit: ".
                "metadata download failed ({$gunid})", TRERR_MD
            );
        }
        $filename = $ret['filename'];
        $ret = $this->xmlrpcCall('archive.downloadMetadataClose',
            array('token'=>$ret['token'])
        );
        if(PEAR::isError($ret)) return $ret;
        return array('mdata'=>$mdata, 'filename'=>$filename);
    }
    
    /* ======================================================= search methods */
    /**
     *  Start search in archive
     */
    function globalSearch()
    {
        // create searchJob from searchData
        // uploadFile searchJob
        // downloadFile searchResults
        // not implemented yet
    }

    /**
     *  Returns results from archive search
     */
    function getSearchResults()
    {
        // return downloaded file with search results
        // not implemented yet
    }
    
    /* =============================================== authentication methods */

    /**
     *  Login to archive server
     *
     *  @return string sessid or error
     */
    function loginToArchive()
    {
        $res = $this->xmlrpcCall(
            'archive.login',
            array(
                'login'=>$this->config['archiveAccountLogin'],
                'pass'=>$this->config['archiveAccountPass']
            )
        );
        return $res;
    }
        
    /**
     *  Logout from archive server
     *
     *  @param sessid session id
     *  @return string Bye or error
     */
    function logoutFromArchive($sessid)
    {
        $res = $this->xmlrpcCall(
            'archive.logout',
            array(
                'sessid'=>$sessid,
            )
        );
        return $res;
    }
        
    /* ==================================================== auxiliary methods */
    /**
     *  
     */
    function _createTrtok()
    {
        $initString =
            microtime().$_SERVER['SERVER_ADDR'].rand()."org.mdlf.livesupport";
        $hash = md5($initString);
        $res = substr($hash, 0, 16);
        return $res;
    }
    
    /**
     *  
     */
    function _getResDir($gunid)
    {
        $resDir = $this->config['storageDir']."/".substr($gunid, 0, 3);
        if(!file_exists($resDir)){ mkdir($resDir, 02775); chmod($resDir, 02775); }
        return $resDir;
    }
    
    /**
     *  Ping to archive server
     *
     *  @return string sessid or error
     */
    function pingToArchive()
    {
        $res = $this->xmlrpcCall(
            'archive.ping',
            array(
                'par'=>'testString_'.date('H:i:s')
            )
        );
        return $res;
    }
    /**
     *  XMLRPC call to archive
     */
    function xmlrpcCall($method, $pars=array())
    {
        $xrp = XML_RPC_encode($pars);
        $c = new XML_RPC_Client(
            "{$this->config['archiveUrlPath']}/".
                "{$this->config['archiveXMLRPC']}",
            $this->config['archiveUrlHost'], $this->config['archiveUrlPort']
        );
        $f=new XML_RPC_Message($method, array($xrp));
        #echo "\n--\n".$f->serialize()."\n--\n";
        $r = $c->send($f);
        if ($r->faultCode()>0) {
            return PEAR::raiseError($r->faultString(), $r->faultCode());
        }else{
            $v = $r->value();
            return XML_RPC_decode($v);
        }
    }

    /**
     *  md5 checksum of local file
     */
    function _chsum($fpath)
    {
        return md5_file($fpath);
    }
    
    /**
     *  logging wrapper for PEAR error object
     *
     *  @param eo PEAR error object
     *  @param row array returned from getRow
     */
    function trLogPear($eo, $row=NULL)
    {
        $msg = $eo->getMessage()." ".$eo->getUserInfo();
        if(!is_null($row)) $msg .= "\n    ".serialize($row);
        $this->trLog($msg);
    }

    /**
     *  logging for debug transports
     *
     *  @param msg string - log message
     */
    function trLog($msg)
    {
        $fp=fopen("{$this->transDir}/log", "a") or die("Can't write to log\n");
        fputs($fp, "---".date("H:i:s")."---\n $msg\n");
        fclose($fp);
    }

    /* ====================================================== install methods */
    /**
     *  Delete all transports
     */
    function resetData()
    {
        return $this->dbc->query("DELETE FROM {$this->transTable}");
    }

    /**
     *  Install method<br>
     *
     *  direction: up | down
     *  state: init | pending | finished | closed | failed
     *  trtype: audioclip | playlist | searchjob
     *  
     */
    function install()
    {
        $this->dbc->query("CREATE TABLE {$this->transTable} (
            id int not null,
            trtok char(16) not null,  -- transport token
            direction varchar(128) not null,
            state varchar(128) not null,
            trtype varchar(128) not null,
            gunid bigint,             -- global unique id
            pdtoken bigint,           -- put/download token from archive
            url varchar(255),
            fname varchar(255),       -- mnemonic filename
            localfile varchar(255),   -- pathname of local part
            expectedsum char(32),     -- expected file checksum
            realsum char(32),         -- checksum of transported part
            expectedsize int,         -- expected filesize in bytes
            realsize int,             -- filesize of transported part
            uid int,                  -- local user id of transport owner
            parid int,                -- local id of download destination folder
            ts timestamp
        )");
        $this->dbc->createSequence("{$this->transTable}_id_seq");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_id_idx
            ON {$this->transTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_trtok_idx
            ON {$this->transTable} (trtok)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_token_idx
            ON {$this->transTable} (token)");
        $this->dbc->query("CREATE INDEX {$this->transTable}_gunid_idx
            ON {$this->transTable} (gunid)");
    }

    /**
     *  Uninstall method
     */
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->transTable}");
        $this->dbc->dropSequence("{$this->transTable}_id_seq");
    }
}

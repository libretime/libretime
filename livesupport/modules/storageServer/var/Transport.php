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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Transport.php,v $

------------------------------------------------------------------------------*/
include_once "xmlrpc/XML/RPC.php";

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
     *  @param config config array
     */
    function Transport(&$dbc, $config)
    {
        $this->dbc        =& $dbc;
        $this->config     = $config;
        $this->transTable = $config['tblNamePrefix'].'trans';
        $this->transDir   = $config['transDir'];
    }

    /**
     *  Return state of transport job
     *
     */
    function getTransportStatus($trid)
    {
        $row = $this->dbc->getRow(
            "SELECT state FROM {$this->transTable} WHERE trid='$trid'"
        );
        if(PEAR::isError($res)) return $res;
        return $row['state'];
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
    
    /* ======================================= general file transport methods */
    
    /**
     *  
     */
    function uploadOpen($file, $type, $sessid='', $gunid='X')
    {
        $trid = $this->_createTrId();
        $md5h = $this->_md5sum($this->transDir."/$file");
        $id = $this->dbc->nextId("{$this->transTable}_id_seq");
        $res = $this->dbc->query("
            INSERT INTO {$this->transTable}
                (id, trid, direction, state, type,
                    md5h, url, fname, gunid
                )
            VALUES
                ($id, '$trid', 'up', 'init', '$type',
                    '$md5h', '', '$file', '$gunid'
                )
        ");
        if(PEAR::isError($res)) return $res;
#??        $this->uploadCron();
        return $trid;
    }
    
    /**
     *  
     */
    function uploadCron()
    {
        // fetch all opened uploads
        $rows = $this->dbc->getAll("
            SELECT * FROM {$this->transTable}
            WHERE direction='up' AND state<>'closed'
        ");
        if(count($rows)==0) return TRUE;
        $asessid = $this->loginToArchive();
        chdir($this->config['transDir']);
        // for all opened uploads:
        foreach($rows as $i=>$row){
            switch($row['state']){
                case"init":     // ------ new uploads
                    $finf = $this->xmlrpcCall( 'archive.uploadOpen',
                        array('sessid'=>$asessid, 'trid'=>$row['trid'],
                            'type'=>$row['type']
                        )
                    );
                    if(PEAR::isError($finf)) return $finf;
                    $res = $this->dbc->query("
                        UPDATE {$this->transTable}
                        SET state='pending', url='{$finf['url']}'
                        WHERE id='{$row['id']}'
                    ");
                    if(PEAR::isError($res)) return $res;
                    $row['url'] = $finf['url'];
#break;
                case"pending":  // ------ pending uploads
                    $finf = $this->uploadCheck($asessid, $row['url']);
                    if(PEAR::isError($finf)) return $finf;
                    // test filesize
                    if(intval($finf['size']) < filesize($row['fname'])){
                        // not finished - upload next part
                        $res = system(
                            "curl -s -C {$finf['size']} --max-time 600".
                            " --speed-time 20 --speed-limit 500".
                            " --connect-timeout 20".
                            " -T {$row['fname']} {$row['url']}",
                            $status
                        );
                    }else{
                        // hmmm - we are finished? OK - continue
                        $status = 0;
                    }
                    if($status == 0 || $status == 18){
                        $finf = $this->uploadCheck($asessid, $row['url']);
                        if(PEAR::isError($finf)) return $finf;
                        // test checksum
                        if($finf['md5h'] == $row['md5h']){
                            // finished
                            $res = $this->dbc->query("
                                UPDATE {$this->transTable} SET state='finished'
                                WHERE id='{$row['id']}'
                            ");
                            if(PEAR::isError($res)) return $res;
                        }else{
                            if(intval($finf['size']) >= filesize($row['fname']))
                            {
                                // wrong md5 at finish - TODO: start again
                                // $this->xmlrpcCall('archive.uploadReset', array());
                                return PEAR::raiseError("Transport::uploadCron:".
                                    " file uploaded with bad md5"
                                );
                            }
                        }
                    }
#break;
                case"finished": // ------ finished uploads
                    $res = $this->xmlrpcCall(
                        'archive.uploadClose',
                        array('sessid'=>$asessid,
                            'url'=>$row['url'], 'type'=>$row['type'],
                            'gunid'=>$row['gunid'],
                        )
                    );
                    if(PEAR::isError($res)) return $res;
                    @unlink($this->transDir."/".$row['fname']);
                    // close upload in db TODO: or delete record?
                    $this->dbc->query("
                        UPDATE {$this->transTable} SET state='closed'
                        WHERE id='{$row['id']}'
                    ");
                    break;
                default:
                    echo "Transport::uploadCron: unknown state".
                        " '{$row['state']}' (id={$row['id']})\n";
            } // switch state
        } // foreach opened
        $this->logoutFromArchive($asessid);
        return TRUE;
    }

    /**
     *  Check state of uploaded file
     *
     *  @param sessid
     *  @param url
     *  @return hash: md5h, size, url
     */
    function uploadCheck($sessid, $url)
    {
        $finf = $this->xmlrpcCall(
            'archive.uploadCheck', 
            array('sessid'=>$sessid, 'url'=>$url)
        );
        return $finf;
    }

    /**
     *  
     */
    function downloadOpen($sessid, $type, $gunid, $uid)
    {
        // insert transport record to db
        $trid = $this->_createTrId();
        $id = $this->dbc->nextId("{$this->transTable}_id_seq");
        $res = $this->dbc->query("
            INSERT INTO {$this->transTable}
                (id, trid, direction, state, type,
                    gunid, sessid, uid
                )
            VALUES
                ($id, '$trid', 'down', 'init', '$type',
                    '$gunid', '$sessid', $uid
                )
        ");
        if(PEAR::isError($res)) return $res;
#??        $this->downloadCron();
        return $trid;
    }
    
    /**
     *  
     */
    function downloadCron(&$gb)
    {
        // fetch all opened downloads
        $rows = $this->dbc->getAll("
            SELECT * FROM {$this->transTable}
            WHERE direction='down' AND state<>'closed'
        ");
        if(count($rows)==0) return TRUE;
        $asessid = $this->loginToArchive();
        chdir($this->config['transDir']);
        // for all opened downloads:
        foreach($rows as $i=>$row){
            switch($row['state']){
                case"init":     // ------ new downloads
                    // call archive.downloadOpen
                    $finf = $this->xmlrpcCall(
                        'archive.downloadOpen',
                        array('sessid'=>$asessid, 'type'=>$row['type'],
                            'par'=>$row['gunid']
                        )
                    );
                    if(PEAR::isError($finf)) return $finf;
                    $res = $this->dbc->query("
                        UPDATE {$this->transTable}
                        SET state='pending', url='{$finf['url']}',
                            md5h='{$finf['md5h']}', fname='{$finf['fname']}'
                        WHERE id='{$row['id']}'
                    ");
                    if(PEAR::isError($res)) return $res;
                    $row = array_merge($row, $finf);
#break;
                case"pending":  // ------ pending downloads
                    // wget the file
                    $res = system(
                        "wget -q -c --timeout={$this->timeout}".
                        " --waitretry={$this->waitretry}".
                        " -t {$this->retries} {$row['url']}",
                        $status
                    );
                    // check consistency
                    $md5h = $this->_md5sum($row['fname']);
                    if($status == 0){
                        if($md5h == $row['md5h']){
                            // mark download as finished
                            $res = $this->dbc->query("
                                UPDATE {$this->transTable}
                                SET state='finished'
                                WHERE id='{$row['id']}'
                            ");
                            if(PEAR::isError($res)) return $res;
                        }else{
                            @unlink($row['fname']);
                        }
                    }
#break;
                case"finished": // ------ finished downloads
                    // call archive that downloads have been finished OK
                    $res = $this->xmlrpcCall(
                        'archive.downloadClose',
                        array('sessid'=>$asessid, 'url'=>$row['url'])
                    );
                    if(PEAR::isError($res)) return $res;
                    // process file in fake session
                    $lsessid = $gb->_fakeSession($row['uid']);
                    if(PEAR::isError($lsessid)) return $lsessid;
                    $res = $gb->processTransported(
                        $lsessid, $row['fname'], $row['type'], $row['gunid']
                    );
                    if(PEAR::isError($res)) return $res;
                    $res = $gb->logout($lsessid);
                    if(PEAR::isError($res)) return $res;

                    // close download in db TODO: or delete record?
                    $res = $this->dbc->query("
                        UPDATE {$this->transTable}
                        SET state='closed'
                        WHERE id='{$row['id']}'
                    ");
                    if(PEAR::isError($res)) return $res;
                    break;
                default:
                    echo "Transport::downloadCron: unknown state".
                        " '{$row['state']}' (id={$row['id']})\n";
            } // switch state
        } // foreach opened
        $this->logoutFromArchive($asessid);
        return TRUE;
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
    function _createTrId()
    {
        return md5(microtime().$_SERVER['SERVER_ADDR'].rand().
            "org.mdlf.livesupport");
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
     *  XMLRPC call to archive
     */
    function xmlrpcCall($method, $pars=array())
    {
        $xrp = xmlrpc_encoder($pars);
        $c = new xmlrpc_client(
            "{$this->config['archiveUrlPath']}/".
                "{$this->config['archiveXMLRPC']}",
            $this->config['archiveUrlHost'], $this->config['archiveUrlPort']
        );
        $f=new xmlrpcmsg($method, array($xrp));
        $r = $c->send($f);
        if ($r->faultCode()>0) {
            return PEAR::raiseError($r->faultString(), $r->faultCode());
        }else{
            $v = $r->value();
            return xmlrpc_decoder($v);
        }
    }

    /**
     *  md5 checksum of local file
     */
    function _md5sum($fpath)
    {
        $md5h = `md5sum $fpath`;
        $arr = split(' ', $md5h);
        return $arr[0];
    }
    
    /* ====================================================== install methods */
    /**
     *  Install method<br>
     *  state: pending, finished, closed
     */
    function install()
    {
        $this->dbc->query("CREATE TABLE {$this->transTable} (
            id int not null,
            trid char(32) not null,
            direction varchar(128) not null, -- down | up
            state varchar(128) not null,
            type varchar(128) not null,      -- file | searchJob
            md5h char(32),
            url varchar(255),
            fname varchar(255),
            gunid char(32),
            sessid char(32),
            uid int,
            parid int,
            ts timestamp
        )");
        $this->dbc->createSequence("{$this->transTable}_id_seq");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_id_idx
            ON {$this->transTable} (id)");
        $this->dbc->query("CREATE INDEX {$this->transTable}_trid_idx
            ON {$this->transTable} (trid)");
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

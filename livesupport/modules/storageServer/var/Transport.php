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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Transport.php,v $

------------------------------------------------------------------------------*/
include("xmlrpc/xmlrpc.inc");

$config['archiveUrlPath'] = '/livesupport/modules/archiveServer/var';
$config['archiveXMLRPC'] = 'xmlrpc/xrArchive.php';
$config['archiveUrlHost'] = 'localhost';
$config['archiveUrlPort'] = 80;
$config['archiveAccountLogin'] = 'root';
$config['archiveAccountPass']  = 'q';

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
        $this->dbc =& $dbc;
        $this->config = $config;
        $this->transTable = $config['tblNamePrefix'].'trans';
    }

    /**
     *  Start of download<br>
     *   - create transport record<br>
     *   - call archive.downloadOpen<br>
     *
     *  @param gunid
     */
    function downloadFile($gunid)
    {
        $res = $this->xmlrpcCall(
            'archive.login',
            array(
                'login'=>$this->config['archiveAccountLogin'],
                'pass'=>$this->config['archiveAccountPass']
            )
        );
        if(PEAR::isError($res)) return $res;
        $sessid = $res;
        // call archive.downloadOpen
        $res = $this->xmlrpcCall(
            'archive.downloadOpen', array('sessid'=>$sessid, 'gunid'=>$gunid)
        );
        if(PEAR::isError($res)) return $res;
        $file = $res;
        // insert transport record to db
        $id = $this->dbc->nextId("{$this->transTable}_id_seq");
        $res = $this->dbc->query("
            INSERT INTO {$this->transTable}
                (id, direction, state, gunid, type, sessid, md5h, url, fname)
            VALUES (
                $id, 'down', 'pending', '$gunid', 'file', '$sessid',
                '{$file['md5h']}', '{$file['url']}', '{$file['fname']}'
            )
        ");
        if(PEAR::isError($res)) return $res;
#??        $this->downloadCron();
        return $id;
    }
    
    /**
     *  Cron method for download.<br>
     *  Should be called periodically.
     *
     */
    function downloadCron()
    {
        // fetch all pending downloads
        $rows = $this->dbc->getAll("
            SELECT id, url, md5h, fname
            FROM {$this->transTable}
            WHERE direction='down' AND state='pending'
        ");
        // for all pending downloads:
        foreach($rows as $i=>$row){
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
                    $this->dbc->query("
                        UPDATE {$this->transTable}
                        SET state='finished'
                        WHERE id='{$row['id']}'
                    ");
                }else{
                    @unlink($fname);
                }
            }
        }
        // fetch all finished downloads
        $rows = $this->dbc->getAll("
            SELECT id, url, md5h, sessid
            FROM {$this->transTable}
            WHERE direction='down' AND state='finished'
        ");
        // for all finished downloads:
        foreach($rows as $i=>$row){
            $res = $this->xmlrpcCall(
                'archive.downloadClose',
                array('sessid'=>$row['sessid'], 'url'=>$row['url'])
            );
            if(PEAR::isError($res)) return $res;
            // close download in db TODO: or delete record?
            $this->dbc->query("
                UPDATE {$this->transTable}
                SET state='closed'
                WHERE id='{$row['id']}'
            ");
        }
        return TRUE;
    }

    /**
     *  Start of upload
     *
     *  @param fname
     *  @param gunid
     */
    function uploadFile($fname, $gunid)
    {
        $res = $this->xmlrpcCall(
            'archive.login',
            array(
                'login'=>$this->config['archiveAccountLogin'],
                'pass'=>$this->config['archiveAccountPass']
            )
        );
        if(PEAR::isError($res)) return $res;
        $sessid = $res;
        $file = $this->xmlrpcCall(
            'archive.uploadOpen', array('sessid'=>$sessid, 'gunid'=>$gunid)
        );
        if(PEAR::isError($file)) return $file;
        $md5h = $this->_md5sum($fname);
        $id = $this->dbc->nextId("{$this->transTable}_id_seq");
        $res = $this->dbc->query("
            INSERT INTO {$this->transTable}
                (id, direction, state, gunid, type, sessid, md5h, url, fname)
            VALUES (
                $id, 'up', 'pending', '$gunid', 'file', '$sessid',
                '$md5h', '{$file['url']}', '$fname'
            )
        ");
        if(PEAR::isError($res)) return $res;
#??        $this->uploadCron();
        return $id;
    }
    
    /**
     *  Cron method for upload.<br>
     *  Should be called periodically.
     *
     */
    function uploadCron()
    {
        // fetch all pending uploads
        $rows = $this->dbc->getAll("
            SELECT id, sessid, gunid, fname, url, md5h
            FROM {$this->transTable}
            WHERE direction='up' AND state='pending'
        ");
        // for all pending uploads:
        foreach($rows as $i=>$row){
            $file = $this->uploadCheck($row['sessid'], $row['url']);
            if(PEAR::isError($file)) return $file;
            // test filesize
            if(intval($file['size']) < filesize($row['fname'])){
                // not finished - upload next part
                $res = system(
                    "curl -s -C {$file['size']} --max-time 600".
                    " --speed-time 20 --speed-limit 500".
                    " --connect-timeout 20".
                    " -T {$row['fname']} {$row['url']}",
                    $status
                );
            }else{
                // hmmm - we are finished? strage, but we'll try to continue
                $status = 0;
            }
            if($status == 0){
                $file = $this->uploadCheck($row['sessid'], $row['url']);
                if(PEAR::isError($file)) return $file;
                // test checksum
                if($file['md5h'] == $row['md5h']){
                    // finished
                    $res = $this->dbc->query("
                        UPDATE {$this->transTable} SET state='finished'
                        WHERE id='{$row['id']}'
                    ");
                    if(PEAR::isError($res)) return $res;
                }else{
                    if(intval($file['size']) >= filesize($row['fname'])){
                        // wrong md5 at finish - we probably have to start again
                        // $this->xmlrpcCall('archive.uploadReset', array());
                        return PEAR::raiseError(
                            "Transport::uploadCron: file uploaded with bad md5"
                        );
                    }
                }
            }
        }
        // fetch all finished uploads
        $rows = $this->dbc->getAll("
            SELECT id, sessid, gunid, fname, url, md5h
            FROM {$this->transTable}
            WHERE direction='up' AND state='finished'
        ");
        // for all finished uploads:
        foreach($rows as $i=>$row){
            $res = $this->xmlrpcCall(
                'archive.uploadClose',
                array('sessid'=>$row['sessid'], 'url'=>$row['url'])
            );
            if(PEAR::isError($res)) return $res;
            // close upload in db TODO: or delete record?
            $this->dbc->query("
                UPDATE {$this->transTable} SET state='closed'
                WHERE id='{$row['id']}'
            ");
        }
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
        $file = $this->xmlrpcCall(
            'archive.uploadCheck', 
            array('sessid'=>$sessid, 'url'=>$url)
        );
        return $file;
    }

    /**
     *  Abort pending upload
     *
     *  @param id local tranport id
     */
    function uploadAbort($id)
    {
        $row = $this->dbc->getRow("
            SELECT id, sessid, gunid, fname, url
            FROM {$this->transTable}
            WHERE id='$id'
        ");
        if(PEAR::isError($row)) return $row;
        $res = $this->xmlrpcCall('archive.uploadAbort',
            array('sessid'=>$row['sessid'], 'url'=>$row['url'])
        );
        if(PEAR::isError($res)) return $res;
    }

    /**
     *  Return state of transport job
     *
     */
    function getTransportStatus($id)
    {
        $row = $this->dbc->getRow(
            "SELECT state FROM {$this->transTable} WHERE id='$id'"
        );
        if(PEAR::isError($res)) return $res;
        return $row['state'];
    }

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
    
    /**
     *  XMLRPC call to archive
     */
    function xmlrpcCall($method, $pars=array())
    {
        $c = new xmlrpc_client(
            "{$this->config['archiveUrlPath']}/".
                "{$this->config['archiveXMLRPC']}",
            $this->config['archiveUrlHost'], $this->config['archiveUrlPort']
        );
        $f=new xmlrpcmsg($method, array(xmlrpc_encoder($pars)));
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
    
    /**
     *  Install method<br>
     *  state: pending, finished, closed
     */
    function install()
    {
        $this->dbc->query("CREATE TABLE {$this->transTable} (
            id int not null,
            gunid char(32) not null,
            md5h char(32) not null,
            sessid char(32) not null,
            url varchar(255) not null,
            fname varchar(255) not null,
            type varchar(128) not null,      -- file | searchJob
            direction varchar(128) not null, -- down | up
            state varchar(128) not null,
            ts timestamp
        )");
        $this->dbc->createSequence("{$this->transTable}_id_seq");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_id_idx
            ON {$this->transTable} (id)");
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

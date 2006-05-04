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
define('TRERR_', 70);
define('TRERR_MD', 71);
define('TRERR_TOK', 72);

include_once "XML/RPC.php";

/**
 *  Class for handling file tranport between StorageServer and ArchiveServer<br>
 *  over unreliable network and from behind firewall<br><br>
 *
 *  Transport states:<ul>
 *   <li>init: transport is prepared, but not started
 *      (e.g. no network connection is present)</li>
 *   <li>pending: transport is in progress, file is not fully transported to
 *   target system</li>
 *   <li>finished: transport is finished, but file processing on target side
 *   is not completed</li>
 *   <li>closed: processing on target side is completed without errors</li>
 *   <li>failed: error - error message stored in errmsg field</li>
 *  </ul>
 */
class Transport
{
    var $dbc;
    var $downTimeout    = 20;
    var $downWaitretry  = 6;
    var $downRetries    = 6;

    var $upTrMaxTime     = 600;
    var $upTrSpeedTime   = 20;
    var $upTrSpeedLimit  = 500;
    var $upTrConnectTimeout = 20;

    /**
     *  Constructor
     *
     *  @param dbc PEAR DB object reference
     *  @param gb LocStor object reference
     *  @param config config array
     */
    function Transport(&$gb)
    {
        $this->gb         =& $gb;
        $this->dbc        =& $gb->dbc;
        $this->config     =& $gb->config;
        $this->transTable =  $gb->config['tblNamePrefix'].'trans';
        $this->transDir   =  $gb->config['transDir'];
    }

/*
    /* ======================================================= public methods * /
    function getTransportInfo($trtok)   
    function turnOnOffTransports($onOff)
    function uploadFile2Hub($filePath) 
    function getHubInitiatedTransfers()
    function startHubInitiatedTransfer($trtok)
    function uploadAudioClip2Hub($gunid)
    function downloadAudioClipFromHub($gunid)
    function uploadPlaylist2Hub($plid, $withContent)
    function downloadPlaylistFromHub($plid, $withContent)
    function globalSearch($criteria) 
    function getSearchResults($trtok)

    /* ======================================================== cron methods * /
    function cronMain()
    function cronInit($row, $asessid)
    function cronPending($row, $asessid)
    function cronFinished($row, $asessid)
    function cronFailed($row, $asessid)
?    function cronCheck($pdtoken)

    /* ========================================================= misc methods * /
    function loginToArchive()
    function logoutFromArchive($sessid)
    function pingToArchive()
    function xmlrpcCall($method, $pars=array())
*/

    /* =============================== DUMMY ================================ */
    /* ------------------------------------------------------- common methods */
    /**
     *  Common "check" method for transports
     *
     *  @param trtok: string - transport token
     *  @return struct/hasharray with fields:
     *      trtype: string - audioclip | playlist | search | file
     *      state: string - transport state
     *                  init | pending | finished | closed | failed
     *      direction: string - up | down
     *      expectedsize: int - file size in bytes
     *      realsize: int - currently transported bytes
     *      expectedchsum: string - orginal file checksum
     *      realchsum: string - transported file checksum
     *      title: string - dc:title or filename etc.
     *      errmsg: string - error message for failed transports
     *      ... ?
     */
    function getTransportInfo($trtok)
    {
    // DUMMY
        switch($trtok){
            case'123456789abcdeff';     // upload/download
                return array(
                    'state'         =>  'finished',
                    'direction'     =>  'up',
                    'trtype'        =>  'audioclip',
                    'expectedsize'  =>  1024,
                    'realsize'      =>  1024,
                    'expectedchsum' =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'realchsum'     =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'title'         =>  'DUMMY !',
                    'errmsg'        =>  '',
                );
            case'123456789abcdef2';     // upload/download
                return array(
                    'state'         =>  'running',
                    'direction'     =>  'down',
                    'trtype'        =>  'playlist',
                    'expectedsize'  =>  1624,
                    'realsize'      =>  342,
                    'expectedchsum' =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'realchsum'     =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'title'         =>  'DUMMY playlist - 2',
                    'errmsg'        =>  '',
                );
            case'123456789abcdef3';     // upload/download
                return array(
                    'state'         =>  'paused',
                    'direction'     =>  'up',
                    'trtype'        =>  'audioclip',
                    'expectedsize'  =>  1024,
                    'realsize'      =>  322,
                    'expectedchsum' =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'realchsum'     =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'title'         =>  'kakaoscsiga - 3',
                    'errmsg'        =>  '',
                );
            case'123456789abcdef4';     // upload/download
                return array(
                    'state'         =>  'running',
                    'direction'     =>  'up',
                    'trtype'        =>  'playlist',
                    'expectedsize'  =>  233,
                    'realsize'      =>  23,
                    'expectedchsum' =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'realchsum'     =>  '12dd9137a855cf600881dd6d3ffa7517',
                    'title'         =>  'ez egy playlist - 4',
                    'errmsg'        =>  '',
                );
            case'123456789abcdefe';     // search
                return array(
                    'state'         =>  'finished',
                );
            default:
                return PEAR::raiseError(
                    "Transport::getTransportInfo:".
                    " invalid transport token ($trtok)"
                );
        }
    }
    
    /**
     *  Turn transports on/off, optionaly return current state.
     *
     *  @param onOff: boolean optional (if not used, current state is returned)
     *  @return boolean - previous state
     */
    // DUMMY
    function turnOnOffTransports($onOff)
    {
        return TRUE;
    }

    /* ------------------------ methods for ls-archive-format file transports */
    /**
     *  Open async file transfer from local storageServer to network hub,
     *  file should be ls-archive-format file.
     *
     *  @param filePath string - local path to uploaded file
     *  @return string - transport token
     */
    function uploadFile2Hub($filePath)
    {
    // DUMMY
        if(!file_exists($filePath)){
            return PEAR::raiseError(
                "Transport::uploadFile2Hub: file not found ($filePath)"
            );
        }
        return '123456789abcdeff';
    }

    /**
     *  Get list of prepared transfers initiated by hub
     *
     *  @return array of structs/hasharrays with fields:
     *      trtok: string transport token
     *      ... ?
     */
    // DUMMY
    function getHubInitiatedTransfers()
    {
        return array(
            array(
                'trtok' =>  '123456789abcdeff',
            ),
            array(
                'trtok' =>  '123456789abcdef2',
            ),
            array(
                'trtok' =>  '123456789abcdef3',
            ),
            array(
                'trtok' =>  '123456789abcdef4',
            ),
        );
    }

    /**
     *  Start of download initiated by hub
     *
     *  @param trtok: string - transport token obtained from
     *          the getHubInitiatedTransfers method
     *  @return string - transport token
     */
    // DUMMY
    function startHubInitiatedTransfer($trtok)
    {
        if($trtok != '123456789abcdeff'){
            return PEAR::raiseError(
                "Transport::startHubInitiatedTransfer:".
                " invalid transport token ($trtok)"
            );
        }
        return $trtok;
    }

    /* ------------- special methods for audioClip/webstream object transport */

    /**
     *  Start upload of audioClip/webstream from local storageServer to hub
     *
     *  @param gunid: string - global unique id of object being transported
     *  @return string - transport token
     */
    function uploadAudioClip2Hub($gunid)
    {
    // DUMMY
        $ac = StoredFile::recallByGunid($this->gb, $gunid);
        if(PEAR::isError($ac)){ return $ac; }
        return '123456789abcdeff';
    }

    /**
     *  Start download of audioClip/webstream from hub to local storageServer
     *
     *  @param gunid: string - global unique id of object being transported
     *  @return string - transport token
     */
    // DUMMY
    function downloadAudioClipFromHub($gunid)
    {
        return '123456789abcdeff';
    }

    /* ------------------------------- special methods for playlist transport */
    /**
     *  Start upload of playlist from local storageServer to hub
     *
     *  @param plid: string - global unique id of playlist being transported
     *  @param withContent: boolean - if true, transport playlist content too
     *  @return string - transport token
     */
    // DUMMY
    function uploadPlaylist2Hub($plid, $withContent)
    {
        $pl = Playlist::recallByGunid($this->gb, $plid);
        if(PEAR::isError($pl)){ return $pl; }
        return TRUE;
    }

    /**
     *  Start download of playlist from hub to local storageServer
     *
     *  @param plid: string - global unique id of playlist being transported
     *  @param withContent: boolean - if true, transport playlist content too
     *  @return string - transport token
     */
    // DUMMY
    function downloadPlaylistFromHub($plid, $withContent)
    {
        $pl = Playlist::recallByGunid($this->gb, $plid);
        if(PEAR::isError($pl)){ return $pl; }
        return TRUE;
    }

    /* ------------------------------------------------ global-search methods */
    /**
     *  Start search job on network hub
     *
     *  @param criteria: LS criteria format (see localSearch)
     *  @return string - transport token
     */
    // DUMMY
    function globalSearch($criteria)
    {
        return '123456789abcdefe';
    }

    /**
     *  Get results from search job on network hub
     *
     *  @param trtok: string - transport token
     *  @return : LS search result format (see localSearch)
     */
    // DUMMY
    function getSearchResults($trtok)
    {
        if($trtok != '123456789abcdefe'){
            return PEAR::raiseError(
                "Transport::getSearchResults: invalid transport token ($trtok)"
            );
        }
        return array(
            'results'   => array('0000000000010001', '0000000000010002'),
            'cnt'       => 2,
        );
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
        
    /* ========================================================= cron methods */
    /* -------------------------------------------------- common cron methods */
    /* ==================================================== auxiliary methods */
    /**
     *  
     */
    function _createTrtok()
    {
        $ip = (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '');
        $initString =
            microtime().$ip.rand()."org.mdlf.livesupport";
        $hash = md5($initString);
        $res = substr($hash, 0, 16);
        return $res;
    }
    
    /**
     *  
     */
    function getTransports($direction=NULL)
    {
        switch($direction){
            case 'up':  $dirCond = "direction='up' AND"; break;
            case 'down':  $dirCond = "direction='down' AND"; break;
            default: $dirCond = ''; break;
        }
        $rows = $this->dbc->getAll("
            SELECT
                id, trtok, state, trtype, direction,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, expectedsum, expectedsize, url
            FROM {$this->transTable}
            WHERE $dirCond state<>'closed' 
        ");
        if(PEAR::isError($rows)){ $this->trLogPear($rows); return FALSE; }
        return $rows;
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
                'par'=>'ping_'.date('H:i:s')
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
        $logfile = "{$this->transDir}/log";
        if(FALSE === ($fp = fopen($logfile, "a"))){
            return PEAR::raiseError(
                "Transport::trLog: Can't write to log ($logfile)"
            );
        }
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
     *  trtype: audioclip | playlist | searchjob | file
     *  
     */
    function install()
    {
        $this->dbc->query("CREATE TABLE {$this->transTable} (
            id int not null,          -- primary key
            trtok char(16) not null,  -- transport token
            direction varchar(128) not null, -- direction: up|down
            state varchar(128) not null,     -- state
            trtype varchar(128) not null,    -- transport type
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

?>
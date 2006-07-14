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
define('TRERR_NOTFIN', 73);
define('TRERR_XR_FAIL', 74);

#define('TR_LOG_LEVEL', 0);
define('TR_LOG_LEVEL', 10);

define('HOSTNAME', 'storageServer');

include_once "XML/RPC.php";
include_once "TransportRecord.php";

/**
 *  Class for handling file tranport between StorageServer and ArchiveServer<br>
 *  over unreliable network and from behind firewall<br><br>
 *
 *  Transport states:
 *  <ul>
 *   <li>init: transport is prepared, but not started
 *      (e.g. no network connection is present)</li>
 *   <li>pending: transport is in progress, file is not fully transported to
 *   target system</li>
 *   <li>waiting: transport is in progress, but not running now</li>
 *   <li>finished: transport is finished, but file processing on target side
 *   is not completed</li>
 *   <li>closed: processing on target side is completed without errors</li>
 *   <li>failed: error - error message stored in errmsg field</li>
 *   <li>paused: transport have been paused</li>
 *  </ul>
 *
 *  Transport types:
 *  <ul>
 *   <li>audioclip</li>
 *   <li>playlist</li>
 *   <li>searchjob</li>
 *   <li>metadata</li>
 *   <li>file</li>
 *  </ul>
 */
class Transport
{
    /**
     *  Container for db connection instance
     */
    var $dbc;

    /**
     *  wget --timeout parameter [s]
     */
    var $downTimeout    = 20;       // 600
    /**
     *  wget --waitretry parameter [s]
     */
    var $downWaitretry  = 10;
    /**
     *  wget --limit-rate parameter
     */
    var $downLimitRate  = NULL;
    /**
     *  wget -t parameter
     */
    var $downRetries    = 6;
    /**
     *  curl --max-time parameter
     */
    var $upTrMaxTime     = 600;
    /**
     *  curl --speed-time parameter
     */
    var $upTrSpeedTime   = 20;
    /**
     *  curl --speed-limit parameter
     */
    var $upTrSpeedLimit  = 500;
    /**
     *  curl --connect-timeout parameter
     */
    var $upTrConnectTimeout = 20;
    /**
     *  curl --limit-rate parameter
     */
    var $upLimitRate  = NULL;

    /**
     *  Constructor
     *
     *  @param gb LocStor object reference
     *  @return Transport object instance
     */
    function Transport(&$gb)
    {
        $this->gb         =& $gb;
        $this->dbc        =& $gb->dbc;
        $this->config     =& $gb->config;
        $this->transTable =  $gb->config['tblNamePrefix'].'trans';
        $this->transDir   =  $gb->config['transDir'];
        $this->cronJobScript = realpath(
            dirname(__FILE__).
            '/../../storageServer/var/cron/transportCronJob.php'
        );
    }

    /* ==================================================== transport methods */
    /* ------------------------------------------------------- common methods */
    /**
     *  Common "check" method for transports
     *
     *  @param trtok: string - transport token
     *  @return struct/hasharray with fields:
     *      trtype: string -
     *          audioclip | playlist | playlistPkg | search | metadata | file
     *      state: string - transport state
     *                  init | pending | waiting | finished | closed | failed
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
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $res = array();
        foreach(array(
            'trtype', 'state', 'direction', 'expectedsize', 'realsize',
            'expectedchsum', 'realchsum', 'title', 'errmsg'               
        ) as $k){
            $res[$k] = ( isset($trec->row[$k]) ? $trec->row[$k] : NULL );
        }
        return $res;
    }
    
    /**
     *  Turn transports on/off, optionaly return current state.
     *  (true=On / false=off)
     *
     *  @param sessid string: session id
     *  @param onOff: boolean optional (if not used, current state is returned)
     *  @return boolean - previous state
     */
    function turnOnOffTransports($sessid, $onOff=NULL)
    {
        require_once 'Prefs.php';
        $pr =& new Prefs($this->gb);
        $group  = 'StationPrefs';
        $key    = 'TransportsDenied';
        $res = $r = $pr->loadGroupPref($sessid, $group, $key);
        if(PEAR::isError($r)){
            if($r->getCode() !== GBERR_PREF) return $r;
            else $res = FALSE;  // default
        }
        $state = !$res;
        if(is_null($onOff)) return $state;
        $res = $r = $pr->saveGroupPref($sessid, $group, $key, !$onOff);
        if(PEAR::isError($r)){ return $r; }
        return $state;
    }

    /**
     *  Pause, resume or cancel transport
     *
     *  @param trtok: string - transport token
     *  @param action: string - pause | resume | cancel
     *  @return string - resulting transport state
     */
    function doTransportAction($trtok, $action)
    {
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        switch($action){
            case'pause';
                $newState = 'paused';
                break;
            case'resume';
                $newState = 'waiting';
                break;
            case'cancel';
                $newState = 'closed';
                break;
            default:
        }
        $res = $trec->setState($newState);
        return $res;
    }
    
    /* ------------- special methods for audioClip/webstream object transport */

    /**
     *  Start upload of audioClip/webstream/playlist from local storageServer
     *  to hub
     *
     *  @param gunid: string - global unique id of object being transported
     *  @param withContent: boolean - if true, transport playlist content too
     *          (optional)
     *  @param pars: array - default parameters (optional, internal use)
     *  @return string - transport token
     */
    function upload2Hub($gunid, $withContent=TRUE, $pars=array())
    {
        switch($ftype = $this->gb->_getType($gunid)){
        case"audioclip":
        case"webstream":
            $ac =& StoredFile::recallByGunid($this->gb, $gunid);
            if(PEAR::isError($ac)) return $ac;
            // handle metadata:
            $mdfpath = $r = $ac->_getRealMDFname();
            if(PEAR::isError($r)) return $r;
            $mdtrec = $r = $this->_uploadGenFile2Hub($mdfpath, 'metadata',
                array_merge(array('gunid'=>$gunid, 'fname'=>'metadata',), $pars)
            );
            if(PEAR::isError($r)){ return $r; }
            // handle raw media file:
            $fpath = $r = $ac->_getRealRADFname();
            if(PEAR::isError($r)) return $r;
            $fname = $r = $ac->_getFileName();
            if(PEAR::isError($r)) return $r;
            $trec = $r = $this->_uploadGenFile2Hub($fpath, 'audioclip',
                array_merge(array(
                    'gunid'=>$gunid, 'fname'=>$fname, 'mdtrtok'=>$mdtrec->trtok,
                ), $pars)
            );
            if(PEAR::isError($r)){ return $r; }
            $this->startCronJobProcess($mdtrec->trtok);
            break;
        case"playlist":
            $plid = $gunid;
            require_once "Playlist.php";
            $pl = Playlist::recallByGunid($this->gb, $plid);
            if(PEAR::isError($pl)){ return $pl; }
            $fname = $r = $pl->_getFileName();
            if(PEAR::isError($r)) return $r;
            if($withContent){
                $res = $r = $this->gb->bsExportPlaylistOpen($plid);
                if(PEAR::isError($r)) return $r;
                $tmpn = tempnam($this->transDir, 'plExport_');
                $plfpath = "$tmpn.lspl";
                copy($res['fname'], $plfpath);
                $res = $r = $this->gb->bsExportPlaylistClose($res['token']);
                if(PEAR::isError($r)) return $r;
                $fname = $fname.".lspl";
                $trtype = 'playlistPkg';
            }else{
                $plfpath = $r = $pl->_getRealMDFname();
                if(PEAR::isError($r)) return $r;
                $trtype = 'playlist';
            }
            $trec = $r = $this->_uploadGenFile2Hub($plfpath, $trtype,
                array_merge(array('gunid'=>$plid,'fname'=>$fname,), $pars)
            );
            if(PEAR::isError($r)){ return $r; }
            break;
        default:
            return PEAR::raiseError(
                "Transport::upload2Hub:".
                " ftype not supported ($ftype)"
            );
        }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }

    /**
     *  Start download of audioClip/webstream/playlist from hub to local
     *  storageServer
     *
     *  @param uid: int - local user id of transport owner
     *      (for downloading file to homedir in storage)
     *  @param gunid: string - global unique id of object being transported
     *  @param withContent: boolean - if true, transport playlist content too
     *          (optional)
     *  @param pars: array - default parameters (optional, internal use)
     *  @return string - transport token
     */
    function downloadFromHub($uid, $gunid, $withContent=TRUE, $pars=array())
    {
#    $this->trLog(var_export($gunid, TRUE));
        $trtype = ($withContent ? 'playlistPkg' : 'unknown' );
        $trec = $r = TransportRecord::create($this, $trtype, 'down',
            array_merge(array('gunid'=>$gunid, 'uid'=>$uid), $pars)
/* merged !???
            array(
                'trtok' =>  '123456789abcdef2',
            ),
            array(
                'trtok' =>  '123456789abcdef3',
            ),
            array(
                'trtok' =>  '123456789abcdef4',
            ),
*/
        );
        if(PEAR::isError($r)){ return $r; }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }

    /* ------------------------------------------------ global-search methods */
    /**
     *  Start search job on network hub
     *
     *  @param criteria: LS criteria format (see localSearch)
     *  @param resultMode: string - 'php' | 'xmlrpc' (optional)
     *  @param pars: array - default parameters (optional, internal use)
     *  @return string - transport token
     */
    function globalSearch($criteria, $resultMode='php', $pars=array())
    {
        $criteria['resultMode'] = $resultMode;
        $localfile = tempnam($this->transDir, 'searchjob_');
        @chmod($localfile, 0660);
        $len = $r = file_put_contents($localfile, serialize($criteria));
        $trec = $r = $this->_uploadGenFile2Hub($localfile, 'searchjob', $pars);
        if(PEAR::isError($r)){ return $r; }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }

    /**
     *  Get results from search job on network hub
     *
     *  @param trtok: string - transport token
     *  @return : LS search result format (see localSearch)
     */
    function getSearchResults($trtok)
    {
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $row = $trec->row;
        switch($st = $trec->getState()){
            case"failed":
                return PEAR::raiseError(
                    "Transport::getSearchResults:".
                    " global search or results transport failed".
                    " ({$trec->row['errmsg']})"
                );
                break;
            case"closed":
                return PEAR::raiseError(
                    "Transport::getSearchResults:".
                    " invalid transport token ($trtok)", TRERR_TOK
                );
                break;
            case"finished":
                if($row['direction']=='down') break;    // really finished
                                    // otherwise only request upload finished
            default:
                return PEAR::raiseError(
                    "Transport::getSearchResults: not finished ($st)",
                    TRERR_NOTFIN
                );
        }
        $res = file_get_contents($row['localfile']);
        $results = unserialize($res);
        @unlink($row['localfile']);
        $r = $trec->close();
        if(PEAR::isError($r)){ return $r; }
        return $results;
    }

    /* ------------------------ methods for ls-archive-format file transports */
    /**
     *  Open async file transfer from local storageServer to network hub,
     *  file should be ls-archive-format file.
     *
     *  @param filePath string - local path to uploaded file
     *  @param pars: array - default parameters (optional, internal use)
     *  @return string - transport token
     */
    function uploadFile2Hub($filePath, $pars=array())
    {
        if(!file_exists($filePath)){
            return PEAR::raiseError(
                "Transport::uploadFile2Hub: file not found ($filePath)"
            );
        }
        $trec = $r = $this->_uploadGenFile2Hub($filePath, 'file', $pars);
        if(PEAR::isError($r)){ return $r; }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }

    /**
     *  Open async file transfer from network hub to local storageServer,
     *  file should be ls-archive-format file.
     *
     *  @param url: string - readable url
     *  @param chsum: string - checksum from remote side (optional)
     *  @param size: int - filesize from remote side (otional)
     *  @param pars: array - default parameters (optional, internal use)
     *  @return hasharray:
     *      trtok: string - transport token
     *      localfile: string - filepath of downloaded file
     */
    function downloadFileFromHub($url, $chsum=NULL, $size=NULL, $pars=array())
    {
        $tmpn = tempnam($this->transDir, 'HITrans_');
        $trec = $r = TransportRecord::create($this, 'file', 'down',
            array_merge(array(
                'url'           => $url,
                'localfile'     => $tmpn,
                'expectedsum'   => $chsum,
                'expectedsize'  => $size,
            ), $pars)
        );
        if(PEAR::isError($r)){ return $r; }
        $this->startCronJobProcess($trec->trtok);
        return array('trtok'=>$trec->trtok, 'localfile'=>$tmpn);
    }

    /**
     *  Get list of prepared transfers initiated by hub
     *
     *  @return array of structs/hasharrays with fields:
     *      trtok: string transport token
     */
    function getHubInitiatedTransfers()
    {
        $ret = $r = $this->xmlrpcCall(
            'archive.listHubInitiatedTransfers',
            array(
                'target'    => HOSTNAME,
            )
        );
        if(PEAR::isError($r)){ return $r; }
        $res = array();
        foreach($ret as $it){
            $res[] = array('trtok'=>$it['trtok']);
        }
        return $res;
    }

    /**
     *  Start of download initiated by hub
     *
     *  @param uid: int - local user id of transport owner
     *      (for downloading file to homedir in storage)
     *  @param rtrtok: string - transport token obtained from
     *          the getHubInitiatedTransfers method
     *  @return string - transport token
     */
    function startHubInitiatedTransfer($uid, $rtrtok)
    {
        $ret = $r = $this->xmlrpcCall(
            'archive.listHubInitiatedTransfers',
            array(
                'target'    => HOSTNAME,
                'trtok'     => $rtrtok,
            )
        );
        if(PEAR::isError($r)){ return $r; }
        if(count($ret)!=1){
            return PEAR::raiseError(
                "Transport::startHubInitiatedTransfer:".
                " wrong number of transports (".count($ret).")"
            );
        }
        $ta = $ret[0];
        // direction invertation to locstor point of view:
        $direction = ( $ta['direction']=='up' ? 'down' : 'up' );
        $gunid  = $ta['gunid'];
        switch($direction){
        case"up":
            switch($ta['trtype']){
            case"audioclip":
            case"playlist":
            case"playlistPkg":
                $trtok = $r = $this->upload2Hub($gunid, TRUE,
                    array('rtrtok'=>$rtrtok));
                if(PEAR::isError($r)){ return $r; }
                break;
            //case"searchjob":  break;  // not supported yet
            //case"file":   break;      // probably unusable
            default:
                return PEAR::raiseError(
                    "Transport::startHubInitiatedTransfer:".
                    " wrong direction / transport type combination".
                    " ({$ta['direction']}/{$ta['trtype']})"
                );
            }
            break;
        case"down":
            switch($ta['trtype']){
            case"audioclip":
            case"playlist":
            case"playlistPkg":
                $trtok = $r = $this->downloadFromHub($uid, $gunid, TRUE,
                    array('rtrtok'=>$rtrtok));
                if(PEAR::isError($r)){ return $r; }
                break;
            //case"searchjob":    break;    // probably unusable
            case"file":
                $r = $this->downloadFileFromHub(
                    $ta['url'], $ta['expectedsum'], $ta['expectedsize'],
                        array('rtrtok'=>$rtrtok));
                if(PEAR::isError($r)){ return $r; }
                extract($r);    // trtok, localfile
                break;
            default:
                return PEAR::raiseError(
                    "Transport::startHubInitiatedTransfer:".
                    " wrong direction / transport type combination".
                    " ({$ta['direction']}/{$ta['trtype']})"
                );
            }
            break;
        default:
            return PEAR::raiseError(
                "Transport::startHubInitiatedTransfer: ???"
            );
        }
        $ret = $r = $this->xmlrpcCall(
            'archive.setHubInitiatedTransfer',
            array(
                'target'    => HOSTNAME,
                'trtok'     => $rtrtok,
                'state'     => 'waiting',
            )
        );
        if(PEAR::isError($r)){ return $r; }
        $this->startCronJobProcess($trtok);
        return $trtok;
    }


    /* =============================================== authentication methods */

    /**
     *  Login to archive server
     *  (account info is taken from storageServer's config)
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
    /**
     *  Main method for periodical transport tasks - called by cron
     *
     *  @param direction: string, optional
     *  @return boolean TRUE
     */
    function cronMain($direction=NULL)
    {
        if(is_null($direction)){
            $r = $this->cronMain('up');
            if(PEAR::isError($r)){ return $r; }
            $r = $this->cronMain('down');
            if(PEAR::isError($r)){ return $r; }
            return TRUE;
        }
        // fetch all opened transports
        $transports = $r = $this->getTransports($direction);
        if(PEAR::isError($r)){ $this->trLog("cronMain: DB error"); return FALSE; }
        if(count($transports)==0){
            if(TR_LOG_LEVEL>1){
                $this->trLog("cronMain: $direction - nothing to do."); }
            return TRUE;
        }
        // ping to archive server:
        $r = $this->pingToArchive();
        chdir($this->config['transDir']);
        // for all opened transports:
        foreach($transports as $i=>$row){
            $r = $this->startCronJobProcess($row['trtok']);
        } // foreach transports
        return TRUE;
    }

    /**
     *  Cron job process starter
     *
     *  @param trtok: string - transport token
     *  @return boolean status
     */
    function startCronJobProcess($trtok)
    {
        if(TR_LOG_LEVEL>2) $redirect = "{$this->transDir}/debug.log";
        else $redirect = "/dev/null";
        $command = "{$this->cronJobScript} {$trtok} >> $redirect &";
        $res = system($command, $status);
        if($res===FALSE){
            $this->trLog(
                "cronMain: Error on execute cronJobScript with trtok {$trtok}"
            );
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     *  Dynamic method caller - wrapper
     *
     *  @param trtok: string - transport token
     *  @return inherited from called method
     */
    function cronCallMethod($trtok)
    {
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $row = $trec->row;
        $state = $row['state'];

        $states = array('init'=>'init', 'pending'=>'pending',
            'waiting'=>'waiting', 'finished'=>'finished', 'failed'=>'failed',
            'closed'=>'closed');
        $directions = array('up'=>'upload', 'down'=>'download');
        // method name construction:
        $mname = "cron";
        if(isset($directions[$row['direction']])){
            $mname .= ucfirst($directions[$row['direction']]);
        }else{
            return PEAR::raiseError(
                "Transport::cronCallMethod: invalid direction ({$row['direction']})"
            );
        }
        if(isset($states[$state])){
            $mname .= ucfirst($states[$state]);
        }else{
            return PEAR::raiseError(
                "Transport::cronCallMethod: invalid state ({$state})"
            );
        }
        switch($state){
            // do nothing if closed, penfing or failed:
            case'closed':   // excluded in SQL query too, but let check it here
            case'failed':   // -"-
            case'pending':
            case'paused':
                return TRUE;
                break;
            case'waiting':
                require_once 'Prefs.php';
                $pr =& new Prefs($this->gb);
                $group  = 'StationPrefs';
                $key    = 'TransportsDenied';
                $res = $r = $pr->loadGroupPref(NULL/*sessid*/, $group, $key);
                if(PEAR::isError($r)){
                    if($r->getCode() !== GBERR_PREF) return $r;
                    else $res = FALSE;  // default
                }
                // transfers turned off
                // if($res){ return TRUE; break; }
                if($res){
                    return PEAR::raiseError(
                        "Transport::cronCallMethod: transfers turned off"
                    );
                }
                // NO break here!
            default:
                if(method_exists($this, $mname)){

                    // lock the job:
                    $r = $trec->setLock(TRUE);
                    if(PEAR::isError($r)){ return $r; }
                    $trec = $r = TransportRecord::recall($this, $trtok);
                    if(PEAR::isError($r)){
                        $r2 = $trec->setLock(FALSE);
                        return $r;
                    }
                    $row = $trec->row;
                    $state = $row['state'];

                    // login to archive server:
                    $r = $this->loginToArchive();
                    if(PEAR::isError($r)){
                        $r2 = $trec->setLock(FALSE);
                        return $r;
                    }
                    $asessid = $r['sessid'];
                    // method call:
                    if(TR_LOG_LEVEL>2){
                        $this->trLog("cronCallMethod: $mname($trtok) >"); }
                    $ret = call_user_func(array($this, $mname), $row, $asessid);
                    if(PEAR::isError($ret)){
                        $r = $trec->setLock(FALSE);
                        return $this->_failFatal($ret, $trec);
                    }
                    if(TR_LOG_LEVEL>2){
                        $this->trLog("cronCallMethod: $mname($trtok) <"); }
                    // unlock the job:
                    $r = $trec->setLock(FALSE);
                    if(PEAR::isError($r)){ return $r; }
                    // logout:
                    $r = $this->logoutFromArchive($asessid);
                    if(PEAR::isError($r)){
                        return $r;
                    }
                    return $ret;
                }else{ 
                    return PEAR::raiseError(
                        "Transport::cronCallMethod: unknown method ($mname)"
                    );
                }
        }
    }

    /**
     *  Upload initialization
     *
     *  @param row: array - row from getTransport results
     *  @param asessid: string - session id (from network hub)
     *  @return boolean TRUE or error object
     */
    function cronUploadInit($row, $asessid)
    {
        $trtok = $row['trtok'];
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $ret = $r = $this->xmlrpcCall( 'archive.uploadOpen',
            array(
               'sessid'     => $asessid ,
               'chsum'      => $row['expectedsum'],
            )
        );
        if(PEAR::isError($r)){ return $r; }
        $r = $trec->setState('waiting',
            array('url'=>$ret['url'], 'pdtoken'=>$ret['token']));
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /**
     *  Download initialization
     *
     *  @param row: array - row from getTransport results
     *  @param asessid: string - session id (from network hub)
     *  @return boolean TRUE or error object
     */
    function cronDownloadInit($row, $asessid)
    {
        $trtok = $row['trtok'];
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $ret = $r = $this->xmlrpcCall(
            'archive.downloadOpen',
            array(
                'sessid'=> $asessid,
                'trtype'=> $row['trtype'],
                'pars'=>array(
                    'gunid' => $row['gunid'],
                    'token' => $row['pdtoken'],
                ),
            )
        );
        if(PEAR::isError($r)){ return $r; }
        $trtype = $ret['trtype'];
        $title = $ret['title'];
        $pars = array();
        switch($trtype){
        case"searchjob":
            $r = $trec->setState('waiting', $pars);
            break;
        case"file":
            $r = $trec->setState('waiting',array_merge($pars, array(
                'trtype'=>$trtype,
                'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
                'expectedsum'=>$ret['chsum'], 'expectedsize'=>$ret['size'],
                'fname'=>$ret['filename'],
                'localfile'=>"{$this->transDir}/$trtok",
            )));
            break;
        case"audioclip":
            $mdtrec = $r = TransportRecord::create($this, 'metadata', 'down',
                array('gunid'=>$row['gunid'], 'uid'=>$row['uid'], )
            );
            if(PEAR::isError($r)){ return $r; }
            $this->startCronJobProcess($mdtrec->trtok);
            $pars = array('mdtrtok'=>$mdtrec->trtok);
            // NO break here !
        default:
            $r = $trec->setState('waiting',array_merge($pars, array(
                'trtype'=>$trtype,
                'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
                'expectedsum'=>$ret['chsum'], 'expectedsize'=>$ret['size'],
                'fname'=>$ret['filename'], 'title'=>$title,
                'localfile'=>"{$this->transDir}/$trtok",
            )));
        }
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /**
     *  Upload next part of transported file
     *
     *  @param row: array - row from getTransport results
     *  @param asessid: string - session id (from network hub)
     *  @return boolean TRUE or error object
     */
    function cronUploadWaiting($row, $asessid)
    {
        $trtok = $row['trtok'];
        $check = $r = $this->uploadCheck($row['pdtoken']);
        if(PEAR::isError($r)) return $r;
        // test filesize
        if(!file_exists($row['localfile'])){
            return PEAR::raiseError("Transport::cronUploadWaiting:".
                " file beeing uploaded not exists! ({$row['localfile']})"
            );
        }
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $size       = escapeshellarg($check['size']);
        $localfile  = escapeshellarg($row['localfile']);
        $url        = escapeshellarg($row['url']);
        $command =
            "curl -s -C $size --max-time {$this->upTrMaxTime}".
            " --speed-time {$this->upTrSpeedTime}".
            " --speed-limit {$this->upTrSpeedLimit}".
            " --connect-timeout {$this->upTrConnectTimeout}".
            (!is_null($this->upLimitRate)?
                " --limit-rate {$this->upLimitRate}" : "").
            " -T $localfile $url"
        ;
        $r = $trec->setState('pending', array(), 'waiting');
        if(PEAR::isError($r)){ return $r; }
        if($r === FALSE) return TRUE;
        $res = system($command, $status);
        // status 18 - Partial file. Only a part of the file was transported.
        if($status == 0 || $status == 18){
            $check = $this->uploadCheck($row['pdtoken']);
            if(PEAR::isError($check)) return $check;
            // test checksum
            if($check['status'] == TRUE){
                // finished
                $r = $trec->setState('finished',
                    array('realsum'=>$check['realsum'], 'realsize'=>$check['size']));
                if(PEAR::isError($r)){ return $r; }
            }else{
                if(intval($check['size']) < $row['expectedsize']){
                    $r = $trec->setState('waiting',
                        array('realsum'=>$check['realsum'], 'realsize'=>$check['size']));
                    if(PEAR::isError($r)){ return $r; }
                }else{
                    // wrong md5 at finish - TODO: start again
                    // $this->xmlrpcCall('archive.uploadReset', array());
                    $trec->fail('file uploaded with bad md5');
                    return PEAR::raiseError("Transport::cronUploadWaiting:".
                        " file uploaded with bad md5 ".
                        "($trtok: {$check['realsum']}/{$check['expectedsum']})"
                    );
                }
            }
        }else{
            return PEAR::raiseError("Transport::cronUploadWaiting:".
                " wrong return status from curl: $status ".
                "($trtok)"
            );
        }
        return TRUE;
    }

    /**
     *  Download next part of transported file
     *
     *  @param row: array - row from getTransport results
     *  @param asessid: string - session id (from network hub)
     *  @return boolean TRUE or error object
     */
    function cronDownloadWaiting($row, $asessid)
    {
        $trtok = $row['trtok'];
        // wget the file
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        $localfile  = escapeshellarg($row['localfile']);
        $url        = escapeshellarg($row['url']);
        $command =
            "wget -q -c".
            " --timeout={$this->downTimeout}".
            " --waitretry={$this->downWaitretry}".
            " -t {$this->downRetries}".
            (!is_null($this->downLimitRate)?
                " --limit-rate={$this->downLimitRate}" : "").
            " -O $localfile $url"
        ;
        $r = $trec->setState('pending', array(), 'waiting');
        if(PEAR::isError($r)){ return $r; }
        if($r === FALSE) return TRUE;
        $res = system($command, $status);
        // check consistency
        $size   = filesize($row['localfile']);
        if($status == 0 || ($status == 1 && $size >= $row['expectedsize'])){
            $chsum  = $this->_chsum($row['localfile']);
            if($chsum == $row['expectedsum']){
                // mark download as finished
                $r = $trec->setState('finished',
                    array('realsum'=>$chsum, 'realsize'=>$size));
                if(PEAR::isError($r)){ return $r; }
            }else{
                // bad checksum
                @unlink($row['localfile']);
                $r = $trec->setState('waiting',
                    array('realsum'=>$chsum, 'realsize'=>$size));
                if(PEAR::isError($r)){ return $r; }
            }
        }else{
            return PEAR::raiseError("Transport::cronDownloadWaiting:".
                " wrong return status from wget: $status ".
                "($trtok)"
            );
        }
        return TRUE;
    }
    
    /**
     *  Finish the upload
     *
     *  @param row: array - row from getTransport results
     *  @param asessid: string - session id (from network hub)
     *  @return boolean TRUE or error object
     */
    function cronUploadFinished($row, $asessid)
    {
        $trtok = $row['trtok'];
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        // don't close metadata transport - audioclip will close it
        if($row['trtype'] == 'metadata') return TRUE;
        // handle metadata transport on audioclip trtype:
        if($row['trtype'] == 'audioclip'){
            $mdtrec = $r = TransportRecord::recall($this, $trec->row['mdtrtok']);
            if(PEAR::isError($r)){ return $r; }
            switch($mdtrec->row['state']){
                case 'failed': 
                case 'closed':
                    return PEAR::raiseError("Transport::cronUploadFinished:".
                        " metadata transport in wrong state: {$mdtrec->row['state']}".
                        " ({$this->trtok})"
                    );
                    break;
                // don't close transport with nonfinished metadata transport:
                case 'init':
                case 'waiting':
                case 'pending':
                case 'paused':
                    return TRUE;
                default:    // finished - ok close parent transport
                    $mdpdtoken = $mdtrec->row['pdtoken'];
            }
        }else $mdpdtoken = NULL;
        $ret = $r = $this->xmlrpcCall( 'archive.uploadClose',
            array(
                'token'     => $row['pdtoken'] ,
                'trtype'      => $row['trtype'],
                'pars'      => array(
                    'gunid'     => $row['gunid'],
                    'name'      => $row['fname'],
                    'mdpdtoken' => $mdpdtoken,
                ),
            )
        );
        if(PEAR::isError($r)){
            if($row['trtype'] == 'audioclip'){
                $r2 = $mdtrec->close();
            }
            return $r;
        }

        if($row['trtype'] == 'searchjob'){
            @unlink($row['localfile']);
            $r = $trec->setState('init', array(
                'direction'     => 'down',
                'pdtoken'       => $ret['token'],
                'expectedsum'   => $ret['chsum'],
                'expectedsize'  => $ret['size'],
                'url'           => $ret['url'],
                'realsize'      => 0,
            ));
            $this->startCronJobProcess($trec->trtok);
        }else{
            $r = $trec->close();
        }
        if(PEAR::isError($r)){ return $r; }
        switch($row['trtype']){
            case 'audioclip':
                // close metadata transport:
                $r = $mdtrec->close();
                if(PEAR::isError($r)){ return $r; }
                break;
            case 'playlistPkg':
                // remove exported playlist (playlist with content)
                $ep = $row['localfile'];
                @unlink($ep);
                if(preg_match("|/(plExport_[^\.]+)\.lspl$|", $ep, $va)){
                    list(,$tmpn) = $va; $tmpn = "{$this->transDir}/$tmpn";
                    if(file_exists($tmpn)) @unlink($tmpn);  
                }

                break;
            default:
        }
        
        return TRUE;
    }
    
    /**
     *  Finish the download
     *
     *  @param row: array - row from getTransport results
     *  @param asessid: string - session id (from network hub)
     *  @return boolean TRUE or error object
     */
    function cronDownloadFinished($row, $asessid)
    {
        $trtok = $row['trtok'];
        $trec = $r = TransportRecord::recall($this, $trtok);
        if(PEAR::isError($r)){ return $r; }
        switch($row['trtype']){
            case"audioclip":
                $mdtrtok = $trec->row['mdtrtok'];
                $mdtrec = $r = TransportRecord::recall($this, $mdtrtok);
                if(PEAR::isError($r)){ return $r; }
                $r = $mdtrec->setLock(TRUE);
                if(PEAR::isError($r)){ return $r; }
                switch($mdtrec->row['state']){
                    // don't close transport with nonfinished metadata transport:
                    case 'init':
                    case 'waiting':
                    case 'pending':
                    case 'paused':
                        $r = $mdtrec->setLock(FALSE);
                        if(PEAR::isError($r)){ return $r; }
                        return TRUE;
                        break;
                    case 'finished':  // metadata finished, close main transport
                        $parid = $r = $this->gb->_getHomeDirId($trec->row['uid']);
                        if($this->dbc->isError($r)){
                            $r2 = $mdtrec->setLock(FALSE);
                            return $r;
                        }
                        $res = $r = $this->gb->bsPutFile($parid, $row['fname'],
                            $trec->row['localfile'], $mdtrec->row['localfile'],
                            $row['gunid'], 'audioclip', 'file');
                        if($this->dbc->isError($r)){
                            $r2 = $mdtrec->setLock(FALSE);
                            return $r;
                        }
                        $ret = $r = $this->xmlrpcCall(
                            'archive.downloadClose',
                            array(
                               'token'      => $mdtrec->row['pdtoken'] ,
                               'trtype'     => 'metadata' ,
                            )
                        );
                        if(PEAR::isError($r)){
                            $r2 = $mdtrec->setLock(FALSE);
                            return $r;
                        }
                        $r = $mdtrec->close();
                        if(PEAR::isError($r)){
                            $r2 = $mdtrec->setLock(FALSE);
                            return $r;
                        }
                        @unlink($trec->row['localfile']);
                        @unlink($mdtrec->row['localfile']);
                        break;
                    default:
                        $r = $mdtrec->setLock(FALSE);
                        return PEAR::raiseError("Transport::cronDownloadFinished:".
                            " metadata transport in wrong state: {$mdtrec->row['state']}".
                            " ({$this->trtok})"
                        );
                        break;
                }
                $r = $mdtrec->setLock(FALSE);
                if(PEAR::isError($r)){ return $r; }
                break;
            case"metadata":
            case"searchjob":
                return TRUE;     // don't close
                break;
        }
        $ret = $r = $this->xmlrpcCall(
            'archive.downloadClose',
            array(
               'token'     => $row['pdtoken'] ,
               'trtype'     => $row['trtype'] ,
            )
        );
        if(PEAR::isError($r)){ return $r; }
        switch($row['trtype']){
            case"playlist":
                $parid = $r = $this->gb->_getHomeDirId($trec->row['uid']);
                if($this->dbc->isError($r)) return $r;
                $res = $r = $this->gb->bsPutFile($parid, $row['fname'],
                    '', $trec->row['localfile'],
                    $row['gunid'], 'playlist', 'file');
                if($this->dbc->isError($r)) return $r;
                @unlink($row['localfile']);
                break;
            case"playlistPkg":
                $subjid = $trec->row['uid'];
                $fname = $trec->row['localfile'];
                $parid = $r = $this->gb->_getHomeDirId($subjid);
                if($this->dbc->isError($r)) return $r;
                $res = $r = $this->gb->bsImportPlaylist($parid, $fname, $subjid);
                if($this->dbc->isError($r)) return $r;
                @unlink($fname);
                break;
            case"audioclip":
            case"metadata":
            case"searchjob":
            case"file":
                break;
            default:
                return PEAR::raiseError("DEBUG: NotImpl ".var_export($row,TRUE));
        }
        if(!is_null($rtrtok = $trec->row['rtrtok'])){
            $ret = $r = $this->xmlrpcCall(
                'archive.setHubInitiatedTransfer',
                array(
                    'target'    => HOSTNAME,
                    'trtok'     => $rtrtok,
                    'state'     => 'closed',
                )
            );
            if(PEAR::isError($r)){ return $r; }
        }
        $r = $trec->close();
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /* ==================================================== auxiliary methods */
    /**
     *  Prepare upload for general file
     *
     *  @param fpath: string - local filepath of uploaded file
     *  @param trtype: string - transport type
     *  @param pars: array - default parameters (optional, internal use)
     *  @return object - transportRecord instance
     */
    function _uploadGenFile2Hub($fpath, $trtype, $pars=array())
    {
        $chsum = $this->_chsum($fpath);
        $size  = filesize($fpath);
        $trec = $r = TransportRecord::create($this, $trtype, 'up',
            array_merge(array(
                'localfile'=>$fpath, 'fname'=>basename($fpath),
                'expectedsum'=>$chsum, 'expectedsize'=>$size
            ), $pars)
        );
        if(PEAR::isError($r)){ return $r; }
        return $trec;
    }

    /**
     *  Create new transport token
     *
     *  @return string - transport token
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
     *  Get all relevant transport records
     *
     *  @param direction: string - 'up' | 'down'
     *  @param target: string - target hostname
     *  @param trtok: string - transport token for specific query
     *  @return array of transportRecords (as hasharrays)
     */
    function getTransports($direction=NULL, $target=NULL, $trtok=NULL)
    {
        switch($direction){
            case 'up':  $dirCond = "direction='up' AND"; break;
            case 'down':  $dirCond = "direction='down' AND"; break;
            default: $dirCond = ''; break;
        }
        if(is_null($target))    $targetCond = "";
        else    $targetCond = "target='$target' AND";
        if(is_null($trtok))    $trtokCond = "";
        else    $trtokCond = "trtok='$trtok' AND";
        $rows = $this->dbc->getAll("
            SELECT
                id, trtok, state, trtype, direction,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, expectedsum, expectedsize, url,
                uid, target
            FROM {$this->transTable}
            WHERE $dirCond $targetCond $trtokCond
                    state not in ('closed', 'failed', 'paused')
            ORDER BY start DESC
        ");
        if(PEAR::isError($rows)){ return $rows; }
        foreach($rows as $i=>$row){
            $rows[$i]['pdtoken'] = StoredFile::_normalizeGunid($row['pdtoken']);
            $rows[$i]['gunid'] = StoredFile::_normalizeGunid($row['gunid']);
        }
        return $rows;
    }

    /**
     *  Check remote state of uploaded file
     *
     *  @param pdtoken: string, put/download token (from network hub)
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

    /**
     *  Ping to archive server
     *
     *  @return string - network hub response or error object
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
     *  XMLRPC call to network hub
     *
     *  @param method: string - method name
     *  @param pars: hasharray - call parameters
     *  @return mixed value - response
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
        $r = $c->send($f);
        if (!$r) {
            return PEAR::raiseError("XML-RPC request failed", TRERR_XR_FAIL);
        }elseif ($r->faultCode()>0) {
            return PEAR::raiseError($r->faultString(), $r->faultCode());
            // return PEAR::raiseError($r->faultString().
            //    " (code ".$r->faultCode().")", TRERR_XR_FAIL);
        }else{
            $v = $r->value();
            return XML_RPC_decode($v);
        }
    }

    /**
     *  Checksum of local file
     *
     *  @param fpath: string - local filepath
     *  @return string - checksum
     */
    function _chsum($fpath)
    {
        return md5_file($fpath);
    }
    
    /**
     *  Check exception and eventually mark transport as failed
     *
     *  @param res: mixed - result object to be checked
     *  @param trec: transport record object
     *  @return 
     */
    function _failFatal($res, $trec)
    {
        if(PEAR::isError($res)){
            switch($res->getCode()){
                // non fatal:
                case TRERR_XR_FAIL:
                    break;
                // fatal:
                default:
                    $trec->fail('', $res);
            }
        }
        return $res;
    }

    /**
     *  Clean up transport jobs
     *
     *  @param interval: string - psql time interval - older closed jobs
     *          will be deleted
     *  @param forced: boolean - if true, delete non-closed jobs too
     *  @return boolean true or error
     */
    function _cleanUp($interval='1 minute'/*'1 hour'*/, $forced=FALSE)
    {
        $cond = ($forced ? '' : " AND state='closed' AND lock = 'N'");
        $r = $this->dbc->query("
            DELETE FROM {$this->transTable}
            WHERE ts < now() - interval '$interval'".$cond
        );
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }
    
    /**
     *  logging wrapper for PEAR error object
     *
     *  @param txt: string - log message
     *  @param eo: PEAR error object
     *  @param row: array returned from getRow
     *  @return void or error object
     */
    function trLogPear($txt, $eo, $row=NULL)
    {
        $msg = $txt.$eo->getMessage()." ".$eo->getUserInfo().
            " [".$eo->getCode()."]";
        if(!is_null($row)){
            $trec = $r = TransportRecord::recall($this, $row['trtok']);
            if(!PEAR::isError($r)){
                $r = $trec->setState('failed', array('errmsg'=>$msg));
            }
            $msg .= "\n    ".serialize($row);
        }
        $this->trLog($msg);
    }

    /**
     *  logging for debug transports
     *
     *  @param msg string - log message
     *  @return void or error object
     */
    function trLog($msg)
    {
        $logfile = "{$this->transDir}/activity.log";
        if(FALSE === ($fp = fopen($logfile, "a"))){
            return PEAR::raiseError(
                "Transport::trLog: Can't write to log ($logfile)"
            );
        }
        flock($fp,LOCK_SH);                                                                                                                                       
        fputs($fp, "---".date("H:i:s")."---\n $msg\n");
        flock($fp,LOCK_UN);                                                                                                                                       
        fclose($fp);
    }

    /* ====================================================== install methods */
    /**
     *  Delete all transports
     *
     *  @return void or error object
     */
    function resetData()
    {
        return $this->dbc->query("DELETE FROM {$this->transTable}");
    }

    /**
     *  Install method<br>
     *
     *  direction: up | down
     *  state: init | pending | waiting | finished | closed | failed | paused
     *  trtype: audioclip | playlist | playlistPkg | searchjob | metadata | file
     *  
     */
    function install()
    {
        $r = $this->dbc->query("CREATE TABLE {$this->transTable} (
            id int not null,          -- primary key
            trtok char(16) not null,  -- transport token
            direction varchar(128) not null,  -- direction: up|down
            state varchar(128) not null,      -- state
            trtype varchar(128) not null,     -- transport type
            lock char(1) not null default 'N',-- running lock
            target varchar(255) default NULL, -- target system,
                                              -- if NULL => predefined set
            rtrtok char(16) default NULL,     -- remote hub's transport token
            mdtrtok char(16),         -- metadata transport token
            gunid bigint,             -- global unique id
            pdtoken bigint,           -- put/download token from archive
            url varchar(255),         -- url on remote side
            localfile varchar(255),   -- pathname of local part
            fname varchar(255),       -- mnemonic filename
            title varchar(255),       -- dc:title mdata value (or filename ...)
            expectedsum char(32),     -- expected file checksum
            realsum char(32),         -- checksum of transported part
            expectedsize int,         -- expected filesize in bytes
            realsize int,             -- filesize of transported part
            uid int,                  -- local user id of transport owner
            errmsg varchar(255),      -- error message string for failed tr.
            start timestamp,          -- starttime
            ts timestamp              -- mtime
        )");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
        $r = $this->dbc->createSequence("{$this->transTable}_id_seq");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
        $r = $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_id_idx
            ON {$this->transTable} (id)");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
        $r = $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_trtok_idx
            ON {$this->transTable} (trtok)");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
        $r = $this->dbc->query("CREATE UNIQUE INDEX {$this->transTable}_token_idx
            ON {$this->transTable} (pdtoken)");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
        $r = $this->dbc->query("CREATE INDEX {$this->transTable}_gunid_idx
            ON {$this->transTable} (gunid)");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
        $r = $this->dbc->query("CREATE INDEX {$this->transTable}_state_idx
            ON {$this->transTable} (state)");
        if(PEAR::isError($r)){ echo $r->getMessage()." ".$r->getUserInfo(); }
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
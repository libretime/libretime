<?php
define('TRERR_', 70);
define('TRERR_MD', 71);
define('TRERR_TOK', 72);
define('TRERR_NOTFIN', 73);
define('TRERR_XR_FAIL', 74);
#define('TR_LOG_LEVEL', 0);
define('TR_LOG_LEVEL', 10);

define('HOSTNAME', 'storageServer');

include_once("XML/RPC.php");
include_once("TransportRecord.php");

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
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class Transport
{
    /**
     * @var GreenBox
     */
    public $gb;

	/**
	 * File name
	 * @var string
	 */
	private $cronJobScript;

    /**
     * wget --read-timeout parameter [s]
     * @var int
     */
    private $downTimeout = 900;

    /**
     * wget --waitretry parameter [s]
     * @var int
     */
    private $downWaitretry = 10;

    /**
     * wget --limit-rate parameter
     */
    private $downLimitRate = NULL;
#    private $downLimitRate = 500;

    /**
     * wget -t parameter
     * @var int
     */
    private $downRetries = 6;

    /**
     * curl --max-time parameter
     * @var int
     */
    private $upTrMaxTime = 1800;

    /**
     * curl --speed-time parameter
     * @var int
     */
    private $upTrSpeedTime = 30;

    /**
     * curl --speed-limit parameter
     * @var int
     */
    private $upTrSpeedLimit  = 30;

    /**
     * curl --connect-timeout parameter
     * @var int
     */
    private $upTrConnectTimeout = 20;

    /**
     * curl --limit-rate parameter
     * @var int
     */
    private $upLimitRate  = NULL;
#    private $upLimitRate  = 500;


    /**
     * Constructor
     *
     * @param LocStor $gb
     * @return Transport
     */
    public function __construct(&$gb)
    {
        $this->gb =& $gb;
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
     * @param string $trtok
     * 		 transport token
     * @return array
     * 		struct/hasharray with fields:
     *      trtype: string -
     *          audioclip | playlist | playlistPkg | search | metadata | file
     *      state: string - transport state
     *                  init | pending | waiting | finished | closed | failed
     *      direction: string - up | down
     *      expectedsize: int - file size in bytes
     *      realsize: int - currently transported bytes
     *      expectedsum: string - orginal file checksum
     *      realsum: string - transported file checksum
     *      title: string - dc:title or filename etc.
     *      errmsg: string - error message for failed transports
     *      ... ?
     */
    function getTransportInfo($trtok)
    {
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $res = array();
        foreach (array(
            'trtype', 'state', 'direction', 'expectedsize', 'realsize',
            'expectedsum', 'realsum', 'title', 'errmsg'
        ) as $k) {
            $res[$k] = ( isset($trec->row[$k]) ? $trec->row[$k] : NULL );
        }
        if ( ($trec->row['direction'] == 'down')  && file_exists($trec->row['localfile']) ){
            $res['realsize'] = filesize($trec->row['localfile']);
            $res['realsum']  = $this->_chsum($trec->row['localfile']);
        }
        if ( ($trec->row['direction'] == 'up') ){
            $check = $this->uploadCheck($trec->row['pdtoken']);
            if (!PEAR::isError($check)) {
                $res['realsize'] = $check['size'];
                $res['realsum'] = $check['realsum'];
            }
        }
        // do not return finished on finished search job upload
        // - whole search is NOT finished
        if ($res['trtype'] == "searchjob" && $res['direction'] == "up" && $res['state'] == "finished") {
            $res['state'] = "waiting";
        }
        return $res;
    }


    /**
     * Turn transports on/off, optionaly return current state.
     * (true=On / false=off)
     *
     * @param string $sessid
     * 		session id
     * @param boolean $onOff
     * 		optional (if not used, current state is returned)
     * @return boolea
     * 		previous state
     */
    function turnOnOffTransports($sessid, $onOff=NULL)
    {
        require_once('Prefs.php');
        $pr = new Prefs($this->gb);
        $group = 'StationPrefs';
        $key = 'TransportsDenied';
        $res = $pr->loadGroupPref($sessid, $group, $key);
        if (PEAR::isError($res)) {
            if ($res->getCode() !== GBERR_PREF) {
            	return $res;
            } else {
            	$res = FALSE;  // default
            }
        }
        $state = !$res;
        if (is_null($onOff)) {
        	return $state;
        }
        $res = $pr->saveGroupPref($sessid, $group, $key, !$onOff);
        if (PEAR::isError($res)) {
        	return $res;
        }
        return $state;
    }


    /**
     * Pause, resume or cancel transport
     *
     * @param string $trtok
     * 		transport token
     * @param string $action
     * 		pause | resume | cancel
     * @return string
     * 		resulting transport state
     */
    function doTransportAction($trtok, $action)
    {
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        if ($trec->getState() == 'closed') {
            return PEAR::raiseError(
                "Transport::doTransportAction:".
                " closed transport token ($trtok)", TRERR_TOK
            );
        }
        switch ($action) {
            case 'pause';
                $newState = 'paused';
                break;
            case 'resume';
                $newState = 'waiting';
                break;
            case 'cancel';
                $newState = 'closed';
                break;
            default:
                return PEAR::raiseError(
                    "Transport::doTransportAction:".
                    " unknown action ($action)"
                );
        }
        $res = $trec->setState($newState);
        switch ($action) {
            case 'pause';
            case 'cancel';
                $trec->killJob();
        }
        return $res;
    }

    /* ------------- special methods for audioClip/webstream object transport */

    /**
     * Start upload of audioClip/webstream/playlist from local storageServer
     * to hub.
     *
     * @param string $gunid
     * 		global unique id of object being transported
     * @param boolean $withContent
     * 		if true, transport playlist content too (optional)
     * @param array $pars
     * 		default parameters (optional, internal use)
     * @return string
     * 		transport token
     */
    function upload2Hub($gunid, $withContent=TRUE, $pars=array())
    {
        global $CC_CONFIG, $CC_DBC;
        $this->trLog("upload2Hub start: ".strftime("%H:%M:%S"));
        switch ($ftype = BasicStor::GetType($gunid)) {
            case "audioclip":
            case "webstream":
                $storedFile = StoredFile::RecallByGunid($gunid);
                if (is_null($storedFile) || PEAR::isError($storedFile)) {
                	return $storedFile;
                }
                // handle metadata:
                $mdfpath = $storedFile->getRealMetadataFileName();
                if (PEAR::isError($mdfpath)) {
                	return $mdfpath;
                }
                $mdtrec = $this->_uploadGeneralFileToHub($mdfpath, 'metadata',
                    array_merge(array('gunid'=>$gunid, 'fname'=>'metadata',), $pars)
                );
                if (PEAR::isError($mdtrec)) {
                	return $mdtrec;
                }
                // handle raw media file:
                $fpath = $storedFile->getRealFileName();
                if (PEAR::isError($fpath)) {
                	return $fpath;
                }
                $fname = $storedFile->getName();
                if (PEAR::isError($fname)) {
                	return $fname;
                }
                $trec = $this->_uploadGeneralFileToHub($fpath, 'audioclip',
                    array_merge(array(
                        'gunid'=>$gunid, 'fname'=>$fname, 'mdtrtok'=>$mdtrec->trtok,
                    ), $pars)
                );
                if (PEAR::isError($trec)) {
                	return $trec;
                }
                $this->startCronJobProcess($mdtrec->trtok);
                break;

            case "playlist":
                $plid = $gunid;
                require_once("Playlist.php");
                $pl = StoredFile::RecallByGunid($plid);
                if (is_null($pl) || PEAR::isError($pl)) {
                	return $pl;
                }
                $fname = $pl->getName();
                if (PEAR::isError($fname)) {
                	return $fname;
                }
                if ($withContent) {
                    $this->trLog("upload2Hub exportPlaylistOpen BEGIN: ".strftime("%H:%M:%S"));
                    $res = $this->gb->bsExportPlaylistOpen($plid);
                    $this->trLog("upload2Hub exportPlaylistOpen END: ".strftime("%H:%M:%S"));
                    if (PEAR::isError($res)) {
                    	return $res;
                    }
                    $tmpn = tempnam($CC_CONFIG['transDir'], 'plExport_');
                    $plfpath = "$tmpn.lspl";
                    $this->trLog("upload2Hub begin copy: ".strftime("%H:%M:%S"));
                    copy($res['fname'], $plfpath);
                    $this->trLog("upload2Hub end copy: ".strftime("%H:%M:%S"));
                    $res = $this->gb->bsExportPlaylistClose($res['token']);
                    if (PEAR::isError($res)) {
                    	return $res;
                    }
                    $fname = $fname.".lspl";
                    $trtype = 'playlistPkg';
                } else {
                    $plfpath = $pl->getRealMetadataFileName();
                    if (PEAR::isError($plfpath)) {
                    	return $plfpath;
                    }
                    $trtype = 'playlist';
                }
                $trec = $this->_uploadGeneralFileToHub($plfpath, $trtype,
                    array_merge(array('gunid'=>$plid,'fname'=>$fname,), $pars));
                if (PEAR::isError($trec)) {
                	return $trec;
                }
                break;
            default:
                return PEAR::raiseError("Transport::upload2Hub: ftype not supported ($ftype)");
        }
        $this->startCronJobProcess($trec->trtok);
        $this->trLog("upload2Hub end: ".strftime("%H:%M:%S"));
        return $trec->trtok;
    }


    /**
     * Start download of audioClip/webstream/playlist from hub to local
     * storageServer
     *
     * @param int $uid
     * 		local user id of transport owner
     *      (for downloading file to homedir in storage)
     * @param string $gunid
     * 		global unique id of object being transported
     * @param boolean $withContent
     * 		if true, transport playlist content too (optional)
     * @param array $pars
     * 		default parameters (optional, internal use)
     * @return string
     * 		transport token
     */
    function downloadFromHub($uid, $gunid, $withContent=TRUE, $pars=array())
    {
        $trtype = ($withContent ? 'playlistPkg' : 'unknown' );
        $trec = TransportRecord::create($this, $trtype, 'down',
            array_merge(array('gunid'=>$gunid, 'uid'=>$uid), $pars));
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }


    /* ------------------------------------------------ global-search methods */
    /**
     * Start search job on network hub
     *
     * @param array $criteria
     * 		LS criteria format (see localSearch)
     * @param string $resultMode
     * 		'php' | 'xmlrpc'
     * @param array $pars
     * 		default parameters (optional, internal use)
     * @return string
     * 		transport token
     */
    function globalSearch($criteria, $resultMode='php', $pars=array())
    {
        global $CC_CONFIG, $CC_DBC;
        // testing of hub availability and hub account configuration.
        // it makes searchjob not async - should be removed for real async
        $r = $this->loginToArchive();
        if (PEAR::isError($r)) {
            switch(intval($r->getCode())) {
                case 802:
                    return PEAR::raiseError("Can't login to Hub ({$r->getMessage()})", TRERR_XR_FAIL);
                case TRERR_XR_FAIL:
                    return PEAR::raiseError("Can't connect to Hub ({$r->getMessage()})", TRERR_XR_FAIL);
            }
            return $r;
        }
        $this->logoutFromArchive($r);
        $criteria['resultMode'] = $resultMode;
        $localfile = tempnam($CC_CONFIG['transDir'], 'searchjob_');
        @chmod($localfile, 0660);
        $len = file_put_contents($localfile, serialize($criteria));
        $trec = $this->_uploadGeneralFileToHub($localfile, 'searchjob', $pars);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }


    /**
     * Get results from search job on network hub
     *
     * @param string $trtok
     * 		transport token
     * @param boolean $andClose
     * 		if TRUE, close transport token
     * @return array
     * 		LS search result format (see localSearch)
     */
    function getSearchResults($trtok, $andClose=TRUE)
    {
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $row = $trec->row;
        switch ($st = $trec->getState()) {
            case "failed":
                return PEAR::raiseError(
                    "Transport::getSearchResults:".
                    " global search or results transport failed".
                    " ({$trec->row['errmsg']})"
                );
            case "closed":
/*
                $res = file_get_contents($row['localfile']);
                $results = unserialize($res);
                return $results;
*/
                return PEAR::raiseError(
                    "Transport::getSearchResults:".
                    " closed transport token ($trtok)", TRERR_TOK
                );
            case "finished":
                if ($row['direction'] == 'down') {
                    // really finished
                    $res = file_get_contents($row['localfile']);
                    $results = unserialize($res);
                    if ($andClose) {
                        $ret = $this->xmlrpcCall('archive.downloadClose',
                            array(
                               'token'     => $row['pdtoken'] ,
                               'trtype'     => $row['trtype'] ,
                            ));
                        if (PEAR::isError($ret)) {
                            return $ret;
                        }
                        @unlink($row['localfile']);
                        $r = $trec->close();
                        if (PEAR::isError($r)) {
                            return $r;
                        }
                    }
                    return $results;
                }
                // otherwise not really finished - only request upload finished
            default:
                return PEAR::raiseError(
                    "Transport::getSearchResults: not finished ($st)",
                    TRERR_NOTFIN
                );
        }
    }


    /* ------------------------ methods for ls-archive-format file transports */
    /**
     * Open async file transfer from local storageServer to network hub,
     * file should be ls-archive-format file.
     *
     * @param string $filePath
     * 		local path to uploaded file
     * @param array $pars
     * 		default parameters (optional, internal use)
     * @return string
     * 		transport token
     */
    function uploadFile2Hub($filePath, $pars=array())
    {
        if (!file_exists($filePath)) {
            return PEAR::raiseError(
                "Transport::uploadFile2Hub: file not found ($filePath)"
            );
        }
        $trec = $this->_uploadGeneralFileToHub($filePath, 'file', $pars);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $this->startCronJobProcess($trec->trtok);
        return $trec->trtok;
    }


    /**
     * Open async file transfer from network hub to local storageServer,
     * file should be ls-archive-format file.
     *
     * @param string $url
     * 		readable url
     * @param string $chsum
     * 		checksum from remote side
     * @param int $size
     * 		filesize from remote side
     * @param array $pars
     * 		default parameters (internal use)
     * @return array
     *      trtok: string - transport token
     *      localfile: string - filepath of downloaded file
     */
    function downloadFileFromHub($url, $chsum=NULL, $size=NULL, $pars=array())
    {
        global $CC_CONFIG, $CC_DBC;
        $tmpn = tempnam($CC_CONFIG['transDir'], 'HITrans_');
        $trec = TransportRecord::create($this, 'file', 'down',
            array_merge(array(
                'url'           => $url,
                'localfile'     => $tmpn,
                'expectedsum'   => $chsum,
                'expectedsize'  => $size,
            ), $pars)
        );
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $this->startCronJobProcess($trec->trtok);
        return array('trtok'=>$trec->trtok, 'localfile'=>$tmpn);
    }


    /**
     * Get list of prepared transfers initiated by hub
     *
     * @return array
     * 		array of structs/hasharrays with fields:
     *      trtok: string transport token
     */
    function getHubInitiatedTransfers()
    {
        $ret = $this->xmlrpcCall('archive.listHubInitiatedTransfers',
            array('target' => HOSTNAME));
        if (PEAR::isError($ret)) {
        	return $ret;
        }
        $res = array();
        foreach ($ret as $it) {
            $res[] = array('trtok'=>$it['trtok']);
        }
        return $res;
    }


    /**
     * Start of download initiated by hub
     *
     * @param int $uid
     * 		local user id of transport owner
     *      (for downloading file to homedir in storage)
     * @param string $rtrtok
     * 		transport token obtained from the getHubInitiatedTransfers method
     * @return string
     * 		transport token
     */
    function startHubInitiatedTransfer($uid, $rtrtok)
    {
        $ret = $this->xmlrpcCall('archive.listHubInitiatedTransfers',
            array(
                'target'    => HOSTNAME,
                'trtok'     => $rtrtok,
            ));
        if (PEAR::isError($ret)) {
        	return $ret;
        }
        if (count($ret) != 1) {
            return PEAR::raiseError(
                "Transport::startHubInitiatedTransfer:".
                " wrong number of transports (".count($ret).")"
            );
        }
        $ta = $ret[0];
        // direction invertation to locstor point of view:
        $direction = ( $ta['direction']=='up' ? 'down' : 'up' );
        $gunid  = $ta['gunid'];
        switch ($direction) {
        case "up":
            switch ($ta['trtype']) {
            case "audioclip":
            case "playlist":
            case "playlistPkg":
                $trtok = $this->upload2Hub($gunid, TRUE,
                    array('rtrtok'=>$rtrtok));
                if (PEAR::isError($trtok)) {
                	return $trtok;
                }
                break;
            //case "searchjob":  break;  // not supported yet
            //case "file":   break;      // probably unusable
            default:
                return PEAR::raiseError(
                    "Transport::startHubInitiatedTransfer:".
                    " wrong direction / transport type combination".
                    " ({$ta['direction']}/{$ta['trtype']})"
                );
            }
            break;
        case "down":
            switch ($ta['trtype']) {
            case "audioclip":
            case "playlist":
            case "playlistPkg":
                $trtok = $this->downloadFromHub($uid, $gunid, TRUE,
                    array('rtrtok'=>$rtrtok));
                if (PEAR::isError($trtok)) {
                	return $trtok;
                }
                break;
            //case "searchjob":    break;    // probably unusable
            case "file":
                $r = $this->downloadFileFromHub(
                    $ta['url'], $ta['expectedsum'], $ta['expectedsize'],
                        array('rtrtok'=>$rtrtok));
                if (PEAR::isError($r)) {
                	return $r;
                }
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
        $ret = $this->xmlrpcCall('archive.setHubInitiatedTransfer',
            array(
                'target'    => HOSTNAME,
                'trtok'     => $rtrtok,
                'state'     => 'waiting',
            ));
        if (PEAR::isError($ret)) {
        	return $ret;
        }
        $this->startCronJobProcess($trtok);
        return $trtok;
    }


    /* =============================================== authentication methods */

    /**
     * Login to archive server
     * (account info is taken from storageServer's config)
     *
     * @return string
     * 		sessid or error
     */
    function loginToArchive()
    {
        global $CC_CONFIG;
        $res = $this->xmlrpcCall('archive.login',
            array(
                'login' => $CC_CONFIG['archiveAccountLogin'],
                'pass' => $CC_CONFIG['archiveAccountPass']
            ));
        if (PEAR::isError($res)) {
            return $res;
        }
        return $res['sessid'];
    }


    /**
     * Logout from archive server
     *
     * @param unknown $sessid
     * 		session id
     * @return string
     * 		Bye or error
     */
    function logoutFromArchive($sessid)
    {
        $res = $this->xmlrpcCall('archive.logout',
            array('sessid'=>$sessid));
        return $res;
    }


    /* ========================================================= cron methods */
    /* -------------------------------------------------- common cron methods */
    /**
     * Main method for periodical transport tasks - called by cron
     *
     * @param string $direction
     * 		optional
     * @return boolean
     * 		TRUE
     */
    function cronMain($direction=NULL)
    {
        global $CC_CONFIG;
        if (is_null($direction)) {
            $r = $this->cronMain('up');
            if (PEAR::isError($r)) {
            	return $r;
            }
            $r = $this->cronMain('down');
            if (PEAR::isError($r)) {
            	return $r;
            }
            return TRUE;
        }
        // fetch all opened transports
        $transports = $this->getTransports($direction);
        if (PEAR::isError($transports)) {
        	$this->trLog("cronMain: DB error");
        	return FALSE;
        }
        if (count($transports) == 0) {
            if (TR_LOG_LEVEL > 1) {
                $this->trLog("cronMain: $direction - nothing to do.");
            }
            return TRUE;
        }
        // ping to archive server:
        $r = $this->pingToArchive();
        chdir($CC_CONFIG['transDir']);
        // for all opened transports:
        foreach ($transports as $i => $row) {
            $r = $this->startCronJobProcess($row['trtok']);
        } // foreach transports
        return TRUE;
    }


    /**
     * Cron job process starter
     *
     * @param string $trtok
     * 		transport token
     * @return boolean
     * 		status
     */
    function startCronJobProcess($trtok)
    {
        global $CC_CONFIG, $CC_DBC;
        if (TR_LOG_LEVEL > 2) {
        	$redirect = $CC_CONFIG['transDir']."/debug.log";
        } else {
        	$redirect = "/dev/null";
        }
        $redirect_escaped = escapeshellcmd($redirect);
        $command = "{$this->cronJobScript} {$trtok}";
        $command_escaped = escapeshellcmd($command);
        $command_final = "$command_escaped >> $redirect_escaped 2>&1 &";
        $res = system($command_final, $status);
        if ($res === FALSE) {
            $this->trLog(
                "cronMain: Error on execute cronJobScript with trtok {$trtok}"
            );
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Dynamic method caller - wrapper
     *
     * @param string $trtok
     * 		transport token
     * @return mixed
     * 		inherited from called method
     */
    function cronCallMethod($trtok)
    {
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $row = $trec->row;
        $state = $row['state'];

        $states = array('init'=>'init',
        				'pending'=>'pending',
        				'waiting'=>'waiting',
        				'finished'=>'finished',
        				'failed'=>'failed',
        				'closed'=>'closed');
        $directions = array('up'=>'upload', 'down'=>'download');
        // method name construction:
        $mname = "cron";
        if (isset($directions[$row['direction']])) {
            $mname .= ucfirst($directions[$row['direction']]);
        } else {
            return PEAR::raiseError(
                "Transport::cronCallMethod: invalid direction ({$row['direction']})"
            );
        }
        if (isset($states[$state])) {
            $mname .= ucfirst($states[$state]);
        } else {
            return PEAR::raiseError(
                "Transport::cronCallMethod: invalid state ({$state})"
            );
        }
        switch ($state) {
            // do nothing if closed, penfing or failed:
            case 'closed':   // excluded in SQL query too, but let check it here
            case 'failed':   // -"-
            case 'pending':
            case 'paused':
                return TRUE;
            case 'waiting':
                require_once('Prefs.php');
                $pr = new Prefs($this->gb);
                $group = 'StationPrefs';
                $key = 'TransportsDenied';
                $res = $pr->loadGroupPref(NULL/*sessid*/, $group, $key);
                if (PEAR::isError($res)) {
                    if ($res->getCode() !== GBERR_PREF) {
                    	return $res;
                    } else {
                    	$res = FALSE;  // default
                    }
                }
                // transfers turned off
                // if ($res) { return TRUE; break; }
                if ($res) {
                    return PEAR::raiseError(
                        "Transport::cronCallMethod: transfers turned off"
                    );
                }
                // NO break here!
            default:
                if (method_exists($this, $mname)) {
                    // lock the job:
                    $pid = getmypid();
                    $r = $trec->setLock(TRUE, $pid);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    $trec = TransportRecord::recall($this, $trtok);
                    if (PEAR::isError($trec)) {
                        $trec->setLock(FALSE);
                        return $trec;
                    }
                    $row = $trec->row;
                    $state = $row['state'];

                    // login to archive server:
                    $r = $this->loginToArchive();
                    if (PEAR::isError($r)) {
                        $r2 = $trec->setLock(FALSE);
                        return $r;
                    }
                    $asessid = $r;
                    // method call:
                    if (TR_LOG_LEVEL > 2) {
                        $this->trLog("cronCallMethod($pid): $mname($trtok) >");
                    }
                    $ret = call_user_func(array($this, $mname), $row, $asessid);
                    if (PEAR::isError($ret)) {
                        $trec->setLock(FALSE);
                        return $this->_failFatal($ret, $trec);
                    }
                    if (TR_LOG_LEVEL > 2) {
                        $this->trLog("cronCallMethod($pid): $mname($trtok) <");
                    }
                    // unlock the job:
                    $r = $trec->setLock(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    // logout:
                    $r = $this->logoutFromArchive($asessid);
                    if (PEAR::isError($r)) {
                        return $r;
                    }
                    return $ret;
                } else {
                    return PEAR::raiseError(
                        "Transport::cronCallMethod: unknown method ($mname)"
                    );
                }
        }
    }


    /**
     * Upload initialization
     *
     * @param array $row
     * 		row from getTransport results
     * @param string $asessid
     * 		session id (from network hub)
     * @return mixed
     * 		boolean TRUE or error object
     */
    function cronUploadInit($row, $asessid)
    {
        $trtok = $row['trtok'];
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $ret = $this->xmlrpcCall('archive.uploadOpen',
            array(
               'sessid' => $asessid ,
               'chsum' => $row['expectedsum'],
            ));
        if (PEAR::isError($ret)) {
        	return $ret;
        }
        $r = $trec->setState('waiting',
            array('url'=>$ret['url'], 'pdtoken'=>$ret['token']));
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Download initialization
     *
     * @param array $row
     * 		row from getTransport results
     * @param string $asessid
     * 		session id (from network hub)
     * @return mixed
     * 		boolean TRUE or error object
     */
    function cronDownloadInit($row, $asessid)
    {
        global $CC_CONFIG;
        $trtok = $row['trtok'];
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $ret = $this->xmlrpcCall('archive.downloadOpen',
            array(
                'sessid'=> $asessid,
                'trtype'=> $row['trtype'],
                'pars'=>array(
                    'gunid' => $row['gunid'],
                    'token' => $row['pdtoken'],
                ),
            ));
        if (PEAR::isError($ret)) {
        	return $ret;
        }
        $trtype = $ret['trtype'];
        $title = $ret['title'];
        $pars = array();
        switch ($trtype) {
	        case "searchjob":
	            $r = $trec->setState('waiting', $pars);
	            break;
	        case "file":
	            $r = $trec->setState('waiting',array_merge($pars, array(
	                'trtype'=>$trtype,
	                'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
	                'expectedsum'=>$ret['chsum'], 'expectedsize'=>$ret['size'],
	                'fname'=>$ret['filename'],
	                'localfile'=>$CC_CONFIG['transDir']."/$trtok",
	            )));
	            break;
	        case "audioclip":
	            $mdtrec = TransportRecord::create($this, 'metadata', 'down',
	                array('gunid'=>$row['gunid'], 'uid'=>$row['uid'], )
	            );
	            if (PEAR::isError($mdtrec)) {
	            	return $mdtrec;
	            }
	            $this->startCronJobProcess($mdtrec->trtok);
	            $pars = array('mdtrtok'=>$mdtrec->trtok);
	            // NO break here !
	        default:
	            $r = $trec->setState('waiting',array_merge($pars, array(
	                'trtype'=>$trtype,
	                'url'=>$ret['url'], 'pdtoken'=>$ret['token'],
	                'expectedsum'=>$ret['chsum'], 'expectedsize'=>$ret['size'],
	                'fname'=>$ret['filename'], 'title'=>$title,
	                'localfile'=>$CC_CONFIG['transDir']."/$trtok",
	            )));
        }
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Upload next part of transported file
     *
     * @param array $row
     * 		row from getTransport results
     * @param string $asessid
     * 		session id (from network hub)
     * @return mixed
     * 		boolean TRUE or error object
     */
    function cronUploadWaiting($row, $asessid)
    {
        $trtok = $row['trtok'];
        $check = $this->uploadCheck($row['pdtoken']);
        if (PEAR::isError($check)) {
        	return $check;
        }
        // test filesize
        if (!file_exists($row['localfile'])) {
            return PEAR::raiseError("Transport::cronUploadWaiting:".
                " file being uploaded does not exist! ({$row['localfile']})"
            );
        }
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $size = escapeshellarg($check['size']);
        $localfile = escapeshellarg($row['localfile']);
        $url = escapeshellarg($row['url']);
        $command =
            "curl -f -s -C $size --max-time {$this->upTrMaxTime}".
            " --speed-time {$this->upTrSpeedTime}".
            " --speed-limit {$this->upTrSpeedLimit}".
            " --connect-timeout {$this->upTrConnectTimeout}".
            (!is_null($this->upLimitRate)?
                " --limit-rate {$this->upLimitRate}" : "").
            " -T $localfile $url";
        $r = $trec->setState('pending', array(), 'waiting');
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($r === FALSE) {
        	return TRUE;
        }
        $res = system($command, $status);

        // leave paused and closed transports
        $trec2 = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $state2 = $trec2->row['state'];
        if ($state2 == 'paused' || $state2 == 'closed' ) {
            return TRUE;
        }


        // status 18 - Partial file. Only a part of the file was transported.
        // status 28 - Timeout. Too long/slow upload, try to resume next time rather.
        // status 6 - Couldn't resolve host.
        // status 7 - Failed to connect to host.
        // status 56 - Failure in receiving network data. Important - this status is
        //             returned if file is locked on server side
        if ($status == 0 || $status == 18 || $status == 28 || $status == 6 || $status == 7 || $status == 56) {
            $check = $this->uploadCheck($row['pdtoken']);
            if (PEAR::isError($check)) {
            	return $check;
            }
            // test checksum
            if ($check['status'] == TRUE) {
                // finished
                $r = $trec->setState('finished',
                    array('realsum'=>$check['realsum'], 'realsize'=>$check['size']));
                if (PEAR::isError($r)) {
                	return $r;
                }
            } else {
                if (intval($check['size']) < $row['expectedsize']) {
                    $r = $trec->setState('waiting',
                        array('realsum'=>$check['realsum'], 'realsize'=>$check['size']));
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                } else {
                    // wrong md5 at finish - TODO: start again
                    // $this->xmlrpcCall('archive.uploadReset', array());
                    $trec->fail('file uploaded with bad md5');
                    return PEAR::raiseError("Transport::cronUploadWaiting:".
                        " file uploaded with bad md5 ".
                        "($trtok: {$check['realsum']}/{$check['expectedsum']})"
                    );
                }
            }
        } else {
            return PEAR::raiseError("Transport::cronUploadWaiting:".
                " wrong return status from curl: $status on $url".
                "($trtok)"
            );
        }
        return TRUE;
    }


    /**
     * Download next part of transported file
     *
     * @param array $row
     * 		row from getTransport results
     * @param string $asessid
     * 		session id (from network hub)
     * @return mixed
     * 		boolean TRUE or error object
     */
    function cronDownloadWaiting($row, $asessid)
    {
        $trtok = $row['trtok'];
        // wget the file
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $localfile = escapeshellarg($row['localfile']);
        $url = escapeshellarg($row['url']);
        $command =
            "wget -q -c".
            " --read-timeout={$this->downTimeout}".
            " --waitretry={$this->downWaitretry}".
            " -t {$this->downRetries}".
            (!is_null($this->downLimitRate)?
                " --limit-rate={$this->downLimitRate}" : "").
            " -O $localfile $url"
        ;
        $r = $trec->setState('pending', array(), 'waiting');
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($r === FALSE) {
        	return TRUE;
        }
        $res = system($command, $status);

        // leave paused and closed transports
        $trec2 = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $state2 = $trec2->row['state'];
        if ($state2 == 'paused' || $state2 == 'closed' ) {
            return TRUE;
        }

        // check consistency
        $size = filesize($row['localfile']);
        if ($size < $row['expectedsize']) {
            // not finished - return to the 'waiting' state
            $r = $trec->setState('waiting', array('realsize'=>$size));
            if (PEAR::isError($r)) {
                return $r;
            }
        } elseif ($size >= $row['expectedsize']) {
            $chsum = $this->_chsum($row['localfile']);
            if ($chsum == $row['expectedsum']) {
                // mark download as finished
                $r = $trec->setState('finished',
                    array('realsum'=>$chsum, 'realsize'=>$size));
                if (PEAR::isError($r)) {
                    return $r;
                }
            } else {
                // bad checksum, retry from the scratch
                @unlink($row['localfile']);
                $r = $trec->setState('waiting',
                    array('realsum'=>$chsum, 'realsize'=>$size));
                if (PEAR::isError($r)) {
                    return $r;
                }
            }
        }
        return TRUE;
    }


    /**
     * Finish the upload
     *
     * @param array $row
     * 		row from getTransport results
     * @param string $asessid
     * 		session id (from network hub)
     * @return mixed
     * 		boolean TRUE or error object
     */
    function cronUploadFinished($row, $asessid)
    {
        global $CC_CONFIG;
        $trtok = $row['trtok'];
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        // don't close metadata transport - audioclip will close it
        if ($row['trtype'] == 'metadata') {
        	return TRUE;
        }
        // handle metadata transport on audioclip trtype:
        if ($row['trtype'] == 'audioclip') {
            $mdtrec = TransportRecord::recall($this, $trec->row['mdtrtok']);
            if (PEAR::isError($mdtrec)) {
            	return $mdtrec;
            }
            switch ($mdtrec->row['state']) {
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
        } else {
        	$mdpdtoken = NULL;
        }
        $ret = $this->xmlrpcCall('archive.uploadClose',
            array(
                'token'     => $row['pdtoken'] ,
                'trtype'      => $row['trtype'],
                'pars'      => array(
                    'gunid'     => $row['gunid'],
                    'name'      => $row['fname'],
                    'mdpdtoken' => $mdpdtoken,
                ),
            ));
        if (PEAR::isError($ret)) {
            if ($row['trtype'] == 'audioclip') {
                $r2 = $mdtrec->close();
            }
            return $ret;
        }

        if ($row['trtype'] == 'searchjob') {
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
        } else {
            $r = $trec->close();
        }
        if (PEAR::isError($r)) {
        	return $r;
        }
        switch ($row['trtype']) {
            case 'audioclip':
                // close metadata transport:
                $r = $mdtrec->close();
                if (PEAR::isError($r)) {
                	return $r;
                }
                break;
            case 'playlistPkg':
                // remove exported playlist (playlist with content)
                $ep = $row['localfile'];
                @unlink($ep);
                if (preg_match("|/(plExport_[^\.]+)\.lspl$|", $ep, $va)) {
                    list(,$tmpn) = $va; $tmpn = $CC_CONFIG['transDir']."/$tmpn";
                    if (file_exists($tmpn)) {
                    	@unlink($tmpn);
                    }
                }

                break;
            default:
        }

        return TRUE;
    }


    /**
     * Finish the download
     *
     * @param array $row
     * 		row from getTransport results
     * @param string $asessid
     * 		session id (from network hub)
     * @return mixed
     * 		boolean TRUE or error object
     */
    function cronDownloadFinished($row, $asessid)
    {
        $trtok = $row['trtok'];
        $trec = TransportRecord::recall($this, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        switch ($row['trtype']) {
            case "audioclip":
                $mdtrtok = $trec->row['mdtrtok'];
                $mdtrec = TransportRecord::recall($this, $mdtrtok);
                if (PEAR::isError($mdtrec)) {
                	return $mdtrec;
                }
                $pid = getmypid();
                $r = $mdtrec->setLock(TRUE, $pid);
                if (PEAR::isError($r)) {
                	return $r;
                }
                switch ($mdtrec->row['state']) {
                    // don't close transport with nonfinished metadata transport:
                    case 'init':
                    case 'waiting':
                    case 'pending':
                    case 'paused':
                        $r = $mdtrec->setLock(FALSE);
                        if (PEAR::isError($r)) {
                        	return $r;
                        }
                        return TRUE;
                    case 'finished':  // metadata finished, close main transport
                        $parid = $this->gb->_getHomeDirId($trec->row['uid']);
                        if (PEAR::isError($parid)) {
                            $mdtrec->setLock(FALSE);
                            return $parid;
                        }
                        $values = array(
                            "filename" => $row['fname'],
                            "filepath" => $trec->row['localfile'],
                            "metadata" => $mdtrec->row['localfile'],
                            "gunid" => $row['gunid'],
                            "filetype" => "audioclip"
                        );
                        $storedFile = $this->gb->bsPutFile($parid, $values);
                        if (PEAR::isError($storedFile)) {
                            $mdtrec->setLock(FALSE);
                            return $storedFile;
                        }
                        $res = $storedFile->getId();
                        $ret = $this->xmlrpcCall('archive.downloadClose',
                            array(
                               'token'      => $mdtrec->row['pdtoken'] ,
                               'trtype'     => 'metadata' ,
                            ));
                        if (PEAR::isError($ret)) {
                            $mdtrec->setLock(FALSE);
                            return $ret;
                        }
                        $r = $mdtrec->close();
                        if (PEAR::isError($r)) {
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
                }
                $r = $mdtrec->setLock(FALSE);
                if (PEAR::isError($r)) {
                	return $r;
                }
                break;
            case "metadata":
            case "searchjob":
                return TRUE;     // don't close - getSearchResults should close it
                break;
        }
        $ret = $this->xmlrpcCall('archive.downloadClose',
            array(
               'token'     => $row['pdtoken'] ,
               'trtype'     => $row['trtype'] ,
            ));
        if (PEAR::isError($ret)) {
        	return $ret;
        }
        switch ($row['trtype']) {
            case "playlist":
                $parid = $this->gb->_getHomeDirId($trec->row['uid']);
                if (PEAR::isError($parid)) {
                	return $parid;
                }
                $values = array(
                    "filename" => $row['fname'],
                    "metadata" => $trec->row['localfile'],
                    "gunid" => $row['gunid'],
                    "filetype" => "playlist"
                );
                $storedFile = $this->gb->bsPutFile($parid, $values);
                if (PEAR::isError($storedFile)) {
                	return $storedFile;
                }
                $res = $storedFile->getId();
                @unlink($row['localfile']);
                break;
            case "playlistPkg":
                $subjid = $trec->row['uid'];
                $fname = $trec->row['localfile'];
                $parid = $this->gb->_getHomeDirId($subjid);
                if (PEAR::isError($parid)) {
                	return $parid;
                }
                $res = $this->gb->bsImportPlaylist($parid, $fname, $subjid);
                if (PEAR::isError($res)) {
                	return $res;
                }
                @unlink($fname);
                break;
            case "audioclip":
            case "metadata":
            case "searchjob":
            case "file":
                break;
            default:
                return PEAR::raiseError("DEBUG: NotImpl ".var_export($row,TRUE));
        }
        if (!is_null($rtrtok = $trec->row['rtrtok'])) {
            $ret = $this->xmlrpcCall('archive.setHubInitiatedTransfer',
                array(
                    'target'    => HOSTNAME,
                    'trtok'     => $rtrtok,
                    'state'     => 'closed',
                ));
            if (PEAR::isError($ret)) {
            	return $ret;
            }
        }
        $r = $trec->close();
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /* ==================================================== auxiliary methods */
    /**
     *  Prepare upload for general file
     *
     *  @param string $fpath
     * 		local filepath of uploaded file
     *  @param string $trtype
     * 		transport type
     *  @param array $pars
     * 		default parameters (optional, internal use)
     *  @return object - transportRecord instance
     */
    function _uploadGeneralFileToHub($fpath, $trtype, $pars=array())
    {
        $chsum = $this->_chsum($fpath);
        $size  = filesize($fpath);
        $trec = TransportRecord::create($this, $trtype, 'up',
            array_merge(array(
                'localfile'=>$fpath, 'fname'=>basename($fpath),
                'expectedsum'=>$chsum, 'expectedsize'=>$size
            ), $pars)
        );
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        return $trec;
    }


    /**
     * Create new transport token
     *
     * @return string
     * 		transport token
     */
    function _createTransportToken()
    {
        $ip = (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '');
        $initString = microtime().$ip.rand()."org.mdlf.campcaster";
        $hash = md5($initString);
        $res = substr($hash, 0, 16);
        return $res;
    }


    /**
     * Get all relevant transport records
     *
     * @param string $direction
     * 		'up' | 'down'
     * @param string $target
     * 		target hostname
     * @param string $trtok
     * 		transport token for specific query
     * @return array
     * 		array of transportRecords (as hasharrays)
     */
    function getTransports($direction=NULL, $target=NULL, $trtok=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        switch ($direction) {
            case 'up':
            	$dirCond = "direction='up' AND";
            	break;
            case 'down':
            	$dirCond = "direction='down' AND";
            	break;
            default:
            	$dirCond = '';
            	break;
        }
        if (is_null($target)) {
        	$targetCond = "";
        } else {
        	$targetCond = "target='$target' AND";
        }
        if (is_null($trtok)) {
        	$trtokCond = "";
        } else {
        	$trtokCond = "trtok='$trtok' AND";
        }
        $rows = $CC_DBC->getAll("
            SELECT
                id, trtok, state, trtype, direction,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, expectedsum, expectedsize, url,
                uid, target
            FROM ".$CC_CONFIG['transTable']."
            WHERE $dirCond $targetCond $trtokCond
                    state not in ('closed', 'failed', 'paused')
            ORDER BY start DESC
        ");
        if (PEAR::isError($rows)) {
        	return $rows;
        }
        foreach ($rows as $i => $row) {
            $rows[$i]['pdtoken'] = StoredFile::NormalizeGunid($row['pdtoken']);
            $rows[$i]['gunid'] = StoredFile::NormalizeGunid($row['gunid']);
        }
        return $rows;
    }


    /**
     * Check remote state of uploaded file
     *
     * @param string $pdtoken
     * 		put/download token (from network hub)
     * @return array
     * 		hash: chsum, size, url
     */
    function uploadCheck($pdtoken)
    {
        $ret = $this->xmlrpcCall('archive.uploadCheck',
            array('token'=>$pdtoken));
        return $ret;
    }


    /**
     * Ping to archive server
     *
     * @return string
     * 		network hub response or error object
     */
    function pingToArchive()
    {
        $res = $this->xmlrpcCall('archive.ping',
            array('par'=>'ping_'.date('H:i:s')));
        return $res;
    }


    /**
     * XMLRPC call to network hub.
     *
     * @param string $method
     * 		method name
     * @param array $pars
     * 		call parameters
     * @return mixed
     * 		response
     */
    function xmlrpcCall($method, $pars=array())
    {
        global $CC_CONFIG;
        $xrp = XML_RPC_encode($pars);
        $c = new XML_RPC_Client(
            $CC_CONFIG['archiveUrlPath']."/".$CC_CONFIG['archiveXMLRPC'],
            $CC_CONFIG['archiveUrlHost'], $CC_CONFIG['archiveUrlPort']
        );
        $f = new XML_RPC_Message($method, array($xrp));
        $r = $c->send($f);
        if (!$r) {
            return PEAR::raiseError("XML-RPC request failed", TRERR_XR_FAIL);
        } elseif ($r->faultCode() > 0) {
            return PEAR::raiseError($r->faultString(), $r->faultCode());
            // return PEAR::raiseError($r->faultString().
            //    " (code ".$r->faultCode().")", TRERR_XR_FAIL);
        } else {
            $v = $r->value();
            return XML_RPC_decode($v);
        }
    }


    /**
     * Checksum of local file
     *
     * @param string $fpath
     * 		local filepath
     * @return string
     * 		checksum
     */
    function _chsum($fpath)
    {
        return md5_file($fpath);
    }


    /**
     * Check exception and eventually mark transport as failed
     *
     * @param mixed $res
     * 		result object to be checked
     * @param unknown $trec
     * 		transport record object
     * @return unknown
     */
    function _failFatal($res, $trec)
    {
        if (PEAR::isError($res)) {
            switch ($res->getCode()) {
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
     * Clean up transport jobs
     *
     * @param string $interval
     * 		psql time interval - older closed jobs will be deleted
     * @param boolean $forced
     * 		if true, delete non-closed jobs too
     * @return boolean true or error
     */
    function _cleanUp($interval='1 minute'/*'1 hour'*/, $forced=FALSE)
    {
        global $CC_CONFIG, $CC_DBC;
        $cond = ($forced ? '' : " AND state='closed' AND lock = 'N'");
        $r = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['transTable']."
            WHERE ts < now() - interval '$interval'".$cond
        );
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Logging wrapper for PEAR error object
     *
     * @param string $txt
     * 		log message
     * @param PEAR_Error $eo
     * @param array $row
     * 		array returned from getRow
     * @return mixed
     * 		void or error object
     */
    function trLogPear($txt, $eo, $row=NULL)
    {
        $msg = $txt.$eo->getMessage()." ".$eo->getUserInfo().
            " [".$eo->getCode()."]";
        if (!is_null($row)) {
            $trec = TransportRecord::recall($this, $row['trtok']);
            if (!PEAR::isError($trec)) {
                $trec->setState('failed', array('errmsg'=>$msg));
            }
            $msg .= "\n    ".serialize($row);
        }
        $this->trLog($msg);
    }


    /**
     * Logging for debug transports
     *
     * @param string $msg
     * 		log message
     * @return mixed
     * 		void or error object
     */
    function trLog($msg)
    {
        global $CC_CONFIG;
        $logfile = $CC_CONFIG['transDir']."/activity.log";
        if (FALSE === ($fp = fopen($logfile, "a"))) {
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
     * Delete all transports
     *
     * @return mixed
     * 		void or error object
     */
    function resetData()
    {
        global $CC_CONFIG, $CC_DBC;
        return $CC_DBC->query("DELETE FROM ".$CC_CONFIG['transTable']);
    }


    /**
     *  Install method<br>
     *
     *  direction: up | down
     *  state: init | pending | waiting | finished | closed | failed | paused
     *  trtype: audioclip | playlist | playlistPkg | searchjob | metadata | file
     *
     */
//    function install()
//    {
//        global $CC_CONFIG, $CC_DBC;
//        $r = $CC_DBC->query("CREATE TABLE {$this->transTable} (
//            id int not null,          -- primary key
//            trtok char(16) not null,  -- transport token
//            direction varchar(128) not null,  -- direction: up|down
//            state varchar(128) not null,      -- state
//            trtype varchar(128) not null,     -- transport type
//            lock char(1) not null default 'N',-- running lock
//            target varchar(255) default NULL, -- target system,
//                                              -- if NULL => predefined set
//            rtrtok char(16) default NULL,     -- remote hub's transport token
//            mdtrtok char(16),         -- metadata transport token
//            gunid bigint,             -- global unique id
//            pdtoken bigint,           -- put/download token from archive
//            url varchar(255),         -- url on remote side
//            localfile varchar(255),   -- pathname of local part
//            fname varchar(255),       -- mnemonic filename
//            title varchar(255),       -- dc:title mdata value (or filename ...)
//            expectedsum char(32),     -- expected file checksum
//            realsum char(32),         -- checksum of transported part
//            expectedsize int,         -- expected filesize in bytes
//            realsize int,             -- filesize of transported part
//            uid int,                  -- local user id of transport owner
//            errmsg varchar(255),      -- error message string for failed tr.
//            start timestamp,          -- starttime
//            ts timestamp              -- mtime
//        )");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//        $r = $CC_DBC->createSequence("{$this->transTable}_id_seq");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//        $r = $CC_DBC->query("CREATE UNIQUE INDEX {$this->transTable}_id_idx
//            ON {$this->transTable} (id)");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//        $r = $CC_DBC->query("CREATE UNIQUE INDEX {$this->transTable}_trtok_idx
//            ON {$this->transTable} (trtok)");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//        $r = $CC_DBC->query("CREATE UNIQUE INDEX {$this->transTable}_token_idx
//            ON {$this->transTable} (pdtoken)");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//        $r = $CC_DBC->query("CREATE INDEX {$this->transTable}_gunid_idx
//            ON {$this->transTable} (gunid)");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//        $r = $CC_DBC->query("CREATE INDEX {$this->transTable}_state_idx
//            ON {$this->transTable} (state)");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()." ".$r->getUserInfo();
//        }
//    }

    /**
     *  Uninstall method
     */
//    function uninstall()
//    {
//        global $CC_CONFIG, $CC_DBC;
//        $CC_DBC->query("DROP TABLE {$this->transTable}");
//        $CC_DBC->dropSequence("{$this->transTable}_id_seq");
//    }
}

?>
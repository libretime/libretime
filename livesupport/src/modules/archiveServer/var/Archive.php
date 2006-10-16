<?php
require_once dirname(__FILE__)."/../../storageServer/var/xmlrpc/XR_LocStor.php";
require_once dirname(__FILE__)."/../../storageServer/var/Transport.php";

/**
 * Extension to StorageServer to act as ArchiveServer.
 *
 * @author $Author$
 * @version $Revision$
 * @package Campcaster
 * @subpackage ArchiveServer
 */
class Archive extends XR_LocStor{

    /**
     * Open upload transport (from station to hub)
     *
     * @param string $sessid
     * 		session id
     * @param string $chsum
     * 		checksum
     * @return array
     * 		hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function uploadOpen($sessid, $chsum)
    {
        $owner = $r = $this->getSessUserId($sessid);
        if ($this->dbc->isError($r)) {
        	return $r;
        }
        $res = $r = $this->bsOpenPut($chsum, NULL, $owner);
        if ($this->dbc->isError($r)) {
        	return $r;
        }
        return array('url'=>$res['url'], 'token'=>$res['token']);
    }


    /**
     * Check uploaded file
     *
     * @param string $token
     * 		transport token
     * @return array
     * 		(md5h string, size int, url string)
     */
    function uploadCheck($token)
    {
        return $this->bsCheckPut($token);
    }


    /**
     * Close upload transport
     *
     * @param string $token
     * 		transport token
     * @param string $trtype
     * 		transport type
     * @param array $pars
     * 		transport parameters
     * @return mixed
     */
    function uploadClose($token, $trtype, $pars=array())
    {
        $res = $r = $this->bsClosePut($token);
        if ($this->dbc->isError($r)) {
        	return $r;
        }
        extract($res);  // fname, owner
        switch ($trtype) {
            case "audioclip":
                $mdtoken = $pars['mdpdtoken'];
                $res = $r = $this->bsClosePut($mdtoken);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $mdfname = $res['fname'];
                if ($gunid == '') {
                	$gunid=NULL;
                }
                $parid = $r = $this->_getHomeDirId($owner);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $res = $r = $this->bsPutFile($parid, $pars['name'],
                    $fname, $mdfname,
                    $pars['gunid'], 'audioclip', 'file');
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                @unlink($fname);
                @unlink($mdfname);
                break;
            case "playlist":
                if ($gunid == '') {
                	$gunid = NULL;
                }
                $parid = $r = $this->_getHomeDirId($owner);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $res = $r = $this->bsPutFile($parid, $pars['name'],
                    '', $fname,
                    $pars['gunid'], 'playlist', 'file');
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                @unlink($fname);
                break;
            case "playlistPkg":
                $chsum = md5_file($fname);
                // importPlaylistOpen:
                $res = $r = $this->bsOpenPut($chsum, NULL, $owner);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $dest = $res['fname'];
                $token = $res['token'];
                copy($fname, $dest);
                $r = $this->importPlaylistClose($token);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                @unlink($fname);
                return $r;
                break;
            case "searchjob":
                $crits = file_get_contents($fname);
                $criteria = unserialize($crits);
                @unlink($fname);
                $results = $r =$this->localSearch($criteria);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $realfile = tempnam($this->accessDir, 'searchjob_');
                @chmod($realfile, 0660);
                $len = $r = file_put_contents($realfile, serialize($results));
                $acc = $r = $this->bsAccess($realfile, '', NULL, 'download');
                if ($this->dbc->isError($r)) {
                	return $r;
                }
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
            case "metadata":
                break;
            default:
        }
        return $res;
    }


    /**
     * Open download transport
     *
     * @param string $sessid - session id
     * @param string $trtype - transport type
     * @param array $pars - transport parameters
     * @return hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function downloadOpen($sessid, $trtype, $pars=array())
    {
        switch ($trtype) {
            case "unknown":
            case "audioclip":
            case "metadata":
            case "playlist":
            case "playlistPkg":
                if (!isset($pars['gunid'])) {
                    return PEAR::raiseError("Archive::downloadOpen: gunid not set");
                }
                break;
        }
        $gunid = $pars['gunid'];
        // resolve trtype by object type:
        if ( ($trtype == 'unknown') || ($trtype == 'playlistPkg') ) {
            $trtype2 = $r = $this->_getType($gunid);
            if ($this->dbc->isError($r)) {
            	return $r;
            }
            // required with content:
            $trtype = ($trtype2 == 'playlist' && $trtype == 'playlistPkg'?
                'playlistPkg' : $trtype2);
#    return PEAR::raiseError("Archive::downloadOpen: TT=$trtype TT2=$trtype2 G=$gunid");
        }
        switch ($trtype) {
            case "audioclip":
                $res = $r = $this->downloadRawAudioDataOpen($sessid, $gunid);
                break;
            case "metadata":
                $res = $r = $this->downloadMetadataOpen($sessid, $gunid);
                break;
            case "playlist":
                $res = $r = $this->accessPlaylist($sessid, $gunid);
                break;
            case "playlistPkg":
                $res = $r = $this->bsExportPlaylistOpen($gunid);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $tmpn = tempnam($this->transDir, 'plExport_');
                $plfpath = "$tmpn.lspl";
                copy($res['fname'], $plfpath);
                $res = $r = $this->bsExportPlaylistClose($res['token']);
                if (PEAR::isError($r)) {
                	return $r;
                }
                $fname = "transported_playlist.lspl";
                $id = $this->_idFromGunid($gunid);
                $acc = $this->bsAccess($plfpath, 'lspl', NULL, 'download');
                if ($this->dbc->isError($acc)) {
                	return $acc;
                }
                $url = $this->getUrlPart()."access/".basename($acc['fname']);
                $chsum = md5_file($plfpath);
                $size = filesize($plfpath);
                $res = array(
                    'url'=>$url, 'token'=>$acc['token'],
                    'chsum'=>$chsum, 'size'=>$size,
                    'filename'=>$fname
                );
                break;
            case "searchjob":
                $res = $pars;
                break;
            case "file":
                $res = $r = array();
                break;
            default:
                return PEAR::raiseError("Archive::downloadOpen: NotImpl ($trtype)");
        }
        if ($this->dbc->isError($r)) {
        	return $r;
        }
        switch ($trtype) {
            case "audioclip":
            case "metadata":
            case "playlist":
            case "playlistPkg":
                $title = $r = $this->bsGetTitle(NULL, $gunid);
                break;
            case "searchjob":    $title = 'searchjob';       break;
            case "file":         $title = 'regular file';    break;
            default:
        }
        $res['title'] = $title;
        $res['trtype'] = $trtype;
        return $res;
    }


    /**
     * Close download transport
     *
     * @param string $token
     * 		transport token
     * @param string $trtype
     * 		transport type
     * @return array
     * 		hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function downloadClose($token, $trtype)
    {
        switch ($trtype) {
            case "audioclip":
                $res = $r = $this->downloadRawAudioDataClose($token);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                return $res;
                break;
            case "metadata":
                $res = $r = $this->downloadMetadataClose($token);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                return $res;
                break;
            case "playlist":
                $res = $r = $this->releasePlaylist(NULL/*$sessid*/, $token);
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                return $res;
                break;
            case "playlistPkg":
                $res = $r = $this->bsRelease($token, 'download');
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                $realFname = $r['realFname'];
                @unlink($realFname);
                if (preg_match("|(plExport_[^\.]+)\.lspl$|", $realFname, $va)) {
                    list(,$tmpn) = $va;
                    $tmpn = "{$this->transDir}/$tmpn";
                    if (file_exists($tmpn)) {
                    	@unlink($tmpn);
                    }
                }
                return $res;
                break;
            case "searchjob":
                $res = $r = $this->bsRelease($token, 'download');
                if ($this->dbc->isError($r)) {
                	return $r;
                }
                return $res;
                break;
            case "file":
                return array();
                break;
            default:
                return PEAR::raiseError("Archive::downloadClose: NotImpl ($trtype)");
        }
    }


    /**
     * Prepare hub initiated transport
     *
     * @param string $target
     * 		hostname of transport target
     * @param string $trtype
     * 		transport type
     * @param string $direction
     * 		'up' | 'down'
     * @param array $pars
     * 		transport parameters
     * @return mixed
     */
    function prepareHubInitiatedTransfer(
        $target, $trtype='file', $direction='up',$pars=array())
    {
        $tr =& new Transport($this);
        $trec = $r = TransportRecord::create($tr, $trtype, $direction,
            array_merge($pars, array('target'=>$target))
        );
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * List hub initiated transports
     *
     * @param string $target
     * 		hostname of transport target
     * @param string $direction
     * 		'up' | 'down'
     * @param string $trtok
     * 		transport token
     * @return mixed
     */
    function listHubInitiatedTransfers(
        $target=NULL, $direction=NULL, $trtok=NULL)
    {
        $tr =& new Transport($this);
        $res = $r = $tr->getTransports($direction, $target, $trtok);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $res;
    }


    /**
     * Set state of hub initiated transport
     *
     * @param string $target
     * 		hostname of transport target
     * @param string $trtok
     * 		transport token
     * @param string $state
     * 		transport state
     * @return
     */
    function setHubInitiatedTransfer($target, $trtok, $state)
    {
        $tr =& new Transport($this);
        $trec = $r = TransportRecord::recall($tr, $trtok);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $trec->setState($state);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $res;
    }

    /* ==================================================== auxiliary methods */

} // class Archive
?>
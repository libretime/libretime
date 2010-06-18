<?php
require_once(dirname(__FILE__)."/../../storageServer/var/xmlrpc/XR_LocStor.php");
require_once(dirname(__FILE__)."/../../storageServer/var/Transport.php");

/**
 * Extension to StorageServer to act as ArchiveServer.
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage ArchiveServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class Archive extends XR_LocStor {

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
        $owner = Alib::GetSessUserId($sessid);
        if (PEAR::isError($owner)) {
        	return $owner;
        }
        $res = $this->bsOpenPut($chsum, NULL, $owner);
        if (PEAR::isError($res)) {
        	return $res;
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
        $res = $this->bsClosePut($token);
        if (PEAR::isError($res)) {
        	return $res;
        }
        extract($res);  // fname, owner
        switch ($trtype) {
            case "audioclip":
                $mdtoken = $pars['mdpdtoken'];
                $res = $this->bsClosePut($mdtoken);
                if (PEAR::isError($res)) {
                	return $res;
                }
                $mdfname = $res['fname'];
                if ($gunid == '') {
                	$gunid = NULL;
                }
                $parid = $this->_getHomeDirId($owner);
                if (PEAR::isError($parid)) {
                	return $parid;
                }
                $values = array(
                    "filename" => $pars['name'],
                    "filepath" => $fname,
                    "metadata" => $mdfname,
                    "gunid" => $pars['gunid'],
                    "filetype" => "audioclip"
                );
                $storedFile = $this->bsPutFile($parid, $values);
                if (PEAR::isError($storedFile)) {
                	return $storedFile;
                }
                $res = $storedFile->getId();
                @unlink($fname);
                @unlink($mdfname);
                break;
            case "playlist":
                if ($gunid == '') {
                	$gunid = NULL;
                }
                $parid = $this->_getHomeDirId($owner);
                if (PEAR::isError($parid)) {
                	return $parid;
                }
                $values = array(
                    "filename" => $pars['name'],
                    "metadata" => $fname,
                    "gunid" => $pars['gunid'],
                    "filetype" => "playlist"
                );
                $storedFile = $this->bsPutFile($parid, $values);
                if (PEAR::isError($storedFile)) {
                	return $storedFile;
                }
                $res = $storedFile->getId();
                @unlink($fname);
                break;
            case "playlistPkg":
                $chsum = md5_file($fname);
                // importPlaylistOpen:
                $res = $this->bsOpenPut($chsum, NULL, $owner);
                if (PEAR::isError($res)) {
                	return $res;
                }
                $dest = $res['fname'];
                $token = $res['token'];
                copy($fname, $dest);
                $r = $this->importPlaylistClose($token);
                if (PEAR::isError($r)) {
                	return $r;
                }
                @unlink($fname);
                return $r;
                break;
            case "searchjob":
                $crits = file_get_contents($fname);
                $criteria = unserialize($crits);
                @unlink($fname);
                $results = $this->localSearch($criteria);
                if (PEAR::isError($results)) {
                	return $results;
                }
                $realfile = tempnam($this->accessDir, 'searchjob_');
                @chmod($realfile, 0660);
                $len = file_put_contents($realfile, serialize($results));
                $acc = BasicStor::bsAccess($realfile, '', NULL, 'download');
                if (PEAR::isError($acc)) {
                	return $acc;
                }
                $url = BasicStor::GetUrlPart()."access/".basename($acc['fname']);
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
     * @param string $sessid
     * 		session id
     * @param string $trtype
     * 		transport type
     * @param array $pars
     * 		transport parameters
     * @return hasharray with:
     *      url string: writable URL
     *      token string: PUT token
     */
    function downloadOpen($sessid, $trtype, $pars=array())
    {
        global $CC_CONFIG;
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
            $trtype2 = BasicStor::GetType($gunid);
            if (PEAR::isError($trtype2)) {
            	return $trtype2;
            }
            // required with content:
            $trtype = ( ($trtype2 == 'playlist') && ($trtype == 'playlistPkg') ?
                'playlistPkg' : $trtype2);
			//return PEAR::raiseError("Archive::downloadOpen: TT=$trtype TT2=$trtype2 G=$gunid");
        }
        switch ($trtype) {
            case "audioclip":
                $res = $this->downloadRawAudioDataOpen($sessid, $gunid);
                break;
            case "metadata":
                $res = $this->downloadMetadataOpen($sessid, $gunid);
                break;
            case "playlist":
                $res = $this->accessPlaylist($sessid, $gunid);
                break;
            case "playlistPkg":
                $res = $this->bsExportPlaylistOpen($gunid);
                if (PEAR::isError($res)) {
                	return $res;
                }
                $tmpn = tempnam($CC_CONFIG['transDir'], 'plExport_');
                $plfpath = "$tmpn.lspl";
                copy($res['fname'], $plfpath);
                $res = $this->bsExportPlaylistClose($res['token']);
                if (PEAR::isError($res)) {
                	return $res;
                }
                $fname = "transported_playlist.lspl";
                $id = BasicStor::IdFromGunid($gunid);
                $acc = BasicStor::bsAccess($plfpath, 'lspl', NULL, 'download');
                if (PEAR::isError($acc)) {
                	return $acc;
                }
                $url = BasicStor::GetUrlPart()."access/".basename($acc['fname']);
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
                $res = array();
                break;
            default:
                return PEAR::raiseError("Archive::downloadOpen: NotImpl ($trtype)");
        }
        if (PEAR::isError($res)) {
        	return $res;
        }
        switch ($trtype) {
            case "audioclip":
            case "metadata":
            case "playlist":
            case "playlistPkg":
                $title = $this->bsGetTitle(NULL, $gunid);
                break;
            case "searchjob":
            	$title = 'searchjob';
            	break;
            case "file":
            	$title = 'regular file';
            	break;
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
                $res = $this->downloadRawAudioDataClose($token);
                if (PEAR::isError($res)) {
                	return $res;
                }
                return $res;
            case "metadata":
                $res = $this->downloadMetadataClose($token);
                return $res;
            case "playlist":
                $res = $this->releasePlaylist(NULL/*$sessid*/, $token);
                return $res;
            case "playlistPkg":
                $res = BasicStor::bsRelease($token, 'download');
                if (PEAR::isError($res)) {
                	return $res;
                }
                $realFname = $r['realFname'];
                @unlink($realFname);
                if (preg_match("|(plExport_[^\.]+)\.lspl$|", $realFname, $va)) {
                    list(,$tmpn) = $va;
                    $tmpn = $CC_CONFIG['transDir']."/$tmpn";
                    if (file_exists($tmpn)) {
                    	@unlink($tmpn);
                    }
                }
                return $res;
            case "searchjob":
                $res = BasicStor::bsRelease($token, 'download');
                return $res;
            case "file":
                return array();
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
        $tr = new Transport($this);
        $trec = TransportRecord::create($tr, $trtype, $direction,
            array_merge($pars, array('target'=>$target)));
        if (PEAR::isError($trec)) {
        	return $trec;
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
        $tr = new Transport($this);
        $res = $tr->getTransports($direction, $target, $trtok);
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
     * @return TransportRecord|PEAR_Error
     */
    function setHubInitiatedTransfer($target, $trtok, $state)
    {
        $tr = new Transport($this);
        $trec = TransportRecord::recall($tr, $trtok);
        if (PEAR::isError($trec)) {
        	return $trec;
        }
        $r = $trec->setState($state);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $trec;
    }

    /* ==================================================== auxiliary methods */

} // class Archive
?>
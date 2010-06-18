<?php
define('RENDER_EXT', 'ogg');

require_once("Playlist.php");

/**
 * Renderer caller class
 *
 * Playlist to file rendering - PHP layer, caller to the renderer executable
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 1949 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see LocStor
 */
class Renderer
{

    /**
     *  Render playlist to ogg file (open handle)
     *
     *  @param GreenBox $gb
     * 		greenbox object reference
     *  @param string $plid
     * 		playlist gunid
     *  @param int $owner
     * 		local subject id, owner of token
     *  @return array
     *      token: string - render token
     */
    function rnRender2FileOpen(&$gb, $plid, $owner=NULL)
    {
        global $CC_CONFIG;
        // recall playlist:
        $pl = StoredFile::RecallByGunid($plid);
        if (is_null($pl) || PEAR::isError($pl)) {
        	return $pl;
        }
        // smil export:
        $smil = $pl->outputToSmil();
        if (PEAR::isError($smil)) {
        	return $smil;
        }
        // temporary file for smil:
        $tmpn = tempnam($CC_CONFIG['bufferDir'], 'plRender_');
        $smilf = "$tmpn.smil";
        file_put_contents($smilf, $smil);
        $url = "file://$smilf";
        // output file:
        $outf = "$tmpn.".RENDER_EXT;
        touch($outf);
        // logging:
        $logf = $CC_CONFIG['bufferDir']."/renderer.log";
        file_put_contents($logf, "--- ".date("Ymd-H:i:s")."\n", FILE_APPEND);
        // open access to output file:         /*gunid*/      /*parent*/
        $acc = BasicStor::bsAccess($outf, RENDER_EXT, $plid, 'render', 0, $owner);
        if (PEAR::isError($acc)) {
        	return $acc;
        }
        extract($acc);
        $statf = Renderer::getStatusFile($gb, $token);
        file_put_contents($statf, "working");
        // command:
        $stServDir = dirname(__FILE__)."/..";
        $renderExe = "$stServDir/bin/renderer.sh";
        $command = "$renderExe -p $url -o $outf -s $statf >> $logf &";
        file_put_contents($logf, "$command\n", FILE_APPEND);
        $res = system($command);
        if ($res === FALSE) {
            return PEAR::raiseError(
                'Renderer::rnRender2File: Error running renderer'
            );
        }
        return array('token'=>$token);
    }


    /**
     * Render playlist to ogg file (check results)
     *
     * @param GreenBox $gb
     * 		GreenBox object reference
     * @param string $token
     * 		render token
     * @return array
     *      status : string - success | working | fault
     *      url : string - readable url
     */
    function rnRender2FileCheck(&$gb, $token)
    {
        $statf  = Renderer::getStatusFile($gb, $token);
        if (!file_exists($statf)) {
            return PEAR::raiseError(
                'Renderer::rnRender2FileCheck: Invalid token'
            );
        }
        $status = trim(file_get_contents($statf));
        $url    = Renderer::getUrl($gb, $token);
        $tmpfile= Renderer::getLocalFile($gb, $token);
        return array('status'=>$status, 'url'=>$url, 'tmpfile'=>$tmpfile);
    }


    /**
     *  Render playlist to ogg file (list results)
     *
     *  @param GreenBox $gb
     * 		greenbox object reference
     *  @param string $stat
     * 		status (optional) if this parameter is not set, then return with all unclosed backups
     *  @return array
     * 		array of hasharray:
     *      status : string - success | working | fault
     *      url : string - readable url
     */
    function rnRender2FileList(&$gb,$stat='') {
        // open temporary dir
        $tokens = BasicStor::GetTokensByType('render');
        foreach ($tokens as $token) {
            $st = Renderer::rnRender2FileCheck($gb, $token);
            if ( ($stat=='') || ($st['status']==$stat) ) {
                $r[] = $st;
            }
        }
        return $r;
    }


    /**
     * Render playlist to ogg file (close handle)
     *
     * @param GreenBox $gb
     * 		greenbox object reference
     * @param string $token
     * 		render token
     * @return mixed
     * 		TRUE or PEAR_Error
     */
    function rnRender2FileClose(&$gb, $token)
    {
        global $CC_CONFIG;
        $r = BasicStor::bsRelease($token, 'render');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $realOgg = $r['realFname'];
        $tmpn = $CC_CONFIG['bufferDir']."/".basename($realOgg, '.'.RENDER_EXT);
        $smilf = "$tmpn.smil";
        $statf = Renderer::getStatusFile($gb, $token);
        @unlink($statf);
        @unlink($realOgg);
        @unlink($smilf);
        @unlink($tmpn);
        return TRUE;
    }


    /**
     * Render playlist to storage as audioClip (check results)
     *
     * @param GreenBox $gb
     * 		greenbox object reference
     * @param string $token
     * 		render token
     * @return array
     *      status : string - success | working | fault
     *      gunid: string - global unique id of result file
     */
    function rnRender2StorageCheck(&$gb, $token)
    {
        $r = Renderer::rnRender2FileCheck($gb, $token);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $status = $r['status'];
        $res = array('status' => $status, 'gunid'=>'NULL');
        switch ($status) {
            case "fault":
                $res['faultString'] = "Error runing renderer";
                break;
            case "success":
                $r = Renderer::rnRender2StorageCore($gb, $token);
                if (PEAR::isError($r)) {
                	return $r;
                }
                $res['gunid'] = $r['gunid'];
                break;
            default:
                break;
        }
        return $res;
    }


    /**
     * Render playlist to storage as audioClip (core method)
     *
     * @param GreenBox $gb
     * 		greenbox object reference
     * @param string $token
     * 		render token
     * @return array:
     *      gunid: string - global unique id of result file
     */
    function rnRender2StorageCore(&$gb, $token)
    {
        $r = BasicStor::bsRelease($token, 'render');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $realOgg = $r['realFname'];
        $owner = $r['owner'];
        $gunid = $r['gunid'];
        $parid = $gb->_getHomeDirId($owner);
        if (PEAR::isError($parid)) {
        	return $parid;
        }
        $fileName = 'rendered_playlist';
        $id = BasicStor::IdFromGunid($gunid);
        if (PEAR::isError($id)) {
        	return $id;
        }
        $mdata = '';
        foreach (array('dc:title', 'dcterms:extent', 'dc:creator', 'dc:description') as $item) {
            $val = $gb->bsGetMetadataValue($id, $item);
            $mdata .= "  <$item>$val</$item>\n";
        }
        $mdata = "<audioClip>\n <metadata>\n$mdata </metadata>\n</audioClip>\n";
        //$mdata = "<audioClip>\n <metadata>\n$mdata<dcterms:extent>0</dcterms:extent>\n</metadata>\n</audioClip>\n";
        $values = array(
            "filename" => $fileName,
            "filepath" => $realOgg,
            "metadata" => $mdata,
            "filetype" => "audioclip"
        );
        $storedFile = $gb->bsPutFile($parid, $values);
        if (PEAR::isError($storedFile)) {
        	return $storedFile;
        }
        return array('gunid' => $storedFile->getGunid());
    }


    /**
     * Return local filepath of rendered file
     *
     * @param Greenbox $gb
     * 		greenbox object reference
     * @param string $token
     * 		render token
     * @return array
     */
    function getLocalFile(&$gb, $token)
    {
        global $CC_CONFIG;
        $token = StoredFile::NormalizeGunid($token);
        return $CC_CONFIG['accessDir']."/$token.".RENDER_EXT;
    }


    /**
     * Return filepath of render status file
     *
     * @param GreenBox $gb
     * 		greenbox object reference
     * @param string $token
     * 		render token
     * @return array
     */
    function getStatusFile(&$gb, $token)
    {
        return Renderer::getLocalFile($gb, $token).".status";
    }


    /**
     * Return remote accessible URL for rendered file
     *
     * @param GreenBox $gb
     * 		greenbox object reference
     * @param string $token
     * 		render token
     * @return array
     */
    function getUrl(&$gb, $token)
    {
        $token = StoredFile::NormalizeGunid($token);
        return BasicStor::GetUrlPart()."access/$token.".RENDER_EXT;
    }

} // class Renderer

?>
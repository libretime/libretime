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
 
 
    Author   : $Author: tomash $
    Version  : $Revision: 1949 $
    Location : $URL: svn+ssh://tomash@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/storageServer/var/BasicStor.php $

------------------------------------------------------------------------------*/

define('RENDER_EXT', 'ogg');

require_once "LsPlaylist.php";

/**
 *  Renderer caller class
 *
 *  Playlist to file rendering - PHP layer, caller to the renderer executable
 *
 *  @author  $Author: tomash $
 *  @version $Revision: 1949 $
 *  @see LocStor
 */
 
class Renderer
{

    /**
     *  Render playlist to ogg file (open handle)
     *
     *  @param gb: greenbox object reference
     *  @param plid : string  -  playlist gunid
     *  @param owner: int - local subject id, owner of token
     *  @return hasharray:
     *      token: string - render token
     */
    function rnRender2FileOpen(&$gb, $plid, $owner=NULL)
    {
        // recall playlist:
        $pl = $r = LsPlaylist::recallByGunid($gb, $plid);
        if(PEAR::isError($r)) return $r;
        // smil export:
        $smil = $r = $pl->output2Smil();
        if(PEAR::isError($r)) return $r;
        // temporary file for smil:
        $tmpn = tempnam($gb->bufferDir, 'plRender_');
        $smilf = "$tmpn.smil";
        file_put_contents($smilf, $smil);
        $url = "file://$smilf";
        // output file:
        $outf = "$tmpn.".RENDER_EXT;
        touch($outf);
        // logging:
        $logf = "{$gb->bufferDir}/renderer.log";
        file_put_contents($logf, "--- ".date("Ymd-H:i:s")."\n", FILE_APPEND);
        // open access to output file:         /*gunid*/      /*parent*/
        $acc = $gb->bsAccess($outf, RENDER_EXT, $plid, 'render', 0, $owner);
        if($gb->dbc->isError($acc)){ return $acc; }
        extract($acc);
        $statf = Renderer::getStatusFile($gb, $token);
        file_put_contents($statf, "working");
        // command:
        $stServDir = dirname(__FILE__)."/..";
        $renderExe = "$stServDir/bin/renderer.sh";
        $command = "$renderExe -p $url -o $outf -s $statf >> $logf &";
        file_put_contents($logf, "$command\n", FILE_APPEND);
        $res = system($command);
        if($res === FALSE){
            return PEAR::raiseError(
                'Renderer::rnRender2File: Error running renderer'
            );
        }
        return array('token'=>$token);
    }

    /**
     *  Render playlist to ogg file (check results)
     *
     *  @param gb: greenbox object reference
     *  @param token  :  string  -  render token
     *  @return hasharray:
     *      status : string - success | working | fault
     *      url : string - readable url
     */
    function rnRender2FileCheck(&$gb, $token)
    {
        $statf  = Renderer::getStatusFile($gb, $token);
        if(!file_exists($statf)){
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
     *  @param gb: greenbox object reference
     *  @param stat : status (optional)
     *      if this parameter is not set, then return with all unclosed backups
     *  @return array of hasharray:
     *      status : string - success | working | fault
     *      url : string - readable url
     */
    function rnRender2FileList(&$gb,$stat='') {
        # open temporary dir
        $tokens = $gb->getTokensByType('render');
        foreach ($tokens as $token) {
            $st = Renderer::rnRender2FileCheck($gb, $token);
            if ($stat=='' || $st['status']==$stat) {
                $r[] = $st;
            }
        }
        return $r;
    }
    
    /**
     *  Render playlist to ogg file (close handle)
     *
     *  @param gb: greenbox object reference
     *  @param token  :  string  -  render token
     *  @return status : boolean
     */
    function rnRender2FileClose(&$gb, $token)
    {
        $r = $gb->bsRelease($token, 'render');
        if(PEAR::isError($r)) return $r;
        $realOgg = $r['realFname'];
        $tmpn = "{$gb->bufferDir}/".basename($realOgg, '.'.RENDER_EXT);
        $smilf = "$tmpn.smil";
        $statf = Renderer::getStatusFile($gb, $token);
        @unlink($statf);
        @unlink($realOgg);
        @unlink($smilf);
        @unlink($tmpn);
        return TRUE;
    }

    /**
     *  Render playlist to storage as audioClip (check results)
     *
     *  @param gb: greenbox object reference
     *  @param token: string - render token
     *  @return hasharray:
     *      status : string - success | working | fault
     *      gunid: string - global unique id of result file
     */
    function rnRender2StorageCheck(&$gb, $token)
    {
        $r = Renderer::rnRender2FileCheck($gb, $token);
        if(PEAR::isError($r)) return $r;
        $status = $r['status'];
        $res = array('status' => $status, 'gunid'=>'NULL');
        switch($status){
            case"fault":
                $res['faultString'] = "Error runing renderer";
                break;
            case"success":
                $r = Renderer::rnRender2StorageCore($gb, $token);
                if(PEAR::isError($r)) return $r;
                $res['gunid'] = $r['gunid'];
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     *  Render playlist to storage as audioClip (core method)
     *
     *  @param gb: greenbox object reference
     *  @param token: string - render token
     *  @return hasharray:
     *      gunid: string - global unique id of result file
     */
    function rnRender2StorageCore(&$gb, $token)
    {
        $r = $gb->bsRelease($token, 'render');
        if(PEAR::isError($r)) return $r;
        $realOgg = $r['realFname'];
        $owner = $r['owner'];
        $gunid = $r['gunid'];
        if(PEAR::isError($r)) return $r;
        $parid = $r = $gb->_getHomeDirId($owner);
        if(PEAR::isError($r)) return $r;
        $fileName = 'rendered_playlist';
        $id = $r = $gb->_idFromGunid($gunid);
        if(PEAR::isError($r)) return $r;
        $mdata = '';
        foreach(array(
            'dc:title', 'dcterms:extent', 'dc:creator', 'dc:description'
        ) as $item){
            $md = $r = $gb->bsGetMetadataValue($id, $item);
            if(PEAR::isError($r)) return $r;
            $val = ( isset($md[0]) ? ( isset($md[0]['value']) ? $md[0]['value'] : '') : '');
            $mdata .= "  <$item>$val</$item>\n";
        }
        $mdata = "<audioClip>\n <metadata>\n$mdata </metadata>\n</audioClip>\n";
        //$mdata = "<audioClip>\n <metadata>\n$mdata<dcterms:extent>0</dcterms:extent>\n</metadata>\n</audioClip>\n";
        $id = $r = $gb->bsPutFile($parid, $fileName, $realOgg, $mdata,
            NULL, 'audioclip', 'string');
        if(PEAR::isError($r)) return $r;
        $ac = $r = StoredFile::recall($gb, $id);
        if(PEAR::isError($r)) return $r;
        return array('gunid' => $ac->gunid);
    }

    /**
     *  Return local filepath of rendered file
     *
     *  @param gb: greenbox object reference
     *  @param token: string - render token
     *  @return hasharray:
     */
    function getLocalFile(&$gb, $token)
    {
        $token = StoredFile::_normalizeGunid($token);
        return "{$gb->accessDir}/$token.".RENDER_EXT;
    }

    /**
     *  Return filepath of render status file
     *
     *  @param gb: greenbox object reference
     *  @param token: string - render token
     *  @return hasharray:
     */
    function getStatusFile(&$gb, $token)
    {
        return Renderer::getLocalFile($gb, $token).".status";
    }

    /**
     *  Return remote accessible URL for rendered file
     *
     *  @param gb: greenbox object reference
     *  @param token: string - render token
     *  @return hasharray:
     */
    function getUrl(&$gb, $token)
    {
        $token = StoredFile::_normalizeGunid($token);
        return $gb->getUrlPart()."access/$token.".RENDER_EXT;
    }
}

?>
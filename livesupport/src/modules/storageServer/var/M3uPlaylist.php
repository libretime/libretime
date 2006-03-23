<?
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
    Version  : $Revision: 1848 $
    Location : $URL: svn+ssh://tomash@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/storageServer/var/AccessRecur.php $

------------------------------------------------------------------------------*/

define('INDCH', ' ');

/**
 *  M3uPlaylist class
 *
 */
class M3uPlaylist {
    /**
     *  Parse M3U file or string
     *
     *  @param data string, local path to M3U file or M3U string
     *  @param loc string, location: 'file'|'string'
     *  @return array reference, parse result tree (or PEAR::error)
     */
    function &parse($data='', $loc='file')
    {
        switch($loc){
        case"file":
            if(!is_file($data)){
                return PEAR::raiseError(
                    "M3uPlaylist::parse: file not found ($data)"
                );
            }
            if(!is_readable($data)){
                return PEAR::raiseError(
                    "M3uPlaylist::parse: can't read file ($data)"
                );
            }
            $data = file_get_contents($data);
        case"string":
            $arr = preg_split("|\n#EXTINF: |", $data);
            if($arr[0] != '#EXTM3U'){
                return PEAR::raiseError(
                    "M3uPlaylist::parse: invalid M3U header"
                );
            }
            array_shift($arr);
            break;
        default:
            return PEAR::raiseError(
                "M3uPlaylist::parse: unsupported data location ($loc)"
            );
        }
        return $arr;
    }

    /**
     *  Import M3U file to storage
     *
     *  @param gb reference to GreenBox object
     *  @param aPath string, absolute path part of imported file
     *              (e.g. /home/user/livesupport)
     *  @param rPath string, relative path/filename part of imported file
     *              (e.g. playlists/playlist_1.smil)
     *  @param gunids hasharray, hash relation from filenames to gunids
     *  @param plid string,  playlist gunid
     *  @param parid int,  destination folder local id
     *  @param subjid int, local subject (user) id (id of user doing the import)
     *  @return instance of Playlist object
     */
    function import(&$gb, $aPath, $rPath, &$gunids, $plid, $parid, $subjid=NULL){
        $path = realpath("$aPath/$rPath");
        if(FALSE === $path){
            return PEAR::raiseError(
                "M3uPlaylist::import: file doesn't exist ($aPath/$rPath)"
            );
        }
        $arr = $r = M3uPlaylist::parse($path);
        if(PEAR::isError($r)) return $r;
        require_once "Playlist.php";
        $pl = $r =& Playlist::create($gb, $plid, "imported_M3U", $parid);
        if(PEAR::isError($r)) return $r;
        $r = $pl->lock($gb, $subjid);
        if(PEAR::isError($r)) return $r;
        foreach($arr as $i=>$it){
            list($md, $uri) = preg_split("|\n|", $it);
            list($length, $title) = preg_split("|, *|", $md);
            // $gunid  = StoredFile::_createGunid();
            $gunid  = ( isset($gunids[basename($uri)]) ?  $gunids[basename($uri)] : NULL);
            $acId = $r = $gb->_idFromGunid($gunid);
            if(PEAR::isError($r)) return $r;
            $length = Playlist::_secsToPlTime($length);
            $offset = '???';
            if(preg_match("|\.([a-zA-Z0-9]+)$|", $uri, $va)){
                switch(strtolower($ext = $va[1])){
                    case"lspl":
                    case"xml":
                    case"smil":
                    case"m3u":
                        $acId = $r = $gb->bsImportPlaylistRaw($parid, $gunid,
                            $aPath, $uri, $ext, $gunids, $subjid);
                        if(PEAR::isError($r)) break;
                        //no break!
                    default:
                        if(is_null($gunid)){
                            return PEAR::raiseError(
                                "M3uPlaylist::import: no gunid");
                        }
                        $r = $pl->addAudioClip($acId);
                        if(PEAR::isError($r)) return $r;
                }
            }
        }
        $r = $pl->unLock($gb);
        if(PEAR::isError($r)) return $r;
        return $pl;
    }
        
    /**
     *  Import M3U file to storage
     *
     *  @param gb reference to GreenBox object
     *  @param data string, local path to M3U file
     *  @return XML string - playlist in Livesupport playlist format
     */
    function convert2lspl(&$gb, $data){
        $arr = $r = M3uPlaylist::parse($data);
        if(PEAR::isError($r)) return $r;
        $ind = ''; $ind2 = $ind.INDCH; $ind3 = $ind2.INDCH;
        $res = '';
        foreach($arr as $i=>$it){
            list($md, $uri) = preg_split("|\n|", $it);
            list($length, $title) = preg_split("|, *|", $md);
            $gunid  = StoredFile::_createGunid();
            $gunid2 = StoredFile::_createGunid();
            $length = Playlist::_secsToPlTime($length);
            $offset = '???';
            $uri_h = preg_replace("|--|", "&#2d;&#2d;", htmlspecialchars("$uri"));
            if(preg_match("|\.([a-zA-Z0-9]+)$|", $uri, $va)){
                switch(strtolower($ext = $va[1])){
                    case"lspl":
                    case"xml":
                    case"smil":
                    case"m3u":
                        $acOrPl = "$ind3<playlist id=\"$gunid2\" ".
                            "playlength=\"$length\" title=\"$title\"/> ".
                            "<!-- $uri_h -->\n";
                        break;
                    default:
                        $acOrPl = "$ind3<audioClip id=\"$gunid2\" ".
                            "playlength=\"$length\" title=\"$title\"/> ".
                            "<!-- $uri_h -->\n";
                        break;
                }
            }
            $res .= "$ind2<playlistElement id=\"$gunid\" relativeOffset=\"$offset\">\n".
                $acOrPl.
                "$ind2</playlistElement>\n";
        }
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<playlist id=\"$gunid\" playlength=\"$playlength\" title=\"\">\n".
            "$ind2<metadata/>\n".
            "$res".
            "$ind</playlist>\n";
        return $res;
    }
}

/**
 *  Several auxiliary classes follows
 */
class M3uPlaylistBodyElement{
    function convert2lspl(&$tree, $ind=''){
        $ind2 = $ind.INDCH;
        if($tree->name != 'body'){
            return PEAR::raiseError("M3uPlaylist::parse: body tag expected");
        }
        if(isset($tree->children[1])){
            return PEAR::raiseError(sprintf(
                "M3uPlaylist::parse: unexpected tag %s in tag body",
                $tree->children[1]->name
            ));
        }
        $res = $r =
            M3uPlaylistParElement::convert2lspl($tree->children[0], $ind2);
        if(PEAR::isError($r)) return $r;
        $gunid     = StoredFile::_createGunid();
        $playlength = '???'; # ***
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<playlist id=\"$gunid\" playlength=\"$playlength\" title=\"\">\n".
            "$ind2<metadata/>\n".
            "$res".
            "$ind</playlist>\n";
        return $res;
    }
}

class M3uPlaylistParElement{
    function convert2lspl(&$tree, $ind=''){
        if($tree->name != 'par'){
            return PEAR::raiseError("M3uPlaylist::parse: par tag expected");
        }
        $res = '';
        foreach($tree->children as $i=>$ch){
            $ch =& $tree->children[$i];
            $r = M3uPlaylistAudioElement::convert2lspl($ch, $ind.INDCH);
            if(PEAR::isError($r)) return $r;
            $res .= $r;
        }
        return $res;
    }
}

class M3uPlaylistAudioElement{
    function convert2lspl(&$tree, $ind=''){
        $ind2 = $ind.INDCH;
        if($tree->name != 'audio'){
            return PEAR::raiseError("M3uPlaylist::parse: audio tag expected");
        }
        if(isset($tree->children[2])){
            return PEAR::raiseError(sprintf(
                "M3uPlaylist::parse: unexpected tag %s in tag audio",
                $tree->children[2]->name
            ));
        }
        $res = ''; $fadeIn = 0; $fadeOut = 0;
        foreach($tree->children as $i=>$ch){
            $ch =& $tree->children[$i];
            $r = M3uPlaylistAnimateElement::convert2lspl($ch, $ind2);
            if(PEAR::isError($r)) return $r;
            switch($r['type']){
                case"fadeIn":  $fadeIn  = $r['val']; break;
                case"fadeOut": $fadeOut = $r['val']; break;
            }
        }
        if($fadeIn > 0 || $fadeOut > 0){
            $fadeIn  = Playlist::_secsToPlTime($fadeIn);
            $fadeOut = Playlist::_secsToPlTime($fadeOut);
            $fInfo  = "$ind2<fadeInfo fadeIn=\"$fadeIn\" fadeOut=\"$fadeOut\"/>\n";
        }else $fInfo = '';
        $plElGunid  = StoredFile::_createGunid();
        $aGunid     = StoredFile::_createGunid();
        $title      = basename($tree->attrs['src']->val);
        $offset     = Playlist::_secsToPlTime($tree->attrs['begin']->val);
        $playlength = '???'; # ***
        $res = "$ind<playlistElement id=\"$plElGunid\" relativeOffset=\"$offset\">\n".
            "$ind2<audioClip id=\"$aGunid\" playlength=\"$playlength\" title=\"$title\"/>\n".
            $fInfo.
            "$ind</playlistElement>\n";
        return $res;
    }
}

class M3uPlaylistAnimateElement{
    function convert2lspl(&$tree, $ind=''){
        if($tree->name != 'animate'){
            return PEAR::raiseError("M3uPlaylist::parse: animate tag expected");
        }
        if($tree->attrs['attributeName']->val == 'soundLevel' &&
            $tree->attrs['from']->val == '0%' &&
            $tree->attrs['to']->val == '100%' &&
            $tree->attrs['calcMode']->val == 'linear' &&
            $tree->attrs['fill']->val == 'freeze' &&
            $tree->attrs['begin']->val == '0s' &&
            preg_match("|^([0-9.]+)s$|", $tree->attrs['end']->val, $va)
        ){
            return array('type'=>'fadeIn', 'val'=>$va[1]);
        }
        if($tree->attrs['attributeName']->val == 'soundLevel' &&
            $tree->attrs['from']->val == '100%' &&
            $tree->attrs['to']->val == '0%' &&
            $tree->attrs['calcMode']->val == 'linear' &&
            $tree->attrs['fill']->val == 'freeze' &&
            preg_match("|^([0-9.]+)s$|", $tree->attrs['begin']->val, $vaBegin) &&
            preg_match("|^([0-9.]+)s$|", $tree->attrs['end']->val, $vaEnd)
        ){
            return array('type'=>'fadeOut', 'val'=>($vaEnd[1] - $vaBegin[1]));
        }
        return PEAR::raiseError(
            "M3uPlaylistAnimateElement::convert2lspl: animate parameters too general"
        );
    }
}

?>

<?
define('INDCH', ' ');

/**
 * M3uPlaylist class
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version  : $Revision: 1848 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class M3uPlaylist {

    /**
     * Parse M3U file or string
     *
     * @param string $data
     * 		local path to M3U file or M3U string
     * @param string $loc
     * 		location: 'file'|'string'
     * @return array
     * 		reference, parse result tree (or PEAR::error)
     */
    function &parse($data='', $loc='file')
    {
        switch ($loc) {
        case "file":
            if (!is_file($data)) {
                return PEAR::raiseError(
                    "M3uPlaylist::parse: file not found ($data)"
                );
            }
            if (!is_readable($data)) {
                return PEAR::raiseError(
                    "M3uPlaylist::parse: can't read file ($data)"
                );
            }
            $data = file_get_contents($data);
        case "string":
            $arr = preg_split("|\n#EXTINF: |", $data);
            if ($arr[0] != '#EXTM3U') {
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
     * @param GreenBox $gb
     * @param string $aPath
     * 		absolute path part of imported file (e.g. /home/user/campcaster)
     * @param string $rPath
     * 		relative path/filename part of imported file
     *      (e.g. playlists/playlist_1.smil)
     * @param array $gunids
     * 		hash relation from filenames to gunids
     * @param string $plid
     * 		playlist gunid
     * @param int $parid
     * 		destination folder local id
     * @param int $subjid
     * 		local subject (user) id (id of user doing the import)
     * @return Playlist
     */
    function import(&$gb, $aPath, $rPath, &$gunids, $plid, $parid, $subjid=NULL)
    {
        $path = realpath("$aPath/$rPath");
        if (FALSE === $path) {
            return PEAR::raiseError(
                "M3uPlaylist::import: file doesn't exist ($aPath/$rPath)"
            );
        }
        $arr = M3uPlaylist::parse($path);
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        require_once("Playlist.php");
        $pl =& Playlist::create($gb, $plid, "imported_M3U", $parid);
        if (PEAR::isError($pl)) {
        	return $pl;
        }
        $r = $pl->lock($gb, $subjid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        foreach ($arr as $i => $it) {
            list($md, $uri) = preg_split("|\n|", $it);
            list($length, $title) = preg_split("|, *|", $md);
            // $gunid  = StoredFile::CreateGunid();
            $gunid = ( isset($gunids[basename($uri)]) ?  $gunids[basename($uri)] : NULL);
            $acId = BasicStor::IdFromGunid($gunid);
            if (PEAR::isError($acId)) {
            	return $acId;
            }
            $length = Playlist::secondsToPlaylistTime($length);
            $offset = '???';
            if (preg_match("|\.([a-zA-Z0-9]+)$|", $uri, $va)) {
                switch (strtolower($ext = $va[1])) {
                    case "lspl":
                    case "xml":
                    case "smil":
                    case "m3u":
                        $acId = $gb->bsImportPlaylistRaw($parid, $gunid,
                            $aPath, $uri, $ext, $gunids, $subjid);
                        if (PEAR::isError($acId)) {
                        	break;
                        }
                        //no break!
                    default:
                        if (is_null($gunid)) {
                            return PEAR::raiseError(
                                "M3uPlaylist::import: no gunid");
                        }
                        $r = $pl->addAudioClip($acId);
                        if (PEAR::isError($r)) {
                        	return $r;
                        }
                }
            }
        }
        $r = $pl->unlock($gb);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $pl;
    }

    /**
     * Import M3U file to storage
     *
     * @param GreenBox $gb
     * @param string $data
     * 		local path to M3U file
     * @return string
     * 		XML playlist in Campcaster playlist format
     */
    function convert2lspl(&$gb, $data)
    {
        $arr = M3uPlaylist::parse($data);
        if (PEAR::isError($arr)) {
        	return $arr;
        }
        $ind = '';
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $res = '';
        foreach ($arr as $i => $it) {
            list($md, $uri) = preg_split("|\n|", $it);
            list($length, $title) = preg_split("|, *|", $md);
            $gunid = StoredFile::CreateGunid();
            $gunid2 = StoredFile::CreateGunid();
            $length = Playlist::secondsToPlaylistTime($length);
            $offset = '???';
            $clipStart = '???';
            $clipEnd = '???';
            $clipLength = '???';
            $uri_h = preg_replace("|--|", "&#2d;&#2d;", htmlspecialchars("$uri"));
            if (preg_match("|\.([a-zA-Z0-9]+)$|", $uri, $va)) {
                switch (strtolower($ext = $va[1])) {
                    case "lspl":
                    case "xml":
                    case "smil":
                    case "m3u":
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
            $res .= "$ind2<playlistElement id=\"$gunid\" relativeOffset=\"$offset\" clipStart=\"$clipStart\" clipEnd=\"$clipEnd\" clipLength=\"$clipLength\">\n".
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
} // class M3uPlaylist


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class M3uPlaylistBodyElement {
    function convert2lspl(&$tree, $ind='')
    {
        $ind2 = $ind.INDCH;
        if ($tree->name != 'body') {
            return PEAR::raiseError("M3uPlaylist::parse: body tag expected");
        }
        if (isset($tree->children[1])) {
            return PEAR::raiseError(sprintf(
                "M3uPlaylist::parse: unexpected tag %s in tag body",
                $tree->children[1]->name
            ));
        }
        $res = M3uPlaylistParElement::convert2lspl($tree->children[0], $ind2);
        if (PEAR::isError($res)) {
        	return $res;
        }
        $gunid = StoredFile::CreateGunid();
        $playlength = '???'; // ***
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<playlist id=\"$gunid\" playlength=\"$playlength\" title=\"\">\n".
            "$ind2<metadata/>\n".
            "$res".
            "$ind</playlist>\n";
        return $res;
    }
}


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class M3uPlaylistParElement {
    function convert2lspl(&$tree, $ind='')
    {
        if ($tree->name != 'par') {
            return PEAR::raiseError("M3uPlaylist::parse: par tag expected");
        }
        $res = '';
        foreach ($tree->children as $i => $ch) {
            $ch =& $tree->children[$i];
            $r = M3uPlaylistAudioElement::convert2lspl($ch, $ind.INDCH);
            if (PEAR::isError($r)) {
            	return $r;
            }
            $res .= $r;
        }
        return $res;
    }
}


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class M3uPlaylistAudioElement {
    function convert2lspl(&$tree, $ind='')
    {
        $ind2 = $ind.INDCH;
        if ($tree->name != 'audio') {
            return PEAR::raiseError("M3uPlaylist::parse: audio tag expected");
        }
        if (isset($tree->children[2])) {
            return PEAR::raiseError(sprintf(
                "M3uPlaylist::parse: unexpected tag %s in tag audio",
                $tree->children[2]->name
            ));
        }
        $res = ''; $fadeIn = 0; $fadeOut = 0;
        foreach ($tree->children as $i => $ch) {
            $ch =& $tree->children[$i];
            $r = M3uPlaylistAnimateElement::convert2lspl($ch, $ind2);
            if (PEAR::isError($r)) {
            	return $r;
            }
            switch ($r['type']) {
                case "fadeIn":
                	$fadeIn  = $r['val'];
                	break;
                case "fadeOut":
                	$fadeOut = $r['val'];
                	break;
            }
        }
        if ($fadeIn > 0 || $fadeOut > 0) {
            $fadeIn  = Playlist::secondsToPlaylistTime($fadeIn);
            $fadeOut = Playlist::secondsToPlaylistTime($fadeOut);
            $fInfo  = "$ind2<fadeInfo fadeIn=\"$fadeIn\" fadeOut=\"$fadeOut\"/>\n";
        } else {
        	$fInfo = '';
        }
        $plElGunid = StoredFile::CreateGunid();
        $aGunid = StoredFile::CreateGunid();
        $title = basename($tree->attrs['src']->val);
        $offset = Playlist::secondsToPlaylistTime($tree->attrs['begin']->val);
        $playlength = '???'; # ***
        $res = "$ind<playlistElement id=\"$plElGunid\" relativeOffset=\"$offset\">\n".
            "$ind2<audioClip id=\"$aGunid\" playlength=\"$playlength\" title=\"$title\"/>\n".
            $fInfo.
            "$ind</playlistElement>\n";
        return $res;
    }
} // class M3uPlaylistAudioElement


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class M3uPlaylistAnimateElement {
    function convert2lspl(&$tree, $ind='') {
        if ($tree->name != 'animate') {
            return PEAR::raiseError("M3uPlaylist::parse: animate tag expected");
        }
        if ($tree->attrs['attributeName']->val == 'soundLevel' &&
            $tree->attrs['from']->val == '0%' &&
            $tree->attrs['to']->val == '100%' &&
            $tree->attrs['calcMode']->val == 'linear' &&
            $tree->attrs['fill']->val == 'freeze' &&
            $tree->attrs['begin']->val == '0s' &&
            preg_match("|^([0-9.]+)s$|", $tree->attrs['end']->val, $va)
        ) {
            return array('type'=>'fadeIn', 'val'=>$va[1]);
        }
        if ($tree->attrs['attributeName']->val == 'soundLevel' &&
            $tree->attrs['from']->val == '100%' &&
            $tree->attrs['to']->val == '0%' &&
            $tree->attrs['calcMode']->val == 'linear' &&
            $tree->attrs['fill']->val == 'freeze' &&
            preg_match("|^([0-9.]+)s$|", $tree->attrs['begin']->val, $vaBegin) &&
            preg_match("|^([0-9.]+)s$|", $tree->attrs['end']->val, $vaEnd)
        ) {
            return array('type'=>'fadeOut', 'val'=>($vaEnd[1] - $vaBegin[1]));
        }
        return PEAR::raiseError(
            "M3uPlaylistAnimateElement::convert2lspl: animate parameters too general"
        );
    }
}

?>
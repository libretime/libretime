<?
require_once "Playlist.php";

define('INDCH', ' ');
define('AC_URL_RELPATH', '../audioClip/');
define('PL_URL_RELPATH', '../playlist/');

/**
 * LsPlaylist class
 *
 * Internal playlist format helper.
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 1848 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @todo Rename this class
 */
class LsPlaylist extends Playlist
{
    /**
     *  Create instance of LsPlaylist object and recall existing file
     *  by gunid.<br/>
     *
     *  @param Greenbox $gb, reference to GreenBox object
     *  @param string $gunid, global unique id
     *  @param string $className, optional classname to recall
     *  @return instance of LsPlaylist object
     */
    function &recallByGunid(&$gb, $gunid, $className='LsPlaylist')
    {
        return parent::recallByGunid($gb, $gunid, $className);
    }


    /**
     *  Create instance of LsPlaylist object and recall existing file
     *  by access token.<br/>
     *
     *  @param GreenBox $gb, reference to GreenBox object
     *  @param string $token, access token
     *  @param string $className, optional classname to recall
     *  @return instance of LsPlaylist object
     */
    function &recallByToken(&$gb, $token, $className='LsPlaylist')
    {
        return parent::recallByToken($gb, $token, $className);
    }


    /**
     *  Export playlist as simplified SMIL XML file
     *
     *  @param boolean $toString
     *		if false don't real export,
     *      return misc info about playlist only
     *  @return string
     * 		XML string or hasharray with misc info
     */
    function output2Smil($toString=TRUE)
    {
        $plGunid = $this->gunid;
        $arr = $r = $this->md->genPhpArray();
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($toString) {
            $r = LsPlaylistTag::output2Smil($this, $arr);
            if (PEAR::isError($r)) {
            	return $r;
            }
            return $r;
        } else {
            return array(
                'type'       => 'playlist',
                'gunid'      => $plGunid,
                'src'        => PL_URL_RELPATH."$plGunid.smil",
                'playlength' => $arr['attrs']['playlength'],
            );
        }
    }


    /**
     *  Export playlist as M3U file
     *
     *  @param toString boolean, if false don't real export,
     *        return misc info about playlist only
     *  @return M3U string or hasharray with misc info
     */
    function output2m3u($toString=TRUE)
    {
        $plGunid = $this->gunid;
        $arr = $r = $this->md->genPhpArray();
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($toString) {
            $r = LsPlaylistTag::output2m3u($this, $arr);
            if (PEAR::isError($r)) {
            	return $r;
            }
            return $r;
        } else {
            return array(
                'type'       => 'playlist',
                'gunid'      => $plGunid,
                'uri'        => PL_URL_RELPATH."$plGunid.m3u",
                'playlength' => $arr['attrs']['playlength'],
                'title'      => $arr['attrs']['title'],
            );
        }
    }


    /**
     * Export playlist as RSS XML file
     *
     * @param boolean $toString
     * 		if false don't really export,
     *      return misc info about playlist only
     * @return mixed
     * 		XML string or hasharray with misc info
     */
    function output2RSS($toString=TRUE)
    {
        $plGunid = $this->gunid;
        $arr = $r = $this->md->genPhpArray();
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($toString) {
            $r = LsPlaylistTag::output2RSS($this, $arr);
            if (PEAR::isError($r)) {
            	return $r;
            }
            return $r;
        } else {
            return array(
                'type'       => 'playlist',
                'gunid'      => $plGunid,
                'src'        => PL_URL_RELPATH."$plGunid.smil",
                'playlength' => $arr['attrs']['playlength'],
            );
        }
    }

} // class LsPlaylist


/**
 * Several auxiliary classes follows
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @todo Rename this class PlaylistTag
 */
class LsPlaylistTag
{
    function output2Smil(&$pl, $plt, $ind='')
    {
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $ind4 = $ind3.INDCH;
        $res = "";
        foreach ($plt['children'] as $ple) {
            switch ($ple['elementname']) {
                case"playlistElement":
                    $r = LsPlaylistElement::output2Smil($pl, $ple, $ind4);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
                case"metadata":
                    $r = LsPlaylistMetadata::output2Smil($pl, $ple, $ind4);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
                default:
            }
        }
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<smil xmlns=\"http://www.w3.org/2001/SMIL20/Language\">\n".
            "$ind2<body>\n".
            "$ind3<par>\n".
            "$res".
            "$ind3</par>\n".
            "$ind2</body>\n".
            "$ind</smil>\n";
        return $res;
    }


    function output2m3u(&$pl, $plt, $ind='')
    {
        $res = "";
        foreach ($plt['children'] as $ple) {
            switch ($ple['elementname']) {
                case"playlistElement":
                    $r = LsPlaylistElement::output2m3u($pl, $ple);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
            }
        }
        $res = "#EXTM3U\n$res";
        return $res;
    }


    function output2RSS(&$pl, $plt, $ind='')
    {
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $res = "";
        foreach ($plt['children'] as $ple) {
            switch ($ple['elementname']) {
                case"playlistElement":
                    $r = LsPlaylistElement::output2RSS($pl, $ple, $ind3);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
                case"metadata":
                    $r = LsPlaylistMetadata::output2RSS($pl, $ple, $ind3);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$res .= $r;
                    }
                break;
                default:
            }
        }
        $res = "$ind<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
            "$ind<rss version=\"2.0\">\n".
            "$ind2<channel>\n".
            "$res".
            "$ind2</channel>\n".
            "$ind</rss>\n";
        return $res;
    }
} // class LsPlaylistTag


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @todo Rename this class "PlaylistElement"
 */
class LsPlaylistElement {


    function output2Smil(&$pl, $ple, $ind='')
    {
        $acOrPl = NULL;
        $finfo = array('fi'=>0, 'fo'=>0);
        $ind2 = $ind.INDCH;
        $ind3 = $ind2.INDCH;
        $anim = '';
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = LsPlaylistAudioClip::output2Smil($pl, $ac, $ind2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                break;
                case "playlist":
                    $gunid = $ac['attrs']['id'];
                    $pl2 = $r = LsPlaylist::recallByGunid($pl->gb, $gunid);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    $r = $pl2->output2Smil(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                break;
                case"fadeInfo":
                    $r = LsPlaylistFadeInfo::output2Smil($pl, $ac, $ind2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$finfo = $r;
                    }
                break;
                default:
                    return PEAR::raiseError(
                        "LsPlaylistElement::output2Smil:".
                        " unknown tag {$ac['elementname']}"
                    );
            }
        }
        $beginS      = $pl->_plTimeToSecs($ple['attrs']['relativeOffset']);
        $playlengthS = $pl->_plTimeToSecs($acOrPl['playlength']);
        $fadeOutS    = $pl->_plTimeToSecs($finfo['fo']);
        $fiBeginS    = 0;
        $fiEndS      = $pl->_plTimeToSecs($finfo['fi']);
        $foBeginS    = ($playlengthS - $fadeOutS);
        $foEndS      = $pl->_plTimeToSecs($acOrPl['playlength']);
        foreach (array('fi','fo') as $ff) {
            if (${$ff."EndS"} - ${$ff."BeginS"} > 0) {
                $anim .= "{$ind2}<animate attributeName = \"soundLevel\"\n".
                    "{$ind3}from = \"".($ff == 'fi' ? 0 : 100)."%\"\n".
                    "{$ind3}to = \"".($ff == 'fi' ? 100 : 0)."%\"\n".
                    "{$ind3}calcMode = \"linear\"\n".
                    "{$ind3}begin = \"{${$ff."BeginS"}}s\"\n".
                    "{$ind3}end = \"{${$ff."EndS"}}s\"\n".
                    "{$ind3}fill = \"freeze\"\n".
                    "{$ind2}/>\n"
                ;
            }
        }
        $src = $acOrPl['src'];
        $str = "$ind<audio src=\"$src\" begin=\"{$beginS}s\"".
            ($anim ? ">\n$anim$ind</audio>" : " />").
            " <!-- {$acOrPl['type']}, {$acOrPl['gunid']}, {$acOrPl['playlength']}  -->".
            "\n";
        return $str;
    }


    function output2m3u(&$pl, $ple, $ind='')
    {
        $acOrPl = NULL;
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = LsPlaylistAudioClip::output2m3u($pl, $ac);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                break;
                case "playlist":
                    $gunid = $ac['attrs']['id'];
                    $pl2 = $r = LsPlaylist::recallByGunid($pl->gb, $gunid);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    $r = $pl2->output2m3u(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                break;
            }
        }
        if (is_null($acOrPl)) {
        	return '';
        }
        $playlength = ceil($pl->_plTimeToSecs($acOrPl['playlength']));
        $title = $acOrPl['title'];
        $uri = (isset($acOrPl['uri']) ? $acOrPl['uri'] : '???' );
        $res  = "#EXTINF: $playlength, $title\n";
        $res .= "$uri\n";
        return $res;
    }


    function output2RSS(&$pl, $ple, $ind='')
    {
        $acOrPl = NULL;
        $ind2 = $ind.INDCH;
        $anim = '';
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = LsPlaylistAudioClip::output2RSS($pl, $ac, $ind2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                break;
                case"playlist":
                    $gunid = $ac['attrs']['id'];
                    $pl2 = $r = LsPlaylist::recallByGunid($pl->gb, $gunid);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    $r = $pl2->output2RSS(FALSE);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    if (!is_null($r)) {
                    	$acOrPl = $r;
                    }
                break;
                case"fadeInfo":
                break;
                default:
                    return PEAR::raiseError(
                        "LsPlaylistElement::output2RSS:".
                        " unknown tag {$ac['elementname']}"
                    );
            }
        }
        $title = (isset($acOrPl['title']) ? htmlspecialchars($acOrPl['title']) : '' );
        $desc = (isset($acOrPl['desc']) ? htmlspecialchars($acOrPl['desc']) : '' );
        $link = htmlspecialchars($acOrPl['src']);
        $desc = '';
        $str = "$ind<item>\n".
            "$ind2<title>$title</title>\n".
            "$ind2<description>$desc</description>\n".
            "$ind2<link>$link</link>\n".
            "$ind</item>\n";
        return $str;
    }
}


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @todo Rename this class to PlaylistAudioClip (notice the caps)
 */
class LsPLaylistAudioClip
{

    function output2Smil(&$pl, $plac, $ind='')
    {
        $gunid = $plac['attrs']['id'];
        $ac = $r = StoredFile::recallByGunid($pl->gb, $gunid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $RADext = $r =$ac->_getExt();
        if (PEAR::isError($r)) {
        	return $r;
        }
        return array(
            'type'       => 'audioclip',
            'gunid'      => $gunid,
            'src'        => AC_URL_RELPATH."$gunid.$RADext",
            'playlength' => $plac['attrs']['playlength'],
        );
    }


    function output2m3u(&$pl, $plac, $ind='')
    {
        $gunid = $plac['attrs']['id'];
        $ac = $r = StoredFile::recallByGunid($pl->gb, $gunid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $RADext = $r =$ac->_getExt();
        if (PEAR::isError($r)) {
        	return $r;
        }
        return array(
            'playlength' => $plac['attrs']['playlength'],
            'title'      => $plac['attrs']['title'],
            'uri'        => AC_URL_RELPATH."$gunid.$RADext",
        );
    }


    function output2RSS(&$pl, $plac, $ind='')
    {
        $gunid = $plac['attrs']['id'];
        $ac = $r = StoredFile::recallByGunid($pl->gb, $gunid);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $RADext = $r =$ac->_getExt();
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $pl->gb->bsGetMetadataValue($ac->getId(), 'dc:title');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $title = ( isset($r[0]) ? $r[0]['value'] : '' );
        $r = $pl->gb->bsGetMetadataValue($ac->getId(), 'dc:description');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $desc = ( isset($r[0]) ? $r[0]['value'] : '' );
        return array(
            'type'       => 'audioclip',
            'gunid'      => $gunid,
            'src'        => "http://XXX/YY/$gunid.$RADext",
            'playlength' => $plac['attrs']['playlength'],
            'title'      => $title,
            'desc'      => $desc,
        );
    }
} // class LsPlaylistAudioClip


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @todo Rename this class "PlaylistFadeInfo" (notive the caps)
 */
class LsPLaylistFadeInfo
{

    function output2Smil(&$pl, $plfi, $ind='')
    {
        $r = array(
            'fi'=>$plfi['attrs']['fadeIn'],
            'fo'=>$plfi['attrs']['fadeOut'],
        );
        return $r;
    }


    function output2m3u(&$pl, $plfa, $ind='')
    {
    	return '';
    }


    function output2RSS(&$pl, $plfa, $ind='')
    {
    	return '';
    }

} // class LsPlaylistFadeInfo


/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @todo Rename this class to PlaylistMetadata (notice the caps)
 */
class LsPLaylistMetadata
{
    function output2Smil(&$pl, $md, $ind='')
    {
    	return NULL;
    }


    function output2m3u(&$pl, $md, $ind='')
    {
    	return NULL;
    }


    function output2RSS(&$pl, $md, $ind='')
    {
    	return NULL;
    }
} // class PlaylistMetadata
?>
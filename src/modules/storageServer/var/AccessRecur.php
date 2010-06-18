<?
/**
 * AccessRecur class
 *
 * Handles recursive accessPlaylist/releasePlaylist.
 * Should be 'required_once' from LocStor.php only.
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
class AccessRecur {
    public $ls;
    public $sessid;

    public function __construct(&$ls, $sessid)
    {
        $this->ls =& $ls;
        $this->sessid = $sessid;
    }


    public static function accessPlaylist(&$ls, $sessid, $plid, $parent='0')
    {
        $ppa = new AccessRecur($ls, $sessid);
        $r = $ls->accessPlaylist($sessid, $plid, FALSE, $parent);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $plRes = $r;
        $r = StoredFile::RecallByGunid($plid);
        if (is_null($r) || PEAR::isError($r)) {
        	return $r;
        }
        $ac = $r;
        $r = $ac->md->genPhpArray();
        if (PEAR::isError($r)) {
        	return $r;
        }
        $pla = $r;
        $r = $ppa->processPlaylist($pla, $plRes['token']);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $plRes['content'] = $r;
        return $plRes;
    }


    public static function releasePlaylist(&$ls, $sessid, $token)
    {
        global $CC_CONFIG, $CC_DBC;
        $ppa = new AccessRecur($ls, $sessid);
        $r = $CC_DBC->getAll("
            SELECT to_hex(token)as token2, to_hex(gunid)as gunid
            FROM ".$CC_CONFIG['accessTable']."
            WHERE parent=x'{$token}'::bigint
        ");
        if (PEAR::isError($r)) {
        	return $r;
        }
        $arr = $r;
        foreach ($arr as $i => $item) {
            extract($item);     // token2, gunid
            $r = BasicStor::GetType($gunid);
            if (PEAR::isError($r)) {
            	return $r;
            }
            $ftype = $r;
            # echo "$ftype/$token2\n";
            switch (strtolower($ftype)) {
                case "audioclip":
                    $r = $ppa->ls->releaseRawAudioData($ppa->sessid, $token2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    # var_dump($r);
                break;
                case "playlist":
                    $r = $ppa->releasePlaylist($ppa->ls, $ppa->sessid, $token2);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    # var_dump($r);
                break;
                default:
            }
        }
        $r = $ppa->ls->releasePlaylist($ppa->sessid, $token, FALSE);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $r;
    }


    private function processPlaylist($pla, $parent)
    {
        $res = array();
        foreach ($pla['children'] as $ple) {
            switch ($ple['elementname']) {
                case "playlistElement":
                    $r = $this->processPlaylistElement($ple, $parent);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    // $res = array_merge($res, $r);
                    $res[] = $r;
                break;
                default:
            }
        }
        return $res;
    }


    private function processAudioClip($gunid, $parent)
    {
        $r = $this->ls->accessRawAudioData($this->sessid, $gunid, $parent);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $r;
    }


    private function processPlaylistElement($ple, $parent='0')
    {
        foreach ($ple['children'] as $ac) {
            switch ($ac['elementname']) {
                case "audioClip":
                    $r = $this->processAudioClip($ac['attrs']['id'], $parent);
                    if (PEAR::isError($r)) {
                    	return $r;
                    }
                    return $r;
                case "playlist":
//                    if(empty($ac['children'])){
                        $r = $this->accessPlaylist($this->ls, $this->sessid,
                            $ac['attrs']['id'], $parent);
                        if (PEAR::isError($r)) {
                            if ($r->getCode() != GBERR_NOTF) {
                            	return $r;
                            } else {
                                $r = $this->processPlaylist($ac, $parent);
                                if (PEAR::isError($r)) {
                                	return $r;
                                }
                                $r = array(
                                    'content'   => $r,
                                    'url'       => NULL,
                                    'token'     => NULL,
                                    'chsum'     => NULL,
                                    'size'      => NULL,
                                    'warning'    => 'inline playlist?',
                                );
                            }
                        }
                        return $r;
/*
                    }else{
                        $r = $this->processPlaylist($ac, $parent);
                        if(PEAR::isError($r)) return $r;
                        $res = array(
                            'content'   => $r,
                            'url'       => NULL,
                            'token'     => NULL,
                            'chsum'     => NULL,
                            'size'      => NULL,
                            'warning'    => 'inline playlist',
                        );
                        return $res;
                    }
*/
                break;
                default:
            }
        }
        return array();
    }

} // class AccessRecur
?>
<?php
require_once('Subjects.php');

define('USE_ALIB_CLASSES', TRUE);
define('ALIBERR_NOTLOGGED', 30);
define('ALIBERR_NOTEXISTS', 31);

/**
 * Authentication/authorization class
 *
 * @package Airtime
 * @subpackage Alib
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class Alib {
    /* ======================================================= public methods */

    /* ----------------------------------------------- session/authentication */

    /* -------------------------------------------------------- authorization */
    /**
     * Remove permission record
     *
     * @param int $permid
     * 		local permission id
     * @param int $subj
     * 		local user/group id
     * @param int $obj
     * 		local object id
     * @return boolean|PEAR_Error
     */
    public static function RemovePerm($permid=NULL, $subj=NULL, $obj=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $ca = array();
        if ($permid) {
            $ca[] = "permid=$permid";
        }
        if ($subj) {
            $ca[] = "subj=$subj";
        }
        if ($obj) {
            $ca[] = "obj=$obj";
        }
        $cond = join(" AND ", $ca);
        if (!$cond) {
            return TRUE;
        }
        $sql = "DELETE FROM ".$CC_CONFIG['permTable']." WHERE $cond";
        return $CC_DBC->query($sql);
    } // fn removePerm


    /* ---------------------------------------------------------- object tree */

    /* --------------------------------------------------------- users/groups */

    /* ------------------------------------------------------------- sessions */
    /**
     * Get login from session id (token)
     *
     * @param string $sessid
     * @return string|PEAR_Error
     */
    public static function GetSessLogin($sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT login FROM ".$CC_CONFIG['sessTable']." WHERE sessid='$sessid'";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        if (is_null($r)){
            return PEAR::raiseError("Alib::GetSessLogin:".
                " invalid session id ($sessid)",
            ALIBERR_NOTEXISTS,  PEAR_ERROR_RETURN);
        }
        return $r;
    } // fn GetSessLogin


    /**
     * Get user id from session id.
     *
     * @param string $p_sessid
     * @return int|PEAR_Error
     */
    public static function GetSessUserId($p_sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT userid FROM ".$CC_CONFIG['sessTable']." WHERE sessid='$p_sessid'";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        if (is_null($r)) {
            return PEAR::raiseError("Alib::getSessUserId:".
                " invalid session id ($p_sessid)",
            ALIBERR_NOTEXISTS,  PEAR_ERROR_RETURN);
        }
        return $r;
    } // fn getSessUserId


    /* --------------------------------------------------------- info methods */
    /**
     * Get all permissions on object.
     *
     * @param int $id
     * @return array|null|PEAR_Error
     */
    public static function GetObjPerms($id)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT s.login, p.* FROM ".$CC_CONFIG['permTable']." p, ".$CC_CONFIG['subjTable']." s"
        ." WHERE s.id=p.subj AND p.obj=$id";
        return $CC_DBC->getAll($sql);
    } // fn GetObjPerms


    /**
     * Get all permissions of subject.
     *
     * @param int $sid
     * @return array
     */
    public static function GetSubjPerms($sid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT *"
        ." FROM ".$CC_CONFIG['permTable']
        ." WHERE p.subj=$sid";
        $a1 = $CC_DBC->getAll($sql);
        return $a1;
    } // fn GetSubjPerms


    /* ------------------------ info methods related to application structure */
    /* (this part should be added/rewritten to allow defining/modifying/using
     * application structure)
     * (only very simple structure definition - in $CC_CONFIG - supported now)
     */

    /* ====================================================== private methods */

    /**
     * Create new session id.  Return the new session ID.
     *
     * @return string
     */
    private static function _createSessid()
    {
        global $CC_CONFIG, $CC_DBC;
        for ($c = 1; $c > 0; ){
            $sessid = md5(uniqid(rand()));
            $sql = "SELECT count(*) FROM ".$CC_CONFIG['sessTable']
            ." WHERE sessid='$sessid'";
            $c = $CC_DBC->getOne($sql);
            if (PEAR::isError($c)) {
                return $c;
            }
        }
        return $sessid;
    } // fn _createSessid


} // class Alib

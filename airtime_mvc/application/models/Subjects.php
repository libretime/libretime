<?php
define('ALIBERR_NOTGR', 20);
define('ALIBERR_BADSMEMB', 21);

/**
 * Subj class
 *
 * users + groups
 * with "linearized recursive membership" ;)
 *   (allow adding users to groups or groups to groups)
 *
 * @package Airtime
 * @subpackage Alib
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Subjects {

    /* ======================================================= public methods */

    /**
     * Check login and password
     *
     * @param string $login
     * @param string $pass
     * 		optional
     * @return boolean|int|PEAR_Error
     */
    public static function Authenticate($login, $pass='')
    {
        global $CC_CONFIG, $CC_DBC;
        $cpass = md5($pass);
        $sql = "SELECT id FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login' AND pass='$cpass' AND type='U'";
        $id = $CC_DBC->getOne($sql);
        if (PEAR::isError($id)) {
            return $id;
        }
        return (is_null($id) ? FALSE : $id);
    } // fn authenticate


    /**
     * Change user password
     *
     * @param string $login
     * @param string $oldpass
     * 		old password (optional for 'superuser mode')
     * @param string $pass
     * 		optional
     * @param boolean $passenc
     * 		optional, password already encrypted if true
     * @return boolean|PEAR_Error
     */
    public static function Passwd($login, $oldpass=null, $pass='', $passenc=FALSE)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!$passenc) {
            $cpass = md5($pass);
        } else {
            $cpass = $pass;
        }
        if (!is_null($oldpass)) {
            $oldcpass = md5($oldpass);
            $oldpCond = "AND pass='$oldcpass'";
        } else {
            $oldpCond = '';
        }
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET pass='$cpass'"
            ." WHERE login='$login' $oldpCond AND type='U'";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn passwd


    /* --------------------------------------------------------------- groups */

    /* --------------------------------------------------------- info methods */

    /**
     * Get subject id from login
     *
     * @param string $login
     * @return int|PEAR_Error
     */
    public static function GetSubjId($login)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $sql = "SELECT id FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login'";
        return $CC_DBC->getOne($sql);
    } // fn getSubjId


    /**
     * Return true if uid is [id]direct member of gid
     *
     * @param int $uid
     * 		local user id
     * @param int $gid
     * 		local group id
     * @return boolean
     */
    public static function IsMemberOf($uid, $gid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*)as cnt"
            ." FROM ".$CC_CONFIG['smembTable']
            ." WHERE uid='$uid' AND gid='$gid'";
        $res = $CC_DBC->getOne($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return (intval($res) > 0);
    } // fn isMemberOf

    public static function increaseLoginAttempts($login){
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET login_attempts = login_attempts+1"
            ." WHERE login='$login'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return (intval($res) > 0);
    }
    
    public static function resetLoginAttempts($login){
        global $CC_CONFIG, $CC_DBC;
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET login_attempts = '0'"
            ." WHERE login='$login'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return (intval($res) > 0);
    }
    
    public static function getLoginAttempts($login){
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT login_attempts FROM ".$CC_CONFIG['subjTable']." WHERE login='$login'";
        $res = $CC_DBC->getOne($sql);
        Logging::log($res);
        if (PEAR::isError($res)) {
            return $res;
        }
        return $res;
    }

} // class Subjects


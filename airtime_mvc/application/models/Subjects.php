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
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $cpass = md5($pass);
        $sql = "SELECT id FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login' AND pass='$cpass' AND type='U'"
            ." LIMIT 1";
        $query = $con->query($sql)->fetchColumn(0);
        return $query;
    }


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
        global $CC_CONFIG;
        $con = Propel::getConnection();
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
        $con->exec($sql);
        return TRUE;
    }


    /* --------------------------------------------------------------- groups */

    /* --------------------------------------------------------- info methods */

    /**
     * Get subject id from login
     *
     * @param string $login
     * @return int|false
     */
    public static function GetSubjId($login)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "SELECT id FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login'";
        $query = $con->query($sql)->fetchColumn(0);
        return $query ? $query : NULL;
    }


    /**
     * Return true if uid is direct member of gid
     *
     * @param int $uid
     * 		local user id
     * @param int $gid
     * 		local group id
     * @return boolean
     */
    public static function IsMemberOf($uid, $gid)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "SELECT count(*) as cnt"
            ." FROM ".$CC_CONFIG['smembTable']
            ." WHERE uid='$uid' AND gid='$gid'";
        $res = $con->query($sql)->fetchColumn(0);
        return (intval($res) > 0);
    }

    public static function increaseLoginAttempts($login)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET login_attempts = login_attempts+1"
            ." WHERE login='$login'";
        $res = $con->exec($sql);
        return (intval($res) > 0);
    }

    public static function resetLoginAttempts($login)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET login_attempts = '0'"
            ." WHERE login='$login'";
        $res = $con->exec($sql);
        return TRUE;
    }

    public static function getLoginAttempts($login)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "SELECT login_attempts FROM ".$CC_CONFIG['subjTable']." WHERE login='$login'";
        $res = $con->query($sql)->fetchColumn(0);
        return $res ? $res : 0;
    }

} // class Subjects


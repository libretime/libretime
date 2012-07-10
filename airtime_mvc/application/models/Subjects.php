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
        return true;
    }

    public static function getLoginAttempts($login)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "SELECT login_attempts FROM ".$CC_CONFIG['subjTable']." WHERE login='$login'";
        $res = $con->query($sql)->fetchColumn(0);
        return ($res !== false) ? $res : 0;
    }

} // class Subjects


<?php

declare(strict_types=1);

/**
 * Subj class.
 *
 * users + groups
 * with "linearized recursive membership" ;)
 *   (allow adding users to groups or groups to groups)
 *
 * @copyright 2010 Sourcefabric O.P.S.
 * @license https://www.gnu.org/licenses/gpl.txt
 */
class Application_Model_Subjects
{
    // ======================================================= public methods

    public static function increaseLoginAttempts($login)
    {
        $sql = 'UPDATE cc_subjs SET login_attempts = login_attempts+1'
            . ' WHERE login=:login';

        $map = [':login' => $login];

        $res = Application_Common_Database::prepareAndExecute(
            $sql,
            $map,
            Application_Common_Database::EXECUTE
        );

        return intval($res) > 0;
    }

    public static function resetLoginAttempts($login)
    {
        $sql = "UPDATE cc_subjs SET login_attempts = '0'"
            . ' WHERE login=:login';
        $map = [':login' => $login];

        $res = Application_Common_Database::prepareAndExecute(
            $sql,
            $map,
            Application_Common_Database::EXECUTE
        );

        return true;
    }

    public static function getLoginAttempts($login)
    {
        $sql = 'SELECT login_attempts FROM cc_subjs WHERE login=:login';
        $map = [':login' => $login];

        $res = Application_Common_Database::prepareAndExecute(
            $sql,
            $map,
            Application_Common_Database::COLUMN
        );

        return ($res !== false) ? $res : 0;
    }
} // class Subjects

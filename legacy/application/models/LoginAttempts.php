<?php

declare(strict_types=1);

class Application_Model_LoginAttempts
{
    public function __construct()
    {
    }

    public static function increaseAttempts($ip)
    {
        $sql = 'select count(*) from cc_login_attempts WHERE ip= :ip';
        $res = Application_Common_Database::prepareAndExecute($sql, [':ip' => $ip], Application_Common_Database::ALL);
        if ($res) {
            $sql = 'UPDATE cc_login_attempts SET attempts=attempts+1 WHERE ip= :ip';
            Application_Common_Database::prepareAndExecute($sql, [':ip' => $ip], Application_Common_Database::EXECUTE);
        } else {
            $sql = "INSERT INTO cc_login_attempts (ip, attempts) values (':ip', '1')";
            Application_Common_Database::prepareAndExecute($sql, [':ip' => $ip], Application_Common_Database::EXECUTE);
        }
    }

    public static function getAttempts($ip)
    {
        $sql = 'select attempts from cc_login_attempts WHERE ip= :ip';
        $res = Application_Common_Database::prepareAndExecute($sql, [':ip' => $ip], Application_Common_Database::ALL);

        return $res ? $res : 0;
    }

    public static function resetAttempts($ip)
    {
        $sql = 'select count(*) from cc_login_attempts WHERE ip= :ip';
        $res = Application_Common_Database::prepareAndExecute($sql, [':ip' => $ip], Application_Common_Database::COLUMN);
        if ($res > 0) {
            $sql = 'DELETE FROM cc_login_attempts WHERE ip= :ip';
            Application_Common_Database::prepareAndExecute($sql, [':ip' => $ip], Application_Common_Database::EXECUTE);
        }
    }
}

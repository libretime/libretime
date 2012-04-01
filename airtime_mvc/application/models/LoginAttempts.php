<?php
class Application_Model_LoginAttempts {
    public function __construct(){

    }

    public static function increaseAttempts($ip){
        $con = Propel::getConnection();
        $sql = "select count(*) from cc_login_attempts WHERE ip='$ip'";
        $res = $con->query($sql)->fetchColumn(0);
        if ($res) {
            $sql = "UPDATE cc_login_attempts SET attempts=attempts+1 WHERE ip='$ip'";
            $con->exec($sql);
        } else {
            $sql = "INSERT INTO cc_login_attempts (ip, attempts) values ('$ip', '1')";
            $con->exec($sql);
        }
    }

    public static function getAttempts($ip){
        $con = Propel::getConnection();
        $sql = "select attempts from cc_login_attempts WHERE ip='$ip'";
        $res = $con->query($sql)->fetchColumn(0);
        return $res ? $res : 0;
    }

    public static function resetAttempts($ip){
        $con = Propel::getConnection();
        $sql = "select count(*) from cc_login_attempts WHERE ip='$ip'";
        $res = $con->query($sql)->fetchColumn(0);
        if ($res > 0) {
            $sql = "DELETE FROM cc_login_attempts WHERE ip='$ip'";
            $con->exec($sql);
        }
    }
}
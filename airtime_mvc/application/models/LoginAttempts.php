<?php
class Application_Model_LoginAttempts {
    public function __construct(){
    
    }
    
    public static function increaseAttempts($ip){
        global $CC_DBC;
        $sql = "select count(*) from cc_login_attempts WHERE ip='$ip'";
        $res = $CC_DBC->GetOne($sql);
        if($res){
            $sql = "UPDATE cc_login_attempts SET attempts=attempts+1 WHERE ip='$ip'";
            $res = $CC_DBC->query($sql);
            if (PEAR::isError($res)) {
                return $res;
            }
        }else{
            $sql = "INSERT INTO cc_login_attempts (ip, attempts) values ('$ip', '1')";
            $res = $CC_DBC->query($sql);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
    }
    
    public static function getAttempts($ip){
        global $CC_DBC;
        $sql = "select attempts from cc_login_attempts WHERE ip='$ip'";
        $res = $CC_DBC->GetOne($sql);
        return $res;
    }
    
    public static function resetAttempts($ip){
        global $CC_DBC;
        $sql = "select count(*) from cc_login_attempts WHERE ip='$ip'";
        $res = $CC_DBC->GetOne($sql);
        if($res){
            $sql = "DELETE FROM cc_login_attempts WHERE ip='$ip'";
            $res = $CC_DBC->query($sql);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
    }
}
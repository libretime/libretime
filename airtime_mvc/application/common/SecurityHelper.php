<?php

class SecurityHelper {

    public static function htmlescape_recursive(&$arr) {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                self::htmlescape_recursive($arr[$key]);
            } else if (is_string($val)) {
                $arr[$key] = htmlspecialchars($val, ENT_QUOTES);
            }
        }
        return $arr;
    }
}
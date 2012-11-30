<?php

class Application_Model_Locale
{
    public static function getLocales()
    {
        $con = Propel::getConnection();
        $sql = "SELECT * FROM cc_locale";
        $res =  $con->query($sql)->fetchAll();
        $out = array();
        foreach ($res as $r) {
            $out[$r["locale_code"]] = $r["locale_lang"];
        }

        return $out;
    }
}

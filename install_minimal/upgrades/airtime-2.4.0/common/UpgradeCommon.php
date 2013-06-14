<?php
/* These are helper functions that are common to each upgrade such as
 * creating connections to a database, backing up config files etc.
 */
class UpgradeCommon{
    const CONF_FILE_AIRTIME      = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO         = "/etc/airtime/pypo.cfg";
    const CONF_FILE_LIQUIDSOAP   = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
    const CONF_FILE_API_CLIENT   = "/etc/airtime/api_client.cfg";

    const CONF_PYPO_GRP          = "pypo";
    const CONF_WWW_DATA_GRP      = "www-data";
    const CONF_BACKUP_SUFFIX     = "240";
    const VERSION_NUMBER         = "2.4.0";
    
    private static function GetAirtimeSrcDir()
    {
        return __DIR__."/../../../../airtime_mvc";
    }

    /**
     * This function generates a random string.
     *
     * The random string uses two parameters: $p_len and $p_chars. These
     * parameters do not need to be provided, in which case defaults are
     * used.
     *
     * @param string $p_len
     *      How long should the generated string be.
     * @param string $p_chars
     *      String containing chars that should be used for generating.
     * @return string
     *      The generated random string.
     */
    public static function GenerateRandomString($p_len=20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $string = '';
        for ($i = 0; $i < $p_len; $i++)
        {
            $pos = mt_rand(0, strlen($p_chars)-1);
            $string .= $p_chars{$pos};
        }
        return $string;
    }

    //stupid hack found on http://stackoverflow.com/a/1268642/276949
    //with some modifications: 1) Spaces are inserted in between sections and
    //2) values are not quoted.
    public static function write_ini_file($assoc_arr, $path, $has_sections = false) 
    {
        $content = "";

        if ($has_sections) {
            $first_line = true;
            foreach ($assoc_arr as $key=>$elem) {
                if ($first_line) {
                    $content .= "[".$key."]\n";
                    $first_line = false;
                } else {
                    $content .= "\n[".$key."]\n";
                }
                foreach ($elem as $key2=>$elem2) {
                    if(is_array($elem2))
                    {
                        for($i=0;$i<count($elem2);$i++)
                        {
                            $content .= $key2."[] = \"".$elem2[$i]."\"\n";
                        }
                    }
                    else if($elem2=="") $content .= $key2." = \n";
                    else $content .= $key2." = ".$elem2."\n";
                }
            }
        } else { 
            foreach ($assoc_arr as $key=>$elem) { 
                if(is_array($elem)) 
                { 
                    for($i=0;$i<count($elem);$i++) 
                    { 
                        $content .= $key."[] = \"".$elem[$i]."\"\n"; 
                    } 
                } 
                else if($elem=="") $content .= $key." = \n"; 
                else $content .= $key." = ".$elem."\n"; 
            } 
        } 

        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        if (!fwrite($handle, $content)) {
            return false;
        }
        fclose($handle);
        return true;
    }
}

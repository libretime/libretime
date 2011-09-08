<?php

class Airtime194Upgrade{

    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_PYPO_GRP = "pypo";
    
    public static function upgradeLiquidsoapCfgPerms(){
        chmod(self::CONF_FILE_LIQUIDSOAP, 0640);
        chgrp(self::CONF_FILE_LIQUIDSOAP, self::CONF_PYPO_GRP);
    }

}

Airtime194Upgrade::upgradeLiquidsoapCfgPerms();

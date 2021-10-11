<?php

class Airtime_View_Helper_SourceConnectionStatus extends Zend_View_Helper_Abstract
{
    public function SourceConnectionStatus()
    {
        return ['live_dj' => Application_Model_Preference::GetSourceStatus('live_dj'), 'master_dj' => Application_Model_Preference::GetSourceStatus('master_dj')];
    }
}

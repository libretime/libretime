<?php

declare(strict_types=1);

class Airtime_View_Helper_SourceSwitchStatus extends Zend_View_Helper_Abstract
{
    public function SourceSwitchStatus()
    {
        return [
            'live_dj' => Application_Model_Preference::GetSourceSwitchStatus('live_dj'),
            'master_dj' => Application_Model_Preference::GetSourceSwitchStatus('master_dj'),
            'scheduled_play' => Application_Model_Preference::GetSourceSwitchStatus('scheduled_play'),
        ];
    }
}

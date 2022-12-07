<?php

declare(strict_types=1);

class TimeFilledFormatter
{
    /**
     * @string seconds
     */
    private $_seconds;

    // @param string $seconds
    public function __construct($seconds)
    {
        $this->_seconds = $seconds;
    }

    public function format()
    {
        $formatted = '';
        $sign = ($this->_seconds < 0) ? '-' : '+';
        $perfect = true;

        $time = Application_Common_DateHelper::secondsToPlaylistTime(abs($this->_seconds));
        $info = explode(':', $time);

        $formatted .= $sign;

        if (intval($info[0]) > 0) {
            $info[0] = ltrim($info[0], '0');
            $formatted .= " {$info[0]}h";
            $perfect = false;
        }

        if (intval($info[1]) > 0) {
            $info[1] = ltrim($info[1], '0');
            $formatted .= " {$info[1]}m";
            $perfect = false;
        }

        if (intval($info[2]) > 0) {
            $sec = round($info[2], 0);
            $formatted .= " {$sec}s";
            $perfect = false;
        }

        // 0 over/under lap of content.
        if ($perfect === true) {
            $formatted = '+ 0s';
        }

        return $formatted;
    }
}

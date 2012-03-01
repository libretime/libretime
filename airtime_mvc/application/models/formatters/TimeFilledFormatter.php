<?php

class TimeFilledFormatter {

    /**
     * @string seconds
     */
    private $_seconds;

    /*
     * @param string $seconds
     */
    public function __construct($seconds)
    {
        $this->_seconds = $seconds;
    }

    public function format()
    {
        $formatted = "";
        $sign = ($this->_seconds < 0) ? "-" : "+";

        $time = Application_Model_Playlist::secondsToPlaylistTime(abs($this->_seconds));
        Logging::log("time is: ".$time);
        $info = explode(":", $time);

        $formatted .= $sign;

        if (intval($info[0]) > 0) {
            $info[0] = ltrim($info[0], "0");
            $formatted .= " {$info[0]}h";
        }

        if (intval($info[1]) > 0) {
            $info[1] = ltrim($info[1], "0");
            $formatted .= " {$info[1]}m";
        }

        if (intval($info[2]) > 0) {
            $sec = round($info[2], 0);
            $formatted .= " {$sec}s";
        }

        return $formatted;
    }
}
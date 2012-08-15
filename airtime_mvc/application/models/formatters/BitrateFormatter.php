<?php

class BitrateFormatter {

    /**
     * @string length
     */
    private $_bitrate;

    /*
     * @param string $bitrate (bits per second)
     */
    public function __construct($bitrate)
    {
        $this->_bitrate = $bitrate;
    }

    public function format()
    {
        $Kbps = bcdiv($this->_bitrate, 1000, 0);

        return "{$Kbps} Kbps";
    }
}
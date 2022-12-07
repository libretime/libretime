<?php

declare(strict_types=1);

class BitrateFormatter
{
    /**
     * @string length
     */
    private $_bitrate;

    // @param string $bitrate (bits per second)
    public function __construct($bitrate)
    {
        $this->_bitrate = $bitrate;
    }

    public function format()
    {
        $kbps = bcdiv($this->_bitrate, 1000, 0);

        if ($kbps == 0) {
            return '';
        }

        return "{$kbps} Kbps";
    }
}

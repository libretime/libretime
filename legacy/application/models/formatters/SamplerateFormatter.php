<?php

declare(strict_types=1);

class SamplerateFormatter
{
    /**
     * @string sample rate
     */
    private $_samplerate;

    // @param string $samplerate Hz
    public function __construct($samplerate)
    {
        $this->_samplerate = $samplerate;
    }

    public function format()
    {
        $kHz = bcdiv($this->_samplerate, 1000, 1);

        return "{$kHz} kHz";
    }
}
